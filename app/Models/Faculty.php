<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'dean_id',
        'location',
        'phone',
        'email',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the dean of this faculty
     */
    public function dean()
    {
        return $this->belongsTo(User::class, 'dean_id');
    }

    /**
     * Get departments in this faculty
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get active departments in this faculty
     */
    public function activeDepartments()
    {
        return $this->departments()->where('is_active', true);
    }

    /**
     * Get courses through departments
     */
    public function courses()
    {
        return $this->hasManyThrough(Course::class, Department::class);
    }

    /**
     * Get active courses through departments
     */
    public function activeCourses()
    {
        return $this->courses()->where('courses.is_active', true);
    }

    /**
     * Get all courses through departments
     */
    public function allCourses()
    {
        return $this->hasManyThrough(Course::class, Department::class);
    }

    /**
     * Scope to get active faculties
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get total number of departments
     */
    public function getDepartmentCountAttribute()
    {
        return $this->departments()->count();
    }

    /**
     * Get total number of active departments
     */
    public function getActiveDepartmentCountAttribute()
    {
        return $this->activeDepartments()->count();
    }

    /**
     * Check if faculty can be deleted
     */
    public function canBeDeleted()
    {
        return $this->departments()->count() === 0;
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }
}
