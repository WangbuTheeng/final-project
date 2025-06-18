@extends('layouts.dashboard')

@section('title', 'Bulk Salary Processing')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bulk Salary Processing</h1>
            <p class="text-gray-600 mt-2">Process salaries for multiple teachers at once</p>
        </div>
        <a href="{{ route('finance.salaries.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Salaries
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Teachers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $teachers->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Unpaid This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $unpaidTeachers->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Amount</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($unpaidTeachers->sum('basic_salary'), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Processing Form -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Bulk Salary Processing</h2>
        </div>
        
        <form action="{{ route('finance.salaries.bulk-store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Month Selection -->
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Salary Month *</label>
                    <input type="month" 
                           id="month" 
                           name="month" 
                           value="{{ old('month', $currentMonth) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('month') border-red-500 @enderror" 
                           required>
                    @error('month')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Date -->
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">Payment Date *</label>
                    <input type="date" 
                           id="payment_date" 
                           name="payment_date" 
                           value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_date') border-red-500 @enderror" 
                           required>
                    @error('payment_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Teacher Selection -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-sm font-medium text-gray-700">Select Teachers *</label>
                    <div class="flex space-x-2">
                        <button type="button" onclick="selectAll()" class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                        <button type="button" onclick="selectNone()" class="text-sm text-blue-600 hover:text-blue-800">Select None</button>
                        <button type="button" onclick="selectUnpaid()" class="text-sm text-green-600 hover:text-green-800">Select Unpaid Only</button>
                    </div>
                </div>

                @if($teachers->count() > 0)
                    <div class="border border-gray-300 rounded-md max-h-96 overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 p-4">
                            @foreach($teachers as $teacher)
                                @php
                                    $year = (int) substr($currentMonth, 0, 4);
                                    $month = (int) substr($currentMonth, -2);
                                    [$canReceive, $message] = $teacher->canReceiveSalaryForMonth($year, $month);
                                @endphp
                                <label class="flex items-center p-3 rounded-lg border {{ $canReceive ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }} hover:bg-gray-100 cursor-pointer">
                                    <input type="checkbox" 
                                           name="teacher_ids[]" 
                                           value="{{ $teacher->id }}"
                                           class="teacher-checkbox mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           {{ $canReceive ? '' : 'disabled' }}
                                           data-unpaid="{{ $canReceive ? 'true' : 'false' }}">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900">{{ $teacher->teacher_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $teacher->employee_id }}</div>
                                        <div class="text-xs text-gray-600">{{ $teacher->department ?: 'No Department' }}</div>
                                        <div class="text-sm font-medium text-green-600">₦{{ number_format($teacher->basic_salary, 2) }}</div>
                                        @if(!$canReceive)
                                            <div class="text-xs text-red-500">{{ $message }}</div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @error('teacher_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <span class="inline-block w-3 h-3 bg-green-50 border border-green-200 rounded mr-1"></span>
                        Green background indicates teachers who can receive salary for the selected month
                    </p>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="mt-2">No teachers found</p>
                    </div>
                @endif
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3"
                          placeholder="Optional notes for this bulk salary processing"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('finance.salaries.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium transition duration-200" id="submitBtn">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Process Selected Salaries
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('.teacher-checkbox:not([disabled])');
    checkboxes.forEach(checkbox => checkbox.checked = true);
    updateSubmitButton();
}

function selectNone() {
    const checkboxes = document.querySelectorAll('.teacher-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    updateSubmitButton();
}

function selectUnpaid() {
    const checkboxes = document.querySelectorAll('.teacher-checkbox');
    checkboxes.forEach(checkbox => {
        if (checkbox.dataset.unpaid === 'true' && !checkbox.disabled) {
            checkbox.checked = true;
        } else {
            checkbox.checked = false;
        }
    });
    updateSubmitButton();
}

function updateSubmitButton() {
    const checkedBoxes = document.querySelectorAll('.teacher-checkbox:checked');
    const submitBtn = document.getElementById('submitBtn');
    
    if (checkedBoxes.length > 0) {
        submitBtn.textContent = `Process ${checkedBoxes.length} Selected Salaries`;
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        submitBtn.textContent = 'Process Selected Salaries';
        submitBtn.disabled = false; // Allow form submission for validation
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// Add event listeners to checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.teacher-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSubmitButton);
    });
    
    // Initial update
    updateSubmitButton();
});
</script>
@endsection
