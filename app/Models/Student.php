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
        'guardian_info'
    ];

    protected $casts = [
        'cgpa' => 'decimal:2',
        'total_credits_earned' => 'integer',
        'expected_graduation_date' => 'date',
        'actual_graduation_date' => 'date',
        'guardian_info' => 'array'
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
     * Get current semester enrollments
     */
    public function currentEnrollments()
    {
        $currentAcademicYear = AcademicYear::current();
        return $this->enrollments()
            ->where('academic_year_id', $currentAcademicYear->id)
            ->where('status', 'enrolled');
    }

    /**
     * Get enrollments for a specific academic year and semester
     */
    public function enrollmentsForSemester($academicYearId, $semester)
    {
        return $this->enrollments()
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester);
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
     * Check if student can enroll in a course
     */
    public function canEnrollInCourse(ClassSection $class, $academicYearId, $semester) // Changed $course to ClassSection $class
    {
        $reasons = [];

        // Check if already enrolled in this specific class for the semester (including soft-deleted)
        $existingEnrollment = $this->enrollments()
            ->withTrashed() // Include soft-deleted records
            ->where('class_id', $class->id) // Check against the specific class ID
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->exists();

        if ($existingEnrollment) {
            $reasons[] = 'Student is already enrolled in this specific class for the selected semester.';
        }

        // Note: Course level checking removed as student levels are no longer tracked

        // Check prerequisites (only if course has prerequisites defined)
        // Use $class->course->prerequisites instead of $course->prerequisites
        if (isset($class->course->prerequisites) && !empty($class->course->prerequisites)) {
            $completedCourses = $this->completedEnrollments()
                ->with('class.course')
                ->get()
                ->pluck('class.course.id')
                ->toArray();

            $missingPrerequisites = [];
            foreach ($class->course->prerequisites as $prerequisiteId) {
                if (!in_array($prerequisiteId, $completedCourses)) {
                    $prerequisiteCourse = Course::find($prerequisiteId);
                    if ($prerequisiteCourse) {
                        $missingPrerequisites[] = $prerequisiteCourse->code . ' - ' . $prerequisiteCourse->title;
                    }
                }
            }

            if (!empty($missingPrerequisites)) {
                $reasons[] = 'Missing prerequisites: ' . implode(', ', $missingPrerequisites) . '.';
            }
        }

        return [empty($reasons), $reasons]; // Return [true/false, reasons_array]
    }

    /**
     * Calculate current semester CGPA
     */
    public function calculateCurrentSemesterGPA($academicYearId, $semester)
    {
        $enrollments = $this->enrollmentsForSemester($academicYearId, $semester)
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
     * Update CGPA based on all completed courses
     */
    public function updateCGPA()
    {
        $completedEnrollments = $this->completedEnrollments()
            ->with('class.course')
            ->get();

        if ($completedEnrollments->isEmpty()) {
            $this->update([
                'cgpa' => 0.00,
                'total_credits_earned' => 0
            ]);
            return;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($completedEnrollments as $enrollment) {
            $creditUnits = $enrollment->class->course->credit_units;
            $gradePoint = $this->getGradePoint($enrollment->final_grade);
            
            $totalPoints += ($gradePoint * $creditUnits);
            $totalCredits += $creditUnits;
        }

        $cgpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.00;
        
        $this->update([
            'cgpa' => $cgpa,
            'total_credits_earned' => $totalCredits
        ]);
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
