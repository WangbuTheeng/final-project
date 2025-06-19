@extends('layouts.dashboard')

@section('page-title')
    {{ __('Dashboard') }}
@endsection

@section('content')
<!-- Dashboard Header -->
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">DASHBOARD</h1>
            <p class="text-gray-600">Welcome back, {{ $user->first_name }}! Here's what's happening at your college today.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <div class="flex items-center space-x-4">
                <button onclick="refreshDashboard()" class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200" title="Refresh Dashboard">
                    <i class="fas fa-sync-alt text-gray-600"></i>
                </button>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">{{ now()->format('l, F j, Y') }}</p>
                    <p class="text-xs text-gray-500 current-time">{{ now()->format('g:i A') }}</p>
                </div>
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-medium" style="background-color: #37a2bc;">
                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @if($role === 'Examiner')
        <!-- Upcoming Exams Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-lg flex-shrink-0" style="background-color: rgba(55, 162, 188, 0.1);">
                    <i class="fas fa-file-alt text-xl" style="color: #37a2bc;"></i>
                </div>
                <div class="ml-4 flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">UPCOMING EXAMS</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['upcoming_exams']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-gray-500">Next 30 days</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Exams Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 responsive-card">
            <div class="flex items-center">
                <div class="p-2 sm:p-3 rounded-lg flex-shrink-0" style="background-color: rgba(55, 162, 188, 0.1);">
                    <i class="fas fa-clipboard-list text-lg sm:text-xl" style="color: #37a2bc;"></i>
                </div>
                <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wide truncate">TOTAL EXAMS</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ number_format($stats['total_exams'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-gray-500">All time</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Pending Results Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                    <i class="fas fa-hourglass-half text-xl" style="color: #37a2bc;"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">PENDING RESULTS</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_results'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-gray-500">Awaiting grades</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Students in Exams Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                    <i class="fas fa-user-graduate text-xl" style="color: #37a2bc;"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">STUDENTS</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-gray-500">In system</span>
                    </p>
                </div>
            </div>
        </div>
    @elseif($role === 'Accountant')
        <!-- Total Revenue Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg" style="background-color: rgba(34, 197, 94, 0.1);">
                    <i class="fas fa-rupee-sign text-xl text-green-600"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">TOTAL REVENUE</p>
                    <p class="text-2xl font-bold text-gray-900">NRs {{ number_format($financeStats['total_revenue'] ?? 0, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-gray-500">All time</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Outstanding Amount Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg" style="background-color: rgba(249, 115, 22, 0.1);">
                    <i class="fas fa-exclamation-triangle text-xl text-orange-600"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">OUTSTANDING</p>
                    <p class="text-2xl font-bold text-gray-900">NRs {{ number_format($financeStats['outstanding_amount'] ?? 0, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-gray-500">{{ $financeStats['pending_invoices'] ?? 0 }} pending</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- This Month Revenue Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg" style="background-color: rgba(59, 130, 246, 0.1);">
                    <i class="fas fa-chart-line text-xl text-blue-600"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">THIS MONTH</p>
                    <p class="text-2xl font-bold text-gray-900">NRs {{ number_format($financeStats['this_month_revenue'] ?? 0, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-gray-500">Revenue</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Students Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                    <i class="fas fa-user-graduate text-xl" style="color: #37a2bc;"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">STUDENTS</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-gray-500">Total enrolled</span>
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- Default cards for Super Admin, Admin, Teacher -->
        <!-- Total Students Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                    <i class="fas fa-user-graduate text-xl" style="color: #37a2bc;"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">TOTAL STUDENTS</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        @if($stats['students_growth'] > 0)
                            <span style="color: #37a2bc;">↗ {{ $stats['students_growth'] }}%</span> from last month
                        @elseif($stats['students_growth'] < 0)
                            <span class="text-red-500">↘ {{ abs($stats['students_growth']) }}%</span> from last month
                        @else
                            <span class="text-gray-500">No change from last month</span>
                        @endif
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
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_classes']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-gray-500">Current academic year</span>
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
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['upcoming_exams']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-gray-500">Next 30 days</span>
                    </p>
                </div>
            </div>
        </div>

        @if($role === 'Super Admin')
        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                    <i class="fas fa-users text-xl" style="color: #37a2bc;"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">TOTAL USERS</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        @if($stats['users_growth'] > 0)
                            <span style="color: #37a2bc;">↗ {{ $stats['users_growth'] }}%</span> from last month
                        @elseif($stats['users_growth'] < 0)
                            <span class="text-red-500">↘ {{ abs($stats['users_growth']) }}%</span> from last month
                        @else
                            <span class="text-gray-500">No change from last month</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @else
        <!-- Total Courses Card for Admin/Teacher -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                    <i class="fas fa-book text-xl" style="color: #37a2bc;"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">TOTAL COURSES</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_courses'] ?? 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <span class="text-gray-500">Available courses</span>
                    </p>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>

<!-- Finance Overview Section -->
@if(($role === 'Super Admin' || $role === 'Accountant') && auth()->user()->can('view-finances'))
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Finance Overview</h2>
            @can('view-finances')
                <a href="{{ route('finance.dashboard') }}" class="text-sm font-medium hover:underline" style="color: #37a2bc;">
                    View Full Finance Dashboard
                </a>
            @endcan
        </div>

        <!-- Finance Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Revenue Card -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium uppercase tracking-wide">Total Revenue</p>
                        <p class="text-2xl font-bold">NRs {{ number_format($financeStats['total_revenue'], 2) }}</p>
                        <p class="text-xs text-green-100 mt-1">All time collections</p>
                    </div>
                    <div class="p-3 rounded-lg bg-white bg-opacity-20">
                        <i class="fas fa-rupee-sign text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- This Month Revenue Card -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">This Month</p>
                        <p class="text-2xl font-bold">NRs {{ number_format($financeStats['this_month_revenue'], 2) }}</p>
                        <p class="text-xs text-blue-100 mt-1">
                            @if($financeStats['revenue_growth'] > 0)
                                ↗ {{ $financeStats['revenue_growth'] }}% from last month
                            @elseif($financeStats['revenue_growth'] < 0)
                                ↘ {{ abs($financeStats['revenue_growth']) }}% from last month
                            @else
                                No change from last month
                            @endif
                        </p>
                    </div>
                    <div class="p-3 rounded-lg bg-white bg-opacity-20">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Outstanding Amount Card -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">Outstanding</p>
                        <p class="text-2xl font-bold">NRs {{ number_format($financeStats['outstanding_amount'], 2) }}</p>
                        <p class="text-xs text-orange-100 mt-1">{{ $financeStats['pending_invoices'] }} pending invoices</p>
                    </div>
                    <div class="p-3 rounded-lg bg-white bg-opacity-20">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Overdue Invoices Card -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium uppercase tracking-wide">Overdue</p>
                        <p class="text-2xl font-bold">{{ $financeStats['overdue_invoices'] }}</p>
                        <p class="text-xs text-red-100 mt-1">Invoices past due date</p>
                    </div>
                    <div class="p-3 rounded-lg bg-white bg-opacity-20">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finance Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @can('create-invoices')
                <a href="{{ route('finance.invoices.create') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg group-hover:bg-blue-50 transition-colors duration-200" style="background-color: rgba(55, 162, 188, 0.1);">
                            <i class="fas fa-file-invoice text-lg" style="color: #37a2bc;"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Create Invoice</h3>
                            <p class="text-xs text-gray-500">Generate new invoice</p>
                        </div>
                    </div>
                </a>
            @endcan

            @can('create-payments')
                <a href="{{ route('finance.payments.create') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg group-hover:bg-green-50 transition-colors duration-200" style="background-color: rgba(34, 197, 94, 0.1);">
                            <i class="fas fa-credit-card text-lg text-green-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Record Payment</h3>
                            <p class="text-xs text-gray-500">Add new payment</p>
                        </div>
                    </div>
                </a>
            @endcan

            @can('manage-fees')
                <a href="{{ route('finance.fees.create') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg group-hover:bg-purple-50 transition-colors duration-200" style="background-color: rgba(147, 51, 234, 0.1);">
                            <i class="fas fa-tags text-lg text-purple-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Create Fee</h3>
                            <p class="text-xs text-gray-500">Define new fee type</p>
                        </div>
                    </div>
                </a>
            @endcan

            @can('view-financial-reports')
                <a href="{{ route('finance.reports.index') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg group-hover:bg-indigo-50 transition-colors duration-200" style="background-color: rgba(99, 102, 241, 0.1);">
                            <i class="fas fa-chart-bar text-lg text-indigo-600"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Reports</h3>
                            <p class="text-xs text-gray-500">Financial reports</p>
                        </div>
                    </div>
                </a>
            @endcan
        </div>

        <!-- Financial Trends Widget -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Financial Trends</h3>
                <span class="text-xs text-gray-500">Last 7 days</span>
            </div>
            @php
                $last7Days = collect();
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $dayPayments = \App\Models\Payment::where('status', 'completed')
                        ->whereDate('payment_date', $date)
                        ->sum('amount');
                    $last7Days->push([
                        'date' => $date->format('M j'),
                        'amount' => $dayPayments,
                        'day' => $date->format('D')
                    ]);
                }
                $maxAmount = $last7Days->max('amount') ?: 1;
            @endphp
            <div class="space-y-3">
                @foreach($last7Days as $day)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="text-xs font-medium text-gray-500 w-8">{{ $day['day'] }}</span>
                            <span class="text-sm text-gray-700">{{ $day['date'] }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full" style="background-color: #37a2bc; width: {{ $maxAmount > 0 ? ($day['amount'] / $maxAmount) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900 w-20 text-right">NRs {{ number_format($day['amount'], 0) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total (7 days)</span>
                    <span class="font-medium text-gray-900">NRs {{ number_format($last7Days->sum('amount'), 2) }}</span>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 responsive-grid">
    @if($role === 'Examiner')
        <!-- Examiner-specific content -->
        <!-- Recent Exams -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Recent Exams</h3>
                @can('manage-exams')
                    <a href="{{ route('exams.index') }}" class="text-sm font-medium hover:underline" style="color: #37a2bc;">View All</a>
                @endcan
            </div>
            <div class="p-6">
                @forelse($upcomingExams as $exam)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $exam['title'] }}</h4>
                            <p class="text-xs text-gray-500">{{ $exam['course'] }} - {{ $exam['class'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $exam['date'] }}</p>
                            <p class="text-xs text-gray-500">{{ $exam['time'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-calendar-alt text-3xl text-gray-300 mb-3"></i>
                        <p>No exams found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Exam Results -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Exam Results</h3>
                @can('view-reports')
                    <a href="{{ route('results.index') }}" class="text-sm font-medium hover:underline" style="color: #37a2bc;">View All</a>
                @endcan
            </div>
            <div class="p-6">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-bar text-3xl text-gray-300 mb-3"></i>
                    <p>Exam results and analytics</p>
                    <p class="text-xs mt-1">View detailed exam performance</p>
                </div>
            </div>
        </div>
    @else
    <!-- Finance Content Row -->
    @if(($role === 'Super Admin' || $role === 'Accountant') && auth()->user()->can('view-finances'))
        <!-- Recent Payments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                @can('view-finances')
                    <a href="{{ route('finance.payments.index') }}" class="text-sm font-medium hover:underline" style="color: #37a2bc;">View All</a>
                @endcan
            </div>
            <div class="p-6">
                @forelse($recentPayments as $payment)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium bg-green-500">
                                    NRs
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $payment['student_name'] }}</h4>
                                    <p class="text-xs text-gray-500">{{ $payment['invoice_number'] }} • {{ $payment['payment_method'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-green-600">NRs {{ number_format($payment['amount'], 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $payment['payment_date'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-credit-card text-3xl text-gray-300 mb-3"></i>
                        <p>No recent payments</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Overdue Invoices -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Overdue Invoices</h3>
                @can('view-finances')
                    <a href="{{ route('finance.invoices.index') }}?status=overdue" class="text-sm font-medium hover:underline" style="color: #37a2bc;">View All</a>
                @endcan
            </div>
            <div class="p-6">
                @forelse($overdueInvoices as $invoice)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium bg-red-500">
                                    !
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $invoice['student_name'] }}</h4>
                                    <p class="text-xs text-gray-500">{{ $invoice['invoice_number'] }} • {{ $invoice['days_overdue'] }} days overdue</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-red-600">NRs {{ number_format($invoice['amount'], 2) }}</p>
                            <p class="text-xs text-gray-500">Due: {{ $invoice['due_date'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-check-circle text-3xl text-green-300 mb-3"></i>
                        <p>No overdue invoices</p>
                        <p class="text-xs mt-1">All payments are up to date!</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
    <!-- Recent Students -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Recent Students</h3>
            @can('view-students')
                <a href="{{ route('students.index') }}" class="text-sm font-medium hover:underline" style="color: #37a2bc;">View All</a>
            @endcan
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
                        @forelse($recentStudents as $student)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium" style="background-color: #37a2bc;">
                                            {{ substr($student['name'], 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            @can('view-students')
                                                <a href="{{ route('students.show', $student['id']) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors duration-150">
                                                    {{ $student['name'] }}
                                                </a>
                                            @else
                                                <p class="text-sm font-medium text-gray-900">{{ $student['name'] }}</p>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-900">{{ $student['admission_number'] }}</td>
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $student['class'] }}</td>
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $student['join_date'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-gray-500">
                                    <i class="fas fa-user-graduate text-3xl text-gray-300 mb-3"></i>
                                    <p>No students found</p>
                                    @can('create-students')
                                        <a href="{{ route('students.create') }}" class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                            Add your first student
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Upcoming Exams</h3>
            @can('manage-exams')
                <a href="{{ route('exams.index') }}" class="text-sm font-medium hover:underline" style="color: #37a2bc;">View All</a>
            @endcan
        </div>
        <div class="p-6">
            @forelse($upcomingExams as $exam)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900">{{ $exam['title'] }}</h4>
                        <p class="text-xs text-gray-500">{{ $exam['course'] }} - {{ $exam['class'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ $exam['date'] }}</p>
                        <p class="text-xs text-gray-500">{{ $exam['time'] }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar-alt text-3xl text-gray-300 mb-3"></i>
                    <p>No upcoming exams</p>
                </div>
            @endforelse
        </div>
    </div>
    @endif
</div>

<!-- Additional Dashboard Sections for Different Roles -->
@if($role === 'Super Admin' || $role === 'Admin')
    <!-- Quick Actions Section -->
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @can('create-students')
                <a href="{{ route('students.create') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                            <i class="fas fa-user-plus text-xl" style="color: #37a2bc;"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Add Student</h3>
                            <p class="text-xs text-gray-500">Register new student</p>
                        </div>
                    </div>
                </a>
            @endcan

            @can('create-classes')
                <a href="{{ route('classes.create') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                            <i class="fas fa-chalkboard-teacher text-xl" style="color: #37a2bc;"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Create Class</h3>
                            <p class="text-xs text-gray-500">Add new class section</p>
                        </div>
                    </div>
                </a>
            @endcan

            @can('manage-exams')
                <a href="{{ route('exams.create') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                            <i class="fas fa-file-alt text-xl" style="color: #37a2bc;"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Create Exam</h3>
                            <p class="text-xs text-gray-500">Schedule new exam</p>
                        </div>
                    </div>
                </a>
            @endcan

            @if(auth()->user()->can('view-finances') && !auth()->user()->hasRole('Admin'))
                <a href="{{ route('finance.dashboard') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                            <i class="fas fa-chart-line text-xl" style="color: #37a2bc;"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-900">Finance</h3>
                            <p class="text-xs text-gray-500">View financial reports</p>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    </div>
@endif

<!-- System Status Section -->
@if($role === 'Super Admin' || $role === 'Admin')
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">System Status</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Academic Year Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Academic Year</h3>
                        @php
                            $currentAcademicYear = \App\Models\AcademicYear::current();
                        @endphp
                        @if($currentAcademicYear)
                            <p class="text-lg font-semibold text-green-600">{{ $currentAcademicYear->name }}</p>
                            <p class="text-xs text-gray-500">Active</p>
                        @else
                            <p class="text-lg font-semibold text-red-600">No Active Year</p>
                            <p class="text-xs text-gray-500">Please set an active academic year</p>
                        @endif
                    </div>
                    <div class="p-2 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                        <i class="fas fa-calendar-alt text-lg" style="color: #37a2bc;"></i>
                    </div>
                </div>
            </div>

            <!-- Database Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Database</h3>
                        <p class="text-lg font-semibold text-green-600">Connected</p>
                        <p class="text-xs text-gray-500">All systems operational</p>
                    </div>
                    <div class="p-2 rounded-lg" style="background-color: rgba(34, 197, 94, 0.1);">
                        <i class="fas fa-database text-lg text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Last Login</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'First time' }}</p>
                        <p class="text-xs text-gray-500">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y g:i A') : 'Welcome!' }}</p>
                    </div>
                    <div class="p-2 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                        <i class="fas fa-clock text-lg" style="color: #37a2bc;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Role-specific Dashboard Content -->
@if($role === 'Teacher')
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Teacher Dashboard</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- My Classes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">My Classes</h3>
                @php
                    $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
                    $myClasses = $teacher ? $teacher->classes()->with('course')->get() : collect();
                @endphp
                @forelse($myClasses as $class)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $class->course->title }}</p>
                            <p class="text-xs text-gray-500">{{ $class->name }}</p>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                            {{ $class->enrollments()->where('status', 'enrolled')->count() }} students
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No classes assigned</p>
                @endforelse
            </div>

            <!-- Quick Actions for Teachers -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @can('manage-exams')
                        <a href="{{ route('exams.create') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="p-2 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                                <i class="fas fa-plus text-sm" style="color: #37a2bc;"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Create Exam</p>
                                <p class="text-xs text-gray-500">Schedule a new exam</p>
                            </div>
                        </a>
                    @endcan

                    @can('enter-marks')
                        <a href="{{ route('marks.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="p-2 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                                <i class="fas fa-edit text-sm" style="color: #37a2bc;"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Enter Marks</p>
                                <p class="text-xs text-gray-500">Grade student exams</p>
                            </div>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endif

@if($role === 'Accountant')
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Accountant Dashboard</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Financial Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Today's Summary</h3>
                @php
                    $todayPayments = \App\Models\Payment::where('status', 'completed')
                        ->whereDate('payment_date', today())
                        ->sum('amount');
                    $todayInvoices = \App\Models\Invoice::whereDate('created_at', today())->count();
                @endphp
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Today's Collections</span>
                        <span class="text-lg font-semibold text-green-600">NRs {{ number_format($todayPayments, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">New Invoices</span>
                        <span class="text-lg font-semibold text-blue-600">{{ $todayInvoices }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Outstanding</span>
                        <span class="text-lg font-semibold text-orange-600">NRs {{ number_format($financeStats['outstanding_amount'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Finance Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @can('create-invoices')
                        <a href="{{ route('finance.invoices.create') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="p-2 rounded-lg" style="background-color: rgba(55, 162, 188, 0.1);">
                                <i class="fas fa-file-invoice text-sm" style="color: #37a2bc;"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Create Invoice</p>
                                <p class="text-xs text-gray-500">Generate new student invoice</p>
                            </div>
                        </a>
                    @endcan

                    @can('create-payments')
                        <a href="{{ route('finance.payments.create') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="p-2 rounded-lg bg-green-100">
                                <i class="fas fa-credit-card text-sm text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">Record Payment</p>
                                <p class="text-xs text-gray-500">Add payment received</p>
                            </div>
                        </a>
                    @endcan

                    @can('view-financial-reports')
                        <a href="{{ route('finance.reports.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="p-2 rounded-lg bg-purple-100">
                                <i class="fas fa-chart-bar text-sm text-purple-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">View Reports</p>
                                <p class="text-xs text-gray-500">Financial analytics</p>
                            </div>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Finance Summary for All Users -->
@if($role !== 'Accountant' && $role === 'Super Admin' && auth()->user()->can('view-finances'))
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Finance Summary</h2>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">NRs {{ number_format($financeStats['total_revenue'], 2) }}</div>
                    <div class="text-sm text-gray-500">Total Revenue</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">NRs {{ number_format($financeStats['this_month_revenue'], 2) }}</div>
                    <div class="text-sm text-gray-500">This Month</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">NRs {{ number_format($financeStats['outstanding_amount'], 2) }}</div>
                    <div class="text-sm text-gray-500">Outstanding</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $financeStats['overdue_invoices'] }}</div>
                    <div class="text-sm text-gray-500">Overdue</div>
                </div>
            </div>
            @can('view-finances')
                <div class="mt-4 text-center">
                    <a href="{{ route('finance.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white" style="background-color: #37a2bc;">
                        <i class="fas fa-chart-line mr-2"></i>
                        View Full Finance Dashboard
                    </a>
                </div>
            @endcan
        </div>
    </div>
@endif

<!-- Dashboard JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update time every minute
    function updateTime() {
        const now = new Date();
        const timeElement = document.querySelector('.current-time');
        if (timeElement) {
            timeElement.textContent = now.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }
    }

    // Update time immediately and then every minute
    updateTime();
    setInterval(updateTime, 60000);

    // Add click animations to cards
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Only animate if it's not a link click
            if (!e.target.closest('a')) {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            }
        });
    });

    // Add hover effects to quick action cards
    const quickActionCards = document.querySelectorAll('a[href*="create"], a[href*="dashboard"]');
    quickActionCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
});

// Function to refresh dashboard data (can be called from external scripts)
function refreshDashboard() {
    window.location.reload();
}

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + R to refresh
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        refreshDashboard();
    }

    // Ctrl/Cmd + N to create new student (if permission exists)
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        const createStudentLink = document.querySelector('a[href*="students/create"]');
        if (createStudentLink) {
            e.preventDefault();
            window.location.href = createStudentLink.href;
        }
    }
});
</script>

<style>
/* Additional dashboard styles */
.bg-white {
    transition: all 0.2s ease-in-out;
}

.bg-white:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Finance card gradients */
.bg-gradient-to-r {
    background-image: linear-gradient(to right, var(--tw-gradient-stops));
}

/* Finance card animations */
.bg-gradient-to-r:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Payment status indicators */
.payment-success {
    background: linear-gradient(135deg, #10b981, #059669);
}

.payment-pending {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.payment-overdue {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

/* Finance quick action hover effects */
.group:hover .group-hover\\:bg-blue-50 {
    background-color: rgba(59, 130, 246, 0.05);
}

.group:hover .group-hover\\:bg-green-50 {
    background-color: rgba(34, 197, 94, 0.05);
}

.group:hover .group-hover\\:bg-purple-50 {
    background-color: rgba(147, 51, 234, 0.05);
}

.group:hover .group-hover\\:bg-indigo-50 {
    background-color: rgba(99, 102, 241, 0.05);
}

/* Smooth animations for statistics cards */
@keyframes countUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.text-2xl.font-bold {
    animation: countUp 0.6s ease-out;
}

/* Loading state for dynamic content */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #37a2bc;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4 {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .text-2xl {
        font-size: 1.5rem;
    }

    .p-6 {
        padding: 1rem;
    }
}
</style>
@endsection
