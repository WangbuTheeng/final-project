@extends('layouts.dashboard')

@section('title', 'Bulk Enrollment')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900">Bulk Student Enrollment</h1>
        <a href="{{ route('enrollments.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mt-4 sm:mt-0">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Enrollments
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-users mr-2"></i> Enroll Multiple Students in Courses
            </h3>
        </div>
        <div class="p-6">
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

            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Bulk Enrollment:</strong> This will enroll all active students in the selected department and level 
                            into the selected courses for the specified academic year and semester.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('enrollments.bulk-store') }}" id="bulkEnrollmentForm" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Academic Year <span class="text-red-600">*</span></label>
                        <div class="mt-1">
                            <select name="academic_year_id" id="academic_year_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                                        {{ $year->name }}
                                        @if($year->is_current) (Current) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700">Semester <span class="text-red-600">*</span></label>
                        <div class="mt-1">
                            <select name="semester" id="semester"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                                <option value="">Select Semester</option>
                                <option value="1">First Semester</option>
                                <option value="2">Second Semester</option>
                            </select>
                            @error('semester')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700">Department <span class="text-red-600">*</span></label>
                        <div class="mt-1">
                            <select name="department_id" id="department_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">
                                        {{ $department->name }} ({{ $department->faculty->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700">Level <span class="text-red-600">*</span></label>
                        <div class="mt-1">
                            <select name="level" id="level"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                                <option value="">Select Level</option>
                                <option value="100">100 Level (Year 1)</option>
                                <option value="200">200 Level (Year 2)</option>
                                <option value="300">300 Level (Year 3)</option>
                                <option value="400">400 Level (Year 4)</option>
                                <option value="500">500 Level (Year 5)</option>
                            </select>
                            @error('level')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="enrollment_date" class="block text-sm font-medium text-gray-700">Enrollment Date <span class="text-red-600">*</span></label>
                    <div class="mt-1">
                        <input type="date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" id="enrollment_date" name="enrollment_date" 
                               value="{{ old('enrollment_date', date('Y-m-d')) }}" required>
                        @error('enrollment_date')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Courses Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Select Courses <span class="text-red-600">*</span></label>
                    <div id="coursesContainer" class="mt-1">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Please select department, level, academic year, and semester to load available courses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('course_ids')
                        <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Student Preview -->
                <div>
                    <h6 class="text-lg font-medium text-gray-900 mb-2">Students to be Enrolled</h6>
                    <div id="studentsPreview">
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">Select department and level to preview students.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('enrollments.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150" id="submitBtn" disabled>
                        <i class="fas fa-users mr-2"></i> Enroll Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department_id');
    const levelSelect = document.getElementById('level');
    const academicYearSelect = document.getElementById('academic_year_id');
    const semesterSelect = document.getElementById('semester');
    const coursesContainer = document.getElementById('coursesContainer');
    const studentsPreview = document.getElementById('studentsPreview');
    const submitBtn = document.getElementById('submitBtn');

    function loadCourses() {
        const departmentId = departmentSelect.value;
        const level = levelSelect.value;
        const academicYearId = academicYearSelect.value;
        const semester = semesterSelect.value;

        if (departmentId && level && academicYearId && semester) {
            // Show loading
            coursesContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-primary-500 text-2xl"></i><p class="text-gray-500 mt-2">Loading courses...</p></div>';

            // Fetch courses (you would implement this endpoint)
            fetch(`/api/courses?department_id=${departmentId}&level=${level}&academic_year_id=${academicYearId}&semester=${semester}`)
                .then(response => response.json())
                .then(data => {
                    if (data.courses && data.courses.length > 0) {
                        let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
                        data.courses.forEach(course => {
                            html += `
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="form-checkbox h-5 w-5 text-primary-600 course-checkbox" 
                                               name="course_ids[]" value="${course.id}" id="course_${course.id}">
                                        <span class="ml-2 text-sm text-gray-700">
                                            <strong>${course.code}</strong> - ${course.title}
                                            <br><span class="text-xs text-gray-500">${course.credit_units} credits</span>
                                        </span>
                                    </label>
                                </div>
                            `;
                        });
                        html += '</div>';
                        coursesContainer.innerHTML = html;

                        // Add event listeners to checkboxes
                        document.querySelectorAll('.course-checkbox').forEach(checkbox => {
                            checkbox.addEventListener('change', validateForm);
                        });
                    } else {
                        coursesContainer.innerHTML = '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-yellow-400"></i></div><div class="ml-3"><p class="text-sm text-yellow-700">No courses found for the selected criteria.</p></div></div></div>';
                    }
                })
                .catch(error => {
                    coursesContainer.innerHTML = '<div class="bg-red-50 border-l-4 border-red-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-times-circle text-red-400"></i></div><div class="ml-3"><p class="text-sm text-red-700">Error loading courses. Please try again.</p></div></div></div>';
                });
        } else {
            coursesContainer.innerHTML = '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-info-circle text-yellow-400"></i></div><div class="ml-3"><p class="text-sm text-yellow-700">Please select department, level, academic year, and semester to load available courses.</p></div></div></div>';
        }
    }

    function loadStudents() {
        const departmentId = departmentSelect.value;
        const level = levelSelect.value;

        if (departmentId && level) {
            // Show loading
            studentsPreview.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-primary-500 text-2xl"></i><p class="text-gray-500 mt-2">Loading students...</p></div>';

            // Fetch students (you would implement this endpoint)
            fetch(`/api/students?department_id=${departmentId}&level=${level}&status=active`)
                .then(response => response.json())
                .then(data => {
                    if (data.students && data.students.length > 0) {
                        let html = `<div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                                      <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle text-green-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-green-700">
                                                <strong>${data.students.length} students</strong> will be enrolled in the selected courses.
                                            </p>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">`;
                        
                        data.students.slice(0, 10).forEach(student => {
                            html += `
                                <div>
                                    <span class="text-sm text-gray-700">${student.admission_number} - ${student.user.first_name} ${student.user.last_name}</span>
                                </div>
                            `;
                        });
                        
                        if (data.students.length > 10) {
                            html += `<div class="col-span-full"><span class="text-xs text-gray-500">... and ${data.students.length - 10} more students</span></div>`;
                        }
                        
                        html += '</div>';
                        studentsPreview.innerHTML = html;
                    } else {
                        studentsPreview.innerHTML = '<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-yellow-400"></i></div><div class="ml-3"><p class="text-sm text-yellow-700">No active students found for the selected department and level.</p></div></div></div>';
                    }
                })
                .catch(error => {
                    studentsPreview.innerHTML = '<div class="bg-red-50 border-l-4 border-red-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-times-circle text-red-400"></i></div><div class="ml-3"><p class="text-sm text-red-700">Error loading students. Please try again.</p></div></div></div>';
                });
        } else {
            studentsPreview.innerHTML = '<div class="bg-blue-50 border-l-4 border-blue-400 p-4"><div class="flex"><div class="flex-shrink-0"><i class="fas fa-info-circle text-blue-400"></i></div><div class="ml-3"><p class="text-sm text-blue-700">Select department and level to preview students.</p></div></div></div>';
        }
    }

    function validateForm() {
        const selectedCourses = document.querySelectorAll('.course-checkbox:checked').length;
        const hasRequiredFields = departmentSelect.value && levelSelect.value && 
                                 academicYearSelect.value && semesterSelect.value;
        
        submitBtn.disabled = !(hasRequiredFields && selectedCourses > 0);
    }

    // Event listeners
    [departmentSelect, levelSelect, academicYearSelect, semesterSelect].forEach(select => {
        select.addEventListener('change', function() {
            loadCourses();
            validateForm();
        });
    });

    [departmentSelect, levelSelect].forEach(select => {
        select.addEventListener('change', loadStudents);
    });

    // Initial validation
    validateForm();
});
</script>
@endpush
@endsection
