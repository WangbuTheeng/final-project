@extends('layouts.dashboard')

@section('title', 'Academic Years')

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
            <h1 class="text-2xl font-bold text-gray-900">Academic Years</h1>
            <p class="mt-1 text-sm text-gray-500">Manage academic year periods and settings</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('academic-years.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Academic Year
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Academic Years Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Academic Years List</h3>
        </div>
        <!-- Table View (Desktop/Tablet) -->
        <div class="compact-table table-view">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">
                            Academic Year
                        </th>
                        <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">
                            Duration
                        </th>
                        <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">
                            Classes & Subjects
                        </th>
                        <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                            Status
                        </th>
                        <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                            Current
                        </th>
                        <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($academicYears as $year)
                        <tr class="hover:bg-gray-50">
                            <!-- Academic Year Column -->
                            <td class="px-2 py-3">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 leading-tight">
                                        {{ $year->name }}
                                    </div>
                                    <div class="text-xs text-blue-600 font-mono mt-1">
                                        {{ $year->code }}
                                    </div>
                                </div>
                            </td>

                            <!-- Duration Column -->
                            <td class="px-2 py-3">
                                <div class="text-xs text-gray-600">
                                    <div class="flex items-center mb-1">
                                        <i class="fas fa-play text-green-500 mr-1 w-3"></i>
                                        <span>{{ $year->start_date ? $year->start_date->format('M d, Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-stop text-red-500 mr-1 w-3"></i>
                                        <span>{{ $year->end_date ? $year->end_date->format('M d, Y') : 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Classes & Subjects Column -->
                            <td class="px-2 py-3 text-center">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-center text-xs text-gray-600">
                                        <i class="fas fa-chalkboard-teacher text-blue-500 mr-1"></i>
                                        <span class="font-medium">{{ $year->classes_count ?? 0 }}</span>
                                        <span class="ml-1">Classes</span>
                                    </div>
                                    <div class="flex items-center justify-center text-xs text-gray-600">
                                        <i class="fas fa-book text-purple-500 mr-1"></i>
                                        <span class="font-medium">{{ $year->subjects_count ?? 0 }}</span>
                                        <span class="ml-1">Subjects</span>
                                    </div>
                                    @if($year->courses_count > 0)
                                    <div class="flex items-center justify-center text-xs text-gray-600">
                                        <i class="fas fa-graduation-cap text-green-500 mr-1"></i>
                                        <span class="font-medium">{{ $year->courses_count }}</span>
                                        <span class="ml-1">Courses</span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <!-- Status Column -->
                            <td class="px-2 py-3 text-center">
                                @if($year->is_active)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-800" title="Active">
                                        <i class="fas fa-check text-xs"></i>
                                    </span>
                                @else
                                    <div class="flex flex-col items-center space-y-1">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-800" title="Inactive">
                                            <i class="fas fa-times text-xs"></i>
                                        </span>
                                        <form method="POST" action="{{ route('academic-years.set-active', $year) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                    class="inline-flex items-center px-1 py-0.5 border border-green-300 text-xs font-medium rounded text-green-700 bg-white hover:bg-green-50"
                                                    onclick="return confirm('Set this as the active academic year? This will deactivate all other academic years.')"
                                                    title="Set Active">
                                                <i class="fas fa-play text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>

                            <!-- Current Column -->
                            <td class="px-2 py-3 text-center">
                                @if($year->is_current)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary-100 text-primary-800" title="Current">
                                        <i class="fas fa-star text-xs"></i>
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('academic-years.set-current', $year) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                                class="inline-flex items-center px-1 py-0.5 border border-primary-300 text-xs font-medium rounded text-primary-700 bg-white hover:bg-primary-50"
                                                onclick="return confirm('Set this as the current academic year?')"
                                                title="Set Current">
                                            <i class="fas fa-star text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <!-- Actions Column -->
                            <td class="px-2 py-3">
                                <div class="flex items-center justify-center space-x-1">
                                    <a href="{{ route('academic-years.show', $year) }}"
                                       class="inline-flex items-center justify-center w-6 h-6 text-primary-600 hover:text-primary-900 hover:bg-primary-50 rounded transition-colors duration-200"
                                       title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('academic-years.edit', $year) }}"
                                       class="inline-flex items-center justify-center w-6 h-6 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 rounded transition-colors duration-200"
                                       title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    @if(!$year->is_current)
                                        <form method="POST" action="{{ route('academic-years.destroy', $year) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-6 h-6 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition-colors duration-200"
                                                    onclick="return confirm('Are you sure you want to delete this academic year?')"
                                                    title="Delete">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-calendar-alt text-4xl mb-4 text-gray-300"></i>
                                    <p class="text-lg font-medium">No academic years found</p>
                                    <p class="text-sm">Get started by creating your first academic year.</p>
                                    <a href="{{ route('academic-years.create') }}"
                                       class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create Academic Year
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Card View (Mobile) -->
        <div class="card-view space-y-4 p-4">
            @forelse($academicYears as $year)
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900 leading-tight">
                                {{ $year->name }}
                            </h4>
                            <p class="text-xs text-blue-600 font-mono mt-1">{{ $year->code }}</p>
                        </div>
                        <div class="flex items-center space-x-2 ml-2">
                            @if($year->is_active)
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100 text-green-800" title="Active">
                                    <i class="fas fa-check text-xs"></i>
                                </span>
                            @endif
                            @if($year->is_current)
                                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary-100 text-primary-800" title="Current">
                                    <i class="fas fa-star text-xs"></i>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                        <div>
                            <span class="text-gray-500">Start Date:</span>
                            <div class="text-gray-900">{{ $year->start_date ? $year->start_date->format('M d, Y') : 'N/A' }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500">End Date:</span>
                            <div class="text-gray-900">{{ $year->end_date ? $year->end_date->format('M d, Y') : 'N/A' }}</div>
                        </div>
                    </div>

                    <!-- Classes & Subjects Info -->
                    <div class="bg-gray-50 rounded-lg p-3 mb-3">
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div class="text-center">
                                <div class="flex items-center justify-center mb-1">
                                    <i class="fas fa-chalkboard-teacher text-blue-500 mr-1"></i>
                                </div>
                                <div class="font-medium text-gray-900">{{ $year->classes_count ?? 0 }}</div>
                                <div class="text-gray-500">Classes</div>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center mb-1">
                                    <i class="fas fa-book text-purple-500 mr-1"></i>
                                </div>
                                <div class="font-medium text-gray-900">{{ $year->subjects_count ?? 0 }}</div>
                                <div class="text-gray-500">Subjects</div>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center mb-1">
                                    <i class="fas fa-graduation-cap text-green-500 mr-1"></i>
                                </div>
                                <div class="font-medium text-gray-900">{{ $year->courses_count ?? 0 }}</div>
                                <div class="text-gray-500">Courses</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            @if(!$year->is_active)
                                <form method="POST" action="{{ route('academic-years.set-active', $year) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="inline-flex items-center px-2 py-1 border border-green-300 text-xs font-medium rounded text-green-700 bg-white hover:bg-green-50"
                                            onclick="return confirm('Set this as the active academic year?')">
                                        Set Active
                                    </button>
                                </form>
                            @endif
                            @if(!$year->is_current)
                                <form method="POST" action="{{ route('academic-years.set-current', $year) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="inline-flex items-center px-2 py-1 border border-primary-300 text-xs font-medium rounded text-primary-700 bg-white hover:bg-primary-50"
                                            onclick="return confirm('Set this as the current academic year?')">
                                        Set Current
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('academic-years.show', $year) }}" class="p-1 text-primary-600 hover:bg-primary-50 rounded">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('academic-years.edit', $year) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            @if(!$year->is_current)
                                <form method="POST" action="{{ route('academic-years.destroy', $year) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1 text-red-600 hover:bg-red-50 rounded"
                                            onclick="return confirm('Are you sure you want to delete this academic year?')">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <i class="fas fa-calendar-alt text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium text-gray-500">No academic years found</p>
                    <p class="text-sm text-gray-400">Get started by creating your first academic year.</p>
                    <a href="{{ route('academic-years.create') }}"
                       class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700">
                        <i class="fas fa-plus mr-2"></i>
                        Create Academic Year
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
