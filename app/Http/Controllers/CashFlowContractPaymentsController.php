<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\ContractPayment;
use App\Models\CashFlowEntry;
use App\Models\Project;
use App\Models\SalesContract;
use App\Services\CashFlowLedgerService;
use App\Support\ContractPaymentTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CashFlowContractPaymentsController extends Controller
{
    protected function authorizeCashFlow(): void
    {
        if (! auth()->check() || ! auth()->user()->canAccessCashFlowModule()) {
            abort(403, __('cash_flow.unauthorized'));
        }
    }

    public function create(Request $request)
    {
        $this->authorizeCashFlow();

        $projects = Project::query()
            ->active()
            ->whereNotNull('sales_contract_id')
            ->orderByDesc('id')
            ->get(['id', 'project_code', 'name', 'client_name', 'sales_contract_id']);

        $selectedProjectId = $request->filled('project_id') ? (int) $request->project_id : null;
        $contract = null;

        if ($selectedProjectId) {
            $project = $projects->firstWhere('id', $selectedProjectId);

            if ($project) {
                $contract = SalesContract::query()
                    ->with(['payments'])
                    ->find($project->sales_contract_id);
            }
        }

        return view('cash-flow.contract-payments.create', [
            'projects' => $projects,
            'selectedProjectId' => $selectedProjectId,
            'contract' => $contract,
        ]);
    }

    public function store(Request $request, CashFlowLedgerService $ledger)
    {
        $this->authorizeCashFlow();

        $validated = $request->validate([
            'project_id' => [
                'required',
                'integer',
                Rule::exists('projects', 'id')->where(function ($query) {
                    $query->whereNotNull('sales_contract_id');
                }),
            ],
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ], [], [
            'project_id' => __('project_reports.field_project'),
            'amount' => __('cash_flow.field_amount'),
            'payment_date' => __('cash_flow.field_date'),
        ]);

        $project = Project::query()->findOrFail($validated['project_id']);

        if ($project->isCompleted()) {
            return back()->withErrors([
                'project_id' => __('cash_flow.error_project_completed'),
            ])->withInput();
        }

        $contract = SalesContract::with(['payments'])->findOrFail($project->sales_contract_id);

        $remaining = max(0, ((float) $contract->project_value) - (float) $contract->payments->sum('amount'));
        $amount = (float) $validated['amount'];

        if ($amount > $remaining) {
            return back()->withErrors([
                'amount' => __('contracts.payment_amount_exceeds', ['remaining' => number_format($remaining, 2)]),
            ])->withInput();
        }

        $hadPaymentsBefore = $contract->payments->isNotEmpty();

        $paymentType = $contract->isGovernmentPayment()
            ? ContractPaymentTypes::GOVERNMENT
            : ($contract->isFullPaymentPlan() ? ContractPaymentTypes::FULL : 'installment');

        $payment = null;

        DB::transaction(function () use (&$payment, $contract, $validated, $paymentType, $ledger) {
            $payment = ContractPayment::create([
                'sales_contract_id' => $contract->id,
                'payment_type' => $paymentType,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $ledger->syncContractPayment($payment);
        });

        $contract->syncPaymentStatus();

        // First payment unlocks designs stage (same behavior as ContractPaymentController).
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
        }

        AuditHelper::log(
            'create',
            'ContractPayment',
            $payment->id,
            'Recorded contract payment from cash-flow module'
        );

        return redirect()
            ->route('cash-flow.index')
            ->with('success', __('cash_flow.flash_contract_payment_created'));
    }

    public function destroy(Request $request, CashFlowLedgerService $ledger, ContractPayment $payment)
    {
        $this->authorizeCashFlow();

        $payment->loadMissing('contract.project');

        if (! $payment->contract) {
            return back()->withErrors(['payment' => __('cash_flow.error_payment_missing')]);
        }

        $contract = $payment->contract;
        $project = $contract->project;

        DB::transaction(function () use ($payment, $ledger, $contract, $project) {
            // Delete the payment record.
            $payment->delete();

            // Remove auto ledger entry created from this payment.
            $ledger->removeContractPayment($payment);

            $contract->syncPaymentStatus();

            // Optional: revert stage if it was the first payment for a contract that unlocks designs.
            if (
                $project
                && $contract->requiresFirstPaymentForDesigns()
                && $project->current_stage === 'architect'
                && ! $contract->payments()->exists()
            ) {
                $project->update([
                    'current_stage' => 'contracts',
                    'status' => 'pending',
                ]);
            }
        });

        AuditHelper::log(
            'delete',
            'ContractPayment',
            $payment->id,
            'Deleted contract payment from cash-flow module'
        );

        return redirect()
            ->route('cash-flow.index')
            ->with('success', __('cash_flow.flash_contract_payment_deleted'));
    }
}

