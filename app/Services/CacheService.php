<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CacheService
{
    // Cache durations in seconds
    const CACHE_SHORT = 300;      // 5 minutes
    const CACHE_MEDIUM = 1800;    // 30 minutes
    const CACHE_LONG = 3600;      // 1 hour
    const CACHE_VERY_LONG = 86400; // 24 hours

    // Cache key prefixes
    const PREFIX_STATS = 'stats:';
    const PREFIX_USER = 'user:';
    const PREFIX_STUDENT = 'student:';
    const PREFIX_TEACHER = 'teacher:';
    const PREFIX_COURSE = 'course:';
    const PREFIX_DASHBOARD = 'dashboard:';

    /**
     * Get or set cached data with automatic key generation.
     */
    public function remember($key, $ttl, $callback)
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning('Cache operation failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to direct execution if cache fails
            return $callback();
        }
    }

    /**
     * Cache dashboard statistics.
     */
    public function getDashboardStats($userId)
    {
        $key = self::PREFIX_DASHBOARD . "stats:{$userId}";
        
        return $this->remember($key, self::CACHE_MEDIUM, function () {
            return [
                'total_students' => $this->getTotalStudents(),
                'total_teachers' => $this->getTotalTeachers(),
                'total_courses' => $this->getTotalCourses(),
                'active_enrollments' => $this->getActiveEnrollments(),
                'recent_activities' => $this->getRecentActivities(),
                'upcoming_exams' => $this->getUpcomingExams(),
                'pending_applications' => $this->getPendingApplications(),
                'system_alerts' => $this->getSystemAlerts(),
            ];
        });
    }

    /**
     * Cache user-specific data.
     */
    public function getUserData($userId)
    {
        $key = self::PREFIX_USER . $userId;
        
        return $this->remember($key, self::CACHE_LONG, function () use ($userId) {
            return DB::table('users')
                ->select('id', 'name', 'email', 'avatar', 'last_login_at', 'preferences')
                ->where('id', $userId)
                ->first();
        });
    }

    /**
     * Cache student statistics.
     */
    public function getStudentStats()
    {
        $key = self::PREFIX_STATS . 'students';
        
        return $this->remember($key, self::CACHE_MEDIUM, function () {
            return [
                'total' => DB::table('students')->count(),
                'active' => DB::table('students')->where('status', 'active')->count(),
                'graduated' => DB::table('students')->where('status', 'graduated')->count(),
                'suspended' => DB::table('students')->where('status', 'suspended')->count(),
                'by_faculty' => DB::table('students')
                    ->join('faculties', 'students.faculty_id', '=', 'faculties.id')
                    ->select('faculties.name', DB::raw('count(*) as count'))
                    ->groupBy('faculties.id', 'faculties.name')
                    ->get(),
                'recent_registrations' => DB::table('students')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
            ];
        });
    }

    /**
     * Cache teacher statistics.
     */
    public function getTeacherStats()
    {
        $key = self::PREFIX_STATS . 'teachers';
        
        return $this->remember($key, self::CACHE_MEDIUM, function () {
            return [
                'total' => 0, // Placeholder until Teacher model is implemented
                'active' => 0,
                'by_department' => [],
                'by_position' => [],
            ];
        });
    }

    /**
     * Cache course statistics.
     */
    public function getCourseStats()
    {
        $key = self::PREFIX_STATS . 'courses';
        
        return $this->remember($key, self::CACHE_MEDIUM, function () {
            return [
                'total' => 0, // Placeholder until Course model is implemented
                'active' => 0,
                'by_faculty' => [],
                'enrollment_stats' => [],
            ];
        });
    }

    /**
     * Cache navigation menu data.
     */
    public function getNavigationMenu($userId)
    {
        $key = "navigation:menu:{$userId}";
        
        return $this->remember($key, self::CACHE_VERY_LONG, function () use ($userId) {
            // This would build the navigation menu based on user permissions
            return [
                'main_menu' => $this->buildMainMenu($userId),
                'quick_actions' => $this->buildQuickActions($userId),
                'recent_items' => $this->getRecentItems($userId),
            ];
        });
    }

    /**
     * Invalidate cache by pattern.
     */
    public function forget($key)
    {
        return Cache::forget($key);
    }

    /**
     * Invalidate multiple cache keys by pattern.
     */
    public function forgetByPattern($pattern)
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $keys = Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        } else {
            // For other cache drivers, we'll need to track keys manually
            Log::info('Pattern-based cache invalidation not supported for current cache driver');
        }
    }

    /**
     * Invalidate user-specific caches.
     */
    public function invalidateUserCache($userId)
    {
        $this->forget(self::PREFIX_USER . $userId);
        $this->forget(self::PREFIX_DASHBOARD . "stats:{$userId}");
        $this->forget("navigation:menu:{$userId}");
    }

    /**
     * Invalidate statistics caches.
     */
    public function invalidateStatsCache()
    {
        $this->forget(self::PREFIX_STATS . 'students');
        $this->forget(self::PREFIX_STATS . 'teachers');
        $this->forget(self::PREFIX_STATS . 'courses');
        $this->forgetByPattern(self::PREFIX_DASHBOARD . 'stats:*');
    }

    /**
     * Warm up critical caches.
     */
    public function warmUp()
    {
        try {
            // Warm up statistics
            $this->getStudentStats();
            $this->getTeacherStats();
            $this->getCourseStats();
            
            Log::info('Cache warm-up completed successfully');
        } catch (\Exception $e) {
            Log::error('Cache warm-up failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get cache statistics.
     */
    public function getStats()
    {
        $stats = [
            'driver' => config('cache.default'),
            'hit_ratio' => 0,
            'memory_usage' => 0,
            'key_count' => 0,
        ];

        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getRedis();
                $info = $redis->info();
                
                $stats['memory_usage'] = $info['used_memory_human'] ?? 'N/A';
                $stats['key_count'] = $redis->dbsize();
                $stats['hit_ratio'] = isset($info['keyspace_hits'], $info['keyspace_misses']) 
                    ? round($info['keyspace_hits'] / ($info['keyspace_hits'] + $info['keyspace_misses']) * 100, 2)
                    : 0;
            }
        } catch (\Exception $e) {
            Log::warning('Could not retrieve cache stats', ['error' => $e->getMessage()]);
        }

        return $stats;
    }

    // Private helper methods
    private function getTotalStudents()
    {
        return DB::table('students')->count();
    }

    private function getTotalTeachers()
    {
        return 0; // Placeholder
    }

    private function getTotalCourses()
    {
        return 0; // Placeholder
    }

    private function getActiveEnrollments()
    {
        return 0; // Placeholder
    }

    private function getRecentActivities()
    {
        return DB::table('activities')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    private function getUpcomingExams()
    {
        return []; // Placeholder
    }

    private function getPendingApplications()
    {
        return 0; // Placeholder
    }

    private function getSystemAlerts()
    {
        return []; // Placeholder
    }

    private function buildMainMenu($userId)
    {
        // Build navigation menu based on user permissions
        return [];
    }

    private function buildQuickActions($userId)
    {
        // Build quick actions based on user role
        return [];
    }

    private function getRecentItems($userId)
    {
        // Get recently accessed items for user
        return [];
    }
}
