<!-- Top Navigation Bar -->
<div class="sticky top-0 z-30 flex h-16 bg-white/95 backdrop-blur-xl shadow-soft-lg border-b border-gray-200/50">
    <!-- Mobile menu button -->
    <button
        type="button"
        class="mobile-nav-button ripple-button ripple-dark mobile-touch touch-feedback px-3 sm:px-4 text-gray-500 hover:text-brand hover:bg-brand/5 transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-brand/20 lg:hidden rounded-lg mx-1 sm:mx-2 my-2"
        @click="sidebarOpen = true; document.body.classList.add('mobile-sidebar-open')"
    >
        <span class="sr-only">Open sidebar</span>
        <i class="fas fa-bars text-base sm:text-lg"></i>
    </button>

    <div class="flex justify-between flex-1 px-2 sm:px-4 lg:px-6">
        @if(auth()->user()->roles()->exists())
        <!-- Global Search Bar -->
        <div class="flex items-center flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg">
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
                       class="block w-full pl-8 sm:pl-10 pr-8 sm:pr-10 py-2 sm:py-3 border border-gray-200 rounded-lg sm:rounded-xl leading-5 bg-white/80 backdrop-blur-sm placeholder-gray-400 focus:outline-none focus:placeholder-gray-300 focus:ring-2 focus:ring-brand/20 focus:border-brand/30 text-xs sm:text-sm font-medium shadow-soft-sm transition-all duration-200"
                       placeholder="Search... (Ctrl+K)"
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
                     class="absolute z-50 w-full sm:w-96 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-80 sm:max-h-96 overflow-y-auto responsive-dropdown"
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
        <div class="flex items-center space-x-1 sm:space-x-2 lg:space-x-3">
            <!-- Notifications -->
            <div class="relative hidden sm:block">
                <button class="p-2 sm:p-3 text-gray-400 hover:text-brand hover:bg-brand/5 focus:outline-none focus:ring-2 focus:ring-brand/20 rounded-lg sm:rounded-xl transition-all duration-200 group">
                    <i class="fas fa-bell text-sm sm:text-base lg:text-lg group-hover:animate-bounce-gentle"></i>
                    <span class="absolute top-1 sm:top-2 right-1 sm:right-2 block h-2 w-2 sm:h-2.5 sm:w-2.5 bg-gradient-to-r from-red-400 to-red-500 rounded-full ring-1 sm:ring-2 ring-white shadow-sm animate-pulse"></span>
                </button>
            </div>

            <!-- Settings -->
            <div class="relative hidden md:block">
                <button class="p-2 sm:p-3 text-gray-400 hover:text-brand hover:bg-brand/5 focus:outline-none focus:ring-2 focus:ring-brand/20 rounded-lg sm:rounded-xl transition-all duration-200 group">
                    <i class="fas fa-cog text-sm sm:text-base lg:text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                </button>
            </div>
        @else
        <!-- No Role Message for users without roles -->
        <div class="flex items-center flex-1">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-2 sm:px-4 py-2 mx-auto max-w-xs sm:max-w-none">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-1 sm:mr-2 text-sm"></i>
                    <span class="text-yellow-800 text-xs sm:text-sm font-medium">Contact owner for access</span>
                </div>
            </div>
        </div>

        <!-- Right side items for users without roles (minimal) -->
        <div class="flex items-center space-x-1 sm:space-x-2 lg:space-x-3">
        @endif

            <!-- User dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button
                    type="button"
                    class="flex items-center space-x-1 sm:space-x-2 lg:space-x-3 text-sm rounded-lg sm:rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/20 bg-white/80 backdrop-blur-sm border border-gray-200/50 px-2 sm:px-3 py-2 hover:bg-white hover:shadow-soft-md transition-all duration-200 group"
                    @click="open = !open"
                >
                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gradient-to-br from-brand to-brand-dark rounded-md sm:rounded-lg flex items-center justify-center text-white shadow-soft-sm group-hover:scale-105 transition-transform duration-200">
                        <span class="font-semibold text-xs sm:text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-xs sm:text-sm font-medium text-gray-700 group-hover:text-gray-900 truncate max-w-24 lg:max-w-none">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">
                            @if(!auth()->user()->roles()->exists())
                                No Role
                            @elseif(auth()->user()->hasRole('Super Admin'))
                                Super Admin
                            @elseif(auth()->user()->hasRole('Admin'))
                                Admin
                            @elseif(auth()->user()->hasRole('Teacher'))
                                Teacher
                            @elseif(auth()->user()->hasRole('Examiner'))
                                Examiner
                            @elseif(auth()->user()->hasRole('Accountant'))
                                Accountant
                            @else
                                User
                            @endif
                        </p>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-gray-600 transition-all duration-200 hidden sm:block" :class="{ 'rotate-180': open }"></i>
                </button>

                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="transform opacity-0 scale-95 translate-y-1"
                    x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="transform opacity-0 scale-95 translate-y-1"
                    @click.away="open = false"
                    class="origin-top-right absolute right-0 mt-2 sm:mt-3 w-48 sm:w-56 rounded-lg sm:rounded-xl shadow-soft-xl bg-white/95 backdrop-blur-xl ring-1 ring-gray-200/50 border border-gray-200/50 focus:outline-none responsive-dropdown"
                    style="display: none;"
                >
                    <div class="py-2">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>

                        @if(auth()->user()->roles()->exists())
                        <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-brand/5 hover:text-brand transition-all duration-200 group">
                            <i class="fas fa-user-circle mr-3 text-gray-400 group-hover:text-brand"></i>
                            My Profile
                        </a>
                        <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-brand/5 hover:text-brand transition-all duration-200 group">
                            <i class="fas fa-cog mr-3 text-gray-400 group-hover:text-brand"></i>
                            Settings
                        </a>
                        <a href="#" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-brand/5 hover:text-brand transition-all duration-200 group">
                            <i class="fas fa-bell mr-3 text-gray-400 group-hover:text-brand"></i>
                            Notifications
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        @else
                        <!-- No Role Message in dropdown -->
                        <div class="px-4 py-3">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <div class="flex items-center justify-center mb-2">
                                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                </div>
                                <p class="text-yellow-800 text-xs text-center font-medium mb-1">Access Restricted</p>
                                <p class="text-yellow-700 text-xs text-center">Contact the owner to view the dashboard</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 my-1"></div>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-all duration-200 group">
                                <i class="fas fa-sign-out-alt mr-3 text-red-400 group-hover:text-red-600"></i>
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