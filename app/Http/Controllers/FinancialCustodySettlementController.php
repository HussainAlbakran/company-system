<?php

namespace App\Http\Controllers;

use App\Models\FinancialCustody;
use App\Models\FinancialCustodyInvoice;
use App\Models\FinancialCustodySettlement;
use App\Services\CustodySettlementService;
use App\Services\CustodyInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinancialCustodySettlementController extends Controller
{
    public function __construct(
        protected CustodySettlementService $settlementService,
        protected CustodyInvoiceService $invoiceService
    ) {}

    protected function authorizeFinance(): void
    {
        abort_unless(auth()->check() && auth()->user()->canAccessCashFlowModule(), 403);
    }

    public function index()
    {
        $this->authorizeFinance();

        $custodies = FinancialCustody::query()
            ->with(['employee', 'settlements' => fn ($q) => $q->latest('id')->limit(1)])
            ->where('status', FinancialCustody::STATUS_OPEN)
            ->latest('issued_at')
            ->get();

        return view('custody-settlements.index', compact('custodies'));
    }

    public function records(Request $request)
    {
        $this->authorizeFinance();

        $filters = $request->validate([
            'employee' => ['nullable', 'string', 'max:120'],
            'code' => ['nullable', 'string', 'max:20'],
        ]);

        $query = FinancialCustodySettlement::query()
            ->with(['employee', 'approver'])
            ->where('status', FinancialCustodySettlement::STATUS_APPROVED)
            ->whereNotNull('sequence_number');

        if (! empty($filters['employee'])) {
            $name = trim($filters['employee']);
            $query->whereHas('employee', fn ($q) => $q->where('name', 'like', '%'.$name.'%'));
        }

        if (! empty($filters['code'])) {
            $digits = preg_replace('/\D/', '', $filters['code']) ?? '';
            if ($digits !== '') {
                if (strlen($digits) >= 5) {
                    $query->where('settlement_year', (int) substr($digits, 0, 2))
                        ->where('sequence_number', (int) substr($digits, 2));
                } else {
                    $query->whereRaw(
                        "CONCAT(LPAD(settlement_year, 2, '0'), LPAD(sequence_number, 3, '0')) LIKE ?",
                        ['%'.$digits.'%']
                    );
                }
            }
        }

        $settlements = $query
            ->orderByDesc('approved_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('custody-settlements.records', [
            'settlements' => $settlements,
            'filters' => $filters,
        ]);
    }

    public function open(FinancialCustody $financialCustody)
    {
        $this->authorizeFinance();

        if (! $financialCustody->isOpen()) {
            return redirect()
                ->route('custody-settlements.index')
                ->with('error', __('financial_custody.already_closed'));
        }

        $settlement = $this->settlementService->openOrCreateDraft($financialCustody, (int) auth()->id());

        return redirect()->route('custody-settlements.show', $settlement);
    }

    public function show(FinancialCustodySettlement $settlement)
    {
        $this->authorizeFinance();

        if ($settlement->isDraft()) {
            $this->settlementService->attachPendingInvoices($settlement);
        }

        $settlement->load(['employee', 'invoices', 'custody', 'approver']);

        return view('custody-settlements.show', compact('settlement'));
    }

    public function update(Request $request, FinancialCustodySettlement $settlement)
    {
        $this->authorizeFinance();

        $validated = $request->validate([
            'settlement_date' => ['nullable', 'date'],
            'lines' => ['present', 'array'],
            'lines.*.id' => ['nullable', 'integer'],
            'lines.*.invoice_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'lines.*.invoice_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'lines.*.invoice_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'lines.*.supplier_name' => ['nullable', 'string', 'max:255'],
            'lines.*.supplier_tax_number' => ['nullable', 'string', 'max:50'],
            'lines.*.classification' => ['nullable', 'string', 'max:120'],
            'lines.*.description' => ['nullable', 'string', 'max:2000'],
            'lines.*.amount' => ['nullable', 'numeric', 'min:0'],
            'lines.*.tax_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $lines = $this->settlementService->mergeMissingInvoicesIntoLines(
            $settlement,
            $validated['lines'] ?? []
        );

        $lines = collect($lines)->map(function (array $line) {
            if (! empty($line['invoice_year']) && ! empty($line['invoice_month']) && ! empty($line['invoice_day'])) {
                try {
                    $line['invoice_date'] = sprintf(
                        '%04d-%02d-%02d',
                        (int) $line['invoice_year'],
                        (int) $line['invoice_month'],
                        (int) $line['invoice_day']
                    );
                } catch (\Throwable) {
                    $line['invoice_date'] = now()->toDateString();
                }
            } else {
                $line['invoice_date'] = now()->toDateString();
            }

            return $line;
        })->all();

        try {
            $settlement = $this->settlementService->updateDraft(
                $settlement,
                $lines,
                $validated['settlement_date'] ?? null,
                (int) auth()->id()
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        foreach ($request->allFiles() as $key => $file) {
            if (str_starts_with($key, 'line_attachment_')) {
                $invoiceId = (int) str_replace('line_attachment_', '', $key);
                $invoice = FinancialCustodyInvoice::query()->find($invoiceId);
                if ($invoice && $file) {
                    try {
                        $this->settlementService->uploadLineAttachment($settlement, $invoice, $file);
                    } catch (\InvalidArgumentException $e) {
                        return back()->with('error', $e->getMessage());
                    }
                }
            }
        }

        return redirect()
            ->route('custody-settlements.show', $settlement)
            ->with('success', __('custody_settlement.saved_success'));
    }

    public function approve(FinancialCustodySettlement $settlement)
    {
        $this->authorizeFinance();

        try {
            $result = $this->settlementService->approve($settlement, (int) auth()->id());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        $approved = $result['settlement'];
        $message = $result['carryover_target']
            ? __('custody_settlement.approved_success_with_carryover', [
                'code' => $approved->referenceCode(),
                'amount' => number_format((float) $result['carryover_amount'], 2),
                'target' => $result['carryover_target']->id,
            ])
            : __('custody_settlement.approved_success', ['code' => $approved->referenceCode()]);

        return redirect()
            ->route('custody-settlements.show', $approved)
            ->with('success', $message);
    }

    public function uploadAttachment(
        Request $request,
        FinancialCustodySettlement $settlement,
        FinancialCustodyInvoice $invoice
    ) {
        $this->authorizeFinance();

        $validated = $request->validate([
            'attachment' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,gif,pdf'],
        ]);

        try {
            $this->settlementService->uploadLineAttachment($settlement, $invoice, $validated['attachment']);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', __('custody_settlement.attachment_saved'));
    }

    public function attachment(FinancialCustodyInvoice $invoice)
    {
        $this->authorizeFinance();
        abort_unless($invoice->hasAttachment(), 404);

        return Storage::disk('local')->response(
            $invoice->attachment_path,
            $invoice->attachment_original_name ?? 'invoice'
        );
    }
}
