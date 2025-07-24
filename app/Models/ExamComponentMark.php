<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ExamComponentMark extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'exam_id',
        'exam_component_id',
        'student_id',
        'subject_id',
        'enrollment_id',
        'marks_obtained',
        'total_marks',
        'percentage',
        'status',
        'remarks',
        'submitted_at',
        'verified_at',
        'entered_by',
        'verified_by'
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime'
    ];

    /**
     * Get the exam this mark belongs to
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the exam component this mark belongs to
     */
    public function examComponent()
    {
        return $this->belongsTo(ExamComponent::class);
    }

    /**
     * Get the student this mark belongs to
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject this mark belongs to
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the enrollment this mark belongs to
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the user who entered this mark
     */
    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * Get the user who verified this mark
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope for specific status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for submitted marks
     */
    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', ['submitted', 'verified', 'published']);
    }

    /**
     * Scope for verified marks
     */
    public function scopeVerified($query)
    {
        return $query->whereIn('status', ['verified', 'published']);
    }

    /**
     * Scope for published marks
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Calculate percentage automatically
     */
    public function calculatePercentage()
    {
        if ($this->total_marks > 0) {
            $this->percentage = ($this->marks_obtained / $this->total_marks) * 100;
        } else {
            $this->percentage = 0;
        }
        return $this->percentage;
    }

    /**
     * Check if mark is passing
     */
    public function isPassing()
    {
        $passPercentage = $this->exam->minimum_pass_percentage ?? 32;
        return $this->percentage >= $passPercentage;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'verified' => 'Verified',
            'published' => 'Published'
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'gray',
            'submitted' => 'blue',
            'verified' => 'green',
            'published' => 'purple'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Submit marks for verification
     */
    public function submit()
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now()
        ]);
    }

    /**
     * Verify marks
     */
    public function verify($verifiedBy = null)
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $verifiedBy ?? auth()->id()
        ]);
    }

    /**
     * Publish marks
     */
    public function publish()
    {
        $this->update(['status' => 'published']);
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate percentage before saving
        static::saving(function ($mark) {
            $mark->calculatePercentage();
        });
    }

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['marks_obtained', 'status', 'remarks'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
