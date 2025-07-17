<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'action',
        'description',
        'properties',
        'ip_address',
        'user_agent',
        'batch_uuid',
        'event_type',
        'severity',
        'tags'
    ];

    protected $casts = [
        'properties' => 'array',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Activity types
    const TYPE_CREATE = 'create';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';
    const TYPE_VIEW = 'view';
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_EXPORT = 'export';
    const TYPE_IMPORT = 'import';
    const TYPE_APPROVE = 'approve';
    const TYPE_REJECT = 'reject';

    // Event types
    const EVENT_USER = 'user';
    const EVENT_STUDENT = 'student';
    const EVENT_TEACHER = 'teacher';
    const EVENT_COURSE = 'course';
    const EVENT_ENROLLMENT = 'enrollment';
    const EVENT_EXAM = 'exam';
    const EVENT_GRADE = 'grade';
    const EVENT_SYSTEM = 'system';

    // Severity levels
    const SEVERITY_LOW = 'low';
    const SEVERITY_NORMAL = 'normal';
    const SEVERITY_HIGH = 'high';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Get the user that performed the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject of the activity.
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Scope for activities by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for activities by action.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for activities by event type.
     */
    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for activities by severity.
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for activities within date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for recent activities.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for activities with specific tags.
     */
    public function scopeWithTags($query, $tags)
    {
        if (is_string($tags)) {
            $tags = [$tags];
        }

        return $query->where(function ($q) use ($tags) {
            foreach ($tags as $tag) {
                $q->orWhereJsonContains('tags', $tag);
            }
        });
    }

    /**
     * Get formatted time ago.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get activity icon based on action.
     */
    public function getIconAttribute()
    {
        return match($this->action) {
            self::TYPE_CREATE => 'fas fa-plus-circle',
            self::TYPE_UPDATE => 'fas fa-edit',
            self::TYPE_DELETE => 'fas fa-trash',
            self::TYPE_VIEW => 'fas fa-eye',
            self::TYPE_LOGIN => 'fas fa-sign-in-alt',
            self::TYPE_LOGOUT => 'fas fa-sign-out-alt',
            self::TYPE_EXPORT => 'fas fa-download',
            self::TYPE_IMPORT => 'fas fa-upload',
            self::TYPE_APPROVE => 'fas fa-check',
            self::TYPE_REJECT => 'fas fa-times',
            default => 'fas fa-info-circle',
        };
    }

    /**
     * Get activity color based on action.
     */
    public function getColorAttribute()
    {
        return match($this->action) {
            self::TYPE_CREATE => 'green',
            self::TYPE_UPDATE => 'blue',
            self::TYPE_DELETE => 'red',
            self::TYPE_VIEW => 'gray',
            self::TYPE_LOGIN => 'green',
            self::TYPE_LOGOUT => 'yellow',
            self::TYPE_EXPORT => 'indigo',
            self::TYPE_IMPORT => 'purple',
            self::TYPE_APPROVE => 'green',
            self::TYPE_REJECT => 'red',
            default => 'blue',
        };
    }

    /**
     * Get severity color.
     */
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            self::SEVERITY_CRITICAL => 'red',
            self::SEVERITY_HIGH => 'orange',
            self::SEVERITY_NORMAL => 'blue',
            self::SEVERITY_LOW => 'gray',
            default => 'blue',
        };
    }

    /**
     * Log an activity.
     */
    public static function log($description, $properties = [])
    {
        return static::create([
            'user_id' => auth()->id(),
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'action' => $properties['action'] ?? 'unknown',
            'event_type' => $properties['event_type'] ?? self::EVENT_SYSTEM,
            'severity' => $properties['severity'] ?? self::SEVERITY_NORMAL,
            'tags' => $properties['tags'] ?? [],
        ]);
    }

    /**
     * Log a user activity.
     */
    public static function logUser($action, $description, $subject = null, $properties = [])
    {
        return static::create([
            'user_id' => auth()->id(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event_type' => self::EVENT_USER,
            'severity' => $properties['severity'] ?? self::SEVERITY_NORMAL,
            'tags' => $properties['tags'] ?? [],
        ]);
    }

    /**
     * Log a student activity.
     */
    public static function logStudent($action, $description, $student = null, $properties = [])
    {
        return static::create([
            'user_id' => auth()->id(),
            'subject_type' => $student ? get_class($student) : null,
            'subject_id' => $student ? $student->id : null,
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event_type' => self::EVENT_STUDENT,
            'severity' => $properties['severity'] ?? self::SEVERITY_NORMAL,
            'tags' => $properties['tags'] ?? ['student'],
        ]);
    }

    /**
     * Log a system activity.
     */
    public static function logSystem($action, $description, $properties = [])
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event_type' => self::EVENT_SYSTEM,
            'severity' => $properties['severity'] ?? self::SEVERITY_NORMAL,
            'tags' => $properties['tags'] ?? ['system'],
        ]);
    }

    /**
     * Get activity summary for dashboard.
     */
    public static function getSummary($days = 7)
    {
        $activities = static::recent($days)->get();

        return [
            'total' => $activities->count(),
            'by_action' => $activities->groupBy('action')->map->count(),
            'by_event_type' => $activities->groupBy('event_type')->map->count(),
            'by_severity' => $activities->groupBy('severity')->map->count(),
            'by_user' => $activities->groupBy('user_id')->map->count(),
            'recent' => $activities->take(10),
        ];
    }

    /**
     * Get user activity statistics.
     */
    public static function getUserStats($userId, $days = 30)
    {
        $activities = static::byUser($userId)->recent($days)->get();

        return [
            'total_activities' => $activities->count(),
            'actions_performed' => $activities->groupBy('action')->map->count(),
            'most_active_day' => $activities->groupBy(function ($activity) {
                return $activity->created_at->format('Y-m-d');
            })->sortByDesc->count()->keys()->first(),
            'activity_by_hour' => $activities->groupBy(function ($activity) {
                return $activity->created_at->format('H');
            })->map->count(),
            'recent_activities' => $activities->take(20),
        ];
    }
}
