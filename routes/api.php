<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Enrollment API routes
Route::middleware(['auth:web'])->group(function () {
    Route::get('/courses', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getCourses']);
    Route::get('/students', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getStudents']);
    Route::get('/classes/available', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getAvailableClasses']);
    Route::post('/enrollment/check-eligibility', [App\Http\Controllers\Api\EnrollmentApiController::class, 'checkEligibility']);
    Route::get('/enrollment/stats', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getEnrollmentStats']);

    // Hierarchical selection endpoints
    Route::get('/faculties', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getFaculties']);
    Route::get('/departments-by-faculty/{facultyId}', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getDepartmentsByFaculty']);
    Route::get('/courses-by-faculty', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getCoursesByFaculty']);
    Route::get('/classes/by-course', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getClassesByCourse']);

    // Enrollment filtering endpoints
    Route::get('/enrollment/courses', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getCoursesByFaculty']);
    Route::get('/classes/by-course', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getClassesByCourse']);
});
