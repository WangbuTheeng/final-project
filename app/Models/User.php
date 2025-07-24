<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'contact_number',
        'date_of_birth',
        'gender',
        'address',
        'role',
        'status',
        'last_login_at',
        // Nepal-specific fields
        'citizenship_number',
        'alternative_id_type',
        'alternative_id_number',
        'permanent_address',
        'temporary_address',
        'district',
        'province',
        'religion',
        'caste_ethnicity',
        'blood_group',
        'country',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'first_name',
                'last_name',
                'email',
                'phone',
                'contact_number',
                'date_of_birth',
                'gender',
                'address',
                'role',
                'status',
                'last_login_at'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "User {$eventName}")
            ->useLogName('user_management');
    }

    /**
     * Get the student record associated with the user
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the teacher record associated with the user
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Get the user's full name
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Enhanced role checking with Spatie roles support
     */
    public function hasRole(string $role): bool
    {
        // Check both direct role and Spatie roles
        return $this->role === $role || $this->roles->contains('name', $role);
    }
    
    public function hasAnyRole(array $roles): bool
    {
        return collect($roles)->contains(fn($role) => $this->hasRole($role));
    }
    
    /**
     * Polymorphic access to role-specific data
     */
    public function profile()
    {
        return match($this->role) {
            'student' => $this->student,
            'teacher' => $this->teacher,
            default => null
        };
    }
    
    /**
     * Enhanced accessors using new role checking
     */
    public function getIsStudentAttribute(): bool
    {
        return $this->hasRole('student');
    }
    
    public function getIsTeacherAttribute(): bool
    {
        return $this->hasRole('teacher');
    }
    
    public function getIsAdminAttribute(): bool
    {
        return $this->hasAnyRole(['admin', 'super_admin']);
    }

    /**
     * Legacy methods for backward compatibility
     * @deprecated Use hasRole() or attribute accessors instead
     */
    public function isStudent()
    {
        return $this->is_student;
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function isTeacher()
    {
        return $this->is_teacher;
    }

    /**
     * Scope for searching users
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('email', 'LIKE', "%{$searchTerm}%")
              ->orWhere('first_name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('contact_number', 'LIKE', "%{$searchTerm}%")
              ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"])
              ->orWhereHas('roles', function ($roleQuery) use ($searchTerm) {
                  $roleQuery->where('name', 'LIKE', "%{$searchTerm}%");
              });
        });
    }

    /**
     * Scope for filtering by role
     */
    public function scopeByRole($query, $roleName)
    {
        return $query->whereHas('roles', function ($roleQuery) use ($roleName) {
            $roleQuery->where('name', $roleName);
        });
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
