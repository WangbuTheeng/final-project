@extends('layouts.dashboard')

@section('content')
<style>
.responsive-table {
    max-width: 100%;
    overflow: visible;
}

.responsive-table table {
    table-layout: fixed;
    width: 100%;
}

.responsive-table td {
    word-wrap: break-word;
    overflow-wrap: break-word;
    vertical-align: top;
}

/* Ensure text doesn't overflow */
.text-truncate-custom {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Mobile card view for very small screens */
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
            <h1 class="text-2xl font-bold text-gray-900">Subjects</h1>
            <p class="mt-1 text-sm text-gray-500">Manage subjects/topics within classes</p>
        </div>
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('subjects.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Subject
            </a>
        </div>
        @endif
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
            <!-- Table View (Desktop/Tablet) -->
            <div class="responsive-table table-view">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/5">
                                Subject Info
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                Class & Course
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                Details
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
                        @foreach($subjects as $subject)
                            <tr class="hover:bg-gray-50">
                                <!-- Subject Info Column -->
                                <td class="px-2 py-3">
                                    <div class="flex items-start space-x-2">
                                        <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-primary-100 text-primary-800 text-xs font-medium flex-shrink-0 mt-0.5">
                                            {{ $subject->order_sequence }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 leading-tight">
                                                {{ Str::limit($subject->name, 35) }}
                                            </div>
                                            <div class="text-xs text-gray-500 font-mono">
                                                {{ $subject->code }}
                                            </div>
                                            @if($subject->description)
                                                <div class="text-xs text-gray-400 mt-1 leading-tight">
                                                    {{ Str::limit($subject->description, 40) }}
                                                </div>
                                            @endif
                                            @if($subject->instructor)
                                                <div class="text-xs text-blue-600 mt-1">
                                                    <i class="fas fa-user text-xs mr-1"></i>{{ Str::limit($subject->instructor->name, 20) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <!-- Class & Course Column -->
                                <td class="px-2 py-3">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 leading-tight">
                                            {{ Str::limit($subject->class->name, 20) }}
                                        </div>
                                        <div class="text-gray-600 text-xs leading-tight">
                                            {{ Str::limit($subject->class->course->title, 25) }}
                                        </div>
                                        <div class="text-gray-400 text-xs leading-tight">
                                            {{ Str::limit($subject->class->course->faculty->name ?? 'No Faculty', 20) }}
                                        </div>
                                    </div>
                                </td>
                                <!-- Details Column -->
                                <td class="px-2 py-3">
                                    <div class="space-y-1 text-xs">
                                        <div class="flex items-center">
                                            <i class="fas fa-signal text-gray-400 mr-1 text-xs w-3"></i>
                                            <span class="capitalize text-gray-600">{{ $subject->difficulty_level }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-tag text-gray-400 mr-1 text-xs w-3"></i>
                                            <span class="capitalize text-gray-600">{{ $subject->subject_type }}</span>
                                        </div>
                                        @if($subject->duration_hours)
                                            <div class="flex items-center">
                                                <i class="fas fa-clock text-gray-400 mr-1 text-xs w-3"></i>
                                                <span class="text-gray-600">{{ $subject->duration_hours }}h</span>
                                            </div>
                                        @endif
                                        @if($subject->credit_weight)
                                            <div class="flex items-center">
                                                <i class="fas fa-star text-gray-400 mr-1 text-xs w-3"></i>
                                                <span class="text-gray-600">{{ $subject->credit_weight }}%</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center">
                                            <i class="fas fa-{{ $subject->is_mandatory ? 'exclamation-circle' : 'circle' }} text-{{ $subject->is_mandatory ? 'red' : 'green' }}-400 mr-1 text-xs w-3"></i>
                                            <span class="text-{{ $subject->is_mandatory ? 'red' : 'green' }}-600">{{ $subject->is_mandatory ? 'Required' : 'Optional' }}</span>
                                        </div>
                                        @if($subject->total_full_marks > 0)
                                            <div class="flex items-center">
                                                <i class="fas fa-calculator text-gray-400 mr-1 text-xs w-3"></i>
                                                <span class="text-gray-600">{{ $subject->total_full_marks }}pts</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <!-- Status Column -->
                                <td class="px-2 py-3 text-center">
                                    @if($subject->is_active)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-800" title="Active">
                                            <i class="fas fa-check text-xs"></i>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-800" title="Inactive">
                                            <i class="fas fa-times text-xs"></i>
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions Column -->
                                <td class="px-2 py-3">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('subjects.show', $subject) }}"
                                           class="inline-flex items-center justify-center w-6 h-6 text-primary-600 hover:text-primary-900 hover:bg-primary-50 rounded transition-colors duration-200"
                                           title="View Details">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                        <a href="{{ route('subjects.edit', $subject) }}"
                                           class="inline-flex items-center justify-center w-6 h-6 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 rounded transition-colors duration-200"
                                           title="Edit Subject">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('subjects.destroy', $subject) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Are you sure you want to delete this subject? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-6 h-6 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition-colors duration-200"
                                                    title="Delete Subject">
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
                @foreach($subjects as $subject)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-start space-x-2 flex-1">
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-primary-100 text-primary-800 text-xs font-medium flex-shrink-0">
                                    {{ $subject->order_sequence }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 leading-tight">
                                        {{ $subject->name }}
                                    </h4>
                                    <p class="text-xs text-gray-500 font-mono mt-1">{{ $subject->code }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-1 ml-2">
                                @if($subject->is_active)
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check text-xs"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times text-xs"></i>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                            <div>
                                <span class="text-gray-500">Class:</span>
                                <div class="font-medium text-gray-900">{{ $subject->class->name }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500">Course:</span>
                                <div class="text-gray-700">{{ Str::limit($subject->class->course->title, 20) }}</div>
                            </div>
                            @if($subject->instructor)
                                <div>
                                    <span class="text-gray-500">Instructor:</span>
                                    <div class="text-blue-600">{{ Str::limit($subject->instructor->name, 20) }}</div>
                                </div>
                            @endif
                            <div>
                                <span class="text-gray-500">Type:</span>
                                <div class="text-gray-700 capitalize">{{ $subject->subject_type }}</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 text-xs">
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-{{ $subject->is_mandatory ? 'red' : 'green' }}-100 text-{{ $subject->is_mandatory ? 'red' : 'green' }}-800">
                                    {{ $subject->is_mandatory ? 'Required' : 'Optional' }}
                                </span>
                                @if($subject->duration_hours)
                                    <span class="text-gray-500">{{ $subject->duration_hours }}h</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-1">
                                <a href="{{ route('subjects.show', $subject) }}" class="p-1 text-primary-600 hover:bg-primary-50 rounded">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                <a href="{{ route('subjects.edit', $subject) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('subjects.destroy', $subject) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Are you sure you want to delete this subject?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-red-600 hover:bg-red-50 rounded">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
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
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                        Get started by creating a new subject.
                    @else
                        No subjects are available to view.
                    @endif
                </p>
                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                <div class="mt-6">
                    <a href="{{ route('subjects.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Add Subject
                    </a>
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
