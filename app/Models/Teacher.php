<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_name',
        'employee_id',
        'email',
        'phone',
        'department',
        'position',
        'hire_date',
        'basic_salary',
        'status',
        'bank_account',
        'address'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'basic_salary' => 'decimal:2'
    ];

    /**
     * Get all salary payments for this teacher
     */
    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }

    /**
     * Get salary payments for a specific year
     */
    public function salaryPaymentsForYear($year)
    {
        return $this->salaryPayments()
            ->whereYear('payment_date', $year);
    }

    /**
     * Get salary payment for a specific month
     */
    public function salaryPaymentForMonth($year, $month)
    {
        return $this->salaryPayments()
            ->where('month', sprintf('%04d-%02d', $year, $month))
            ->first();
    }

    /**
     * Scope to get active teachers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get teachers by department
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope to get teachers by position
     */
    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'on_leave' => 'On Leave',
            'terminated' => 'Terminated'
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }

    /**
     * Get years of service
     */
    public function getYearsOfServiceAttribute()
    {
        return $this->hire_date->diffInYears(now());
    }

    /**
     * Get total salary paid this year
     */
    public function getTotalSalaryThisYearAttribute()
    {
        return $this->salaryPaymentsForYear(now()->year)
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * Get total salary paid last year
     */
    public function getTotalSalaryLastYearAttribute()
    {
        return $this->salaryPaymentsForYear(now()->year - 1)
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * Check if salary is paid for a specific month
     */
    public function isSalaryPaidForMonth($year, $month)
    {
        return $this->salaryPaymentForMonth($year, $month) !== null;
    }

    /**
     * Calculate monthly salary with allowances and deductions
     */
    public function calculateMonthlySalary($allowances = [], $deductions = [])
    {
        $totalAllowances = array_sum($allowances);
        $totalDeductions = array_sum($deductions);
        
        return $this->basic_salary + $totalAllowances - $totalDeductions;
    }

    /**
     * Generate unique employee ID
     */
    public static function generateEmployeeId($department = null)
    {
        $prefix = 'EMP';
        $year = now()->format('y');
        
        if ($department) {
            $deptCode = strtoupper(substr($department, 0, 3));
        } else {
            $deptCode = 'GEN';
        }
        
        // Get the next sequence number
        $lastTeacher = static::where('employee_id', 'like', $prefix . $year . $deptCode . '%')
                            ->orderBy('employee_id', 'desc')
                            ->first();
        
        if ($lastTeacher) {
            $lastSequence = (int) substr($lastTeacher->employee_id, -3);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }
        
        // Format: EMPYYDDDXXX (e.g., EMP24CSC001)
        $employeeId = $prefix . $year . $deptCode . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        while (static::where('employee_id', $employeeId)->exists()) {
            $nextSequence++;
            $employeeId = $prefix . $year . $deptCode . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
        }
        
        return $employeeId;
    }

    /**
     * Get unpaid months for current year
     */
    public function getUnpaidMonthsThisYear()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        
        $paidMonths = $this->salaryPaymentsForYear($currentYear)
            ->where('status', 'paid')
            ->pluck('month')
            ->map(function ($month) {
                return (int) substr($month, -2);
            })
            ->toArray();
        
        $unpaidMonths = [];
        for ($month = 1; $month <= $currentMonth; $month++) {
            if (!in_array($month, $paidMonths)) {
                $unpaidMonths[] = $month;
            }
        }
        
        return $unpaidMonths;
    }

    /**
     * Get monthly salary breakdown
     */
    public function getMonthlySalaryBreakdown($allowances = [], $deductions = [])
    {
        $breakdown = [
            'basic_salary' => $this->basic_salary,
            'allowances' => $allowances,
            'total_allowances' => array_sum($allowances),
            'deductions' => $deductions,
            'total_deductions' => array_sum($deductions),
            'gross_salary' => $this->basic_salary + array_sum($allowances),
            'net_salary' => $this->basic_salary + array_sum($allowances) - array_sum($deductions)
        ];
        
        return $breakdown;
    }

    /**
     * Get salary history for a specific year
     */
    public function getSalaryHistory($year = null)
    {
        $year = $year ?? now()->year;
        
        return $this->salaryPaymentsForYear($year)
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    /**
     * Check if teacher can receive salary for a month
     */
    public function canReceiveSalaryForMonth($year, $month)
    {
        // Check if teacher is active
        if ($this->status !== 'active') {
            return [false, 'Teacher is not active'];
        }
        
        // Check if salary already paid for this month
        if ($this->isSalaryPaidForMonth($year, $month)) {
            return [false, 'Salary already paid for this month'];
        }
        
        // Check if month is not in the future
        $targetDate = now()->setYear($year)->setMonth($month);
        if ($targetDate->isFuture()) {
            return [false, 'Cannot pay salary for future months'];
        }
        
        return [true, 'Teacher can receive salary'];
    }
}
