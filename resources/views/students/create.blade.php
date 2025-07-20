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

    <!-- Enhanced Form Container with Multi-Step Design -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
            <div class="flex items-center">
                <div class="bg-white/20 rounded-full p-3 mr-4">
                    <i class="fas fa-user-graduate text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">Student Registration</h2>
                    <p class="text-blue-100 mt-1">Complete student information for Nepal college admission</p>
                </div>
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="bg-gray-50 px-8 py-4">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center space-x-8">
                    <div class="flex items-center text-blue-600">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">1</div>
                        <span class="ml-2 font-medium">Personal Info</span>
                    </div>
                    <div class="flex items-center text-gray-400">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-bold">2</div>
                        <span class="ml-2">Address</span>
                    </div>
                    <div class="flex items-center text-gray-400">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-bold">3</div>
                        <span class="ml-2">Academic</span>
                    </div>
                    <div class="flex items-center text-gray-400">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-bold">4</div>
                        <span class="ml-2">Family</span>
                    </div>
                    <div class="flex items-center text-gray-400">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white text-xs font-bold">5</div>
                        <span class="ml-2">Additional</span>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Section 1: Personal Information -->
            <div class="px-8 py-6 space-y-6" id="section-1">
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
                               placeholder="Enter first name" value="{{ old('first_name') }}">
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
                               placeholder="Enter last name" value="{{ old('last_name') }}">
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
                               placeholder="Enter email address" value="{{ old('email') }}">
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
                               placeholder="Enter phone number" value="{{ old('phone') }}">
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
                               value="{{ old('date_of_birth') }}">
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
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
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
                               placeholder="Enter citizenship number" value="{{ old('citizenship_number') }}">
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
                            <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddhist" {{ old('religion') == 'Buddhist' ? 'selected' : '' }}>Buddhist</option>
                            <option value="Christian" {{ old('religion') == 'Christian' ? 'selected' : '' }}>Christian</option>
                            <option value="Muslim" {{ old('religion') == 'Muslim' ? 'selected' : '' }}>Muslim</option>
                            <option value="Kirat" {{ old('religion') == 'Kirat' ? 'selected' : '' }}>Kirat</option>
                            <option value="Other" {{ old('religion') == 'Other' ? 'selected' : '' }}>Other</option>
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
                               placeholder="Enter caste/ethnicity" value="{{ old('caste_ethnicity') }}">
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
                            <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                        @error('blood_group')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Address Information -->
            <div class="px-8 py-6 space-y-6 border-t border-gray-200" id="section-2">
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
                                  placeholder="Enter permanent address">{{ old('permanent_address') }}</textarea>
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
                                  placeholder="Enter temporary address (if different from permanent)">{{ old('temporary_address') }}</textarea>
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
                            <option value="Kathmandu" {{ old('district') == 'Kathmandu' ? 'selected' : '' }}>Kathmandu</option>
                            <option value="Lalitpur" {{ old('district') == 'Lalitpur' ? 'selected' : '' }}>Lalitpur</option>
                            <option value="Bhaktapur" {{ old('district') == 'Bhaktapur' ? 'selected' : '' }}>Bhaktapur</option>
                            <option value="Chitwan" {{ old('district') == 'Chitwan' ? 'selected' : '' }}>Chitwan</option>
                            <option value="Pokhara" {{ old('district') == 'Pokhara' ? 'selected' : '' }}>Pokhara</option>
                            <option value="Dharan" {{ old('district') == 'Dharan' ? 'selected' : '' }}>Dharan</option>
                            <option value="Butwal" {{ old('district') == 'Butwal' ? 'selected' : '' }}>Butwal</option>
                            <option value="Other" {{ old('district') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('district')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Province -->
                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-globe text-gray-400 mr-2"></i>Province *
                        </label>
                        <select name="province" id="province" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('province') border-red-500 @enderror">
                            <option value="">Select province</option>
                            <option value="Province 1" {{ old('province') == 'Province 1' ? 'selected' : '' }}>Province 1</option>
                            <option value="Madhesh Province" {{ old('province') == 'Madhesh Province' ? 'selected' : '' }}>Madhesh Province</option>
                            <option value="Bagmati Province" {{ old('province') == 'Bagmati Province' ? 'selected' : '' }}>Bagmati Province</option>
                            <option value="Gandaki Province" {{ old('province') == 'Gandaki Province' ? 'selected' : '' }}>Gandaki Province</option>
                            <option value="Lumbini Province" {{ old('province') == 'Lumbini Province' ? 'selected' : '' }}>Lumbini Province</option>
                            <option value="Karnali Province" {{ old('province') == 'Karnali Province' ? 'selected' : '' }}>Karnali Province</option>
                            <option value="Sudurpashchim Province" {{ old('province') == 'Sudurpashchim Province' ? 'selected' : '' }}>Sudurpashchim Province</option>
                        </select>
                        @error('province')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Academic Background -->
            <div class="px-8 py-6 space-y-6 border-t border-gray-200" id="section-3">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-graduation-cap mr-3 text-purple-500"></i>
                        Academic Background
                    </h3>
                    <p class="text-gray-600 mt-1">Previous education and academic qualifications</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Previous School Name -->
                    <div class="lg:col-span-2">
                        <label for="previous_school_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-school text-gray-400 mr-2"></i>Previous School/College Name
                        </label>
                        <input type="text" name="previous_school_name" id="previous_school_name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('previous_school_name') border-red-500 @enderror"
                               placeholder="Enter previous school/college name" value="{{ old('previous_school_name') }}">
                        @error('previous_school_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SLC/SEE Board -->
                    <div>
                        <label for="slc_see_board" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-certificate text-gray-400 mr-2"></i>SLC/SEE Board
                        </label>
                        <select name="slc_see_board" id="slc_see_board"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('slc_see_board') border-red-500 @enderror">
                            <option value="">Select board</option>
                            <option value="NEB" {{ old('slc_see_board') == 'NEB' ? 'selected' : '' }}>NEB (National Examination Board)</option>
                            <option value="CBSE" {{ old('slc_see_board') == 'CBSE' ? 'selected' : '' }}>CBSE</option>
                            <option value="Other" {{ old('slc_see_board') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('slc_see_board')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SLC/SEE Year -->
                    <div>
                        <label for="slc_see_year" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>SLC/SEE Year
                        </label>
                        <input type="number" name="slc_see_year" id="slc_see_year" min="2000" max="{{ date('Y') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('slc_see_year') border-red-500 @enderror"
                               placeholder="Enter year" value="{{ old('slc_see_year') }}">
                        @error('slc_see_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SLC/SEE Marks -->
                    <div>
                        <label for="slc_see_marks" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-star text-gray-400 mr-2"></i>SLC/SEE Marks/GPA
                        </label>
                        <input type="text" name="slc_see_marks" id="slc_see_marks"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('slc_see_marks') border-red-500 @enderror"
                               placeholder="Enter marks or GPA" value="{{ old('slc_see_marks') }}">
                        @error('slc_see_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- +2/Intermediate Board -->
                    <div>
                        <label for="plus_two_board" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-certificate text-gray-400 mr-2"></i>+2/Intermediate Board
                        </label>
                        <select name="plus_two_board" id="plus_two_board"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('plus_two_board') border-red-500 @enderror">
                            <option value="">Select board</option>
                            <option value="NEB" {{ old('plus_two_board') == 'NEB' ? 'selected' : '' }}>NEB (National Examination Board)</option>
                            <option value="CBSE" {{ old('plus_two_board') == 'CBSE' ? 'selected' : '' }}>CBSE</option>
                            <option value="HSEB" {{ old('plus_two_board') == 'HSEB' ? 'selected' : '' }}>HSEB</option>
                            <option value="Other" {{ old('plus_two_board') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('plus_two_board')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- +2/Intermediate Year -->
                    <div>
                        <label for="plus_two_year" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>+2/Intermediate Year
                        </label>
                        <input type="number" name="plus_two_year" id="plus_two_year" min="2000" max="{{ date('Y') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('plus_two_year') border-red-500 @enderror"
                               placeholder="Enter year" value="{{ old('plus_two_year') }}">
                        @error('plus_two_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- +2/Intermediate Marks -->
                    <div>
                        <label for="plus_two_marks" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-star text-gray-400 mr-2"></i>+2/Intermediate Marks/Percentage
                        </label>
                        <input type="text" name="plus_two_marks" id="plus_two_marks"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('plus_two_marks') border-red-500 @enderror"
                               placeholder="Enter marks or percentage" value="{{ old('plus_two_marks') }}">
                        @error('plus_two_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- +2/Intermediate Stream -->
                    <div>
                        <label for="plus_two_stream" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-stream text-gray-400 mr-2"></i>+2/Intermediate Stream
                        </label>
                        <select name="plus_two_stream" id="plus_two_stream"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('plus_two_stream') border-red-500 @enderror">
                            <option value="">Select stream</option>
                            <option value="Science" {{ old('plus_two_stream') == 'Science' ? 'selected' : '' }}>Science</option>
                            <option value="Management" {{ old('plus_two_stream') == 'Management' ? 'selected' : '' }}>Management</option>
                            <option value="Humanities" {{ old('plus_two_stream') == 'Humanities' ? 'selected' : '' }}>Humanities</option>
                            <option value="Technical" {{ old('plus_two_stream') == 'Technical' ? 'selected' : '' }}>Technical</option>
                            <option value="Other" {{ old('plus_two_stream') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('plus_two_stream')
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
                               placeholder="Enter entrance exam score" value="{{ old('entrance_exam_score') }}">
                        @error('entrance_exam_score')
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
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building text-gray-400 mr-2"></i>Department
                        </label>
                        <select name="department_id" id="department_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('department_id') border-red-500 @enderror">
                            <option value="">Select Department (Optional)</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
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
                                <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
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
                            <option value="entrance_exam" {{ old('mode_of_entry') == 'entrance_exam' ? 'selected' : '' }}>Entrance Exam</option>
                            <option value="direct_entry" {{ old('mode_of_entry') == 'direct_entry' ? 'selected' : '' }}>Direct Entry</option>
                            <option value="transfer" {{ old('mode_of_entry') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        @error('mode_of_entry')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Admission Number Info -->
                    <div class="lg:col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-800">Admission Number</h4>
                                    <p class="text-sm text-blue-700">Will be generated automatically upon creation</p>
                                    <p class="text-xs text-blue-600 mt-1">Format: YYDDDNNNN (e.g., 24CSC0001)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Family Information -->
            <div class="px-8 py-6 space-y-6 border-t border-gray-200" id="section-4">
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
                               placeholder="Enter father's name" value="{{ old('father_name') }}">
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
                               placeholder="Enter father's occupation" value="{{ old('father_occupation') }}">
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
                               placeholder="Enter mother's name" value="{{ old('mother_name') }}">
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
                               placeholder="Enter mother's occupation" value="{{ old('mother_occupation') }}">
                        @error('mother_occupation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Guardian Citizenship Number -->
                    <div>
                        <label for="guardian_citizenship_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card text-gray-400 mr-2"></i>Guardian Citizenship Number
                        </label>
                        <input type="text" name="guardian_citizenship_number" id="guardian_citizenship_number"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('guardian_citizenship_number') border-red-500 @enderror"
                               placeholder="Enter guardian's citizenship number" value="{{ old('guardian_citizenship_number') }}">
                        @error('guardian_citizenship_number')
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
                               placeholder="Enter annual family income" value="{{ old('annual_family_income') }}">
                        @error('annual_family_income')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Legacy Guardian Info (for backward compatibility) -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Emergency Contact Information</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="guardian_name" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
                            <input type="text" name="guardian_name" id="guardian_name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('guardian_name') border-red-500 @enderror"
                                   placeholder="Enter emergency contact name" value="{{ old('guardian_name') }}">
                            @error('guardian_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="guardian_phone" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Phone</label>
                            <input type="text" name="guardian_phone" id="guardian_phone"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('guardian_phone') border-red-500 @enderror"
                                   placeholder="Enter emergency contact phone" value="{{ old('guardian_phone') }}">
                            @error('guardian_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="guardian_email" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Email</label>
                            <input type="email" name="guardian_email" id="guardian_email"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('guardian_email') border-red-500 @enderror"
                                   placeholder="Enter emergency contact email" value="{{ old('guardian_email') }}">
                            @error('guardian_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="guardian_relationship" class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                            <select name="guardian_relationship" id="guardian_relationship"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('guardian_relationship') border-red-500 @enderror">
                                <option value="">Select Relationship</option>
                                <option value="father" {{ old('guardian_relationship') == 'father' ? 'selected' : '' }}>Father</option>
                                <option value="mother" {{ old('guardian_relationship') == 'mother' ? 'selected' : '' }}>Mother</option>
                                <option value="guardian" {{ old('guardian_relationship') == 'guardian' ? 'selected' : '' }}>Guardian</option>
                                <option value="uncle" {{ old('guardian_relationship') == 'uncle' ? 'selected' : '' }}>Uncle</option>
                                <option value="aunt" {{ old('guardian_relationship') == 'aunt' ? 'selected' : '' }}>Aunt</option>
                                <option value="sibling" {{ old('guardian_relationship') == 'sibling' ? 'selected' : '' }}>Sibling</option>
                                <option value="other" {{ old('guardian_relationship') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('guardian_relationship')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-2">
                            <label for="guardian_address" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Address</label>
                            <textarea name="guardian_address" id="guardian_address" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('guardian_address') border-red-500 @enderror"
                                      placeholder="Enter emergency contact address">{{ old('guardian_address') }}</textarea>
                            @error('guardian_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 5: Additional Information -->
            <div class="px-8 py-6 space-y-6 border-t border-gray-200" id="section-5">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-plus-circle mr-3 text-indigo-500"></i>
                        Additional Information
                    </h3>
                    <p class="text-gray-600 mt-1">Scholarship, hostel, medical and other details</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Scholarship Information -->
                    <div class="lg:col-span-2">
                        <label for="scholarship_info" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-award text-gray-400 mr-2"></i>Scholarship Information
                        </label>
                        <textarea name="scholarship_info" id="scholarship_info" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('scholarship_info') border-red-500 @enderror"
                                  placeholder="Enter scholarship details (if applicable)">{{ old('scholarship_info') }}</textarea>
                        @error('scholarship_info')
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
                            <option value="0" {{ old('hostel_required') == '0' ? 'selected' : '' }}>Not Required</option>
                            <option value="1" {{ old('hostel_required') == '1' ? 'selected' : '' }}>Required</option>
                        </select>
                        @error('hostel_required')
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
                               placeholder="Enter preferred subjects" value="{{ old('preferred_subjects') }}">
                        @error('preferred_subjects')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Medical Information -->
                    <div class="lg:col-span-2">
                        <label for="medical_info" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-medkit text-gray-400 mr-2"></i>Medical Information
                        </label>
                        <textarea name="medical_info" id="medical_info" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('medical_info') border-red-500 @enderror"
                                  placeholder="Enter any medical conditions, allergies, or special requirements">{{ old('medical_info') }}</textarea>
                        @error('medical_info')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 6: Document Upload -->
            <div class="px-8 py-6 space-y-6 border-t border-gray-200" id="section-6">
                <div class="border-b border-gray-200 pb-4">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-upload mr-3 text-red-500"></i>
                        Document Upload
                    </h3>
                    <p class="text-gray-600 mt-1">Upload student photo and required documents</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Student Photo -->
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-camera text-gray-400 mr-2"></i>Student Photo
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('photo') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Upload a passport-size photo (JPG, PNG, max 2MB)</p>
                        @error('photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Documents -->
                    <div>
                        <label for="documents" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-alt text-gray-400 mr-2"></i>Supporting Documents
                        </label>
                        <input type="file" name="documents[]" id="documents" multiple accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('documents') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Upload citizenship, certificates, etc. (PDF, JPG, PNG, max 5MB each)</p>
                        @error('documents')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <a href="{{ route('students.index') }}"
                       class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>

                    <div class="flex space-x-3">
                        <button type="button" onclick="saveDraft()"
                                class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Save as Draft
                        </button>

                        <button type="submit"
                                class="inline-flex items-center px-8 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create Student
                        </button>
                    </div>
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

    // Auto-fill temporary address from permanent address
    const permanentAddressField = document.getElementById('permanent_address');
    const temporaryAddressField = document.getElementById('temporary_address');

    permanentAddressField.addEventListener('blur', function() {
        if (!temporaryAddressField.value.trim()) {
            temporaryAddressField.value = this.value;
        }
    });

    // Form validation and progress tracking
    const sections = document.querySelectorAll('[id^="section-"]');
    const progressSteps = document.querySelectorAll('.progress-step');

    function validateSection(sectionIndex) {
        const section = sections[sectionIndex];
        const requiredFields = section.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
            } else {
                field.classList.remove('border-red-500');
            }
        });

        return isValid;
    }

    // File upload preview
    const photoInput = document.getElementById('photo');
    const documentsInput = document.getElementById('documents');

    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Create preview if doesn't exist
                let preview = document.getElementById('photo-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.id = 'photo-preview';
                    preview.className = 'mt-2';
                    photoInput.parentNode.appendChild(preview);
                }
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Photo Preview" class="w-24 h-24 object-cover rounded-lg border">
                    <p class="text-xs text-gray-500 mt-1">${file.name}</p>
                `;
            };
            reader.readAsDataURL(file);
        }
    });

    documentsInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        let preview = document.getElementById('documents-preview');
        if (!preview) {
            preview = document.createElement('div');
            preview.id = 'documents-preview';
            preview.className = 'mt-2 space-y-1';
            documentsInput.parentNode.appendChild(preview);
        }

        preview.innerHTML = files.map(file => `
            <div class="flex items-center text-xs text-gray-600">
                <i class="fas fa-file mr-2"></i>
                ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
            </div>
        `).join('');
    });

    // Save as draft functionality
    window.saveDraft = function() {
        const formData = new FormData(document.querySelector('form'));
        formData.append('is_draft', '1');

        fetch(document.querySelector('form').action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Draft saved successfully!');
            } else {
                alert('Error saving draft. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving draft. Please try again.');
        });
    };

    // Form submission with loading state
    const form = document.querySelector('form');
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Student...';
    });
});
</script>
@endpush

@endsection
