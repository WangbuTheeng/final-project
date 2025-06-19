@extends('layouts.dashboard')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">My Profile</h1>
                <p class="text-gray-600">Manage your personal information and account settings</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Profile
                </a>
                <a href="{{ route('profile.password') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-key mr-2"></i>
                    Change Password
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Profile Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8">
            <div class="flex items-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-2xl font-bold text-blue-600">{{ substr($user->name, 0, 1) }}</span>
                </div>
                <div class="ml-6 text-white">
                    <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                    <p class="text-blue-100">{{ $user->email }}</p>
                    <div class="mt-2">
                        @if($user->roles->isNotEmpty())
                            @foreach($user->roles as $role)
                                <span class="inline-block bg-blue-400 text-white text-xs px-2 py-1 rounded-full mr-2">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        @else
                            <span class="inline-block bg-yellow-400 text-yellow-900 text-xs px-2 py-1 rounded-full">
                                No Role Assigned
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Full Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->name ?: 'Not provided' }}</p>
                        </div>
                        
                        @if($user->first_name || $user->last_name)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">First Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->first_name ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->last_name ?: 'Not provided' }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Gender</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($user->gender)
                                    {{ ucfirst(str_replace('_', ' ', $user->gender)) }}
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Date of Birth</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($user->date_of_birth)
                                    {{ \Carbon\Carbon::parse($user->date_of_birth)->format('F j, Y') }}
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->phone ?: $user->contact_number ?: 'Not provided' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Address</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->address ?: 'Not provided' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Account Status</label>
                            <p class="mt-1">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        {{ ucfirst($user->status) }}
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Login</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($user->last_login_at)
                                    {{ \Carbon\Carbon::parse($user->last_login_at)->format('F j, Y g:i A') }}
                                @else
                                    Never
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Member Since</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information for Students/Teachers -->
            @if($user->student || $user->teacher)
            <div class="mt-8 pt-6 border-t border-gray-200">
                @if($user->student)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Student ID</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->student->student_id ?? 'Not assigned' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Admission Number</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->student->admission_number ?? 'Not assigned' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($user->teacher)
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Teacher Information</h3>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Employee ID</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->teacher->employee_id ?? 'Not assigned' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Department</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->teacher->department->name ?? 'Not assigned' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
