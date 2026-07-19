<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeAdvancePayment;
use App\Models\PayrollRegister;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdvancePayrollService
{
    public function __construct(
        protected CashFlowLedgerService $ledger
    ) {}

    /**
     * قسط شهري = مبلغ السلفة ÷ عدد الأقساط (مثال: 1200 ÷ 6 = 200).
     *
     * @return list<float>
     */
    public static function computeInstallmentSchedule(float $total, int $installmentCount): array
    {
        if ($installmentCount < 1 || $total <= 0) {
            return [];
        }

        $perMonth = round($total / $installmentCount, 2);
        $schedule = array_fill(0, $installmentCount, $perMonth);
        $diff = round($total - array_sum($schedule), 2);
        $schedule[$installmentCount - 1] = round($schedule[$installmentCount - 1] + $diff, 2);

        return $schedule;
    }

    public static function computeInstallmentAmount(float $total, int $installmentCount): float
    {
        $schedule = self::computeInstallmentSchedule($total, $installmentCount);

        return $schedule[0] ?? 0.0;
    }

    /**
     * أول شهر سداد = شهر تاريخ السلفة + عدد أشهر التأجيل.
     *
     * @return array{month: int, year: int}
     */
    public static function computeRepaymentStart(Carbon $issuedAt, int $delayMonths): array
    {
        $start = $issuedAt->copy()->startOfMonth()->addMonths(max(0, $delayMonths));

        return [
            'month' => (int) $start->month,
            'year' => (int) $start->year,
        ];
    }

    public function activeAdvancesFor(Employee $employee): Collection
    {
        return EmployeeAdvance::query()
            ->where('employee_id', $employee->id)
            ->where('status', EmployeeAdvance::STATUS_ACTIVE)
            ->orderByDesc('issued_at')
            ->get();
    }

    /**
     * خصم السلفة على مسير محدد: مسجّل إن وُجد، وإلا القسط المستحق للمسير الجاري.
     */
    public function deductionForPayrollRegister(
        Employee $employee,
        int $month,
        int $year,
        ?int $payrollRegisterId = null
    ): float {
        $recorded = $this->sumRecordedPaymentsForPeriod($employee->id, $month, $year, $payrollRegisterId);
        if ($recorded > 0) {
            return round($recorded, 2);
        }

        return $this->plannedDeductionForMonth($employee, $month, $year, $payrollRegisterId);
    }

    /**
     * خصم السلفة في الملف الشخصي: صفر إن لم توجد سلفة نشطة بأقساط متبقية.
     */
    public function profilePlannedDeduction(Employee $employee, int $month, int $year): float
    {
        if ($this->activeAdvancesFor($employee)->isEmpty()) {
            return 0.0;
        }

        return $this->plannedDeductionForMonth($employee, $month, $year);
    }

    public function plannedDeductionForMonth(
        Employee $employee,
        int $month,
        int $year,
        ?int $payrollRegisterId = null
    ): float {
        $total = 0.0;

        foreach ($this->activeAdvancesFor($employee) as $advance) {
            if ($this->isInstallmentDueOnRegister($advance, $month, $year, $payrollRegisterId)) {
                $total += $this->installmentAmountFor($advance);
            }
        }

        return round($total, 2);
    }

    /**
     * قسط مستحق على هذا المسير ما دامت السلفة نشطة وباقي أقساط — ينتقل من مسير لمسير حتى الإغلاق.
     */
    public function isInstallmentDueOnRegister(
        EmployeeAdvance $advance,
        int $month,
        int $year,
        ?int $payrollRegisterId = null
    ): bool {
        if (! $advance->isActive()) {
            return false;
        }

        if ($advance->remainingInstallments() <= 0 || $advance->remainingBalance() <= 0.01) {
            return false;
        }

        if ($this->hasPaymentForRegister($advance, $month, $year, $payrollRegisterId)) {
            return false;
        }

        return $this->isRegisterOnOrAfterRepaymentStart($advance, $month, $year);
    }

    public function installmentAmountFor(EmployeeAdvance $advance): float
    {
        $remaining = $advance->remainingBalance();
        $left = $advance->remainingInstallments();

        if ($left <= 0 || $remaining <= 0) {
            return 0.0;
        }

        if ($left === 1) {
            return $remaining;
        }

        return min((float) $advance->installment_amount, $remaining);
    }

    /**
     * عند اعتماد مسير الرواتب: تسجيل قسط واحد لكل سلفة نشطة مستحقة على هذا المسير.
     *
     * @return int عدد دفعات السلف المسجّلة
     */
    public function recordInstallmentsOnPayrollApproval(PayrollRegister $register, ?int $recordedBy): int
    {
        $month = (int) $register->month;
        $year = (int) $register->year;
        $recorded = 0;

        $advances = EmployeeAdvance::query()
            ->where('status', EmployeeAdvance::STATUS_ACTIVE)
            ->get();

        foreach ($advances as $advance) {
            if (! $this->isInstallmentDueOnRegister($advance, $month, $year, $register->id)) {
                continue;
            }

            $amount = $this->installmentAmountFor($advance);

            if ($amount <= 0) {
                continue;
            }

            $payment = EmployeeAdvancePayment::create([
                'employee_advance_id' => $advance->id,
                'payroll_register_id' => $register->id,
                'month' => $month,
                'year' => $year,
                'amount' => $amount,
                'recorded_by' => $recordedBy,
                'recorded_at' => now(),
            ]);

            $advance->installments_paid = (int) $advance->installments_paid + 1;

            if ($advance->installments_paid >= $advance->installment_count
                || $advance->remainingBalance() <= 0.01) {
                $advance->status = EmployeeAdvance::STATUS_COMPLETED;
            }

            $advance->save();

            $advance->refresh();
            $this->ledger->syncAdvancePayment($payment);
            $recorded++;
        }

        return $recorded;
    }

    protected function sumRecordedPaymentsForPeriod(
        int $employeeId,
        int $month,
        int $year,
        ?int $payrollRegisterId = null
    ): float {
        $query = EmployeeAdvancePayment::query()
            ->whereHas('advance', fn ($q) => $q->where('employee_id', $employeeId));

        if ($payrollRegisterId) {
            $query->where('payroll_register_id', $payrollRegisterId);
        } else {
            $query->where('month', $month)->where('year', $year);
        }

        return (float) $query->sum('amount');
    }

    protected function isRegisterOnOrAfterRepaymentStart(EmployeeAdvance $advance, int $month, int $year): bool
    {
        $startYear = (int) $advance->start_year;
        $startMonth = (int) $advance->start_month;

        if ($year > $startYear) {
            return true;
        }

        if ($year < $startYear) {
            return false;
        }

        return $month >= $startMonth;
    }

    protected function hasPaymentForRegister(
        EmployeeAdvance $advance,
        int $month,
        int $year,
        ?int $payrollRegisterId = null
    ): bool {
        $query = EmployeeAdvancePayment::query()
            ->where('employee_advance_id', $advance->id);

        if ($payrollRegisterId) {
            return $query->where('payroll_register_id', $payrollRegisterId)->exists();
        }

        return $query
            ->where('month', $month)
            ->where('year', $year)
            ->exists();
    }
}
