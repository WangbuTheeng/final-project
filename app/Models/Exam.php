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
        'subject_id',
        'academic_year_id',
        'exam_type',
        'semester',
        'year',
        'exam_date',
        'start_date',
        'end_date',
        'duration_minutes',
        'total_marks',
        'theory_marks',
        'practical_marks',
        'pass_mark',
        'venue',
        'instructions',
        'status',
        'created_by'
    ];

    protected $casts = [
        'exam_date' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_marks' => 'decimal:2',
        'theory_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'pass_mark' => 'decimal:2',
        'duration_minutes' => 'integer'
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
     * Get the course through the class relationship
     */
    public function course()
    {
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
     * Scope for ongoing exams
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    /**
     * Check if exam is upcoming
     */
    public function isUpcoming()
    {
        return $this->exam_date > now() && $this->status === 'scheduled';
    }

    /**
     * Check if exam is ongoing
     */
    public function isOngoing()
    {
        return $this->status === 'ongoing';
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
            'ongoing' => ['label' => 'Ongoing', 'color' => 'yellow'],
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
        return ($this->pass_mark / $this->total_marks) * 100;
    }

    /**
     * Get enrolled students count for this exam
     */
    public function getEnrolledStudentsCount()
    {
        return $this->class->enrollments()
            ->where('academic_year_id', $this->academic_year_id)
            ->where('semester', $this->semester)
            ->where('status', 'enrolled')
            ->count();
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
    }

    /**
     * Auto-load marks from subject
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
}
