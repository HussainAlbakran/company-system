<?php

namespace App\Services;

use App\Helpers\AuditHelper;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmployeeAdvanceService
{
    public function __construct(
        protected CashFlowLedgerService $ledger
    ) {}

    public function issue(array $data, int $issuedBy): EmployeeAdvance
    {
        $installmentCount = (int) $data['installment_count'];

        if (! in_array($installmentCount, EmployeeAdvance::ALLOWED_INSTALLMENTS, true)) {
            throw ValidationException::withMessages([
                'installment_count' => [__('employee_advance.invalid_installments')],
            ]);
        }

        $employee = Employee::query()->findOrFail($data['employee_id']);
        $baseSalary = round((float) ($employee->salary ?? 0), 2);

        if ($baseSalary <= 0) {
            throw ValidationException::withMessages([
                'employee_id' => [__('employee_advance.requires_base_salary')],
            ]);
        }

        $activeExists = EmployeeAdvance::query()
            ->where('employee_id', $employee->id)
            ->where('status', EmployeeAdvance::STATUS_ACTIVE)
            ->exists();

        if ($activeExists) {
            throw ValidationException::withMessages([
                'employee_id' => [__('employee_advance.active_exists')],
            ]);
        }

        return DB::transaction(function () use ($data, $issuedBy, $installmentCount, $employee, $baseSalary) {
            $total = round((float) $data['total_amount'], 2);
            $schedule = AdvancePayrollService::computeInstallmentSchedule($total, $installmentCount);
            $installmentAmount = $schedule[0] ?? 0.0;

            if ($installmentAmount > $baseSalary) {
                throw ValidationException::withMessages([
                    'total_amount' => [__('employee_advance.installment_exceeds_salary', [
                        'installment' => number_format($installmentAmount, 2),
                        'salary' => number_format($baseSalary, 2),
                    ])],
                ]);
            }

            $now = now();
            $issuedAt = Carbon::parse($data['issued_at'] ?? $now->toDateString());
            $delayMonths = (int) ($data['repayment_delay_months'] ?? 0);

            if ($delayMonths < 0 || $delayMonths > EmployeeAdvance::MAX_REPAYMENT_DELAY_MONTHS) {
                throw ValidationException::withMessages([
                    'repayment_delay_months' => [__('employee_advance.invalid_repayment_delay')],
                ]);
            }

            $repaymentStart = AdvancePayrollService::computeRepaymentStart($issuedAt, $delayMonths);

            $advance = EmployeeAdvance::create([
                'employee_id' => $employee->id,
                'total_amount' => $total,
                'base_salary_at_issue' => $baseSalary,
                'installment_count' => $installmentCount,
                'installment_amount' => $installmentAmount,
                'installments_paid' => 0,
                'status' => EmployeeAdvance::STATUS_ACTIVE,
                'start_month' => $repaymentStart['month'],
                'start_year' => $repaymentStart['year'],
                'repayment_delay_months' => $delayMonths,
                'issued_at' => $issuedAt->toDateString(),
                'issued_by' => $issuedBy,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->ledger->syncEmployeeAdvance($advance);

            AuditHelper::log(
                'employee_advance_issued',
                'EmployeeAdvance',
                $advance->id,
                'سلفة '.$total.' على '.$installmentCount.' أقساط (قسط '.$installmentAmount.') — موظف #'.$employee->id,
                $issuedBy
            );

            return $advance;
        });
    }
}
