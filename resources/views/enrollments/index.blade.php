@extends('layouts.dashboard')

@section('title', 'Student Enrollments')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Student Enrollments</h1>
            <p class="mt-1 text-sm text-gray-500">Manage student course enrollments and academic progress</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('enrollments.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                New Enrollment
            </a>
            <a href="{{ route('enrollments.bulk-create') }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-users mr-2"></i>
                Bulk Enrollment
            </a>
        </div>
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Enrollments</dt>
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Enrollments</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['enrolled'] }}</dd>
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['completed'] }}</dd>
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
                                    <dt class="text-sm font-medium text-gray-500 truncate">Dropped</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['dropped'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Filter Enrollments</h3>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('enrollments.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                            <div>
                                <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Academic Year</label>
                                <div class="mt-1">
                                    <select name="academic_year_id" id="academic_year_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                        @if($academicYears->isEmpty())
                                            <option value="">No active academic years</option>
                                        @else
                                            @foreach($academicYears as $year)
                                                <option value="{{ $year->id }}"
                                                        {{ $selectedAcademicYear == $year->id ? 'selected' : '' }}>
                                                    {{ $year->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="faculty_id" class="block text-sm font-medium text-gray-700">Faculty</label>
                                <div class="mt-1">
                                    <select name="faculty_id" id="faculty_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                        <option value="">All Faculties</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->id }}"
                                                    {{ $selectedFaculty == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->name }}
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
                                        @foreach($availableCourses as $course)
                                            <option value="{{ $course->id }}"
                                                    {{ $selectedCourse == $course->id ? 'selected' : '' }}>
                                                {{ $course->code }} - {{ $course->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                                <div class="mt-1">
                                    <select name="class_id" id="class_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                        <option value="">All Classes</option>
                                        @foreach($availableClasses as $class)
                                            <option value="{{ $class->id }}"
                                                    {{ $selectedClass == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700">Department <span class="text-gray-400 text-xs">(Optional)</span></label>
                                <div class="mt-1">
                                    <select name="department_id" id="department_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}"
                                                    {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
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
                                        <option value="enrolled" {{ $selectedStatus == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                        <option value="completed" {{ $selectedStatus == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="failed" {{ $selectedStatus == 'failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="dropped" {{ $selectedStatus == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                        <option value="pending_grade" {{ $selectedStatus == 'pending_grade' ? 'selected' : '' }}>Pending Grade</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-filter mr-2"></i>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Enrollments Table -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            Enrollments - {{ $academicYears->find($selectedAcademicYear)->name ?? 'Unknown' }}
                        </h3>
                        <div class="text-sm text-gray-500">
                            {{ $enrollments->total() }} {{ Str::plural('enrollment', $enrollments->total()) }} found
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

                    <div class="overflow-x-auto table-container">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Student
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Admission
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Faculty
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Course
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Class
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col" class="relative px-3 py-2">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($enrollments as $enrollment)
                                    <tr class="table-row-hover transition-all duration-200 hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full avatar-gradient flex items-center justify-center shadow-sm">
                                                        <span class="text-white font-bold text-xs">
                                                            {{ substr($enrollment->student->user->first_name, 0, 1) }}{{ substr($enrollment->student->user->last_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-2">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $enrollment->student->user->full_name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ $enrollment->student->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 font-mono">
                                                {{ $enrollment->student->admission_number }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                            <div class="truncate max-w-24" title="{{ $enrollment->student->faculty->name ?? 'N/A' }}">
                                                {{ $enrollment->student->faculty->name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $enrollment->class->course->code }}</div>
                                                <div class="text-xs text-gray-500 truncate max-w-32" title="{{ $enrollment->class->course->title }}">{{ $enrollment->class->course->title }}</div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $enrollment->class->name }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium status-badge bg-{{ $enrollment->status_badge_color }}-100 text-{{ $enrollment->status_badge_color }}-800">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-900">{{ $enrollment->formatted_enrollment_date }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center space-x-1">
                                                <a href="{{ route('enrollments.show', $enrollment) }}"
                                                   class="action-btn inline-flex items-center p-1.5 border border-transparent rounded text-primary-600 hover:bg-primary-50 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-primary-500"
                                                   title="View Details">
                                                    <i class="fas fa-eye w-3 h-3"></i>
                                                </a>
                                                @if($enrollment->status === 'enrolled' && $enrollment->canBeDropped())
                                                    <button type="button" class="action-btn inline-flex items-center p-1.5 border border-transparent rounded text-red-600 hover:bg-red-50 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-red-500"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#dropModal{{ $enrollment->id }}"
                                                            title="Drop Enrollment">
                                                        <i class="fas fa-user-times w-3 h-3"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Drop Modal -->
                                    @if($enrollment->status === 'enrolled' && $enrollment->canBeDropped())
                                        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden" id="dropModal{{ $enrollment->id }}" tabindex="-1" aria-labelledby="dropModalLabel{{ $enrollment->id }}" aria-hidden="true">
                                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                                <form method="POST" action="{{ route('enrollments.drop', $enrollment) }}">
                                                    @csrf
                                                    <div class="flex justify-between items-center pb-3">
                                                        <h5 class="text-lg font-medium text-gray-900">Drop Enrollment</h5>
                                                        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="document.getElementById('dropModal{{ $enrollment->id }}').classList.add('hidden')" aria-label="Close">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    <div class="mt-2 space-y-4">
                                                        <p class="text-sm text-gray-500">Are you sure you want to drop <strong>{{ $enrollment->student->user->full_name }}</strong> 
                                                           from <strong>{{ $enrollment->class->course->code }}</strong>?</p>
                                                        
                                                        <div>
                                                            <label for="drop_reason" class="block text-sm font-medium text-gray-700">Reason for dropping <span class="text-red-600">*</span></label>
                                                            <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" id="drop_reason" name="drop_reason" 
                                                                      rows="3" required placeholder="Enter reason for dropping this enrollment"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="items-center px-4 py-3 mt-4 sm:flex sm:flex-row-reverse">
                                                        <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                            Drop Enrollment
                                                        </button>
                                                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm" onclick="document.getElementById('dropModal{{ $enrollment->id }}').classList.add('hidden')">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                                                </div>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">No enrollments found</h3>
                                                <p class="text-gray-500 mb-6">No enrollments match the selected criteria. Try adjusting your filters or create a new enrollment.</p>
                                                <a href="{{ route('enrollments.create') }}"
                                                   class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    <i class="fas fa-plus mr-2"></i>
                                                    Create First Enrollment
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($enrollments->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    {{ $enrollments->appends(request()->query())->links() }}
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing
                                            <span class="font-medium">{{ $enrollments->firstItem() }}</span>
                                            to
                                            <span class="font-medium">{{ $enrollments->lastItem() }}</span>
                                            of
                                            <span class="font-medium">{{ $enrollments->total() }}</span>
                                            results
                                        </p>
                                    </div>
                                    <div>
                                        {{ $enrollments->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all buttons that open modals
        const modalOpenButtons = document.querySelectorAll('[data-bs-toggle="modal"]');

        modalOpenButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetModalId = this.getAttribute('data-bs-target').substring(1);
                const targetModal = document.getElementById(targetModalId);
                if (targetModal) {
                    targetModal.classList.remove('hidden');
                }
            });
        });

        // Get all buttons that close modals
        const modalCloseButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');

        modalCloseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const modal = this.closest('.fixed.inset-0');
                if (modal) {
                    modal.classList.add('hidden');
                }
            });
        });

        // Dynamic filtering for academic year → faculty → course → class
        const academicYearSelect = document.getElementById('academic_year_id');
        const facultySelect = document.getElementById('faculty_id');
        const courseSelect = document.getElementById('course_id');
        const classSelect = document.getElementById('class_id');

        function updateCourses() {
            const academicYearId = academicYearSelect.value;
            const facultyId = facultySelect.value;

            if (!academicYearId || !facultyId || academicYearId === '' || facultyId === '') {
                courseSelect.innerHTML = '<option value="">All Courses</option>';
                classSelect.innerHTML = '<option value="">All Classes</option>';
                return;
            }

            // Show loading
            courseSelect.innerHTML = '<option value="">Loading courses...</option>';

            // Build URL with parameters
            const params = new URLSearchParams({
                academic_year_id: academicYearId,
                faculty_id: facultyId
            });

            // Fetch courses
            fetch(`/ajax/enrollment/courses?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    courseSelect.innerHTML = '<option value="">All Courses</option>';

                    if (data.courses && data.courses.length > 0) {
                        data.courses.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.id;
                            option.textContent = `${course.code} - ${course.title}`;
                            courseSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching courses:', error);
                    courseSelect.innerHTML = '<option value="">Error loading courses</option>';
                });
        }

        function updateClasses() {
            const academicYearId = academicYearSelect.value;
            const courseId = courseSelect.value;

            if (!academicYearId || !courseId || academicYearId === '' || courseId === '') {
                classSelect.innerHTML = '<option value="">All Classes</option>';
                return;
            }

            // Show loading
            classSelect.innerHTML = '<option value="">Loading classes...</option>';

            // Build URL with parameters
            const params = new URLSearchParams({
                academic_year_id: academicYearId,
                course_id: courseId
            });

            // Fetch classes
            fetch(`/ajax/classes/by-course?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    classSelect.innerHTML = '<option value="">All Classes</option>';

                    if (data.classes && data.classes.length > 0) {
                        data.classes.forEach(classItem => {
                            const option = document.createElement('option');
                            option.value = classItem.id;
                            option.textContent = classItem.name;
                            classSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching classes:', error);
                    classSelect.innerHTML = '<option value="">Error loading classes</option>';
                });
        }

        // Add event listeners
        academicYearSelect.addEventListener('change', function() {
            courseSelect.innerHTML = '<option value="">All Courses</option>';
            classSelect.innerHTML = '<option value="">All Classes</option>';
            if (this.value && facultySelect.value) {
                updateCourses();
            }
        });

        facultySelect.addEventListener('change', function() {
            courseSelect.innerHTML = '<option value="">All Courses</option>';
            classSelect.innerHTML = '<option value="">All Classes</option>';
            if (this.value && academicYearSelect.value) {
                updateCourses();
            }
        });

        courseSelect.addEventListener('change', function() {
            classSelect.innerHTML = '<option value="">All Classes</option>';
            if (this.value && academicYearSelect.value) {
                updateClasses();
            }
        });
    });
</script>
@endpush

<style>
/* Custom hover effects for table rows */
.table-row-hover:hover {
    background-color: #f9fafb;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Smooth transitions for action buttons */
.action-btn {
    transition: all 0.2s ease-in-out;
}

.action-btn:hover {
    transform: scale(1.05);
}

/* Custom gradient for avatars */
.avatar-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Status badge animations */
.status-badge {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
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
@endsection
