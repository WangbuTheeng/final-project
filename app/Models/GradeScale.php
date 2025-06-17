<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'grading_system_id',
        'grade_letter',
        'grade_point',
        'min_percent',
        'max_percent',
        'description',
        'status',
        'order_sequence'
    ];

    protected $casts = [
        'grade_point' => 'decimal:2',
        'min_percent' => 'decimal:2',
        'max_percent' => 'decimal:2',
        'order_sequence' => 'integer'
    ];

    /**
     * Get the grading system this grade scale belongs to
     */
    public function gradingSystem()
    {
        return $this->belongsTo(GradingSystem::class);
    }

    /**
     * Scope for active grade scales
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for ordered grade scales
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_sequence');
    }

    /**
     * Get grade by percentage for a specific grading system
     */
    public static function getGradeByPercentage($percentage, $gradingSystemId = null)
    {
        $query = static::active()
            ->where('min_percent', '<=', $percentage)
            ->where('max_percent', '>=', $percentage);

        if ($gradingSystemId) {
            $query->where('grading_system_id', $gradingSystemId);
        }

        return $query->first();
    }

    /**
     * Get all active grades ordered
     */
    public static function getActiveGrades()
    {
        return static::active()->ordered()->get();
    }

    /**
     * Check if percentage falls in this grade range
     */
    public function containsPercentage($percentage)
    {
        return $percentage >= $this->min_percent && $percentage <= $this->max_percent;
    }

    /**
     * Get formatted grade display
     */
    public function getFormattedGradeAttribute()
    {
        return $this->grade_letter . ' (' . $this->grade_point . ')';
    }

    /**
     * Get percentage range display
     */
    public function getPercentageRangeAttribute()
    {
        return $this->min_percent . '% - ' . $this->max_percent . '%';
    }
}
