@extends('layouts.app')

@section('title', 'Grade Details')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Grade Details</h1>
                        <p class="text-sm text-gray-600 mt-1">View grade information and performance</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('grades.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Grades
                        </a>
                        @can('edit-exams')
                            <a href="{{ route('grades.edit', $grade) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Grade
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Grade Information -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Grade Information</h2>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Student</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $grade->student->user->first_name }} {{ $grade->student->user->last_name }}
                                    <div class="text-xs text-gray-500">{{ $grade->student->matric_number }}</div>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Course</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $grade->enrollment->class->course->title }}
                                    <div class="text-xs text-gray-500">{{ $grade->enrollment->class->course->code }}</div>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $grade->academicYear->name }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Semester</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($grade->semester) }} Semester</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Grade Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $grade->grade_type)) }}
                                    </span>
                                </dd>
                            </div>
                            
                            @if($grade->exam)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Exam</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="{{ route('exams.show', $grade->exam) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $grade->exam->title }}
                                    </a>
                                </dd>
                            </div>
                            @endif
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Graded By</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $grade->grader->first_name ?? 'System' }} {{ $grade->grader->last_name ?? '' }}
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Graded At</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $grade->graded_at ? $grade->graded_at->format('M d, Y H:i') : 'Not set' }}
                                </dd>
                            </div>
                        </dl>
                        
                        @if($grade->remarks)
                        <div class="mt-6">
                            <dt class="text-sm font-medium text-gray-500">Remarks</dt>
                            <dd class="mt-1 text-sm text-gray-900 bg-gray-50 rounded-md p-3">
                                {{ $grade->remarks }}
                            </dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="space-y-6">
                <!-- Score Card -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Performance</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-gray-900">
                                {{ number_format($grade->score, 1) }}<span class="text-lg text-gray-500">/{{ number_format($grade->max_score, 1) }}</span>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ number_format($grade->getPercentage(), 1) }}%
                            </div>
                            
                            <!-- Letter Grade -->
                            <div class="mt-4">
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium
                                    {{ $grade->letter_grade === 'A' ? 'bg-green-100 text-green-800' : 
                                       ($grade->letter_grade === 'B' ? 'bg-blue-100 text-blue-800' : 
                                       ($grade->letter_grade === 'C' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($grade->letter_grade === 'F' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                    Grade {{ $grade->letter_grade }}
                                </span>
                            </div>
                            
                            <!-- Grade Point -->
                            <div class="mt-2">
                                <div class="text-sm text-gray-500">Grade Point</div>
                                <div class="text-xl font-semibold text-gray-900">{{ number_format($grade->grade_point, 1) }}</div>
                            </div>
                            
                            <!-- Status -->
                            <div class="mt-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $grade->isPassing() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $grade->getStatus() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Score Breakdown</h4>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Score</span>
                                    <span class="font-medium">{{ number_format($grade->getPercentage(), 1) }}%</span>
                                </div>
                                <div class="mt-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $grade->getPercentage() }}%"></div>
                                </div>
                            </div>
                            
                            @if($grade->exam)
                            <div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Pass Mark</span>
                                    <span class="font-medium">{{ number_format(($grade->exam->pass_mark / $grade->exam->total_marks) * 100, 1) }}%</span>
                                </div>
                                <div class="mt-1 bg-gray-200 rounded-full h-1">
                                    <div class="bg-yellow-500 h-1 rounded-full" style="width: {{ ($grade->exam->pass_mark / $grade->exam->total_marks) * 100 }}%"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                @can('edit-exams')
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Quick Actions</h4>
                        <div class="space-y-2">
                            <a href="{{ route('grades.edit', $grade) }}" 
                               class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Edit Grade
                            </a>
                            <a href="{{ route('grades.student-results', $grade->student) }}" 
                               class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                View All Student Results
                            </a>
                            @if($grade->exam)
                            <a href="{{ route('grades.exam-results', $grade->exam) }}" 
                               class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                View Exam Results
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
