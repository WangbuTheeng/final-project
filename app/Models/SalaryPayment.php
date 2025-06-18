<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'month',
        'amount',
        'payment_date',
        'status',
        'notes',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'approved_at' => 'datetime'
    ];

    /**
     * Get the teacher this salary payment belongs to
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the user who approved this salary payment
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to get payments by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get paid salary payments
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to get pending salary payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get payments by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('payment_date', $year);
    }

    /**
     * Scope to get payments by month
     */
    public function scopeByMonth($query, $month)
    {
        return $query->where('month', $month);
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled'
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }

    /**
     * Get month display name
     */
    public function getMonthDisplayAttribute()
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m', $this->month);
        return $date->format('F Y');
    }

    /**
     * Get year from month
     */
    public function getYearAttribute()
    {
        return (int) substr($this->month, 0, 4);
    }

    /**
     * Get month number from month
     */
    public function getMonthNumberAttribute()
    {
        return (int) substr($this->month, -2);
    }
}
