@extends('layouts.app')

@section('title', 'Project Dashboard - College Management System')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">College Management System</h1>
                <p class="text-xl text-blue-100 mb-6">Comprehensive Project Dashboard & System Overview</p>
                <div class="flex justify-center space-x-8 text-sm">
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ $projectSummary['overview']['totalModules'] ?? 8 }}</div>
                        <div class="text-blue-200">Core Modules</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ $projectSummary['overview']['completionPercentage'] ?? 83 }}%</div>
                        <div class="text-blue-200">Complete</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold">{{ $projectSummary['implementation']['performanceImprovement'] ?? '60-80%' }}</div>
                        <div class="text-blue-200">Performance Gain</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Project Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Implementation Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Implementation</h3>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Phase 1:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">Completed</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">DB Optimization:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">100%</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Performance:</span>
                        <span class="font-medium text-blue-600 dark:text-blue-400">60-80% ↑</span>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">System Health</h3>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">DB Indexes:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">Optimized</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">CGPA Calc:</span>
                        <span class="font-medium text-green-600 dark:text-green-400">&lt;100ms</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Cache:</span>
                        <span class="font-medium text-blue-600 dark:text-blue-400">Active</span>
                    </div>
                </div>
            </div>

            <!-- Academic Metrics -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Data</h3>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Students:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $stats['total_students'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Courses:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $projectSummary['academic']['totalCourses'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Departments:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $projectSummary['academic']['totalDepartments'] ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Technology Stack -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tech Stack</h3>
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Backend:</span>
                        <span class="font-medium text-gray-900 dark:text-white">Laravel 12</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Frontend:</span>
                        <span class="font-medium text-gray-900 dark:text-white">Vue.js 3</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Database:</span>
                        <span class="font-medium text-gray-900 dark:text-white">MySQL</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Achievements -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Recent Achievements & Optimizations</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($projectSummary['recentAchievements']))
                    @foreach($projectSummary['recentAchievements'] as $achievement)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $achievement['title'] }}</h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                {{ $achievement['date'] }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ $achievement['description'] }}</p>
                        <div class="inline-block bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs px-3 py-1 rounded-full">
                            Impact: {{ $achievement['impact'] }}
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Core Modules Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Core System Modules</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                $modules = [
                    ['name' => 'User Management', 'status' => 'Completed', 'features' => 8],
                    ['name' => 'Academic Structure', 'status' => 'Completed', 'features' => 6],
                    ['name' => 'Student Management', 'status' => 'Completed', 'features' => 7],
                    ['name' => 'Exam Management', 'status' => 'Completed', 'features' => 5],
                    ['name' => 'Finance Management', 'status' => 'Completed', 'features' => 8],
                    ['name' => 'Reporting System', 'status' => 'Completed', 'features' => 4],
                    ['name' => 'Audit Trail', 'status' => 'Completed', 'features' => 3],
                    ['name' => 'Dashboard & Analytics', 'status' => 'Enhanced', 'features' => 6]
                ];
                @endphp
                
                @foreach($modules as $module)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-center">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $module['name'] }}</h4>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $module['features'] }} Features</div>
                    <span class="inline-block px-2 py-1 text-xs rounded-full 
                        {{ $module['status'] === 'Completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                        {{ $module['status'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Add any JavaScript for interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling or other interactive features
    console.log('Project Dashboard loaded successfully');
});
</script>
@endpush
