<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'class_id',
        'course_id', // Added for course-level exams
        'academic_year_id',
        'subject_id',
        'exam_type',
        'semester',
        'year',
        'exam_date',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'total_marks', // Auto-calculated from all subjects
        'theory_marks', // Auto-calculated from all subjects
        'practical_marks', // Auto-calculated from all subjects
        'pass_mark',
        'venue',
        'max_students',
        'instructions',
        'status',
        'is_multi_subject', // Flag for multi-subject exams
        'auto_load_subjects', // Flag to auto-load all course subjects
        'grading_system_id', // Grading system for this exam
        'created_by',
        // New Nepali system fields
        'education_level',
        'stream',
        'program_code',
        'exam_type_id',
        'assessment_category',
        'weightage_percentage',
        'auto_enroll_students',
        'is_published',
        'send_notifications',
        'allow_late_submission',
        'minimum_pass_percentage',
        'overall_pass_percentage',
        'requires_attendance',
        'minimum_attendance_percentage'
    ];

    protected $casts = [
        'exam_date' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'total_marks' => 'decimal:2',
        'theory_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'pass_mark' => 'decimal:2',
        'duration_minutes' => 'integer',
        'max_students' => 'integer',
        'is_multi_subject' => 'boolean',
        'auto_load_subjects' => 'boolean',
        // New Nepali system casts
        'weightage_percentage' => 'decimal:2',
        'auto_enroll_students' => 'boolean',
        'is_published' => 'boolean',
        'send_notifications' => 'boolean',
        'allow_late_submission' => 'boolean',
        'minimum_pass_percentage' => 'decimal:2',
        'overall_pass_percentage' => 'decimal:2',
        'requires_attendance' => 'boolean',
        'minimum_attendance_percentage' => 'decimal:2'
    ];

    protected $dates = [
        'exam_date',
        'start_date',
        'end_date'
    ];

    /**
     * Get the class this exam belongs to
     */
    public function class()
    {
        return $this->belongsTo(ClassSection::class, 'class_id');
    }

    /**
     * Get the subject this exam belongs to
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the academic year this exam belongs to
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the grading system for this exam
     */
    public function gradingSystem()
    {
        return $this->belongsTo(GradingSystem::class);
    }

    /**
     * Get the exam type for this exam
     */
    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    /**
     * Get the user who created this exam
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all grades for this exam
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get exam subjects (many-to-many relationship)
     */
    public function examSubjects()
    {
        return $this->hasMany(ExamSubject::class);
    }

    /**
     * Get subjects attached to this exam
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'exam_subjects')
            ->withPivot(['theory_marks', 'practical_marks', 'pass_marks_theory', 'pass_marks_practical', 'is_active'])
            ->withTimestamps();
    }

    /**
     * Get exam component marks for this exam
     */
    public function componentMarks()
    {
        return $this->hasMany(ExamComponentMark::class);
    }

    /**
     * Get the course directly or through the class relationship
     */
    public function course()
    {
        if ($this->course_id) {
            return $this->belongsTo(Course::class);
        }
        return $this->hasOneThrough(Course::class, ClassSection::class, 'id', 'id', 'class_id', 'course_id');
    }

    /**
     * Scope for active exams
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    /**
     * Scope for completed exams
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for upcoming exams
     */
    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>', now())
                    ->where('status', 'scheduled');
    }

    /**
     * Scope for incomplete exams
     */
    public function scopeIncomplete($query)
    {
        return $query->where('status', 'incomplete');
    }

    /**
     * Check if exam is upcoming
     */
    public function isUpcoming()
    {
        return $this->exam_date > now() && $this->status === 'scheduled';
    }

    /**
     * Check if exam is incomplete
     */
    public function isIncomplete()
    {
        return $this->status === 'incomplete';
    }

    /**
     * Check if exam is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if exam has theory component
     */
    public function hasTheory()
    {
        return $this->theory_marks > 0;
    }

    /**
     * Check if exam has practical component
     */
    public function hasPractical()
    {
        return $this->practical_marks > 0;
    }

    /**
     * Get exam type label
     */
    public function getExamTypeLabel()
    {
        $types = [
            'internal' => 'Internal Exam',
            'board' => 'Board Exam',
            'practical' => 'Practical Exam',
            'midterm' => 'Midterm Exam',
            'annual' => 'Annual Exam',
            'quiz' => 'Quiz',
            'test' => 'Test',
            'final' => 'Final Exam',
            'assignment' => 'Assignment'
        ];

        return $types[$this->exam_type] ?? ucfirst($this->exam_type);
    }

    /**
     * Get status label with color
     */
    public function getStatusLabel()
    {
        $statuses = [
            'scheduled' => ['label' => 'Scheduled', 'color' => 'blue'],
            'incomplete' => ['label' => 'Incomplete', 'color' => 'yellow'],
            'completed' => ['label' => 'Completed', 'color' => 'green'],
            'cancelled' => ['label' => 'Cancelled', 'color' => 'red']
        ];

        return $statuses[$this->status] ?? ['label' => ucfirst($this->status), 'color' => 'gray'];
    }

    /**
     * Get formatted exam date
     */
    public function getFormattedExamDate()
    {
        return $this->exam_date->format('M d, Y \a\t g:i A');
    }

    /**
     * Get duration in hours and minutes
     */
    public function getFormattedDuration()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Calculate pass percentage
     */
    public function getPassPercentage()
    {
        if ($this->total_marks == 0) {
            return 0;
        }
        return ($this->pass_mark / $this->total_marks) * 100;
    }

    /**
     * Get enrolled students count for this exam
     */
    public function getEnrolledStudentsCount()
    {
        if (!$this->class) {
            return 0;
        }

        // Start with basic enrolled students in the class
        $query = $this->class->enrollments()
            ->where('status', 'enrolled');

        // Try to match academic year, but be flexible if not found
        if ($this->academic_year_id) {
            $withAcademicYear = clone $query;
            $withAcademicYear->where('academic_year_id', $this->academic_year_id);

            // If we have matches with academic year, use those
            if ($withAcademicYear->count() > 0) {
                $query = $withAcademicYear;
            }
            // Otherwise, use all enrolled students in the class
        }

        // Note: Semester filtering removed because enrollments table doesn't have semester column
        // Semester information is now stored in the class, not in individual enrollments

        return $query->count();
    }

    /**
     * Get graded students count
     */
    public function getGradedStudentsCount()
    {
        return $this->grades()->count();
    }

    /**
     * Check if all students are graded
     */
    public function isFullyGraded()
    {
        return $this->getGradedStudentsCount() >= $this->getEnrolledStudentsCount();
    }

    /**
     * Get average score for this exam
     */
    public function getAverageScore()
    {
        return $this->grades()->avg('score') ?? 0;
    }

    /**
     * Get pass rate for this exam
     */
    public function getPassRate()
    {
        $totalGrades = $this->grades()->count();
        if ($totalGrades === 0) {
            return 0;
        }

        $passedGrades = $this->grades()->where('score', '>=', $this->pass_mark)->count();
        return ($passedGrades / $totalGrades) * 100;
    }

    /**
     * Check if exam is multi-subject
     */
    public function isMultiSubject()
    {
        return $this->examSubjects()->count() > 1;
    }

    /**
     * Get exam period duration in days
     */
    public function getExamPeriodDuration()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Check if exam is within exam period
     */
    public function isWithinExamPeriod()
    {
        if (!$this->start_date || !$this->end_date) {
            return true; // No period defined, so it's valid
        }

        $examDate = $this->exam_date->toDateString();
        return $examDate >= $this->start_date->toDateString() &&
               $examDate <= $this->end_date->toDateString();
    }

    /**
     * Get available exam types
     */
    public static function getExamTypes()
    {
        return [
            'first_assessment' => 'First Assessment',
            'first_terminal' => 'First Terminal',
            'second_assessment' => 'Second Assessment',
            'second_terminal' => 'Second Terminal',
            'third_assessment' => 'Third Assessment',
            'final_term' => 'Final Term',
            'monthly_term' => 'Monthly Term',
            'weekly_test' => 'Weekly Test'
        ];
    }

    /**
     * Get the effective grading system (exam's grading system or default)
     */
    public function getEffectiveGradingSystem()
    {
        return $this->gradingSystem ?: GradingSystem::getDefault();
    }

    /**
     * Get grade by percentage using exam's grading system
     */
    public function getGradeByPercentage($percentage)
    {
        $gradingSystem = $this->getEffectiveGradingSystem();

        if ($gradingSystem) {
            return $gradingSystem->getGradeByPercentage($percentage);
        }

        // Fallback to old method if no grading system
        return GradeScale::getGradeByPercentage($percentage);
    }

    /**
     * Auto-load marks from subject (for single subject exams)
     */
    public function loadMarksFromSubject()
    {
        if (!$this->subject_id) {
            return;
        }

        $subject = $this->subject;
        if ($subject) {
            $this->theory_marks = $subject->full_marks_theory;
            $this->practical_marks = $subject->full_marks_practical;
            $this->total_marks = $subject->total_full_marks;
            $this->pass_mark = $subject->total_pass_marks;
        }
    }

    /**
     * Auto-load all subjects for course-based exams
     */
    public function autoLoadCourseSubjects()
    {
        if (!$this->auto_load_subjects || !$this->class_id) {
            return;
        }

        $subjects = Subject::where('class_id', $this->class_id)
            ->where('is_active', true)
            ->get();

        $totalTheoryMarks = 0;
        $totalPracticalMarks = 0;

        foreach ($subjects as $subject) {
            // Attach subject to exam with default marks
            $this->subjects()->syncWithoutDetaching([
                $subject->id => [
                    'theory_marks' => $subject->full_marks_theory ?? 0,
                    'practical_marks' => $subject->full_marks_practical ?? 0,
                    'pass_marks_theory' => $subject->pass_marks_theory ?? 0,
                    'pass_marks_practical' => $subject->pass_marks_practical ?? 0,
                    'is_active' => true
                ]
            ]);

            $totalTheoryMarks += $subject->full_marks_theory ?? 0;
            $totalPracticalMarks += $subject->full_marks_practical ?? 0;
        }

        // Update exam total marks
        $this->theory_marks = $totalTheoryMarks;
        $this->practical_marks = $totalPracticalMarks;
        $this->total_marks = $totalTheoryMarks + $totalPracticalMarks;
        $this->is_multi_subject = true;
        $this->save();
    }

    /**
     * Calculate total marks from attached subjects
     */
    public function calculateTotalMarksFromSubjects()
    {
        $totalTheory = $this->subjects()->sum('exam_subjects.theory_marks');
        $totalPractical = $this->subjects()->sum('exam_subjects.practical_marks');

        $this->theory_marks = $totalTheory;
        $this->practical_marks = $totalPractical;
        $this->total_marks = $totalTheory + $totalPractical;

        return $this->total_marks;
    }

    /**
     * Get marks for this exam
     */
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Auto-load subjects based on class and education level (Nepali system)
     */
    public function autoLoadSubjectsNepali()
    {
        if (!$this->auto_load_subjects || !$this->class) {
            return;
        }

        $query = Subject::query();

        // Filter by education level
        if ($this->education_level) {
            $query->where(function ($q) {
                $q->where('education_level', $this->education_level)
                  ->orWhere('education_level', 'both');
            });
        }

        // Filter by stream for +2
        if ($this->education_level === 'plus_two' && $this->stream) {
            $query->where(function ($q) {
                $q->whereJsonContains('applicable_streams', $this->stream)
                  ->orWhereNull('applicable_streams');
            });
        }

        // Filter by program for Bachelor's
        if ($this->education_level === 'bachelors' && $this->program_code) {
            $query->where(function ($q) {
                $q->whereJsonContains('applicable_programs', $this->program_code)
                  ->orWhereNull('applicable_programs');
            });
        }

        $subjects = $query->where('status', 'active')->get();

        // Attach subjects to exam
        foreach ($subjects as $subject) {
            $this->subjects()->syncWithoutDetaching([
                $subject->id => [
                    'theory_marks' => $subject->theory_marks ?? 80,
                    'practical_marks' => $subject->has_practical ? ($subject->practical_marks ?? 20) : 0,
                    'pass_marks_theory' => ($subject->theory_marks ?? 80) * ($this->minimum_pass_percentage / 100),
                    'pass_marks_practical' => $subject->has_practical ? (($subject->practical_marks ?? 20) * ($this->minimum_pass_percentage / 100)) : 0,
                    'is_active' => true
                ]
            ]);
        }
    }

    /**
     * Check if exam is for +2 level
     */
    public function isPlusTwo()
    {
        return $this->education_level === 'plus_two';
    }

    /**
     * Check if exam is for Bachelor's level
     */
    public function isBachelors()
    {
        return $this->education_level === 'bachelors';
    }

    /**
     * Check if exam is internal assessment
     */
    public function isInternal()
    {
        return $this->assessment_category === 'internal';
    }

    /**
     * Check if exam is external assessment
     */
    public function isExternal()
    {
        return $this->assessment_category === 'external';
    }

    /**
     * Get formatted education level
     */
    public function getEducationLevelLabelAttribute()
    {
        return $this->education_level ? ucfirst(str_replace('_', ' ', $this->education_level)) : 'Not Set';
    }

    /**
     * Get formatted assessment category
     */
    public function getAssessmentCategoryLabelAttribute()
    {
        return $this->assessment_category ? ucfirst($this->assessment_category) : 'Not Set';
    }

    /**
     * Auto-enroll students based on class and education level criteria
     */
    public function autoEnrollStudents()
    {
        if (!$this->auto_enroll_students || !$this->class_id) {
            return;
        }

        $query = Enrollment::with('student')
            ->where('class_id', $this->class_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'enrolled');

        // Additional filtering based on education level
        if ($this->education_level === 'plus_two' && $this->stream) {
            $query->whereHas('student', function ($q) {
                $q->where('stream', $this->stream);
            });
        } elseif ($this->education_level === 'bachelors' && $this->program_code) {
            $query->whereHas('student', function ($q) {
                $q->where('program_code', $this->program_code);
            });
        }

        $enrollments = $query->get();

        // Create exam enrollments or marks placeholders if needed
        foreach ($enrollments as $enrollment) {
            // This could be extended to create exam-specific enrollment records
            // For now, we rely on the existing enrollment system
        }

        return $enrollments->count();
    }

    /**
     * Get students eligible for this exam
     */
    public function getEligibleStudents()
    {
        $query = Student::whereHas('enrollments', function ($q) {
            $q->where('class_id', $this->class_id)
              ->where('academic_year_id', $this->academic_year_id)
              ->where('status', 'enrolled');
        });

        // Filter by education level criteria
        if ($this->education_level === 'plus_two' && $this->stream) {
            $query->where('stream', $this->stream);
        } elseif ($this->education_level === 'bachelors' && $this->program_code) {
            $query->where('program_code', $this->program_code);
        }

        // Check attendance requirements if applicable
        if ($this->requires_attendance && $this->minimum_attendance_percentage) {
            $query->whereHas('attendances', function ($q) {
                // This would need to be implemented based on your attendance system
                // $q->havingRaw('(attended_classes / total_classes) * 100 >= ?', [$this->minimum_attendance_percentage]);
            });
        }

        return $query->get();
    }

    /**
     * Calculate overall result for a student across all subjects
     */
    public function calculateStudentOverallResult($studentId)
    {
        $marks = $this->marks()->where('student_id', $studentId)->get();

        if ($marks->isEmpty()) {
            return null;
        }

        $totalObtained = $marks->sum('obtained_marks');
        $totalMaximum = $marks->sum('total_marks');
        $overallPercentage = $totalMaximum > 0 ? ($totalObtained / $totalMaximum) * 100 : 0;

        // Check if student passes overall
        $passPercentage = $this->overall_pass_percentage ?? $this->minimum_pass_percentage ?? 40;
        $isPassed = $overallPercentage >= $passPercentage;

        // Check if student passes in all subjects individually
        $passedAllSubjects = $marks->every(function ($mark) {
            $subjectPassPercentage = $this->minimum_pass_percentage ?? 40;
            return $mark->percentage >= $subjectPassPercentage;
        });

        return [
            'total_obtained' => $totalObtained,
            'total_maximum' => $totalMaximum,
            'overall_percentage' => $overallPercentage,
            'overall_grade' => $this->getGradeByPercentage($overallPercentage),
            'is_passed' => $isPassed && $passedAllSubjects,
            'passed_subjects' => $marks->where('status', 'pass')->count(),
            'failed_subjects' => $marks->where('status', 'fail')->count(),
            'subject_results' => $marks
        ];
    }

    /**
     * Get exam completion status
     */
    public function getCompletionStatus()
    {
        $totalStudents = $this->getEligibleStudents()->count();
        $studentsWithMarks = $this->marks()->distinct('student_id')->count();
        $completionPercentage = $totalStudents > 0 ? ($studentsWithMarks / $totalStudents) * 100 : 0;

        return [
            'total_students' => $totalStudents,
            'students_with_marks' => $studentsWithMarks,
            'completion_percentage' => $completionPercentage,
            'is_complete' => $completionPercentage >= 100
        ];
    }
}
