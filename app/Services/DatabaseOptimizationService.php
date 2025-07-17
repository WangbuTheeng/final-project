<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DatabaseOptimizationService
{
    /**
     * Optimize database queries with eager loading.
     */
    public function getOptimizedStudents($perPage = 15, $filters = [])
    {
        $query = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('faculties', 'students.faculty_id', '=', 'faculties.id')
            ->leftJoin('departments', 'students.department_id', '=', 'departments.id')
            ->select([
                'students.id',
                'students.admission_number',
                'students.status',
                'students.created_at',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'faculties.name as faculty_name',
                'departments.name as department_name'
            ]);

        // Apply filters efficiently
        if (isset($filters['status'])) {
            $query->where('students.status', $filters['status']);
        }

        if (isset($filters['faculty_id'])) {
            $query->where('students.faculty_id', $filters['faculty_id']);
        }

        if (isset($filters['department_id'])) {
            $query->where('students.department_id', $filters['department_id']);
        }

        if (isset($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('users.first_name', 'like', $search)
                  ->orWhere('users.last_name', 'like', $search)
                  ->orWhere('users.email', 'like', $search)
                  ->orWhere('students.admission_number', 'like', $search);
            });
        }

        return $query->orderBy('students.created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get dashboard statistics with optimized queries.
     */
    public function getDashboardStatistics()
    {
        return Cache::remember('dashboard_statistics', 1800, function () {
            $stats = [];

            // Use single query for multiple counts
            $studentStats = DB::table('students')
                ->select([
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active'),
                    DB::raw('SUM(CASE WHEN status = "graduated" THEN 1 ELSE 0 END) as graduated'),
                    DB::raw('SUM(CASE WHEN status = "suspended" THEN 1 ELSE 0 END) as suspended'),
                    DB::raw('SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as recent')
                ])
                ->first();

            $stats['students'] = [
                'total' => $studentStats->total,
                'active' => $studentStats->active,
                'graduated' => $studentStats->graduated,
                'suspended' => $studentStats->suspended,
                'recent' => $studentStats->recent,
            ];

            // Faculty distribution
            $stats['faculty_distribution'] = DB::table('students')
                ->join('faculties', 'students.faculty_id', '=', 'faculties.id')
                ->select('faculties.name', DB::raw('COUNT(*) as count'))
                ->groupBy('faculties.id', 'faculties.name')
                ->orderBy('count', 'desc')
                ->get();

            // Recent activities (limited and optimized)
            $stats['recent_activities'] = DB::table('activities')
                ->join('users', 'activities.user_id', '=', 'users.id')
                ->select([
                    'activities.id',
                    'activities.description',
                    'activities.action',
                    'activities.created_at',
                    'users.name as user_name'
                ])
                ->orderBy('activities.created_at', 'desc')
                ->limit(10)
                ->get();

            return $stats;
        });
    }

    /**
     * Optimize notification queries.
     */
    public function getOptimizedNotifications($userId, $limit = 10)
    {
        return DB::table('notifications')
            ->select([
                'id',
                'title',
                'message',
                'type',
                'priority',
                'read_at',
                'created_at',
                'action_url',
                'action_text'
            ])
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get optimized activity timeline.
     */
    public function getOptimizedActivityTimeline($filters = [], $limit = 50)
    {
        $query = DB::table('activities')
            ->leftJoin('users', 'activities.user_id', '=', 'users.id')
            ->select([
                'activities.id',
                'activities.description',
                'activities.action',
                'activities.event_type',
                'activities.severity',
                'activities.created_at',
                'users.name as user_name'
            ]);

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('activities.user_id', $filters['user_id']);
        }

        if (isset($filters['event_type'])) {
            $query->where('activities.event_type', $filters['event_type']);
        }

        if (isset($filters['severity'])) {
            $query->where('activities.severity', $filters['severity']);
        }

        if (isset($filters['days'])) {
            $query->where('activities.created_at', '>=', now()->subDays($filters['days']));
        }

        return $query->orderBy('activities.created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Analyze and suggest database optimizations.
     */
    public function analyzePerformance()
    {
        $analysis = [
            'slow_queries' => $this->getSlowQueries(),
            'missing_indexes' => $this->suggestIndexes(),
            'table_sizes' => $this->getTableSizes(),
            'recommendations' => []
        ];

        // Generate recommendations
        if (count($analysis['slow_queries']) > 0) {
            $analysis['recommendations'][] = 'Consider optimizing slow queries or adding indexes';
        }

        if (count($analysis['missing_indexes']) > 0) {
            $analysis['recommendations'][] = 'Add suggested indexes to improve query performance';
        }

        return $analysis;
    }

    /**
     * Get slow queries from performance schema (MySQL).
     */
    private function getSlowQueries()
    {
        try {
            if (DB::connection()->getDriverName() === 'mysql') {
                return DB::select("
                    SELECT 
                        SUBSTRING(sql_text, 1, 100) as query_snippet,
                        exec_count,
                        avg_timer_wait / 1000000000 as avg_time_seconds
                    FROM performance_schema.events_statements_summary_by_digest 
                    WHERE avg_timer_wait > 1000000000
                    ORDER BY avg_timer_wait DESC 
                    LIMIT 10
                ");
            }
        } catch (\Exception $e) {
            Log::info('Could not retrieve slow queries: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Suggest indexes based on common query patterns.
     */
    private function suggestIndexes()
    {
        $suggestions = [];

        // Check if common indexes exist
        $tables = [
            'students' => ['status', 'faculty_id', 'department_id', 'created_at'],
            'activities' => ['user_id', 'event_type', 'severity', 'created_at'],
            'notifications' => ['user_id', 'read_at', 'expires_at', 'created_at'],
        ];

        foreach ($tables as $table => $columns) {
            if (Schema::hasTable($table)) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        $indexName = "idx_{$table}_{$column}";
                        
                        // Check if index exists (simplified check)
                        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Column_name = ?", [$column]);
                        
                        if (empty($indexes)) {
                            $suggestions[] = [
                                'table' => $table,
                                'column' => $column,
                                'suggested_index' => $indexName,
                                'reason' => 'Frequently used in WHERE clauses'
                            ];
                        }
                    }
                }
            }
        }

        return $suggestions;
    }

    /**
     * Get table sizes for optimization analysis.
     */
    private function getTableSizes()
    {
        try {
            if (DB::connection()->getDriverName() === 'mysql') {
                return DB::select("
                    SELECT 
                        table_name,
                        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                        table_rows
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE()
                    ORDER BY (data_length + index_length) DESC
                ");
            }
        } catch (\Exception $e) {
            Log::info('Could not retrieve table sizes: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Optimize database tables.
     */
    public function optimizeTables()
    {
        try {
            $tables = DB::select("SHOW TABLES");
            $optimized = [];

            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                
                // Skip system tables
                if (in_array($tableName, ['migrations', 'password_resets', 'failed_jobs'])) {
                    continue;
                }

                DB::statement("OPTIMIZE TABLE {$tableName}");
                $optimized[] = $tableName;
            }

            Log::info('Database tables optimized', ['tables' => $optimized]);
            return $optimized;
        } catch (\Exception $e) {
            Log::error('Database optimization failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Clean up old data to improve performance.
     */
    public function cleanupOldData()
    {
        $cleaned = [];

        try {
            // Clean old activities (older than 90 days)
            $deletedActivities = DB::table('activities')
                ->where('created_at', '<', now()->subDays(90))
                ->delete();
            
            if ($deletedActivities > 0) {
                $cleaned['activities'] = $deletedActivities;
            }

            // Clean old notifications (older than 30 days and read)
            $deletedNotifications = DB::table('notifications')
                ->where('created_at', '<', now()->subDays(30))
                ->whereNotNull('read_at')
                ->delete();
            
            if ($deletedNotifications > 0) {
                $cleaned['notifications'] = $deletedNotifications;
            }

            // Clean expired sessions
            $deletedSessions = DB::table('sessions')
                ->where('last_activity', '<', now()->subDays(7)->timestamp)
                ->delete();
            
            if ($deletedSessions > 0) {
                $cleaned['sessions'] = $deletedSessions;
            }

            Log::info('Old data cleanup completed', $cleaned);
        } catch (\Exception $e) {
            Log::error('Data cleanup failed', ['error' => $e->getMessage()]);
        }

        return $cleaned;
    }

    /**
     * Get database connection statistics.
     */
    public function getConnectionStats()
    {
        try {
            if (DB::connection()->getDriverName() === 'mysql') {
                $stats = DB::select("SHOW STATUS LIKE 'Connections'");
                $maxConnections = DB::select("SHOW VARIABLES LIKE 'max_connections'");
                $threadsConnected = DB::select("SHOW STATUS LIKE 'Threads_connected'");

                return [
                    'total_connections' => $stats[0]->Value ?? 0,
                    'max_connections' => $maxConnections[0]->Value ?? 0,
                    'current_connections' => $threadsConnected[0]->Value ?? 0,
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Could not retrieve connection stats', ['error' => $e->getMessage()]);
        }

        return [];
    }
}
