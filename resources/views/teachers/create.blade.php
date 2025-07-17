@extends('layouts.dashboard')

@section('title', 'Add New Teacher')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add New Teacher</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-2">Create a new teacher account and profile</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('teachers.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Teachers
                </a>
            </div>
        </div>
    </div>

    <!-- Modern Form Container -->
    <x-forms.container 
        title="Teacher Registration" 
        subtitle="Fill in the teacher details below"
        icon="fas fa-chalkboard-teacher"
        action="{{ route('teachers.store') }}" 
        method="POST"
    >
        <!-- Personal Information Section -->
        <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-user mr-3 text-blue-500"></i>
                    Personal Information
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Basic personal details of the teacher</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- First Name -->
                <x-forms.input
                    name="first_name"
                    label="First Name"
                    icon="fas fa-user"
                    :required="true"
                    placeholder="Enter first name"
                    help="Teacher's legal first name"
                />

                <!-- Last Name -->
                <x-forms.input
                    name="last_name"
                    label="Last Name"
                    icon="fas fa-user"
                    :required="true"
                    placeholder="Enter last name"
                    help="Teacher's legal last name"
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
                    help="Contact number for the teacher"
                />

                <!-- Date of Birth -->
                <x-forms.input
                    name="date_of_birth"
                    type="date"
                    label="Date of Birth"
                    icon="fas fa-calendar"
                    help="Teacher's date of birth"
                />

                <!-- Gender -->
                <x-forms.select
                    name="gender"
                    label="Gender"
                    icon="fas fa-venus-mars"
                    placeholder="Select gender"
                    :options="[
                        ['value' => 'male', 'text' => 'Male'],
                        ['value' => 'female', 'text' => 'Female'],
                        ['value' => 'other', 'text' => 'Other']
                    ]"
                    help="Teacher's gender identity"
                />
            </div>
            
            <!-- Address (Full Width) -->
            <x-forms.textarea
                name="address"
                label="Address"
                :rows="3"
                placeholder="Enter full address"
                help="Teacher's residential address"
                :auto-resize="true"
            />
        </div>

        <!-- Professional Information Section -->
        <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-briefcase mr-3 text-emerald-500"></i>
                    Professional Information
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Academic and professional details</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Employee ID Info -->
                <div class="lg:col-span-2">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-200">Employee ID</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300">Will be generated automatically upon creation</p>
                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Format: EMP-YYYY-NNNN (e.g., EMP-2024-0001)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department -->
                <x-forms.select
                    name="department_id"
                    label="Department"
                    icon="fas fa-building"
                    :required="true"
                    placeholder="Select Department"
                    :searchable="true"
                    :options="[]"
                    help="Choose the department for this teacher"
                />

                <!-- Position -->
                <x-forms.select
                    name="position"
                    label="Position"
                    icon="fas fa-user-tie"
                    :required="true"
                    placeholder="Select Position"
                    :options="[
                        ['value' => 'professor', 'text' => 'Professor'],
                        ['value' => 'associate_professor', 'text' => 'Associate Professor'],
                        ['value' => 'assistant_professor', 'text' => 'Assistant Professor'],
                        ['value' => 'lecturer', 'text' => 'Lecturer'],
                        ['value' => 'instructor', 'text' => 'Instructor']
                    ]"
                    help="Academic position/rank"
                />

                <!-- Qualification -->
                <x-forms.input
                    name="qualification"
                    label="Highest Qualification"
                    icon="fas fa-graduation-cap"
                    :required="true"
                    placeholder="e.g., PhD in Computer Science"
                    help="Highest academic qualification"
                />

                <!-- Experience -->
                <x-forms.input
                    name="experience_years"
                    type="number"
                    label="Years of Experience"
                    icon="fas fa-clock"
                    placeholder="Enter years of experience"
                    help="Total years of teaching experience"
                />

                <!-- Specialization -->
                <x-forms.input
                    name="specialization"
                    label="Specialization"
                    icon="fas fa-star"
                    placeholder="e.g., Machine Learning, Database Systems"
                    help="Areas of expertise and specialization"
                />

                <!-- Salary -->
                <x-forms.input
                    name="salary"
                    type="number"
                    label="Monthly Salary"
                    icon="fas fa-dollar-sign"
                    placeholder="Enter monthly salary"
                    help="Monthly salary amount"
                />
            </div>
        </div>
        
        <!-- Form Actions -->
        <x-slot name="actions">
            <div class="flex items-center justify-between">
                <a href="{{ route('teachers.index') }}" 
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
                        icon="fas fa-chalkboard-teacher"
                        size="lg"
                    >
                        Create Teacher
                    </x-forms.button>
                </div>
            </div>
        </x-slot>
    </x-forms.container>
</div>
@endsection
