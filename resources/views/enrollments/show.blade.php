@extends('layouts.dashboard')

@section('title', 'Enrollment Details')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900">Enrollment Details</h1>
        <a href="{{ route('enrollments.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mt-4 sm:mt-0">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Enrollments
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Enrollment Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500">Enrollment ID:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->id }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Status:</p>
                <p class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium status-badge bg-{{ $enrollment->status_badge_color }}-100 text-{{ $enrollment->status_badge_color }}-800">
                        {{ ucfirst($enrollment->status) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Enrollment Date:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->formatted_enrollment_date }}</p>
            </div>
            @if($enrollment->drop_date)
            <div>
                <p class="text-sm font-medium text-gray-500">Drop Date:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->formatted_drop_date }}</p>
            </div>
            @endif
            @if($enrollment->drop_reason)
            <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-500">Drop Reason:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->drop_reason }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Student Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500">Student Name:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->student->user->full_name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Admission Number:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->student->admission_number }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Email:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->student->user->email }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Department:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->student->department->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Faculty:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->student->faculty->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Current Level:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->student->current_level }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Course & Class Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500">Course Code:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->class->course->code }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Course Title:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->class->course->title }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Class Name:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->class->name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Instructor:</p>
                <p class="mt-1 text-sm text-gray-900">
                    {{ $enrollment->class->instructor->user->full_name ?? 'Not Assigned' }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Academic Year:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->academicYear->name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Semester:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->semester ? ucfirst($enrollment->semester) : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Scores & Grade</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500">CA Score:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->ca_score ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Exam Score:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->exam_score ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Score:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->total_score ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Final Grade:</p>
                <p class="mt-1 text-sm text-gray-900">{{ $enrollment->final_grade ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
