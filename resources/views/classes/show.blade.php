@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $class->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $class->course ? $class->course->title : 'Course not found' }} - {{ $class->academicYear ? $class->academicYear->name : 'Academic year not found' }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            @can('manage-classes')
            <a href="{{ route('classes.edit', $class) }}"
               class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i>
                Edit Class
            </a>
            @endcan
            <a href="{{ route('classes.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Classes
            </a>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Class Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Class Information</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Class Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $class->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($class->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>
                                @elseif($class->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-flag-checkered mr-1"></i>
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Cancelled
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $class->academicYear ? $class->academicYear->name : 'Academic year not found' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Semester</dt>
                            <dd class="mt-1 text-sm text-gray-900">Semester {{ $class->semester }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Room</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $class->room ?: 'Not assigned' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Capacity</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $class->capacity }} students</dd>
                        </div>
                        @if($class->start_date && $class->end_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $class->start_date->format('M d, Y') }} - {{ $class->end_date->format('M d, Y') }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Course Information -->
            @if($class->course)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Course Information</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Course Title</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $class->course->title }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Course Code</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $class->course->code }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Credit Units</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $class->course->credit_units }} units</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Course Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($class->course->course_type) }}</dd>
                        </div>
                        @if($class->course->department)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Department</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $class->course->department->name }}</dd>
                            </div>
                            @if($class->course->department->faculty)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Faculty</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $class->course->department->faculty->name }}</dd>
                                </div>
                            @endif
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Organization</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $class->course->period_display }}</dd>
                        </div>
                    </dl>
                    @if($class->course->description)
                        <div class="mt-4">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $class->course->description }}</dd>
                        </div>
                    @endif
                </div>
            </div>
            @else
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Course Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-yellow-400 text-3xl mb-4"></i>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Course Not Found</h4>
                        <p class="text-sm text-gray-500">The course associated with this class section could not be found. It may have been deleted.</p>
                        <div class="mt-4">
                            <a href="{{ route('classes.edit', $class) }}"
                               class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Class to Fix
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Subjects Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">
                        Subjects ({{ $class->subjects->count() }})
                    </h3>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('subjects.index', ['class_id' => $class->id]) }}"
                           class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-list mr-2"></i>
                            View All Subjects
                        </a>
                        @can('manage-courses')
                            <a href="{{ route('subjects.create', ['class_id' => $class->id]) }}"
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-plus mr-2"></i>
                                Add Subject
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="px-6 py-4">
                    @if($class->subjects->count() > 0)
                        <div class="space-y-4">
                            @foreach($class->subjects as $subject)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <h4 class="text-sm font-medium text-gray-900">
                                                    {{ $subject->name }}
                                                </h4>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $subject->code }}
                                                </span>
                                                @if($subject->is_mandatory)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Mandatory
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Optional
                                                    </span>
                                                @endif
                                            </div>
                                            @if($subject->description)
                                                <p class="mt-1 text-sm text-gray-600">{{ Str::limit($subject->description, 100) }}</p>
                                            @endif
                                            <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                                @if($subject->instructor)
                                                    <span><i class="fas fa-user mr-1"></i>{{ $subject->instructor->name }}</span>
                                                @endif
                                                @if($subject->credit_weight)
                                                    <span><i class="fas fa-star mr-1"></i>{{ $subject->credit_weight }} credits</span>
                                                @endif
                                                @if($subject->duration_hours)
                                                    <span><i class="fas fa-clock mr-1"></i>{{ $subject->duration_hours }} hours</span>
                                                @endif
                                                <span><i class="fas fa-layer-group mr-1"></i>{{ ucfirst($subject->difficulty_level ?? 'Not set') }}</span>
                                            </div>
                                            @if($subject->full_marks_theory || $subject->full_marks_practical)
                                                <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                                    @if($subject->full_marks_theory)
                                                        <span><i class="fas fa-book mr-1"></i>Theory: {{ $subject->full_marks_theory }} marks</span>
                                                    @endif
                                                    @if($subject->full_marks_practical)
                                                        <span><i class="fas fa-flask mr-1"></i>Practical: {{ $subject->full_marks_practical }} marks</span>
                                                    @endif
                                                    <span><i class="fas fa-trophy mr-1"></i>Total: {{ ($subject->full_marks_theory ?? 0) + ($subject->full_marks_practical ?? 0) }} marks</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 ml-4">
                                            @can('view-subjects')
                                                <a href="{{ route('subjects.show', $subject) }}"
                                                   class="text-primary-600 hover:text-primary-900 text-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('manage-courses')
                                                <a href="{{ route('subjects.edit', $subject) }}"
                                                   class="text-yellow-600 hover:text-yellow-900 text-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Subjects Summary -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $class->subjects->where('is_mandatory', true)->count() }}</div>
                                <div class="text-sm text-gray-500">Mandatory Subjects</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $class->subjects->where('is_mandatory', false)->count() }}</div>
                                <div class="text-sm text-gray-500">Optional Subjects</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $class->subjects->sum('credit_weight') ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Total Credits</div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-book-open text-gray-400 text-3xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Subjects Added</h4>
                            <p class="text-sm text-gray-500 mb-4">This class doesn't have any subjects assigned yet.</p>
                            @can('manage-courses')
                                <a href="{{ route('subjects.create', ['class_id' => $class->id]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add First Subject
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>

            <!-- Schedule Information -->
            @if($class->schedule && count($class->schedule) > 0)
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Class Schedule</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            @foreach($class->schedule as $schedule)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-calendar-day text-gray-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ ucfirst($schedule['day'] ?? 'N/A') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $schedule['time'] ?? 'Time not set' }}
                                            </div>
                                        </div>
                                    </div>
                                    @if(isset($schedule['duration']))
                                        <div class="text-sm text-gray-500">
                                            {{ $schedule['duration'] }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Enrolled Students -->
            @if($class->enrollments->count() > 0)
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Enrolled Students ({{ $class->enrollments->count() }})</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Student
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Matric Number
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Enrollment Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        CGPA
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($class->enrollments as $enrollment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-gray-600">
                                                            {{ substr($enrollment->student->user->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $enrollment->student->user->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $enrollment->student->user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $enrollment->student->matric_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($enrollment->status === 'enrolled')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Enrolled
                                                </span>
                                            @elseif($enrollment->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Completed
                                                </span>
                                            @elseif($enrollment->status === 'dropped')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Dropped
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Failed
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $enrollment->enrollment_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($enrollment->hasParticipatedInExam())
                                                {{ $enrollment->getCgpa() }}
                                                @if($enrollment->getCgpa() == 0)
                                                    (Fail)
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Instructor Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Instructor</h3>
                </div>
                <div class="px-6 py-4">
                    @if($class->instructor)
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-600">
                                        {{ substr($class->instructor->name, 0, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $class->instructor->name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $class->instructor->email }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash text-gray-400 text-2xl mb-2"></i>
                            <p class="text-sm text-gray-500">No instructor assigned</p>
                            @can('manage-classes')
                            <button type="button" 
                                    class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                    onclick="document.getElementById('assign-instructor-modal').classList.remove('hidden')">
                                <i class="fas fa-plus mr-2"></i>
                                Assign Instructor
                            </button>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Stats</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Enrolled Students</span>
                        <span class="text-sm font-medium text-gray-900">{{ $class->enrollments->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Total Subjects</span>
                        <span class="text-sm font-medium text-gray-900">{{ $class->subjects->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Capacity</span>
                        <span class="text-sm font-medium text-gray-900">{{ $class->capacity }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Available Spots</span>
                        <span class="text-sm font-medium text-gray-900">{{ $class->capacity - $class->enrollments->count() }}</span>
                    </div>
                    <div class="pt-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Enrollment</span>
                            <span class="font-medium">{{ $class->capacity > 0 ? round(($class->enrollments->count() / $class->capacity) * 100) : 0 }}%</span>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $class->capacity > 0 ? min(100, ($class->enrollments->count() / $class->capacity) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
             @can('manage-classes')
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <a href="{{ route('subjects.index', ['class_id' => $class->id]) }}"
                       class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-book mr-2"></i>
                        Manage Subjects
                    </a>
                    <a href="{{ route('classes.edit', $class) }}"
                       class="w-full inline-flex justify-center items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Class
                    </a>
                    
                    @if($class->enrollments->count() == 0)
                        <form action="{{ route('classes.destroy', $class) }}"
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this class section? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Class
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>

<!-- Assign Instructor Modal -->
@if(!$class->instructor)
<div id="assign-instructor-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Instructor</h3>
            <form action="{{ route('classes.assign-instructor', $class) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="instructor_id" class="block text-sm font-medium text-gray-700">Select Instructor</label>
                    <select name="instructor_id" 
                            id="instructor_id" 
                            class='mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500'
                            required>
                        <option value="">Choose an instructor</option>
                        @foreach(\App\Models\User::whereHas('roles', function($query) { $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']); })->orderBy('name')->get() as $instructor)
                            <option value="{{ $instructor->id }}">{{ $instructor->name }} ({{ $instructor->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('assign-instructor-modal').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-primary-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
