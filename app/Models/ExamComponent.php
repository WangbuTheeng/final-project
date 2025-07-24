<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'component_type',
        'default_marks',
        'default_weightage',
        'education_level',
        'is_active',
        'order_sequence',
        'applicable_programs'
    ];

    protected $casts = [
        'default_marks' => 'decimal:2',
        'default_weightage' => 'decimal:2',
        'is_active' => 'boolean',
        'order_sequence' => 'integer',
        'applicable_programs' => 'array'
    ];

    /**
     * Scope for active components
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered components
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_sequence')->orderBy('name');
    }

    /**
     * Scope for specific education level
     */
    public function scopeForEducationLevel($query, $level)
    {
        return $query->where(function ($q) use ($level) {
            $q->where('education_level', $level)
              ->orWhere('education_level', 'both');
        });
    }

    /**
     * Scope for specific component type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('component_type', $type);
    }

    /**
     * Get component marks for this component
     */
    public function componentMarks()
    {
        return $this->hasMany(ExamComponentMark::class);
    }

    /**
     * Check if component is applicable for given program
     */
    public function isApplicableFor($program)
    {
        return empty($this->applicable_programs) || in_array($program, $this->applicable_programs ?? []);
    }

    /**
     * Get formatted name with type
     */
    public function getFormattedNameAttribute()
    {
        return $this->name . ' (' . ucfirst($this->component_type) . ')';
    }

    /**
     * Get component type label
     */
    public function getComponentTypeLabelAttribute()
    {
        $labels = [
            'attendance' => 'Attendance',
            'assignment' => 'Assignment',
            'quiz' => 'Quiz',
            'presentation' => 'Presentation',
            'practical' => 'Practical',
            'viva' => 'Viva-Voce',
            'project' => 'Project',
            'midterm' => 'Mid-term',
            'other' => 'Other'
        ];

        return $labels[$this->component_type] ?? ucfirst($this->component_type);
    }

    /**
     * Get components for +2 level
     */
    public static function getPlusTwoComponents()
    {
        return static::active()
            ->forEducationLevel('plus_two')
            ->ordered()
            ->get();
    }

    /**
     * Get components for Bachelor's level
     */
    public static function getBachelorsComponents()
    {
        return static::active()
            ->forEducationLevel('bachelors')
            ->ordered()
            ->get();
    }

    /**
     * Get components by type
     */
    public static function getComponentsByType($type, $educationLevel = null)
    {
        $query = static::active()
            ->ofType($type)
            ->ordered();

        if ($educationLevel) {
            $query->forEducationLevel($educationLevel);
        }

        return $query->get();
    }

    /**
     * Get default internal assessment components for Bachelor's
     */
    public static function getDefaultBachelorsInternalComponents()
    {
        return [
            ['name' => 'Attendance', 'code' => 'ATT', 'component_type' => 'attendance', 'default_marks' => 10, 'default_weightage' => 25],
            ['name' => 'Assignment', 'code' => 'ASG', 'component_type' => 'assignment', 'default_marks' => 15, 'default_weightage' => 37.5],
            ['name' => 'Quiz/Test', 'code' => 'QUZ', 'component_type' => 'quiz', 'default_marks' => 10, 'default_weightage' => 25],
            ['name' => 'Presentation', 'code' => 'PRE', 'component_type' => 'presentation', 'default_marks' => 5, 'default_weightage' => 12.5],
        ];
    }

    /**
     * Get default internal assessment components for +2
     */
    public static function getDefaultPlusTwoInternalComponents()
    {
        return [
            ['name' => 'Class Test', 'code' => 'CT', 'component_type' => 'quiz', 'default_marks' => 20, 'default_weightage' => 50],
            ['name' => 'Assignment', 'code' => 'ASG', 'component_type' => 'assignment', 'default_marks' => 15, 'default_weightage' => 37.5],
            ['name' => 'Attendance', 'code' => 'ATT', 'component_type' => 'attendance', 'default_marks' => 5, 'default_weightage' => 12.5],
        ];
    }
}
