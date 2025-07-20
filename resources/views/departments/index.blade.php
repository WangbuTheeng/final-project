@extends('layouts.dashboard')

@section('content')
<style>
    /* Responsive table/card view */
    .table-view { display: block; }
    .card-view { display: none; }

    @media (max-width: 1024px) {
        .table-view { display: none; }
        .card-view { display: block; }
    }

    .compact-table table {
        font-size: 0.875rem;
    }

    .compact-table th,
    .compact-table td {
        padding: 0.5rem;
        vertical-align: top;
    }
</style>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Departments</h1>
            <p class="mt-1 text-sm text-gray-500">Manage all departments across faculties</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('departments.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Department
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

    <!-- Departments Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Departments</h3>
        </div>

        @if($departments->count() > 0)
            <!-- Table View (Desktop/Tablet) -->
            <div class="compact-table table-view">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/5">
                                Department & Faculty
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                HOD & Program
                            </th>
                            <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                Statistics
                            </th>
                            <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                Status
                            </th>
                            <th scope="col" class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($departments as $department)
                            <tr class="hover:bg-gray-50">
                                <!-- Department & Faculty Column -->
                                <td class="px-2 py-3">
                                    <div>
                                        <div class="flex items-center mb-1">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $department->name }}
                                            </div>
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $department->code }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-university text-gray-400 mr-1"></i>
                                                <span>{{ $department->faculty->name }}</span>
                                            </div>
                                        </div>
                                        @if($department->location)
                                            <div class="text-xs text-gray-400 flex items-center mt-1">
                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                {{ $department->location }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- HOD & Program Column -->
                                <td class="px-2 py-3">
                                    <div class="space-y-1">
                                        @if($department->hod)
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-6 w-6">
                                                    <div class="h-6 w-6 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-gray-700">
                                                            {{ substr($department->hod->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-2">
                                                    <div class="text-xs font-medium text-gray-900">
                                                        {{ Str::limit($department->hod->name, 20) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">No HOD assigned</span>
                                        @endif
                                        <div class="text-xs text-gray-600">
                                            <span class="capitalize">{{ $department->degree_type }}</span> • {{ $department->duration_years }}yr
                                        </div>
                                    </div>
                                </td>

                                <!-- Statistics Column -->
                                <td class="px-2 py-3 text-center">
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-center text-xs text-gray-600">
                                            <i class="fas fa-book text-blue-500 mr-1"></i>
                                            <span class="font-medium">{{ $department->courses_count }}</span>
                                            <span class="ml-1">courses</span>
                                        </div>
                                        <div class="flex items-center justify-center text-xs text-gray-600">
                                            <i class="fas fa-users text-green-500 mr-1"></i>
                                            <span class="font-medium">{{ $department->students_count }}</span>
                                            <span class="ml-1">students</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status Column -->
                                <td class="px-2 py-3 text-center">
                                    @if($department->is_active)
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
                                <td class="px-2 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <a href="{{ route('departments.show', $department) }}"
                                           class="text-primary-600 hover:text-primary-900 transition-colors duration-200 p-1"
                                           title="View Details">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('departments.edit', $department) }}"
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200 p-1"
                                           title="Edit Department">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('departments.destroy', $department) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Are you sure you want to delete this department?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200 p-1"
                                                    title="Delete Department">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Card View (Mobile) -->
            <div class="card-view space-y-4 p-4">
                @foreach($departments as $department)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center mb-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $department->name }}</h4>
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $department->code }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">{{ $department->faculty->name }}</p>
                            </div>
                            <div class="ml-2">
                                @if($department->is_active)
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-green-100 text-green-800" title="Active">
                                        <i class="fas fa-check text-xs"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-100 text-red-800" title="Inactive">
                                        <i class="fas fa-times text-xs"></i>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs mb-3">
                            <div>
                                <span class="text-gray-500">HOD:</span>
                                <div class="text-gray-900">
                                    {{ $department->hod ? $department->hod->name : 'Not assigned' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500">Program:</span>
                                <div class="text-gray-900">{{ ucfirst($department->degree_type) }} ({{ $department->duration_years }}yr)</div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div class="text-center">
                                    <div class="flex items-center justify-center mb-1">
                                        <i class="fas fa-book text-blue-500 mr-1"></i>
                                    </div>
                                    <div class="font-medium text-gray-900">{{ $department->courses_count }}</div>
                                    <div class="text-gray-500">Courses</div>
                                </div>
                                <div class="text-center">
                                    <div class="flex items-center justify-center mb-1">
                                        <i class="fas fa-users text-green-500 mr-1"></i>
                                    </div>
                                    <div class="font-medium text-gray-900">{{ $department->students_count }}</div>
                                    <div class="text-gray-500">Students</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('departments.show', $department) }}"
                               class="text-primary-600 hover:text-primary-900 transition-colors duration-200 p-2"
                               title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('departments.edit', $department) }}"
                               class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200 p-2"
                               title="Edit Department">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('departments.destroy', $department) }}"
                                  method="POST"
                                  class="inline-block"
                                  onsubmit="return confirm('Are you sure you want to delete this department?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2"
                                        title="Delete Department">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($departments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $departments->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-building text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No departments found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new department.</p>
                <div class="mt-6">
                    <a href="{{ route('departments.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Add Department
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
