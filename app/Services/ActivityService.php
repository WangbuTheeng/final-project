<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityService
{
    /**
     * Log a general activity.
     */
    public function log($description, $properties = [])
    {
        return Activity::log($description, $properties);
    }

    /**
     * Log user authentication activity.
     */
    public function logAuth($action, $user = null)
    {
        $user = $user ?? Auth::user();
        
        $email = $user->email ?? 'unknown';
        $descriptions = [
            'login' => "User {$user->name} logged in",
            'logout' => "User {$user->name} logged out",
            'failed_login' => "Failed login attempt for {$email}",
            'password_reset' => "Password reset requested for {$user->email}",
            'password_changed' => "Password changed for {$user->name}",
        ];

        return Activity::logUser(
            $action,
            $descriptions[$action] ?? "Authentication action: {$action}",
            $user,
            [
                'event_type' => Activity::EVENT_USER,
                'severity' => $action === 'failed_login' ? Activity::SEVERITY_HIGH : Activity::SEVERITY_NORMAL,
                'tags' => ['authentication', $action],
            ]
        );
    }

    /**
     * Log student-related activity.
     */
    public function logStudentActivity($action, $student, $description = null, $properties = [])
    {
        $defaultDescriptions = [
            'create' => "Student {$student->user->name} was created",
            'update' => "Student {$student->user->name} was updated",
            'delete' => "Student {$student->user->name} was deleted",
            'view' => "Student {$student->user->name} profile was viewed",
            'enroll' => "Student {$student->user->name} was enrolled",
            'graduate' => "Student {$student->user->name} graduated",
            'suspend' => "Student {$student->user->name} was suspended",
        ];

        return Activity::logStudent(
            $action,
            $description ?? $defaultDescriptions[$action] ?? "Student action: {$action}",
            $student,
            array_merge([
                'student_id' => $student->id,
                'admission_number' => $student->admission_number,
                'faculty' => $student->faculty->name ?? null,
                'department' => $student->department->name ?? null,
            ], $properties)
        );
    }

    /**
     * Log teacher-related activity.
     */
    public function logTeacherActivity($action, $teacher, $description = null, $properties = [])
    {
        $defaultDescriptions = [
            'create' => "Teacher {$teacher->user->name} was created",
            'update' => "Teacher {$teacher->user->name} was updated",
            'delete' => "Teacher {$teacher->user->name} was deleted",
            'view' => "Teacher {$teacher->user->name} profile was viewed",
            'assign_course' => "Course assigned to teacher {$teacher->user->name}",
            'remove_course' => "Course removed from teacher {$teacher->user->name}",
        ];

        return Activity::create([
            'user_id' => Auth::id(),
            'subject_type' => get_class($teacher),
            'subject_id' => $teacher->id,
            'action' => $action,
            'description' => $description ?? $defaultDescriptions[$action] ?? "Teacher action: {$action}",
            'properties' => array_merge([
                'teacher_id' => $teacher->id,
                'employee_id' => $teacher->employee_id,
                'department' => $teacher->department->name ?? null,
            ], $properties),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event_type' => Activity::EVENT_TEACHER,
            'severity' => $properties['severity'] ?? Activity::SEVERITY_NORMAL,
            'tags' => array_merge(['teacher'], $properties['tags'] ?? []),
        ]);
    }

    /**
     * Log course-related activity.
     */
    public function logCourseActivity($action, $course, $description = null, $properties = [])
    {
        $defaultDescriptions = [
            'create' => "Course {$course->name} was created",
            'update' => "Course {$course->name} was updated",
            'delete' => "Course {$course->name} was deleted",
            'view' => "Course {$course->name} was viewed",
            'enroll_student' => "Student enrolled in course {$course->name}",
            'remove_student' => "Student removed from course {$course->name}",
        ];

        return Activity::create([
            'user_id' => Auth::id(),
            'subject_type' => get_class($course),
            'subject_id' => $course->id,
            'action' => $action,
            'description' => $description ?? $defaultDescriptions[$action] ?? "Course action: {$action}",
            'properties' => array_merge([
                'course_id' => $course->id,
                'course_code' => $course->code,
                'course_name' => $course->name,
                'faculty' => $course->faculty->name ?? null,
            ], $properties),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event_type' => Activity::EVENT_COURSE,
            'severity' => $properties['severity'] ?? Activity::SEVERITY_NORMAL,
            'tags' => array_merge(['course'], $properties['tags'] ?? []),
        ]);
    }

    /**
     * Log enrollment activity.
     */
    public function logEnrollmentActivity($action, $enrollment, $description = null, $properties = [])
    {
        $student = $enrollment->student ?? null;
        $course = $enrollment->course ?? null;

        $defaultDescriptions = [
            'create' => "Enrollment created for student " . ($student ? $student->user->name : 'Unknown') . 
                       " in course " . ($course ? $course->name : 'Unknown'),
            'update' => "Enrollment updated for student " . ($student ? $student->user->name : 'Unknown'),
            'delete' => "Enrollment deleted for student " . ($student ? $student->user->name : 'Unknown'),
            'approve' => "Enrollment approved for student " . ($student ? $student->user->name : 'Unknown'),
            'reject' => "Enrollment rejected for student " . ($student ? $student->user->name : 'Unknown'),
        ];

        return Activity::create([
            'user_id' => Auth::id(),
            'subject_type' => get_class($enrollment),
            'subject_id' => $enrollment->id,
            'action' => $action,
            'description' => $description ?? $defaultDescriptions[$action] ?? "Enrollment action: {$action}",
            'properties' => array_merge([
                'enrollment_id' => $enrollment->id,
                'student_id' => $student ? $student->id : null,
                'student_name' => $student ? $student->user->name : null,
                'course_id' => $course ? $course->id : null,
                'course_name' => $course ? $course->name : null,
                'status' => $enrollment->status ?? null,
            ], $properties),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event_type' => Activity::EVENT_ENROLLMENT,
            'severity' => $properties['severity'] ?? Activity::SEVERITY_NORMAL,
            'tags' => array_merge(['enrollment'], $properties['tags'] ?? []),
        ]);
    }

    /**
     * Log system activity.
     */
    public function logSystemActivity($action, $description, $properties = [])
    {
        return Activity::logSystem($action, $description, $properties);
    }

    /**
     * Log data export activity.
     */
    public function logExport($type, $description = null, $properties = [])
    {
        return Activity::create([
            'user_id' => Auth::id(),
            'action' => Activity::TYPE_EXPORT,
            'description' => $description ?? "Data export: {$type}",
            'properties' => array_merge([
                'export_type' => $type,
                'format' => $properties['format'] ?? 'csv',
                'record_count' => $properties['record_count'] ?? null,
            ], $properties),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event_type' => Activity::EVENT_SYSTEM,
            'severity' => Activity::SEVERITY_NORMAL,
            'tags' => ['export', $type],
        ]);
    }

    /**
     * Log data import activity.
     */
    public function logImport($type, $description = null, $properties = [])
    {
        return Activity::create([
            'user_id' => Auth::id(),
            'action' => Activity::TYPE_IMPORT,
            'description' => $description ?? "Data import: {$type}",
            'properties' => array_merge([
                'import_type' => $type,
                'format' => $properties['format'] ?? 'csv',
                'record_count' => $properties['record_count'] ?? null,
                'success_count' => $properties['success_count'] ?? null,
                'error_count' => $properties['error_count'] ?? null,
            ], $properties),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event_type' => Activity::EVENT_SYSTEM,
            'severity' => ($properties['error_count'] ?? 0) > 0 ? Activity::SEVERITY_HIGH : Activity::SEVERITY_NORMAL,
            'tags' => ['import', $type],
        ]);
    }

    /**
     * Get activities with filters and pagination.
     */
    public function getActivities($filters = [], $perPage = 20)
    {
        $query = Activity::with('user', 'subject')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->byAction($filters['action']);
        }

        if (isset($filters['event_type'])) {
            $query->byEventType($filters['event_type']);
        }

        if (isset($filters['severity'])) {
            $query->bySeverity($filters['severity']);
        }

        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->inDateRange($filters['date_from'], $filters['date_to']);
        }

        if (isset($filters['days'])) {
            $query->recent($filters['days']);
        }

        if (isset($filters['tags'])) {
            $query->withTags($filters['tags']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get activity statistics.
     */
    public function getStatistics($days = 30)
    {
        return Activity::getSummary($days);
    }

    /**
     * Get user activity statistics.
     */
    public function getUserStatistics($userId, $days = 30)
    {
        return Activity::getUserStats($userId, $days);
    }

    /**
     * Get recent activities for dashboard.
     */
    public function getRecentActivities($limit = 10)
    {
        return Activity::with('user', 'subject')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Clean up old activities.
     */
    public function cleanup($days = 90)
    {
        return Activity::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Batch log activities.
     */
    public function batchLog($activities)
    {
        $batchUuid = Str::uuid();
        $timestamp = now();

        $records = collect($activities)->map(function ($activity) use ($batchUuid, $timestamp) {
            return array_merge([
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'batch_uuid' => $batchUuid,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'event_type' => Activity::EVENT_SYSTEM,
                'severity' => Activity::SEVERITY_NORMAL,
            ], $activity);
        })->toArray();

        return Activity::insert($records);
    }
}
