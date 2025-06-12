@extends('layouts.dashboard')

@section('page-title')
    {{ __('Dashboard') }}
@endsection

@section('content')
<!-- Dashboard Header -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">DASHBOARD</h1>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Students Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                <i class="fas fa-user-graduate text-xl" style="color: #37a2bc;"></i>
            </div>
            <div class="ml-4 flex-1">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">TOTAL STUDENTS</p>
                <p class="text-2xl font-bold text-gray-900">0</p>
                <p class="text-xs text-gray-500 mt-1">
                    <span style="color: #37a2bc;">↗ 2%</span> from last month
                </p>
            </div>
        </div>
    </div>

    <!-- Active Classes Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                <i class="fas fa-chalkboard text-xl" style="color: #37a2bc;"></i>
            </div>
            <div class="ml-4 flex-1">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">ACTIVE CLASSES</p>
                <p class="text-2xl font-bold text-gray-900">0</p>
                <p class="text-xs text-gray-500 mt-1">
                    <span style="color: #37a2bc;">↗ 2%</span> from last month
                </p>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                <i class="fas fa-file-alt text-xl" style="color: #37a2bc;"></i>
            </div>
            <div class="ml-4 flex-1">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">UPCOMING EXAMS</p>
                <p class="text-2xl font-bold text-gray-900">0</p>
                <p class="text-xs text-gray-500 mt-1">
                    <span class="text-gray-500">No change from last month</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Total Users Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                <i class="fas fa-users text-xl" style="color: #37a2bc;"></i>
            </div>
            <div class="ml-4 flex-1">
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">TOTAL USERS</p>
                <p class="text-2xl font-bold text-gray-900">8</p>
                <p class="text-xs text-gray-500 mt-1">
                    <span style="color: #37a2bc;">↗ 10%</span> from last month
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Students -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Recent Students</h3>
            <a href="#" class="text-sm font-medium hover:underline" style="color: #37a2bc;">View All</a>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-medium text-gray-500 text-sm uppercase tracking-wide">STUDENT</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-500 text-sm uppercase tracking-wide">ID</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-500 text-sm uppercase tracking-wide">CLASS</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-500 text-sm uppercase tracking-wide">JOIN DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center py-8 text-gray-500">
                                No students found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Upcoming Exams</h3>
            <a href="#" class="text-sm font-medium hover:underline" style="color: #37a2bc;">View All</a>
        </div>
        <div class="p-6">
            <div class="text-center py-8 text-gray-500">
                No upcoming exams
            </div>
        </div>
    </div>
</div>
@endsection
