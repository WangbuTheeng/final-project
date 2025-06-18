<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'fee_type',
        'amount',
        'course_id',
        'department_id',
        'academic_year_id',
        'is_mandatory',
        'is_active',
        'due_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'due_date' => 'date'
    ];

    /**
     * Get the academic year this fee belongs to
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the course this fee is specific to (optional)
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the department this fee is specific to (optional)
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all invoices that include this fee
     */
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_fee')
                    ->withPivot('amount', 'quantity')
                    ->withTimestamps();
    }

    /**
     * Scope to get active fees
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get mandatory fees
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope to get fees by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('fee_type', $type);
    }

    /**
     * Scope to get fees by academic year
     */
    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope to get fees by course
     */
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope to get fees by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Get applicable fees for a student
     */
    public static function getApplicableFeesForStudent(Student $student, $academicYearId)
    {
        return static::active()
            ->where('academic_year_id', $academicYearId)
            ->where(function ($query) use ($student) {
                $query->whereNull('department_id')
                      ->orWhere('department_id', $student->department_id);
            })
            ->where(function ($query) use ($student) {
                // Check if fee is for a specific course that the student is enrolled in
                // or if it's a general fee (course_id is null)
                $query->whereNull('course_id')
                      ->orWhereHas('course', function ($courseQuery) use ($student) {
                          $courseQuery->whereHas('classes', function ($classQuery) use ($student) {
                              $classQuery->whereHas('enrollments', function ($enrollmentQuery) use ($student) {
                                  $enrollmentQuery->where('student_id', $student->id);
                              });
                          });
                      });
            })
            ->get();
    }

    /**
     * Get fee type display name
     */
    public function getFeeTypeDisplayAttribute()
    {
        $types = [
            'tuition' => 'Tuition Fee',
            'library' => 'Library Fee',
            'laboratory' => 'Laboratory Fee',
            'sports' => 'Sports Fee',
            'medical' => 'Medical Fee',
            'accommodation' => 'Accommodation Fee',
            'registration' => 'Registration Fee',
            'examination' => 'Examination Fee',
            'other' => 'Other Fee'
        ];

        return $types[$this->fee_type] ?? 'Unknown';
    }



    /**
     * Get study mode display name
     */
    public function getStudyModeDisplayAttribute()
    {
        $modes = [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'distance' => 'Distance Learning',
            'all' => 'All Modes'
        ];

        return $modes[$this->study_mode] ?? 'Unknown';
    }

    /**
     * Check if fee is overdue
     */
    public function getIsOverdueAttribute()
    {
        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Generate unique fee code
     */
    public static function generateFeeCode($feeType, $academicYearId)
    {
        $academicYear = AcademicYear::find($academicYearId);
        $yearCode = $academicYear ? substr($academicYear->start_date->format('Y'), -2) : '00';
        
        $typeCode = strtoupper(substr($feeType, 0, 3));
        
        // Get the next sequence number
        $lastFee = static::where('code', 'like', $yearCode . $typeCode . '%')
                         ->orderBy('code', 'desc')
                         ->first();
        
        if ($lastFee) {
            $lastSequence = (int) substr($lastFee->code, -3);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }
        
        // Format: YYTTTXXX (e.g., 24TUI001)
        $code = $yearCode . $typeCode . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        while (static::where('code', $code)->exists()) {
            $nextSequence++;
            $code = $yearCode . $typeCode . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
        }
        
        return $code;
    }
}
