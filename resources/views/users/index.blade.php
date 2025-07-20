@extends('layouts.dashboard')

@section('title', 'User Management')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
            <p class="mt-1 text-sm text-gray-500">Manage system users and their roles</p>
        </div>
        <div class="mt-4 sm:mt-0">
            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>
                Create New User
            </a>
            @endif
        </div>
    </div>

    <!-- Advanced Search Section -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <i class="fas fa-search mr-2 text-blue-500"></i>
            Advanced Search
        </h3>

        <form method="GET" action="{{ route('users.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search Input with Auto-suggestions -->
                <div class="relative">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                    <div class="relative">
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Type name, email, phone, role..."
                               class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                               autocomplete="off">

                        <!-- Search Icon -->
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>

                        <!-- Loading Indicator -->
                        <div id="search-loading" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
                        </div>

                        <!-- Clear Button -->
                        <div id="search-clear" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden cursor-pointer">
                            <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
                        </div>

                        <div id="search-suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Search by name, email, phone, or role. Minimum 2 characters for suggestions.
                        <span class="ml-2 px-2 py-1 bg-gray-100 rounded text-xs font-mono">Ctrl+K</span>
                    </div>
                </div>

                <!-- Role Filter -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Filter by Role</label>
                    <select name="role" id="role" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                    </select>
                </div>
            </div>

            <!-- Search Actions -->
            <div class="flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-search mr-2"></i>
                    Search
                </button>

                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-times mr-2"></i>
                    Clear
                </a>

                @if(request()->hasAny(['search', 'role', 'status']))
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i>
                        Showing filtered results
                        @if(request('search'))
                            for "<strong>{{ request('search') }}</strong>"
                        @endif
                    </div>
                @endif

                <!-- Keyboard Shortcuts Info -->
                <div class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-keyboard mr-1"></i>
                    <strong>Tips:</strong> Use ↑↓ to navigate suggestions, Enter to select, Esc to close
                </div>
            </div>
        </form>
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

    <!-- Results Summary -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <div class="flex items-center space-x-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-users text-primary-500 mr-2"></i> Users
                </h2>
                <div class="text-sm text-gray-600">
                    <span class="font-medium">{{ $users->total() }}</span>
                    {{ $users->total() === 1 ? 'user' : 'users' }} found
                    @if(request()->hasAny(['search', 'role', 'status']))
                        <span class="text-blue-600">
                            (filtered)
                        </span>
                    @endif
                </div>
            </div>

            @if(request()->hasAny(['search', 'role', 'status']))
                <div class="mt-2 sm:mt-0">
                    <a href="{{ route('users.index') }}"
                       class="inline-flex items-center px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-full transition-colors duration-150">
                        <i class="fas fa-times mr-1"></i>
                        Clear Filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white overflow-hidden shadow-soft-xl sm:rounded-lg animate-fade-in">
        <div class="p-6 bg-white border-b border-gray-100">
        <div class="overflow-hidden rounded-lg border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Roles</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out @if($loop->even) bg-gray-50/50 @endif">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600">
                                    #{{ $user->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                                            <span class="text-primary-600 font-medium text-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">User ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ $user->contact_number ?: 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            @php
                                                $colors = [
                                                    'Super Admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                    'Admin' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                    'Teacher' => 'bg-green-100 text-green-800 border-green-200',
                                                    'Student' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                    'Accountant' => 'bg-orange-100 text-orange-800 border-orange-200',
                                                    'Examiner' => 'bg-red-100 text-red-800 border-red-200',
                                                ];
                                                $colorClass = $colors[$role->name] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                            @endphp
                                            <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-md border {{ $colorClass }}">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('users.show', $user) }}"
                                           class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-md transition-colors duration-150 ease-in-out"
                                           title="View User">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-md transition-colors duration-150 ease-in-out"
                                           title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if(auth()->id() != $user->id)
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-md transition-colors duration-150 ease-in-out"
                                                    onclick="return confirm('Are you sure you want to delete this user?')"
                                                    title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-search text-gray-300 text-4xl mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                                        <p class="text-gray-500 mb-4">
                                            @if(request()->hasAny(['search', 'role', 'status']))
                                                Try adjusting your search criteria or filters.
                                            @else
                                                No users have been created yet.
                                            @endif
                                        </p>
                                        @if(request()->hasAny(['search', 'role', 'status']))
                                            <a href="{{ route('users.index') }}"
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                <i class="fas fa-times mr-2"></i>
                                                Clear Filters
                                            </a>
                                        @else
                                            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                                <a href="{{ route('users.create') }}"
                                                   class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    <i class="fas fa-plus mr-2"></i>
                                                    Create First User
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">
            <div class="px-4 py-3 bg-white border border-gray-200 rounded-lg shadow-sm">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const suggestionsContainer = document.getElementById('search-suggestions');
    const loadingIndicator = document.getElementById('search-loading');
    const clearButton = document.getElementById('search-clear');
    let searchTimeout;

    // Show/hide clear button based on input
    function updateClearButton() {
        if (searchInput.value.trim().length > 0) {
            clearButton.classList.remove('hidden');
        } else {
            clearButton.classList.add('hidden');
        }
    }

    // Clear button functionality
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        hideSuggestions();
        updateClearButton();
        searchInput.focus();
    });

    // Auto-suggestions functionality
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        updateClearButton();

        // Clear previous timeout
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            hideSuggestions();
            hideLoading();
            return;
        }

        // Show loading indicator
        showLoading();

        // Debounce search requests
        searchTimeout = setTimeout(() => {
            fetchSuggestions(query);
        }, 300);
    });

    // Initialize clear button state
    updateClearButton();

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            hideSuggestions();
        }
    });

    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const suggestions = suggestionsContainer.querySelectorAll('.suggestion-item');
        const activeSuggestion = suggestionsContainer.querySelector('.suggestion-item.active');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (activeSuggestion) {
                activeSuggestion.classList.remove('active');
                const next = activeSuggestion.nextElementSibling;
                if (next) {
                    next.classList.add('active');
                } else {
                    suggestions[0]?.classList.add('active');
                }
            } else {
                suggestions[0]?.classList.add('active');
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (activeSuggestion) {
                activeSuggestion.classList.remove('active');
                const prev = activeSuggestion.previousElementSibling;
                if (prev) {
                    prev.classList.add('active');
                } else {
                    suggestions[suggestions.length - 1]?.classList.add('active');
                }
            } else {
                suggestions[suggestions.length - 1]?.classList.add('active');
            }
        } else if (e.key === 'Enter') {
            if (activeSuggestion) {
                e.preventDefault();
                activeSuggestion.click();
            }
        } else if (e.key === 'Escape') {
            hideSuggestions();
        }
    });

    function showLoading() {
        loadingIndicator.classList.remove('hidden');
        clearButton.classList.add('hidden');
    }

    function hideLoading() {
        loadingIndicator.classList.add('hidden');
        updateClearButton();
    }

    function fetchSuggestions(query) {
        fetch(`{{ route('users.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                hideLoading();
                displaySuggestions(data);
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                hideLoading();
                hideSuggestions();
            });
    }

    function displaySuggestions(suggestions) {
        if (suggestions.length === 0) {
            // Show "no results" message
            suggestionsContainer.innerHTML = `
                <div class="px-4 py-3 text-center text-gray-500">
                    <i class="fas fa-search text-gray-300 mb-2"></i>
                    <div class="text-sm">No users found</div>
                </div>
            `;
            suggestionsContainer.classList.remove('hidden');
            return;
        }

        let html = `
            <div class="px-4 py-2 bg-gray-50 border-b border-gray-100">
                <div class="text-xs text-gray-600 font-medium">
                    <i class="fas fa-users mr-1"></i>
                    ${suggestions.length} user${suggestions.length !== 1 ? 's' : ''} found
                </div>
            </div>
        `;

        suggestions.forEach((suggestion, index) => {
            const highlightedName = highlightMatch(suggestion.name, searchInput.value);
            const highlightedEmail = highlightMatch(suggestion.email, searchInput.value);

            html += `
                <div class="suggestion-item px-4 py-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0 ${index === 0 ? 'active' : ''}"
                     data-name="${suggestion.name}"
                     data-email="${suggestion.email}">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-medium text-xs">${suggestion.name.substring(0, 2).toUpperCase()}</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm font-medium text-gray-900">${highlightedName}</div>
                            <div class="text-xs text-gray-500">${highlightedEmail}</div>
                        </div>
                        <div class="ml-auto flex items-center space-x-2">
                            <span class="text-xs text-gray-400">#${suggestion.id}</span>
                            <i class="fas fa-arrow-right text-gray-300 text-xs"></i>
                        </div>
                    </div>
                </div>
            `;
        });

        suggestionsContainer.innerHTML = html;
        suggestionsContainer.classList.remove('hidden');

        // Add click handlers to suggestions
        suggestionsContainer.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function() {
                const name = this.dataset.name;
                searchInput.value = name;
                hideSuggestions();
                // Optionally submit the form automatically
                // searchInput.closest('form').submit();
            });

            item.addEventListener('mouseenter', function() {
                suggestionsContainer.querySelectorAll('.suggestion-item').forEach(s => s.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }

    function highlightMatch(text, query) {
        if (!query || query.length < 2) return text;

        const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return text.replace(regex, '<mark class="bg-yellow-200 px-1 rounded">$1</mark>');
    }

    function hideSuggestions() {
        suggestionsContainer.classList.add('hidden');
        suggestionsContainer.innerHTML = '';
    }

    // Global keyboard shortcut: Ctrl+K or Cmd+K to focus search
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });
});
</script>

<style>
.suggestion-item.active {
    background-color: #f3f4f6;
}

#search-suggestions {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>
@endpush
