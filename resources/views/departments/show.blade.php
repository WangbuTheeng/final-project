@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $department->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Department details and information</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('departments.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Departments
            </a>
            <a href="{{ route('departments.edit', $department) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i>
                Edit Department
            </a>
        </div>
    </div>

    <!-- Department Information Card -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Department Information</h3>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $department->code }}
                    </span>
                    @if($department->is_active)
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
                        <label class="block text-sm font-medium text-gray-500">Department Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $department->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Department Code</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $department->code }}</p>
                    </div>

                    @if($department->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Description</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $department->description }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Faculty</label>
                        @if($department->faculty)
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="{{ route('faculties.show', $department->faculty) }}" class="text-primary-600 hover:text-primary-800">
                                    {{ $department->faculty->name }} ({{ $department->faculty->code }})
                                </a>
                            </p>
                        @else
                            <p class="mt-1 text-sm text-gray-400 italic">No faculty assigned</p>
                        @endif
                    </div>
                </div>

                <!-- Leadership & Timestamps -->
                <div class="space-y-4">
                    <!-- Head of Department Information -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Head of Department</label>
                        @if($department->headOfDepartment)
                            <div class="mt-1 flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($department->headOfDepartment->name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $department->headOfDepartment->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $department->headOfDepartment->email }}</p>
                                </div>
                            </div>
                        @else
                            <p class="mt-1 text-sm text-gray-400 italic">No head of department assigned</p>
                        @endif
                    </div>

                    <!-- Timestamps -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $department->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>

                    @if($department->updated_at != $department->created_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $department->updated_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Section -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Courses Offered</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ $department->courses->count() }} courses
                </span>
            </div>
        </div>

        @if($department->courses->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($department->courses as $course)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $course->name }}</h4>
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $course->code }}
                                    </span>
                                    @if($course->is_active)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                                @if($course->description)
                                    <p class="mt-1 text-sm text-gray-500">{{ Str::limit($course->description, 100) }}</p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('courses.show', $course) }}" 
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
                    <i class="fas fa-book text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No courses</h3>
                <p class="mt-1 text-sm text-gray-500">This department doesn't offer any courses yet.</p>
                <div class="mt-6">
                    <a href="{{ route('courses.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Add Course
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
