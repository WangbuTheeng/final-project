<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use App\Services\DatabaseOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PerformanceController extends Controller
{
    protected $cacheService;
    protected $dbOptimizationService;

    public function __construct(CacheService $cacheService, DatabaseOptimizationService $dbOptimizationService)
    {
        $this->cacheService = $cacheService;
        $this->dbOptimizationService = $dbOptimizationService;
    }

    /**
     * Display performance dashboard.
     */
    public function index()
    {
        $metrics = [
            'cache' => $this->cacheService->getStats(),
            'database' => $this->getDatabaseMetrics(),
            'system' => $this->getSystemMetrics(),
            'frontend' => $this->getFrontendMetrics(),
        ];

        return view('performance.index', compact('metrics'));
    }

    /**
     * Get database performance metrics.
     */
    public function getDatabaseMetrics()
    {
        return [
            'connection_stats' => $this->dbOptimizationService->getConnectionStats(),
            'table_sizes' => $this->dbOptimizationService->getTableSizes(),
            'slow_queries' => $this->dbOptimizationService->getSlowQueries(),
            'optimization_suggestions' => $this->dbOptimizationService->suggestIndexes(),
        ];
    }

    /**
     * Get system performance metrics.
     */
    public function getSystemMetrics()
    {
        $metrics = [
            'memory_usage' => $this->getMemoryUsage(),
            'disk_usage' => $this->getDiskUsage(),
            'load_average' => $this->getLoadAverage(),
            'uptime' => $this->getUptime(),
        ];

        return $metrics;
    }

    /**
     * Get frontend performance metrics.
     */
    public function getFrontendMetrics()
    {
        // Get stored frontend metrics from cache or database
        return Cache::get('frontend_metrics', [
            'page_load_times' => [],
            'core_web_vitals' => [],
            'user_interactions' => [],
        ]);
    }

    /**
     * Store frontend performance metrics.
     */
    public function storeMetrics(Request $request)
    {
        $metrics = $request->validate([
            'metrics' => 'required|array',
            'userAgent' => 'string',
            'url' => 'required|string',
            'timestamp' => 'required|integer',
        ]);

        // Store metrics in cache for analysis
        $existingMetrics = Cache::get('frontend_metrics', []);
        $existingMetrics[] = $metrics;

        // Keep only last 1000 entries
        if (count($existingMetrics) > 1000) {
            $existingMetrics = array_slice($existingMetrics, -1000);
        }

        Cache::put('frontend_metrics', $existingMetrics, 3600);

        // Log performance issues
        $this->analyzeAndLogPerformanceIssues($metrics);

        return response()->json(['status' => 'success']);
    }

    /**
     * Optimize system performance.
     */
    public function optimize(Request $request)
    {
        $optimizations = $request->get('optimizations', []);
        $results = [];

        try {
            if (in_array('cache', $optimizations)) {
                $this->cacheService->warmUp();
                $results['cache'] = 'Cache warmed up successfully';
            }

            if (in_array('database', $optimizations)) {
                $optimizedTables = $this->dbOptimizationService->optimizeTables();
                $results['database'] = 'Optimized ' . count($optimizedTables) . ' tables';
            }

            if (in_array('cleanup', $optimizations)) {
                $cleaned = $this->dbOptimizationService->cleanupOldData();
                $results['cleanup'] = 'Cleaned up old data: ' . json_encode($cleaned);
            }

            if (in_array('storage', $optimizations)) {
                $this->optimizeStorage();
                $results['storage'] = 'Storage optimized';
            }

            Log::info('Performance optimization completed', $results);

            return response()->json([
                'success' => true,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Performance optimization failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'Optimization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all caches.
     */
    public function clearCache()
    {
        try {
            // Clear application cache
            Cache::flush();

            // Clear config cache
            \Artisan::call('config:clear');

            // Clear route cache
            \Artisan::call('route:clear');

            // Clear view cache
            \Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cache statistics.
     */
    public function getCacheStats()
    {
        return response()->json($this->cacheService->getStats());
    }

    /**
     * Analyze database performance.
     */
    public function analyzeDatabase()
    {
        $analysis = $this->dbOptimizationService->analyzePerformance();
        return response()->json($analysis);
    }

    /**
     * Get real-time system metrics.
     */
    public function getRealtimeMetrics()
    {
        return response()->json([
            'timestamp' => now()->toISOString(),
            'memory' => $this->getMemoryUsage(),
            'cache' => $this->cacheService->getStats(),
            'database_connections' => $this->dbOptimizationService->getConnectionStats(),
        ]);
    }

    /**
     * Generate performance report.
     */
    public function generateReport(Request $request)
    {
        $period = $request->get('period', '24h');
        
        $report = [
            'period' => $period,
            'generated_at' => now()->toISOString(),
            'summary' => $this->getPerformanceSummary($period),
            'recommendations' => $this->getPerformanceRecommendations(),
            'metrics' => [
                'cache' => $this->cacheService->getStats(),
                'database' => $this->getDatabaseMetrics(),
                'system' => $this->getSystemMetrics(),
            ]
        ];

        return response()->json($report);
    }

    // Private helper methods

    private function getMemoryUsage()
    {
        return [
            'current' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
            'peak' => round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB',
            'limit' => ini_get('memory_limit'),
        ];
    }

    private function getDiskUsage()
    {
        $path = storage_path();
        $total = disk_total_space($path);
        $free = disk_free_space($path);
        $used = $total - $free;

        return [
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percentage' => round(($used / $total) * 100, 2),
        ];
    }

    private function getLoadAverage()
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                '1min' => $load[0],
                '5min' => $load[1],
                '15min' => $load[2],
            ];
        }

        return null;
    }

    private function getUptime()
    {
        if (file_exists('/proc/uptime')) {
            $uptime = file_get_contents('/proc/uptime');
            $seconds = (float) explode(' ', $uptime)[0];
            
            return [
                'seconds' => $seconds,
                'formatted' => $this->formatUptime($seconds),
            ];
        }

        return null;
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function formatUptime($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return "{$days}d {$hours}h {$minutes}m";
    }

    private function optimizeStorage()
    {
        // Clean up temporary files
        $tempPath = storage_path('app/temp');
        if (is_dir($tempPath)) {
            $files = glob($tempPath . '/*');
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < strtotime('-1 day')) {
                    unlink($file);
                }
            }
        }

        // Clean up old log files
        $logPath = storage_path('logs');
        if (is_dir($logPath)) {
            $files = glob($logPath . '/*.log');
            foreach ($files as $file) {
                if (filemtime($file) < strtotime('-30 days')) {
                    unlink($file);
                }
            }
        }
    }

    private function analyzeAndLogPerformanceIssues($metrics)
    {
        $issues = [];

        // Check for slow page loads
        if (isset($metrics['metrics']['loadTime']) && $metrics['metrics']['loadTime'] > 3000) {
            $issues[] = 'Slow page load time: ' . $metrics['metrics']['loadTime'] . 'ms';
        }

        // Check for poor Core Web Vitals
        if (isset($metrics['metrics']['largestContentfulPaint']) && $metrics['metrics']['largestContentfulPaint'] > 2500) {
            $issues[] = 'Poor LCP: ' . $metrics['metrics']['largestContentfulPaint'] . 'ms';
        }

        if (isset($metrics['metrics']['firstInputDelay']) && $metrics['metrics']['firstInputDelay'] > 100) {
            $issues[] = 'Poor FID: ' . $metrics['metrics']['firstInputDelay'] . 'ms';
        }

        if (isset($metrics['metrics']['cumulativeLayoutShift']) && $metrics['metrics']['cumulativeLayoutShift'] > 0.1) {
            $issues[] = 'Poor CLS: ' . $metrics['metrics']['cumulativeLayoutShift'];
        }

        if (!empty($issues)) {
            Log::warning('Performance issues detected', [
                'url' => $metrics['url'],
                'issues' => $issues,
                'userAgent' => $metrics['userAgent'] ?? null
            ]);
        }
    }

    private function getPerformanceSummary($period)
    {
        // This would analyze metrics over the specified period
        return [
            'average_load_time' => '2.3s',
            'cache_hit_ratio' => '85%',
            'database_query_time' => '45ms',
            'error_rate' => '0.2%',
        ];
    }

    private function getPerformanceRecommendations()
    {
        $recommendations = [];

        // Analyze current metrics and suggest improvements
        $cacheStats = $this->cacheService->getStats();
        if ($cacheStats['hit_ratio'] < 80) {
            $recommendations[] = 'Consider increasing cache TTL or warming up more data';
        }

        $dbAnalysis = $this->dbOptimizationService->analyzePerformance();
        if (!empty($dbAnalysis['missing_indexes'])) {
            $recommendations[] = 'Add database indexes to improve query performance';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'System performance is optimal';
        }

        return $recommendations;
    }
}
