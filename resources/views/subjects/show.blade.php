@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $subject->display_name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Subject details and information</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('subjects.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Subjects
            </a>
            <a href="{{ route('subjects.edit', $subject) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i>
                Edit Subject
            </a>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <span class="text-gray-500 text-sm">{{ $subject->class->course->faculty->name }}</span>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500 text-sm">{{ $subject->class->course->title }}</span>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500 text-sm">{{ $subject->class->name }}</span>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-900 text-sm font-medium">{{ $subject->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Subject Information Card -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Subject Information</h3>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $subject->code }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subject->difficulty_level === 'beginner' ? 'bg-green-100 text-green-800' : ($subject->difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($subject->difficulty_level) }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        {{ ucfirst($subject->subject_type) }}
                    </span>
                    @if($subject->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>
                            Inactive
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Subject Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $subject->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Subject Code</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $subject->code }}</p>
                    </div>

                    @if($subject->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Description</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $subject->description }}</p>
                        </div>
                    @endif

                    <!-- Class Information -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Class</label>
                        <div class="mt-1">
                            <p class="text-sm font-medium text-gray-900">{{ $subject->class->name }}</p>
                            <p class="text-sm text-gray-500">{{ $subject->class->course->title }}</p>
                            <p class="text-sm text-gray-400">{{ $subject->class->course->faculty->name }}</p>
                        </div>
                    </div>

                    <!-- Instructor Information -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Instructor</label>
                        @if($subject->instructor)
                            <div class="mt-1 flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($subject->instructor->name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $subject->instructor->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $subject->instructor->email }}</p>
                                </div>
                            </div>
                        @else
                            <p class="mt-1 text-sm text-gray-400 italic">No instructor assigned</p>
                        @endif
                    </div>
                </div>

                <!-- Subject Details -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Order Sequence</label>
                            <p class="mt-1 text-sm text-gray-900 flex items-center">
                                <i class="fas fa-sort-numeric-up text-gray-400 mr-2"></i>
                                {{ $subject->order_sequence }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Duration</label>
                            <p class="mt-1 text-sm text-gray-900 flex items-center">
                                <i class="fas fa-clock text-gray-400 mr-2"></i>
                                {{ $subject->formatted_duration }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Credit Weight</label>
                            <p class="mt-1 text-sm text-gray-900 flex items-center">
                                <i class="fas fa-percentage text-gray-400 mr-2"></i>
                                {{ $subject->credit_weight ? $subject->credit_weight . '%' : 'Not specified' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Type</label>
                            <p class="mt-1 text-sm text-gray-900 flex items-center">
                                <i class="fas fa-tag text-gray-400 mr-2"></i>
                                {{ ucfirst($subject->subject_type) }}
                            </p>
                        </div>
                    </div>

                    <!-- Dates -->
                    @if($subject->start_date || $subject->end_date)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Schedule</label>
                            <div class="mt-1 space-y-1">
                                @if($subject->start_date)
                                    <p class="text-sm text-gray-900 flex items-center">
                                        <i class="fas fa-play text-gray-400 mr-2"></i>
                                        Start: {{ $subject->start_date->format('M d, Y') }}
                                    </p>
                                @endif
                                @if($subject->end_date)
                                    <p class="text-sm text-gray-900 flex items-center">
                                        <i class="fas fa-stop text-gray-400 mr-2"></i>
                                        End: {{ $subject->end_date->format('M d, Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1 space-y-1">
                            <p class="text-sm text-gray-900 flex items-center">
                                <i class="fas fa-{{ $subject->is_mandatory ? 'star' : 'star-half-alt' }} text-gray-400 mr-2"></i>
                                {{ $subject->is_mandatory ? 'Mandatory' : 'Optional' }}
                            </p>
                            <p class="text-sm text-gray-900 flex items-center">
                                <i class="fas fa-{{ $subject->is_active ? 'check-circle' : 'times-circle' }} text-gray-400 mr-2"></i>
                                {{ $subject->is_active ? 'Active' : 'Inactive' }}
                            </p>
                        </div>
                    </div>

                    <!-- Exam Marks Configuration -->
                    @if($subject->hasTheoryComponent() || $subject->hasPracticalComponent())
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Exam Marks Configuration</label>
                            <div class="mt-2 bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if($subject->hasTheoryComponent())
                                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                                            <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                                <i class="fas fa-book text-blue-500 mr-2"></i>
                                                Theory Component
                                            </h4>
                                            <div class="space-y-1">
                                                <p class="text-sm text-gray-600">
                                                    Full Marks: <span class="font-medium text-gray-900">{{ $subject->full_marks_theory }}</span>
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Pass Marks: <span class="font-medium text-gray-900">{{ $subject->pass_marks_theory }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($subject->hasPracticalComponent())
                                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                                            <h4 class="text-sm font-medium text-gray-900 mb-2 flex items-center">
                                                <i class="fas fa-flask text-green-500 mr-2"></i>
                                                Practical Component
                                            </h4>
                                            <div class="space-y-1">
                                                <p class="text-sm text-gray-600">
                                                    Full Marks: <span class="font-medium text-gray-900">{{ $subject->full_marks_practical ?? 'Not specified' }}</span>
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Pass Marks: <span class="font-medium text-gray-900">{{ $subject->pass_marks_practical ?? 'Not specified' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($subject->total_full_marks > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-900">Total Marks:</span>
                                            <span class="text-lg font-bold text-primary-600">{{ $subject->total_full_marks }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-900">Total Pass Marks:</span>
                                            <span class="text-lg font-bold text-green-600">{{ $subject->total_pass_marks }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Timestamps -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $subject->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>

                    @if($subject->updated_at != $subject->created_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $subject->updated_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Learning Objectives -->
    @if($subject->learning_objectives && count($subject->learning_objectives) > 0)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Learning Objectives</h3>
            </div>
            <div class="p-6">
                <ul class="space-y-2">
                    @foreach($subject->learning_objectives as $objective)
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                            <span class="text-sm text-gray-900">{{ $objective }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Resources -->
    @if($subject->resources && count($subject->resources) > 0)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Resources</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($subject->resources as $key => $resource)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $key) }}</h4>
                            <p class="mt-1 text-sm text-gray-600">{{ $resource }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Other Subjects in Class -->
    @if($classSubjects->count() > 0)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Other Subjects in {{ $subject->class->name }}</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($classSubjects as $classSubject)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-800 text-xs font-medium mr-3">
                                    {{ $classSubject->order_sequence }}
                                </span>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $classSubject->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $classSubject->code }}</p>
                                </div>
                                <div class="ml-4 flex space-x-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $classSubject->difficulty_level === 'beginner' ? 'bg-green-100 text-green-800' : ($classSubject->difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($classSubject->difficulty_level) }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ ucfirst($classSubject->subject_type) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('subjects.show', $classSubject) }}" 
                                   class="text-primary-600 hover:text-primary-900 text-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Navigation -->
    <div class="flex justify-between">
        @if($subject->getPreviousSubject())
            <a href="{{ route('subjects.show', $subject->getPreviousSubject()) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-chevron-left mr-2"></i>
                Previous Subject
            </a>
        @else
            <div></div>
        @endif

        @if($subject->getNextSubject())
            <a href="{{ route('subjects.show', $subject->getNextSubject()) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Next Subject
                <i class="fas fa-chevron-right ml-2"></i>
            </a>
        @endif
    </div>
</div>
@endsection
