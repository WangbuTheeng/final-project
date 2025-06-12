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
        'is_active'
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'order_sequence' => 'integer',
        'duration_hours' => 'integer',
        'credit_weight' => 'integer',
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
}
