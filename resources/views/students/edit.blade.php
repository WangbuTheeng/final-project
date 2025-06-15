@extends('layouts.dashboard')

@section('title', 'Edit Student')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Student</h1>
            <p class="mt-1 text-sm text-gray-500">Update student information</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('students.show', $student) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-eye mr-2"></i>
                View Student
            </a>
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

    <!-- Edit Student Form -->
    <form action="{{ route('students.update', $student) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Personal Information -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                        <div class="mt-1">
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $student->user->first_name) }}" required
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('first_name') border-red-500 @enderror">
                        </div>
                        @error('first_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                        <div class="mt-1">
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $student->user->last_name) }}" required
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('last_name') border-red-500 @enderror">
                        </div>
                        @error('last_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                        <div class="mt-1">
                            <input type="email" name="email" id="email" value="{{ old('email', $student->user->email) }}" required
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('email') border-red-500 @enderror">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <div class="mt-1">
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $student->user->phone) }}"
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('phone') border-red-500 @enderror">
                        </div>
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth *</label>
                        <div class="mt-1">
                            <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $student->user->date_of_birth?->format('Y-m-d')) }}" required
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('date_of_birth') border-red-500 @enderror">
                        </div>
                        @error('date_of_birth')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender *</label>
                        <div class="mt-1">
                            <select name="gender" id="gender" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('gender') border-red-500 @enderror">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $student->user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $student->user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $student->user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        @error('gender')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <div class="mt-1">
                            <textarea name="address" id="address" rows="3"
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('address') border-red-500 @enderror">{{ old('address', $student->user->address) }}</textarea>
                        </div>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Academic Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Admission Number</label>
                        <div class="mt-1">
                            <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm text-gray-900 font-mono">
                                {{ $student->admission_number }}
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Admission number cannot be changed</p>
                    </div>

                    <div>
                        <label for="faculty_id" class="block text-sm font-medium text-gray-700">Faculty *</label>
                        <div class="mt-1">
                            <select name="faculty_id" id="faculty_id" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('faculty_id') border-red-500 @enderror">
                                <option value="">Select Faculty</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ old('faculty_id', $student->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('faculty_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                        <div class="mt-1">
                            <select name="department_id" id="department_id"
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('department_id') border-red-500 @enderror">
                                <option value="">Select Department (Optional)</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" data-faculty="{{ $department->faculty_id }}" {{ old('department_id', $student->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('department_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="current_level" class="block text-sm font-medium text-gray-700">Current Level *</label>
                        <div class="mt-1">
                            <select name="current_level" id="current_level" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('current_level') border-red-500 @enderror">
                                <option value="">Select Level</option>
                                <option value="100" {{ old('current_level', $student->current_level) == '100' ? 'selected' : '' }}>100 Level</option>
                                <option value="200" {{ old('current_level', $student->current_level) == '200' ? 'selected' : '' }}>200 Level</option>
                                <option value="300" {{ old('current_level', $student->current_level) == '300' ? 'selected' : '' }}>300 Level</option>
                                <option value="400" {{ old('current_level', $student->current_level) == '400' ? 'selected' : '' }}>400 Level</option>
                                <option value="500" {{ old('current_level', $student->current_level) == '500' ? 'selected' : '' }}>500 Level</option>
                            </select>
                        </div>
                        @error('current_level')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mode_of_entry" class="block text-sm font-medium text-gray-700">Mode of Entry *</label>
                        <div class="mt-1">
                            <select name="mode_of_entry" id="mode_of_entry" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('mode_of_entry') border-red-500 @enderror">
                                <option value="">Select Mode of Entry</option>
                                <option value="entrance_exam" {{ old('mode_of_entry', $student->mode_of_entry) == 'entrance_exam' ? 'selected' : '' }}>Entrance Exam</option>
                                <option value="direct_entry" {{ old('mode_of_entry', $student->mode_of_entry) == 'direct_entry' ? 'selected' : '' }}>Direct Entry</option>
                                <option value="transfer" {{ old('mode_of_entry', $student->mode_of_entry) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            </select>
                        </div>
                        @error('mode_of_entry')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="study_mode" class="block text-sm font-medium text-gray-700">Study Mode *</label>
                        <div class="mt-1">
                            <select name="study_mode" id="study_mode" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('study_mode') border-red-500 @enderror">
                                <option value="">Select Study Mode</option>
                                <option value="full_time" {{ old('study_mode', $student->study_mode) == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                <option value="part_time" {{ old('study_mode', $student->study_mode) == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                <option value="distance" {{ old('study_mode', $student->study_mode) == 'distance' ? 'selected' : '' }}>Distance Learning</option>
                            </select>
                        </div>
                        @error('study_mode')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                        <div class="mt-1">
                            <select name="status" id="status" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('status') border-red-500 @enderror">
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="withdrawn" {{ old('status', $student->status) == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                                <option value="deferred" {{ old('status', $student->status) == 'deferred' ? 'selected' : '' }}>Deferred</option>
                            </select>
                        </div>
                        @error('status')
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
                            <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name', $student->guardian_info['name'] ?? '') }}"
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('guardian_name') border-red-500 @enderror">
                        </div>
                        @error('guardian_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="guardian_phone" class="block text-sm font-medium text-gray-700">Guardian Phone</label>
                        <div class="mt-1">
                            <input type="text" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone', $student->guardian_info['phone'] ?? '') }}"
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('guardian_phone') border-red-500 @enderror">
                        </div>
                        @error('guardian_phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="guardian_email" class="block text-sm font-medium text-gray-700">Guardian Email</label>
                        <div class="mt-1">
                            <input type="email" name="guardian_email" id="guardian_email" value="{{ old('guardian_email', $student->guardian_info['email'] ?? '') }}"
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
                                <option value="father" {{ old('guardian_relationship', $student->guardian_info['relationship'] ?? '') == 'father' ? 'selected' : '' }}>Father</option>
                                <option value="mother" {{ old('guardian_relationship', $student->guardian_info['relationship'] ?? '') == 'mother' ? 'selected' : '' }}>Mother</option>
                                <option value="guardian" {{ old('guardian_relationship', $student->guardian_info['relationship'] ?? '') == 'guardian' ? 'selected' : '' }}>Guardian</option>
                                <option value="uncle" {{ old('guardian_relationship', $student->guardian_info['relationship'] ?? '') == 'uncle' ? 'selected' : '' }}>Uncle</option>
                                <option value="aunt" {{ old('guardian_relationship', $student->guardian_info['relationship'] ?? '') == 'aunt' ? 'selected' : '' }}>Aunt</option>
                                <option value="sibling" {{ old('guardian_relationship', $student->guardian_info['relationship'] ?? '') == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                <option value="other" {{ old('guardian_relationship', $student->guardian_info['relationship'] ?? '') == 'other' ? 'selected' : '' }}>Other</option>
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
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md @error('guardian_address') border-red-500 @enderror">{{ old('guardian_address', $student->guardian_info['address'] ?? '') }}</textarea>
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
            <a href="{{ route('students.show', $student) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150"
                    style="background-color: #37a2bc; hover:background-color: #2d8299; focus:ring-color: #37a2bc;">
                <i class="fas fa-save mr-2"></i>
                Update Student
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const facultySelect = document.getElementById('faculty_id');
    const departmentSelect = document.getElementById('department_id');
    const allDepartmentOptions = Array.from(departmentSelect.options);
    const currentDepartmentId = '{{ old("department_id", $student->department_id) }}';

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
                        if (department.id == currentDepartmentId) {
                            option.selected = true;
                        }
                        departmentSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching departments:', error);
                    // Fallback to client-side filtering
                    allDepartmentOptions.forEach(option => {
                        if (option.value && option.dataset.faculty === selectedFacultyId) {
                            const newOption = option.cloneNode(true);
                            if (newOption.value === currentDepartmentId) {
                                newOption.selected = true;
                            }
                            departmentSelect.appendChild(newOption);
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
