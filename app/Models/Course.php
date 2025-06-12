<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'description',
        'faculty_id',
        'department_id',
        'credit_units',
        'level',
        'semester',
        'course_type',
        'prerequisites',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_units' => 'integer',
        'level' => 'integer',
        'prerequisites' => 'array'
    ];

    /**
     * Get the faculty this course belongs to
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the department this course belongs to (optional)
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get class sections for this course
     */
    public function classes()
    {
        return $this->hasMany(ClassSection::class);
    }

    /**
     * Get active class sections for this course
     */
    public function activeClasses()
    {
        return $this->classes()->where('status', 'active');
    }

    /**
     * Get prerequisite courses
     */
    public function prerequisiteCourses()
    {
        if (empty($this->prerequisites)) {
            return collect();
        }

        return static::whereIn('id', $this->prerequisites)->get();
    }

    /**
     * Get courses that have this course as prerequisite
     */
    public function dependentCourses()
    {
        return static::whereJsonContains('prerequisites', $this->id)->get();
    }

    /**
     * Scope to get active courses
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
     * Scope to filter by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope to filter by level
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope to filter by semester
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope to filter by course type
     */
    public function scopeByCourseType($query, $type)
    {
        return $query->where('course_type', $type);
    }

    /**
     * Check if course can be deleted
     */
    public function canBeDeleted()
    {
        return $this->classes()->count() === 0 &&
               $this->dependentCourses()->count() === 0;
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->code . ' - ' . $this->title;
    }

    /**
     * Get level name
     */
    public function getLevelNameAttribute()
    {
        $levels = [
            100 => '100 Level',
            200 => '200 Level',
            300 => '300 Level',
            400 => '400 Level',
            500 => '500 Level'
        ];

        return $levels[$this->level] ?? $this->level . ' Level';
    }

    /**
     * Get semester name
     */
    public function getSemesterNameAttribute()
    {
        $semesters = [
            'first' => 'First Semester',
            'second' => 'Second Semester',
            'both' => 'Both Semesters'
        ];

        return $semesters[$this->semester] ?? $this->semester;
    }

    /**
     * Get course type name
     */
    public function getCourseTypeNameAttribute()
    {
        $types = [
            'core' => 'Core Course',
            'elective' => 'Elective Course',
            'general' => 'General Studies'
        ];

        return $types[$this->course_type] ?? $this->course_type;
    }
}
