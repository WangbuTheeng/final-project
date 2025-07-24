<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'education_level',
        'assessment_category',
        'default_weightage',
        'default_duration_minutes',
        'is_active',
        'order_sequence',
        'applicable_streams',
        'applicable_programs'
    ];

    protected $casts = [
        'default_weightage' => 'decimal:2',
        'default_duration_minutes' => 'integer',
        'is_active' => 'boolean',
        'order_sequence' => 'integer',
        'applicable_streams' => 'array',
        'applicable_programs' => 'array'
    ];

    /**
     * Scope for active exam types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered exam types
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
     * Scope for specific assessment category
     */
    public function scopeForAssessmentCategory($query, $category)
    {
        return $query->where(function ($q) use ($category) {
            $q->where('assessment_category', $category)
              ->orWhere('assessment_category', 'both');
        });
    }

    /**
     * Get exams using this exam type
     */
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Check if exam type is applicable for given stream/program
     */
    public function isApplicableFor($streamOrProgram, $educationLevel)
    {
        // Check education level compatibility
        if ($this->education_level !== 'both' && $this->education_level !== $educationLevel) {
            return false;
        }

        // Check stream/program compatibility
        if ($educationLevel === 'plus_two') {
            return empty($this->applicable_streams) || in_array($streamOrProgram, $this->applicable_streams ?? []);
        } else {
            return empty($this->applicable_programs) || in_array($streamOrProgram, $this->applicable_programs ?? []);
        }
    }

    /**
     * Get formatted name with education level
     */
    public function getFormattedNameAttribute()
    {
        $level = $this->education_level === 'both' ? 'All Levels' : ucfirst(str_replace('_', ' ', $this->education_level));
        return $this->name . ' (' . $level . ')';
    }

    /**
     * Get exam types for +2 level
     */
    public static function getPlusTwoTypes()
    {
        return static::active()
            ->forEducationLevel('plus_two')
            ->ordered()
            ->get();
    }

    /**
     * Get exam types for Bachelor's level
     */
    public static function getBachelorsTypes()
    {
        return static::active()
            ->forEducationLevel('bachelors')
            ->ordered()
            ->get();
    }

    /**
     * Get internal assessment exam types
     */
    public static function getInternalTypes($educationLevel = null)
    {
        $query = static::active()
            ->forAssessmentCategory('internal')
            ->ordered();

        if ($educationLevel) {
            $query->forEducationLevel($educationLevel);
        }

        return $query->get();
    }

    /**
     * Get external assessment exam types
     */
    public static function getExternalTypes($educationLevel = null)
    {
        $query = static::active()
            ->forAssessmentCategory('external')
            ->ordered();

        if ($educationLevel) {
            $query->forEducationLevel($educationLevel);
        }

        return $query->get();
    }
}
