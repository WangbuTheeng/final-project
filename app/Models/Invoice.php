<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'student_id',
        'academic_year_id',
        'semester',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'amount_paid',
        'balance',
        'status',
        'issue_date',
        'due_date',
        'paid_date',
        'notes',
        'line_items',
        'created_by'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'line_items' => 'array'
    ];

    /**
     * Get the student this invoice belongs to
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic year this invoice is for
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the user who created this invoice
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all payments for this invoice
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get completed payments for this invoice
     */
    public function completedPayments()
    {
        return $this->payments()->where('status', 'completed');
    }

    /**
     * Scope to get invoices by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get overdue invoices
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('status', ['sent', 'partially_paid']);
    }

    /**
     * Scope to get paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to get unpaid invoices
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'partially_paid']);
    }

    /**
     * Scope to get invoices by student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get invoices by academic year
     */
    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope to get invoices by semester
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'draft' => 'Draft',
            'sent' => 'Sent',
            'paid' => 'Paid',
            'partially_paid' => 'Partially Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled'
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }

    /**
     * Get semester display name
     */
    public function getSemesterDisplayAttribute()
    {
        $semesters = [
            'first' => 'First Semester',
            'second' => 'Second Semester',
            'both' => 'Both Semesters'
        ];

        return $semesters[$this->semester] ?? 'Unknown';
    }

    /**
     * Check if invoice is overdue
     */
    public function getIsOverdueAttribute()
    {
        return $this->due_date->isPast() && in_array($this->status, ['sent', 'partially_paid']);
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute()
    {
        return $this->total_amount - $this->amount_paid;
    }

    /**
     * Get payment percentage
     */
    public function getPaymentPercentageAttribute()
    {
        return $this->total_amount > 0 ? round(($this->amount_paid / $this->total_amount) * 100, 2) : 0;
    }

    /**
     * Update invoice status based on payments
     */
    public function updateStatus()
    {
        $totalPaid = $this->completedPayments()->sum('amount');
        
        if ($totalPaid >= $this->total_amount) {
            $this->status = 'paid';
            $this->paid_date = now();
        } elseif ($totalPaid > 0) {
            $this->status = 'partially_paid';
        } elseif ($this->due_date->isPast() && $this->status === 'sent') {
            $this->status = 'overdue';
        }
        
        $this->amount_paid = $totalPaid;
        $this->balance = $this->total_amount - $totalPaid;
        $this->save();
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber($academicYearId, $semester)
    {
        $academicYear = AcademicYear::find($academicYearId);
        $yearCode = $academicYear ? substr($academicYear->start_date->format('Y'), -2) : '00';
        
        $semesterCode = $semester === 'first' ? '1' : ($semester === 'second' ? '2' : '0');
        
        // Get the next sequence number for this year and semester
        $lastInvoice = static::where('invoice_number', 'like', 'INV' . $yearCode . $semesterCode . '%')
                            ->orderBy('invoice_number', 'desc')
                            ->first();
        
        if ($lastInvoice) {
            $lastSequence = (int) substr($lastInvoice->invoice_number, -5);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }
        
        // Format: INVYYSXXXXX (e.g., INV24100001)
        $invoiceNumber = 'INV' . $yearCode . $semesterCode . str_pad($nextSequence, 5, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        while (static::where('invoice_number', $invoiceNumber)->exists()) {
            $nextSequence++;
            $invoiceNumber = 'INV' . $yearCode . $semesterCode . str_pad($nextSequence, 5, '0', STR_PAD_LEFT);
        }
        
        return $invoiceNumber;
    }

    /**
     * Create invoice from fees for a student
     */
    public static function createFromFees(Student $student, $academicYearId, array $feeIds, $dueDate = null, $notes = null, $includeOutstanding = false, array $feeDescriptions = [], array $customExpenses = [])
    {
        return DB::transaction(function () use ($student, $academicYearId, $feeIds, $dueDate, $notes, $includeOutstanding, $feeDescriptions, $customExpenses) {
            $lineItems = [];
            $subtotal = 0;

            // Process regular fees
            if (!empty($feeIds)) {
                $fees = Fee::whereIn('id', $feeIds)->get();

                foreach ($fees as $fee) {
                    $description = isset($feeDescriptions[$fee->id]) && !empty($feeDescriptions[$fee->id])
                        ? $feeDescriptions[$fee->id]
                        : $fee->description;

                    $lineItems[] = [
                        'fee_id' => $fee->id,
                        'fee_name' => $fee->name,
                        'fee_code' => $fee->code,
                        'amount' => $fee->amount,
                        'quantity' => 1,
                        'description' => $description,
                        'type' => 'fee'
                    ];
                    $subtotal += $fee->amount;
                }
            }

            // Process custom expenses
            if (!empty($customExpenses)) {
                foreach ($customExpenses as $expense) {
                    if (!empty($expense['description']) && !empty($expense['amount'])) {
                        $lineItems[] = [
                            'fee_id' => null,
                            'fee_name' => $expense['description'],
                            'fee_code' => 'CUSTOM',
                            'amount' => (float) $expense['amount'],
                            'quantity' => 1,
                            'description' => $expense['description'],
                            'type' => 'custom_expense'
                        ];
                        $subtotal += (float) $expense['amount'];
                    }
                }
            }

            // Add outstanding balance if requested
            $outstandingBalance = 0;
            if ($includeOutstanding) {
                $outstandingBalance = $student->getOutstandingBalanceForYear($academicYearId);
                if ($outstandingBalance > 0) {
                    $lineItems[] = [
                        'fee_id' => null,
                        'fee_name' => 'Outstanding Balance',
                        'fee_code' => 'OUTSTANDING',
                        'amount' => $outstandingBalance,
                        'quantity' => 1,
                        'description' => 'Previous outstanding balance',
                        'type' => 'outstanding'
                    ];
                    $subtotal += $outstandingBalance;
                }
            }

            // Default semester to 'both' since fees no longer have semester specificity
            $semester = 'both';

            $invoice = static::create([
                'invoice_number' => static::generateInvoiceNumber($academicYearId, $semester),
                'student_id' => $student->id,
                'academic_year_id' => $academicYearId,
                'semester' => $semester,
                'subtotal' => $subtotal,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => $subtotal,
                'amount_paid' => 0,
                'balance' => $subtotal,
                'status' => 'draft',
                'issue_date' => now(),
                'due_date' => $dueDate ?? now()->addDays(30),
                'notes' => $notes,
                'line_items' => $lineItems,
                'created_by' => auth()->id()
            ]);

            return $invoice;
        });
    }
}
