@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Grading Systems</h1>
            <p class="mt-1 text-sm text-gray-500">Manage custom grading systems and grade scales</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('grading-systems.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Create Grading System
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

    <!-- Grading Systems List -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Grading Systems</h3>
            <p class="mt-1 text-sm text-gray-500">{{ $gradingSystems->total() }} grading system(s) found</p>
        </div>

        @if($gradingSystems->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                System Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grade Scales
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usage
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($gradingSystems as $gradingSystem)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $gradingSystem->name }}
                                                </div>
                                                @if($gradingSystem->is_default)
                                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-star mr-1"></i>
                                                        Default
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Code: {{ $gradingSystem->code }}
                                            </div>
                                            @if($gradingSystem->description)
                                                <div class="text-xs text-gray-400 mt-1">
                                                    {{ Str::limit($gradingSystem->description, 50) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $gradingSystem->gradeScales->count() }} grade scale(s)
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        @if($gradingSystem->gradeScales->count() > 0)
                                            {{ $gradingSystem->gradeScales->pluck('grade_letter')->join(', ') }}
                                        @else
                                            No grades defined
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $gradingSystem->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <i class="fas fa-circle mr-1 text-xs"></i>
                                        {{ ucfirst($gradingSystem->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $gradingSystem->exams->count() }} exam(s)
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('grading-systems.show', $gradingSystem) }}"
                                           class="text-primary-600 hover:text-primary-900"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('grading-systems.edit', $gradingSystem) }}"
                                           class="text-indigo-600 hover:text-indigo-900"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if(!$gradingSystem->is_default)
                                            <form action="{{ route('grading-systems.set-default', $gradingSystem) }}" 
                                                  method="POST" 
                                                  class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="text-yellow-600 hover:text-yellow-900"
                                                        title="Set as Default"
                                                        onclick="return confirm('Set this as the default grading system?')">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('grading-systems.toggle-status', $gradingSystem) }}" 
                                              method="POST" 
                                              class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="text-gray-600 hover:text-gray-900"
                                                    title="{{ $gradingSystem->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                                    @if($gradingSystem->is_default && $gradingSystem->status === 'active') disabled @endif>
                                                <i class="fas fa-{{ $gradingSystem->status === 'active' ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>

                                        @if($gradingSystem->canBeDeleted())
                                            <form action="{{ route('grading-systems.destroy', $gradingSystem) }}" 
                                                  method="POST" 
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this grading system?')">
                                                    <i class="fas fa-trash"></i>
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
            @if($gradingSystems->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $gradingSystems->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-graduation-cap text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No grading systems found</h3>
                <p class="text-gray-500 mb-4">Get started by creating your first grading system.</p>
                <a href="{{ route('grading-systems.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i>
                    Create Grading System
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
