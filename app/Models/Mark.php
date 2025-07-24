<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'subject_id',
        'student_id',
        'enrollment_id',
        'theory_marks',
        'practical_marks',
        'internal_marks',
        'total_marks',
        'obtained_marks',
        'percentage',
        'grade_letter',
        'grade_point',
        'status',
        'remarks',
        'entered_by',
        'entered_at'
    ];

    protected $casts = [
        'theory_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'internal_marks' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'entered_at' => 'datetime'
    ];

    /**
     * Get the exam this mark belongs to
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the subject this mark belongs to
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the student this mark belongs to
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the enrollment this mark belongs to
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the user who entered this mark
     */
    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * Calculate total obtained marks
     */
    public function calculateObtainedMarks()
    {
        $total = 0;
        if ($this->theory_marks) $total += $this->theory_marks;
        if ($this->practical_marks) $total += $this->practical_marks;
        if ($this->internal_marks) $total += $this->internal_marks;

        $this->obtained_marks = $total;
        return $total;
    }

    /**
     * Calculate percentage
     */
    public function calculatePercentage()
    {
        if ($this->total_marks > 0) {
            $this->percentage = ($this->obtained_marks / $this->total_marks) * 100;
            return $this->percentage;
        }
        return 0;
    }

    /**
     * Determine grade based on percentage using exam's grading system
     */
    public function determineGrade()
    {
        // Use exam's grading system if available
        if ($this->exam) {
            $gradeScale = $this->exam->getGradeByPercentage($this->percentage);
            if ($gradeScale) {
                $this->grade_letter = $gradeScale->grade_letter;
                $this->grade_point = $gradeScale->grade_point;
                return $gradeScale;
            }
        }

        // Fallback to default grading system
        $gradeScale = GradeScale::where('min_percentage', '<=', $this->percentage)
            ->where('max_percentage', '>=', $this->percentage)
            ->where('status', 'active')
            ->first();

        if ($gradeScale) {
            $this->grade_letter = $gradeScale->grade_letter;
            $this->grade_point = $gradeScale->grade_point;
        }

        return $gradeScale;
    }

    /**
     * Check if student passed
     */
    public function isPassed()
    {
        $passPercentage = CollegeSetting::getPassPercentage();
        return $this->percentage >= $passPercentage;
    }

    /**
     * Scope for passed marks
     */
    public function scopePassed($query)
    {
        $passPercentage = CollegeSetting::getPassPercentage();
        return $query->where('percentage', '>=', $passPercentage);
    }

    /**
     * Scope for failed marks
     */
    public function scopeFailed($query)
    {
        $passPercentage = CollegeSetting::getPassPercentage();
        return $query->where('percentage', '<', $passPercentage);
    }
}
