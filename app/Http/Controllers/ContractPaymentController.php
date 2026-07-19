<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ContractPayment;
use App\Models\SalesContract;
use App\Services\CashFlowLedgerService;
use App\Services\StageNotificationService;
use App\Support\ContractPaymentTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContractPaymentController extends Controller
{
    public function store(Request $request, $id, StageNotificationService $stageNotificationService)
    {
        abort_unless(auth()->check() && auth()->user()->canAccessContractsModule(), 403);

        $contract = SalesContract::with(['project', 'payments'])->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validator->after(function ($validator) use ($contract, $request) {
            $remaining = max(0, ((float) $contract->project_value) - (float) $contract->payments()->sum('amount'));
            $amount = (float) $request->input('amount', 0);

            if ($amount > $remaining) {
                $validator->errors()->add(
                    'amount',
                    __('contracts.payment_amount_exceeds', ['remaining' => number_format($remaining, 2)])
                );
            }
        });

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => __('contracts.payment_data_invalid'),
                    'errors' => $validator->errors(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $hadPaymentsBefore = $contract->payments()->exists();

        $payment = ContractPayment::create([
            'sales_contract_id' => $contract->id,
            'payment_type' => $this->resolvePaymentRowType($contract),
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $contract->syncPaymentStatus();

        app(CashFlowLedgerService::class)->syncContractPayment($payment);

        if (
            $contract->requiresFirstPaymentForDesigns()
            && ! $hadPaymentsBefore
            && $contract->project
            && $contract->project->current_stage !== 'architect'
        ) {
            $contract->project->update([
                'current_stage' => 'architect',
                'status' => 'ongoing',
            ]);

            $stageNotificationService->sendDesignStageNotification($contract);
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'ContractPayment',
            'model_id' => $payment->id,
            'description' => 'تم تسجيل دفعة للعقد رقم ' . $contract->contract_no . ' بمبلغ ' . $payment->amount,
        ]);

        $message = $contract->isGovernmentPayment()
            ? __('contracts.payment_recorded_government')
            : __('contracts.payment_recorded');

        return redirect()
            ->route('sales-contracts.show', $contract->id)
            ->with('success', $message);
    }

    private function resolvePaymentRowType(SalesContract $contract): string
    {
        if ($contract->isGovernmentPayment()) {
            return ContractPaymentTypes::GOVERNMENT;
        }

        if ($contract->isFullPaymentPlan()) {
            return ContractPaymentTypes::FULL;
        }

        return 'installment';
    }
}
