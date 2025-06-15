<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'subject_id',
        'theory_marks',
        'practical_marks',
        'pass_marks_theory',
        'pass_marks_practical',
        'is_active'
    ];

    protected $casts = [
        'theory_marks' => 'integer',
        'practical_marks' => 'integer',
        'pass_marks_theory' => 'integer',
        'pass_marks_practical' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get the exam this belongs to
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the subject this belongs to
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get total marks (theory + practical)
     */
    public function getTotalMarksAttribute()
    {
        return ($this->theory_marks ?? 0) + ($this->practical_marks ?? 0);
    }

    /**
     * Get total pass marks (theory + practical)
     */
    public function getTotalPassMarksAttribute()
    {
        return ($this->pass_marks_theory ?? 0) + ($this->pass_marks_practical ?? 0);
    }

    /**
     * Load marks from subject
     */
    public function loadMarksFromSubject()
    {
        if ($this->subject) {
            $this->theory_marks = $this->subject->full_marks_theory;
            $this->practical_marks = $this->subject->full_marks_practical;
            $this->pass_marks_theory = $this->subject->pass_marks_theory;
            $this->pass_marks_practical = $this->subject->pass_marks_practical;
        }
    }
}
