<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ContractPayment;
use App\Models\SalesContract;
use App\Services\StageNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractPaymentController extends Controller
{
    public function store(Request $request, $id, StageNotificationService $stageNotificationService)
    {
        $contract = SalesContract::with(['project', 'payments'])->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

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

        // ======================================
        // ✅ حالة الدفع الكامل
        // ======================================
        if ($contract->payment_type === 'full') {

            if ($totalPaid >= (float) ($contract->project_value ?? 0)) {

                $shouldMoveToDesigns = true;

                $contract->update([
                    'status' => 'paid',
                ]);

            } else {

                $contract->update([
                    'status' => 'partial',
                ]);
            }
        }

        // ======================================
        // ✅ حالة الدفعات (أول دفعة فقط)
        // ======================================
        if ($contract->payment_type === 'installments') {

            if ($contract->payments()->count() >= 1) {

                $shouldMoveToDesigns = true;

                $contract->update([
                    'status' => 'partial',
                ]);
            }
        }

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
            ->with('success', 'تم تسجيل الدفعة بنجاح');
    }
}