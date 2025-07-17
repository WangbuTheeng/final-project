@extends('layouts.dashboard')

@section('title', 'Create Student')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Student</h1>
            <p class="mt-1 text-sm text-gray-500">Add a new student to the system</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('students.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Students
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Modern Form Container -->
    <x-forms.container
        title="Student Registration"
        subtitle="Fill in the student details below"
        icon="fas fa-user-graduate"
        action="{{ route('students.store') }}"
        method="POST"
    >
        <!-- Personal Information Section -->
        <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-user mr-3 text-blue-500"></i>
                    Personal Information
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Basic personal details of the student</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- First Name -->
                <x-forms.input
                    name="first_name"
                    label="First Name"
                    icon="fas fa-user"
                    :required="true"
                    placeholder="Enter first name"
                    help="Student's legal first name"
                />

                <!-- Last Name -->
                <x-forms.input
                    name="last_name"
                    label="Last Name"
                    icon="fas fa-user"
                    :required="true"
                    placeholder="Enter last name"
                    help="Student's legal last name"
                />

                <!-- Email Address -->
                <x-forms.input
                    name="email"
                    type="email"
                    label="Email Address"
                    icon="fas fa-envelope"
                    :required="true"
                    placeholder="Enter email address"
                    help="This will be used for login and communication"
                />

                <!-- Phone Number -->
                <x-forms.input
                    name="phone"
                    type="tel"
                    label="Phone Number"
                    icon="fas fa-phone"
                    placeholder="Enter phone number"
                    help="Contact number for the student"
                />

                <!-- Date of Birth -->
                <x-forms.input
                    name="date_of_birth"
                    type="date"
                    label="Date of Birth"
                    icon="fas fa-calendar"
                    :required="true"
                    help="Student's date of birth"
                />

                <!-- Gender -->
                <x-forms.select
                    name="gender"
                    label="Gender"
                    icon="fas fa-venus-mars"
                    :required="true"
                    placeholder="Select gender"
                    :options="[
                        ['value' => 'male', 'text' => 'Male'],
                        ['value' => 'female', 'text' => 'Female'],
                        ['value' => 'other', 'text' => 'Other']
                    ]"
                    help="Student's gender identity"
                />
            </div>

            <!-- Address (Full Width) -->
            <x-forms.textarea
                name="address"
                label="Address"
                :rows="3"
                placeholder="Enter full address"
                help="Student's residential address"
                :auto-resize="true"
            />
                </div>
            </div>
        </div>

        <!-- Academic Information Section -->
        <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-graduation-cap mr-3 text-emerald-500"></i>
                    Academic Information
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Academic details and enrollment information</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Admission Number Info -->
                <div class="lg:col-span-2">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200">Admission Number</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Will be generated automatically upon creation</p>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Format: YYDDDNNNN (e.g., 24CSC0001)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Faculty -->
                <x-forms.select
                    name="faculty_id"
                    label="Faculty"
                    icon="fas fa-university"
                    :required="true"
                    placeholder="Select Faculty"
                    :searchable="true"
                    :options="$faculties->map(fn($faculty) => ['value' => $faculty->id, 'text' => $faculty->name])->toArray()"
                    help="Choose the faculty for this student"
                />

                <!-- Department -->
                <x-forms.select
                    name="department_id"
                    label="Department"
                    icon="fas fa-building"
                    placeholder="Select Department (Optional)"
                    :searchable="true"
                    :options="$departments->map(fn($dept) => ['value' => $dept->id, 'text' => $dept->name])->toArray()"
                    help="Choose the department (optional)"
                />

                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Admission Year *</label>
                        <div class="mt-1">
                            <select name="academic_year_id" id="academic_year_id" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('academic_year_id') border-red-500 @enderror">
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('academic_year_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>



                    <div>
                        <label for="mode_of_entry" class="block text-sm font-medium text-gray-700">Mode of Entry *</label>
                        <div class="mt-1">
                            <select name="mode_of_entry" id="mode_of_entry" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('mode_of_entry') border-red-500 @enderror">
                                <option value="">Select Mode of Entry</option>
                                <option value="entrance_exam" {{ old('mode_of_entry') == 'entrance_exam' ? 'selected' : '' }}>Entrance Exam</option>
                                <option value="direct_entry" {{ old('mode_of_entry') == 'direct_entry' ? 'selected' : '' }}>Direct Entry</option>
                                <option value="transfer" {{ old('mode_of_entry') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                        </div>
                        @error('mode_of_entry')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                </div>
            </div>
        </div>

        <!-- Guardian Information -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Guardian Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                    <div>
                        <label for="guardian_name" class="block text-sm font-medium text-gray-700">Guardian Name</label>
                        <div class="mt-1">
                            <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name') }}"
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('guardian_name') border-red-500 @enderror">
                        </div>
                        @error('guardian_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="guardian_phone" class="block text-sm font-medium text-gray-700">Guardian Phone</label>
                        <div class="mt-1">
                            <input type="text" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone') }}"
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('guardian_phone') border-red-500 @enderror">
                        </div>
                        @error('guardian_phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="guardian_email" class="block text-sm font-medium text-gray-700">Guardian Email</label>
                        <div class="mt-1">
                            <input type="email" name="guardian_email" id="guardian_email" value="{{ old('guardian_email') }}"
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('guardian_email') border-red-500 @enderror">
                        </div>
                        @error('guardian_email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="guardian_relationship" class="block text-sm font-medium text-gray-700">Relationship</label>
                        <div class="mt-1">
                            <select name="guardian_relationship" id="guardian_relationship"
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('guardian_relationship') border-red-500 @enderror">
                                <option value="">Select Relationship</option>
                                <option value="father" {{ old('guardian_relationship') == 'father' ? 'selected' : '' }}>Father</option>
                                <option value="mother" {{ old('guardian_relationship') == 'mother' ? 'selected' : '' }}>Mother</option>
                                <option value="guardian" {{ old('guardian_relationship') == 'guardian' ? 'selected' : '' }}>Guardian</option>
                                <option value="uncle" {{ old('guardian_relationship') == 'uncle' ? 'selected' : '' }}>Uncle</option>
                                <option value="aunt" {{ old('guardian_relationship') == 'aunt' ? 'selected' : '' }}>Aunt</option>
                                <option value="sibling" {{ old('guardian_relationship') == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                <option value="other" {{ old('guardian_relationship') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        @error('guardian_relationship')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="guardian_address" class="block text-sm font-medium text-gray-700">Guardian Address</label>
                        <div class="mt-1">
                            <textarea name="guardian_address" id="guardian_address" rows="3"
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('guardian_address') border-red-500 @enderror">{{ old('guardian_address') }}</textarea>
                        </div>
                        @error('guardian_address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('students.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150"
                    style="background-color: #37a2bc; hover:background-color: #2d8299; focus:ring-color: #37a2bc;">
                <i class="fas fa-save mr-2"></i>
                Create Student
            </button>
        </div>

        <!-- Form Actions -->
        <x-slot name="actions">
            <div class="flex items-center justify-between">
                <a href="{{ route('students.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </a>

                <div class="flex space-x-3">
                    <x-forms.button
                        type="button"
                        variant="secondary"
                        icon="fas fa-save"
                        onclick="document.querySelector('form').submit()"
                    >
                        Save as Draft
                    </x-forms.button>

                    <x-forms.button
                        type="submit"
                        variant="primary"
                        icon="fas fa-user-plus"
                        size="lg"
                    >
                        Create Student
                    </x-forms.button>
                </div>
            </div>
        </x-slot>
    </x-forms.container>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const facultySelect = document.getElementById('faculty_id');
    const departmentSelect = document.getElementById('department_id');
    const allDepartmentOptions = Array.from(departmentSelect.options);

    function filterDepartments() {
        const selectedFacultyId = facultySelect.value;

        // Clear current options except the first one
        departmentSelect.innerHTML = '<option value="">Select Department (Optional)</option>';

        if (selectedFacultyId) {
            // Use AJAX to get departments for the selected faculty
            fetch(`/students/departments-by-faculty/${selectedFacultyId}`, {
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
                    departments.forEach(department => {
                        const option = document.createElement('option');
                        option.value = department.id;
                        option.textContent = department.name;
                        departmentSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching departments:', error);
                    // Fallback to client-side filtering
                    allDepartmentOptions.forEach(option => {
                        if (option.value && option.dataset.faculty === selectedFacultyId) {
                            departmentSelect.appendChild(option.cloneNode(true));
                        }
                    });
                });
        }
    }

    facultySelect.addEventListener('change', filterDepartments);

    // Initialize on page load
    filterDepartments();
});
</script>
@endpush

@endsection
