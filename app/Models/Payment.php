<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_reference',
        'invoice_id',
        'student_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'payment_date',
        'description',
        'payment_details',
        'received_by',
        'verified_by',
        'verified_at',
        'notes'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (Payment $payment) {
            if ($payment->invoice_id) {
                $payment->updateInvoiceStatus();
            }
        });

        static::updated(function (Payment $payment) {
            if ($payment->invoice_id) {
                $payment->updateInvoiceStatus();
            }
        });

        static::deleted(function (Payment $payment) {
            if ($payment->invoice_id) {
                $payment->updateInvoiceStatus();
            }
        });
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'verified_at' => 'datetime',
        'payment_details' => 'array'
    ];

    /**
     * Get the invoice this payment belongs to
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the student who made this payment
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who received this payment
     */
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the user who verified this payment
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope to get payments by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get payments by method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope to get payments by student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to get payments by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded'
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodDisplayAttribute()
    {
        $methods = [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'card' => 'Card Payment',
            'mobile_money' => 'Mobile Money',
            'cheque' => 'Cheque',
            'online' => 'Online Payment'
        ];

        return $methods[$this->payment_method] ?? 'Unknown';
    }

    /**
     * Check if payment is verified
     */
    public function getIsVerifiedAttribute()
    {
        return !is_null($this->verified_at) && !is_null($this->verified_by);
    }

    /**
     * Check if payment can be verified
     */
    public function canBeVerified()
    {
        return $this->status === 'pending' && !$this->is_verified;
    }

    /**
     * Check if payment can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'failed']);
    }

    /**
     * Verify payment
     */
    public function verify($verifiedBy = null)
    {
        if (!$this->canBeVerified()) {
            throw new \Exception('Payment cannot be verified');
        }

        return DB::transaction(function () use ($verifiedBy) {
            $this->update([
                'status' => 'completed',
                'verified_by' => $verifiedBy ?? auth()->id(),
                'verified_at' => now()
            ]);

            // Update invoice status
            $this->invoice->updateStatus();

            return $this;
        });
    }

    /**
     * Cancel payment
     */
    public function cancel($reason = null)
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('Payment cannot be cancelled');
        }

        return DB::transaction(function () use ($reason) {
            $this->update([
                'status' => 'cancelled',
                'notes' => $this->notes . ($reason ? "\nCancellation reason: " . $reason : '')
            ]);

            // Update invoice status
            $this->invoice->updateStatus();

            return $this;
        });
    }

    /**
     * Generate unique payment reference
     */
    public static function generatePaymentReference()
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        
        // Get the next sequence number for today
        $lastPayment = static::where('payment_reference', 'like', $prefix . $date . '%')
                            ->orderBy('payment_reference', 'desc')
                            ->first();
        
        if ($lastPayment) {
            $lastSequence = (int) substr($lastPayment->payment_reference, -4);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }
        
        // Format: PAYYYYYMMDDXXXX (e.g., PAY202412170001)
        $reference = $prefix . $date . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        while (static::where('payment_reference', $reference)->exists()) {
            $nextSequence++;
            $reference = $prefix . $date . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        }
        
        return $reference;
    }

    /**
     * Create payment for invoice
     */
    public static function createForInvoice(Invoice $invoice, array $paymentData)
    {
        return DB::transaction(function () use ($invoice, $paymentData) {
            $payment = static::create([
                'payment_reference' => static::generatePaymentReference(),
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'amount' => $paymentData['amount'],
                'payment_method' => $paymentData['payment_method'],
                'transaction_id' => $paymentData['transaction_id'] ?? null,
                'status' => $paymentData['status'] ?? 'pending',
                'payment_date' => $paymentData['payment_date'] ?? now(),
                'description' => $paymentData['description'] ?? "Payment for invoice {$invoice->invoice_number}",
                'payment_details' => $paymentData['payment_details'] ?? null,
                'received_by' => $paymentData['received_by'] ?? auth()->id(),
                'notes' => $paymentData['notes'] ?? null
            ]);

            // If payment is completed, update invoice status
            if ($payment->status === 'completed') {
                $invoice->updateStatus();
            }

            return $payment;
        });
    }

    /**
     * Get payment summary for a date range
     */
    public static function getSummaryByDateRange($startDate, $endDate)
    {
        return static::completed()
            ->byDateRange($startDate, $endDate)
            ->selectRaw('
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount
            ')
            ->groupBy('payment_method')
            ->get();
    }

    /**
     * Update the related invoice status and amounts
     */
    public function updateInvoiceStatus()
    {
        if (!$this->invoice_id) {
            return;
        }

        $invoice = $this->invoice;
        if (!$invoice) {
            return;
        }

        // Calculate total payments for this invoice
        $totalPaid = $invoice->payments()->whereIn('status', ['completed', 'pending'])->sum('amount');

        // Update invoice amounts
        $invoice->amount_paid = $totalPaid;
        $invoice->balance = $invoice->total_amount - $totalPaid;

        // Update status based on payment amounts
        if ($invoice->balance <= 0) {
            $invoice->status = 'paid';
            $invoice->paid_date = now();
        } elseif ($totalPaid > 0) {
            $invoice->status = 'partially_paid';
        }

        $invoice->save();
    }

    /**
     * Get daily payment totals for a date range
     */
    public static function getDailyTotals($startDate, $endDate)
    {
        return static::completed()
            ->byDateRange($startDate, $endDate)
            ->selectRaw('
                DATE(payment_date) as date,
                COUNT(*) as count,
                SUM(amount) as total_amount
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
