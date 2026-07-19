<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\EmployeePayrollAdjustment;
use App\Models\FinancialCustody;
use App\Models\Leave;
use Carbon\Carbon;

class EmployeeSelfProfileService
{
    public function __construct(
        protected PayrollCalculationService $payrollCalculation,
        protected AdvancePayrollService $advancePayroll
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function build(Employee $employee, ?Carbon $asOf = null): array
    {
        $asOf ??= Carbon::now();
        $month = (int) $asOf->month;
        $year = (int) $asOf->year;

        $adjustment = EmployeePayrollAdjustment::query()
            ->where('employee_id', $employee->id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        $payrollRow = $this->payrollCalculation->calculate($employee, $adjustment, $month, $year);

        $advanceDeduction = $this->advancePayroll->profilePlannedDeduction($employee, $month, $year);
        $leaveDeduction = $payrollRow['leave_deduction'];
        $otherDeduction = $payrollRow['other_deduction'];
        $deductionsTotal = round($leaveDeduction + $otherDeduction + $advanceDeduction, 2);
        $finalSalary = round($payrollRow['gross'] - $deductionsTotal, 2);

        $payroll = [
            'base_salary' => $payrollRow['base_salary'],
            'allowances_total' => $payrollRow['allowances_total'],
            'allowances_breakdown' => [
                'housing' => $payrollRow['housing'],
                'transportation' => $payrollRow['transport'],
                'other' => $payrollRow['other_allowances'],
            ],
            'overtime_hours' => $payrollRow['overtime_hours'],
            'overtime_amount' => $payrollRow['overtime_amount'],
            'deductions_total' => $deductionsTotal,
            'deductions_breakdown' => [
                'leave' => $leaveDeduction,
                'other' => $otherDeduction,
                'advance' => $advanceDeduction,
            ],
            'final_salary' => $finalSalary,
        ];

        $recentLeaves = Leave::query()
            ->where('employee_id', $employee->id)
            ->latest()
            ->limit(8)
            ->get();

        $openCustody = FinancialCustody::openForEmployee($employee->id);
        if ($openCustody) {
            $openCustody->load([
                'transactions' => fn ($q) => $q->with('recorder')->orderBy('recorded_at'),
            ]);
        }

        $custodyHistory = FinancialCustody::query()
            ->where('employee_id', $employee->id)
            ->with(['transactions' => fn ($q) => $q->with('recorder')->orderBy('recorded_at')])
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->get();

        $advances = EmployeeAdvance::query()
            ->where('employee_id', $employee->id)
            ->with(['payments' => fn ($q) => $q->with('payrollRegister')->orderByDesc('recorded_at')])
            ->latest('issued_at')
            ->limit(5)
            ->get();

        return [
            'payroll' => $payroll,
            'recent_leaves' => $recentLeaves,
            'payroll_month' => $month,
            'payroll_year' => $year,
            'open_custody' => $openCustody,
            'custody_history' => $custodyHistory,
            'advances' => $advances,
        ];
    }
}
