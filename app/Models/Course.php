<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Course extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'code',
        'description',
        'department_id',
        'credit_units',
        'organization_type',
        'year',
        'semester_period',
        'course_type',
        'examination_system',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_units' => 'integer',
        'year' => 'integer'
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title',
                'code',
                'description',
                'department_id',
                'credit_units',
                'organization_type',
                'year',
                'semester_period',
                'course_type',
                'is_active'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Course {$eventName}")
            ->useLogName('course_management');
    }

    /**
     * Get the faculty this course belongs to through department
     */
    public function faculty()
    {
        return $this->hasOneThrough(
            Faculty::class,
            Department::class,
            'id',           // Foreign key on departments table
            'id',           // Foreign key on faculties table
            'department_id', // Local key on courses table
            'faculty_id'    // Local key on departments table
        );
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
     * Get the display text for the course period (year/semester)
     */
    public function getPeriodDisplayAttribute()
    {
        if ($this->organization_type === 'yearly') {
            return $this->year . ($this->year == 1 ? 'st' : ($this->year == 2 ? 'nd' : ($this->year == 3 ? 'rd' : 'th'))) . ' Year';
        } else {
            return 'Semester ' . $this->semester_period;
        }
    }

    /**
     * Get available years for yearly organization (1-4)
     */
    public static function getYearlyOptions()
    {
        return [1, 2, 3, 4];
    }

    /**
     * Get available semesters for semester organization (1-8)
     */
    public static function getSemesterOptions()
    {
        return [1, 2, 3, 4, 5, 6, 7, 8];
    }

    /**
     * Scope to filter by organization type
     */
    public function scopeByOrganizationType($query, $type)
    {
        return $query->where('organization_type', $type);
    }

    /**
     * Get active class sections for this course
     */
    public function activeClasses()
    {
        return $this->classes()->where('status', 'active');
    }



    /**
     * Scope to get active courses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by faculty through department
     */
    public function scopeByFaculty($query, $facultyId)
    {
        return $query->whereHas('department', function ($q) use ($facultyId) {
            $q->where('faculty_id', $facultyId);
        });
    }

    /**
     * Scope to filter by department
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }



    /**
     * Scope to filter by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to filter by semester period
     */
    public function scopeBySemesterPeriod($query, $semesterPeriod)
    {
        return $query->where('semester_period', $semesterPeriod);
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
        return $this->classes()->count() === 0;
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->code . ' - ' . $this->title;
    }



    /**
     * Get semester period name
     */
    public function getSemesterPeriodNameAttribute()
    {
        if ($this->organization_type === 'semester' && $this->semester_period) {
            return 'Semester ' . $this->semester_period;
        }
        return null;
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
