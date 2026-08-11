<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Project;
use App\Models\SalesContract;
use App\Services\StageNotificationService;
use App\Support\ContractPaymentTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SalesContractController extends Controller
{
    protected function authorizeContractsModule(): void
    {
        abort_unless(auth()->check() && auth()->user()->canAccessContractsModule(), 403);
    }

    public function index()
    {
        $this->authorizeContractsModule();
        // Hide contracts linked to completed/archived projects.
        $contracts = SalesContract::with(['project', 'creator', 'payments'])
            ->whereHas('project', function ($query) {
                $query->active();
            })
            ->latest()
            ->paginate(10);

        return view('sales_contracts.index', compact('contracts'));
    }

    public function create()
    {
        $this->authorizeContractsModule();

        return view('sales_contracts.create', [
            'maxUploadMb' => $this->formatMegabytes($this->maxContractUploadKilobytes()),
        ]);
    }

    public function store(Request $request, StageNotificationService $stageNotificationService)
    {
        $this->authorizeContractsModule();

        $maxKb = $this->maxContractUploadKilobytes();

        if ($request->hasFile('contract_file') && ! $request->file('contract_file')->isValid()) {
            return back()
                ->withInput($request->except('contract_file'))
                ->withErrors([
                    'contract_file' => $this->contractUploadErrorMessage(
                        (int) $request->file('contract_file')->getError(),
                        $maxKb
                    ),
                ]);
        }

        $request->validate([
            'contract_no' => 'required|string|max:255|unique:sales_contracts,contract_no',
            'contract_date' => 'required|date',
            'client_name' => 'required|string|max:255',
            'main_contractor' => 'nullable|string|max:255',
            'project_name' => 'required|string|max:255',
            'project_location' => 'nullable|string|max:255',
            'project_value' => 'nullable|numeric',
            'project_duration' => 'nullable|integer',
            'expected_start_date' => 'nullable|date',
            'actual_start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date|after_or_equal:expected_start_date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'contract_file' => [
                'nullable',
                'file',
                'max:'.$maxKb,
            ],

            'payment_type' => ['required', Rule::in(ContractPaymentTypes::ALL)],
            'full_payment_amount' => 'nullable|numeric|min:0',
            'first_payment_title' => 'nullable|string|max:255',
            'first_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'first_payment_amount' => 'nullable|numeric|min:0',
            'first_payment_due_date' => 'nullable|date',
        ], [
            'contract_file.max' => __('contracts.error_contract_file_too_large', [
                'max' => $this->formatMegabytes($maxKb),
            ]),
            'contract_file.uploaded' => __('contracts.error_contract_file_too_large', [
                'max' => $this->formatMegabytes($maxKb),
            ]),
        ]);

        if ($request->hasFile('contract_file')) {
            $extensionError = $this->contractFileExtensionError($request->file('contract_file'));
            if ($extensionError !== null) {
                return back()
                    ->withInput($request->except('contract_file'))
                    ->withErrors(['contract_file' => $extensionError]);
            }
        }

        $user = auth()->user();
        $canFullFin = $user->canViewProjectFinancials();
        $canValueOnly = $user->canViewProjectValueOnly();

        $resolvedProjectValue = 0.0;
        if ($canFullFin || $canValueOnly) {
            $resolvedProjectValue = (float) ($request->project_value ?? 0);
        }

        $paymentType = $request->payment_type;
        $isGovernment = $paymentType === ContractPaymentTypes::GOVERNMENT;
        $isInstallment = ContractPaymentTypes::isInstallmentType($paymentType);
        $installmentCount = ContractPaymentTypes::installmentCountFor($paymentType);

        $fullPaymentAmount = $canFullFin && $paymentType === ContractPaymentTypes::FULL
            ? (float) ($request->full_payment_amount ?? 0)
            : null;

        $firstTitle = $canFullFin && $isInstallment ? $request->first_payment_title : null;
        $firstPct = $canFullFin && $isInstallment ? $request->first_payment_percentage : null;
        $firstAmt = $canFullFin && $isInstallment ? (float) ($request->first_payment_amount ?? 0) : null;
        $firstDue = $canFullFin && $isInstallment ? $request->first_payment_due_date : null;

        $contractFilePath = null;

        if ($request->hasFile('contract_file')) {
            try {
                Storage::disk('public')->makeDirectory('contracts');
                $contractFilePath = $request->file('contract_file')->store('contracts', 'public');

                if (! is_string($contractFilePath) || $contractFilePath === '' || ! Storage::disk('public')->exists($contractFilePath)) {
                    return back()
                        ->withInput($request->except('contract_file'))
                        ->withErrors(['contract_file' => __('contracts.error_contract_file_store_failed')]);
                }
            } catch (\Throwable $e) {
                report($e);

                return back()
                    ->withInput($request->except('contract_file'))
                    ->withErrors(['contract_file' => __('contracts.error_contract_file_store_failed')]);
            }
        }

        $startDate = $request->actual_start_date ?: $request->expected_start_date ?: now()->toDateString();

        $endDate = $request->expected_end_date
            ?: date('Y-m-d', strtotime($startDate . ' +30 days'));

        try {
            [$project, $contract] = DB::transaction(function () use (
                $request,
                $startDate,
                $endDate,
                $resolvedProjectValue,
                $contractFilePath,
                $paymentType,
                $installmentCount,
                $fullPaymentAmount,
                $isInstallment,
                $firstTitle,
                $firstPct,
                $firstAmt,
                $firstDue,
                $isGovernment
            ) {
                $project = Project::create([
                    'project_code' => Project::generateNextCode(),
                    'name' => $request->project_name,
                    'client_name' => $request->client_name,
                    'main_contractor' => $request->main_contractor,
                    'description' => $request->description,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'project_value' => $resolvedProjectValue,
                    'status' => 'pending',
                    'current_stage' => 'contracts',
                    'notes' => $request->notes,
                    'created_by' => Auth::id(),
                ]);

                $contract = SalesContract::create([
                    'project_id' => $project->id,
                    'contract_no' => $request->contract_no,
                    'contract_date' => $request->contract_date,
                    'client_name' => $request->client_name,
                    'main_contractor' => $request->main_contractor,
                    'project_name' => $request->project_name,
                    'project_location' => $request->project_location,
                    'project_value' => $resolvedProjectValue,
                    'project_duration' => $request->project_duration,
                    'expected_start_date' => $request->expected_start_date,
                    'actual_start_date' => $request->actual_start_date,
                    'expected_end_date' => $request->expected_end_date,
                    'description' => $request->description,
                    'notes' => $request->notes,
                    'contract_file' => $contractFilePath,

                    'payment_type' => $paymentType,
                    'installment_count' => $installmentCount,
                    'full_payment_amount' => $paymentType === ContractPaymentTypes::FULL ? $fullPaymentAmount : null,
                    'first_payment_title' => $isInstallment ? $firstTitle : null,
                    'first_payment_percentage' => $isInstallment ? $firstPct : null,
                    'first_payment_amount' => $isInstallment ? $firstAmt : null,
                    'first_payment_due_date' => $isInstallment ? $firstDue : null,
                    'status' => $isGovernment ? 'government' : 'awaiting_payment',
                    'created_by' => Auth::id(),
                ]);

                $project->update([
                    'sales_contract_id' => $contract->id,
                ]);

                if ($isGovernment) {
                    $project->update([
                        'current_stage' => 'architect',
                        'status' => 'ongoing',
                    ]);
                }

                return [$project, $contract];
            });
        } catch (\Throwable $e) {
            if ($contractFilePath && Storage::disk('public')->exists($contractFilePath)) {
                Storage::disk('public')->delete($contractFilePath);
            }

            throw $e;
        }

        if ($isGovernment) {
            $stageNotificationService->sendDesignStageNotification($contract);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'model' => 'SalesContract',
                'model_id' => $contract->id,
                'description' => 'عقد حكومي رقم ' . $contract->contract_no . ' — نُقل للتصاميم دون دفعة أولى؛ سجل الدفعات يدوي.',
            ]);

            return redirect()
                ->route('sales-contracts.index')
                ->with('success', __('contracts.flash_created_government'));
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'SalesContract',
            'model_id' => $contract->id,
            'description' => 'تم إنشاء عقد جديد برقم ' . $contract->contract_no . ' وإنشاء المشروع المرتبط بدون تحويله للتصاميم حتى تسجيل الدفع.',
        ]);

        return redirect()
            ->route('sales-contracts.index')
            ->with('success', __('contracts.flash_created'));
    }

    public function show($id)
    {
        $this->authorizeContractsModule();

        $contract = SalesContract::with(['project', 'creator', 'payments'])->findOrFail($id);

        return view('sales_contracts.show', compact('contract'));
    }

    public function edit($id)
    {
        $this->authorizeContractsModule();

        $contract = SalesContract::with(['project', 'payments'])->findOrFail($id);

        return view('sales_contracts.edit', [
            'contract' => $contract,
            'maxUploadMb' => $this->formatMegabytes($this->maxContractUploadKilobytes()),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeContractsModule();

        $contract = SalesContract::findOrFail($id);
        $maxKb = $this->maxContractUploadKilobytes();

        if ($request->hasFile('contract_file') && ! $request->file('contract_file')->isValid()) {
            return back()
                ->withInput($request->except('contract_file'))
                ->withErrors([
                    'contract_file' => $this->contractUploadErrorMessage(
                        (int) $request->file('contract_file')->getError(),
                        $maxKb
                    ),
                ]);
        }

        $request->validate([
            'contract_no' => 'required|string|max:255|unique:sales_contracts,contract_no,' . $contract->id,
            'contract_date' => 'required|date',
            'client_name' => 'required|string|max:255',
            'main_contractor' => 'nullable|string|max:255',
            'project_name' => 'required|string|max:255',
            'project_location' => 'nullable|string|max:255',
            'project_value' => 'nullable|numeric',
            'project_duration' => 'nullable|integer',
            'expected_start_date' => 'nullable|date',
            'actual_start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date|after_or_equal:expected_start_date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'contract_file' => [
                'nullable',
                'file',
                'max:'.$maxKb,
            ],

            'payment_type' => ['required', Rule::in(ContractPaymentTypes::ALL)],
            'full_payment_amount' => 'nullable|numeric|min:0',
            'first_payment_title' => 'nullable|string|max:255',
            'first_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'first_payment_amount' => 'nullable|numeric|min:0',
            'first_payment_due_date' => 'nullable|date',
        ], [
            'contract_file.max' => __('contracts.error_contract_file_too_large', [
                'max' => $this->formatMegabytes($maxKb),
            ]),
            'contract_file.uploaded' => __('contracts.error_contract_file_too_large', [
                'max' => $this->formatMegabytes($maxKb),
            ]),
        ]);

        if ($request->hasFile('contract_file')) {
            $extensionError = $this->contractFileExtensionError($request->file('contract_file'));
            if ($extensionError !== null) {
                return back()
                    ->withInput($request->except('contract_file'))
                    ->withErrors(['contract_file' => $extensionError]);
            }
        }

        $contractFilePath = $contract->contract_file;

        if ($request->hasFile('contract_file')) {
            try {
                Storage::disk('public')->makeDirectory('contracts');

                if ($contract->contract_file && Storage::disk('public')->exists($contract->contract_file)) {
                    Storage::disk('public')->delete($contract->contract_file);
                }

                $stored = $request->file('contract_file')->store('contracts', 'public');

                if (! is_string($stored) || $stored === '' || ! Storage::disk('public')->exists($stored)) {
                    return back()
                        ->withInput($request->except('contract_file'))
                        ->withErrors(['contract_file' => __('contracts.error_contract_file_store_failed')]);
                }

                $contractFilePath = $stored;
            } catch (\Throwable $e) {
                report($e);

                return back()
                    ->withInput($request->except('contract_file'))
                    ->withErrors(['contract_file' => __('contracts.error_contract_file_store_failed')]);
            }
        }

        $user = auth()->user();
        $canFullFin = $user->canViewProjectFinancials();
        $canValueOnly = $user->canViewProjectValueOnly();

        $resolvedProjectValue = (float) ($contract->project_value ?? 0);
        if ($canFullFin || $canValueOnly) {
            $resolvedProjectValue = (float) ($request->project_value ?? 0);
        }

        $paymentPayload = [
            'payment_type' => $contract->payment_type,
            'installment_count' => $contract->installment_count,
            'full_payment_amount' => $contract->full_payment_amount,
            'first_payment_title' => $contract->first_payment_title,
            'first_payment_percentage' => $contract->first_payment_percentage,
            'first_payment_amount' => $contract->first_payment_amount,
            'first_payment_due_date' => $contract->first_payment_due_date,
        ];

        if ($canFullFin) {
            $paymentType = $request->payment_type;
            $isInstallment = ContractPaymentTypes::isInstallmentType($paymentType);

            $paymentPayload = [
                'payment_type' => $paymentType,
                'installment_count' => ContractPaymentTypes::installmentCountFor($paymentType),
                'full_payment_amount' => $paymentType === ContractPaymentTypes::FULL ? ($request->full_payment_amount ?? 0) : null,
                'first_payment_title' => $isInstallment ? $request->first_payment_title : null,
                'first_payment_percentage' => $isInstallment ? $request->first_payment_percentage : null,
                'first_payment_amount' => $isInstallment ? $request->first_payment_amount : null,
                'first_payment_due_date' => $isInstallment ? $request->first_payment_due_date : null,
            ];
        } elseif ($canValueOnly) {
            // المبيعات: تحديث طريقة الدفع وعدد الأقساط بدون تفاصيل مالية كاملة.
            $paymentType = $request->payment_type;
            $paymentPayload['payment_type'] = $paymentType;
            $paymentPayload['installment_count'] = ContractPaymentTypes::installmentCountFor($paymentType);

            if ($paymentType === ContractPaymentTypes::FULL) {
                $paymentPayload['first_payment_title'] = null;
                $paymentPayload['first_payment_percentage'] = null;
                $paymentPayload['first_payment_amount'] = null;
                $paymentPayload['first_payment_due_date'] = null;
            } elseif ($paymentType === ContractPaymentTypes::GOVERNMENT) {
                $paymentPayload['full_payment_amount'] = null;
                $paymentPayload['first_payment_title'] = null;
                $paymentPayload['first_payment_percentage'] = null;
                $paymentPayload['first_payment_amount'] = null;
                $paymentPayload['first_payment_due_date'] = null;
            }
        }

        $contract->update([
            'contract_no' => $request->contract_no,
            'contract_date' => $request->contract_date,
            'client_name' => $request->client_name,
            'main_contractor' => $request->main_contractor,
            'project_name' => $request->project_name,
            'project_location' => $request->project_location,
            'project_value' => $resolvedProjectValue,
            'project_duration' => $request->project_duration,
            'expected_start_date' => $request->expected_start_date,
            'actual_start_date' => $request->actual_start_date,
            'expected_end_date' => $request->expected_end_date,
            'description' => $request->description,
            'notes' => $request->notes,
            'contract_file' => $contractFilePath,

            'payment_type' => $paymentPayload['payment_type'],
            'installment_count' => $paymentPayload['installment_count'] ?? $contract->installment_count,
            'full_payment_amount' => $paymentPayload['full_payment_amount'],
            'first_payment_title' => $paymentPayload['first_payment_title'],
            'first_payment_percentage' => $paymentPayload['first_payment_percentage'],
            'first_payment_amount' => $paymentPayload['first_payment_amount'],
            'first_payment_due_date' => $paymentPayload['first_payment_due_date'],
        ]);

        if ($contract->project) {
            $startDate = $request->actual_start_date ?: $request->expected_start_date ?: $contract->project->start_date;

            $endDate = $request->expected_end_date ?: $contract->project->end_date;

            $contract->project->update([
                'name' => $request->project_name,
                'client_name' => $request->client_name,
                'main_contractor' => $request->main_contractor,
                'description' => $request->description,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'project_value' => $resolvedProjectValue,
                'notes' => $request->notes,
            ]);
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'update',
            'model' => 'SalesContract',
            'model_id' => $contract->id,
            'description' => 'تم تحديث بيانات العقد رقم ' . $contract->contract_no . '.',
        ]);

        return redirect()
            ->route('sales-contracts.index')
            ->with('success', __('contracts.flash_updated'));
    }

    public function destroy($id)
    {
        $this->authorizeContractsModule();

        $contract = SalesContract::findOrFail($id);

        if ($contract->contract_file && Storage::disk('public')->exists($contract->contract_file)) {
            Storage::disk('public')->delete($contract->contract_file);
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete',
            'model' => 'SalesContract',
            'model_id' => $contract->id,
            'description' => 'تم حذف العقد رقم ' . $contract->contract_no . '.',
        ]);

        if ($contract->project) {
            $contract->project->delete();
        }

        $contract->delete();

        return redirect()
            ->route('sales-contracts.index')
            ->with('success', __('contracts.flash_deleted'));
    }

    private function maxContractUploadKilobytes(): int
    {
        $phpBytes = $this->phpSizeToBytes((string) ini_get('upload_max_filesize'));
        $postBytes = $this->phpSizeToBytes((string) ini_get('post_max_size'));
        $limitBytes = min($phpBytes, $postBytes);

        // Leave a small margin under post_max_size for other form fields.
        $limitBytes = max(1024 * 100, $limitBytes - (1024 * 100));

        $desiredBytes = 20 * 1024 * 1024; // 20 MB app target
        $effectiveBytes = min($desiredBytes, $limitBytes);

        return max(1, (int) floor($effectiveBytes / 1024));
    }

    private function phpSizeToBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '') {
            return 2 * 1024 * 1024;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return (int) match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (float) $value,
        };
    }

    private function formatMegabytes(int $kilobytes): string
    {
        $mb = $kilobytes / 1024;

        return rtrim(rtrim(number_format($mb, 1, '.', ''), '0'), '.');
    }

    private function contractUploadErrorMessage(int $errorCode, int $maxKb): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => __('contracts.error_contract_file_too_large', [
                'max' => $this->formatMegabytes($maxKb),
            ]),
            UPLOAD_ERR_PARTIAL => __('contracts.error_contract_file_partial'),
            UPLOAD_ERR_NO_FILE => __('contracts.error_contract_file_required'),
            default => __('contracts.error_contract_file_upload_failed'),
        };
    }

    private function contractFileExtensionError($file): ?string
    {
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        $extension = strtolower((string) $file->getClientOriginalExtension());

        if ($extension === '' || ! in_array($extension, $allowed, true)) {
            return __('contracts.error_contract_file_type', [
                'types' => implode(', ', $allowed),
            ]);
        }

        return null;
    }
}