<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassSection extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'course_id',
        'academic_year_id',
        'instructor_id',
        'semester',
        'year',
        'room',
        'schedule',
        'capacity',
        'enrolled_count',
        'status',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'schedule' => 'array',
        'capacity' => 'integer',
        'enrolled_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    /**
     * Get the course this class belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the academic year this class belongs to
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the instructor for this class
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get enrollments for this class
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    /**
     * Get active enrollments for this class
     */
    public function activeEnrollments()
    {
        return $this->enrollments()->where('status', 'enrolled');
    }

    /**
     * Get exams for this class
     */
    public function exams()
    {
        return $this->hasMany(Exam::class, 'class_id');
    }

    /**
     * Get subjects for this class
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    /**
     * Get active subjects for this class
     */
    public function activeSubjects()
    {
        return $this->subjects()->where('is_active', true)->orderBy('order_sequence');
    }

    /**
     * Scope to get active classes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by academic year
     */
    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope to filter by semester
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope to filter by instructor
     */
    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Check if class has available slots
     */
    public function hasAvailableSlots()
    {
        return $this->enrolled_count < $this->capacity;
    }

    /**
     * Get available slots
     */
    public function getAvailableSlotsAttribute()
    {
        return max(0, $this->capacity - $this->enrolled_count); // Changed to enrolled_count
    }

    /**
     * Get enrollment percentage
     */
    public function getEnrollmentPercentageAttribute()
    {
        if ($this->capacity == 0) {
            return 0;
        }

        return round(($this->enrolled_count / $this->capacity) * 100, 2); // Changed to enrolled_count
    }

    /**
     * Update enrolled count
     */
    public function updateEnrolledCount()
    {
        $this->enrolled_count = $this->activeEnrollments()->count(); // Changed to enrolled_count
        $this->save();
    }

    /**
     * Get total marks from all subjects in this class
     */
    public function getTotalSubjectMarks()
    {
        return $this->subjects()
            ->active()
            ->get()
            ->sum(function ($subject) {
                return $subject->total_full_marks;
            });
    }

    /**
     * Get total theory marks from all subjects in this class
     */
    public function getTotalTheoryMarks()
    {
        return $this->subjects()
            ->active()
            ->sum('full_marks_theory');
    }

    /**
     * Get total practical marks from all subjects in this class
     */
    public function getTotalPracticalMarks()
    {
        return $this->subjects()
            ->active()
            ->sum('full_marks_practical');
    }

    /**
     * Get total pass marks from all subjects in this class
     */
    public function getTotalPassMarks()
    {
        return $this->subjects()
            ->active()
            ->get()
            ->sum(function ($subject) {
                return $subject->total_pass_marks;
            });
    }

    /**
     * Get marks breakdown for all subjects in this class
     */
    public function getSubjectMarksBreakdown()
    {
        $subjects = $this->subjects()->active()->get();

        $breakdown = [
            'total_marks' => 0,
            'total_theory_marks' => 0,
            'total_practical_marks' => 0,
            'total_pass_marks' => 0,
            'subjects' => [],
            'subject_count' => $subjects->count()
        ];

        foreach ($subjects as $subject) {
            $subjectData = [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
                'theory_marks' => $subject->full_marks_theory ?? 0,
                'practical_marks' => $subject->full_marks_practical ?? 0,
                'total_marks' => $subject->total_full_marks,
                'pass_marks' => $subject->total_pass_marks,
                'is_practical' => $subject->is_practical,
                'has_theory' => $subject->hasTheoryComponent(),
                'has_practical' => $subject->hasPracticalComponent(),
            ];

            $breakdown['subjects'][] = $subjectData;
            $breakdown['total_marks'] += $subjectData['total_marks'];
            $breakdown['total_theory_marks'] += $subjectData['theory_marks'];
            $breakdown['total_practical_marks'] += $subjectData['practical_marks'];
            $breakdown['total_pass_marks'] += $subjectData['pass_marks'];
        }

        return $breakdown;
    }

    // Removed the redundant getEnrolledCountAttribute as 'enrolled_count' is a direct column
    // The accessor was aliasing 'current_enrollment' which was not a column.
    // Now 'enrolled_count' is the direct column and should be accessed directly.

    /**
     * Check if class can be deleted
     */
    public function canBeDeleted()
    {
        return $this->enrollments()->count() === 0 &&
               $this->exams()->count() === 0;
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->course->code . ' - ' . $this->name;
    }

    /**
     * Get semester name
     */
    public function getSemesterNameAttribute()
    {
        return 'Semester ' . $this->semester;
    }

    /**
     * Get status name
     */
    public function getStatusNameAttribute()
    {
        $statuses = [
            'active' => 'Active',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get formatted schedule
     */
    public function getFormattedScheduleAttribute()
    {
        if (empty($this->schedule)) {
            return 'Not scheduled';
        }

        $formatted = [];
        foreach ($this->schedule as $session) {
            $day = $session['day'] ?? '';
            $time = $session['time'] ?? '';
            $duration = $session['duration'] ?? '';

            if ($day && $time) {
                $formatted[] = $day . ' ' . $time . ($duration ? ' (' . $duration . ')' : '');
            }
        }

        return implode(', ', $formatted);
    }
}
