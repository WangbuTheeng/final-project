@extends('layouts.dashboard')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Student Reports</h1>
            <p class="text-gray-600 mt-1">Comprehensive student enrollment and demographic reports</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Reports
            </a>
            <button onclick="exportReport()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white" style="background-color: #37a2bc;">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-blue-100">
                <i class="fas fa-users text-xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Students</p>
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
                <p class="text-sm font-medium text-gray-500">Enrolled</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['enrolled']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-purple-100">
                <i class="fas fa-graduation-cap text-xl text-purple-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Graduated</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['graduated']) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-lg bg-red-100">
                <i class="fas fa-user-times text-xl text-red-600"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Dropped</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['dropped']) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Students</h3>
    <form method="GET" action="{{ route('reports.students') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
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
            <label for="faculty_id" class="block text-sm font-medium text-gray-700 mb-2">Faculty</label>
            <select name="faculty_id" id="faculty_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Faculties</option>
                @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                        {{ $faculty->name }}
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

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
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

<!-- Students Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Student Directory</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admission No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($students as $student)
                    @php
                        $latestEnrollment = $student->enrollments->first();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-medium" style="background-color: #37a2bc;">
                                    {{ substr($student->user->first_name, 0, 1) }}{{ substr($student->user->last_name, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->user->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $student->admission_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $latestEnrollment ? $latestEnrollment->class->course->title : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $latestEnrollment ? $latestEnrollment->class->name : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($latestEnrollment)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $latestEnrollment->status === 'enrolled' ? 'bg-green-100 text-green-800' : 
                                       ($latestEnrollment->status === 'graduated' ? 'bg-blue-100 text-blue-800' : 
                                        ($latestEnrollment->status === 'dropped' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($latestEnrollment->status) }}
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Not Enrolled
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $latestEnrollment ? $latestEnrollment->academicYear->name : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @can('view-students')
                                    <a href="{{ route('students.show', $student) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan
                                @can('view-financial-reports')
                                    <a href="{{ route('finance.reports.student-statement', ['student_id' => $student->id]) }}" class="text-green-600 hover:text-green-900" title="Financial Statement">
                                        <i class="fas fa-dollar-sign"></i>
                                    </a>
                                @endcan
                                <a href="#" onclick="generateStudentReport({{ $student->id }})" class="text-purple-600 hover:text-purple-900" title="Generate Report">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-user-graduate text-4xl text-gray-300 mb-4"></i>
                            <p class="text-lg font-medium">No students found</p>
                            <p class="text-sm">Try adjusting your filters or add some students to get started.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($students->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $students->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script>
function exportReport() {
    // Get current filters
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'csv');
    
    // Create download link
    const url = '{{ route("reports.export-students") }}?' + params.toString();
    window.open(url, '_blank');
}

function generateStudentReport(studentId) {
    // Implementation for generating individual student report
    alert('Student report generation coming soon!');
}

// Auto-submit form when filters change
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('select[name]');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Optional: Auto-submit on change
            // this.form.submit();
        });
    });
});
</script>
@endsection
