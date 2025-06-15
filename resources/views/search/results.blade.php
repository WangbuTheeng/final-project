@extends('layouts.dashboard')

@section('title', 'Search Results')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Search Results</h1>
            <p class="mt-1 text-sm text-gray-500">
                Found {{ $totalResults }} results for "<strong>{{ $query }}</strong>"
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ url()->previous() }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <form method="GET" action="{{ route('search.results') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="q" 
                       value="{{ $query }}" 
                       placeholder="Search across all modules..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>
            <div>
                <select name="type" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Categories</option>
                    <option value="users" {{ $type === 'users' ? 'selected' : '' }}>Users</option>
                    <option value="students" {{ $type === 'students' ? 'selected' : '' }}>Students</option>
                    <option value="faculties" {{ $type === 'faculties' ? 'selected' : '' }}>Faculties</option>
                    <option value="departments" {{ $type === 'departments' ? 'selected' : '' }}>Departments</option>
                    <option value="courses" {{ $type === 'courses' ? 'selected' : '' }}>Courses</option>
                </select>
            </div>
            <button type="submit" 
                    class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-search mr-2"></i>
                Search
            </button>
        </form>
    </div>

    @if($totalResults === 0)
        <!-- No Results -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-12 text-center">
            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No results found</h3>
            <p class="text-gray-500 mb-6">
                We couldn't find anything matching "<strong>{{ $query }}</strong>". 
                Try adjusting your search terms or search in a different category.
            </p>
            <div class="space-x-3">
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-home mr-2"></i>
                    Go to Dashboard
                </a>
                <button onclick="document.querySelector('input[name=q]').focus()" 
                        class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-search mr-2"></i>
                    Try Again
                </button>
            </div>
        </div>
    @else
        <!-- Results -->
        <div class="space-y-6">
            @if(isset($results['users']) && $results['users']->count() > 0)
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-users text-blue-500 mr-2"></i>
                            Users ({{ $results['users']->total() }})
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($results['users'] as $user)
                            <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">
                                                    <a href="{{ route('users.show', $user) }}" class="hover:text-blue-600">
                                                        {{ $user->name }}
                                                    </a>
                                                </h4>
                                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                                <p class="text-xs text-gray-400">
                                                    Roles: {{ $user->roles->pluck('name')->implode(', ') }}
                                                </p>
                                            </div>
                                            <div class="text-xs text-gray-400">#{{ $user->id }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($results['users']->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $results['users']->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            @endif

            @if(isset($results['students']) && $results['students']->count() > 0)
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-user-graduate text-green-500 mr-2"></i>
                            Students ({{ $results['students']->total() }})
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($results['students'] as $student)
                            <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600 font-medium text-sm">{{ strtoupper(substr($student->user->name ?? 'ST', 0, 2)) }}</span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">
                                                    <a href="{{ route('students.show', $student) }}" class="hover:text-green-600">
                                                        {{ $student->user->name ?? 'Unknown Student' }}
                                                    </a>
                                                </h4>
                                                <p class="text-sm text-gray-500">{{ $student->admission_number }}</p>
                                                <p class="text-xs text-gray-400">
                                                    Department: {{ $student->department->name ?? 'No Department' }}
                                                </p>
                                            </div>
                                            <div class="text-xs text-gray-400">#{{ $student->id }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($results['students']->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $results['students']->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
