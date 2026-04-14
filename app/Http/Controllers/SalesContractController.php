<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Project;
use App\Models\SalesContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SalesContractController extends Controller
{
    public function index()
    {
        $contracts = SalesContract::with(['project', 'creator', 'payments'])
            ->latest()
            ->paginate(10);

        return view('sales_contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('sales_contracts.create');
    }

    public function store(Request $request)
    {
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
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',

            // طريقة الدفع
            'payment_type' => 'required|in:full,installments',
            'full_payment_amount' => 'nullable|numeric|min:0',

            // بيانات أول دفعة
            'first_payment_title' => 'nullable|string|max:255',
            'first_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'first_payment_amount' => 'nullable|numeric|min:0',
            'first_payment_due_date' => 'nullable|date',
        ]);

        $contractFilePath = null;

        if ($request->hasFile('contract_file')) {
            $contractFilePath = $request->file('contract_file')->store('contracts', 'public');
        }

        $projectCode = 'PRJ-' . date('Y') . '-' . str_pad((Project::count() + 1), 4, '0', STR_PAD_LEFT);

        $startDate = $request->actual_start_date ?: $request->expected_start_date ?: now()->toDateString();

        $endDate = $request->expected_end_date
            ?: date('Y-m-d', strtotime($startDate . ' +30 days'));

        // إنشاء المشروع بدون تحويله مباشرة إلى التصاميم
        $project = Project::create([
            'project_code' => $projectCode,
            'name' => $request->project_name,
            'client_name' => $request->client_name,
            'main_contractor' => $request->main_contractor,
            'description' => $request->description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'project_value' => $request->project_value ?? 0,
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
            'project_value' => $request->project_value,
            'project_duration' => $request->project_duration,
            'expected_start_date' => $request->expected_start_date,
            'actual_start_date' => $request->actual_start_date,
            'expected_end_date' => $request->expected_end_date,
            'description' => $request->description,
            'notes' => $request->notes,
            'contract_file' => $contractFilePath,

            // بيانات الدفع
            'payment_type' => $request->payment_type,
            'full_payment_amount' => $request->payment_type === 'full' ? ($request->full_payment_amount ?? 0) : null,
            'first_payment_title' => $request->payment_type === 'installments' ? $request->first_payment_title : null,
            'first_payment_percentage' => $request->payment_type === 'installments' ? $request->first_payment_percentage : null,
            'first_payment_amount' => $request->payment_type === 'installments' ? $request->first_payment_amount : null,
            'first_payment_due_date' => $request->payment_type === 'installments' ? $request->first_payment_due_date : null,

            // لا ينتقل إلا بعد تسجيل دفعة
            'status' => 'awaiting_payment',
            'created_by' => Auth::id(),
        ]);

        $project->update([
            'sales_contract_id' => $contract->id,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'SalesContract',
            'model_id' => $contract->id,
            'description' => 'تم إنشاء عقد جديد برقم ' . $contract->contract_no . ' وإنشاء المشروع المرتبط بدون تحويله للتصاميم حتى تسجيل الدفع.',
        ]);

        return redirect()
            ->route('sales-contracts.index')
            ->with('success', 'تم إنشاء العقد بنجاح. لن ينتقل العقد إلى التصاميم إلا بعد دفع كامل المبلغ أو تسجيل الدفعة الأولى.');
    }

    public function show($id)
    {
        $contract = SalesContract::with(['project', 'creator', 'payments'])->findOrFail($id);

        return view('sales_contracts.show', compact('contract'));
    }

    public function edit($id)
    {
        $contract = SalesContract::with(['project', 'payments'])->findOrFail($id);

        return view('sales_contracts.edit', compact('contract'));
    }

    public function update(Request $request, $id)
    {
        $contract = SalesContract::findOrFail($id);

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
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',

            // طريقة الدفع
            'payment_type' => 'required|in:full,installments',
            'full_payment_amount' => 'nullable|numeric|min:0',

            // بيانات أول دفعة
            'first_payment_title' => 'nullable|string|max:255',
            'first_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'first_payment_amount' => 'nullable|numeric|min:0',
            'first_payment_due_date' => 'nullable|date',
        ]);

        $contractFilePath = $contract->contract_file;

        if ($request->hasFile('contract_file')) {
            if ($contract->contract_file && Storage::disk('public')->exists($contract->contract_file)) {
                Storage::disk('public')->delete($contract->contract_file);
            }

            $contractFilePath = $request->file('contract_file')->store('contracts', 'public');
        }

        $contract->update([
            'contract_no' => $request->contract_no,
            'contract_date' => $request->contract_date,
            'client_name' => $request->client_name,
            'main_contractor' => $request->main_contractor,
            'project_name' => $request->project_name,
            'project_location' => $request->project_location,
            'project_value' => $request->project_value,
            'project_duration' => $request->project_duration,
            'expected_start_date' => $request->expected_start_date,
            'actual_start_date' => $request->actual_start_date,
            'expected_end_date' => $request->expected_end_date,
            'description' => $request->description,
            'notes' => $request->notes,
            'contract_file' => $contractFilePath,

            // بيانات الدفع
            'payment_type' => $request->payment_type,
            'full_payment_amount' => $request->payment_type === 'full' ? ($request->full_payment_amount ?? 0) : null,
            'first_payment_title' => $request->payment_type === 'installments' ? $request->first_payment_title : null,
            'first_payment_percentage' => $request->payment_type === 'installments' ? $request->first_payment_percentage : null,
            'first_payment_amount' => $request->payment_type === 'installments' ? $request->first_payment_amount : null,
            'first_payment_due_date' => $request->payment_type === 'installments' ? $request->first_payment_due_date : null,
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
                'project_value' => $request->project_value ?? 0,
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
            ->with('success', 'تم تحديث العقد بنجاح.');
    }

    public function destroy($id)
    {
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
            ->with('success', 'تم حذف العقد بنجاح.');
    }
}