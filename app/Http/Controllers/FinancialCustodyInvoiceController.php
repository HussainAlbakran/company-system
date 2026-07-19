<?php

namespace App\Http\Controllers;

use App\Models\FinancialCustody;
use App\Models\FinancialCustodyInvoice;
use App\Services\CustodyInvoiceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FinancialCustodyInvoiceController extends Controller
{
    public function __construct(
        protected CustodyInvoiceService $invoiceService
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $employee = $user->employee;

        if (! $employee) {
            return redirect()
                ->route('profile.show')
                ->with('error', __('custody_invoice.no_employee_profile'));
        }

        $custody = FinancialCustody::openForEmployee($employee->id);

        if (! $custody) {
            return view('custody-invoices.index', [
                'employee' => $employee,
                'custody' => null,
                'invoices' => collect(),
                'pendingTotal' => 0,
                'availableToRegister' => 0,
                'editingInvoice' => null,
                'editMaxTotal' => 0,
            ]);
        }

        $invoices = FinancialCustodyInvoice::query()
            ->where('financial_custody_id', $custody->id)
            ->where('employee_id', $employee->id)
            ->latest('invoice_date')
            ->latest('id')
            ->get();

        $pendingTotal = $this->invoiceService->pendingInvoicesTotal($custody);
        $availableToRegister = $this->invoiceService->availableForRegistration($custody);

        $editingInvoice = null;
        $editMaxTotal = 0;

        if ($request->filled('edit')) {
            $candidate = FinancialCustodyInvoice::query()
                ->whereKey($request->integer('edit'))
                ->where('financial_custody_id', $custody->id)
                ->where('employee_id', $employee->id)
                ->first();

            if ($candidate && $this->invoiceService->userCanEditInvoice($user, $candidate)) {
                $editingInvoice = $candidate;
                $editMaxTotal = $this->invoiceService->availableForRegistration($custody, $candidate->id);
            }
        }

        return view('custody-invoices.index', compact(
            'employee',
            'custody',
            'invoices',
            'pendingTotal',
            'availableToRegister',
            'editingInvoice',
            'editMaxTotal'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        abort_unless($user?->employee, 403);

        $validated = $this->validateInvoicePayload($request);

        try {
            $this->invoiceService->registerForEmployee(
                $user,
                $this->payloadFromValidated($validated),
                $request->file('attachment')
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()->with('success', __('custody_invoice.saved_success'));
    }

    public function update(Request $request, FinancialCustodyInvoice $invoice)
    {
        $user = auth()->user();
        abort_unless($user?->employee, 403);
        abort_unless($this->invoiceService->userCanEditInvoice($user, $invoice), 403);

        $validated = $this->validateInvoicePayload($request);

        try {
            $this->invoiceService->updateForEmployee(
                $user,
                $invoice,
                $this->payloadFromValidated($validated),
                $request->file('attachment')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('custody-invoices.index', ['edit' => $invoice->id])
                ->withErrors($e->errors())
                ->withInput();
        }

        return redirect()
            ->route('custody-invoices.index')
            ->with('success', __('custody_invoice.updated_success'));
    }

    public function attachment(FinancialCustodyInvoice $invoice)
    {
        $user = auth()->user();
        abort_unless($user, 403);
        abort_unless($this->invoiceService->userCanViewInvoice($user, $invoice), 403);
        abort_unless($invoice->hasAttachment(), 404);

        $filename = basename($invoice->attachment_original_name ?? 'invoice');

        return Storage::disk('local')->response(
            $invoice->attachment_path,
            $filename,
            ['Content-Disposition' => 'inline; filename="'.$filename.'"']
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateInvoicePayload(Request $request): array
    {
        $request->merge([
            'supplier_tax_number' => trim((string) $request->input('supplier_tax_number', '')) ?: null,
        ]);

        $validated = $request->validate([
            'invoice_day' => ['required', 'integer', 'min:1', 'max:31'],
            'invoice_month' => ['required', 'integer', 'min:1', 'max:12'],
            'invoice_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'supplier_tax_number' => ['nullable', 'string', 'regex:/^\d{15}$/'],
            'description' => ['nullable', 'string', 'max:2000'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,gif,pdf'],
        ], [
            'supplier_tax_number.regex' => __('custody_invoice.invalid_tax_number'),
        ]);

        $day = (int) $validated['invoice_day'];
        $month = (int) $validated['invoice_month'];
        $year = (int) $validated['invoice_year'];

        if (! checkdate($month, $day, $year)) {
            throw ValidationException::withMessages([
                'invoice_day' => [__('custody_invoice.invalid_date')],
            ]);
        }

        $date = Carbon::createFromDate($year, $month, $day)->toDateString();

        if (Carbon::parse($date)->isFuture()) {
            throw ValidationException::withMessages([
                'invoice_day' => [__('custody_invoice.future_date')],
            ]);
        }

        $validated['invoice_date'] = $date;

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function payloadFromValidated(array $validated): array
    {
        return [
            'invoice_date' => $validated['invoice_date'],
            'supplier_name' => $validated['supplier_name'],
            'supplier_tax_number' => $validated['supplier_tax_number'] ?? null,
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
        ];
    }
}
