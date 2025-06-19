@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h1>
            <p class="mt-1 text-sm text-gray-500">Course details and information</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Courses
            </a>
            @can('manage-courses')
            <a href="{{ route('courses.edit', $course) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i>
                Edit Course
            </a>
            @endcan
        </div>
    </div>

    <!-- Course Information Card -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Course Information</h3>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $course->code }}
                    </span>
                    @if($course->is_active)
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
                        <label class="block text-sm font-medium text-gray-500">Course Title</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $course->title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Course Code</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $course->code }}</p>
                    </div>

                    @if($course->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Description</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $course->description }}</p>
                        </div>
                    @endif

                    <!-- Faculty Information (Primary) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Faculty</label>
                        <div class="mt-1 flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                    <span class="text-sm font-medium text-primary-700">
                                        {{ $course->faculty->code }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $course->faculty->name }}</p>
                                <p class="text-sm text-gray-500">Primary Faculty</p>
                            </div>
                        </div>
                    </div>

                    <!-- Department Information (Optional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Department</label>
                        @if($course->department)
                            <div class="mt-1 flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ $course->department->code }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $course->department->name }}</p>
                                    <p class="text-sm text-gray-500">Optional Organization</p>
                                </div>
                            </div>
                        @else
                            <p class="mt-1 text-sm text-gray-400 italic">No department assigned</p>
                        @endif
                    </div>
                </div>

                <!-- Course Details -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Credit Units</label>
                            <p class="mt-1 text-sm text-gray-900 flex items-center">
                                <i class="fas fa-star text-gray-400 mr-2"></i>
                                {{ $course->credit_units }} units
                            </p>
                        </div>



                        <div>
                            <label class="block text-sm font-medium text-gray-500">Year</label>
                            <p class="mt-1 text-sm text-gray-900 flex items-center">
                                <i class="fas fa-graduation-cap text-gray-400 mr-2"></i>
                                {{ $course->year }}{{ $course->year == 1 ? 'st' : ($course->year == 2 ? 'nd' : ($course->year == 3 ? 'rd' : 'th')) }} Year
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Semester</label>
                            <p class="mt-1 text-sm text-gray-900 flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                {{ ucfirst($course->semester) }} Semester
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Course Type</label>
                            <p class="mt-1 text-sm text-gray-900 flex items-center">
                                <i class="fas fa-tag text-gray-400 mr-2"></i>
                                {{ ucfirst($course->course_type) }} Course
                            </p>
                        </div>
                    </div>



                    <!-- Timestamps -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $course->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>

                    @if($course->updated_at != $course->created_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $course->updated_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Classes Section -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Classes</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ $course->classes->count() }} classes
                </span>
            </div>
        </div>

        @if($course->classes->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($course->classes as $class)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $class->name }}</h4>
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($class->semester) }} Semester
                                    </span>
                                    @if($class->status === 'active')
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($class->status) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    <span>{{ $class->academicYear->name }}</span>
                                    @if($class->instructor)
                                        • <span>Instructor: {{ $class->instructor->name }}</span>
                                    @endif
                                    @if($class->room)
                                        • <span>Room: {{ $class->room }}</span>
                                    @endif
                                    • <span>Capacity: {{ $class->current_enrollment }}/{{ $class->capacity }}</span>
                                    • <span>{{ $class->subjects->count() }} subjects</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('classes.show', $class) }}" 
                                   class="text-primary-600 hover:text-primary-900 text-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-chalkboard-teacher text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No classes</h3>
                <p class="mt-1 text-sm text-gray-500">This course doesn't have any classes yet.</p>
                <div class="mt-6">
                    <a href="{{ route('classes.create') }}?course_id={{ $course->id }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Add Class
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
