<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'status',
        'enrollment_date',
        'drop_date',
        'drop_reason',
        'attendance_percentage',
        'ca_score',
        'exam_score',
        'total_score',
        'final_grade',
        // Nepal-specific fields
        'credit_hours',
        'fee_amount',
        'enrollment_type',
        'waitlist_position',
        'prerequisites_met',
        'fee_payment_date',
        'payment_status',
        'enrollment_period_start',
        'enrollment_period_end',
        'add_drop_deadline',
        'attendance_required',
        'minimum_attendance_percentage',
        'enrollment_notes'
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'drop_date' => 'date',
        'attendance_percentage' => 'decimal:2',
        'ca_score' => 'decimal:2',
        'exam_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        // Nepal-specific field casts
        'credit_hours' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'prerequisites_met' => 'boolean',
        'fee_payment_date' => 'date',
        'enrollment_period_start' => 'date',
        'enrollment_period_end' => 'date',
        'add_drop_deadline' => 'date',
        'attendance_required' => 'boolean',
        'minimum_attendance_percentage' => 'decimal:2'
    ];

    protected $dates = [
        'enrollment_date',
        'drop_date',
        'fee_payment_date',
        'enrollment_period_start',
        'enrollment_period_end',
        'add_drop_deadline',
        'deleted_at'
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'student_id',
                'class_id',
                'academic_year_id',
                'status',
                'enrollment_date',
                'drop_date',
                'drop_reason',
                'attendance_percentage',
                'ca_score',
                'exam_score',
                'total_score',
                'final_grade'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Enrollment {$eventName}")
            ->useLogName('enrollment_management');
    }

    /**
     * Get the student that owns the enrollment
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the class section for this enrollment
     */
    public function class()
    {
        return $this->belongsTo(ClassSection::class, 'class_id');
    }

    /**
     * Get the academic year for this enrollment
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Scope to get enrollments by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get semester through class relationship
     */
    public function getSemesterAttribute()
    {
        return $this->class->semester ?? null;
    }

    /**
     * Scope to get enrollments by semester (through class relationship)
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->whereHas('class', function ($q) use ($semester) {
            $q->where('semester', $semester);
        });
    }

    /**
     * Scope to get enrollments by academic year
     */
    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope to get current enrollments
     */
    public function scopeCurrent($query)
    {
        $currentAcademicYear = AcademicYear::current();
        if ($currentAcademicYear) {
            return $query->where('academic_year_id', $currentAcademicYear->id)
                        ->where('status', 'enrolled');
        }
        return $query->where('status', 'enrolled');
    }

    /**
     * Scope to get active enrollments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'enrolled');
    }

    /**
     * Get course through class relationship
     */
    public function getCourseAttribute()
    {
        return $this->class->course ?? null;
    }

    /**
     * Get course code
     */
    public function getCourseCodeAttribute()
    {
        return $this->course->code ?? 'N/A';
    }

    /**
     * Get course title
     */
    public function getCourseTitleAttribute()
    {
        return $this->course->title ?? 'N/A';
    }

    /**
     * Get credit units
     */
    public function getCreditUnitsAttribute()
    {
        return $this->course->credit_units ?? 0;
    }

    /**
     * Check if enrollment can be dropped
     */
    public function canBeDropped()
    {
        // Can only drop if currently enrolled and within drop period
        if ($this->status !== 'enrolled') {
            return false;
        }

        // Check if within drop period (e.g., first 4 weeks of semester)
        $dropDeadline = $this->enrollment_date->addWeeks(4);
        return now()->lte($dropDeadline);
    }

    /**
     * Drop the enrollment
     */
    public function drop($reason = null)
    {
        if (!$this->canBeDropped()) {
            throw new \Exception('Enrollment cannot be dropped at this time.');
        }

        $this->update([
            'status' => 'dropped',
            'drop_date' => now(),
            'drop_reason' => $reason
        ]);

        // Update class enrollment count
        $this->class->decrement('enrolled_count');

        return true;
    }

    /**
     * Calculate final grade based on CA and Exam scores
     */
    public function calculateFinalGrade()
    {
        if (is_null($this->ca_score) || is_null($this->exam_score)) {
            // If scores are not available, set status to pending_grade and return null
            $this->update([
                'total_score' => null,
                'final_grade' => null,
                'status' => 'pending_grade' // New status for un-graded enrollments
            ]);
            $this->student->updateCGPA(); // Recalculate CGPA after status change
            return null;
        }

        $total = $this->ca_score + $this->exam_score;
        $this->update(['total_score' => $total]);

        // Determine letter grade
        $letterGrade = $this->getLetterGrade($total);
        $this->update(['final_grade' => $letterGrade]);

        // Update enrollment status based on grade
        $status = ($letterGrade === 'F') ? 'failed' : 'completed';
        $this->update(['status' => $status]);

        // Recalculate student's CGPA after enrollment status/grade update
        $this->student->updateCGPA();

        return $letterGrade;
    }

    /**
     * Get letter grade from total score
     */
    private function getLetterGrade($score)
    {
        if ($score >= 70) {
            return 'A';
        } elseif ($score >= 60) {
            return 'B';
        } elseif ($score >= 50) {
            return 'C';
        } elseif ($score >= 45) {
            return 'D';
        } elseif ($score >= 40) {
            return 'E';
        } else {
            return 'F';
        }
    }

    /**
     * Get grade point for the final grade
     */
    public function getGradePointAttribute()
    {
        $gradePoints = [
            'A' => 5.0,
            'B' => 4.0,
            'C' => 3.0,
            'D' => 2.0,
            'E' => 1.0,
            'F' => 0.0
        ];

        return $gradePoints[$this->final_grade] ?? 0.0;
    }

    /**
     * Check if enrollment is passed
     */
    public function isPassed()
    {
        return in_array($this->final_grade, ['A', 'B', 'C', 'D', 'E']);
    }

    /**
     * Check if enrollment is failed
     */
    public function isFailed()
    {
        return $this->final_grade === 'F';
    }

    /**
     * Check if student has participated in any exam for this enrollment
     */
    public function hasParticipatedInExam()
    {
        return $this->marks()->exists();
    }

    /**
     * Get student's CGPA
     */
    public function getCgpa()
    {
        return $this->student->cgpa ?? 0.00;
    }

    /**
     * Get marks relationship
     */
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Get status badge color for UI
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'enrolled' => 'bg-green-100 text-green-800',
            'waitlisted' => 'bg-yellow-100 text-yellow-800',
            'dropped' => 'bg-red-100 text-red-800',
            'completed' => 'bg-blue-100 text-blue-800',
            'failed' => 'bg-red-100 text-red-800',
            'withdrawn' => 'bg-gray-100 text-gray-800',
            'pending_grade' => 'bg-indigo-100 text-indigo-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get formatted enrollment date
     */
    public function getFormattedEnrollmentDateAttribute()
    {
        return $this->enrollment_date->format('M d, Y');
    }

    /**
     * Get formatted drop date
     */
    public function getFormattedDropDateAttribute()
    {
        return $this->drop_date ? $this->drop_date->format('M d, Y') : null;
    }

    // ==================== NEPAL-SPECIFIC METHODS ====================

    /**
     * Check if enrollment is waitlisted
     */
    public function isWaitlisted()
    {
        return $this->status === 'waitlisted';
    }

    /**
     * Check if enrollment fees are paid
     */
    public function isFeePaid()
    {
        return in_array($this->payment_status, ['paid', 'waived']);
    }

    /**
     * Check if enrollment is within add/drop period (Nepal-specific)
     */
    public function isWithinAddDropPeriod()
    {
        if (!$this->add_drop_deadline) {
            return false;
        }

        return now()->lte($this->add_drop_deadline) &&
               in_array($this->status, ['enrolled', 'waitlisted']);
    }

    /**
     * Check if prerequisites are met
     */
    public function hasPrerequisitesMet()
    {
        return $this->prerequisites_met;
    }

    /**
     * Get enrollment type display name
     */
    public function getEnrollmentTypeDisplayAttribute()
    {
        return match($this->enrollment_type) {
            'regular' => 'Regular Enrollment',
            'late' => 'Late Enrollment',
            'makeup' => 'Makeup Enrollment',
            'readmission' => 'Readmission',
            default => ucfirst($this->enrollment_type)
        };
    }

    /**
     * Get payment status display name
     */
    public function getPaymentStatusDisplayAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'Payment Pending',
            'paid' => 'Paid',
            'partial' => 'Partially Paid',
            'waived' => 'Fee Waived',
            default => ucfirst($this->payment_status)
        };
    }



    /**
     * Get payment status badge color for UI
     */
    public function getPaymentStatusBadgeColorAttribute()
    {
        return match($this->payment_status) {
            'paid' => 'bg-green-100 text-green-800',
            'waived' => 'bg-blue-100 text-blue-800',
            'partial' => 'bg-yellow-100 text-yellow-800',
            'pending' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Calculate total fee including late penalties
     */
    public function calculateTotalFee()
    {
        $baseFee = $this->fee_amount ?? 0;

        // Add late enrollment penalty if applicable
        if ($this->enrollment_type === 'late') {
            // You can get this from enrollment period or set a default
            $latePenalty = 500; // NPR 500 default late penalty
            $baseFee += $latePenalty;
        }

        return $baseFee;
    }

    /**
     * Check if student meets minimum attendance requirement
     */
    public function meetsAttendanceRequirement()
    {
        if (!$this->attendance_required) {
            return true;
        }

        $requiredPercentage = $this->minimum_attendance_percentage ?? 75.00;
        return $this->attendance_percentage >= $requiredPercentage;
    }

    /**
     * Get attendance status for display
     */
    public function getAttendanceStatusAttribute()
    {
        if (!$this->attendance_required) {
            return 'Not Required';
        }

        if ($this->attendance_percentage === null) {
            return 'Not Recorded';
        }

        $required = $this->minimum_attendance_percentage ?? 75.00;

        if ($this->attendance_percentage >= $required) {
            return 'Satisfactory';
        } else {
            return 'Below Requirement';
        }
    }

    /**
     * Scope to get enrollments by payment status
     */
    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Scope to get waitlisted enrollments
     */
    public function scopeWaitlisted($query)
    {
        return $query->where('status', 'waitlisted');
    }

    /**
     * Scope to get enrollments by enrollment type
     */
    public function scopeByEnrollmentType($query, $type)
    {
        return $query->where('enrollment_type', $type);
    }

    /**
     * Scope to get enrollments with unpaid fees
     */
    public function scopeUnpaidFees($query)
    {
        return $query->whereIn('payment_status', ['pending', 'partial']);
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When enrollment is created, increment class enrollment count
        static::created(function ($enrollment) {
            $enrollment->class->increment('enrolled_count'); // Changed to enrolled_count
        });

        // When enrollment is deleted, decrement class enrollment count
        static::deleted(function ($enrollment) {
            if ($enrollment->status === 'enrolled') {
                $enrollment->class->decrement('enrolled_count'); // Changed to enrolled_count
            }
        });
    }
}
