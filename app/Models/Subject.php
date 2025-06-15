<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'class_id',
        'instructor_id',
        'order_sequence',
        'duration_hours',
        'credit_weight',
        'start_date',
        'end_date',
        'learning_objectives',
        'resources',
        'difficulty_level',
        'subject_type',
        'is_mandatory',
        'is_active',
        'full_marks_theory',
        'pass_marks_theory',
        'full_marks_practical',
        'pass_marks_practical',
        'is_practical'
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'is_practical' => 'boolean',
        'order_sequence' => 'integer',
        'duration_hours' => 'integer',
        'credit_weight' => 'integer',
        'full_marks_theory' => 'integer',
        'pass_marks_theory' => 'integer',
        'full_marks_practical' => 'integer',
        'pass_marks_practical' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'learning_objectives' => 'array',
        'resources' => 'array'
    ];

    /**
     * Get the class this subject belongs to
     */
    public function class()
    {
        return $this->belongsTo(ClassSection::class, 'class_id');
    }

    /**
     * Get the instructor for this subject
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the course through the class relationship
     */
    public function course()
    {
        return $this->hasOneThrough(Course::class, ClassSection::class, 'id', 'id', 'class_id', 'course_id');
    }

    /**
     * Get the faculty through class → course → faculty
     */
    public function faculty()
    {
        return $this->class->course->faculty ?? null;
    }

    /**
     * Scope to get active subjects
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by class
     */
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope to filter by difficulty level
     */
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    /**
     * Scope to filter by subject type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('subject_type', $type);
    }

    /**
     * Scope to get mandatory subjects
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope to get optional subjects
     */
    public function scopeOptional($query)
    {
        return $query->where('is_mandatory', false);
    }

    /**
     * Scope to order by sequence
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_sequence');
    }

    /**
     * Get the next subject in sequence
     */
    public function getNextSubject()
    {
        return static::where('class_id', $this->class_id)
            ->where('order_sequence', '>', $this->order_sequence)
            ->orderBy('order_sequence')
            ->first();
    }

    /**
     * Get the previous subject in sequence
     */
    public function getPreviousSubject()
    {
        return static::where('class_id', $this->class_id)
            ->where('order_sequence', '<', $this->order_sequence)
            ->orderBy('order_sequence', 'desc')
            ->first();
    }

    /**
     * Check if subject can be deleted
     */
    public function canBeDeleted()
    {
        // Add any business logic for deletion constraints
        // For example, check if there are exams, assignments, etc.
        return true;
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_hours) {
            return 'Not specified';
        }

        if ($this->duration_hours == 1) {
            return '1 hour';
        }

        return $this->duration_hours . ' hours';
    }

    /**
     * Get progress percentage (can be extended with actual progress tracking)
     */
    public function getProgressPercentageAttribute()
    {
        // This can be extended to track actual progress
        // For now, return 0 as placeholder
        return 0;
    }

    /**
     * Get display name with sequence
     */
    public function getDisplayNameAttribute()
    {
        return $this->order_sequence . '. ' . $this->name;
    }

    /**
     * Scope to filter practical subjects
     */
    public function scopePractical($query)
    {
        return $query->where('is_practical', true);
    }

    /**
     * Scope to filter theory subjects
     */
    public function scopeTheory($query)
    {
        return $query->where('is_practical', false);
    }

    /**
     * Get total full marks (theory + practical)
     */
    public function getTotalFullMarksAttribute()
    {
        return ($this->full_marks_theory ?? 0) + ($this->full_marks_practical ?? 0);
    }

    /**
     * Get total pass marks (theory + practical)
     */
    public function getTotalPassMarksAttribute()
    {
        return ($this->pass_marks_theory ?? 0) + ($this->pass_marks_practical ?? 0);
    }

    /**
     * Check if subject has theory component
     */
    public function hasTheoryComponent()
    {
        return $this->full_marks_theory > 0;
    }

    /**
     * Check if subject has practical component
     */
    public function hasPracticalComponent()
    {
        return $this->full_marks_practical > 0 || $this->is_practical;
    }

    /**
     * Get marks breakdown for display
     */
    public function getMarksBreakdownAttribute()
    {
        $breakdown = [];

        if ($this->hasTheoryComponent()) {
            $breakdown['theory'] = [
                'full_marks' => $this->full_marks_theory,
                'pass_marks' => $this->pass_marks_theory
            ];
        }

        if ($this->hasPracticalComponent()) {
            $breakdown['practical'] = [
                'full_marks' => $this->full_marks_practical,
                'pass_marks' => $this->pass_marks_practical
            ];
        }

        return $breakdown;
    }
}
