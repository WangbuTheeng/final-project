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
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                    <p class="mb-0">Total Enrollments</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['enrolled'] }}</h4>
                                    <p class="mb-0">Active Enrollments</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['completed'] }}</h4>
                                    <p class="mb-0">Completed</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-graduation-cap fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $stats['dropped'] }}</h4>
                                    <p class="mb-0">Dropped</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-times fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('enrollments.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="academic_year_id" class="form-label">Academic Year</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select">
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" 
                                                {{ $selectedAcademicYear == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="semester" class="form-label">Semester</label>
                                <select name="semester" id="semester" class="form-select">
                                    <option value="first" {{ $selectedSemester == 'first' ? 'selected' : '' }}>First Semester</option>
                                    <option value="second" {{ $selectedSemester == 'second' ? 'selected' : '' }}>Second Semester</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="department_id" class="form-label">Department</label>
                                <select name="department_id" id="department_id" class="form-select">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" 
                                                {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="enrolled" {{ $selectedStatus == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                                    <option value="completed" {{ $selectedStatus == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ $selectedStatus == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="dropped" {{ $selectedStatus == 'dropped' ? 'selected' : '' }}>Dropped</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Enrollments Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Enrollments - {{ $academicYears->find($selectedAcademicYear)->name ?? 'Unknown' }} 
                        ({{ ucfirst($selectedSemester) }} Semester)
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

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Student</th>
                                    <th>Matric Number</th>
                                    <th>Course</th>
                                    <th>Class</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Enrollment Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrollments as $enrollment)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <span class="text-white fw-bold">
                                                        {{ substr($enrollment->student->user->first_name, 0, 1) }}{{ substr($enrollment->student->user->last_name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $enrollment->student->user->full_name }}</div>
                                                    <small class="text-muted">{{ $enrollment->student->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $enrollment->student->matric_number }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-bold">{{ $enrollment->class->course->code }}</div>
                                                <small class="text-muted">{{ $enrollment->class->course->title }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $enrollment->class->name }}</td>
                                        <td>{{ $enrollment->student->department->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $enrollment->status_badge_color }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $enrollment->formatted_enrollment_date }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('enrollments.show', $enrollment) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($enrollment->status === 'enrolled' && $enrollment->canBeDropped())
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#dropModal{{ $enrollment->id }}"
                                                            title="Drop Enrollment">
                                                        <i class="fas fa-user-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Drop Modal -->
                                    @if($enrollment->status === 'enrolled' && $enrollment->canBeDropped())
                                        <div class="modal fade" id="dropModal{{ $enrollment->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('enrollments.drop', $enrollment) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Drop Enrollment</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to drop <strong>{{ $enrollment->student->user->full_name }}</strong> 
                                                               from <strong>{{ $enrollment->class->course->code }}</strong>?</p>
                                                            
                                                            <div class="mb-3">
                                                                <label for="drop_reason" class="form-label">Reason for dropping <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" id="drop_reason" name="drop_reason" 
                                                                          rows="3" required placeholder="Enter reason for dropping this enrollment"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Drop Enrollment</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <p>No enrollments found for the selected criteria.</p>
                                                <a href="{{ route('enrollments.create') }}" class="btn btn-primary">
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
                        <div class="d-flex justify-content-center mt-4">
                            {{ $enrollments->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
}
</style>
@endsection
