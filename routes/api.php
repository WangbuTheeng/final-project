<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Enrollment API routes
Route::middleware(['auth'])->group(function () {
    Route::get('/courses', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getCourses']);
    Route::get('/students', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getStudents']);
    Route::get('/classes/available', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getAvailableClasses']);
    Route::post('/enrollment/check-eligibility', [App\Http\Controllers\Api\EnrollmentApiController::class, 'checkEligibility']);
    Route::get('/enrollment/stats', [App\Http\Controllers\Api\EnrollmentApiController::class, 'getEnrollmentStats']);
});
