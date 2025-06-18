@extends('layouts.dashboard')

@section('title', 'Add Teacher')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Add Teacher</h1>
            <p class="text-gray-600 mt-2">Add a new teacher for salary management</p>
        </div>
        <a href="{{ route('finance.teachers.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Teachers
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md">
        <form action="{{ route('finance.teachers.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Teacher Name -->
                <div>
                    <label for="teacher_name" class="block text-sm font-medium text-gray-700 mb-2">Teacher Name *</label>
                    <input type="text" 
                           id="teacher_name" 
                           name="teacher_name" 
                           value="{{ old('teacher_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('teacher_name') border-red-500 @enderror" 
                           required>
                    @error('teacher_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Employee ID -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                    <input type="text" 
                           id="employee_id" 
                           name="employee_id" 
                           value="{{ old('employee_id') }}"
                           placeholder="Leave blank for auto-generation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('employee_id') border-red-500 @enderror">
                    @error('employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">If left blank, an ID will be auto-generated based on department</p>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Faculty -->
                <div>
                    <label for="faculty_id" class="block text-sm font-medium text-gray-700 mb-2">Faculty</label>
                    <select id="faculty_id"
                            name="faculty_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('faculty_id') border-red-500 @enderror"
                            onchange="loadDepartments()">
                        <option value="">Select Faculty (Optional)</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('faculty_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select id="department_id"
                            name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('department_id') border-red-500 @enderror"
                            disabled>
                        <option value="">Select Faculty First (Optional)</option>
                    </select>
                    @error('department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Department selection is optional. Select a faculty first to choose a department.</p>
                </div>

                <!-- Position -->
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position *</label>
                    <input type="text" 
                           id="position" 
                           name="position" 
                           value="{{ old('position') }}"
                           placeholder="e.g., Professor, Lecturer, Assistant Professor"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('position') border-red-500 @enderror" 
                           required>
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hire Date -->
                <div>
                    <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-2">Hire Date *</label>
                    <input type="date" 
                           id="hire_date" 
                           name="hire_date" 
                           value="{{ old('hire_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('hire_date') border-red-500 @enderror" 
                           required>
                    @error('hire_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Basic Salary -->
                <div>
                    <label for="basic_salary" class="block text-sm font-medium text-gray-700 mb-2">Basic Salary (₦) *</label>
                    <input type="number" 
                           id="basic_salary" 
                           name="basic_salary" 
                           value="{{ old('basic_salary') }}"
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('basic_salary') border-red-500 @enderror" 
                           required>
                    @error('basic_salary')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select id="status" 
                            name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror" 
                            required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ old('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="terminated" {{ old('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bank Account -->
                <div>
                    <label for="bank_account" class="block text-sm font-medium text-gray-700 mb-2">Bank Account</label>
                    <input type="text" 
                           id="bank_account" 
                           name="bank_account" 
                           value="{{ old('bank_account') }}"
                           placeholder="Bank name and account number"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_account') border-red-500 @enderror">
                    @error('bank_account')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address -->
            <div class="mt-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea id="address" 
                          name="address" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('finance.teachers.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium transition duration-200">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Add Teacher
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function loadDepartments() {
    const facultySelect = document.getElementById('faculty_id');
    const departmentSelect = document.getElementById('department_id');
    const facultyId = facultySelect.value;

    // Reset department dropdown
    departmentSelect.innerHTML = '<option value="">Loading...</option>';
    departmentSelect.disabled = true;

    if (facultyId) {
        // Fetch departments for the selected faculty
        fetch(`/students/departments-by-faculty/${facultyId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(departments => {
            departmentSelect.innerHTML = '<option value="">Select Department (Optional)</option>';
            departments.forEach(department => {
                const option = document.createElement('option');
                option.value = department.id;
                option.textContent = department.name;
                departmentSelect.appendChild(option);
            });
            departmentSelect.disabled = false;

            // Pre-select department if it was selected before
            const oldDepartmentId = "{{ old('department_id') }}";
            if (oldDepartmentId) {
                departmentSelect.value = oldDepartmentId;
            }
        })
        .catch(error => {
            console.error('Error fetching departments:', error);
            departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
            departmentSelect.disabled = false;
        });
    } else {
        departmentSelect.innerHTML = '<option value="">Select Faculty First (Optional)</option>';
        departmentSelect.disabled = true;
    }
}

// Load departments on page load if faculty is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const facultySelect = document.getElementById('faculty_id');
    if (facultySelect.value) {
        loadDepartments();
    }
});
</script>
@endsection
