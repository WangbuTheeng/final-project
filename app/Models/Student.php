<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log; // Import Log facade
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Student extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'admission_number',
        'department_id',
        'faculty_id',
        'academic_year_id',
        'mode_of_entry',
        'status',
        'cgpa',
        'total_credits_earned',
        'expected_graduation_date',
        'actual_graduation_date',
        'guardian_info',
        // Nepal-specific academic fields
        'previous_school_name',
        'slc_see_board',
        'slc_see_year',
        'slc_see_marks',
        'plus_two_board',
        'plus_two_year',
        'plus_two_marks',
        'plus_two_stream',
        // Enhanced family information
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'guardian_citizenship_number',
        'annual_family_income',
        // Additional information
        'scholarship_info',
        'hostel_required',
        'medical_info',
        'entrance_exam_score',
        'preferred_subjects',
        'photo_path',
        'document_paths'
    ];

    protected $casts = [
        'cgpa' => 'decimal:2',
        'total_credits_earned' => 'integer',
        'expected_graduation_date' => 'date',
        'actual_graduation_date' => 'date',
        'guardian_info' => 'array',
        'annual_family_income' => 'decimal:2',
        'entrance_exam_score' => 'decimal:2',
        'hostel_required' => 'boolean',
        'document_paths' => 'array'
    ];

    protected $dates = [
        'expected_graduation_date',
        'actual_graduation_date'
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'admission_number',
                'department_id',
                'faculty_id',
                'academic_year_id',
                'mode_of_entry',
                'status',
                'cgpa',
                'total_credits_earned',
                'expected_graduation_date',
                'actual_graduation_date'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Student {$eventName}")
            ->useLogName('student_management');
    }

    /**
     * Get the user associated with the student
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }



    /**
     * Get student's total outstanding balance across all unpaid invoices
     */
    public function getOutstandingBalanceAttribute()
    {
        return $this->invoices()
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->sum('balance');
    }

    /**
     * Get student's unpaid invoices
     */
    public function getUnpaidInvoices()
    {
        return $this->invoices()
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->where('balance', '>', 0)
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get student's outstanding balance for a specific academic year
     */
    public function getOutstandingBalanceForYear($academicYearId)
    {
        return $this->invoices()
            ->where('academic_year_id', $academicYearId)
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->sum('balance');
    }

    /**
     * Get the department this student belongs to
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the faculty this student belongs to
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the academic year this student was admitted
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get all enrollments for this student
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get current academic year enrollments
     */
    public function currentEnrollments()
    {
        $currentAcademicYear = AcademicYear::current();
        return $this->enrollments()
            ->where('academic_year_id', $currentAcademicYear->id)
            ->where('status', 'enrolled');
    }

    /**
     * Get enrollments for a specific academic year
     */
    public function enrollmentsForAcademicYear($academicYearId)
    {
        return $this->enrollments()
            ->where('academic_year_id', $academicYearId);
    }

    /**
     * Get completed enrollments (passed courses)
     */
    public function completedEnrollments()
    {
        return $this->enrollments()->where('status', 'completed');
    }

    /**
     * Get failed enrollments
     */
    public function failedEnrollments()
    {
        return $this->enrollments()->where('status', 'failed');
    }

    /**
     * Scope to get active students
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get students by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope to get students by faculty
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }



    /**
     * Scope to get students by academic year
     */
    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Get student's full name
     */
    public function getFullNameAttribute()
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }



    /**
     * Check if student can enroll in a class using the new validation service
     */
    public function canEnrollInClass(ClassSection $class, int $academicYearId): \App\Services\EnrollmentValidationResult
    {
        $validator = new \App\Services\EnrollmentValidator($this, $class, $academicYearId);
        return $validator->validate();
    }

    /**
     * Legacy method for backward compatibility
     * @deprecated Use canEnrollInClass() instead
     */
    public function canEnrollInCourse(ClassSection $class, $academicYearId)
    {
        $result = $this->canEnrollInClass($class, $academicYearId);
        return [$result->isValid(), $result->getErrors()];
    }

    /**
     * Calculate current academic year GPA
     */
    public function calculateCurrentAcademicYearGPA($academicYearId)
    {
        $enrollments = $this->enrollmentsForAcademicYear($academicYearId)
            ->where('status', 'completed')
            ->with('class.course')
            ->get();

        if ($enrollments->isEmpty()) {
            return 0.00;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($enrollments as $enrollment) {
            $creditUnits = $enrollment->class->course->credit_units;
            $gradePoint = $this->getGradePoint($enrollment->final_grade);
            
            $totalPoints += ($gradePoint * $creditUnits);
            $totalCredits += $creditUnits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.00;
    }

    /**
     * Update CGPA based on all completed courses using optimized single query
     */
    public function updateCGPA(): void
    {
        // Single optimized query to calculate CGPA
        $result = \DB::table('enrollments')
            ->join('classes', 'enrollments.class_id', '=', 'classes.id')
            ->join('courses', 'classes.course_id', '=', 'courses.id')
            ->where('enrollments.student_id', $this->id)
            ->where('enrollments.status', 'completed')
            ->whereNotNull('enrollments.final_grade')
            ->select([
                \DB::raw('SUM(courses.credit_units * CASE 
                    WHEN enrollments.final_grade = "A" THEN 5.0
                    WHEN enrollments.final_grade = "B" THEN 4.0
                    WHEN enrollments.final_grade = "C" THEN 3.0
                    WHEN enrollments.final_grade = "D" THEN 2.0
                    WHEN enrollments.final_grade = "E" THEN 1.0
                    ELSE 0.0
                END) as total_points'),
                \DB::raw('SUM(courses.credit_units) as total_credits')
            ])
            ->first();
            
        $cgpa = $result->total_credits > 0 
            ? round($result->total_points / $result->total_credits, 2) 
            : 0.00;
            
        $this->update([
            'cgpa' => $cgpa,
            'total_credits_earned' => $result->total_credits ?? 0
        ]);
        
        // Clear related caches
        \Cache::forget("student.{$this->id}.cgpa");
        \Cache::tags(['student:' . $this->id])->flush();
    }
    
    /**
     * Get cached CGPA for performance
     */
    public function getCachedCGPA(): float
    {
        return \Cache::remember("student.{$this->id}.cgpa", 3600, function () {
            return $this->cgpa ?? 0.00;
        });
    }

    /**
     * Get grade point for a letter grade
     */
    private function getGradePoint($letterGrade)
    {
        $gradePoints = [
            'A' => 5.0,
            'B' => 4.0,
            'C' => 3.0,
            'D' => 2.0,
            'E' => 1.0,
            'F' => 0.0
        ];

        return $gradePoints[$letterGrade] ?? 0.0;
    }

    /**
     * Check if student is eligible for graduation
     */
    public function isEligibleForGraduation()
    {
        $requiredCredits = $this->department->duration_years * 30; // Assuming 30 credits per year
        
        return $this->total_credits_earned >= $requiredCredits && 
               $this->cgpa >= 1.0 && 
               $this->status === 'active';
    }

    /**
     * Get academic standing based on CGPA
     */
    public function getAcademicStandingAttribute()
    {
        if ($this->cgpa == 0.00 && $this->total_credits_earned == 0) {
            return 'No Record';
        } elseif ($this->cgpa >= 4.5) {
            return 'First Class';
        } elseif ($this->cgpa >= 3.5) {
            return 'Second Class Upper';
        } elseif ($this->cgpa >= 2.5) {
            return 'Second Class Lower';
        } elseif ($this->cgpa >= 1.5) {
            return 'Third Class';
        } elseif ($this->cgpa >= 1.0) {
            return 'Pass';
        } else {
            return 'Fail';
        }
    }

    /**
     * Get all invoices for this student
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get all payments made by this student
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get total amount billed to this student
     */
    public function getTotalBilledAttribute()
    {
        return $this->invoices()->sum('total_amount');
    }

    /**
     * Get total amount paid by this student
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }



    /**
     * Generate a unique admission number
     */
    public static function generateAdmissionNumber($academicYearId, $departmentId = null, $facultyId = null)
    {
        $academicYear = \App\Models\AcademicYear::find($academicYearId);

        if (!$academicYear) {
            throw new \Exception('Invalid academic year');
        }

        // Get the year from academic year (e.g., 2024/2025 -> 24)
        $yearCode = substr($academicYear->start_date->format('Y'), -2);

        // Get code based on department or faculty
        if ($departmentId) {
            $department = \App\Models\Department::find($departmentId);
            if (!$department) {
                throw new \Exception('Invalid department');
            }
            $code = strtoupper(substr($department->code ?? $department->name, 0, 3));
            $filterField = 'department_id';
            $filterId = $departmentId;
        } elseif ($facultyId) {
            $faculty = \App\Models\Faculty::find($facultyId);
            if (!$faculty) {
                throw new \Exception('Invalid faculty');
            }
            $code = strtoupper(substr($faculty->code ?? $faculty->name, 0, 3));
            $filterField = 'faculty_id';
            $filterId = $facultyId;
        } else {
            throw new \Exception('Either department or faculty must be provided');
        }

        // Get the next sequence number for this year and department/faculty
        $query = static::where('academic_year_id', $academicYearId)
            ->where('admission_number', 'like', $yearCode . $code . '%')
            ->orderBy('admission_number', 'desc');

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        } else {
            $query->where('faculty_id', $facultyId)->whereNull('department_id');
        }

        $lastStudent = $query->first();

        if ($lastStudent) {
            // Extract the sequence number from the last admission number
            $lastSequence = (int) substr($lastStudent->admission_number, -4);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }

        // Format: YYDDDNNNN (e.g., 24CSC0001)
        $admissionNumber = $yearCode . $code . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

        // Ensure uniqueness
        while (static::where('admission_number', $admissionNumber)->exists()) {
            $nextSequence++;
            $admissionNumber = $yearCode . $code . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        }

        return $admissionNumber;
    }
}
