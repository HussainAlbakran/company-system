<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\FinancialCustody;
use App\Services\FinancialCustodyService;
use Illuminate\Http\Request;

class FinancialCustodyController extends Controller
{
    public function __construct(
        protected FinancialCustodyService $custodyService
    ) {}

    protected function authorizeFinanceModule(): void
    {
        abort_unless(auth()->check() && auth()->user()->canAccessCashFlowModule(), 403);
    }

    public function index(Request $request)
    {
        $this->authorizeFinanceModule();

        $custodies = FinancialCustody::query()
            ->with(['employee.department', 'issuer'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('employee_id'), fn ($q) => $q->where('employee_id', $request->employee_id))
            ->latest('issued_at')
            ->paginate(20)
            ->withQueryString();

        $employees = Employee::query()->orderBy('name')->get(['id', 'name']);

        return view('financial-custodies.index', compact('custodies', 'employees'));
    }

    public function create()
    {
        $this->authorizeFinanceModule();

        $employees = Employee::query()->orderBy('name')->get();

        return view('financial-custodies.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->authorizeFinanceModule();

        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'issued_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $custody = $this->custodyService->issue($validated, (int) auth()->id());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        $message = (float) $custody->carried_over_amount > 0
            ? __('financial_custody.issued_success_with_carryover', [
                'new' => number_format($custody->newCashAmount(), 2),
                'carryover' => number_format((float) $custody->carried_over_amount, 2),
                'total' => number_format((float) $custody->amount_issued, 2),
            ])
            : __('financial_custody.issued_success');

        return redirect()
            ->route('financial-custodies.index')
            ->with('success', $message);
    }

    public function show(FinancialCustody $financialCustody)
    {
        $this->authorizeFinanceModule();

        $financialCustody->load(['employee.department', 'issuer', 'transactions.recorder']);

        return view('financial-custodies.show', ['custody' => $financialCustody]);
    }

    public function settleFull(Request $request, FinancialCustody $financialCustody)
    {
        $this->authorizeFinanceModule();

        $validated = $request->validate([
            'purchase_description' => ['required', 'string', 'min:3', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->custodyService->settleFull(
                $financialCustody,
                $validated['purchase_description'],
                $validated['notes'] ?? null,
                (int) auth()->id()
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()
            ->route('financial-custodies.show', $financialCustody)
            ->with('success', __('financial_custody.full_settled_success'));
    }

    public function settlePartial(Request $request, FinancialCustody $financialCustody)
    {
        $this->authorizeFinanceModule();

        $validated = $request->validate([
            'amount_spent' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'purchase_description' => ['required', 'string', 'min:3', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->custodyService->settlePartial(
                $financialCustody,
                (float) $validated['amount_spent'],
                $validated['purchase_description'],
                $validated['notes'] ?? null,
                (int) auth()->id()
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()
            ->route('financial-custodies.show', $financialCustody)
            ->with('success', __('financial_custody.partial_settled_success'));
    }

    public function returnRemaining(Request $request, FinancialCustody $financialCustody)
    {
        $this->authorizeFinanceModule();

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->custodyService->returnRemaining(
                $financialCustody,
                $validated['notes'] ?? null,
                (int) auth()->id()
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()
            ->route('financial-custodies.show', $financialCustody)
            ->with('success', __('financial_custody.return_success'));
    }
}
