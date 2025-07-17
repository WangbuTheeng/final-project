<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Services\CacheService;
use App\Services\DatabaseOptimizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $cacheService;
    protected $dbOptimizationService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('admin');
        $this->cacheService = app(CacheService::class);
        $this->dbOptimizationService = app(DatabaseOptimizationService::class);
    }

    /**
     * Test cache service functionality.
     */
    public function test_cache_service_functionality()
    {
        $key = 'test_key';
        $value = ['test' => 'data'];
        $ttl = 300;

        // Test cache remember functionality
        $result = $this->cacheService->remember($key, $ttl, function () use ($value) {
            return $value;
        });

        $this->assertEquals($value, $result);
        $this->assertTrue(Cache::has($key));
    }

    /**
     * Test dashboard statistics caching.
     */
    public function test_dashboard_statistics_caching()
    {
        // Create test data
        Student::factory()->count(10)->create(['status' => 'active']);
        Student::factory()->count(5)->create(['status' => 'graduated']);

        $userId = $this->adminUser->id;

        // First call should cache the data
        $stats1 = $this->cacheService->getDashboardStats($userId);
        
        // Second call should return cached data
        $stats2 = $this->cacheService->getDashboardStats($userId);

        $this->assertEquals($stats1, $stats2);
        $this->assertIsArray($stats1);
        $this->assertArrayHasKey('total_students', $stats1);
    }

    /**
     * Test database optimization queries.
     */
    public function test_database_optimization_queries()
    {
        // Create test students
        Student::factory()->count(20)->create();

        $filters = ['status' => 'active'];
        $perPage = 10;

        $startTime = microtime(true);
        $students = $this->dbOptimizationService->getOptimizedStudents($perPage, $filters);
        $endTime = microtime(true);

        $queryTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $this->assertLessThan(100, $queryTime); // Should complete in under 100ms
        $this->assertNotNull($students);
        $this->assertLessThanOrEqual($perPage, $students->count());
    }

    /**
     * Test performance dashboard access.
     */
    public function test_performance_dashboard_access()
    {
        $response = $this->actingAs($this->adminUser)->get('/performance');

        $response->assertStatus(200);
        $response->assertViewIs('performance.index');
        $response->assertViewHas('metrics');
    }

    /**
     * Test performance metrics endpoint.
     */
    public function test_performance_metrics_endpoint()
    {
        $response = $this->actingAs($this->adminUser)
                        ->getJson('/performance/realtime-metrics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'timestamp',
                    'memory',
                    'cache',
                    'database_connections',
                ]);
    }

    /**
     * Test cache clearing functionality.
     */
    public function test_cache_clearing()
    {
        // Set some cache data
        Cache::put('test_key', 'test_value', 300);
        $this->assertTrue(Cache::has('test_key'));

        $response = $this->actingAs($this->adminUser)
                        ->postJson('/performance/clear-cache');

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        // Cache should be cleared
        $this->assertFalse(Cache::has('test_key'));
    }

    /**
     * Test database optimization.
     */
    public function test_database_optimization()
    {
        $response = $this->actingAs($this->adminUser)
                        ->postJson('/performance/optimize', [
                            'optimizations' => ['database']
                        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'results'
                ]);
    }

    /**
     * Test memory usage monitoring.
     */
    public function test_memory_usage_monitoring()
    {
        $initialMemory = memory_get_usage();

        // Create some data to use memory
        $data = [];
        for ($i = 0; $i < 1000; $i++) {
            $data[] = str_repeat('x', 1000);
        }

        $finalMemory = memory_get_usage();
        $memoryIncrease = $finalMemory - $initialMemory;

        $this->assertGreaterThan(0, $memoryIncrease);
        $this->assertLessThan(50 * 1024 * 1024, $finalMemory); // Less than 50MB
    }

    /**
     * Test query performance with large datasets.
     */
    public function test_query_performance_with_large_dataset()
    {
        // Create a larger dataset
        Student::factory()->count(100)->create();

        $startTime = microtime(true);
        
        // Test complex query
        $result = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->select('students.*', 'users.first_name', 'users.last_name')
            ->where('students.status', 'active')
            ->orderBy('students.created_at', 'desc')
            ->limit(20)
            ->get();

        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(50, $queryTime); // Should complete in under 50ms
        $this->assertNotNull($result);
        $this->assertLessThanOrEqual(20, $result->count());
    }

    /**
     * Test API response times.
     */
    public function test_api_response_times()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-device')->plainTextToken;

        $startTime = microtime(true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/auth/me');

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(200, $responseTime); // Should respond in under 200ms
    }

    /**
     * Test concurrent request handling.
     */
    public function test_concurrent_request_handling()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-device')->plainTextToken;

        $responses = [];
        $startTime = microtime(true);

        // Simulate multiple concurrent requests
        for ($i = 0; $i < 10; $i++) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->getJson('/api/v1/auth/me');
            
            $responses[] = $response;
        }

        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;

        // All requests should succeed
        foreach ($responses as $response) {
            $response->assertStatus(200);
        }

        // Total time for 10 requests should be reasonable
        $this->assertLessThan(2000, $totalTime); // Under 2 seconds for 10 requests
    }

    /**
     * Test cache hit ratio.
     */
    public function test_cache_hit_ratio()
    {
        $key = 'performance_test_key';
        $value = 'test_value';

        // First access - cache miss
        $result1 = $this->cacheService->remember($key, 300, function () use ($value) {
            return $value;
        });

        // Second access - cache hit
        $result2 = $this->cacheService->remember($key, 300, function () {
            return 'should_not_be_called';
        });

        $this->assertEquals($value, $result1);
        $this->assertEquals($value, $result2);
    }

    /**
     * Test database connection pooling.
     */
    public function test_database_connection_efficiency()
    {
        $startTime = microtime(true);

        // Perform multiple database operations
        for ($i = 0; $i < 10; $i++) {
            DB::table('users')->count();
        }

        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;

        // Multiple queries should be efficient due to connection pooling
        $this->assertLessThan(100, $totalTime); // Under 100ms for 10 simple queries
    }

    /**
     * Test lazy loading performance.
     */
    public function test_lazy_loading_performance()
    {
        // Create students with relationships
        $students = Student::factory()->count(10)->create();

        $startTime = microtime(true);

        // Test eager loading
        $studentsWithUser = Student::with('user')->get();

        $endTime = microtime(true);
        $eagerLoadTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(50, $eagerLoadTime); // Should be fast with eager loading
        $this->assertEquals(10, $studentsWithUser->count());
    }

    /**
     * Test frontend performance metrics storage.
     */
    public function test_frontend_performance_metrics_storage()
    {
        $metricsData = [
            'metrics' => [
                'loadTime' => 1500,
                'firstContentfulPaint' => 800,
                'largestContentfulPaint' => 1200,
                'cumulativeLayoutShift' => 0.05,
                'firstInputDelay' => 50,
            ],
            'userAgent' => 'Mozilla/5.0 Test Browser',
            'url' => '/dashboard',
            'timestamp' => time() * 1000,
        ];

        $response = $this->postJson('/performance/metrics', $metricsData);

        $response->assertStatus(200)
                ->assertJson(['status' => 'success']);
    }

    /**
     * Test performance report generation.
     */
    public function test_performance_report_generation()
    {
        $response = $this->actingAs($this->adminUser)
                        ->getJson('/performance/report?period=24h');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'period',
                    'generated_at',
                    'summary',
                    'recommendations',
                    'metrics',
                ]);
    }

    /**
     * Test system resource monitoring.
     */
    public function test_system_resource_monitoring()
    {
        $response = $this->actingAs($this->adminUser)
                        ->getJson('/performance/realtime-metrics');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        $this->assertArrayHasKey('memory', $data);
        $this->assertArrayHasKey('cache', $data);
        $this->assertArrayHasKey('database_connections', $data);
    }
}
