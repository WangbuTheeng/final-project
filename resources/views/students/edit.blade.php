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

    <!-- Enhanced Edit Student Form -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
            <div class="flex items-center">
                <div class="bg-white/20 rounded-full p-3 mr-4">
                    <i class="fas fa-user-edit text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">Edit Student Information</h2>
                    <p class="text-blue-100 mt-1">Update student details for {{ $student->user->full_name }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Section 1: Personal Information -->
            <div class="px-8 py-6 space-y-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user mr-3 text-blue-500"></i>
                        Personal Information
                    </h3>
                    <p class="text-gray-600 mt-1">Basic personal details of the student</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-2"></i>First Name *
                        </label>
                        <input type="text" name="first_name" id="first_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('first_name') border-red-500 @enderror"
                               placeholder="Enter first name" value="{{ old('first_name', $student->user->first_name) }}">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-2"></i>Last Name *
                        </label>
                        <input type="text" name="last_name" id="last_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('last_name') border-red-500 @enderror"
                               placeholder="Enter last name" value="{{ old('last_name', $student->user->last_name) }}">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-gray-400 mr-2"></i>Email Address *
                        </label>
                        <input type="email" name="email" id="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 @enderror"
                               placeholder="Enter email address" value="{{ old('email', $student->user->email) }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone text-gray-400 mr-2"></i>Phone Number
                        </label>
                        <input type="tel" name="phone" id="phone"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('phone') border-red-500 @enderror"
                               placeholder="Enter phone number" value="{{ old('phone', $student->user->phone) }}">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar text-gray-400 mr-2"></i>Date of Birth *
                        </label>
                        <input type="date" name="date_of_birth" id="date_of_birth" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('date_of_birth') border-red-500 @enderror"
                               value="{{ old('date_of_birth', $student->user->date_of_birth?->format('Y-m-d')) }}">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-venus-mars text-gray-400 mr-2"></i>Gender *
                        </label>
                        <select name="gender" id="gender" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('gender') border-red-500 @enderror">
                            <option value="">Select gender</option>
                            <option value="male" {{ old('gender', $student->user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $student->user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $student->user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Citizenship Number -->
                    <div>
                        <label for="citizenship_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card text-gray-400 mr-2"></i>Citizenship Number *
                        </label>
                        <input type="text" name="citizenship_number" id="citizenship_number" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('citizenship_number') border-red-500 @enderror"
                               placeholder="Enter citizenship number" value="{{ old('citizenship_number', $student->user->citizenship_number) }}">
                        @error('citizenship_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Religion -->
                    <div>
                        <label for="religion" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-pray text-gray-400 mr-2"></i>Religion
                        </label>
                        <select name="religion" id="religion"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('religion') border-red-500 @enderror">
                            <option value="">Select religion</option>
                            <option value="Hindu" {{ old('religion', $student->user->religion) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddhist" {{ old('religion', $student->user->religion) == 'Buddhist' ? 'selected' : '' }}>Buddhist</option>
                            <option value="Christian" {{ old('religion', $student->user->religion) == 'Christian' ? 'selected' : '' }}>Christian</option>
                            <option value="Muslim" {{ old('religion', $student->user->religion) == 'Muslim' ? 'selected' : '' }}>Muslim</option>
                            <option value="Kirat" {{ old('religion', $student->user->religion) == 'Kirat' ? 'selected' : '' }}>Kirat</option>
                            <option value="Other" {{ old('religion', $student->user->religion) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('religion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Caste/Ethnicity -->
                    <div>
                        <label for="caste_ethnicity" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-users text-gray-400 mr-2"></i>Caste/Ethnicity
                        </label>
                        <input type="text" name="caste_ethnicity" id="caste_ethnicity"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('caste_ethnicity') border-red-500 @enderror"
                               placeholder="Enter caste/ethnicity" value="{{ old('caste_ethnicity', $student->user->caste_ethnicity) }}">
                        @error('caste_ethnicity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Blood Group -->
                    <div>
                        <label for="blood_group" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tint text-gray-400 mr-2"></i>Blood Group
                        </label>
                        <select name="blood_group" id="blood_group"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('blood_group') border-red-500 @enderror">
                            <option value="">Select blood group</option>
                            <option value="A+" {{ old('blood_group', $student->user->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_group', $student->user->blood_group) == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_group', $student->user->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_group', $student->user->blood_group) == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('blood_group', $student->user->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_group', $student->user->blood_group) == 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('blood_group', $student->user->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_group', $student->user->blood_group) == 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                        @error('blood_group')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Address Information -->
            <div class="px-8 py-6 space-y-6 border-t border-gray-200">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-map-marker-alt mr-3 text-green-500"></i>
                        Address Information
                    </h3>
                    <p class="text-gray-600 mt-1">Permanent and temporary address details</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Permanent Address -->
                    <div class="lg:col-span-2">
                        <label for="permanent_address" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-home text-gray-400 mr-2"></i>Permanent Address *
                        </label>
                        <textarea name="permanent_address" id="permanent_address" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('permanent_address') border-red-500 @enderror"
                                  placeholder="Enter permanent address">{{ old('permanent_address', $student->user->permanent_address) }}</textarea>
                        @error('permanent_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Temporary Address -->
                    <div class="lg:col-span-2">
                        <label for="temporary_address" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-pin text-gray-400 mr-2"></i>Temporary Address
                        </label>
                        <textarea name="temporary_address" id="temporary_address" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('temporary_address') border-red-500 @enderror"
                                  placeholder="Enter temporary address (if different from permanent)">{{ old('temporary_address', $student->user->temporary_address) }}</textarea>
                        @error('temporary_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- District -->
                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map text-gray-400 mr-2"></i>District *
                        </label>
                        <select name="district" id="district" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('district') border-red-500 @enderror">
                            <option value="">Select district</option>
                            @php
                                $districts = ['Kathmandu', 'Lalitpur', 'Bhaktapur', 'Chitwan', 'Pokhara', 'Dharan', 'Biratnagar', 'Janakpur', 'Nepalgunj', 'Dhangadhi'];
                            @endphp
                            @foreach($districts as $district)
                                <option value="{{ $district }}" {{ old('district', $student->user->district) == $district ? 'selected' : '' }}>{{ $district }}</option>
                            @endforeach
                        </select>
                        @error('district')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Province -->
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-flag text-gray-400 mr-2"></i>Province *
                        </label>
                        <select name="province" id="province" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('province') border-red-500 @enderror">
                            <option value="">Select province</option>
                            @php
                                $provinces = ['Province 1', 'Madhesh Province', 'Bagmati Province', 'Gandaki Province', 'Lumbini Province', 'Karnali Province', 'Sudurpashchim Province'];
                            @endphp
                            @foreach($provinces as $province)
                                <option value="{{ $province }}" {{ old('province', $student->user->province) == $province ? 'selected' : '' }}>{{ $province }}</option>
                            @endforeach
                        </select>
                        @error('province')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Academic Information -->
            <div class="px-8 py-6 space-y-6 border-t border-gray-200">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-graduation-cap mr-3 text-purple-500"></i>
                        Academic Information
                    </h3>
                    <p class="text-gray-600 mt-1">Academic background and admission details</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Admission Number (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-badge text-gray-400 mr-2"></i>Admission Number
                        </label>
                        <div class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 font-mono">
                            {{ $student->admission_number }}
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Admission number cannot be changed</p>
                    </div>

                    <!-- Previous School -->
                    <div>
                        <label for="previous_school_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-school text-gray-400 mr-2"></i>Previous School/College
                        </label>
                        <input type="text" name="previous_school_name" id="previous_school_name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('previous_school_name') border-red-500 @enderror"
                               placeholder="Enter previous school name" value="{{ old('previous_school_name', $student->previous_school_name) }}">
                        @error('previous_school_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Faculty -->
                    <div>
                        <label for="faculty_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-university text-gray-400 mr-2"></i>Faculty *
                        </label>
                        <select name="faculty_id" id="faculty_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('faculty_id') border-red-500 @enderror">
                            <option value="">Select Faculty</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ old('faculty_id', $student->faculty_id) == $faculty->id ? 'selected' : '' }}>
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
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building text-gray-400 mr-2"></i>Department
                        </label>
                        <select name="department_id" id="department_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('department_id') border-red-500 @enderror">
                            <option value="">Select Department (Optional)</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $student->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Academic Year -->
                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar text-gray-400 mr-2"></i>Admission Year *
                        </label>
                        <select name="academic_year_id" id="academic_year_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('academic_year_id') border-red-500 @enderror">
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $student->academic_year_id) == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mode of Entry -->
                    <div>
                        <label for="mode_of_entry" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-door-open text-gray-400 mr-2"></i>Mode of Entry *
                        </label>
                        <select name="mode_of_entry" id="mode_of_entry" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('mode_of_entry') border-red-500 @enderror">
                            <option value="">Select Mode of Entry</option>
                            <option value="entrance_exam" {{ old('mode_of_entry', $student->mode_of_entry) == 'entrance_exam' ? 'selected' : '' }}>Entrance Exam</option>
                            <option value="direct_entry" {{ old('mode_of_entry', $student->mode_of_entry) == 'direct_entry' ? 'selected' : '' }}>Direct Entry</option>
                            <option value="transfer" {{ old('mode_of_entry', $student->mode_of_entry) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        @error('mode_of_entry')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-check text-gray-400 mr-2"></i>Status *
                        </label>
                        <select name="status" id="status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('status') border-red-500 @enderror">
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="withdrawn" {{ old('status', $student->status) == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                            <option value="deferred" {{ old('status', $student->status) == 'deferred' ? 'selected' : '' }}>Deferred</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Entrance Exam Score -->
                    <div>
                        <label for="entrance_exam_score" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-trophy text-gray-400 mr-2"></i>Entrance Exam Score
                        </label>
                        <input type="number" name="entrance_exam_score" id="entrance_exam_score" step="0.01" min="0" max="100"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('entrance_exam_score') border-red-500 @enderror"
                               placeholder="Enter entrance exam score" value="{{ old('entrance_exam_score', $student->entrance_exam_score) }}">
                        @error('entrance_exam_score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preferred Subjects -->
                    <div>
                        <label for="preferred_subjects" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-heart text-gray-400 mr-2"></i>Preferred Subjects/Electives
                        </label>
                        <input type="text" name="preferred_subjects" id="preferred_subjects"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('preferred_subjects') border-red-500 @enderror"
                               placeholder="Enter preferred subjects" value="{{ old('preferred_subjects', $student->preferred_subjects) }}">
                        @error('preferred_subjects')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 4: Family Information -->
            <div class="px-8 py-6 space-y-6 border-t border-gray-200">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-users mr-3 text-orange-500"></i>
                        Family Information
                    </h3>
                    <p class="text-gray-600 mt-1">Parent and guardian details</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Father's Name -->
                    <div>
                        <label for="father_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-male text-gray-400 mr-2"></i>Father's Name
                        </label>
                        <input type="text" name="father_name" id="father_name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('father_name') border-red-500 @enderror"
                               placeholder="Enter father's name" value="{{ old('father_name', $student->father_name) }}">
                        @error('father_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Father's Occupation -->
                    <div>
                        <label for="father_occupation" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-briefcase text-gray-400 mr-2"></i>Father's Occupation
                        </label>
                        <input type="text" name="father_occupation" id="father_occupation"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('father_occupation') border-red-500 @enderror"
                               placeholder="Enter father's occupation" value="{{ old('father_occupation', $student->father_occupation) }}">
                        @error('father_occupation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mother's Name -->
                    <div>
                        <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-female text-gray-400 mr-2"></i>Mother's Name
                        </label>
                        <input type="text" name="mother_name" id="mother_name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('mother_name') border-red-500 @enderror"
                               placeholder="Enter mother's name" value="{{ old('mother_name', $student->mother_name) }}">
                        @error('mother_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mother's Occupation -->
                    <div>
                        <label for="mother_occupation" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-briefcase text-gray-400 mr-2"></i>Mother's Occupation
                        </label>
                        <input type="text" name="mother_occupation" id="mother_occupation"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('mother_occupation') border-red-500 @enderror"
                               placeholder="Enter mother's occupation" value="{{ old('mother_occupation', $student->mother_occupation) }}">
                        @error('mother_occupation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Annual Family Income -->
                    <div>
                        <label for="annual_family_income" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-money-bill text-gray-400 mr-2"></i>Annual Family Income (NPR)
                        </label>
                        <input type="number" name="annual_family_income" id="annual_family_income" step="0.01" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('annual_family_income') border-red-500 @enderror"
                               placeholder="Enter annual family income" value="{{ old('annual_family_income', $student->annual_family_income) }}">
                        @error('annual_family_income')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hostel Required -->
                    <div>
                        <label for="hostel_required" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-bed text-gray-400 mr-2"></i>Hostel Accommodation
                        </label>
                        <select name="hostel_required" id="hostel_required"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('hostel_required') border-red-500 @enderror">
                            <option value="0" {{ old('hostel_required', $student->hostel_required) == '0' ? 'selected' : '' }}>Not Required</option>
                            <option value="1" {{ old('hostel_required', $student->hostel_required) == '1' ? 'selected' : '' }}>Required</option>
                        </select>
                        @error('hostel_required')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <a href="{{ route('students.show', $student) }}"
                       class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center px-8 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Update Student
                    </button>
                </div>
            </div>
        </form>
    </div>
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
                        // Preserve selected state if editing
                        if (department.id == {{ $student->department_id ?? 'null' }}) {
                            option.selected = true;
                        }
                        departmentSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching departments:', error);
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
