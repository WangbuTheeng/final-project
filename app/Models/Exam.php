<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Exam
 * 
 * Represents an exam in the college management system.
 * 
 * @package App\Models
 * @property int $id
 * @property string $title
 * @property int $class_id
 * @property int $academic_year_id
 * @property string $exam_type
 * @property string $semester
 * @property \Carbon\Carbon $exam_date
 * @property int $duration_minutes
 * @property float $total_marks
 * @property float $pass_mark
 * @property string|null $venue
 * @property string|null $instructions
 * @property string $status
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Exam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'class_id',
        'academic_year_id',
        'exam_type',
        'semester',
        'exam_date',
        'duration_minutes',
        'total_marks',
        'pass_mark',
        'venue',
        'instructions',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'exam_date' => 'datetime',
        'total_marks' => 'decimal:2',
        'pass_mark' => 'decimal:2',
        'duration_minutes' => 'integer',
    ];

    /**
     * Exam type constants
     */
    public const TYPE_QUIZ = 'quiz';
    public const TYPE_TEST = 'test';
    public const TYPE_MIDTERM = 'midterm';
    public const TYPE_FINAL = 'final';
    public const TYPE_PRACTICAL = 'practical';
    public const TYPE_ASSIGNMENT = 'assignment';

    /**
     * Exam status constants
     */
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Semester constants
     */
    public const SEMESTER_FIRST = 'first';
    public const SEMESTER_SECOND = 'second';

    /**
     * Get the class section that owns the exam.
     *
     * @return BelongsTo
     */
    public function classSection(): BelongsTo
    {
        return $this->belongsTo(ClassSection::class, 'class_id');
    }

    /**
     * Get the academic year that owns the exam.
     *
     * @return BelongsTo
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the user who created the exam.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the grades for the exam.
     *
     * @return HasMany
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Scope a query to only include exams of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('exam_type', $type);
    }

    /**
     * Scope a query to only include exams with a given status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include exams for a given semester.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $semester
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForSemester($query, string $semester)
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope a query to only include upcoming exams.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('exam_date', '>', now())
                    ->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * Check if the exam is scheduled.
     *
     * @return bool
     */
    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Check if the exam is ongoing.
     *
     * @return bool
     */
    public function isOngoing(): bool
    {
        return $this->status === self::STATUS_ONGOING;
    }

    /**
     * Check if the exam is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the exam is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Get the exam duration in hours and minutes format.
     *
     * @return string
     */
    public function getFormattedDuration(): string
    {
        $hours = intval($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        
        return $minutes . 'm';
    }

    /**
     * Get the pass percentage for the exam.
     *
     * @return float
     */
    public function getPassPercentage(): float
    {
        return ($this->pass_mark / $this->total_marks) * 100;
    }

    /**
     * Get all available exam types.
     *
     * @return array
     */
    public static function getExamTypes(): array
    {
        return [
            self::TYPE_QUIZ => 'Quiz',
            self::TYPE_TEST => 'Test',
            self::TYPE_MIDTERM => 'Midterm',
            self::TYPE_FINAL => 'Final',
            self::TYPE_PRACTICAL => 'Practical',
            self::TYPE_ASSIGNMENT => 'Assignment',
        ];
    }

    /**
     * Get all available exam statuses.
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_ONGOING => 'Ongoing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Get all available semesters.
     *
     * @return array
     */
    public static function getSemesters(): array
    {
        return [
            self::SEMESTER_FIRST => 'First Semester',
            self::SEMESTER_SECOND => 'Second Semester',
        ];
    }
}
