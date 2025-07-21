<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class EnrollmentPeriod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'academic_year_id',
        'semester',
        'type',
        'enrollment_start_date',
        'enrollment_end_date',
        'add_drop_deadline',
        'late_enrollment_deadline',
        'base_enrollment_fee',
        'late_enrollment_penalty',
        'per_credit_fee',
        'is_active',
        'allow_waitlist',
        'max_credits_per_student',
        'min_credits_per_student',
        'minimum_attendance_required',
        'requires_prerequisite_check',
        'enrollment_instructions',
        'notes'
    ];

    protected $casts = [
        'enrollment_start_date' => 'date',
        'enrollment_end_date' => 'date',
        'add_drop_deadline' => 'date',
        'late_enrollment_deadline' => 'date',
        'base_enrollment_fee' => 'decimal:2',
        'late_enrollment_penalty' => 'decimal:2',
        'per_credit_fee' => 'decimal:2',
        'minimum_attendance_required' => 'decimal:2',
        'is_active' => 'boolean',
        'allow_waitlist' => 'boolean',
        'requires_prerequisite_check' => 'boolean'
    ];

    protected $dates = [
        'enrollment_start_date',
        'enrollment_end_date',
        'add_drop_deadline',
        'late_enrollment_deadline',
        'deleted_at'
    ];

    /**
     * Get the academic year for this enrollment period
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get enrollments for this period
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Scope to get active enrollment periods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get current enrollment periods
     */
    public function scopeCurrent($query)
    {
        $today = Carbon::today();
        return $query->where('enrollment_start_date', '<=', $today)
                    ->where('enrollment_end_date', '>=', $today)
                    ->where('is_active', true);
    }

    /**
     * Scope to get enrollment periods by semester
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Check if enrollment is currently open
     */
    public function isEnrollmentOpen()
    {
        $today = Carbon::today();
        return $this->is_active &&
               $today->between($this->enrollment_start_date, $this->enrollment_end_date);
    }

    /**
     * Check if late enrollment is allowed
     */
    public function isLateEnrollmentOpen()
    {
        if (!$this->late_enrollment_deadline) {
            return false;
        }

        $today = Carbon::today();
        return $this->is_active &&
               $today->between($this->enrollment_end_date->addDay(), $this->late_enrollment_deadline);
    }

    /**
     * Check if add/drop is allowed
     */
    public function isAddDropOpen()
    {
        $today = Carbon::today();
        return $this->is_active && $today <= $this->add_drop_deadline;
    }

    /**
     * Get enrollment status for display
     */
    public function getEnrollmentStatusAttribute()
    {
        if ($this->isEnrollmentOpen()) {
            return 'Open';
        } elseif ($this->isLateEnrollmentOpen()) {
            return 'Late Enrollment';
        } elseif ($this->isAddDropOpen()) {
            return 'Add/Drop Only';
        } else {
            return 'Closed';
        }
    }

    /**
     * Calculate total enrollment fee for given credits
     */
    public function calculateEnrollmentFee($creditHours, $isLateEnrollment = false)
    {
        $fee = $this->base_enrollment_fee + ($creditHours * $this->per_credit_fee);

        if ($isLateEnrollment) {
            $fee += $this->late_enrollment_penalty;
        }

        return $fee;
    }

    /**
     * Get semester display name
     */
    public function getSemesterDisplayAttribute()
    {
        return match($this->semester) {
            'first' => 'First Semester',
            'second' => 'Second Semester',
            'summer' => 'Summer Semester',
            default => ucfirst($this->semester)
        };
    }
}
