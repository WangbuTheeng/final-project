@extends('layouts.dashboard')

@section('content')
<style>
.compact-table {
    max-width: 100%;
    overflow: visible;
}

.compact-table table {
    table-layout: fixed;
    width: 100%;
}

.compact-table td {
    word-wrap: break-word;
    overflow-wrap: break-word;
    vertical-align: top;
}

/* Mobile card view */
@media (max-width: 640px) {
    .table-view {
        display: none;
    }
    .card-view {
        display: block;
    }
}

@media (min-width: 641px) {
    .table-view {
        display: block;
    }
    .card-view {
        display: none;
    }
}
</style>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Class Sections</h1>
            <p class="mt-1 text-sm text-gray-500">Manage class sections and their schedules</p>
        </div>
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('classes.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Class Section
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
            <form method="GET" action="{{ route('classes.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search classes..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Academic Year Filter -->
                <div>
                    <select name="academic_year_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Academic Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Semester Filter -->
                <div>
                    <select name="semester" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Semesters</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester }}" {{ request('semester') == $semester ? 'selected' : '' }}>
                                Semester {{ $semester }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
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

    <!-- Classes Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Class Sections</h3>
        </div>

        @if($classes->count() > 0)
            <!-- Table View (Desktop/Tablet) -->
            <div class="compact-table table-view">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">
                                Class & Course
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                Instructor & Details
                            </th>
                            <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                Enrollment
                            </th>
                            <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                Status
                            </th>
                            <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                Actions
                            </th>
                        </tr>
                    </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($classes as $class)
                        <tr class="hover:bg-gray-50">
                            <!-- Class & Course Column -->
                            <td class="px-2 py-3">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 leading-tight">
                                        {{ Str::limit($class->name, 30) }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $class->academicYear->name }} - Sem {{ $class->semester }}
                                    </div>
                                    @if($class->room)
                                        <div class="text-xs text-gray-400 mt-1">
                                            <i class="fas fa-map-marker-alt mr-1 w-3"></i>{{ $class->room }}
                                        </div>
                                    @endif
                                    <div class="mt-2 pt-2 border-t border-gray-100">
                                        <div class="text-sm font-medium text-blue-900 leading-tight">
                                            {{ Str::limit($class->course->title, 35) }}
                                        </div>
                                        <div class="text-xs text-blue-600 mt-1">
                                            {{ $class->course->code }} • {{ $class->course->credit_units }} units
                                        </div>
                                        @if($class->course->department)
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ Str::limit($class->course->department->name, 25) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Instructor & Details Column -->
                            <td class="px-2 py-3">
                                <div class="space-y-2">
                                    @if($class->instructor)
                                        <div class="flex items-center space-x-2">
                                            <div class="flex-shrink-0 h-5 w-5 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-600">
                                                    {{ substr($class->instructor->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-xs font-medium text-gray-900 leading-tight">
                                                    {{ Str::limit($class->instructor->name, 20) }}
                                                </div>
                                                <div class="text-xs text-gray-500 truncate">
                                                    {{ Str::limit($class->instructor->email, 25) }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-400 italic">No instructor</div>
                                    @endif

                                    <div class="space-y-1 text-xs">
                                        <div class="flex items-center">
                                            <i class="fas fa-users text-gray-400 mr-1 w-3"></i>
                                            <span class="text-gray-600">Cap: {{ $class->capacity }}</span>
                                        </div>
                                        @if($class->start_date && $class->end_date)
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar text-gray-400 mr-1 w-3"></i>
                                                <span class="text-gray-600">{{ $class->start_date->format('M d') }} - {{ $class->end_date->format('M d') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <!-- Enrollment Column -->
                            <td class="px-2 py-3 text-center">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $class->enrollments_count }}/{{ $class->capacity }}
                                    </span>
                                    @if($class->capacity > 0)
                                        <div class="w-12 bg-gray-200 rounded-full h-1.5 mx-auto">
                                            <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, ($class->enrollments_count / $class->capacity) * 100) }}%"></div>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ round(($class->enrollments_count / $class->capacity) * 100) }}%
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Status Column -->
                            <td class="px-2 py-3 text-center">
                                @if($class->status === 'active')
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-800" title="Active">
                                        <i class="fas fa-check text-xs"></i>
                                    </span>
                                @elseif($class->status === 'completed')
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-800" title="Completed">
                                        <i class="fas fa-flag text-xs"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-800" title="Cancelled">
                                        <i class="fas fa-times text-xs"></i>
                                    </span>
                                @endif
                            </td>

                            <!-- Actions Column -->
                            <td class="px-2 py-3">
                                <div class="flex items-center justify-center space-x-1">
                                    <a href="{{ route('classes.show', $class) }}"
                                       class="inline-flex items-center justify-center w-6 h-6 text-primary-600 hover:text-primary-900 hover:bg-primary-50 rounded transition-colors duration-200"
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                    <a href="{{ route('classes.edit', $class) }}"
                                       class="inline-flex items-center justify-center w-6 h-6 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 rounded transition-colors duration-200"
                                       title="Edit Class">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('classes.destroy', $class) }}"
                                          method="POST"
                                          class="inline-block"
                                          onsubmit="return confirm('Are you sure you want to delete this class section? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-6 h-6 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition-colors duration-200"
                                                title="Delete Class">
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

        <!-- Card View (Mobile) -->
        <div class="card-view space-y-4 p-4">
            @foreach($classes as $class)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900 leading-tight">
                                {{ $class->name }}
                            </h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $class->academicYear->name }} - Semester {{ $class->semester }}</p>
                            @if($class->room)
                                <p class="text-xs text-gray-400 mt-1">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $class->room }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2 ml-2">
                            @if($class->status === 'active')
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100 text-green-800" title="Active">
                                    <i class="fas fa-check text-xs"></i>
                                </span>
                            @elseif($class->status === 'completed')
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-800" title="Completed">
                                    <i class="fas fa-flag text-xs"></i>
                                </span>
                            @else
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100 text-red-800" title="Cancelled">
                                    <i class="fas fa-times text-xs"></i>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3 p-2 bg-blue-50 rounded">
                        <div class="text-sm font-medium text-blue-900">{{ $class->course->title }}</div>
                        <div class="text-xs text-blue-600">{{ $class->course->code }} • {{ $class->course->credit_units }} units</div>
                        @if($class->course->department)
                            <div class="text-xs text-blue-500 mt-1">{{ $class->course->department->name }}</div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                        <div>
                            <span class="text-gray-500">Instructor:</span>
                            <div class="text-gray-900">{{ $class->instructor ? Str::limit($class->instructor->name, 20) : 'Not assigned' }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500">Capacity:</span>
                            <div class="text-gray-700">{{ $class->capacity }} students</div>
                        </div>
                        <div>
                            <span class="text-gray-500">Enrolled:</span>
                            <div class="text-blue-600">{{ $class->enrollments_count }}/{{ $class->capacity }} ({{ $class->capacity > 0 ? round(($class->enrollments_count / $class->capacity) * 100) : 0 }}%)</div>
                        </div>
                        @if($class->start_date && $class->end_date)
                            <div>
                                <span class="text-gray-500">Duration:</span>
                                <div class="text-gray-700">{{ $class->start_date->format('M d') }} - {{ $class->end_date->format('M d, Y') }}</div>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end space-x-1">
                        <a href="{{ route('classes.show', $class) }}" class="p-1 text-primary-600 hover:bg-primary-50 rounded">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                        <a href="{{ route('classes.edit', $class) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <form action="{{ route('classes.destroy', $class) }}"
                              method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Are you sure you want to delete this class section?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 text-red-600 hover:bg-red-50 rounded">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

            <!-- Pagination -->
            @if($classes->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $classes->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-chalkboard-teacher text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No class sections found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                        Get started by creating a new class section.
                    @else
                        No class sections are available to view.
                    @endif
                </p>
                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                <div class="mt-6">
                    <a href="{{ route('classes.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Add Class Section
                    </a>
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
