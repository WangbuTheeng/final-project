@extends('layouts.dashboard')

@section('title', 'Bulk Enrollment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Bulk Student Enrollment</h1>
                <a href="{{ route('enrollments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Enrollments
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users"></i> Enroll Multiple Students in Courses
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Bulk Enrollment:</strong> This will enroll all active students in the selected department and level 
                        into the selected courses for the specified academic year and semester.
                    </div>

                    <form method="POST" action="{{ route('enrollments.bulk-store') }}" id="bulkEnrollmentForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                                                {{ $year->name }}
                                                @if($year->is_current) (Current) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                    <select name="semester" id="semester" class="form-select" required>
                                        <option value="">Select Semester</option>
                                        <option value="first">First Semester</option>
                                        <option value="second">Second Semester</option>
                                    </select>
                                    @error('semester')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select name="department_id" id="department_id" class="form-select" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">
                                                {{ $department->name }} ({{ $department->faculty->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                                    <select name="level" id="level" class="form-select" required>
                                        <option value="">Select Level</option>
                                        <option value="100">100 Level (Year 1)</option>
                                        <option value="200">200 Level (Year 2)</option>
                                        <option value="300">300 Level (Year 3)</option>
                                        <option value="400">400 Level (Year 4)</option>
                                        <option value="500">500 Level (Year 5)</option>
                                    </select>
                                    @error('level')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="enrollment_date" class="form-label">Enrollment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="enrollment_date" name="enrollment_date" 
                                   value="{{ old('enrollment_date', date('Y-m-d')) }}" required>
                            @error('enrollment_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Courses Selection -->
                        <div class="mb-4">
                            <label class="form-label">Select Courses <span class="text-danger">*</span></label>
                            <div id="coursesContainer">
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle"></i>
                                    Please select department, level, academic year, and semester to load available courses.
                                </div>
                            </div>
                            @error('course_ids')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Student Preview -->
                        <div class="mb-4">
                            <h6>Students to be Enrolled</h6>
                            <div id="studentsPreview">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Select department and level to preview students.
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('enrollments.index') }}" class="btn btn-secondary me-md-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                                <i class="fas fa-users"></i> Enroll Students
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
            coursesContainer.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading courses...</div>';

            // Fetch courses (you would implement this endpoint)
            fetch(`/api/courses?department_id=${departmentId}&level=${level}&academic_year_id=${academicYearId}&semester=${semester}`)
                .then(response => response.json())
                .then(data => {
                    if (data.courses && data.courses.length > 0) {
                        let html = '<div class="row">';
                        data.courses.forEach(course => {
                            html += `
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input course-checkbox" type="checkbox" 
                                               name="course_ids[]" value="${course.id}" id="course_${course.id}">
                                        <label class="form-check-label" for="course_${course.id}">
                                            <strong>${course.code}</strong> - ${course.title}
                                            <br><small class="text-muted">${course.credit_units} credits</small>
                                        </label>
                                    </div>
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
                        coursesContainer.innerHTML = '<div class="alert alert-warning">No courses found for the selected criteria.</div>';
                    }
                })
                .catch(error => {
                    coursesContainer.innerHTML = '<div class="alert alert-danger">Error loading courses. Please try again.</div>';
                });
        } else {
            coursesContainer.innerHTML = '<div class="alert alert-warning"><i class="fas fa-info-circle"></i> Please select department, level, academic year, and semester to load available courses.</div>';
        }
    }

    function loadStudents() {
        const departmentId = departmentSelect.value;
        const level = levelSelect.value;

        if (departmentId && level) {
            // Show loading
            studentsPreview.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading students...</div>';

            // Fetch students (you would implement this endpoint)
            fetch(`/api/students?department_id=${departmentId}&level=${level}&status=active`)
                .then(response => response.json())
                .then(data => {
                    if (data.students && data.students.length > 0) {
                        let html = `<div class="alert alert-success">
                                      <strong>${data.students.length} students</strong> will be enrolled in the selected courses.
                                    </div>
                                    <div class="row">`;
                        
                        data.students.slice(0, 10).forEach(student => {
                            html += `
                                <div class="col-md-6 mb-1">
                                    <small>${student.matric_number} - ${student.user.first_name} ${student.user.last_name}</small>
                                </div>
                            `;
                        });
                        
                        if (data.students.length > 10) {
                            html += `<div class="col-12"><small class="text-muted">... and ${data.students.length - 10} more students</small></div>`;
                        }
                        
                        html += '</div>';
                        studentsPreview.innerHTML = html;
                    } else {
                        studentsPreview.innerHTML = '<div class="alert alert-warning">No active students found for the selected department and level.</div>';
                    }
                })
                .catch(error => {
                    studentsPreview.innerHTML = '<div class="alert alert-danger">Error loading students. Please try again.</div>';
                });
        } else {
            studentsPreview.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Select department and level to preview students.</div>';
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
@endsection
