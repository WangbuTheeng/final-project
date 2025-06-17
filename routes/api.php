<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Test route for API functionality
Route::get('/test', function() {
    return response()->json(['message' => 'API is working']);
});

// Note: Enrollment API routes moved to web.php to avoid CSRF issues with AJAX calls
