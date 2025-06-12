<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Grade
 * 
 * Represents a grade/result for a student in an exam.
 * 
 * @package App\Models
 * @property int $id
 * @property int $student_id
 * @property int $enrollment_id
 * @property int|null $exam_id
 * @property int $academic_year_id
 * @property string $semester
 * @property string $grade_type
 * @property float $score
 * @property float $max_score
 * @property string|null $letter_grade
 * @property float|null $grade_point
 * @property string|null $remarks
 * @property int $graded_by
 * @property \Carbon\Carbon $graded_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Grade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'enrollment_id',
        'exam_id',
        'academic_year_id',
        'semester',
        'grade_type',
        'score',
        'max_score',
        'letter_grade',
        'grade_point',
        'remarks',
        'graded_by',
        'graded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'graded_at' => 'datetime',
    ];

    /**
     * Grade type constants
     */
    public const TYPE_CA = 'ca'; // Continuous Assessment
    public const TYPE_EXAM = 'exam';
    public const TYPE_FINAL = 'final';

    /**
     * Semester constants
     */
    public const SEMESTER_FIRST = 'first';
    public const SEMESTER_SECOND = 'second';

    /**
     * Grade letter constants
     */
    public const GRADE_A = 'A';
    public const GRADE_B = 'B';
    public const GRADE_C = 'C';
    public const GRADE_D = 'D';
    public const GRADE_E = 'E';
    public const GRADE_F = 'F';

    /**
     * Get the student that owns the grade.
     *
     * @return BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the enrollment that owns the grade.
     *
     * @return BelongsTo
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the exam that owns the grade.
     *
     * @return BelongsTo
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the academic year that owns the grade.
     *
     * @return BelongsTo
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the user who graded this.
     *
     * @return BelongsTo
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Scope a query to only include grades of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('grade_type', $type);
    }

    /**
     * Scope a query to only include grades for a given semester.
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
     * Calculate and set the letter grade based on score percentage.
     *
     * @return void
     */
    public function calculateLetterGrade(): void
    {
        $percentage = ($this->score / $this->max_score) * 100;
        
        if ($percentage >= 80) {
            $this->letter_grade = self::GRADE_A;
            $this->grade_point = 5.0;
        } elseif ($percentage >= 70) {
            $this->letter_grade = self::GRADE_B;
            $this->grade_point = 4.0;
        } elseif ($percentage >= 60) {
            $this->letter_grade = self::GRADE_C;
            $this->grade_point = 3.0;
        } elseif ($percentage >= 50) {
            $this->letter_grade = self::GRADE_D;
            $this->grade_point = 2.0;
        } elseif ($percentage >= 40) {
            $this->letter_grade = self::GRADE_E;
            $this->grade_point = 1.0;
        } else {
            $this->letter_grade = self::GRADE_F;
            $this->grade_point = 0.0;
        }
    }

    /**
     * Get the percentage score.
     *
     * @return float
     */
    public function getPercentage(): float
    {
        return ($this->score / $this->max_score) * 100;
    }

    /**
     * Check if the grade is a passing grade.
     *
     * @return bool
     */
    public function isPassing(): bool
    {
        return $this->letter_grade !== self::GRADE_F;
    }

    /**
     * Get the grade status (Pass/Fail).
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->isPassing() ? 'Pass' : 'Fail';
    }

    /**
     * Get all available grade types.
     *
     * @return array
     */
    public static function getGradeTypes(): array
    {
        return [
            self::TYPE_CA => 'Continuous Assessment',
            self::TYPE_EXAM => 'Exam',
            self::TYPE_FINAL => 'Final Grade',
        ];
    }

    /**
     * Get all available letter grades.
     *
     * @return array
     */
    public static function getLetterGrades(): array
    {
        return [
            self::GRADE_A => 'A (80-100%)',
            self::GRADE_B => 'B (70-79%)',
            self::GRADE_C => 'C (60-69%)',
            self::GRADE_D => 'D (50-59%)',
            self::GRADE_E => 'E (40-49%)',
            self::GRADE_F => 'F (0-39%)',
        ];
    }

    /**
     * Get grade point for a letter grade.
     *
     * @param string $letterGrade
     * @return float
     */
    public static function getGradePoint(string $letterGrade): float
    {
        $gradePoints = [
            self::GRADE_A => 5.0,
            self::GRADE_B => 4.0,
            self::GRADE_C => 3.0,
            self::GRADE_D => 2.0,
            self::GRADE_E => 1.0,
            self::GRADE_F => 0.0,
        ];

        return $gradePoints[$letterGrade] ?? 0.0;
    }
}
