@extends('layouts.dashboard')

@section('page-title')
    {{ __('Dashboard') }}
@endsection

@section('content')
<!-- Modern Dashboard Header -->
<div class="mb-8 relative overflow-hidden">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-r from-primary-50 via-white to-primary-50 rounded-2xl"></div>

    <!-- Content -->
    <div class="relative bg-white/80 backdrop-blur-sm rounded-2xl shadow-soft border border-primary-100 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <!-- Welcome Section -->
            <div class="flex items-center space-x-4">
                <!-- Avatar -->
                <div class="relative">
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-medium">
                        <span class="text-white text-xl font-bold">
                            @if($user->first_name)
                                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->name, 1, 1)) }}
                            @endif
                        </span>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-success-500 rounded-full border-2 border-white"></div>
                </div>

                <!-- Welcome Text -->
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-1">
                        Welcome back, <span class="text-primary-600">{{ $user->first_name ?: $user->name }}</span>!
                    </h1>
                    <p class="text-gray-600 text-sm lg:text-base">
                        Here's what's happening at your college today.
                    </p>
                    <div class="flex items-center mt-2 space-x-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                            {{ $role }}
                        </span>
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            Last login: {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'First time' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Section -->
            <div class="mt-6 lg:mt-0 flex items-center space-x-4">
                <!-- Quick Stats -->
                <div class="hidden lg:flex items-center space-x-6 mr-6">
                    <div class="text-center">
                        <div class="text-lg font-bold text-gray-900">{{ now()->format('j') }}</div>
                        <div class="text-xs text-gray-500 uppercase tracking-wide">{{ now()->format('M') }}</div>
                    </div>
                    <div class="w-px h-8 bg-gray-200"></div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900">{{ now()->format('l') }}</div>
                        <div class="text-xs text-gray-500 current-time">{{ now()->format('g:i A') }}</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-2">
                    <button onclick="refreshDashboard()"
                            class="p-3 rounded-xl bg-white hover:bg-primary-50 border border-gray-200 hover:border-primary-200 transition-all duration-200 group shadow-sm hover:shadow-medium"
                            title="Refresh Dashboard">
                        <i class="fas fa-sync-alt text-gray-600 group-hover:text-primary-600 transition-colors duration-200"></i>
                    </button>

                    <button class="p-3 rounded-xl bg-primary-500 hover:bg-primary-600 text-white transition-all duration-200 shadow-medium hover:shadow-large hover:scale-105"
                            title="Quick Actions">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
    @if($role === 'Examiner')
        <!-- Upcoming Exams Card -->
        <div class="group relative bg-white rounded-2xl shadow-soft hover:shadow-large transition-all duration-300 border border-gray-100 hover:border-primary-200 p-6 overflow-hidden"
             x-show="loaded"
             x-transition:enter="transition ease-out duration-500 delay-100"
             x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 transform translate-y-0 scale-100">

            <!-- Background Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary-50/50 to-primary-100/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <!-- Floating Icon -->
            <div class="relative mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-medium group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <i class="fas fa-file-alt text-xl text-white"></i>
                </div>
                <div class="absolute -top-1 -right-1 w-6 h-6 bg-warning-400 rounded-full flex items-center justify-center">
                    <span class="text-xs font-bold text-white">{{ $stats['upcoming_exams'] }}</span>
                </div>
            </div>

            <!-- Content -->
            <div class="relative">
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Upcoming Exams</h3>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-bold text-gray-900">{{ number_format($stats['upcoming_exams']) }}</span>
                    <span class="text-sm text-gray-500">exams</span>
                </div>
                <div class="flex items-center mt-3 text-xs text-primary-600">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <span>Next 30 days</span>
                </div>
            </div>

            <!-- Hover Glow Effect -->
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-primary-500/0 to-primary-600/0 group-hover:from-primary-500/5 group-hover:to-primary-600/5 transition-all duration-300"></div>
        </div>

        <!-- Total Exams Card -->
        <div class="group relative bg-white rounded-2xl shadow-soft hover:shadow-large transition-all duration-300 border border-gray-100 hover:border-success-200 p-6 overflow-hidden"
             x-show="loaded"
             x-transition:enter="transition ease-out duration-500 delay-200"
             x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 transform translate-y-0 scale-100">

            <!-- Background Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-success-50/50 to-success-100/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <!-- Floating Icon -->
            <div class="relative mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center shadow-medium group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <i class="fas fa-clipboard-list text-xl text-white"></i>
                </div>
            </div>

            <!-- Content -->
            <div class="relative">
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Total Exams</h3>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_exams'] ?? 0) }}</span>
                    <span class="text-sm text-gray-500">exams</span>
                </div>
                <div class="flex items-center mt-3 text-xs text-success-600">
                    <i class="fas fa-calendar-check mr-1"></i>
                    <span>All time</span>
                </div>
            </div>

            <!-- Hover Glow Effect -->
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-success-500/0 to-success-600/0 group-hover:from-success-500/5 group-hover:to-success-600/5 transition-all duration-300"></div>
        </div>

        <!-- Pending Results Card -->
        <div class="group relative bg-white rounded-2xl shadow-soft hover:shadow-large transition-all duration-300 border border-gray-100 hover:border-warning-200 p-6 overflow-hidden"
             x-show="loaded"
             x-transition:enter="transition ease-out duration-500 delay-300"
             x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 transform translate-y-0 scale-100">

            <!-- Background Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-warning-50/50 to-warning-100/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <!-- Floating Icon -->
            <div class="relative mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-warning-500 to-warning-600 rounded-xl flex items-center justify-center shadow-medium group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <i class="fas fa-hourglass-half text-xl text-white"></i>
                </div>
                @if(($stats['pending_results'] ?? 0) > 0)
                <div class="absolute -top-1 -right-1 w-6 h-6 bg-danger-400 rounded-full flex items-center justify-center animate-pulse">
                    <span class="text-xs font-bold text-white">!</span>
                </div>
                @endif
            </div>

            <!-- Content -->
            <div class="relative">
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Pending Results</h3>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_results'] ?? 0) }}</span>
                    <span class="text-sm text-gray-500">results</span>
                </div>
                <div class="flex items-center mt-3 text-xs text-warning-600">
                    <i class="fas fa-clock mr-1"></i>
                    <span>Awaiting grades</span>
                </div>
            </div>

            <!-- Hover Glow Effect -->
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-warning-500/0 to-warning-600/0 group-hover:from-warning-500/5 group-hover:to-warning-600/5 transition-all duration-300"></div>
        </div>

        <!-- Students Card -->
        <div class="group relative bg-white rounded-2xl shadow-soft hover:shadow-large transition-all duration-300 border border-gray-100 hover:border-secondary-200 p-6 overflow-hidden"
             x-show="loaded"
             x-transition:enter="transition ease-out duration-500 delay-400"
             x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 transform translate-y-0 scale-100">

            <!-- Background Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-secondary-50/50 to-secondary-100/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

            <!-- Floating Icon -->
            <div class="relative mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl flex items-center justify-center shadow-medium group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                    <i class="fas fa-user-graduate text-xl text-white"></i>
                </div>
                @if($stats['students_growth'] > 0)
                <div class="absolute -top-1 -right-1 w-6 h-6 bg-success-400 rounded-full flex items-center justify-center">
                    <i class="fas fa-arrow-up text-xs text-white"></i>
                </div>
                @endif
            </div>

            <!-- Content -->
            <div class="relative">
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Total Students</h3>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</span>
                    <span class="text-sm text-gray-500">students</span>
                </div>
                <div class="flex items-center mt-3 text-xs text-secondary-600">
                    <i class="fas fa-users mr-1"></i>
                    <span>In system</span>
                    @if($stats['students_growth'] > 0)
                        <span class="ml-2 text-success-600">+{{ $stats['students_growth'] }}%</span>
                    @endif
                </div>
            </div>

            <!-- Hover Glow Effect -->
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-secondary-500/0 to-secondary-600/0 group-hover:from-secondary-500/5 group-hover:to-secondary-600/5 transition-all duration-300"></div>
        </div>
    @elseif($role === 'Accountant')
        <!-- Total Revenue Card -->
        <div class="group relative bg-gradient-to-br from-emerald-50 to-green-100 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-emerald-200/50 p-6 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-green-500/10 opacity-50"></div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl"></div>

            <div class="relative flex items-center">
                <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-rupee-sign text-xl text-white"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-emerald-700 uppercase tracking-wide mb-1">TOTAL REVENUE</p>
                    <p class="text-3xl font-bold text-gray-900 mb-1">NRs {{ number_format($financeStats['total_revenue'] ?? 0, 2) }}</p>
                    <div class="flex items-center text-xs text-emerald-600">
                        <i class="fas fa-chart-line mr-1"></i>
                        <span>All time</span>
                    </div>
                </div>
            </div>

            <!-- Hover Effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/0 to-green-500/0 group-hover:from-emerald-500/5 group-hover:to-green-500/5 transition-all duration-300 rounded-xl"></div>
        </div>

        <!-- Outstanding Amount Card -->
        <div class="group relative bg-gradient-to-br from-red-50 to-rose-100 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-red-200/50 p-6 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-rose-500/10 opacity-50"></div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-red-500/10 rounded-full blur-xl"></div>

            <div class="relative flex items-center">
                <div class="p-4 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-exclamation-triangle text-xl text-white"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-red-700 uppercase tracking-wide mb-1">OUTSTANDING</p>
                    <p class="text-3xl font-bold text-gray-900 mb-1">NRs {{ number_format($financeStats['outstanding_amount'] ?? 0, 2) }}</p>
                    <div class="flex items-center text-xs text-red-600">
                        <i class="fas fa-clock mr-1"></i>
                        <span>{{ $financeStats['pending_invoices'] ?? 0 }} pending</span>
                    </div>
                </div>
            </div>

            <!-- Hover Effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-red-500/0 to-rose-500/0 group-hover:from-red-500/5 group-hover:to-rose-500/5 transition-all duration-300 rounded-xl"></div>
        </div>

        <!-- This Month Revenue Card -->
        <div class="group relative bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-blue-200/50 p-6 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-500/10 opacity-50"></div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-blue-500/10 rounded-full blur-xl"></div>

            <div class="relative flex items-center">
                <div class="p-4 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-chart-line text-xl text-white"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide mb-1">THIS MONTH</p>
                    <p class="text-3xl font-bold text-gray-900 mb-1">NRs {{ number_format($financeStats['this_month_revenue'] ?? 0, 2) }}</p>
                    <div class="flex items-center text-xs text-blue-600">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <span>Revenue</span>
                    </div>
                </div>
            </div>

            <!-- Hover Effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/0 to-indigo-500/0 group-hover:from-blue-500/5 group-hover:to-indigo-500/5 transition-all duration-300 rounded-xl"></div>
        </div>

        <!-- Students Card -->
        <div class="group relative bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-purple-200/50 p-6 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-violet-500/10 opacity-50"></div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-purple-500/10 rounded-full blur-xl"></div>

            <div class="relative flex items-center">
                <div class="p-4 rounded-xl bg-gradient-to-br from-purple-500 to-violet-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-graduate text-xl text-white"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-purple-700 uppercase tracking-wide mb-1">STUDENTS</p>
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($stats['total_students']) }}</p>
                    <div class="flex items-center text-xs text-purple-600">
                        <i class="fas fa-users mr-1"></i>
                        <span>Total enrolled</span>
                    </div>
                </div>
            </div>

            <!-- Hover Effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-purple-500/0 to-violet-500/0 group-hover:from-purple-500/5 group-hover:to-violet-500/5 transition-all duration-300 rounded-xl"></div>
        </div>
    @else
        <!-- Default cards for Super Admin, Admin, Teacher -->
        <!-- Total Students Card -->
        <div class="group relative bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-blue-200/50 p-6 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-500/10 opacity-50"></div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-blue-500/10 rounded-full blur-xl"></div>

            <div class="relative flex items-center">
                <div class="p-4 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-graduate text-xl text-white"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide mb-1">TOTAL STUDENTS</p>
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($stats['total_students']) }}</p>
                    <div class="flex items-center text-xs">
                        @if($stats['students_growth'] > 0)
                            <i class="fas fa-arrow-up text-emerald-500 mr-1"></i>
                            <span class="text-emerald-600 font-medium">{{ $stats['students_growth'] }}%</span>
                            <span class="text-blue-600 ml-1">from last month</span>
                        @elseif($stats['students_growth'] < 0)
                            <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                            <span class="text-red-600 font-medium">{{ abs($stats['students_growth']) }}%</span>
                            <span class="text-blue-600 ml-1">from last month</span>
                        @else
                            <i class="fas fa-minus text-gray-500 mr-1"></i>
                            <span class="text-blue-600">No change from last month</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hover Effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/0 to-indigo-500/0 group-hover:from-blue-500/5 group-hover:to-indigo-500/5 transition-all duration-300 rounded-xl"></div>
        </div>

        <!-- Active Classes Card -->
        <div class="group relative bg-gradient-to-br from-emerald-50 to-green-100 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-emerald-200/50 p-6 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-green-500/10 opacity-50"></div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl"></div>

            <div class="relative flex items-center">
                <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-chalkboard text-xl text-white"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-emerald-700 uppercase tracking-wide mb-1">ACTIVE CLASSES</p>
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($stats['active_classes']) }}</p>
                    <div class="flex items-center text-xs text-emerald-600">
                        <i class="fas fa-calendar-check mr-1"></i>
                        <span>Current academic year</span>
                    </div>
                </div>
            </div>

            <!-- Hover Effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/0 to-green-500/0 group-hover:from-emerald-500/5 group-hover:to-green-500/5 transition-all duration-300 rounded-xl"></div>
        </div>

        <!-- Upcoming Exams Card -->
        <div class="group relative bg-gradient-to-br from-orange-50 to-amber-100 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-orange-200/50 p-6 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-amber-500/10 opacity-50"></div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-orange-500/10 rounded-full blur-xl"></div>

            <div class="relative flex items-center">
                <div class="p-4 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-file-alt text-xl text-white"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-orange-700 uppercase tracking-wide mb-1">UPCOMING EXAMS</p>
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($stats['upcoming_exams']) }}</p>
                    <div class="flex items-center text-xs text-orange-600">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <span>Next 30 days</span>
                    </div>
                </div>
            </div>

            <!-- Hover Effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-orange-500/0 to-amber-500/0 group-hover:from-orange-500/5 group-hover:to-amber-500/5 transition-all duration-300 rounded-xl"></div>
        </div>

        @if($role === 'Super Admin')
        <!-- Total Users Card -->
        <div class="group relative bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-purple-200/50 p-6 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-violet-500/10 opacity-50"></div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-purple-500/10 rounded-full blur-xl"></div>

            <div class="relative flex items-center">
                <div class="p-4 rounded-xl bg-gradient-to-br from-purple-500 to-violet-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-users text-xl text-white"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-purple-700 uppercase tracking-wide mb-1">TOTAL USERS</p>
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($stats['total_users']) }}</p>
                    <div class="flex items-center text-xs">
                        @if($stats['users_growth'] > 0)
                            <i class="fas fa-arrow-up text-emerald-500 mr-1"></i>
                            <span class="text-emerald-600 font-medium">{{ $stats['users_growth'] }}%</span>
                            <span class="text-purple-600 ml-1">from last month</span>
                        @elseif($stats['users_growth'] < 0)
                            <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                            <span class="text-red-600 font-medium">{{ abs($stats['users_growth']) }}%</span>
                            <span class="text-purple-600 ml-1">from last month</span>
                        @else
                            <i class="fas fa-minus text-gray-500 mr-1"></i>
                            <span class="text-purple-600">No change from last month</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hover Effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-purple-500/0 to-violet-500/0 group-hover:from-purple-500/5 group-hover:to-violet-500/5 transition-all duration-300 rounded-xl"></div>
        </div>
        @else
        <!-- Total Courses Card for Admin/Teacher -->
        <div class="group relative bg-gradient-to-br from-indigo-50 to-blue-100 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-indigo-200/50 p-6 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-blue-500/10 opacity-50"></div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl"></div>

            <div class="relative flex items-center">
                <div class="p-4 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-book text-xl text-white"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-semibold text-indigo-700 uppercase tracking-wide mb-1">TOTAL COURSES</p>
                    <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($stats['total_courses'] ?? 0) }}</p>
                    <div class="flex items-center text-xs text-indigo-600">
                        <i class="fas fa-graduation-cap mr-1"></i>
                        <span>Available courses</span>
                    </div>
                </div>
            </div>

            <!-- Hover Effect -->
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/0 to-blue-500/0 group-hover:from-indigo-500/5 group-hover:to-blue-500/5 transition-all duration-300 rounded-xl"></div>
        </div>
        @endif
    @endif
</div>

<!-- Modern Quick Actions Section -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Quick Actions</h2>
            <p class="text-gray-600 text-sm">Frequently used actions for your role</p>
        </div>
        <button class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center space-x-1">
            <span>Customize</span>
            <i class="fas fa-cog"></i>
        </button>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Dashboard Refresh (Always Available) -->
        <button onclick="refreshDashboard()" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-blue-200 text-center">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-sync-alt text-white text-lg"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-1">Refresh</h3>
            <p class="text-xs text-gray-500">Reload dashboard</p>
            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/0 to-blue-600/0 group-hover:from-blue-500/5 group-hover:to-blue-600/5 transition-all duration-300"></div>
        </button>

        @if($role === 'Super Admin' || $role === 'Admin')
            <!-- Add Student -->
            @can('create-students')
            <a href="{{ route('students.create') }}" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-primary-200 text-center">
                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-user-plus text-white text-lg"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Add Student</h3>
                <p class="text-xs text-gray-500">Register new student</p>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-primary-500/0 to-primary-600/0 group-hover:from-primary-500/5 group-hover:to-primary-600/5 transition-all duration-300"></div>
            </a>
            @endcan

            <!-- Create Class -->
            @can('create-classes')
            <a href="{{ route('classes.create') }}" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-success-200 text-center">
                <div class="w-12 h-12 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-chalkboard-teacher text-white text-lg"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Create Class</h3>
                <p class="text-xs text-gray-500">Add new class</p>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-success-500/0 to-success-600/0 group-hover:from-success-500/5 group-hover:to-success-600/5 transition-all duration-300"></div>
            </a>
            @endcan

            <!-- Manage Users -->
            @can('view-users')
            <a href="{{ route('users.index') }}" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-secondary-200 text-center">
                <div class="w-12 h-12 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-users-cog text-white text-lg"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Manage Users</h3>
                <p class="text-xs text-gray-500">User management</p>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-secondary-500/0 to-secondary-600/0 group-hover:from-secondary-500/5 group-hover:to-secondary-600/5 transition-all duration-300"></div>
            </a>
            @endcan
        @endif

        @if($role === 'Examiner' || $role === 'Super Admin')
            <!-- Schedule Exam -->
            @can('create-exams')
            <a href="{{ route('exams.create') }}" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-warning-200 text-center">
                <div class="w-12 h-12 bg-gradient-to-br from-warning-500 to-warning-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-calendar-plus text-white text-lg"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Schedule Exam</h3>
                <p class="text-xs text-gray-500">Create new exam</p>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-warning-500/0 to-warning-600/0 group-hover:from-warning-500/5 group-hover:to-warning-600/5 transition-all duration-300"></div>
            </a>
            @endcan

            <!-- View Results -->
            @can('view-results')
            <a href="{{ route('results.index') }}" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-primary-200 text-center">
                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-chart-bar text-white text-lg"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">View Results</h3>
                <p class="text-xs text-gray-500">Exam results</p>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-primary-500/0 to-primary-600/0 group-hover:from-primary-500/5 group-hover:to-primary-600/5 transition-all duration-300"></div>
            </a>
            @endcan
        @endif

        @if($role === 'Accountant' || $role === 'Super Admin')
            <!-- Create Invoice -->
            @can('create-invoices')
            <a href="{{ route('finance.invoices.create') }}" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-success-200 text-center">
                <div class="w-12 h-12 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-file-invoice text-white text-lg"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Create Invoice</h3>
                <p class="text-xs text-gray-500">New invoice</p>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-success-500/0 to-success-600/0 group-hover:from-success-500/5 group-hover:to-success-600/5 transition-all duration-300"></div>
            </a>
            @endcan

            <!-- View Payments -->
            @can('view-payments')
            <a href="{{ route('finance.payments.index') }}" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-warning-200 text-center">
                <div class="w-12 h-12 bg-gradient-to-br from-warning-500 to-warning-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-credit-card text-white text-lg"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">View Payments</h3>
                <p class="text-xs text-gray-500">Payment history</p>
                <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-warning-500/0 to-warning-600/0 group-hover:from-warning-500/5 group-hover:to-warning-600/5 transition-all duration-300"></div>
            </a>
            @endcan
        @endif

        <!-- Reports (Available to all) -->
        <a href="{{ route('reports.index') }}" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-secondary-200 text-center">
            <div class="w-12 h-12 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-chart-line text-white text-lg"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-1">Reports</h3>
            <p class="text-xs text-gray-500">Analytics & reports</p>
            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-secondary-500/0 to-secondary-600/0 group-hover:from-secondary-500/5 group-hover:to-secondary-600/5 transition-all duration-300"></div>
        </a>

        <!-- Settings -->
        <a href="#" onclick="alert('Settings feature coming soon!')" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-danger-200 text-center">
            <div class="w-12 h-12 bg-gradient-to-br from-danger-500 to-danger-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-cog text-white text-lg"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-1">Settings</h3>
            <p class="text-xs text-gray-500">System settings</p>
            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-danger-500/0 to-danger-600/0 group-hover:from-danger-500/5 group-hover:to-danger-600/5 transition-all duration-300"></div>
        </a>

        <!-- My Profile -->
        <a href="{{ route('profile.show') }}" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-indigo-200 text-center">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-user text-white text-lg"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-1">My Profile</h3>
            <p class="text-xs text-gray-500">View & edit profile</p>
            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-indigo-500/0 to-indigo-600/0 group-hover:from-indigo-500/5 group-hover:to-indigo-600/5 transition-all duration-300"></div>
        </a>

        <!-- Activity Log -->
        <a href="{{ route('activity-logs.index') }}" class="group relative bg-white rounded-xl shadow-soft hover:shadow-large transition-all duration-300 p-4 border border-gray-100 hover:border-purple-200 text-center">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
                <i class="fas fa-history text-white text-lg"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-1">Activity Log</h3>
            <p class="text-xs text-gray-500">Recent activities</p>
            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-500/0 to-purple-600/0 group-hover:from-purple-500/5 group-hover:to-purple-600/5 transition-all duration-300"></div>
        </a>
    </div>

    <!-- Debug Information (Remove in production) -->

</div>

@if(($role === 'Super Admin' || $role === 'Admin') && isset($projectSummary) && !empty($projectSummary))
<!-- Project Summary Section -->
<div class="mb-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">System Overview</h2>
        <span class="text-sm text-gray-500">Project Status & Performance</span>
    </div>

    <!-- Project Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Project Status -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Project Status</h3>
                <i class="fas fa-chart-line text-2xl opacity-80"></i>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-blue-100">Completion:</span>
                    <span class="font-bold">{{ $projectSummary['overview']['completionPercentage'] ?? 83 }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-blue-100">Modules:</span>
                    <span class="font-bold">{{ $projectSummary['overview']['totalModules'] ?? 8 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-blue-100">Version:</span>
                    <span class="font-bold">{{ $projectSummary['overview']['version'] ?? '2.0' }}</span>
                </div>
            </div>
        </div>

        <!-- System Performance -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Performance</h3>
                <i class="fas fa-tachometer-alt text-2xl opacity-80"></i>
            </div>
            <div class="space-y-2">
                <div class="text-green-100 text-sm">DB Optimization: {{ $projectSummary['implementation']['databaseOptimization'] ?? '100%' }}</div>
                <div class="text-green-100 text-sm">Query Speed: {{ $projectSummary['implementation']['performanceImprovement'] ?? '60-80%' }} ↑</div>
                <div class="text-green-100 text-sm">CGPA Calc: {{ $projectSummary['systemHealth']['cgpaCalculation'] ?? '<100ms' }}</div>
            </div>
        </div>

        <!-- Academic Data -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Academic Data</h3>
                <i class="fas fa-graduation-cap text-2xl opacity-80"></i>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-purple-100">Students:</span>
                    <span class="font-bold">{{ $stats['total_students'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-purple-100">Courses:</span>
                    <span class="font-bold">{{ $projectSummary['academic']['totalCourses'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-purple-100">Teachers:</span>
                    <span class="font-bold">{{ $projectSummary['academic']['totalTeachers'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Technology Stack -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Tech Stack</h3>
                <i class="fas fa-code text-2xl opacity-80"></i>
            </div>
            <div class="space-y-2">
                <div class="text-orange-100 text-sm">{{ $projectSummary['technologyStack']['backend'] ?? 'Laravel 12' }}</div>
                <div class="text-orange-100 text-sm">{{ $projectSummary['technologyStack']['frontend'] ?? 'Vue.js 3' }}</div>
                <div class="text-orange-100 text-sm">{{ $projectSummary['technologyStack']['database'] ?? 'MySQL' }}</div>
            </div>
        </div>
    </div>

    <!-- Implementation Progress -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200/50 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Implementation Progress</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Phase 1: Core Optimizations</span>
                    <span>100%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                </div>

                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Overall Project</span>
                    <span>{{ $projectSummary['overview']['completionPercentage'] ?? 83 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $projectSummary['overview']['completionPercentage'] ?? 83 }}%"></div>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-900">Database Optimization</span>
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Completed</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-900">Performance Indexes</span>
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Completed</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-900">UI/UX Enhancements</span>
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Phase 2</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Achievements -->
    @if(isset($projectSummary['recentAchievements']) && !empty($projectSummary['recentAchievements']))
    <div class="bg-white rounded-xl shadow-lg border border-gray-200/50 p-6 mt-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Recent System Improvements</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($projectSummary['recentAchievements'] as $achievement)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between mb-2">
                    <h4 class="font-semibold text-gray-900 text-sm">{{ $achievement['title'] }}</h4>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $achievement['date'] }}</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">{{ $achievement['description'] }}</p>
                <div class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                    Impact: {{ $achievement['impact'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif

<!-- Data Visualization Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Enrollment Trends Chart -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200/50 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Student Enrollment Trends</h3>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                <span class="text-sm text-gray-600">Last 6 months</span>
            </div>
        </div>
        <div class="relative h-64">
            <canvas id="enrollmentChart"></canvas>
        </div>
    </div>

    <!-- Academic Performance Chart -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200/50 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Academic Performance</h3>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                <span class="text-sm text-gray-600">Current semester</span>
            </div>
        </div>
        <div class="relative h-64">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>
</div>

@if(($role === 'Super Admin' || $role === 'Accountant'))
<!-- Financial Analytics Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Revenue Trends -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-200/50 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Revenue Trends</h3>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span class="text-sm text-gray-600">Monthly revenue</span>
            </div>
        </div>
        <div class="relative h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Payment Status Distribution -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200/50 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900">Payment Status</h3>
            <i class="fas fa-chart-pie text-gray-400"></i>
        </div>
        <div class="relative h-64">
            <canvas id="paymentStatusChart"></canvas>
        </div>
    </div>
</div>
@endif

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

<script>
// Chart.js Configuration and Initialization
document.addEventListener('DOMContentLoaded', function() {
    // Common chart options
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    color: '#6B7280'
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#6B7280'
                }
            }
        }
    };

    // Enrollment Trends Chart
    const enrollmentCtx = document.getElementById('enrollmentChart');
    if (enrollmentCtx) {
        new Chart(enrollmentCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['enrollment_trends']['labels']),
                datasets: [{
                    label: 'New Enrollments',
                    data: @json($chartData['enrollment_trends']['data']),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3B82F6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    ...chartOptions.plugins,
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#3B82F6',
                        borderWidth: 1
                    }
                }
            }
        });
    }

    // Academic Performance Chart
    const performanceCtx = document.getElementById('performanceChart');
    if (performanceCtx) {
        new Chart(performanceCtx, {
            type: 'doughnut',
            data: {
                labels: @json($chartData['academic_performance']['labels']),
                datasets: [{
                    data: @json($chartData['academic_performance']['data']),
                    backgroundColor: @json($chartData['academic_performance']['colors']),
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            color: '#6B7280'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff'
                    }
                }
            }
        });
    }

    @if(($role === 'Super Admin' || $role === 'Accountant'))
    // Revenue Trends Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['revenue_trends']['labels']),
                datasets: [{
                    label: 'Revenue (NRs)',
                    data: @json($chartData['revenue_trends']['data']),
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#10B981',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    ...chartOptions.plugins,
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: NRs ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Payment Status Chart
    const paymentStatusCtx = document.getElementById('paymentStatusChart');
    if (paymentStatusCtx) {
        new Chart(paymentStatusCtx, {
            type: 'pie',
            data: {
                labels: @json($chartData['payment_status']['labels']),
                datasets: [{
                    data: @json($chartData['payment_status']['data']),
                    backgroundColor: @json($chartData['payment_status']['colors']),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            color: '#6B7280'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Modern Dashboard Enhancements

    // Real-time clock update
    function updateClock() {
        const clockElement = document.querySelector('.current-time');
        if (clockElement) {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            clockElement.textContent = timeString;
        }
    }

    // Update clock every minute
    setInterval(updateClock, 60000);

    // Smooth scroll for quick actions
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add ripple effect to buttons
    function createRipple(event) {
        const button = event.currentTarget;
        const circle = document.createElement("span");
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${event.clientX - button.offsetLeft - radius}px`;
        circle.style.top = `${event.clientY - button.offsetTop - radius}px`;
        circle.classList.add("ripple");

        const ripple = button.getElementsByClassName("ripple")[0];
        if (ripple) {
            ripple.remove();
        }

        button.appendChild(circle);
    }

    // Apply ripple effect to buttons
    document.querySelectorAll('.group').forEach(button => {
        button.addEventListener('click', createRipple);
    });

    // Refresh dashboard function
    window.refreshDashboard = function() {
        // Add loading state
        const refreshBtn = document.querySelector('[onclick="refreshDashboard()"]');
        if (refreshBtn) {
            const icon = refreshBtn.querySelector('i');
            icon.classList.add('fa-spin');

            // Simulate refresh (in real app, this would reload data)
            setTimeout(() => {
                icon.classList.remove('fa-spin');
                // Show success message
                showNotification('Dashboard refreshed successfully!', 'success');
            }, 1000);
        }
    };

    // Show notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-large max-w-sm transform transition-all duration-300 translate-x-full`;

        const bgColor = type === 'success' ? 'bg-success-500' :
                       type === 'error' ? 'bg-danger-500' :
                       type === 'warning' ? 'bg-warning-500' : 'bg-primary-500';

        notification.classList.add(bgColor);
        notification.innerHTML = `
            <div class="flex items-center space-x-3 text-white">
                <i class="fas fa-${type === 'success' ? 'check-circle' :
                                  type === 'error' ? 'exclamation-circle' :
                                  type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
                <span class="font-medium">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Add loading states to quick action buttons
    document.querySelectorAll('.group a').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.href.includes('#') || this.onclick) return;

            const icon = this.querySelector('i');
            const originalClass = icon.className;
            icon.className = 'fas fa-spinner fa-spin text-white text-lg';

            // Restore original icon after a short delay (for demo purposes)
            setTimeout(() => {
                icon.className = originalClass;
            }, 500);
        });
    });

    // Initialize tooltips for quick actions
    document.querySelectorAll('.group').forEach(element => {
        element.addEventListener('mouseenter', function() {
            const h3 = this.querySelector('h3');
            const p = this.querySelector('p');
            const title = h3 ? h3.textContent : '';
            const description = p ? p.textContent : '';
            // Create tooltip (simplified version)
            this.setAttribute('title', `${title}: ${description}`);
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + R to refresh dashboard
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            refreshDashboard();
        }

        // Escape to close any open modals/notifications
        if (e.key === 'Escape') {
            document.querySelectorAll('.fixed.top-4.right-4').forEach(el => el.remove());
        }
    });

    // Performance monitoring
    if ('performance' in window) {
        window.addEventListener('load', function() {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log(`Dashboard loaded in ${loadTime}ms`);
        });
    }

    // Global functions for compatibility
    window.toggleShortcutVisibility = function(shortcutId) {
        // This function is handled by Alpine.js components
        console.log('toggleShortcutVisibility called for:', shortcutId);
    };

    window.isShortcutVisible = function(shortcutId) {
        // This function is handled by Alpine.js components
        return true;
    };

    // Fix for visitableShortcuts error
    if (typeof window.visitableShortcuts === 'undefined') {
        window.visitableShortcuts = [];
    }

    // Global toast functions
    window.showSuccessToast = function(message) {
        showNotification(message, 'success');
    };

    window.showErrorToast = function(message) {
        showNotification(message, 'error');
    };

    window.showWarningToast = function(message) {
        showNotification(message, 'warning');
    };

    window.showInfoToast = function(message) {
        showNotification(message, 'info');
    };
});

// Add CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection
