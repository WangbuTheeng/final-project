@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Subjects</h1>
            <p class="mt-1 text-sm text-gray-500">Manage subjects/topics within classes</p>
        </div>
        @can('manage-courses')
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('subjects.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Subject
            </a>
        </div>
        @endcan
    </div>

    <!-- Hierarchy Info -->
    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm">
                    <strong>Structure:</strong> Faculty → Course → Class → Subject
                    <br>Subjects are specific topics or modules within a class.
                </p>
            </div>
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

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('subjects.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search subjects..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Class Filter -->
                <div>
                    <select name="class_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} ({{ $class->course->title }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Difficulty Filter -->
                <div>
                    <select name="difficulty_level" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Difficulties</option>
                        @foreach($difficultyLevels as $level)
                            <option value="{{ $level }}" {{ request('difficulty_level') == $level ? 'selected' : '' }}>
                                {{ ucfirst($level) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <select name="subject_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Types</option>
                        @foreach($subjectTypes as $type)
                            <option value="{{ $type }}" {{ request('subject_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Button -->
                <div>
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-filter mr-2"></i>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Subjects Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Subjects</h3>
        </div>

        @if($subjects->count() > 0)
            <div class="overflow-x-auto responsive-table">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subject
                            </th>
                            <th scope="col" class="hidden md:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Class → Course → Faculty
                            </th>
                            <th scope="col" class="hidden lg:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Instructor
                            </th>
                            <th scope="col" class="hidden xl:table-cell px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Details
                            </th>
                            <th scope="col" class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="relative px-3 sm:px-6 py-2 sm:py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subjects as $subject)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <div>
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center justify-center h-5 w-5 sm:h-6 sm:w-6 rounded-full bg-primary-100 text-primary-800 text-xs font-medium mr-2 sm:mr-3 flex-shrink-0">
                                                {{ $subject->order_sequence }}
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $subject->name }}
                                                </div>
                                                <div class="text-xs sm:text-sm text-gray-500 truncate">
                                                    {{ $subject->code }}
                                                </div>
                                                <!-- Mobile: Show class info here -->
                                                <div class="md:hidden text-xs text-gray-400 mt-1 truncate">
                                                    {{ $subject->class->name }} • {{ $subject->class->course->title }}
                                                </div>
                                            </div>
                                        </div>
                                        @if($subject->description)
                                            <div class="text-xs sm:text-sm text-gray-500 truncate max-w-xs mt-1 hidden sm:block">
                                                {{ Str::limit($subject->description, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-3 sm:px-6 py-3 sm:py-4">
                                    <div class="text-sm text-gray-900">
                                        <div class="font-medium truncate">{{ $subject->class->name }}</div>
                                        <div class="text-gray-500 truncate">{{ $subject->class->course->title }}</div>
                                        <div class="text-gray-400 text-xs truncate">{{ $subject->class->course->faculty->name }}</div>
                                    </div>
                                </td>
                                <td class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 text-sm text-gray-900">
                                    @if($subject->instructor)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-6 w-6 sm:h-8 sm:w-8">
                                                <div class="h-6 w-6 sm:h-8 sm:w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-700">
                                                        {{ substr($subject->instructor->name, 0, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-2 sm:ml-3 min-w-0">
                                                <div class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $subject->instructor->name }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic text-xs sm:text-sm">No instructor</span>
                                    @endif
                                </td>
                                <td class="hidden xl:table-cell px-3 sm:px-6 py-3 sm:py-4 text-sm text-gray-500">
                                    <div class="space-y-1">
                                        <div class="flex items-center">
                                            <i class="fas fa-signal text-gray-400 mr-1 sm:mr-2 text-xs"></i>
                                            <span class="capitalize text-xs sm:text-sm">{{ $subject->difficulty_level }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-tag text-gray-400 mr-1 sm:mr-2 text-xs"></i>
                                            <span class="capitalize text-xs sm:text-sm">{{ $subject->subject_type }}</span>
                                        </div>
                                        @if($subject->duration_hours)
                                            <div class="flex items-center">
                                                <i class="fas fa-clock text-gray-400 mr-1 sm:mr-2 text-xs"></i>
                                                <span class="text-xs sm:text-sm">{{ $subject->formatted_duration }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center">
                                            <i class="fas fa-{{ $subject->is_mandatory ? 'star' : 'star-half-alt' }} text-gray-400 mr-1 sm:mr-2 text-xs"></i>
                                            <span class="text-xs sm:text-sm">{{ $subject->is_mandatory ? 'Mandatory' : 'Optional' }}</span>
                                        </div>
                                        @if($subject->total_full_marks > 0)
                                            <div class="flex items-center">
                                                <i class="fas fa-calculator text-gray-400 mr-1 sm:mr-2 text-xs"></i>
                                                <span class="text-xs">
                                                    Total: {{ $subject->total_full_marks }} marks
                                                    @if($subject->is_practical)
                                                        <span class="text-green-600 ml-1">(Practical)</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    @if($subject->is_active)
                                        <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1 text-xs"></i>
                                            <span class="hidden sm:inline">Active</span>
                                            <span class="sm:hidden">✓</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1 text-xs"></i>
                                            <span class="hidden sm:inline">Inactive</span>
                                            <span class="sm:hidden">✗</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-1 sm:space-x-2">
                                        <a href="{{ route('subjects.show', $subject) }}"
                                           class="text-primary-600 hover:text-primary-900 transition-colors duration-200 p-1"
                                           title="View Details">
                                            <i class="fas fa-eye text-xs sm:text-sm"></i>
                                        </a>
                                        @can('manage-courses')
                                        <a href="{{ route('subjects.edit', $subject) }}"
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200 p-1"
                                           title="Edit Subject">
                                            <i class="fas fa-edit text-xs sm:text-sm"></i>
                                        </a>
                                        <form action="{{ route('subjects.destroy', $subject) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Are you sure you want to delete this subject? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200 p-1"
                                                    title="Delete Subject">
                                                <i class="fas fa-trash text-xs sm:text-sm"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($subjects->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $subjects->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-book-open text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No subjects found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @can('manage-courses')
                        Get started by creating a new subject.
                    @else
                        No subjects are available to view.
                    @endcan
                </p>
                @can('manage-courses')
                <div class="mt-6">
                    <a href="{{ route('subjects.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Add Subject
                    </a>
                </div>
                @endcan
            </div>
        @endif
    </div>
</div>
@endsection
