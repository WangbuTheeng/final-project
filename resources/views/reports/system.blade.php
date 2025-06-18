@extends('layouts.dashboard')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">System Reports</h1>
            <p class="text-gray-600 mt-1">System usage statistics and analytics</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Reports
            </a>
            <button onclick="exportReport()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white" style="background-color: #37a2bc;">
                <i class="fas fa-download mr-2"></i>
                Export Report
            </button>
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-blue-100">
                <i class="fas fa-users text-xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($userStats['total_users']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-green-100">
                <i class="fas fa-user-check text-xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Active Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($userStats['active_users']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-purple-100">
                <i class="fas fa-clock text-xl text-purple-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Recent Logins</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($userStats['recent_logins']) }}</p>
                <p class="text-xs text-gray-500">Last 7 days</p>
            </div>
        </div>
    </div>
</div>

<!-- System Usage Statistics -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">System Usage Overview</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-3" style="background-color: rgba(55, 162, 188, 0.1);">
                <i class="fas fa-calendar-alt text-2xl" style="color: #37a2bc;"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($systemStats['total_academic_years']) }}</p>
            <p class="text-sm text-gray-500">Academic Years</p>
        </div>

        <div class="text-center">
            <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-3" style="background-color: rgba(55, 162, 188, 0.1);">
                <i class="fas fa-university text-2xl" style="color: #37a2bc;"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($systemStats['total_faculties']) }}</p>
            <p class="text-sm text-gray-500">Faculties</p>
        </div>

        <div class="text-center">
            <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-3" style="background-color: rgba(55, 162, 188, 0.1);">
                <i class="fas fa-book text-2xl" style="color: #37a2bc;"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($systemStats['total_courses']) }}</p>
            <p class="text-sm text-gray-500">Courses</p>
        </div>

        <div class="text-center">
            <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-3" style="background-color: rgba(55, 162, 188, 0.1);">
                <i class="fas fa-chalkboard text-2xl" style="color: #37a2bc;"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($systemStats['total_classes']) }}</p>
            <p class="text-sm text-gray-500">Classes</p>
        </div>

        <div class="text-center">
            <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-3" style="background-color: rgba(55, 162, 188, 0.1);">
                <i class="fas fa-book-open text-2xl" style="color: #37a2bc;"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($systemStats['total_subjects']) }}</p>
            <p class="text-sm text-gray-500">Subjects</p>
        </div>
    </div>
</div>

<!-- Recent User Activity -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Recent User Activity</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentUsers as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium" style="background-color: #37a2bc;">
                                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->roles->isNotEmpty())
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $user->roles->first()->name }}
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    No Role
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                                <div class="text-xs text-gray-500">{{ $user->last_login_at->format('M d, Y g:i A') }}</div>
                            @else
                                <span class="text-gray-500">Never</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                            <p class="text-lg font-medium">No user activity found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- System Health & Performance -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- User Role Distribution -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">User Role Distribution</h3>
        <div class="space-y-3">
            @php
                $roleDistribution = \App\Models\User::with('roles')
                    ->get()
                    ->flatMap(function($user) {
                        return $user->roles->pluck('name');
                    })
                    ->countBy()
                    ->sortByDesc(function($count) { return $count; });
            @endphp
            @foreach($roleDistribution as $role => $count)
                @php
                    $percentage = $userStats['total_users'] > 0 ? ($count / $userStats['total_users']) * 100 : 0;
                    $colors = [
                        'Super Admin' => 'bg-red-500',
                        'Admin' => 'bg-blue-500',
                        'Teacher' => 'bg-green-500',
                        'Accountant' => 'bg-yellow-500',
                        'Examiner' => 'bg-purple-500',
                    ];
                    $color = $colors[$role] ?? 'bg-gray-500';
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700 w-24">{{ $role }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $color }}" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ $count }} ({{ number_format($percentage, 1) }}%)
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- System Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">System Information</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm font-medium text-gray-700">Laravel Version</span>
                <span class="text-sm text-gray-900">{{ app()->version() }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm font-medium text-gray-700">PHP Version</span>
                <span class="text-sm text-gray-900">{{ PHP_VERSION }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm font-medium text-gray-700">Database</span>
                <span class="text-sm text-gray-900">{{ config('database.default') }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm font-medium text-gray-700">Environment</span>
                <span class="text-sm text-gray-900">{{ app()->environment() }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm font-medium text-gray-700">Timezone</span>
                <span class="text-sm text-gray-900">{{ config('app.timezone') }}</span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-sm font-medium text-gray-700">Last Updated</span>
                <span class="text-sm text-gray-900">{{ now()->format('M d, Y g:i A') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">System Management</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('activity-logs.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
            <div class="p-2 rounded-lg bg-blue-100 mr-3">
                <i class="fas fa-history text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">Activity Logs</p>
                <p class="text-xs text-gray-500">View system logs</p>
            </div>
        </a>

        <a href="{{ route('users.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
            <div class="p-2 rounded-lg bg-green-100 mr-3">
                <i class="fas fa-users text-green-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">User Management</p>
                <p class="text-xs text-gray-500">Manage users</p>
            </div>
        </a>

        <a href="{{ route('roles.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
            <div class="p-2 rounded-lg bg-purple-100 mr-3">
                <i class="fas fa-user-tag text-purple-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">Roles & Permissions</p>
                <p class="text-xs text-gray-500">Manage access</p>
            </div>
        </a>

        <button onclick="clearCache()" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
            <div class="p-2 rounded-lg bg-orange-100 mr-3">
                <i class="fas fa-broom text-orange-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">Clear Cache</p>
                <p class="text-xs text-gray-500">System maintenance</p>
            </div>
        </button>
    </div>
</div>

<script>
function exportReport() {
    // Implementation for export
    alert('System report export coming soon!');
}

function clearCache() {
    if (confirm('Are you sure you want to clear the system cache?')) {
        // Implementation for cache clearing
        alert('Cache clearing functionality coming soon!');
    }
}
</script>
@endsection
