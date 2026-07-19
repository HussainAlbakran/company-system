<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeePayrollAdjustment;

class PayrollRegisterTotalService
{
    public function __construct(
        protected PayrollCalculationService $payrollCalculation
    ) {}

    public function calculateMonthTotal(int $month, int $year): float
    {
        $adjustments = EmployeePayrollAdjustment::query()
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy('employee_id');

        $total = 0.0;

        foreach (Employee::query()->get() as $employee) {
            $adjustment = $adjustments->get($employee->id);
            $row = $this->payrollCalculation->calculate($employee, $adjustment, $month, $year);
            $total += $row['final_salary'];
        }

        return round($total, 2);
    }
}
