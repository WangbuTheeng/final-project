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
            <h1 class="text-2xl font-bold text-gray-900">Faculties</h1>
            <p class="mt-1 text-sm text-gray-500">Manage all faculties in the institution</p>
        </div>
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('faculties.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Add Faculty
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

    <!-- Faculties Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Faculties</h3>
        </div>

        @if($faculties->count() > 0)
            <!-- Table View (Desktop/Tablet) -->
            <div class="compact-table table-view">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/5">
                                Faculty Info
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">
                                Dean & Departments
                            </th>
                            <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                Contact
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
                        @foreach($faculties as $faculty)
                            <tr class="hover:bg-gray-50">
                                <!-- Faculty Info Column -->
                                <td class="px-2 py-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 leading-tight">
                                            {{ Str::limit($faculty->name, 40) }}
                                        </div>
                                        <div class="text-xs text-blue-600 font-mono mt-1">
                                            {{ $faculty->code }}
                                        </div>
                                        @if($faculty->description)
                                            <div class="text-xs text-gray-500 mt-1 leading-tight">
                                                {{ Str::limit($faculty->description, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Dean & Departments Column -->
                                <td class="px-2 py-3">
                                    <div class="space-y-2">
                                        @if($faculty->dean)
                                            <div class="flex items-center space-x-2">
                                                <div class="flex-shrink-0 h-5 w-5 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-700">
                                                        {{ substr($faculty->dean->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div class="text-xs text-gray-900 leading-tight">
                                                    {{ Str::limit($faculty->dean->name, 20) }}
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-xs text-gray-400 italic">No dean</div>
                                        @endif
                                        <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-building text-xs mr-1"></i>
                                            {{ $faculty->departments_count }} dept{{ $faculty->departments_count != 1 ? 's' : '' }}
                                        </div>
                                    </div>
                                </td>

                                <!-- Contact Column -->
                                <td class="px-2 py-3">
                                    <div class="space-y-1 text-xs">
                                        @if($faculty->email)
                                            <div class="flex items-center">
                                                <i class="fas fa-envelope text-gray-400 mr-1 w-3"></i>
                                                <span class="text-gray-600 truncate">{{ Str::limit($faculty->email, 20) }}</span>
                                            </div>
                                        @endif
                                        @if($faculty->phone)
                                            <div class="flex items-center">
                                                <i class="fas fa-phone text-gray-400 mr-1 w-3"></i>
                                                <span class="text-gray-600">{{ $faculty->phone }}</span>
                                            </div>
                                        @endif
                                        @if(!$faculty->email && !$faculty->phone)
                                            <span class="text-gray-400 italic">No contact</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status Column -->
                                <td class="px-2 py-3 text-center">
                                    @if($faculty->is_active)
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
                                        <a href="{{ route('faculties.show', $faculty) }}"
                                           class="inline-flex items-center justify-center w-6 h-6 text-primary-600 hover:text-primary-900 hover:bg-primary-50 rounded transition-colors duration-200"
                                           title="View Details">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                        <a href="{{ route('faculties.edit', $faculty) }}"
                                           class="inline-flex items-center justify-center w-6 h-6 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 rounded transition-colors duration-200"
                                           title="Edit Faculty">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('faculties.destroy', $faculty) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Are you sure you want to delete this faculty? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-6 h-6 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition-colors duration-200"
                                                    title="Delete Faculty">
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
                @foreach($faculties as $faculty)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 leading-tight">
                                    {{ $faculty->name }}
                                </h4>
                                <p class="text-xs text-blue-600 font-mono mt-1">{{ $faculty->code }}</p>
                                @if($faculty->description)
                                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($faculty->description, 60) }}</p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-1 ml-2">
                                @if($faculty->is_active)
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
                                <span class="text-gray-500">Dean:</span>
                                <div class="text-gray-900">{{ $faculty->dean ? Str::limit($faculty->dean->name, 20) : 'Not assigned' }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500">Departments:</span>
                                <div class="text-gray-700">{{ $faculty->departments_count }} department{{ $faculty->departments_count != 1 ? 's' : '' }}</div>
                            </div>
                            @if($faculty->email)
                                <div>
                                    <span class="text-gray-500">Email:</span>
                                    <div class="text-blue-600">{{ Str::limit($faculty->email, 25) }}</div>
                                </div>
                            @endif
                            @if($faculty->phone)
                                <div>
                                    <span class="text-gray-500">Phone:</span>
                                    <div class="text-gray-700">{{ $faculty->phone }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-end space-x-1">
                            <a href="{{ route('faculties.show', $faculty) }}" class="p-1 text-primary-600 hover:bg-primary-50 rounded">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                            <a href="{{ route('faculties.edit', $faculty) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="{{ route('faculties.destroy', $faculty) }}"
                                  method="POST"
                                  class="inline-block"
                                  onsubmit="return confirm('Are you sure you want to delete this faculty?')">
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
            @if($faculties->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $faculties->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-university text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No faculties found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                        Get started by creating a new faculty.
                    @else
                        No faculties are available to view.
                    @endif
                </p>
                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                <div class="mt-6">
                    <a href="{{ route('faculties.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Add Faculty
                    </a>
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
