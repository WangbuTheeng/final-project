@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Courses</h1>
            <p class="mt-1 text-sm text-gray-500">Manage courses across all faculties</p>
        </div>
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('courses.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Course
            </a>
        </div>
        @endif
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
            <form method="GET" action="{{ route('courses.index') }}" class="space-y-4">
                <!-- First Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search courses..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Faculty Filter -->
                    <div>
                        <select name="faculty_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All Faculties</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Organization Type Filter -->
                    <div>
                        <select name="organization_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All Organization Types</option>
                            @foreach($organizationTypes as $type)
                                <option value="{{ $type }}" {{ request('organization_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }} Based
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Second Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Year Filter (for yearly organization) -->
                    <div>
                        <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All Years</option>
                            @foreach($yearlyOptions as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}{{ $year == 1 ? 'st' : ($year == 2 ? 'nd' : ($year == 3 ? 'rd' : 'th')) }} Year
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester Period Filter (for semester organization) -->
                    <div>
                        <select name="semester_period" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All Semester Periods</option>
                            @foreach($semesterOptions as $sem)
                                <option value="{{ $sem }}" {{ request('semester_period') == $sem ? 'selected' : '' }}>
                                    Semester {{ $sem }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Course Type Filter -->
                    <div>
                        <select name="course_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All Course Types</option>
                            @foreach($courseTypes as $type)
                                <option value="{{ $type }}" {{ request('course_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }} Course
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
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Courses</h3>
        </div>

        @if($courses->count() > 0)
            <div class="overflow-x-auto compact-table">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Faculty
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Department
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Details
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Classes
                            </th>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="relative px-3 py-2">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($courses as $course)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div>
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ Str::limit($course->title, 30) }}
                                            </div>
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $course->code }}
                                            </span>
                                        </div>
                                        @if($course->description)
                                            <div class="text-xs text-gray-500 truncate max-w-xs">
                                                {{ Str::limit($course->description, 40) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @if($course->faculty)
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-6 w-6">
                                                <div class="h-6 w-6 rounded-full bg-primary-100 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-primary-700">
                                                        {{ $course->faculty->code }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ Str::limit($course->faculty->name, 20) }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-6 w-6">
                                                <div class="h-6 w-6 rounded-full bg-gray-100 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-500">
                                                        N/A
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-2">
                                                <div class="text-sm font-medium text-gray-500">
                                                    No Faculty
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                    @if($course->department)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $course->department->code }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($course->department->name, 15) }}</div>
                                    @else
                                        <span class="text-gray-400 italic text-xs">No department</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">
                                    <div class="space-y-1">
                                        <div class="flex items-center">
                                            <i class="fas fa-cog text-gray-400 mr-1 w-3"></i>
                                            <span class="capitalize">{{ $course->organization_type ?? 'yearly' }}</span>
                                        </div>
                                        @if(($course->organization_type ?? 'yearly') === 'yearly')
                                            @if($course->year)
                                                <div class="flex items-center">
                                                    <i class="fas fa-graduation-cap text-gray-400 mr-1 w-3"></i>
                                                    <span>{{ $course->year }}{{ $course->year == 1 ? 'st' : ($course->year == 2 ? 'nd' : ($course->year == 3 ? 'rd' : 'th')) }} Yr</span>
                                                </div>
                                            @endif
                                        @else
                                            @if($course->semester_period)
                                                <div class="flex items-center">
                                                    <i class="fas fa-calendar text-gray-400 mr-1 w-3"></i>
                                                    <span>Sem {{ $course->semester_period }}</span>
                                                </div>
                                            @endif
                                        @endif
                                        <div class="flex items-center">
                                            <i class="fas fa-star text-gray-400 mr-1 w-3"></i>
                                            <span>{{ $course->credit_units }} units</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-tag text-gray-400 mr-1 w-3"></i>
                                            <span class="capitalize">{{ $course->course_type }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $course->classes_count }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @if($course->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-1">
                                        <a href="{{ route('courses.show', $course) }}"
                                           class="text-primary-600 hover:text-primary-900 transition-colors duration-200 p-1"
                                           title="View Details">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                        <a href="{{ route('courses.edit', $course) }}"
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200 p-1"
                                           title="Edit Course">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('courses.destroy', $course) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200 p-1"
                                                    title="Delete Course">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($courses->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $courses->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-book text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No courses found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                        Get started by creating a new course.
                    @else
                        No courses are available to view.
                    @endif
                </p>
                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                <div class="mt-6">
                    <a href="{{ route('courses.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Add Course
                    </a>
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
