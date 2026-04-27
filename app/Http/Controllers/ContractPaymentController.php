<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ContractPayment;
use App\Models\SalesContract;
use App\Services\StageNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContractPaymentController extends Controller
{
    public function store(Request $request, $id, StageNotificationService $stageNotificationService)
    {
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

        // 🔥 إنشاء الدفعة
        $payment = ContractPayment::create([
            'sales_contract_id' => $contract->id,
            'payment_type' => $contract->payment_type === 'full' ? 'full' : 'installment',
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // 🔥 إعادة الحساب بعد إضافة الدفعة
        $totalPaid = $contract->payments()->sum('amount');

        $shouldMoveToDesigns = false;

        if ($contract->payments()->count() >= 1) {
            $shouldMoveToDesigns = true;
        }

        $contract->update([
            'status' => $totalPaid >= (float) ($contract->project_value ?? 0) ? 'paid' : 'partial',
        ]);

        // ======================================
        // 🔥 نقل المشروع + إرسال إيميل
        // ======================================
        if ($shouldMoveToDesigns && $contract->project) {

            // تأكد ما يرسل مرتين
            if ($contract->project->current_stage !== 'architect') {

                $contract->project->update([
                    'current_stage' => 'architect',
                    'status' => 'ongoing',
                ]);

                $stageNotificationService->sendDesignStageNotification($contract);
            }
        }

        // ======================================
        // 🔥 تسجيل اللوق
        // ======================================
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'create',
            'model' => 'ContractPayment',
            'model_id' => $payment->id,
            'description' => 'تم تسجيل دفعة للعقد رقم ' . $contract->contract_no . ' بمبلغ ' . $payment->amount,
        ]);

        return redirect()
            ->route('sales-contracts.show', $contract->id)
            ->with('success', __('contracts.payment_recorded'));
    }
}