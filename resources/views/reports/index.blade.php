@extends('layouts.dashboard')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports Dashboard</h1>
            <p class="text-gray-600 mt-1">Comprehensive reporting and analytics for your college</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="text-right">
                <p class="text-sm font-medium text-gray-900">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-xs text-gray-500">{{ now()->format('g:i A') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-blue-100">
                <i class="fas fa-user-graduate text-xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Students</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-green-100">
                <i class="fas fa-chalkboard-teacher text-xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Teachers</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_teachers']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-purple-100">
                <i class="fas fa-file-alt text-xl text-purple-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Exams</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_exams']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-orange-100">
                <i class="fas fa-book text-xl text-orange-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Courses</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_courses']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-indigo-100">
                <i class="fas fa-calendar-alt text-xl text-indigo-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Academic Year</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['active_academic_year'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Report Categories -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <!-- Student Reports -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
            <div class="flex items-center text-white">
                <div class="p-3 rounded-lg bg-white bg-opacity-20">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-bold">Student Reports</h3>
                    <p class="text-blue-100">Enrollment, performance, and demographics</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="{{ route('reports.students') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-blue-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-list text-blue-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-blue-600">Student Directory</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-600"></i>
                </a>
                
                <a href="{{ route('reports.enrollments') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-blue-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-clipboard-list text-blue-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-blue-600">Enrollment Reports</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-600"></i>
                </a>
                
                <a href="#" class="flex items-center justify-between p-3 rounded-lg hover:bg-blue-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-chart-pie text-blue-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-blue-600">Demographics</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-600"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Academic Reports -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200" style="background: linear-gradient(135deg, #10b981, #047857);">
            <div class="flex items-center text-white">
                <div class="p-3 rounded-lg bg-white bg-opacity-20">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-bold">Academic Reports</h3>
                    <p class="text-green-100">Grades, exams, and performance analytics</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="{{ route('reports.academic') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-green-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-chart-line text-green-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-green-600">Performance Analytics</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-green-600"></i>
                </a>
                
                <a href="{{ route('results.index') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-green-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-file-alt text-green-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-green-600">Exam Results</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-green-600"></i>
                </a>
                
                <a href="{{ route('grades.index') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-green-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-star text-green-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-green-600">Grade Reports</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-green-600"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Teacher Reports -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
            <div class="flex items-center text-white">
                <div class="p-3 rounded-lg bg-white bg-opacity-20">
                    <i class="fas fa-chalkboard-teacher text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-bold">Teacher Reports</h3>
                    <p class="text-purple-100">Faculty management and analytics</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="{{ route('reports.teachers') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-purple-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-users text-purple-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-purple-600">Teacher Directory</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-600"></i>
                </a>
                
                <a href="#" class="flex items-center justify-between p-3 rounded-lg hover:bg-purple-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-chart-bar text-purple-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-purple-600">Workload Analysis</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-600"></i>
                </a>
                
                <a href="#" class="flex items-center justify-between p-3 rounded-lg hover:bg-purple-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-check text-purple-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-purple-600">Attendance Reports</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-600"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Financial Reports -->
    @can('view-financial-reports')
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <div class="flex items-center text-white">
                <div class="p-3 rounded-lg bg-white bg-opacity-20">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-bold">Financial Reports</h3>
                    <p class="text-orange-100">Revenue, expenses, and financial analytics</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="{{ route('finance.reports.index') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-orange-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-rupee-sign text-orange-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-orange-600">Revenue Reports</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-orange-600"></i>
                </a>
                
                <a href="{{ route('finance.reports.payment-report') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-orange-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-credit-card text-orange-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-orange-600">Payment Reports</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-orange-600"></i>
                </a>
                
                <a href="{{ route('finance.reports.outstanding-dues') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-orange-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-orange-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-orange-600">Outstanding Dues</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-orange-600"></i>
                </a>
            </div>
        </div>
    </div>
    @endcan

    <!-- System Reports -->
    @if(auth()->user()->hasRole('Super Admin'))
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
            <div class="flex items-center text-white">
                <div class="p-3 rounded-lg bg-white bg-opacity-20">
                    <i class="fas fa-cogs text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-bold">System Reports</h3>
                    <p class="text-indigo-100">Usage statistics and system analytics</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="{{ route('reports.system') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-indigo-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-server text-indigo-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-indigo-600">System Usage</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600"></i>
                </a>
                
                <a href="{{ route('activity-logs.index') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-indigo-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-history text-indigo-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-indigo-600">Activity Logs</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600"></i>
                </a>
                
                <a href="{{ route('users.index') }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-indigo-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-users text-indigo-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-indigo-600">User Analytics</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-600"></i>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Custom Reports -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
            <div class="flex items-center text-white">
                <div class="p-3 rounded-lg bg-white bg-opacity-20">
                    <i class="fas fa-tools text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-bold">Custom Reports</h3>
                    <p class="text-red-100">Build your own reports and analytics</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="#" class="flex items-center justify-between p-3 rounded-lg hover:bg-red-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-plus text-red-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-red-600">Create Report</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-red-600"></i>
                </a>
                
                <a href="#" class="flex items-center justify-between p-3 rounded-lg hover:bg-red-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-save text-red-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-red-600">Saved Reports</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-red-600"></i>
                </a>
                
                <a href="#" class="flex items-center justify-between p-3 rounded-lg hover:bg-red-50 transition-colors duration-200 group">
                    <div class="flex items-center">
                        <i class="fas fa-download text-red-600 mr-3"></i>
                        <span class="text-gray-900 group-hover:text-red-600">Export Data</span>
                    </div>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-red-600"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
