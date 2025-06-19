@extends('layouts.dashboard')

@section('title', 'Fee Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Fee Management</h1>
            <p class="text-gray-600 mt-2">Manage student fees and fee structures</p>
        </div>
        @can('manage-fees')
            <a href="{{ route('finance.fees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Fee
            </a>
        @endcan
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('finance.fees.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Search fees..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Academic Year -->
            <div>
                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                <select name="academic_year_id" id="academic_year_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Years</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fee Type -->
            <div>
                <label for="fee_type" class="block text-sm font-medium text-gray-700 mb-1">Fee Type</label>
                <select name="fee_type" id="fee_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="tuition" {{ request('fee_type') == 'tuition' ? 'selected' : '' }}>Tuition Fee</option>
                    <option value="library" {{ request('fee_type') == 'library' ? 'selected' : '' }}>Library Fee</option>
                    <option value="laboratory" {{ request('fee_type') == 'laboratory' ? 'selected' : '' }}>Laboratory Fee</option>
                    <option value="sports" {{ request('fee_type') == 'sports' ? 'selected' : '' }}>Sports Fee</option>
                    <option value="medical" {{ request('fee_type') == 'medical' ? 'selected' : '' }}>Medical Fee</option>
                    <option value="accommodation" {{ request('fee_type') == 'accommodation' ? 'selected' : '' }}>Accommodation Fee</option>
                    <option value="registration" {{ request('fee_type') == 'registration' ? 'selected' : '' }}>Registration Fee</option>
                    <option value="examination" {{ request('fee_type') == 'examination' ? 'selected' : '' }}>Examination Fee</option>
                    <option value="other" {{ request('fee_type') == 'other' ? 'selected' : '' }}>Other Fee</option>
                </select>
            </div>



            <!-- Filter Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium transition duration-200">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Fees Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type & Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($fees as $fee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $fee->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $fee->code }}</div>
                                    @if($fee->description)
                                        <div class="text-xs text-gray-400 mt-1">{{ Str::limit($fee->description, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $fee->fee_type_display }}</div>
                                    <div class="text-sm font-semibold text-green-600">NRs {{ number_format($fee->amount, 2) }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm text-gray-900">{{ $fee->academicYear->name }}</div>
                                    @if($fee->course)
                                        <div class="text-sm text-gray-500">{{ $fee->course->title }} ({{ $fee->course->code }})</div>
                                    @endif
                                    @if($fee->department)
                                        <div class="text-xs text-gray-400">{{ $fee->department->name }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-1">
                                    @if($fee->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                    @if($fee->is_mandatory)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Mandatory
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <!-- View Button -->
                                    <a href="{{ route('finance.fees.show', $fee) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-full transition-colors duration-200"
                                       title="View Fee">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    @can('manage-fees')
                                        <!-- Edit Button -->
                                        <a href="{{ route('finance.fees.edit', $fee) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-full transition-colors duration-200"
                                           title="Edit Fee">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>

                                        <!-- Delete Button -->
                                        <form action="{{ route('finance.fees.destroy', $fee) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-full transition-colors duration-200"
                                                    title="Delete Fee"
                                                    onclick="return confirm('Are you sure you want to delete this fee?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No fees found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($fees->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $fees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
