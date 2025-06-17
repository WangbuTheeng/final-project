<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'enrollment_id',
        'exam_id',
        'subject_id',
        'academic_year_id',
        'semester',
        'year',
        'grade_type',
        'theory_score',
        'practical_score',
        'score',
        'max_score',
        'letter_grade',
        'grade_point',
        'remarks',
        'graded_by',
        'graded_at'
    ];

    protected $casts = [
        'theory_score' => 'decimal:2',
        'practical_score' => 'decimal:2',
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'graded_at' => 'datetime'
    ];

    protected $dates = [
        'graded_at'
    ];

    /**
     * Get the student this grade belongs to
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the enrollment this grade belongs to
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the exam this grade belongs to
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the subject this grade belongs to
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the academic year this grade belongs to
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the user who graded this
     */
    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Calculate letter grade based on score using exam's grading system
     */
    public function calculateLetterGrade()
    {
        $percentage = ($this->score / $this->max_score) * 100;

        // Use exam's grading system if available
        if ($this->exam) {
            $gradeScale = $this->exam->getGradeByPercentage($percentage);
            if ($gradeScale) {
                return $gradeScale->grade_letter;
            }
        }

        // Fallback to default grading logic
        if ($percentage >= 80) {
            return 'A';
        } elseif ($percentage >= 70) {
            return 'B';
        } elseif ($percentage >= 60) {
            return 'C';
        } elseif ($percentage >= 50) {
            return 'D';
        } elseif ($percentage >= 40) {
            return 'E';
        } else {
            return 'F';
        }
    }

    /**
     * Calculate grade point based on letter grade using exam's grading system
     */
    public function calculateGradePoint()
    {
        $percentage = ($this->score / $this->max_score) * 100;

        // Use exam's grading system if available
        if ($this->exam) {
            $gradeScale = $this->exam->getGradeByPercentage($percentage);
            if ($gradeScale) {
                return $gradeScale->grade_point;
            }
        }

        // Fallback to default grade points
        $gradePoints = [
            'A' => 5.0,
            'B' => 4.0,
            'C' => 3.0,
            'D' => 2.0,
            'E' => 1.0,
            'F' => 0.0
        ];

        return $gradePoints[$this->letter_grade] ?? 0.0;
    }

    /**
     * Get percentage score
     */
    public function getPercentage()
    {
        return ($this->score / $this->max_score) * 100;
    }

    /**
     * Check if grade is passing
     */
    public function isPassing()
    {
        if ($this->exam) {
            return $this->score >= $this->exam->pass_mark;
        }
        
        // Default pass mark is 40% if no exam is associated
        return $this->getPercentage() >= 40;
    }

    /**
     * Get grade status
     */
    public function getStatus()
    {
        return $this->isPassing() ? 'Pass' : 'Fail';
    }

    /**
     * Get grade type label
     */
    public function getGradeTypeLabel()
    {
        $types = [
            'ca' => 'Continuous Assessment',
            'exam' => 'Examination',
            'final' => 'Final Grade'
        ];

        return $types[$this->grade_type] ?? ucfirst($this->grade_type);
    }

    /**
     * Get formatted score display
     */
    public function getFormattedScore()
    {
        return number_format($this->score, 1) . '/' . number_format($this->max_score, 0);
    }

    /**
     * Get color class based on grade
     */
    public function getGradeColor()
    {
        switch ($this->letter_grade) {
            case 'A':
                return 'text-green-600';
            case 'B':
                return 'text-blue-600';
            case 'C':
                return 'text-yellow-600';
            case 'D':
                return 'text-orange-600';
            case 'E':
                return 'text-red-500';
            case 'F':
                return 'text-red-700';
            default:
                return 'text-gray-600';
        }
    }

    /**
     * Get background color class based on grade
     */
    public function getGradeBgColor()
    {
        switch ($this->letter_grade) {
            case 'A':
                return 'bg-green-100';
            case 'B':
                return 'bg-blue-100';
            case 'C':
                return 'bg-yellow-100';
            case 'D':
                return 'bg-orange-100';
            case 'E':
                return 'bg-red-100';
            case 'F':
                return 'bg-red-200';
            default:
                return 'bg-gray-100';
        }
    }

    /**
     * Scope for passing grades
     */
    public function scopePassing($query)
    {
        return $query->whereIn('letter_grade', ['A', 'B', 'C', 'D', 'E']);
    }

    /**
     * Scope for failing grades
     */
    public function scopeFailing($query)
    {
        return $query->where('letter_grade', 'F');
    }

    /**
     * Scope for specific grade type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('grade_type', $type);
    }

    /**
     * Scope for specific academic year and semester
     */
    public function scopeForPeriod($query, $academicYearId, $semester = null)
    {
        $query->where('academic_year_id', $academicYearId);
        
        if ($semester) {
            $query->where('semester', $semester);
        }
        
        return $query;
    }

    /**
     * Auto-calculate letter grade and grade point before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($grade) {
            if ($grade->score !== null && $grade->max_score !== null) {
                $grade->letter_grade = $grade->calculateLetterGrade();
                $grade->grade_point = $grade->calculateGradePoint();
            }
        });
    }
}
