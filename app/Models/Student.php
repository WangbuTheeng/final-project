<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matric_number',
        'department_id',
        'academic_year_id',
        'current_level',
        'mode_of_entry',
        'study_mode',
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
        'current_level' => 'integer',
        'expected_graduation_date' => 'date',
        'actual_graduation_date' => 'date',
        'guardian_info' => 'array'
    ];

    protected $dates = [
        'expected_graduation_date',
        'actual_graduation_date'
    ];

    /**
     * Get the user associated with the student.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department this student belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the academic year this student was admitted.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get all enrollments for this student.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get current semester enrollments for the active academic year.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function currentEnrollments()
    {
        $currentAcademicYear = AcademicYear::current();
        return $this->enrollments()
            ->where('academic_year_id', $currentAcademicYear->id)
            ->where('status', 'enrolled');
    }

    /**
     * Get enrollments for a specific academic year and semester.
     *
     * @param int $academicYearId The academic year ID
     * @param string $semester The semester ('first' or 'second')
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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
     * Scope to get students by level
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('current_level', $level);
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
     * Get student's level name
     */
    public function getLevelNameAttribute()
    {
        $levels = [
            100 => '100 Level (Year 1)',
            200 => '200 Level (Year 2)',
            300 => '300 Level (Year 3)',
            400 => '400 Level (Year 4)',
            500 => '500 Level (Year 5)'
        ];

        return $levels[$this->current_level] ?? 'Unknown Level';
    }

    /**
     * Check if student can enroll in a course
     */
    public function canEnrollInCourse($course, $academicYearId, $semester)
    {
        // Check if already enrolled in this course for the semester
        $existingEnrollment = $this->enrollments()
            ->whereHas('class', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->where('status', 'enrolled')
            ->exists();

        if ($existingEnrollment) {
            return false;
        }

        // Check if course level matches student level
        if ($course->level != $this->current_level) {
            return false;
        }

        // Check prerequisites
        if (!empty($course->prerequisites)) {
            $completedCourses = $this->completedEnrollments()
                ->with('class.course')
                ->get()
                ->pluck('class.course.id')
                ->toArray();

            foreach ($course->prerequisites as $prerequisiteId) {
                if (!in_array($prerequisiteId, $completedCourses)) {
                    return false;
                }
            }
        }

        return true;
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
            $this->update(['cgpa' => 0.00]);
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
        if ($this->cgpa >= 4.5) {
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
}
