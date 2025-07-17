<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Middleware\ApiRateLimit;
use App\Http\Middleware\ApiVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Legacy route for backward compatibility
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Test route for API functionality
Route::get('/test', function() {
    return response()->json(['message' => 'API is working']);
});

// API Health Check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is healthy',
        'timestamp' => now()->toISOString(),
        'version' => 'v1',
        'environment' => app()->environment(),
    ]);
});

// API Documentation
Route::get('/docs', function () {
    return response()->json([
        'success' => true,
        'message' => 'College Management System API',
        'version' => 'v1',
        'documentation' => url('/docs/api'),
        'endpoints' => [
            'auth' => [
                'POST /api/v1/auth/register' => 'Register a new user',
                'POST /api/v1/auth/login' => 'Login user',
                'POST /api/v1/auth/logout' => 'Logout user',
                'GET /api/v1/auth/me' => 'Get authenticated user',
            ],
            'students' => [
                'GET /api/v1/students' => 'List students',
                'POST /api/v1/students' => 'Create student',
                'GET /api/v1/students/{id}' => 'Get student details',
                'PUT /api/v1/students/{id}' => 'Update student',
                'DELETE /api/v1/students/{id}' => 'Delete student',
            ],
        ],
        'rate_limits' => [
            'unauthenticated' => '60 requests per minute',
            'authenticated' => '1000 requests per minute',
        ],
        'authentication' => 'Bearer token (Sanctum)',
    ]);
});

// API V1 Routes
Route::prefix('v1')->middleware([ApiVersion::class . ':v1'])->group(function () {

    // Public routes (with rate limiting)
    Route::middleware([ApiRateLimit::class . ':60'])->group(function () {
        // Authentication routes
        Route::prefix('auth')->group(function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/login', [AuthController::class, 'login']);
        });

        // Public information routes
        Route::get('/faculties', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'faculties' => [
                        ['id' => 1, 'name' => 'Engineering', 'code' => 'ENG'],
                        ['id' => 2, 'name' => 'Business', 'code' => 'BUS'],
                        ['id' => 3, 'name' => 'Arts', 'code' => 'ART'],
                    ]
                ]
            ]);
        });
    });

    // Protected routes (require authentication)
    Route::middleware(['auth:sanctum', ApiRateLimit::class . ':1000'])->group(function () {

        // Authentication routes
        Route::prefix('auth')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::get('/sessions', [AuthController::class, 'sessions']);
            Route::delete('/sessions/{tokenId}', [AuthController::class, 'revokeSession']);
        });

        // Student routes
        Route::prefix('students')->group(function () {
            Route::get('/', [StudentController::class, 'index']);
            Route::post('/', [StudentController::class, 'store'])->middleware('can:create-students');
            Route::get('/statistics', [StudentController::class, 'statistics'])->middleware('can:view-statistics');
            Route::get('/search', [StudentController::class, 'search']);
            Route::post('/bulk-action', [StudentController::class, 'bulkAction'])->middleware('can:manage-students');

            Route::get('/{id}', [StudentController::class, 'show']);
            Route::put('/{id}', [StudentController::class, 'update'])->middleware('can:update-students');
            Route::delete('/{id}', [StudentController::class, 'destroy'])->middleware('can:delete-students');
        });

        // Notification routes
        Route::prefix('notifications')->group(function () {
            Route::get('/', function (Request $request) {
                $notifications = $request->user()->notifications()
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'notifications' => $notifications->map(function ($notification) {
                            return [
                                'id' => $notification->id,
                                'title' => $notification->title,
                                'message' => $notification->message,
                                'type' => $notification->type,
                                'read_at' => $notification->read_at?->toISOString(),
                                'created_at' => $notification->created_at->toISOString(),
                            ];
                        }),
                        'unread_count' => $request->user()->unreadNotifications()->count(),
                    ]
                ]);
            });

            Route::post('/{id}/read', function (Request $request, $id) {
                $notification = $request->user()->notifications()->findOrFail($id);
                $notification->markAsRead();

                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            });
        });
    });
});

// Catch-all route for unsupported API versions
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'supported_versions' => ['v1'],
        'documentation' => url('/docs/api'),
    ], 404);
});
