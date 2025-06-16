@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $exam->getExamTypeLabel() }} - {{ $exam->getFormattedExamDate() }}
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="{{ route('exams.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Exams
            </a>
            @can('manage-exams')
                <a href="{{ route('exams.grades', $exam) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-edit mr-2"></i>
                    Enter Grades
                </a>
                <a href="{{ route('exams.edit', $exam) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-pencil-alt mr-2"></i>
                    Edit Exam
                </a>
            @endcan
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Exam Status Banner -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @php
                        $statusInfo = $exam->getStatusLabel();
                        $statusColors = [
                            'blue' => 'bg-blue-100 text-blue-800',
                            'yellow' => 'bg-yellow-100 text-yellow-800',
                            'green' => 'bg-green-100 text-green-800',
                            'red' => 'bg-red-100 text-red-800',
                            'gray' => 'bg-gray-100 text-gray-800'
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$statusInfo['color']] ?? $statusColors['gray'] }}">
                        {{ $statusInfo['label'] }}
                    </span>
                    @if($exam->venue)
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            {{ $exam->venue }}
                        </div>
                    @endif
                </div>
                <div class="text-sm text-gray-500">
                    Duration: {{ $exam->getFormattedDuration() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Exam Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Exam Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Class & Course</h4>
                            <p class="text-lg font-semibold text-gray-900">{{ $exam->class->name }}</p>
                            <p class="text-sm text-gray-600">{{ $exam->class->course->title }}</p>
                            @if($exam->class->course->faculty)
                                <p class="text-sm text-gray-500">{{ $exam->class->course->faculty->name }}</p>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Academic Year</h4>
                            <p class="text-lg font-semibold text-gray-900">{{ $exam->academicYear->name }}</p>
                            @if($exam->semester)
                                <p class="text-sm text-gray-600">Semester {{ $exam->semester }}</p>
                            @elseif($exam->year)
                                <p class="text-sm text-gray-600">Year {{ $exam->year }}</p>
                            @endif
                        </div>
                        @if($exam->subject)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Subject</h4>
                                <p class="text-lg font-semibold text-gray-900">{{ $exam->subject->name }}</p>
                                <p class="text-sm text-gray-600">{{ $exam->subject->code }}</p>
                            </div>
                        @endif
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Created By</h4>
                            <p class="text-lg font-semibold text-gray-900">{{ $exam->creator->name ?? 'System' }}</p>
                            <p class="text-sm text-gray-600">{{ $exam->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Marks Distribution -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Marks Distribution</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-600">{{ number_format($exam->total_marks, 0) }}</div>
                            <div class="text-sm text-gray-500">Total Marks</div>
                        </div>
                        @if($exam->hasTheory())
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">{{ number_format($exam->theory_marks, 0) }}</div>
                                <div class="text-sm text-gray-500">Theory Marks</div>
                            </div>
                        @endif
                        @if($exam->hasPractical())
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600">{{ number_format($exam->practical_marks, 0) }}</div>
                                <div class="text-sm text-gray-500">Practical Marks</div>
                            </div>
                        @endif
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Pass Mark:</span>
                            <span class="text-lg font-semibold text-gray-900">
                                {{ number_format($exam->pass_mark, 0) }} 
                                <span class="text-sm text-gray-500">({{ number_format($exam->getPassPercentage(), 1) }}%)</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Period -->
            @if($exam->start_date && $exam->end_date)
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Exam Period</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Start Date</h4>
                                <p class="text-lg font-semibold text-gray-900">{{ $exam->start_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">End Date</h4>
                                <p class="text-lg font-semibold text-gray-900">{{ $exam->end_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Duration</h4>
                                <p class="text-lg font-semibold text-gray-900">{{ $exam->getExamPeriodDuration() }} days</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Instructions -->
            @if($exam->instructions)
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Instructions</h3>
                    </div>
                    <div class="p-6">
                        <div class="prose max-w-none">
                            {!! nl2br(e($exam->instructions)) !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Statistics Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Statistics</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Enrolled Students</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $stats['total_enrolled'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Graded Students</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $stats['total_graded'] }}</span>
                    </div>
                    @if($stats['total_graded'] > 0)
                        <div class="pt-4 border-t border-gray-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Average Score</span>
                                <span class="text-lg font-semibold text-gray-900">{{ number_format($stats['average_score'], 1) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Pass Rate</span>
                                <span class="text-lg font-semibold text-green-600">{{ number_format($stats['pass_rate'], 1) }}%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Highest Score</span>
                                <span class="text-lg font-semibold text-blue-600">{{ number_format($stats['highest_score'], 1) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Lowest Score</span>
                                <span class="text-lg font-semibold text-red-600">{{ number_format($stats['lowest_score'], 1) }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Progress -->
            @if($stats['total_enrolled'] > 0)
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Grading Progress</h3>
                    </div>
                    <div class="p-6">
                        @php
                            $progressPercentage = $stats['total_enrolled'] > 0 ? ($stats['total_graded'] / $stats['total_enrolled']) * 100 : 0;
                        @endphp
                        <div class="mb-2 flex justify-between text-sm">
                            <span class="text-gray-600">{{ $stats['total_graded'] }} of {{ $stats['total_enrolled'] }} graded</span>
                            <span class="font-medium text-gray-900">{{ number_format($progressPercentage, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        @if($progressPercentage < 100)
                            <p class="mt-2 text-sm text-gray-500">
                                {{ $stats['total_enrolled'] - $stats['total_graded'] }} students remaining
                            </p>
                        @else
                            <p class="mt-2 text-sm text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                All students graded
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            @can('manage-exams')
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('exams.grades', $exam) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-edit mr-2"></i>
                            Enter/Edit Grades
                        </a>
                        <a href="{{ route('exams.edit', $exam) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-pencil-alt mr-2"></i>
                            Edit Exam
                        </a>
                        @if($exam->grades()->count() === 0)
                            <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this exam?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-trash mr-2"></i>
                                    Delete Exam
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endcan
        </div>
    </div>
</div>
@endsection
