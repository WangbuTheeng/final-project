@extends('layouts.dashboard')

@section('title', 'Academic Year Details - ' . $academicYear->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $academicYear->name }}</h1>
                <p class="mt-2 text-sm text-gray-600">Academic Year Details</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('academic-years.edit', $academicYear) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('academic-years.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    <!-- Academic Year Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Basic Info Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-2xl text-blue-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Academic Year</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $academicYear->code }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classes Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-chalkboard-teacher text-2xl text-green-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Classes</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $academicYear->classes_count ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subjects Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-book text-2xl text-purple-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Subjects</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $academicYear->subjects_count ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-graduate text-2xl text-orange-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Students</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $academicYear->students_count ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Year Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Basic Information -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Academic Year Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $academicYear->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Code</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $academicYear->code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Duration</label>
                        <div class="mt-1 text-sm text-gray-900">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-play text-green-500 mr-2"></i>
                                <span>{{ $academicYear->start_date ? $academicYear->start_date->format('F d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-stop text-red-500 mr-2"></i>
                                <span>{{ $academicYear->end_date ? $academicYear->end_date->format('F d, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <div class="mt-1 flex items-center space-x-2">
                            @if($academicYear->is_current)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-star mr-1"></i>
                                    Current
                                </span>
                            @endif
                            @if($academicYear->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-pause mr-1"></i>
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($academicYear->description)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $academicYear->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Classes and Subjects -->
        <div class="lg:col-span-2">
            <!-- Classes Section -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Classes ({{ $academicYear->classes_count ?? 0 }})</h3>
                </div>
                <div class="px-6 py-4">
                    @if($academicYear->classes && $academicYear->classes->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($academicYear->classes as $class)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $class->name }}</h4>
                                            <p class="text-xs text-gray-500 mt-1">{{ $class->course->title ?? 'N/A' }}</p>
                                            <div class="mt-2 flex items-center text-xs text-gray-600">
                                                <i class="fas fa-users mr-1"></i>
                                                <span>{{ $class->enrolled_count }}/{{ $class->capacity }} students</span>
                                            </div>
                                            @if($class->subjects && $class->subjects->count() > 0)
                                                <div class="mt-2">
                                                    <span class="text-xs text-gray-500">Subjects: {{ $class->subjects->count() }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-2">
                                            @if($class->status === 'active')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Inactive
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-chalkboard-teacher text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No classes found for this academic year.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Subjects Section -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Subjects ({{ $academicYear->subjects_count ?? 0 }})</h3>
                </div>
                <div class="px-6 py-4">
                    @if($academicYear->classes && $academicYear->classes->count() > 0)
                        @php
                            $allSubjects = collect();
                            foreach($academicYear->classes as $class) {
                                if($class->subjects) {
                                    foreach($class->subjects as $subject) {
                                        $allSubjects->push([
                                            'subject' => $subject,
                                            'class' => $class
                                        ]);
                                    }
                                }
                            }
                            $groupedSubjects = $allSubjects->groupBy('subject.name');
                        @endphp

                        @if($groupedSubjects->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($groupedSubjects as $subjectName => $subjectInstances)
                                    @php $firstSubject = $subjectInstances->first()['subject']; @endphp
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $subjectName }}</h4>
                                                <p class="text-xs text-gray-500 mt-1 font-mono">{{ $firstSubject->code }}</p>
                                                <div class="mt-2 text-xs text-gray-600">
                                                    <div class="flex items-center mb-1">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        <span>{{ $firstSubject->credit_hours ?? 'N/A' }} credit hours</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                        <span>{{ $subjectInstances->count() }} class(es)</span>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <span class="text-xs text-gray-500">Classes:</span>
                                                    <div class="mt-1">
                                                        @foreach($subjectInstances as $instance)
                                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                                                {{ $instance['class']->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-book text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No subjects found for this academic year.</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-book text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No subjects found. Classes need to be created first.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection