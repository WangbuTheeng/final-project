<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_number',
        'title',
        'description',
        'category',
        'amount',
        'expense_date',
        'payment_method',
        'vendor_name',
        'vendor_contact',
        'receipt_number',
        'department_id',
        'approved_by',
        'approved_at',
        'status',
        'notes',
        'attachments',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'approved_at' => 'datetime',
        'attachments' => 'array'
    ];

    /**
     * Get the department this expense belongs to
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who created this expense
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this expense
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to get approved expenses
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get pending expenses
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get expenses by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get expenses by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * Generate expense number
     */
    public static function generateExpenseNumber()
    {
        $year = now()->year;
        $month = now()->format('m');
        $prefix = "EXP-{$year}{$month}-";

        $lastExpense = static::where('expense_number', 'like', $prefix . '%')
                            ->orderBy('expense_number', 'desc')
                            ->first();

        if ($lastExpense) {
            $lastNumber = intval(substr($lastExpense->expense_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get total amount by category
     */
    public static function getTotalByCategory($category, $startDate = null, $endDate = null)
    {
        $query = static::approved()->where('category', $category);

        if ($startDate && $endDate) {
            $query->whereBetween('expense_date', [$startDate, $endDate]);
        }

        return $query->sum('amount');
    }

    /**
     * Get monthly expenses
     */
    public static function getMonthlyExpenses($year, $month)
    {
        return static::approved()
                    ->whereYear('expense_date', $year)
                    ->whereMonth('expense_date', $month)
                    ->sum('amount');
    }

    /**
     * Check if expense can be edited
     */
    public function canBeEdited()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if expense can be approved
     */
    public function canBeApproved()
    {
        return $this->status === 'pending';
    }

    /**
     * Approve the expense
     */
    public function approve($approvedBy)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now()
        ]);
    }

    /**
     * Reject the expense
     */
    public function reject($rejectedBy, $reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $rejectedBy,
            'approved_at' => now(),
            'notes' => $this->notes . "\n\nRejection Reason: " . $reason
        ]);
    }
}
