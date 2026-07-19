<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\AssetMaintenanceLog;
use App\Models\CashFlowEntry;
use App\Services\CashFlowLedgerService;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    public function __construct(
        protected CashFlowLedgerService $ledger
    ) {}

    protected function authorizeCashFlow(): void
    {
        if (! auth()->check() || ! auth()->user()->canAccessCashFlowModule()) {
            abort(403, __('cash_flow.unauthorized'));
        }
    }

    public function index(Request $request)
    {
        $this->authorizeCashFlow();

        $this->ledger->syncAllMissing();

        $baseQuery = CashFlowEntry::query();

        if ($request->filled('type') && in_array($request->type, ['income', 'expense', 'neutral'], true)) {
            $baseQuery->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $baseQuery->whereDate('entry_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $baseQuery->whereDate('entry_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $baseQuery->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('category', 'like', $term)
                    ->orWhere('notes', 'like', $term);
            });
        }

        $totalsQuery = clone $baseQuery;

        $totalIncome = (float) (clone $totalsQuery)->where('type', CashFlowEntry::TYPE_INCOME)->sum('amount');
        $totalExpense = (float) (clone $totalsQuery)->where('type', CashFlowEntry::TYPE_EXPENSE)->sum('amount');
        $totalNeutral = (float) (clone $totalsQuery)->where('type', CashFlowEntry::TYPE_NEUTRAL)->sum('amount');
        $balance = $totalIncome - $totalExpense + $totalNeutral;

        $expenseBreakdown = $this->buildCategoryBreakdown(clone $baseQuery, CashFlowEntry::TYPE_EXPENSE);
        $incomeBreakdown = $this->buildCategoryBreakdown(clone $baseQuery, CashFlowEntry::TYPE_INCOME);

        $entries = (clone $baseQuery)
            ->with('recorder')
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('cash-flow.index', compact(
            'entries',
            'totalIncome',
            'totalExpense',
            'totalNeutral',
            'balance',
            'expenseBreakdown',
            'incomeBreakdown'
        ));
    }

    public function maintenance(Request $request)
    {
        $this->authorizeCashFlow();

        $logs = AssetMaintenanceLog::query()
            ->with(['asset', 'recorder'])
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('maintenance_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('maintenance_date', '<=', $request->date_to))
            ->orderByDesc('maintenance_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $sumQuery = AssetMaintenanceLog::query();
        if ($request->filled('date_from')) {
            $sumQuery->whereDate('maintenance_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $sumQuery->whereDate('maintenance_date', '<=', $request->date_to);
        }
        $totalMaintenance = (float) $sumQuery->sum('maintenance_cost');

        return view('cash-flow.maintenance', compact('logs', 'totalMaintenance'));
    }

    public function store(Request $request)
    {
        $this->authorizeCashFlow();

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $entry = CashFlowEntry::create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'category' => $validated['category'] ?? CashFlowEntry::CATEGORY_MANUAL,
            'amount' => $validated['amount'],
            'entry_date' => $validated['entry_date'],
            'notes' => $validated['notes'] ?? null,
            'source_type' => CashFlowEntry::SOURCE_MANUAL,
            'source_id' => null,
            'recorded_by' => auth()->id(),
        ]);

        $typeLabel = $entry->isIncome() ? 'مدخول' : 'صرف';

        AuditHelper::log(
            'create',
            'CashFlowEntry',
            $entry->id,
            $typeLabel . ': ' . $entry->title . ' — ' . number_format((float) $entry->amount, 2)
        );

        return redirect()
            ->route('cash-flow.index', $request->only(['type', 'date_from', 'date_to', 'search']))
            ->with('success', __('cash_flow.flash_created'));
    }

    public function destroy(Request $request, CashFlowEntry $entry)
    {
        $this->authorizeCashFlow();

        if ($entry->isAuto()) {
            return redirect()
                ->route('cash-flow.index', $request->only(['type', 'date_from', 'date_to', 'search']))
                ->with('error', __('cash_flow.cannot_delete_auto'));
        }

        $title = $entry->title;
        $amount = $entry->amount;
        $entryId = $entry->id;

        $entry->delete();

        AuditHelper::log(
            'delete',
            'CashFlowEntry',
            $entryId,
            'حذف حركة: ' . $title . ' — ' . number_format((float) $amount, 2)
        );

        return redirect()
            ->route('cash-flow.index', $request->only(['type', 'date_from', 'date_to', 'search']))
            ->with('success', __('cash_flow.flash_deleted'));
    }

    /**
     * @return list<array{category: string, amount: float, url: string|null}>
     */
    protected function buildCategoryBreakdown($query, string $type): array
    {
        $rows = (clone $query)
            ->where('type', $type)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $linkLabels = [
            CashFlowEntry::CATEGORY_MAINTENANCE => __('cash_flow.btn_maintenance_list'),
            CashFlowEntry::CATEGORY_PAYROLL => __('cash_flow.btn_payroll_list'),
            CashFlowEntry::CATEGORY_CONTRACTS => __('cash_flow.btn_contracts_list'),
            CashFlowEntry::CATEGORY_PURCHASES => __('cash_flow.btn_purchases_list'),
            CashFlowEntry::CATEGORY_FINANCIAL_CUSTODY => __('cash_flow.btn_custody_records'),
        ];

        $user = auth()->user();

        $links = [
            CashFlowEntry::CATEGORY_MAINTENANCE => route('cash-flow.maintenance'),
            CashFlowEntry::CATEGORY_PAYROLL => $user?->canAccessHRModule()
                ? route('employees.payroll-register')
                : null,
            CashFlowEntry::CATEGORY_CONTRACTS => $user?->canAccessContractsModule()
                ? route('sales-contracts.index')
                : null,
            CashFlowEntry::CATEGORY_PURCHASES => $user?->canAccessGeneralPurchasesModule()
                ? route('general-purchases.index')
                : ($user?->canAccessContractPurchasesModule()
                    ? route('purchases.index')
                    : null),
            CashFlowEntry::CATEGORY_FINANCIAL_CUSTODY => $user?->canAccessCashFlowModule()
                ? route('financial-custodies.index')
                : null,
            CashFlowEntry::CATEGORY_MANUAL => null,
        ];

        return $rows->map(function ($row) use ($links, $linkLabels, $type) {
            $category = $row->category ?: CashFlowEntry::CATEGORY_MANUAL;

            return [
                'category' => $category,
                'amount' => (float) $row->total,
                'url' => $links[$category] ?? null,
                'link_label' => $linkLabels[$category] ?? __('cash_flow.view_category_list', ['category' => $category]),
            ];
        })->values()->all();
    }
}
