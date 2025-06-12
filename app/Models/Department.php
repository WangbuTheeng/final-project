<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'faculty_id',
        'hod_id',
        'location',
        'phone',
        'email',
        'duration_years',
        'degree_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_years' => 'integer'
    ];

    /**
     * Get the faculty this department belongs to
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the head of department
     */
    public function hod()
    {
        return $this->belongsTo(User::class, 'hod_id');
    }

    /**
     * Get courses optionally associated with this department
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get active courses optionally associated with this department
     */
    public function activeCourses()
    {
        return $this->courses()->where('is_active', true);
    }

    /**
     * Get students in this department
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get active students in this department
     */
    public function activeStudents()
    {
        return $this->students()->where('status', 'active');
    }

    /**
     * Scope to get active departments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by faculty
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    /**
     * Get total number of courses
     */
    public function getCourseCountAttribute()
    {
        return $this->courses()->count();
    }

    /**
     * Get total number of students
     */
    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }

    /**
     * Check if department can be deleted
     */
    public function canBeDeleted()
    {
        return $this->courses()->count() === 0 &&
               $this->students()->count() === 0;
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * Get full name with faculty
     */
    public function getFullNameAttribute()
    {
        return $this->faculty->name . ' - ' . $this->name;
    }
}
