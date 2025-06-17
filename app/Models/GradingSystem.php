<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingSystem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'is_default',
        'order_sequence'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'order_sequence' => 'integer'
    ];

    /**
     * Scope for active grading systems
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for ordered grading systems
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_sequence')->orderBy('name');
    }

    /**
     * Get the grade scales for this grading system
     */
    public function gradeScales()
    {
        return $this->hasMany(GradeScale::class)->ordered();
    }

    /**
     * Get exams using this grading system
     */
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Get the default grading system
     */
    public static function getDefault()
    {
        return static::active()->where('is_default', true)->first();
    }

    /**
     * Set this as the default grading system
     */
    public function setAsDefault()
    {
        // Remove default from all other systems
        static::where('is_default', true)->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }

    /**
     * Get grade by percentage for this grading system
     */
    public function getGradeByPercentage($percentage)
    {
        return $this->gradeScales()
            ->where('min_percent', '<=', $percentage)
            ->where('max_percent', '>=', $percentage)
            ->first();
    }

    /**
     * Get all active grading systems for selection
     */
    public static function getActiveOptions()
    {
        return static::active()->ordered()->get();
    }

    /**
     * Check if this grading system can be deleted
     */
    public function canBeDeleted()
    {
        return !$this->is_default && $this->exams()->count() === 0;
    }

    /**
     * Get formatted name with code
     */
    public function getFormattedNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Ensure only one default grading system
        static::saving(function ($gradingSystem) {
            if ($gradingSystem->is_default) {
                static::where('id', '!=', $gradingSystem->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });

        // Prevent deletion of default grading system
        static::deleting(function ($gradingSystem) {
            if ($gradingSystem->is_default) {
                throw new \Exception('Cannot delete the default grading system.');
            }
        });
    }
}
