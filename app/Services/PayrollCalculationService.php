<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeePayrollAdjustment;

class PayrollCalculationService
{
    public function __construct(
        protected AdvancePayrollService $advancePayroll
    ) {}

    /**
     * @return array{
     *     base_salary: float,
     *     housing: float,
     *     transport: float,
     *     other_allowances: float,
     *     allowances_total: float,
     *     overtime_hours: float,
     *     overtime_amount: float,
     *     leave_deduction: float,
     *     other_deduction: float,
     *     insurance_percent: float,
     *     insurance_deduction: float,
     *     advance_deduction: float,
     *     deductions_total: float,
     *     gross: float,
     *     final_salary: float
     * }
     */
    public function calculate(
        Employee $employee,
        ?EmployeePayrollAdjustment $adjustment,
        int $month,
        int $year,
        ?int $payrollRegisterId = null
    ): array {
        $base = (float) ($employee->salary ?? 0);
        $housing = (float) ($employee->housing_allowance ?? 0);
        $transport = (float) ($employee->transportation_allowance ?? 0);
        $otherAllowances = (float) ($employee->travel_allowance ?? 0)
            + (float) ($employee->risk_allowance ?? 0)
            + (float) ($employee->transfer_allowance ?? 0)
            + (float) ($employee->overtime_allowance ?? 0);

        $allowancesTotal = $housing + $transport + $otherAllowances;

        $overtimeHours = (float) ($adjustment->overtime_hours ?? 0);
        $leaveDeductionDays = (float) ($adjustment->leave_deduction_days ?? 0);
        $otherDeduction = (float) ($adjustment->other_deduction ?? 0);

        $hourlyRate = $base > 0 ? $base / 240 : 0;
        $overtimeAmount = $overtimeHours * $hourlyRate * 1.5;
        $dailyRate = $base > 0 ? $base / 30 : 0;
        $leaveDeduction = $leaveDeductionDays * $dailyRate;

        $insurancePercent = max(0, min(100, (float) ($employee->insurance_deduction_percent ?? 0)));
        $insuranceDeduction = round($base * ($insurancePercent / 100), 2);

        $advanceDeduction = $this->advancePayroll->deductionForPayrollRegister(
            $employee,
            $month,
            $year,
            $payrollRegisterId
        );

        $gross = $base + $allowancesTotal + $overtimeAmount;
        $deductionsTotal = $leaveDeduction + $otherDeduction + $insuranceDeduction + $advanceDeduction;
        $finalSalary = round($gross - $deductionsTotal, 2);

        return [
            'base_salary' => round($base, 2),
            'housing' => round($housing, 2),
            'transport' => round($transport, 2),
            'other_allowances' => round($otherAllowances, 2),
            'allowances_total' => round($allowancesTotal, 2),
            'overtime_hours' => round($overtimeHours, 2),
            'overtime_amount' => round($overtimeAmount, 2),
            'leave_deduction' => round($leaveDeduction, 2),
            'other_deduction' => round($otherDeduction, 2),
            'insurance_percent' => round($insurancePercent, 2),
            'insurance_deduction' => $insuranceDeduction,
            'advance_deduction' => round($advanceDeduction, 2),
            'deductions_total' => round($deductionsTotal, 2),
            'gross' => round($gross, 2),
            'final_salary' => $finalSalary,
        ];
    }
}
