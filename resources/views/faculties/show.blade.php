@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $faculty->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Faculty details and information</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('faculties.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Faculties
            </a>
            <a href="{{ route('faculties.edit', $faculty) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i>
                Edit Faculty
            </a>
        </div>
    </div>

    <!-- Faculty Information Card -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Faculty Information</h3>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $faculty->code }}
                    </span>
                    @if($faculty->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>
                            Inactive
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Faculty Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $faculty->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Faculty Code</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $faculty->code }}</p>
                    </div>

                    @if($faculty->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Description</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $faculty->description }}</p>
                        </div>
                    @endif

                    @if($faculty->location)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Location</label>
                            <p class="mt-1 text-sm text-gray-900 flex items-center">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                {{ $faculty->location }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Contact & Leadership -->
                <div class="space-y-4">
                    <!-- Dean Information -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Dean</label>
                        @if($faculty->dean)
                            <div class="mt-1 flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($faculty->dean->name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $faculty->dean->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $faculty->dean->email }}</p>
                                </div>
                            </div>
                        @else
                            <p class="mt-1 text-sm text-gray-400 italic">No dean assigned</p>
                        @endif
                    </div>

                    <!-- Contact Information -->
                    @if($faculty->email || $faculty->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Contact Information</label>
                            <div class="mt-1 space-y-2">
                                @if($faculty->email)
                                    <p class="text-sm text-gray-900 flex items-center">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                        <a href="mailto:{{ $faculty->email }}" class="text-primary-600 hover:text-primary-800">
                                            {{ $faculty->email }}
                                        </a>
                                    </p>
                                @endif
                                @if($faculty->phone)
                                    <p class="text-sm text-gray-900 flex items-center">
                                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                                        <a href="tel:{{ $faculty->phone }}" class="text-primary-600 hover:text-primary-800">
                                            {{ $faculty->phone }}
                                        </a>
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Timestamps -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $faculty->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>

                    @if($faculty->updated_at != $faculty->created_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $faculty->updated_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Section -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Departments</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ $faculty->departments->count() }} departments
                </span>
            </div>
        </div>

        @if($faculty->departments->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($faculty->departments as $department)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $department->name }}</h4>
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $department->code }}
                                    </span>
                                    @if($department->is_active)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                                @if($department->description)
                                    <p class="mt-1 text-sm text-gray-500">{{ Str::limit($department->description, 100) }}</p>
                                @endif
                                @if($department->hod)
                                    <p class="mt-1 text-xs text-gray-400">
                                        HOD: {{ $department->hod->name }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('departments.show', $department) }}" 
                                   class="text-primary-600 hover:text-primary-900 text-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-building text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No departments</h3>
                <p class="mt-1 text-sm text-gray-500">This faculty doesn't have any departments yet.</p>
                <div class="mt-6">
                    <a href="{{ route('departments.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>
                        Add Department
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
