<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\DocumentAutoFillParser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentParseController extends Controller
{
    public function __construct(private readonly DocumentAutoFillParser $parser)
    {
    }

    public function parse(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'form_key' => ['required', 'string', 'in:employees,contracts,purchases,projects,factory,warehouse'],
            'document' => [
                'required',
                'file',
                'mimes:pdf,xlsx,csv,jpg,jpeg,png,webp',
                'mimetypes:application/pdf,text/csv,text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/jpeg,image/png,image/webp',
                'max:5120',
            ],
        ]);

        $formKey = (string) $validated['form_key'];
        if (! $this->canParseForm($formKey, $request)) {
            return response()->json([
                'message' => 'غير مصرح لك بتحليل هذا النموذج.',
            ], 403);
        }

        $file = $validated['document'];
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $tempPath = $file->store('autofill-temp', 'local');
        $absolutePath = Storage::disk('local')->path($tempPath);

        try {
            $parsed = $this->parser->parse($absolutePath, $extension, $formKey);
            $data = $this->mapForForm($formKey, $parsed);

            $this->safeLog([
                'user_id' => Auth::id(),
                'action' => 'file_uploaded',
                'model' => 'DocumentAutoFill',
                'model_id' => null,
                'description' => 'module=' . $formKey . ' | file_name=' . $file->getClientOriginalName(),
            ]);

            $this->safeLog([
                'user_id' => Auth::id(),
                'action' => 'parse_document',
                'model' => 'DocumentAutoFill',
                'model_id' => null,
                'description' => 'Auto-fill parse executed for form [' . $formKey . '] with keys: ' . implode(', ', array_keys($data)),
            ]);

            return response()->json([
                'form_key' => $formKey,
                'fields' => $data,
            ]);
        } catch (\Throwable $e) {
            $this->safeLog([
                'user_id' => Auth::id(),
                'action' => 'parse_document_failed',
                'model' => 'DocumentAutoFill',
                'model_id' => null,
                'description' => 'Auto-fill parse failed for form [' . $formKey . '].',
            ]);

            return response()->json([
                'message' => 'تعذر تحليل الملف. تأكد من أن الملف صحيح وقابل للقراءة.',
            ], 422);
        } finally {
            Storage::disk('local')->delete($tempPath);
        }
    }

    private function mapForForm(string $formKey, array $parsed): array
    {
        $fieldMappings = [
            'employees' => [
                'name' => 'name',
                'email' => 'email',
                'phone' => 'phone',
                'date' => 'hire_date',
                'salary' => 'salary',
                'amount' => 'salary',
                'passport_number' => 'passport_number',
                'iqama_number' => 'residency_number',
                'passport_expiry' => 'passport_expiry_date',
            ],
            'contracts' => [
                'client_name' => 'client_name',
                'project_name' => 'project_name',
                'date' => 'contract_date',
                'amount' => 'project_value',
                'project_value' => 'project_value',
                'vendor' => 'main_contractor',
                'main_contractor' => 'main_contractor',
                'contract_number' => 'contract_no',
            ],
            'purchases' => [
                'name' => 'name',
                'category' => 'category',
                'quantity' => 'quantity',
                'vendor' => 'vendor',
                'date' => 'date',
                'amount' => 'total_cost',
                'total_cost' => 'total_cost',
                'invoice_number' => 'invoice_number',
            ],
            'projects' => [
                'project_name' => 'name',
                'description' => 'description',
                'start_date' => 'start_date',
                'end_date' => 'end_date',
                'project_value' => 'project_value',
                'expenses' => 'expenses',
                'notes' => 'notes',
            ],
            'factory' => [
                'order_number' => 'order_number',
                'product_name' => 'product_name',
                'quantity' => 'planned_quantity',
                'start_date' => 'production_start_date',
                'end_date' => 'expected_end_date',
                'daily_target' => 'daily_target',
                'notes' => 'notes',
            ],
            'warehouse' => [
                'name' => 'name',
                'quantity' => 'quantity',
                'unit' => 'unit',
                'notes' => 'notes',
            ],
        ];

        $mapping = $fieldMappings[$formKey] ?? [];
        $result = [];
        foreach ($mapping as $source => $target) {
            if (array_key_exists($source, $parsed) && $parsed[$source] !== null && $parsed[$source] !== '') {
                $result[$target] = $parsed[$source];
            }
        }

        return $result;
    }

    private function canParseForm(string $formKey, Request $request): bool
    {
        $user = $request->user();
        if (! $user) {
            return false;
        }

        return match ($formKey) {
            'employees' => $user->canAccessHRModule(),
            'contracts' => $user->canAccessContractsModule(),
            'purchases' => $user->canAccessProcurementModule()
                || $user->canAccessGeneralPurchasesModule()
                || $user->canAccessContractPurchasesModule(),
            'warehouse' => $user->canAccessProcurementModule(),
            'projects' => $user->canAccessEngineeringModule(),
            'factory' => $user->canAccessOperationsModule(),
            default => false,
        };
    }

    private function safeLog(array $payload): void
    {
        try {
            AuditLog::create($payload);
        } catch (\Throwable $e) {
            // Keep document parsing non-blocking even if audit logging fails.
        }
    }
}
