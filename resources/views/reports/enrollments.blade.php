@extends('layouts.dashboard')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Enrollment Reports</h1>
            <p class="text-gray-600 mt-1">Student enrollment trends and analytics</p>
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

<!-- Enrollment Statistics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-blue-100">
                <i class="fas fa-clipboard-list text-xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Enrollments</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_enrollments']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-green-100">
                <i class="fas fa-user-check text-xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Active Enrollments</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_enrollments']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-purple-100">
                <i class="fas fa-calendar-alt text-xl text-purple-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">This Year</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_year_enrollments']) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Enrollment Trends by Academic Year -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Enrollment Trends by Academic Year</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-3 px-4 font-medium text-gray-500 text-sm uppercase tracking-wide">Academic Year</th>
                    <th class="text-left py-3 px-4 font-medium text-gray-500 text-sm uppercase tracking-wide">Total Enrollments</th>
                    <th class="text-left py-3 px-4 font-medium text-gray-500 text-sm uppercase tracking-wide">Active Enrollments</th>
                    <th class="text-left py-3 px-4 font-medium text-gray-500 text-sm uppercase tracking-wide">Growth</th>
                </tr>
            </thead>
            <tbody>
                @foreach($enrollmentTrends as $index => $trend)
                    @php
                        $previousTrend = $enrollmentTrends->get($index + 1);
                        $growth = $previousTrend ? 
                            round((($trend->total_enrollments - $previousTrend->total_enrollments) / $previousTrend->total_enrollments) * 100, 1) : 0;
                    @endphp
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm font-medium text-gray-900">
                            {{ $trend->academicYear->name }}
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-900">
                            {{ number_format($trend->total_enrollments) }}
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-900">
                            {{ number_format($trend->active_enrollments) }}
                        </td>
                        <td class="py-3 px-4 text-sm">
                            @if($growth > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    {{ $growth }}%
                                </span>
                            @elseif($growth < 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-arrow-down mr-1"></i>
                                    {{ abs($growth) }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    No change
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Enrollment by Faculty -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Enrollment Distribution by Faculty</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($enrollmentByFaculty as $faculty)
            @php
                $percentage = $stats['active_enrollments'] > 0 ? 
                    ($faculty->total_enrollments / $stats['active_enrollments']) * 100 : 0;
            @endphp
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-900">{{ $faculty->faculty_name }}</h4>
                    <span class="text-sm text-gray-500">{{ number_format($percentage, 1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    <div class="h-2 rounded-full" style="background-color: #37a2bc; width: {{ $percentage }}%"></div>
                </div>
                <p class="text-lg font-bold text-gray-900">{{ number_format($faculty->total_enrollments) }}</p>
                <p class="text-xs text-gray-500">students enrolled</p>
            </div>
        @endforeach
    </div>
</div>

<!-- Recent Enrollments -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Recent Enrollments</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrollment Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentEnrollments as $enrollment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium" style="background-color: #37a2bc;">
                                    {{ substr($enrollment->student->user->first_name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $enrollment->student->user->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $enrollment->student->admission_number }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $enrollment->class->course->title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $enrollment->class->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $enrollment->academicYear->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $enrollment->status === 'enrolled' ? 'bg-green-100 text-green-800' : 
                                   ($enrollment->status === 'graduated' ? 'bg-blue-100 text-blue-800' : 
                                    ($enrollment->status === 'dropped' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $enrollment->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-4"></i>
                            <p class="text-lg font-medium">No recent enrollments found</p>
                            <p class="text-sm">Enrollment data will appear here as students enroll.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Enrollment Analytics -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Monthly Enrollment Trends -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Enrollment Trends</h3>
        <div class="space-y-3">
            @php
                $monthlyEnrollments = $recentEnrollments->groupBy(function($enrollment) {
                    return $enrollment->created_at->format('M Y');
                })->map->count()->take(6);
            @endphp
            @foreach($monthlyEnrollments as $month => $count)
                @php
                    $maxCount = $monthlyEnrollments->max();
                    $percentage = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700 w-16">{{ $month }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full" style="background-color: #37a2bc; width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ $count }} enrollments
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Enrollment Status Distribution</h3>
        <div class="space-y-3">
            @php
                $statusDistribution = [
                    'enrolled' => $stats['active_enrollments'],
                    'graduated' => $stats['total_enrollments'] - $stats['active_enrollments'], // Simplified
                    'dropped' => 0, // Would need actual data
                    'suspended' => 0, // Would need actual data
                ];
                $totalForPercentage = array_sum($statusDistribution);
            @endphp
            @foreach($statusDistribution as $status => $count)
                @php
                    $percentage = $totalForPercentage > 0 ? ($count / $totalForPercentage) * 100 : 0;
                    $color = $status === 'enrolled' ? 'bg-green-500' : 
                            ($status === 'graduated' ? 'bg-blue-500' : 
                             ($status === 'dropped' ? 'bg-red-500' : 'bg-yellow-500'));
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700 w-20 capitalize">{{ $status }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $color }}" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ number_format($count) }} ({{ number_format($percentage, 1) }}%)
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function exportReport() {
    // Implementation for export
    alert('Enrollment report export coming soon!');
}
</script>
@endsection
