@extends('layouts.dashboard')

@section('title', 'Process Salary')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Process Salary</h1>
            <p class="text-gray-600 mt-2">Process salary payments for teachers</p>
        </div>
        <a href="{{ route('finance.salaries.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Salaries
        </a>
    </div>

    <!-- Processing Options -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Individual Processing -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Individual Salary Processing</h3>
            <p class="text-gray-600 mb-4">Process salary for a specific teacher</p>
            
            <form action="{{ route('finance.salaries.store') }}" method="POST" id="individualForm">
                @csrf
                
                <div class="space-y-4">
                    <!-- Teacher Selection -->
                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-2">Teacher *</label>
                        <select id="teacher_id" name="teacher_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('teacher_id') border-red-500 @enderror" 
                                required onchange="updateTeacherDetails()">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}"
                                        data-salary="{{ $teacher->basic_salary }}"
                                        data-faculty="{{ $teacher->facultyInfo ? $teacher->facultyInfo->name : 'N/A' }}"
                                        data-department="{{ $teacher->department ?? 'N/A' }}"
                                        data-employee-id="{{ $teacher->employee_id }}"
                                        {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->teacher_name }} ({{ $teacher->employee_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teacher Details Display -->
                    <div id="teacher-details" class="hidden bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Teacher Details</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Employee ID:</span>
                                <span id="employee-id" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Faculty:</span>
                                <span id="faculty" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Department:</span>
                                <span id="department" class="font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Basic Salary:</span>
                                <span id="basic-salary" class="font-medium text-green-600"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Month Selection -->
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Month *</label>
                        <select id="month" name="month" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('month') border-red-500 @enderror" 
                                required>
                            <option value="">Select Month</option>
                            @for($i = 1; $i <= 12; $i++)
                                @php
                                    $monthValue = now()->year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                    $currentMonth = now()->format('Y-m');
                                @endphp
                                <option value="{{ $monthValue }}" {{ old('month', $currentMonth) == $monthValue ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }} {{ now()->year }}
                                </option>
                            @endfor
                        </select>
                        @error('month')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Salary Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Salary Amount (₹) *</label>
                        <input type="number" id="amount" name="amount" value="{{ old('amount') }}" 
                               step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror" 
                               required>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Amount will be auto-filled based on teacher's basic salary</p>
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">Payment Date *</label>
                        <input type="date" id="payment_date" name="payment_date" 
                               value="{{ old('payment_date', now()->format('Y-m-d')) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_date') border-red-500 @enderror" 
                               required>
                        @error('payment_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea id="notes" name="notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror" 
                                  placeholder="Optional notes about this salary payment">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="resetForm()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-200">
                        Reset
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Process Salary
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Processing -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bulk Salary Processing</h3>
            <p class="text-gray-600 mb-4">Process salaries for multiple teachers at once</p>
            
            <form action="{{ route('finance.salaries.bulk-store') }}" method="POST" id="bulkForm">
                @csrf
                
                <div class="space-y-4">
                    <!-- Month Selection for Bulk -->
                    <div>
                        <label for="bulk_month" class="block text-sm font-medium text-gray-700 mb-2">Month *</label>
                        <select id="bulk_month" name="month" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                required>
                            <option value="">Select Month</option>
                            @for($i = 1; $i <= 12; $i++)
                                @php
                                    $monthValue = now()->year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                                    $currentMonth = now()->format('Y-m');
                                @endphp
                                <option value="{{ $monthValue }}" {{ $currentMonth == $monthValue ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }} {{ now()->year }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <!-- Faculty Filter -->
                    <div>
                        <label for="bulk_faculty" class="block text-sm font-medium text-gray-700 mb-2">Faculty</label>
                        <select id="bulk_faculty" name="faculty_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                onchange="loadTeachersForBulk()">
                            <option value="">All Faculties</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Date for Bulk -->
                    <div>
                        <label for="bulk_payment_date" class="block text-sm font-medium text-gray-700 mb-2">Payment Date *</label>
                        <input type="date" id="bulk_payment_date" name="payment_date" 
                               value="{{ now()->format('Y-m-d') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                    </div>

                    <!-- Teacher Selection for Bulk -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Teachers</label>
                        <div class="border border-gray-300 rounded-md p-3 max-h-60 overflow-y-auto">
                            <div class="mb-2">
                                <label class="flex items-center">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Select All</span>
                                </label>
                            </div>
                            <div id="teachers-list" class="space-y-2">
                                @foreach($teachers as $teacher)
                                    <label class="flex items-center teacher-checkbox" data-faculty="{{ $teacher->facultyInfo ? $teacher->facultyInfo->id : '' }}">
                                        <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">
                                            {{ $teacher->teacher_name }} ({{ $teacher->employee_id }}) - ₹{{ number_format($teacher->basic_salary, 2) }}
                                            @if($teacher->facultyInfo)
                                                <span class="text-xs text-gray-500">- {{ $teacher->facultyInfo->name }}</span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Select teachers to process salaries for</p>
                    </div>

                    <!-- Bulk Notes -->
                    <div>
                        <label for="bulk_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea id="bulk_notes" name="notes" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                  placeholder="Optional notes for bulk salary processing"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="resetBulkForm()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-200">
                        Reset
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Process Bulk Salaries
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Salary Payments -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Salary Payments</h3>
        
        @if($recentPayments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentPayments as $payment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $payment->teacher->teacher_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ DateTime::createFromFormat('Y-m', $payment->month)->format('F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹{{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($payment->status == 'paid') bg-green-100 text-green-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No recent salary payments found.</p>
        @endif
    </div>
</div>

<script>
function updateTeacherDetails() {
    const select = document.getElementById('teacher_id');
    const selectedOption = select.options[select.selectedIndex];
    const detailsDiv = document.getElementById('teacher-details');
    const amountInput = document.getElementById('amount');

    if (selectedOption.value) {
        const salary = selectedOption.dataset.salary;
        const faculty = selectedOption.dataset.faculty;
        const department = selectedOption.dataset.department;
        const employeeId = selectedOption.dataset.employeeId;

        document.getElementById('employee-id').textContent = employeeId;
        document.getElementById('faculty').textContent = faculty;
        document.getElementById('department').textContent = department;
        document.getElementById('basic-salary').textContent = '₹' + parseFloat(salary).toLocaleString();

        amountInput.value = salary;
        detailsDiv.classList.remove('hidden');
    } else {
        detailsDiv.classList.add('hidden');
        amountInput.value = '';
    }
}

function resetForm() {
    document.getElementById('individualForm').reset();
    document.getElementById('teacher-details').classList.add('hidden');
}

function resetBulkForm() {
    document.getElementById('bulkForm').reset();
    document.querySelectorAll('input[name="teacher_ids[]"]').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
}

function loadTeachersForBulk() {
    const facultyId = document.getElementById('bulk_faculty').value;
    const checkboxes = document.querySelectorAll('.teacher-checkbox');

    checkboxes.forEach(checkbox => {
        if (!facultyId || checkbox.dataset.faculty == facultyId) {
            checkbox.style.display = 'flex';
        } else {
            checkbox.style.display = 'none';
            checkbox.querySelector('input').checked = false;
        }
    });

    document.getElementById('select-all').checked = false;
}

// Select all functionality
document.getElementById('select-all').addEventListener('change', function() {
    const isChecked = this.checked;
    const visibleCheckboxes = document.querySelectorAll('.teacher-checkbox:not([style*="display: none"]) input[type="checkbox"]');
    
    visibleCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
});

// Update select all when individual checkboxes change
document.querySelectorAll('input[name="teacher_ids[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const visibleCheckboxes = document.querySelectorAll('.teacher-checkbox:not([style*="display: none"]) input[type="checkbox"]');
        const checkedBoxes = document.querySelectorAll('.teacher-checkbox:not([style*="display: none"]) input[type="checkbox"]:checked');
        
        document.getElementById('select-all').checked = visibleCheckboxes.length === checkedBoxes.length && visibleCheckboxes.length > 0;
    });
});
</script>
@endsection
