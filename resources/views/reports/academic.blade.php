@extends('layouts.dashboard')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Academic Performance Reports</h1>
            <p class="text-gray-600 mt-1">Comprehensive academic performance and grade analytics</p>
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

<!-- Performance Statistics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-blue-100">
                <i class="fas fa-star text-xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Grades</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_grades']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-green-100">
                <i class="fas fa-chart-line text-xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Average GPA</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_gpa'], 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-purple-100">
                <i class="fas fa-trophy text-xl text-purple-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Highest GPA</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['highest_gpa'], 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-orange-100">
                <i class="fas fa-percentage text-xl text-orange-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Pass Rate</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pass_rate'], 1) }}%</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Academic Data</h3>
    <form method="GET" action="{{ route('reports.academic') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
            <select name="academic_year_id" id="academic_year_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Years</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                        {{ $year->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-2">Exam</label>
            <select name="exam_id" id="exam_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Exams</option>
                @foreach($exams as $exam)
                    <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                        {{ $exam->title }} - {{ $exam->class->course->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
            <select name="course_id" id="course_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->title }}
                    </option>
                @endforeach
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

<!-- Academic Performance Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Academic Performance Data</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Theory Marks</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Practical Marks</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Marks</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GPA</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($grades as $grade)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium" style="background-color: #37a2bc;">
                                    {{ substr($grade->student->user->first_name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $grade->student->user->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $grade->student->admission_number }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $grade->exam->title }}</div>
                            <div class="text-sm text-gray-500">{{ $grade->exam->exam_date->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $grade->subject->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $grade->theory_marks ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $grade->practical_marks ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $grade->total_marks }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $grade->grade === 'A+' || $grade->grade === 'A' ? 'bg-green-100 text-green-800' : 
                                   ($grade->grade === 'B+' || $grade->grade === 'B' ? 'bg-blue-100 text-blue-800' : 
                                    ($grade->grade === 'C+' || $grade->grade === 'C' ? 'bg-yellow-100 text-yellow-800' : 
                                     ($grade->grade === 'D' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'))) }}">
                                {{ $grade->grade }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ number_format($grade->grade_point, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-chart-line text-4xl text-gray-300 mb-4"></i>
                            <p class="text-lg font-medium">No academic data found</p>
                            <p class="text-sm">Try adjusting your filters or add some exam results to get started.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($grades->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $grades->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Performance Analytics -->
<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Grade Distribution -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Grade Distribution</h3>
        <div class="space-y-3">
            @php
                $gradeDistribution = $grades->groupBy('grade')->map->count()->sortKeys();
            @endphp
            @foreach(['A+', 'A', 'B+', 'B', 'C+', 'C', 'D', 'F'] as $gradeLevel)
                @php
                    $count = $gradeDistribution->get($gradeLevel, 0);
                    $percentage = $grades->count() > 0 ? ($count / $grades->count()) * 100 : 0;
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700 w-8">{{ $gradeLevel }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $gradeLevel === 'A+' || $gradeLevel === 'A' ? 'bg-green-500' : 
                                ($gradeLevel === 'B+' || $gradeLevel === 'B' ? 'bg-blue-500' : 
                                 ($gradeLevel === 'C+' || $gradeLevel === 'C' ? 'bg-yellow-500' : 
                                  ($gradeLevel === 'D' ? 'bg-orange-500' : 'bg-red-500'))) }}" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ $count }} ({{ number_format($percentage, 1) }}%)
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Top Performers -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performers</h3>
        <div class="space-y-3">
            @php
                $topPerformers = $grades->sortByDesc('grade_point')->take(5);
            @endphp
            @foreach($topPerformers as $index => $grade)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-medium" 
                             style="background-color: {{ $index === 0 ? '#fbbf24' : ($index === 1 ? '#9ca3af' : ($index === 2 ? '#cd7c2f' : '#37a2bc')) }};">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $grade->student->user->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $grade->subject->name }}</div>
                        </div>
                    </div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ number_format($grade->grade_point, 2) }} GPA
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
    alert('Academic report export coming soon!');
}
</script>
@endsection
