@extends('layouts.dashboard')

@section('title', 'Record Payment')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Record Payment</h1>
            <p class="text-gray-600 mt-2">Record a new payment from student</p>
        </div>
        <a href="{{ route('finance.payments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Payments
        </a>
    </div>

    <!-- Payment Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('finance.payments.store') }}" method="POST" id="paymentForm" onsubmit="return validateFormSubmission()">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Payment Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
                    
                    <!-- Student Selection -->
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Student *</label>
                        @if($preSelectedStudent)
                            <!-- Pre-selected student (from invoice) -->
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                <span class="text-gray-900 font-medium">{{ $preSelectedStudent->user->name }}</span>
                                <span class="text-gray-600">({{ $preSelectedStudent->student_id }})</span>
                                @if($preSelectedStudent->admission_number)
                                    <span class="text-gray-500">- {{ $preSelectedStudent->admission_number }}</span>
                                @endif
                            </div>
                            <input type="hidden" id="student_id" name="student_id" value="{{ $preSelectedStudent->id }}">
                            <p class="mt-1 text-sm text-gray-500">Student is pre-selected from the invoice</p>
                        @else
                            <!-- Normal student selection -->
                            <select id="student_id" name="student_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('student_id') border-red-500 @enderror"
                                    required onchange="loadStudentInvoices()">
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id', request('student_id')) == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->name }} ({{ $student->student_id }})
                                        @if($student->admission_number) - {{ $student->admission_number }} @endif
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('student_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Invoice Selection -->
                    <div>
                        <label for="invoice_id" class="block text-sm font-medium text-gray-700 mb-2">Invoice</label>
                        @if($invoice)
                            <!-- Pre-selected invoice -->
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                <span class="text-gray-900 font-medium">#{{ $invoice->invoice_number }}</span>
                                <span class="text-gray-600">- NRs {{ number_format($invoice->balance, 2) }} due</span>
                                <span class="text-gray-500">({{ $invoice->due_date->format('M d, Y') }})</span>
                            </div>
                            <input type="hidden" id="invoice_id" name="invoice_id" value="{{ $invoice->id }}">
                            <p class="mt-1 text-sm text-gray-500">Invoice is pre-selected</p>
                        @else
                            <!-- Normal invoice selection -->
                            <select id="invoice_id" name="invoice_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('invoice_id') border-red-500 @enderror"
                                    onchange="updateInvoiceDetails()">
                                <option value="">Select Invoice (Optional)</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Leave empty for general payment</p>
                        @endif
                        @error('invoice_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Payment Amount (NRs) *</label>
                        <input type="number" id="amount" name="amount"
                               value="{{ old('amount', $invoice ? $invoice->balance : '') }}"
                               step="0.01" min="0.01"
                               @if($invoice) max="{{ $invoice->balance }}" @endif
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror"
                               required>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($invoice)
                            <div class="mt-1 text-sm text-gray-500">
                                Outstanding balance: <span class="font-medium text-red-600">NRs {{ number_format($invoice->balance, 2) }}</span>
                                <span class="text-xs text-gray-400">(Maximum payment amount)</span>
                            </div>
                        @else
                            <div id="invoice-balance" class="mt-1 text-sm text-gray-500 hidden">
                                Outstanding balance: <span class="font-medium text-red-600"></span>
                                <span class="text-xs text-gray-400">(Maximum payment amount)</span>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                        <select id="payment_method" name="payment_method" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_method') border-red-500 @enderror" 
                                required>
                            <option value="">Select Payment Method</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card Payment</option>
                            <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Date -->
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">Payment Date *</label>
                        <input type="datetime-local" id="payment_date" name="payment_date" 
                               value="{{ old('payment_date', now()->format('Y-m-d\TH:i')) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payment_date') border-red-500 @enderror" 
                               required>
                        @error('payment_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction ID -->
                    <div>
                        <label for="transaction_id" class="block text-sm font-medium text-gray-700 mb-2">Transaction ID</label>
                        <input type="text" id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('transaction_id') border-red-500 @enderror" 
                               placeholder="Bank/Gateway transaction reference">
                        @error('transaction_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Details</h3>
                    
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror" 
                                  placeholder="Payment description or notes">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Details (JSON) -->
                    <div id="payment-details-section" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Details</label>
                        <div id="payment-details-fields" class="space-y-3">
                            <!-- Dynamic fields based on payment method -->
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Internal Notes</label>
                        <textarea id="notes" name="notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror" 
                                  placeholder="Internal notes (not visible to student)">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Payment Status *</label>
                        <select id="status" name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror" 
                                required>
                            <option value="completed" {{ old('status', 'completed') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending Verification</option>
                            <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Invoice Details Display -->
                    @if($invoice)
                        <!-- Pre-selected invoice details -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Invoice Details</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p>Invoice: <span class="font-mono">#{{ $invoice->invoice_number }}</span></p>
                                <p>Issue Date: <span class="font-medium">{{ $invoice->issue_date->format('M d, Y') }}</span></p>
                                <p>Due Date: <span class="font-medium">{{ $invoice->due_date->format('M d, Y') }}</span></p>
                                <p>Total Amount: <span class="font-medium">NRs {{ number_format($invoice->total_amount, 2) }}</span></p>
                                <p>Amount Paid: <span class="font-medium">NRs {{ number_format($invoice->amount_paid, 2) }}</span></p>
                                <p>Balance Due: <span class="font-medium text-red-600">NRs {{ number_format($invoice->balance, 2) }}</span></p>
                                <p>Academic Year: <span class="font-medium">{{ $invoice->academicYear->name }}</span></p>
                            </div>
                        </div>
                    @else
                        <!-- Dynamic invoice details for normal selection -->
                        <div id="invoice-details" class="hidden bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Selected Invoice Details</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p>Invoice: <span id="invoice-number" class="font-mono"></span></p>
                                <p>Total Amount: <span id="invoice-total" class="font-medium"></span></p>
                                <p>Amount Paid: <span id="invoice-paid" class="font-medium"></span></p>
                                <p>Balance Due: <span id="invoice-balance-amount" class="font-medium text-red-600"></span></p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('finance.payments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function loadStudentInvoices() {
    const studentId = document.getElementById('student_id').value;
    const invoiceSelect = document.getElementById('invoice_id');

    // Only proceed if we have a normal invoice select (not pre-selected)
    if (!invoiceSelect || invoiceSelect.type === 'hidden') {
        return;
    }

    // Clear existing options
    invoiceSelect.innerHTML = '<option value="">Select Invoice (Optional)</option>';

    if (!studentId) {
        return;
    }

    // Show loading state
    invoiceSelect.innerHTML = '<option value="">Loading invoices...</option>';

    // Fetch student invoices via AJAX
    fetch(`/finance/get-student-invoices?student_id=${studentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Clear loading state
            invoiceSelect.innerHTML = '<option value="">Select Invoice (Optional)</option>';

            if (data.success && data.invoices.length > 0) {
                data.invoices.forEach(invoice => {
                    const option = document.createElement('option');
                    option.value = invoice.id;
                    option.textContent = `#${invoice.invoice_number} - NRs ${parseFloat(invoice.balance).toFixed(2)} due`;
                    option.dataset.invoice = JSON.stringify(invoice);
                    invoiceSelect.appendChild(option);
                });
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No unpaid invoices found';
                option.disabled = true;
                invoiceSelect.appendChild(option);
            }
        })
        .catch(error => {
            console.error('Error loading invoices:', error);
            invoiceSelect.innerHTML = '<option value="">Error loading invoices</option>';
        });
}

function updateInvoiceDetails() {
    const invoiceSelect = document.getElementById('invoice_id');

    // Skip if invoice is pre-selected (hidden input)
    if (!invoiceSelect || invoiceSelect.type === 'hidden') {
        return;
    }

    const selectedOption = invoiceSelect.options[invoiceSelect.selectedIndex];
    const invoiceDetails = document.getElementById('invoice-details');
    const invoiceBalance = document.getElementById('invoice-balance');
    const amountInput = document.getElementById('amount');

    if (selectedOption.value && selectedOption.dataset.invoice) {
        const invoice = JSON.parse(selectedOption.dataset.invoice);

        // Show invoice details
        document.getElementById('invoice-number').textContent = invoice.invoice_number;
        document.getElementById('invoice-total').textContent = `NRs ${parseFloat(invoice.total_amount).toFixed(2)}`;
        document.getElementById('invoice-paid').textContent = `NRs ${parseFloat(invoice.amount_paid).toFixed(2)}`;
        document.getElementById('invoice-balance-amount').textContent = `NRs ${parseFloat(invoice.balance).toFixed(2)}`;

        // Show balance info
        if (invoiceBalance) {
            invoiceBalance.querySelector('span').textContent = `NRs ${parseFloat(invoice.balance).toFixed(2)}`;
            invoiceBalance.classList.remove('hidden');
        }

        // Set amount to balance due and set max limit
        amountInput.value = parseFloat(invoice.balance).toFixed(2);
        amountInput.max = invoice.balance;
        amountInput.step = '0.01';

        // Add real-time validation
        amountInput.addEventListener('input', function() {
            validatePaymentAmount(invoice.balance);
        });

        if (invoiceDetails) {
            invoiceDetails.classList.remove('hidden');
        }
    } else {
        if (invoiceDetails) {
            invoiceDetails.classList.add('hidden');
        }
        if (invoiceBalance) {
            invoiceBalance.classList.add('hidden');
        }
        amountInput.removeAttribute('max');
        amountInput.value = '';

        // Remove validation listener
        amountInput.removeEventListener('input', validatePaymentAmount);
    }
}

function validatePaymentAmount(maxAmount) {
    const amountInput = document.getElementById('amount');
    const currentAmount = parseFloat(amountInput.value);

    // Remove any existing error styling
    amountInput.classList.remove('border-red-500');

    // Remove any existing error message
    const existingError = document.getElementById('amount-error');
    if (existingError) {
        existingError.remove();
    }

    if (currentAmount > maxAmount) {
        // Add error styling
        amountInput.classList.add('border-red-500');

        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.id = 'amount-error';
        errorDiv.className = 'mt-1 text-sm text-red-600';
        errorDiv.textContent = `Payment amount cannot exceed balance of NRs ${parseFloat(maxAmount).toFixed(2)}`;
        amountInput.parentNode.appendChild(errorDiv);

        // Disable submit button
        document.querySelector('button[type="submit"]').disabled = true;
        document.querySelector('button[type="submit"]').classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        // Enable submit button
        document.querySelector('button[type="submit"]').disabled = false;
        document.querySelector('button[type="submit"]').classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// Handle payment method change to show relevant fields
document.getElementById('payment_method').addEventListener('change', function() {
    const method = this.value;
    const detailsSection = document.getElementById('payment-details-section');
    const fieldsContainer = document.getElementById('payment-details-fields');
    
    fieldsContainer.innerHTML = '';
    
    if (method === 'bank_transfer') {
        fieldsContainer.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                <input type="text" name="payment_details[bank_name]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                <input type="text" name="payment_details[account_number]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        `;
        detailsSection.classList.remove('hidden');
    } else if (method === 'cheque') {
        fieldsContainer.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cheque Number</label>
                <input type="text" name="payment_details[cheque_number]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                <input type="text" name="payment_details[bank_name]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        `;
        detailsSection.classList.remove('hidden');
    } else if (method === 'card') {
        fieldsContainer.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Card Type</label>
                <select name="payment_details[card_type]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Card Type</option>
                    <option value="visa">Visa</option>
                    <option value="mastercard">Mastercard</option>
                    <option value="amex">American Express</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Last 4 Digits</label>
                <input type="text" name="payment_details[last_four]" maxlength="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        `;
        detailsSection.classList.remove('hidden');
    } else {
        detailsSection.classList.add('hidden');
    }
});

// Initialize page based on pre-selected data
document.addEventListener('DOMContentLoaded', function() {
    @if($preSelectedStudent && !$invoice)
        // If student is pre-selected but no specific invoice, load their invoices
        loadStudentInvoices();
    @endif

    @if(!$preSelectedStudent)
        // If no pre-selected student, enable normal student selection
        const studentSelect = document.getElementById('student_id');
        if (studentSelect && studentSelect.tagName === 'SELECT') {
            studentSelect.addEventListener('change', loadStudentInvoices);
        }
    @endif

    @if($invoice)
        // If invoice is pre-selected, set up validation for the amount field
        const amountInput = document.getElementById('amount');
        const maxAmount = {{ $invoice->balance }};

        amountInput.addEventListener('input', function() {
            validatePaymentAmount(maxAmount);
        });

        // Initial validation
        validatePaymentAmount(maxAmount);
    @endif
});

function validateFormSubmission() {
    const amountInput = document.getElementById('amount');
    const invoiceSelect = document.getElementById('invoice_id');

    // Check if we have an invoice selected (either pre-selected or chosen)
    let maxAmount = null;

    @if($invoice)
        maxAmount = {{ $invoice->balance }};
    @else
        if (invoiceSelect && invoiceSelect.value && invoiceSelect.options[invoiceSelect.selectedIndex].dataset.invoice) {
            const invoice = JSON.parse(invoiceSelect.options[invoiceSelect.selectedIndex].dataset.invoice);
            maxAmount = parseFloat(invoice.balance);
        }
    @endif

    if (maxAmount !== null) {
        const currentAmount = parseFloat(amountInput.value);
        if (currentAmount > maxAmount) {
            alert(`Payment amount (NRs ${currentAmount.toFixed(2)}) cannot exceed the remaining balance of NRs ${maxAmount.toFixed(2)}`);
            return false;
        }
    }

    return true;
}
</script>
@endsection
