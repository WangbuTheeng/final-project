@extends('layouts.dashboard')

@section('title', 'Fee Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Fee Details</h1>
            <p class="text-gray-600 mt-2">View fee information and details</p>
        </div>
        <div class="flex space-x-3">
            @can('manage-fees')
                <a href="{{ route('finance.fees.edit', $fee) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Fee
                </a>
            @endcan
            <a href="{{ route('finance.fees.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Fees
            </a>
        </div>
    </div>

    <!-- Fee Information Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Fee Name</label>
                        <p class="text-gray-900 font-medium">{{ $fee->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Fee Code</label>
                        <p class="text-gray-900 font-mono">{{ $fee->code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Fee Type</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($fee->fee_type == 'tuition') bg-blue-100 text-blue-800
                            @elseif($fee->fee_type == 'library') bg-green-100 text-green-800
                            @elseif($fee->fee_type == 'laboratory') bg-purple-100 text-purple-800
                            @elseif($fee->fee_type == 'sports') bg-yellow-100 text-yellow-800
                            @elseif($fee->fee_type == 'medical') bg-red-100 text-red-800
                            @elseif($fee->fee_type == 'accommodation') bg-indigo-100 text-indigo-800
                            @elseif($fee->fee_type == 'registration') bg-pink-100 text-pink-800
                            @elseif($fee->fee_type == 'examination') bg-orange-100 text-orange-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $fee->fee_type)) }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Amount</label>
                        <p class="text-2xl font-bold text-green-600">NRs {{ number_format($fee->amount, 2) }}</p>
                    </div>
                    @if($fee->description)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Description</label>
                        <p class="text-gray-900">{{ $fee->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Academic Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Academic Year</label>
                        <p class="text-gray-900">{{ $fee->academicYear->name ?? 'N/A' }}</p>
                    </div>
                    @if($fee->course)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Course</label>
                        <p class="text-gray-900">{{ $fee->course->title }} ({{ $fee->course->code }})</p>
                        <p class="text-sm text-gray-500">{{ $fee->course->faculty->name }}</p>
                    </div>
                    @endif
                    @if($fee->department)
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Department</label>
                        <p class="text-gray-900">{{ $fee->department->name }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status and Settings -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status & Settings</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600">Status</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($fee->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                    {{ $fee->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Mandatory</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($fee->is_mandatory) bg-orange-100 text-orange-800 @else bg-blue-100 text-blue-800 @endif">
                    {{ $fee->is_mandatory ? 'Yes' : 'No' }}
                </span>
            </div>
            @if($fee->due_date)
            <div>
                <label class="block text-sm font-medium text-gray-600">Due Date</label>
                <p class="text-gray-900">{{ $fee->due_date->format('M d, Y') }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Timestamps -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Record Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600">Created At</label>
                <p class="text-gray-900">{{ $fee->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Last Updated</label>
                <p class="text-gray-900">{{ $fee->updated_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
