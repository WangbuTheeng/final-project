@extends('layouts.dashboard')

@section('title', 'Students')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Students</h1>
            <p class="mt-1 text-sm text-gray-500">Manage student records and information</p>
        </div>
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('students.create') }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Student
            </a>
        </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-primary-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-user-check text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Students</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['active'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Graduated</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['graduated'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-user-times text-white"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Suspended</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['suspended'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filter Students</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('students.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <div class="mt-1">
                            <input type="text"   id="search"   name="search"   value="{{ $search }}"      placeholder="Name, email, or admission number"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="faculty_id" class="block text-sm font-medium text-gray-700">Faculty</label>
                        <div class="mt-1">
                            <select name="faculty_id" id="faculty_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="">All Faculties</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ $selectedFaculty == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700">Department (Optional)</label>
                        <div class="mt-1">
                            <select name="department_id" id="department_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="">All Departments</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700">Course</label>
                        <div class="mt-1">
                            <select name="course_id" id="course_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ $selectedCourse == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="mt-1">
                            <select name="status" id="status"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="">All Statuses</option>
                                <option value="active" {{ $selectedStatus == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="graduated" {{ $selectedStatus == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                <option value="suspended" {{ $selectedStatus == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="withdrawn" {{ $selectedStatus == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                                <option value="deferred" {{ $selectedStatus == 'deferred' ? 'selected' : '' }}>Deferred</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Admission Year</label>
                        <div class="mt-1">
                            <select name="academic_year_id" id="academic_year_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="">All Years</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $selectedAcademicYear == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Students List</h3>
                <div class="text-sm text-gray-500">
                    {{ $students->total() }} {{ Str::plural('student', $students->total()) }} found
                </div>
            </div>
        </div>
        <div class="overflow-hidden">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Responsive Compact Table Design -->
            <div class="hidden lg:block table-container no-scroll">
                <!-- Desktop Compact Table -->
                <table class="w-full divide-y divide-gray-200 compact-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Student
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Admission
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Faculty
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Location
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                CGPA
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="relative px-2 py-2">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($students as $student)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <!-- Student Info -->
                                <td class="px-3 py-3">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full avatar-gradient flex items-center justify-center">
                                                <span class="text-white font-bold text-xs">
                                                    {{ substr($student->user->first_name, 0, 1) }}{{ substr($student->user->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 truncate max-w-32">
                                                {{ $student->user->first_name . ' ' . $student->user->last_name }}
                                            </div>
                                            <div class="text-xs text-gray-500 truncate max-w-32">{{ $student->user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Admission Number -->
                                <td class="px-2 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 font-mono">
                                        {{ $student->admission_number }}
                                    </span>
                                </td>

                                <!-- Faculty -->
                                <td class="px-2 py-3">
                                    <div class="text-sm text-gray-900 font-medium truncate max-w-24">{{ $student->faculty->name ?? 'Not assigned' }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-24">{{ $student->department->name ?? 'No dept' }}</div>
                                </td>

                                <!-- Location -->
                                <td class="px-2 py-3">
                                    <div class="text-sm text-gray-900 truncate max-w-20">{{ $student->user->district ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-20">{{ $student->user->province ?? 'N/A' }}</div>
                                </td>

                                <!-- CGPA -->
                                <td class="px-2 py-3">
                                    <div class="text-center">
                                        <span class="text-sm font-bold {{ $student->cgpa >= 3.5 ? 'text-green-600' : ($student->cgpa >= 2.5 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ number_format($student->cgpa, 2) }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-2 py-3">
                                    @php
                                        $statusColors = [
                                            'active' => 'bg-green-100 text-green-800',
                                            'graduated' => 'bg-blue-100 text-blue-800',
                                            'suspended' => 'bg-red-100 text-red-800',
                                            'withdrawn' => 'bg-gray-100 text-gray-800',
                                            'deferred' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                        $statusColor = $statusColors[$student->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-2 py-3">
                                    <div class="flex items-center space-x-1">
                                        <a href="{{ route('students.show', $student) }}"
                                           class="inline-flex items-center p-1.5 border border-transparent rounded text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                           title="View Details">
                                            <i class="fas fa-eye w-3 h-3"></i>
                                        </a>
                                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                        <a href="{{ route('students.edit', $student) }}"
                                           class="inline-flex items-center p-1.5 border border-transparent rounded text-yellow-600 hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                                           title="Edit">
                                            <i class="fas fa-edit w-3 h-3"></i>
                                        </a>
                                        @endif
                                    </div>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-users text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No students found</h3>
                                        <p class="text-gray-500 mb-6">
                                            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                                No students match the selected criteria. Try adjusting your filters or add a new student.
                                            @else
                                                No students match the selected criteria. Try adjusting your filters.
                                            @endif
                                        </p>
                                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                        <a href="{{ route('students.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <i class="fas fa-plus mr-2"></i>
                                            Add First Student
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden space-y-4">
                @forelse($students as $student)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm mobile-card">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full avatar-gradient flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">
                                            {{ substr($student->user->first_name, 0, 1) }}{{ substr($student->user->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $student->user->first_name . ' ' . $student->user->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $student->user->email }}</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'graduated' => 'bg-blue-100 text-blue-800',
                                        'suspended' => 'bg-red-100 text-red-800',
                                        'withdrawn' => 'bg-gray-100 text-gray-800',
                                        'deferred' => 'bg-yellow-100 text-yellow-800'
                                    ];
                                    $statusColor = $statusColors[$student->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500">Admission:</span>
                                <span class="font-mono text-gray-900">{{ $student->admission_number }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">CGPA:</span>
                                <span class="font-bold {{ $student->cgpa >= 3.5 ? 'text-green-600' : ($student->cgpa >= 2.5 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($student->cgpa, 2) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Faculty:</span>
                                <span class="text-gray-900">{{ $student->faculty->name ?? 'Not assigned' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Location:</span>
                                <span class="text-gray-900">{{ $student->user->district ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                Citizenship: {{ $student->user->citizenship_number ?? 'N/A' }}
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('students.show', $student) }}"
                                   class="inline-flex items-center p-2 border border-transparent rounded text-blue-600 hover:bg-blue-50"
                                   title="View Details">
                                    <i class="fas fa-eye w-4 h-4"></i>
                                </a>
                                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                <a href="{{ route('students.edit', $student) }}"
                                   class="inline-flex items-center p-2 border border-transparent rounded text-yellow-600 hover:bg-yellow-50"
                                   title="Edit">
                                    <i class="fas fa-edit w-4 h-4"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No students found</h3>
                        <p class="text-gray-500 mb-6">
                            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                No students match the selected criteria. Try adjusting your filters or add a new student.
                            @else
                                No students match the selected criteria. Try adjusting your filters.
                            @endif
                        </p>
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                        <a href="{{ route('students.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-plus mr-2"></i>
                            Add First Student
                        </a>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($students->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            {{ $students->appends(request()->query())->links() }}
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{ $students->firstItem() }}</span>
                                    to
                                    <span class="font-medium">{{ $students->lastItem() }}</span>
                                    of
                                    <span class="font-medium">{{ $students->total() }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                {{ $students->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>


@push('styles')
<style>
/* Compact Table Styles */
.compact-table {
    font-size: 0.875rem;
}

.compact-table th {
    padding: 0.5rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.compact-table td {
    padding: 0.75rem;
    vertical-align: middle;
}

/* Prevent horizontal scrolling */
.no-scroll {
    overflow-x: hidden;
}

/* Truncate text for compact display */
.truncate-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Custom gradient for avatars */
.avatar-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Responsive table adjustments */
@media (max-width: 1024px) {
    .compact-table th,
    .compact-table td {
        padding: 0.5rem 0.25rem;
        font-size: 0.75rem;
    }
}

/* Mobile card hover effects */
.mobile-card {
    transition: all 0.2s ease-in-out;
}

.mobile-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Status badge styles */
.status-badge {
    font-size: 0.75rem;
    font-weight: 500;
}

/* Action button styles */
.action-btn {
    transition: all 0.2s ease-in-out;
}

.action-btn:hover {
    transform: scale(1.1);
}

/* Compact spacing utilities */
.space-compact > * + * {
    margin-left: 0.25rem;
}

/* Table row hover effects */
.table-row:hover {
    background-color: #f8fafc;
}

/* Ensure table fits container */
.table-container {
    width: 100%;
    max-width: 100%;
}

/* Hide scrollbars but keep functionality */
.table-container::-webkit-scrollbar {
    display: none;
}

.table-container {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Custom scrollbar for table */
.table-container::-webkit-scrollbar {
    height: 8px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const facultySelect = document.getElementById('faculty_id');
        const courseSelect = document.getElementById('course_id');
        const departmentSelect = document.getElementById('department_id');
        const academicYearSelect = document.getElementById('academic_year_id');

        function fetchDepartmentsAndCourses() {
            const facultyId = facultySelect.value;
            const academicYearId = academicYearSelect.value;

            // Reset departments and courses
            departmentSelect.innerHTML = '<option value="">All Departments</option>';
            courseSelect.innerHTML = '<option value="">All Courses</option>';

            if (facultyId) {
                // Fetch departments
                fetch(`/api/departments-by-faculty/${facultyId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(department => {
                            const option = document.createElement('option');
                            option.value = department.id;
                            option.textContent = department.name;
                            departmentSelect.appendChild(option);
                        });
                        // Pre-select department if it was selected before
                        if ("{{ $selectedDepartment }}" && "{{ $selectedFaculty }}" === facultyId) {
                            departmentSelect.value = "{{ $selectedDepartment }}";
                        }
                    })
                    .catch(error => console.error('Error fetching departments:', error));

                // Fetch courses if academic year is also selected
                if (academicYearId) {
                    fetch(`/api/courses-by-faculty?faculty_id=${facultyId}&academic_year_id=${academicYearId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.courses.forEach(course => {
                                const option = document.createElement('option');
                                option.value = course.id;
                                option.textContent = course.name;
                                courseSelect.appendChild(option);
                            });
                            // Pre-select course if it was selected before
                            if ("{{ $selectedCourse }}" && "{{ $selectedFaculty }}" === facultyId && "{{ $selectedAcademicYear }}" === academicYearId) {
                                courseSelect.value = "{{ $selectedCourse }}";
                            }
                        })
                        .catch(error => console.error('Error fetching courses:', error));
                }
            }
        }

        facultySelect.addEventListener('change', fetchDepartmentsAndCourses);
        academicYearSelect.addEventListener('change', fetchDepartmentsAndCourses);

        // Initial load if a faculty or academic year is already selected
        if (facultySelect.value || academicYearSelect.value) {
            fetchDepartmentsAndCourses();
        }
</script>
    });
</script>
@endpush
@endsection
