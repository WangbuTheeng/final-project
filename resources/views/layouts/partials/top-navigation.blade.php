<!-- Top Navigation Bar -->
<div class="sticky top-0 z-10 flex h-16 bg-white shadow-sm border-b border-gray-200">
    <button
        type="button"
        class="px-4 text-gray-500 hover:text-teal-600 hover:bg-gray-50 transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500 lg:hidden"
        @click="sidebarOpen = true"
    >
        <span class="sr-only">Open sidebar</span>
        <i class="fas fa-bars"></i>
    </button>

    <div class="flex justify-between flex-1 px-6">
        <!-- Global Search Bar -->
        <div class="flex items-center flex-1 max-w-lg">
            <div class="relative w-full" x-data="globalSearch()">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>

                <!-- Loading Indicator -->
                <div x-show="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
                </div>

                <!-- Keyboard Shortcut Hint -->
                <div x-show="query.length === 0 && !loading"
                     class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded font-mono">Ctrl+K</span>
                </div>

                <!-- Clear Button -->
                <div x-show="query.length > 0 && !loading"
                     @click="clearSearch()"
                     class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                    <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
                </div>

                <input type="text"
                       x-model="query"
                       @input.debounce.300ms="search()"
                       @keydown.enter="goToResults()"
                       @keydown.escape="hideSuggestions()"
                       @keydown.arrow-down.prevent="navigateDown()"
                       @keydown.arrow-up.prevent="navigateUp()"
                       @focus="showSuggestions = true"
                       class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 text-sm"
                       style="--tw-ring-color: #37a2bc; --tw-border-opacity: 1; border-color: rgb(209 213 219);"
                       onfocus="this.style.borderColor='#37a2bc'; this.style.boxShadow='0 0 0 1px #37a2bc';"
                       onblur="setTimeout(() => { if (!this.closest('[x-data]').__x.$data.showSuggestions) { this.style.borderColor='rgb(209 213 219)'; this.style.boxShadow='none'; } }, 200);"
                       placeholder="Search users, students, courses... (Ctrl+K)"
                       autocomplete="off">

                <!-- Search Suggestions Dropdown -->
                <div x-show="showSuggestions && (Object.keys(results).length > 0 || query.length >= 2)"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     @click.away="hideSuggestions()"
                     class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-96 overflow-y-auto"
                     style="display: none;">

                    <!-- Search Header -->
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-gray-700">
                                <i class="fas fa-search mr-2"></i>
                                <span x-text="query.length >= 2 ? 'Search Results' : 'Start typing to search...'"></span>
                            </div>
                            <div class="text-xs text-gray-500">
                                <span class="px-2 py-1 bg-gray-200 rounded font-mono">Enter</span> for all results
                            </div>
                        </div>
                    </div>

                    <!-- No Results -->
                    <div x-show="query.length >= 2 && Object.keys(results).length === 0 && !loading"
                         class="px-4 py-6 text-center text-gray-500">
                        <i class="fas fa-search text-gray-300 text-2xl mb-2"></i>
                        <div class="text-sm">No results found</div>
                    </div>

                    <!-- Results by Category -->
                    <template x-for="(category, key) in results" :key="key">
                        <div class="border-b border-gray-100 last:border-b-0">
                            <!-- Category Header -->
                            <div class="px-4 py-2 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                        <i :class="category.icon" class="mr-2"></i>
                                        <span x-text="category.title"></span>
                                    </div>
                                    <a :href="category.view_all_url"
                                       class="text-xs text-blue-600 hover:text-blue-800">
                                        View All
                                    </a>
                                </div>
                            </div>

                            <!-- Category Items -->
                            <template x-for="(item, index) in category.items" :key="index">
                                <a :href="item.url"
                                   class="block px-4 py-3 hover:bg-gray-50 transition-colors duration-150"
                                   @click="hideSuggestions()">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center text-white text-xs font-medium"
                                             :class="{
                                                 'bg-blue-500': item.type === 'user',
                                                 'bg-green-500': item.type === 'student',
                                                 'bg-purple-500': item.type === 'faculty',
                                                 'bg-orange-500': item.type === 'department',
                                                 'bg-red-500': item.type === 'course'
                                             }">
                                            <span x-text="item.avatar"></span>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="text-sm font-medium text-gray-900" x-text="item.title"></div>
                                            <div class="text-xs text-gray-500" x-text="item.subtitle"></div>
                                            <div class="text-xs text-gray-400" x-text="item.description"></div>
                                        </div>
                                        <div class="ml-2">
                                            <i class="fas fa-arrow-right text-gray-300 text-xs"></i>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </template>

                    <!-- View All Results Footer -->
                    <div x-show="Object.keys(results).length > 0"
                         class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                        <button @click="goToResults()"
                                class="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fas fa-search mr-2"></i>
                            View all results for "<span x-text="query"></span>"
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side items -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative">
                <button class="p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 rounded-lg" style="--tw-ring-color: #37a2bc;" onfocus="this.style.boxShadow='0 0 0 2px #37a2bc';" onblur="this.style.boxShadow='none';">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white" style="background-color: #37a2bc;"></span>
                </button>
            </div>

            <!-- Settings -->
            <div class="relative">
                <button class="p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 rounded-lg" style="--tw-ring-color: #37a2bc;" onfocus="this.style.boxShadow='0 0 0 2px #37a2bc';" onblur="this.style.boxShadow='none';">
                    <i class="fas fa-cog text-lg"></i>
                </button>
            </div>

            <!-- User dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button
                    type="button"
                    class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2"
                    style="--tw-ring-color: #37a2bc;"
                    onfocus="this.style.boxShadow='0 0 0 2px #37a2bc';"
                    onblur="this.style.boxShadow='none';"
                    @click="open = !open"
                >
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white" style="background-color: #37a2bc;">
                        <span class="font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                </button>

                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    @click.away="open = false"
                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                    style="display: none;"
                >
                    <div class="py-1">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Profile
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function globalSearch() {
    return {
        query: '',
        results: {},
        showSuggestions: false,
        loading: false,
        selectedIndex: -1,

        search() {
            if (this.query.length < 2) {
                this.results = {};
                this.showSuggestions = false;
                return;
            }

            this.loading = true;
            this.showSuggestions = true;

            fetch(`{{ route('global.search') }}?q=${encodeURIComponent(this.query)}&limit=3`)
                .then(response => response.json())
                .then(data => {
                    this.results = data;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Search error:', error);
                    this.loading = false;
                    this.results = {};
                });
        },

        clearSearch() {
            this.query = '';
            this.results = {};
            this.showSuggestions = false;
            this.selectedIndex = -1;
        },

        hideSuggestions() {
            this.showSuggestions = false;
            this.selectedIndex = -1;
        },

        goToResults() {
            if (this.query.length >= 2) {
                window.location.href = `{{ route('search.results') }}?q=${encodeURIComponent(this.query)}`;
            }
        },

        navigateDown() {
            // Implementation for keyboard navigation can be added here
            this.selectedIndex++;
        },

        navigateUp() {
            // Implementation for keyboard navigation can be added here
            this.selectedIndex--;
        }
    }
}

// Global keyboard shortcut for search
document.addEventListener('keydown', function(e) {
    // Ctrl+K or Cmd+K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('[x-data="globalSearch()"] input');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }

    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.querySelector('[x-data="globalSearch()"] input');
        if (searchInput && document.activeElement === searchInput) {
            searchInput.blur();
        }
    }
});
</script>