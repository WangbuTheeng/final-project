<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'action_text',
        'priority',
        'read_at',
        'expires_at',
        'category',
        'icon',
        'color'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Notification types
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';
    const TYPE_SYSTEM = 'system';

    // Notification priorities
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Notification categories
    const CATEGORY_ACADEMIC = 'academic';
    const CATEGORY_FINANCIAL = 'financial';
    const CATEGORY_SYSTEM = 'system';
    const CATEGORY_EXAM = 'exam';
    const CATEGORY_ANNOUNCEMENT = 'announcement';

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope for notifications by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for notifications by category.
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for notifications by priority.
     */
    public function scopeOfPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for non-expired notifications.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Check if notification is read.
     */
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread()
    {
        return is_null($this->read_at);
    }

    /**
     * Check if notification is expired.
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the notification icon based on type.
     */
    public function getIconAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return match($this->type) {
            self::TYPE_SUCCESS => 'fas fa-check-circle',
            self::TYPE_WARNING => 'fas fa-exclamation-triangle',
            self::TYPE_ERROR => 'fas fa-times-circle',
            self::TYPE_SYSTEM => 'fas fa-cog',
            default => 'fas fa-info-circle',
        };
    }

    /**
     * Get the notification color based on type.
     */
    public function getColorAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return match($this->type) {
            self::TYPE_SUCCESS => 'green',
            self::TYPE_WARNING => 'yellow',
            self::TYPE_ERROR => 'red',
            self::TYPE_SYSTEM => 'blue',
            default => 'blue',
        };
    }

    /**
     * Get formatted time ago.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Create a notification for a user.
     */
    public static function createForUser($userId, $data)
    {
        return static::create(array_merge([
            'user_id' => $userId,
            'type' => self::TYPE_INFO,
            'priority' => self::PRIORITY_NORMAL,
            'category' => self::CATEGORY_SYSTEM,
        ], $data));
    }

    /**
     * Create notifications for multiple users.
     */
    public static function createForUsers($userIds, $data)
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = array_merge([
                'user_id' => $userId,
                'type' => self::TYPE_INFO,
                'priority' => self::PRIORITY_NORMAL,
                'category' => self::CATEGORY_SYSTEM,
                'created_at' => now(),
                'updated_at' => now(),
            ], $data);
        }

        return static::insert($notifications);
    }

    /**
     * Create a system notification.
     */
    public static function createSystem($title, $message, $userIds = null)
    {
        $data = [
            'type' => self::TYPE_SYSTEM,
            'title' => $title,
            'message' => $message,
            'category' => self::CATEGORY_SYSTEM,
            'priority' => self::PRIORITY_NORMAL,
        ];

        if ($userIds) {
            return static::createForUsers($userIds, $data);
        }

        // Create for all users if no specific users provided
        $allUserIds = User::pluck('id')->toArray();
        return static::createForUsers($allUserIds, $data);
    }

    /**
     * Create an academic notification.
     */
    public static function createAcademic($title, $message, $userIds, $actionUrl = null)
    {
        $data = [
            'type' => self::TYPE_INFO,
            'title' => $title,
            'message' => $message,
            'category' => self::CATEGORY_ACADEMIC,
            'priority' => self::PRIORITY_NORMAL,
            'action_url' => $actionUrl,
        ];

        return static::createForUsers($userIds, $data);
    }

    /**
     * Create an exam notification.
     */
    public static function createExam($title, $message, $userIds, $actionUrl = null)
    {
        $data = [
            'type' => self::TYPE_WARNING,
            'title' => $title,
            'message' => $message,
            'category' => self::CATEGORY_EXAM,
            'priority' => self::PRIORITY_HIGH,
            'action_url' => $actionUrl,
            'action_text' => 'View Details',
        ];

        return static::createForUsers($userIds, $data);
    }

    /**
     * Create a financial notification.
     */
    public static function createFinancial($title, $message, $userIds, $actionUrl = null)
    {
        $data = [
            'type' => self::TYPE_WARNING,
            'title' => $title,
            'message' => $message,
            'category' => self::CATEGORY_FINANCIAL,
            'priority' => self::PRIORITY_HIGH,
            'action_url' => $actionUrl,
            'action_text' => 'Pay Now',
        ];

        return static::createForUsers($userIds, $data);
    }
}
