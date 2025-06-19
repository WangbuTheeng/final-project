@extends('layouts.dashboard')

@section('title', 'Create Invoice')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('finance.invoices.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Invoice</h1>
                <p class="text-gray-600 mt-2">Generate a new invoice for student fees</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md">
        <form action="{{ route('finance.invoices.store') }}" method="POST" class="p-6" id="invoice-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Student Selection -->
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Student *</label>
                        @if($preSelectedStudent)
                            <!-- Pre-selected student (from invoice show page) -->
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                <span class="text-gray-900 font-medium">{{ $preSelectedStudent->user->first_name }} {{ $preSelectedStudent->user->last_name }}</span>
                                <span class="text-gray-600">({{ $preSelectedStudent->admission_number }})</span>
                            </div>
                            <input type="hidden" id="student_id" name="student_id" value="{{ $preSelectedStudent->id }}">
                            <p class="mt-1 text-sm text-gray-500">Student is pre-selected</p>
                        @else
                            <!-- Normal student selection -->
                            <select name="student_id" id="student_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('student_id') border-red-500 @enderror">
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->first_name }} {{ $student->user->last_name }} ({{ $student->admission_number }})
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('student_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Outstanding Balance Section -->
                    <div id="outstanding-balance-section" class="hidden">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-medium text-yellow-900">Outstanding Balance</h4>
                                <span id="outstanding-amount" class="text-lg font-bold text-red-600">NRs 0.00</span>
                            </div>

                            <div id="unpaid-invoices-list" class="space-y-2 mb-3">
                                <!-- Unpaid invoices will be populated here -->
                            </div>

                            <div class="flex items-center space-x-3">
                                <input type="checkbox" id="include_outstanding" name="include_outstanding" value="1"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="include_outstanding" class="text-sm font-medium text-yellow-900">
                                    Include outstanding balance in this invoice
                                </label>
                            </div>
                            <p class="text-xs text-yellow-700 mt-2">
                                This will add the outstanding balance as a separate line item in the new invoice.
                            </p>
                        </div>
                    </div>

                    <!-- Academic Year -->
                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">Academic Year *</label>
                        @if($preSelectedAcademicYear)
                            <!-- Pre-selected academic year -->
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                <span class="text-gray-900 font-medium">{{ $preSelectedAcademicYear->name }}</span>
                            </div>
                            <input type="hidden" id="academic_year_id" name="academic_year_id" value="{{ $preSelectedAcademicYear->id }}">
                            <p class="mt-1 text-sm text-gray-500">Academic year is pre-selected</p>
                        @else
                            <!-- Normal academic year selection -->
                            <select name="academic_year_id" id="academic_year_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('academic_year_id') border-red-500 @enderror">
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('academic_year_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Student Courses and Classes Info -->
                    <div id="student-info-section" class="hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-900 mb-3">Student Enrollment Information</h4>

                            <!-- Enrolled Courses -->
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-blue-700 mb-1">Enrolled Courses:</label>
                                <div id="enrolled-courses" class="text-sm text-blue-800">
                                    <!-- Courses will be populated here -->
                                </div>
                            </div>

                            <!-- Enrolled Classes -->
                            <div>
                                <label class="block text-xs font-medium text-blue-700 mb-1">Enrolled Classes:</label>
                                <div id="enrolled-classes" class="text-sm text-blue-800">
                                    <!-- Classes will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('due_date') border-red-500 @enderror">
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror"
                                  placeholder="Additional notes for this invoice">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Column - Fees Selection -->
                <div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Applicable Fees</h3>
                        
                        <div id="fees-loading" class="text-center py-8 hidden">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <p class="mt-2 text-gray-600">Loading applicable fees...</p>
                        </div>

                        <div id="fees-container" class="space-y-3">
                            <p class="text-gray-500 text-center py-8">
                                Please select a student and academic year to view applicable fees.
                            </p>
                        </div>

                        <!-- Custom Expenses Section -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-md font-medium text-gray-900">Custom Expenses</h4>
                                <button type="button" id="add-custom-expense"
                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-200">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Expense
                                </button>
                            </div>
                            <div id="custom-expenses-container" class="space-y-3">
                                <p class="text-gray-500 text-sm text-center py-4">No custom expenses added yet.</p>
                            </div>
                        </div>

                        <div id="fees-summary" class="mt-6 pt-4 border-t border-gray-200 hidden">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-medium text-gray-900">Total Amount:</span>
                                <span class="text-xl font-bold text-blue-600" id="total-amount">NRs 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('finance.invoices.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium transition duration-200">
                    Cancel
                </a>
                <button type="submit" id="submit-btn" disabled
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                    Create Invoice
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('student_id');
    const academicYearSelect = document.getElementById('academic_year_id');
    const feesContainer = document.getElementById('fees-container');
    const feesLoading = document.getElementById('fees-loading');
    const feesSummary = document.getElementById('fees-summary');
    const totalAmountSpan = document.getElementById('total-amount');
    const submitBtn = document.getElementById('submit-btn');
    const studentInfoSection = document.getElementById('student-info-section');
    const enrolledCoursesDiv = document.getElementById('enrolled-courses');
    const enrolledClassesDiv = document.getElementById('enrolled-classes');
    const outstandingBalanceSection = document.getElementById('outstanding-balance-section');
    const outstandingAmountSpan = document.getElementById('outstanding-amount');
    const unpaidInvoicesList = document.getElementById('unpaid-invoices-list');
    const includeOutstandingCheckbox = document.getElementById('include_outstanding');
    const addCustomExpenseBtn = document.getElementById('add-custom-expense');
    const customExpensesContainer = document.getElementById('custom-expenses-container');

    let customExpenseCounter = 0;

    function loadStudentCoursesAndClasses() {
        const studentId = studentSelect.value;
        const academicYearId = academicYearSelect.value;

        if (!studentId || !academicYearId) {
            studentInfoSection.classList.add('hidden');
            return;
        }

        fetch(`{{ route('finance.get-student-courses-classes') }}?student_id=${studentId}&academic_year_id=${academicYearId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Display courses
                    if (data.courses.length > 0) {
                        let coursesHtml = '';
                        data.courses.forEach(course => {
                            coursesHtml += `<span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2 mb-1">${course.code} - ${course.title}</span>`;
                        });
                        enrolledCoursesDiv.innerHTML = coursesHtml;
                    } else {
                        enrolledCoursesDiv.innerHTML = '<span class="text-gray-500 text-xs">No courses found</span>';
                    }

                    // Display classes
                    if (data.classes.length > 0) {
                        let classesHtml = '';
                        data.classes.forEach(cls => {
                            classesHtml += `<span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-2 mb-1">${cls.name} (${cls.semester} Semester)</span>`;
                        });
                        enrolledClassesDiv.innerHTML = classesHtml;
                    } else {
                        enrolledClassesDiv.innerHTML = '<span class="text-gray-500 text-xs">No classes found</span>';
                    }

                    studentInfoSection.classList.remove('hidden');
                } else {
                    studentInfoSection.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading student courses and classes:', error);
                studentInfoSection.classList.add('hidden');
            });
    }

    function loadOutstandingBalance() {
        const studentId = studentSelect.value;
        const academicYearId = academicYearSelect.value;

        if (!studentId) {
            outstandingBalanceSection.classList.add('hidden');
            return;
        }

        fetch(`{{ route('finance.get-student-outstanding-balance') }}?student_id=${studentId}&academic_year_id=${academicYearId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const outstandingBalance = parseFloat(data.outstanding_balance);

                    if (outstandingBalance > 0) {
                        outstandingAmountSpan.textContent = `NRs ${outstandingBalance.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;

                        // Display unpaid invoices
                        let invoicesHtml = '';
                        if (data.unpaid_invoices.length > 0) {
                            data.unpaid_invoices.forEach(invoice => {
                                invoicesHtml += `
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-700">#${invoice.invoice_number} (${invoice.academic_year})</span>
                                        <span class="font-medium text-red-600">NRs ${parseFloat(invoice.balance).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
                                    </div>
                                `;
                            });
                        }
                        unpaidInvoicesList.innerHTML = invoicesHtml;

                        outstandingBalanceSection.classList.remove('hidden');
                    } else {
                        outstandingBalanceSection.classList.add('hidden');
                    }
                } else {
                    outstandingBalanceSection.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading outstanding balance:', error);
                outstandingBalanceSection.classList.add('hidden');
            });
    }

    function loadApplicableFees() {
        const studentId = studentSelect.value;
        const academicYearId = academicYearSelect.value;

        if (!studentId || !academicYearId) {
            feesContainer.innerHTML = '<p class="text-gray-500 text-center py-8">Please select a student and academic year to view applicable fees.</p>';
            feesSummary.classList.add('hidden');
            submitBtn.disabled = true;
            return;
        }

        feesLoading.classList.remove('hidden');
        feesContainer.innerHTML = '';

        fetch(`{{ route('finance.get-applicable-fees') }}?student_id=${studentId}&academic_year_id=${academicYearId}`)
            .then(response => response.json())
            .then(data => {
                feesLoading.classList.add('hidden');

                if (data.success && data.fees.length > 0) {
                    let feesHtml = '';
                    let totalAmount = 0;

                    data.fees.forEach(fee => {
                        feesHtml += `
                            <div class="p-3 border border-gray-200 rounded-md fee-item" data-fee-id="${fee.id}">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="fee_ids[]" value="${fee.id}"
                                               class="fee-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               data-amount="${fee.amount}" ${fee.is_mandatory ? 'checked disabled' : ''}>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">${fee.name}</div>
                                            <div class="text-xs text-gray-500">${fee.code} - ${fee.fee_type_display}</div>
                                            ${fee.is_mandatory ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">Mandatory</span>' : ''}
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900">NRs ${parseFloat(fee.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>
                                </div>
                                <div class="fee-description-container ${fee.is_mandatory ? '' : 'hidden'} mt-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Description/Details:</label>
                                    <input type="text" name="fee_descriptions[${fee.id}]"
                                           class="fee-description w-full px-2 py-1 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                           placeholder="e.g., Monthly, Copy-1 Pencil, etc."
                                           maxlength="255">
                                    <p class="text-xs text-gray-500 mt-1">Optional: Add specific details about this fee</p>
                                </div>
                            </div>
                        `;

                        if (fee.is_mandatory) {
                            totalAmount += parseFloat(fee.amount);
                        }
                    });

                    feesContainer.innerHTML = feesHtml;
                    updateTotalAmount();
                    feesSummary.classList.remove('hidden');

                    // Add event listeners to checkboxes
                    document.querySelectorAll('.fee-checkbox').forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            toggleFeeDescription(this);
                            updateTotalAmount();
                        });
                    });

                    updateSubmitButton();
                } else {
                    feesContainer.innerHTML = '<p class="text-gray-500 text-center py-8">No applicable fees found for the selected criteria.</p>';
                    feesSummary.classList.add('hidden');
                    submitBtn.disabled = true;
                }
            })
            .catch(error => {
                feesLoading.classList.add('hidden');
                feesContainer.innerHTML = '<p class="text-red-500 text-center py-8">Error loading fees. Please try again.</p>';
                feesSummary.classList.add('hidden');
                submitBtn.disabled = true;
            });
    }

    function toggleFeeDescription(checkbox) {
        const feeItem = checkbox.closest('.fee-item');
        const descriptionContainer = feeItem.querySelector('.fee-description-container');
        const descriptionInput = feeItem.querySelector('.fee-description');

        if (checkbox.checked) {
            descriptionContainer.classList.remove('hidden');
            // Focus on the description input for better UX
            setTimeout(() => descriptionInput.focus(), 100);
        } else {
            descriptionContainer.classList.add('hidden');
            // Clear the description when unchecked
            descriptionInput.value = '';
        }
    }

    function addCustomExpense() {
        customExpenseCounter++;
        const expenseHtml = `
            <div class="p-3 border border-gray-200 rounded-md custom-expense-item" data-expense-id="${customExpenseCounter}">
                <div class="flex items-center justify-between mb-3">
                    <h5 class="text-sm font-medium text-gray-900">Custom Expense #${customExpenseCounter}</h5>
                    <button type="button" class="remove-custom-expense text-red-600 hover:text-red-800 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description *</label>
                        <input type="text" name="custom_expenses[${customExpenseCounter}][description]"
                               class="custom-expense-description w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               placeholder="e.g., Late fee, Processing fee, etc." required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Amount (NRs) *</label>
                        <input type="number" name="custom_expenses[${customExpenseCounter}][amount]"
                               class="custom-expense-amount w-full px-2 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                               placeholder="0.00" min="0" step="0.01" required>
                    </div>
                </div>
            </div>
        `;

        // Remove the "no expenses" message if it exists
        const noExpensesMsg = customExpensesContainer.querySelector('p');
        if (noExpensesMsg) {
            noExpensesMsg.remove();
        }

        customExpensesContainer.insertAdjacentHTML('beforeend', expenseHtml);

        // Add event listeners to the new expense
        const newExpenseItem = customExpensesContainer.lastElementChild;
        const removeBtn = newExpenseItem.querySelector('.remove-custom-expense');
        const amountInput = newExpenseItem.querySelector('.custom-expense-amount');
        const descriptionInput = newExpenseItem.querySelector('.custom-expense-description');

        removeBtn.addEventListener('click', function() {
            removeCustomExpense(newExpenseItem);
        });

        amountInput.addEventListener('input', function() {
            updateTotalAmount();
            updateSubmitButton();
        });

        descriptionInput.addEventListener('input', function() {
            updateSubmitButton();
        });

        updateTotalAmount();
        updateSubmitButton();
    }

    function removeCustomExpense(expenseItem) {
        expenseItem.remove();

        // Show "no expenses" message if no custom expenses remain
        if (customExpensesContainer.children.length === 0) {
            customExpensesContainer.innerHTML = '<p class="text-gray-500 text-sm text-center py-4">No custom expenses added yet.</p>';
        }

        updateTotalAmount();
        updateSubmitButton();
    }

    function updateTotalAmount() {
        let total = 0;

        // Add selected fees
        document.querySelectorAll('.fee-checkbox:checked').forEach(checkbox => {
            total += parseFloat(checkbox.dataset.amount);
        });

        // Add custom expenses
        document.querySelectorAll('.custom-expense-amount').forEach(input => {
            const amount = parseFloat(input.value) || 0;
            total += amount;
        });

        // Add outstanding balance if included
        if (includeOutstandingCheckbox && includeOutstandingCheckbox.checked) {
            const outstandingText = outstandingAmountSpan.textContent.replace('NRs ', '').replace(/,/g, '');
            const outstandingAmount = parseFloat(outstandingText) || 0;
            total += outstandingAmount;
        }

        totalAmountSpan.textContent = `NRs ${total.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
        updateSubmitButton();
    }

    function updateSubmitButton() {
        const checkedFees = document.querySelectorAll('.fee-checkbox:checked').length;
        const customExpenses = document.querySelectorAll('.custom-expense-item').length;
        const hasValidCustomExpenses = Array.from(document.querySelectorAll('.custom-expense-item')).some(item => {
            const description = item.querySelector('.custom-expense-description').value.trim();
            const amount = parseFloat(item.querySelector('.custom-expense-amount').value) || 0;
            return description && amount > 0;
        });

        submitBtn.disabled = checkedFees === 0 && !hasValidCustomExpenses;

        // Update button text based on what's selected
        if (checkedFees > 0 && hasValidCustomExpenses) {
            submitBtn.textContent = 'Create Invoice';
        } else if (checkedFees > 0) {
            submitBtn.textContent = 'Create Invoice';
        } else if (hasValidCustomExpenses) {
            submitBtn.textContent = 'Create Invoice';
        } else {
            submitBtn.textContent = 'Create Invoice';
        }
    }

    // Event listeners
    if (studentSelect.tagName === 'SELECT') {
        studentSelect.addEventListener('change', function() {
            loadStudentCoursesAndClasses();
            loadOutstandingBalance();
            loadApplicableFees();
        });
    }

    if (academicYearSelect.tagName === 'SELECT') {
        academicYearSelect.addEventListener('change', function() {
            loadStudentCoursesAndClasses();
            loadOutstandingBalance();
            loadApplicableFees();
        });
    }

    // Outstanding balance checkbox listener
    if (includeOutstandingCheckbox) {
        includeOutstandingCheckbox.addEventListener('change', updateTotalAmount);
    }

    // Custom expense button listener
    addCustomExpenseBtn.addEventListener('click', addCustomExpense);

    // Load data if form has values (including pre-selected)
    if (studentSelect.value && academicYearSelect.value) {
        loadStudentCoursesAndClasses();
        loadOutstandingBalance();
        loadApplicableFees();
    }

    @if($preSelectedStudent)
        // If student is pre-selected, load outstanding balance immediately
        loadOutstandingBalance();
    @endif
});
</script>
@endpush
@endsection
