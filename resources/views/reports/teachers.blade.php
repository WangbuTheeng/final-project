@extends('layouts.dashboard')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Teacher Reports</h1>
            <p class="text-gray-600 mt-1">Faculty management and performance analytics</p>
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

<!-- Teacher Statistics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-blue-100">
                <i class="fas fa-users text-xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Teachers</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-green-100">
                <i class="fas fa-user-check text-xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Active Teachers</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-red-100">
                <i class="fas fa-user-times text-xl text-red-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Inactive Teachers</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['inactive']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-yellow-100">
                <i class="fas fa-user-clock text-xl text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">On Leave</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['on_leave']) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Teachers</h3>
    <form method="GET" action="{{ route('reports.teachers') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
            <select name="department" id="department" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                        {{ $department }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white" style="background-color: #37a2bc;">
                <i class="fas fa-filter mr-2"></i>
                Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Teachers Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Teacher Directory</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($teachers as $teacher)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-medium" style="background-color: #37a2bc;">
                                    {{ substr($teacher->teacher_name, 0, 2) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $teacher->teacher_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $teacher->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $teacher->employee_id ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $teacher->department ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $teacher->position ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $teacher->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($teacher->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $teacher->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="#" onclick="viewTeacherDetails({{ $teacher->id }})" class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('view-financial-reports')
                                    <a href="#" onclick="viewSalaryReport({{ $teacher->id }})" class="text-green-600 hover:text-green-900" title="Salary Report">
                                        <i class="fas fa-dollar-sign"></i>
                                    </a>
                                @endcan
                                <a href="#" onclick="generateTeacherReport({{ $teacher->id }})" class="text-purple-600 hover:text-purple-900" title="Generate Report">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-chalkboard-teacher text-4xl text-gray-300 mb-4"></i>
                            <p class="text-lg font-medium">No teachers found</p>
                            <p class="text-sm">Try adjusting your filters or add some teachers to get started.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($teachers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $teachers->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Teacher Analytics -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Department Distribution -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Department Distribution</h3>
        <div class="space-y-3">
            @php
                $departmentDistribution = $teachers->groupBy('department')->map->count()->sortByDesc(function($count) { return $count; });
            @endphp
            @foreach($departmentDistribution as $departmentName => $count)
                @php
                    $percentage = $teachers->count() > 0 ? ($count / $teachers->count()) * 100 : 0;
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700 w-24 truncate">{{ $departmentName ?? 'Unassigned' }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full bg-blue-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ $count }} ({{ number_format($percentage, 1) }}%)
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Distribution</h3>
        <div class="space-y-3">
            @php
                $statusDistribution = [
                    'Active' => $teachers->filter(function($t) { return $t->status === 'active'; })->count(),
                    'Inactive' => $teachers->filter(function($t) { return $t->status === 'inactive'; })->count(),
                    'On Leave' => $teachers->filter(function($t) { return $t->status === 'on_leave'; })->count(),
                    'Terminated' => $teachers->filter(function($t) { return $t->status === 'terminated'; })->count(),
                ];
            @endphp
            @foreach($statusDistribution as $status => $count)
                @php
                    $percentage = $teachers->count() > 0 ? ($count / $teachers->count()) * 100 : 0;
                    $color = $status === 'Active' ? 'bg-green-500' :
                            ($status === 'Inactive' ? 'bg-gray-500' :
                             ($status === 'On Leave' ? 'bg-yellow-500' : 'bg-red-500'));
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700 w-20">{{ $status }}</span>
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
</div>

<script>
function exportReport() {
    // Get current filters
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'csv');
    
    // Implementation for export
    alert('Teacher report export coming soon!');
}

function viewTeacherDetails(teacherId) {
    // Implementation for viewing teacher details
    alert('Teacher details view coming soon!');
}

function viewSalaryReport(teacherId) {
    // Implementation for salary report
    alert('Salary report coming soon!');
}

function generateTeacherReport(teacherId) {
    // Implementation for generating individual teacher report
    alert('Teacher report generation coming soon!');
}
</script>
@endsection
