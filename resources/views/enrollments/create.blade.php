@extends('layouts.dashboard')

@section('title', 'Create Enrollment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create New Enrollment</h1>
                <a href="{{ route('enrollments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Enrollments
                </a>
            </div>

            <div class="row">
                <!-- Filters -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Filters</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('enrollments.create') }}">
                                <div class="mb-3">
                                    <label for="academic_year_id" class="form-label">Academic Year</label>
                                    <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" 
                                                    {{ $selectedAcademicYear == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <select name="semester" id="semester" class="form-select" required>
                                        <option value="first" {{ $selectedSemester == 'first' ? 'selected' : '' }}>First Semester</option>
                                        <option value="second" {{ $selectedSemester == 'second' ? 'selected' : '' }}>Second Semester</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department</label>
                                    <select name="department_id" id="department_id" class="form-select" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                    {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="level" class="form-label">Level</label>
                                    <select name="level" id="level" class="form-select" required>
                                        <option value="">Select Level</option>
                                        <option value="100" {{ $selectedLevel == '100' ? 'selected' : '' }}>100 Level</option>
                                        <option value="200" {{ $selectedLevel == '200' ? 'selected' : '' }}>200 Level</option>
                                        <option value="300" {{ $selectedLevel == '300' ? 'selected' : '' }}>300 Level</option>
                                        <option value="400" {{ $selectedLevel == '400' ? 'selected' : '' }}>400 Level</option>
                                        <option value="500" {{ $selectedLevel == '500' ? 'selected' : '' }}>500 Level</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Enrollment Form -->
                <div class="col-md-8">
                    @if($selectedDepartment && $selectedLevel)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Enroll Student</h5>
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

                                @if($students->isNotEmpty() && $availableClasses->isNotEmpty())
                                    <form method="POST" action="{{ route('enrollments.store') }}">
                                        @csrf
                                        <input type="hidden" name="academic_year_id" value="{{ $selectedAcademicYear }}">
                                        <input type="hidden" name="semester" value="{{ $selectedSemester }}">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                                                    <select name="student_id" id="student_id" class="form-select" required>
                                                        <option value="">Select Student</option>
                                                        @foreach($students as $student)
                                                            <option value="{{ $student->id }}">
                                                                {{ $student->matric_number }} - {{ $student->user->full_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('student_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                                                    <select name="class_id" id="class_id" class="form-select" required>
                                                        <option value="">Select Class</option>
                                                        @foreach($availableClasses as $class)
                                                            <option value="{{ $class->id }}">
                                                                {{ $class->course->code }} - {{ $class->name }}
                                                                ({{ $class->current_enrollment }}/{{ $class->capacity }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('class_id')
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

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <a href="{{ route('enrollments.index') }}" class="btn btn-secondary me-md-2">
                                                Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Create Enrollment
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="text-center py-4">
                                        @if($students->isEmpty())
                                            <div class="text-muted">
                                                <i class="fas fa-user-slash fa-3x mb-3"></i>
                                                <p>No students found for the selected department and level.</p>
                                            </div>
                                        @elseif($availableClasses->isEmpty())
                                            <div class="text-muted">
                                                <i class="fas fa-chalkboard fa-3x mb-3"></i>
                                                <p>No available classes found for the selected criteria.</p>
                                                <p>Please ensure classes are created for this department, level, academic year, and semester.</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Available Classes Info -->
                        @if($availableClasses->isNotEmpty())
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Available Classes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Course Code</th>
                                                    <th>Course Title</th>
                                                    <th>Class Name</th>
                                                    <th>Instructor</th>
                                                    <th>Enrollment</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($availableClasses as $class)
                                                    <tr>
                                                        <td><span class="badge bg-primary">{{ $class->course->code }}</span></td>
                                                        <td>{{ $class->course->title }}</td>
                                                        <td>{{ $class->name }}</td>
                                                        <td>
                                                            @if($class->instructor)
                                                                {{ $class->instructor->full_name }}
                                                            @else
                                                                <span class="text-muted">Not assigned</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar" role="progressbar" 
                                                                     style="width: {{ $class->enrollment_percentage }}%">
                                                                    {{ $class->current_enrollment }}/{{ $class->capacity }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $class->status === 'active' ? 'success' : 'secondary' }}">
                                                                {{ ucfirst($class->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-filter fa-3x text-muted mb-3"></i>
                                <h5>Select Filters</h5>
                                <p class="text-muted">Please select department and level to view available students and classes for enrollment.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
