<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class AcademicYear extends Model
{
    use HasFactory;



    protected $fillable = [
        'name',
        'code',
        'start_date',
        'end_date',
        'is_current',
        'is_active',
        'description',
        'semester_config'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_active' => 'boolean',
        'semester_config' => 'array'
    ];

    /**
     * Set this academic year as the current one
     * Ensures only one academic year is current at a time
     */
    public function setAsCurrent()
    {
        DB::transaction(function () {
            // Set all other academic years as not current
            static::where('is_current', true)->update(['is_current' => false]);

            // Set this academic year as current
            $this->update(['is_current' => true]);
        });
    }

    /**
     * Set this academic year as the active one
     * Ensures only one academic year is active at a time
     */
    public function setAsActive()
    {
        DB::transaction(function () {
            // Set all other academic years as not active
            static::where('is_active', true)->update(['is_active' => false]);

            // Set this academic year as active
            $this->update(['is_active' => true]);
        });
    }

    /**
     * Get the current academic year
     */
    public static function current()
    {
        return static::where('is_current', true)->first();
    }

    /**
     * Get the active academic year
     */
    public static function active()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Scope to get active academic years
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get current academic year
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Get classes for this academic year
     */
    public function classes()
    {
        return $this->hasMany(ClassSection::class);
    }

    /**
     * Get students admitted in this academic year
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get fees for this academic year
     */
    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    /**
     * Check if this academic year can be deleted
     */
    public function canBeDeleted()
    {
        return !$this->is_current &&
               $this->classes()->count() === 0 &&
               $this->students()->count() === 0;
    }

    /**
     * Get formatted name for display
     */
    public function getFormattedNameAttribute()
    {
        return $this->name . ($this->is_current ? ' (Current)' : '');
    }
}
