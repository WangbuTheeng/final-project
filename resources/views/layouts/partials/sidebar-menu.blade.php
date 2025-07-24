<!-- Modern College CMS Header - Desktop -->
<div class="hidden lg:block p-6 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 relative overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent"></div>
    <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/5 rounded-full blur-2xl animate-pulse-soft"></div>
    <div class="absolute -bottom-6 -left-6 w-40 h-40 bg-white/5 rounded-full blur-2xl animate-float"></div>

    <!-- Floating Particles -->
    <div class="absolute top-4 right-8 w-2 h-2 bg-white/30 rounded-full animate-bounce-subtle"></div>
    <div class="absolute top-12 right-16 w-1 h-1 bg-white/40 rounded-full animate-pulse-soft"></div>
    <div class="absolute bottom-8 right-6 w-1.5 h-1.5 bg-white/20 rounded-full animate-float"></div>

    <div class="relative z-10">
        <!-- Brand Section -->
        <div class="text-center mb-6">
            <div class="relative inline-block">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-large group hover:scale-110 transition-all duration-300">
                    <i class="fas fa-graduation-cap text-white text-2xl group-hover:rotate-12 transition-transform duration-300"></i>
                    <div class="absolute -inset-1 bg-gradient-to-r from-white/20 to-white/10 rounded-2xl blur opacity-75 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2 tracking-wide">College CMS</h2>
            <div class="w-20 h-0.5 bg-gradient-to-r from-transparent via-white/60 to-transparent mx-auto"></div>
        </div>

        <!-- User Profile Card -->
        <div class="glass-effect rounded-2xl p-4 border border-white/20 hover:border-white/30 transition-all duration-300 group">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="w-14 h-14 bg-gradient-to-br from-white/25 to-white/15 rounded-xl flex items-center justify-center flex-shrink-0 shadow-medium group-hover:scale-105 transition-transform duration-300">
                        <span class="font-bold text-xl text-white">
                            {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                        </span>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-success-400 rounded-full border-2 border-white status-online"></div>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-lg text-white truncate mb-1">
                        {{ auth()->user()->first_name ?? auth()->user()->name }} {{ auth()->user()->last_name ?? '' }}
                    </h3>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white border border-white/30">
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
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Mobile User Info -->
<div class="lg:hidden p-4 border-b border-gray-100 bg-gradient-to-r from-primary-50 via-white to-primary-50">
    <div class="flex items-center space-x-3">
        <div class="relative">
            <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-medium">
                <span class="font-bold text-sm text-white">
                    {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                </span>
            </div>
            <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-success-400 rounded-full border-2 border-white"></div>
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="font-semibold text-sm text-gray-900 truncate">
                {{ auth()->user()->first_name ?? auth()->user()->name }} {{ auth()->user()->last_name ?? '' }}
            </h3>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 mt-1">
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
                    Accountant
                @else
                    User
                @endif
            </span>
        </div>
    </div>
</div>

<!-- Global Search Section -->
<div class="p-4 border-b border-gray-200/50">
    <div x-data="{
        searchOpen: false,
        searchQuery: '',
        searchResults: [],
        isLoading: false,
        async performSearch() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                return;
            }

            this.isLoading = true;
            try {
                const response = await fetch(`/search?q=${encodeURIComponent(this.searchQuery)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                this.searchResults = data.results || [];
            } catch (error) {
                console.error('Search error:', error);
                this.searchResults = [];
            } finally {
                this.isLoading = false;
            }
        }
    }" class="relative">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 text-sm"></i>
            </div>
            <input
                x-model="searchQuery"
                @input.debounce.300ms="performSearch()"
                @focus="searchOpen = true"
                @keydown.escape="searchOpen = false; searchQuery = ''"
                type="text"
                placeholder="Search students, teachers, courses..."
                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            >
            <div x-show="isLoading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-2 border-blue-500 border-t-transparent"></div>
            </div>
        </div>

        <!-- Search Results Dropdown -->
        <div
            x-show="searchOpen && (searchResults.length > 0 || searchQuery.length > 0)"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            @click.away="searchOpen = false"
            class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-200 z-50 max-h-80 overflow-y-auto"
            style="display: none;"
        >
            <template x-if="searchResults.length > 0">
                <div class="p-2">
                    <template x-for="result in searchResults" :key="result.id">
                        <a :href="result.url" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors duration-150 group">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center mr-3" :class="result.type === 'student' ? 'bg-blue-100 text-blue-600' : result.type === 'teacher' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600'">
                                <i :class="result.type === 'student' ? 'fas fa-user-graduate' : result.type === 'teacher' ? 'fas fa-chalkboard-teacher' : 'fas fa-book'" class="text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="result.title"></p>
                                <p class="text-xs text-gray-500 truncate" x-text="result.subtitle"></p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-arrow-right text-gray-400 text-xs group-hover:text-gray-600"></i>
                            </div>
                        </a>
                    </template>
                </div>
            </template>

            <template x-if="searchQuery.length > 0 && searchResults.length === 0 && !isLoading">
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-search text-2xl mb-2 opacity-50"></i>
                    <p class="text-sm">No results found</p>
                </div>
            </template>
        </div>
    </div>
</div>

<!-- Navigation Menu -->
<div class="py-4 px-3">
    <ul class="space-y-2">
        @if(auth()->user()->roles()->exists())
        <li>
            <!-- Dashboard - visible to all users -->
            <a href="{{ route('dashboard') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/25' : 'text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:text-gray-900' }}">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 {{ request()->routeIs('dashboard') ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-white' }} transition-all duration-200">
                    <i class="fas fa-tachometer-alt text-sm {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500 group-hover:text-blue-600' }}"></i>
                </div>
                <span class="truncate">Dashboard</span>
                @if(request()->routeIs('dashboard'))
                    <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                @endif
            </a>
        </li>

        <!-- Academic Structure -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'))
        <li>
            <div x-data="{ open: {{ request()->routeIs('academic-years.*', 'faculties.*', 'courses.*', 'classes.*', 'departments.*', 'subjects.*', 'college-settings.*', 'grading-systems.*') ? 'true' : 'false' }} }" class="space-y-2">
                <button
                    @click="open = !open"
                    class="group w-full flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('academic-years.*', 'faculties.*', 'courses.*', 'classes.*', 'departments.*', 'subjects.*', 'college-settings.*', 'grading-systems.*') ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/25' : 'text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:text-gray-900' }}"
                >
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg mr-3 {{ request()->routeIs('academic-years.*', 'faculties.*', 'courses.*', 'classes.*', 'departments.*', 'subjects.*', 'college-settings.*', 'grading-systems.*') ? 'bg-white/20' : 'bg-gray-100 group-hover:bg-white' }} transition-all duration-200">
                        <i class="fas fa-university text-sm {{ request()->routeIs('academic-years.*', 'faculties.*', 'courses.*', 'classes.*', 'departments.*', 'subjects.*', 'college-settings.*', 'grading-systems.*') ? 'text-white' : 'text-gray-500 group-hover:text-emerald-600' }}"></i>
                    </div>
                    <span class="flex-1 text-left truncate">Academic Structure</span>
                    <div class="flex items-center">
                        @if(request()->routeIs('academic-years.*', 'faculties.*', 'courses.*', 'classes.*', 'departments.*', 'subjects.*', 'college-settings.*', 'grading-systems.*'))
                            <div class="w-2 h-2 bg-white rounded-full mr-2"></div>
                        @endif
                        <i class="fas transition-transform duration-200 text-sm {{ request()->routeIs('academic-years.*', 'faculties.*', 'courses.*', 'classes.*', 'departments.*', 'subjects.*', 'college-settings.*', 'grading-systems.*') ? 'text-white' : 'text-gray-400 group-hover:text-emerald-600' }}" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                    </div>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 -translate-y-2"
                     x-transition:enter-end="transform opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="transform opacity-100 translate-y-0"
                     x-transition:leave-end="transform opacity-0 -translate-y-2"
                     class="pl-12 space-y-1"
                     {{ request()->routeIs('academic-years.*', 'faculties.*', 'courses.*', 'classes.*', 'departments.*', 'subjects.*', 'college-settings.*', 'grading-systems.*') ? '' : 'style="display: none;"' }}>
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                    <a href="{{ route('academic-years.index') }}" class="{{ request()->routeIs('academic-years.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('academic-years.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-calendar-alt mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('academic-years.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Academic Years
                    </a>
                    @endif

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'))
                    <a href="{{ route('faculties.index') }}" class="{{ request()->routeIs('faculties.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('faculties.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-university mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('faculties.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Faculties
                    </a>
                    @endif

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'))
                    <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('courses.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-book mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('courses.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Courses
                    </a>
                    @endif

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'))
                    <a href="{{ route('classes.index') }}" class="{{ request()->routeIs('classes.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('classes.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-chalkboard-teacher mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('classes.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Classes
                    </a>
                    @endif

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'))
                    <a href="{{ route('subjects.index') }}" class="{{ request()->routeIs('subjects.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('subjects.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-book-open mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('subjects.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Subjects
                    </a>
                    @endif

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                    <a href="{{ route('departments.index') }}" class="{{ request()->routeIs('departments.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('departments.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-building mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('departments.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Departments
                        <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 border">Optional</span>
                    </a>

                    <a href="{{ route('college-settings.index') }}" class="{{ request()->routeIs('college-settings.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('college-settings.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-cog mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('college-settings.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        College Settings
                    </a>

                    <a href="{{ route('grading-systems.index') }}" class="{{ request()->routeIs('grading-systems.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('grading-systems.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-graduation-cap mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('grading-systems.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Grading Systems
                    </a>
                    @endif
                </div>
            </div>
        </li>
        @endif


        <!-- Student Management -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher') || auth()->user()->hasRole('Accountant'))
        <li>
            <div x-data="{ open: {{ request()->routeIs('students.*', 'enrollments.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button
                    @click="open = !open"
                    class="{{ request()->routeIs('students.*', 'enrollments.*') ? 'text-white border-r-3' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group w-full flex items-center px-6 py-3 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('students.*', 'enrollments.*') ? 'style=background-color:#37a2bc;border-right-color:#37a2bc;' : '' }}
                >
                    <i class="fas fa-user-graduate mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('students.*', 'enrollments.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="flex-1 text-left">Student Management</span>
                    <i class="fas transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 -translate-y-2"
                     x-transition:enter-end="transform opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="transform opacity-100 translate-y-0"
                     x-transition:leave-end="transform opacity-0 -translate-y-2"
                     class="pl-12 space-y-1"
                     {{ request()->routeIs('students.*', 'enrollments.*') ? '' : 'style="display: none;"' }}>

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'))
                    <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.index') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('students.index') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-list mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('students.index') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        All Students
                    </a>
                    @endif

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                    <a href="{{ route('students.create') }}" class="{{ request()->routeIs('students.create') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('students.create') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-user-plus mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('students.create') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Add New Student
                    </a>
                    @endif

                    
                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                    <a href="{{ route('enrollments.index') }}" class="{{ request()->routeIs('enrollments.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('enrollments.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-clipboard-list mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('enrollments.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Enrollments
                    </a>

                    <a href="{{ route('enrollments.bulk-create') }}" class="{{ request()->routeIs('enrollments.bulk-create') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('enrollments.bulk-create') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-users-cog mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('enrollments.bulk-create') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Bulk Enrollment
                    </a>
                    @endif

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'))
                    <a href="#" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out">
                        <i class="fas fa-chart-line mr-3 flex-shrink-0 h-4 w-4 text-gray-400 group-hover:text-gray-600"></i>
                        Student Reports
                    </a>
                    @endif

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                    <a href="#" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out">
                        <i class="fas fa-upload mr-3 flex-shrink-0 h-4 w-4 text-gray-400 group-hover:text-gray-600"></i>
                        Bulk Import
                    </a>
                    @endif
                </div>
            </div>
        </li>
        @endif



        <!-- 🇳🇵 Nepal University Examination System -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher') || auth()->user()->hasRole('Examiner'))
        <li>
            <div x-data="{ open: {{ request()->routeIs('examinations.*', 'exam-results.*', 'exams.*', 'bulk-marks.*', 'grades.*', 'marks.*', 'marksheets.*', 'results.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button
                    @click="open = !open"
                    class="{{ request()->routeIs('examinations.*', 'exam-results.*', 'exams.*', 'bulk-marks.*', 'grades.*', 'marks.*', 'marksheets.*', 'results.*') ? 'text-white border-r-3' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group w-full flex items-center px-6 py-3 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('examinations.*', 'exam-results.*', 'exams.*', 'bulk-marks.*', 'grades.*', 'marks.*', 'marksheets.*', 'results.*') ? 'style=background-color:#37a2bc;border-right-color:#37a2bc;' : '' }}
                >
                    <i class="fas fa-clipboard-list mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('examinations.*', 'exam-results.*', 'exams.*', 'bulk-marks.*', 'grades.*', 'marks.*', 'marksheets.*', 'results.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="flex-1 text-left">🇳🇵 Examinations</span>
                    <i class="fas transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 -translate-y-2"
                     x-transition:enter-end="transform opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="transform opacity-100 translate-y-0"
                     x-transition:leave-end="transform opacity-0 -translate-y-2"
                     class="pl-12 space-y-1"
                     {{ request()->routeIs('examinations.*', 'exam-results.*', 'exams.*', 'bulk-marks.*', 'grades.*', 'marks.*', 'marksheets.*', 'results.*') ? '' : 'style="display: none;"' }}>

                    <!-- Nepal University Examination System -->
                    <a href="{{ route('examinations.index') }}" class="{{ request()->routeIs('examinations.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('examinations.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-clipboard-list mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('examinations.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        All Examinations
                    </a>

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                    <a href="{{ route('examinations.create') }}" class="{{ request()->routeIs('examinations.create') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('examinations.create') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-plus mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('examinations.create') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Schedule Exam
                    </a>
                    @endif

                    <!-- Assessment Types -->
                    <div class="border-t border-gray-200 my-2"></div>
                    <div class="px-6 py-1">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Assessment Types</p>
                    </div>

                    <a href="{{ route('examinations.index', ['assessment_type' => 'internal']) }}" class="{{ request()->get('assessment_type') === 'internal' ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->get('assessment_type') === 'internal' ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-edit mr-3 flex-shrink-0 h-4 w-4 {{ request()->get('assessment_type') === 'internal' ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Internal (40%)
                    </a>

                    <a href="{{ route('examinations.index', ['assessment_type' => 'final']) }}" class="{{ request()->get('assessment_type') === 'final' ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->get('assessment_type') === 'final' ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-graduation-cap mr-3 flex-shrink-0 h-4 w-4 {{ request()->get('assessment_type') === 'final' ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Final (60%)
                    </a>

                    <a href="{{ route('examinations.index', ['exam_type' => 'supplementary']) }}" class="{{ request()->get('exam_type') === 'supplementary' ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->get('exam_type') === 'supplementary' ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-redo mr-3 flex-shrink-0 h-4 w-4 {{ request()->get('exam_type') === 'supplementary' ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Supplementary
                    </a>

                    {{-- Legacy System (commented out - use new examination system) --}}
                    {{--
                    <div class="border-t border-gray-200 my-2"></div>
                    <div class="px-6 py-1">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Legacy System</p>
                    </div>

                    <a href="{{ route('exams.index') }}" class="{{ request()->routeIs('exams.*') && !request()->routeIs('bulk-marks.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('exams.*') && !request()->routeIs('bulk-marks.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-file-alt mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('exams.*') && !request()->routeIs('bulk-marks.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Old Exam System
                    </a>
                    --}}

                    <!-- Results & Reports -->
                    <div class="border-t border-gray-200 my-2"></div>
                    <div class="px-6 py-1">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Results & Reports</p>
                    </div>

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                    <a href="{{ route('marksheets.index') }}" class="{{ request()->routeIs('marksheets.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('marksheets.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-certificate mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('marksheets.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Marksheets
                    </a>

                    <a href="{{ route('results.index') }}" class="{{ request()->routeIs('results.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('results.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-chart-bar mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('results.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Results Dashboard
                    </a>
                    @endif
<!--
                    <a href="{{ route('grades.index') }}" class="{{ request()->routeIs('grades.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('grades.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-graduation-cap mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('grades.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Grades
                    </a>

                    <a href="{{ route('grades.bulk-entry') }}" class="{{ request()->routeIs('grades.bulk-entry') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('grades.bulk-entry') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-edit mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('grades.bulk-entry') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Bulk Grade Entry
                    </a> -->

                    <!-- <a href="{{ route('marks.index') }}" class="{{ request()->routeIs('marks.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('marks.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-pencil-alt mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('marks.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Marks Entry
                    </a> -->

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                    <a href="{{ route('marksheets.index') }}" class="{{ request()->routeIs('marksheets.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('marksheets.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-file-pdf mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('marksheets.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Generate Marksheets
                    </a>
                    @endif

                    <a href="{{ route('results.index') }}" class="{{ request()->routeIs('results.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('results.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-chart-line mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('results.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Results & Analytics
                    </a>
                </div>
            </div>
        </li>
        @endif

        <!-- Finance Management -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Accountant') || auth()->user()->can('view-finances'))
        <li>
            <div x-data="{ open: {{ request()->routeIs('finance.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button
                    @click="open = !open"
                    class="{{ request()->routeIs('finance.*') ? 'text-white border-r-3' : 'text-gray-700 hover:bg-gray-50 hover:text-teal-600' }} group w-full flex items-center px-6 py-3 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('finance.*') ? 'style=background-color:#37a2bc;border-right-color:#37a2bc;' : '' }}
                >
                    <i class="fas fa-money-bill-wave mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('finance.*') ? 'text-white' : 'text-gray-400 group-hover:text-teal-500' }}"></i>
                    <span class="flex-1 text-left">Finance Management</span>
                    <i class="fas transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 -translate-y-2"
                     x-transition:enter-end="transform opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="transform opacity-100 translate-y-0"
                     x-transition:leave-end="transform opacity-0 -translate-y-2"
                     class="pl-12 space-y-1"
                     {{ request()->routeIs('finance.*') ? '' : 'style="display: none;"' }}>

                    <a href="{{ route('finance.dashboard') }}" class="{{ request()->routeIs('finance.dashboard') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('finance.dashboard') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-tachometer-alt mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('finance.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('finance.fees.index') }}" class="{{ request()->routeIs('finance.fees.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('finance.fees.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-tags mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('finance.fees.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Fee Management
                    </a>

                    <a href="{{ route('finance.invoices.index') }}" class="{{ request()->routeIs('finance.invoices.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('finance.invoices.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-file-invoice mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('finance.invoices.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Invoices
                    </a>

                    <a href="{{ route('finance.payments.index') }}" class="{{ request()->routeIs('finance.payments.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('finance.payments.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-credit-card mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('finance.payments.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Payments
                    </a>

                    @can('manage-salaries')
                    <a href="{{ route('finance.teachers.index') }}" class="{{ request()->routeIs('finance.teachers.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('finance.teachers.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-chalkboard-teacher mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('finance.teachers.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Teachers
                    </a>

                    <a href="{{ route('finance.salaries.index') }}" class="{{ request()->routeIs('finance.salaries.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('finance.salaries.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-money-check-alt mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('finance.salaries.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Salary Payments
                    </a>
                    @endcan

                    @can('view-financial-reports')
                    <a href="{{ route('finance.reports.index') }}" class="{{ request()->routeIs('finance.reports.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('finance.reports.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-chart-line mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('finance.reports.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Reports
                    </a>
                    @endcan
                </div>
            </div>
        </li>
        @endif





        <!-- User Management -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
        <li>
            <div x-data="{ open: {{ request()->routeIs('users.*', 'roles.*', 'permissions.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button
                    @click="open = !open"
                    class="{{ request()->routeIs('users.*', 'roles.*', 'permissions.*') ? 'text-white border-r-3' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group w-full flex items-center px-6 py-3 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('users.*', 'roles.*', 'permissions.*') ? 'style=background-color:#37a2bc;border-right-color:#37a2bc;' : '' }}
                >
                    <i class="fas fa-users mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('users.*', 'roles.*', 'permissions.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="flex-1 text-left">User Management</span>
                    <i class="fas transition-transform duration-200" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 -translate-y-2"
                     x-transition:enter-end="transform opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="transform opacity-100 translate-y-0"
                     x-transition:leave-end="transform opacity-0 -translate-y-2"
                     class="pl-12 space-y-1"
                     {{ request()->routeIs('users.*', 'roles.*', 'permissions.*') ? '' : 'style="display: none;"' }}>

                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('users.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-users mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('users.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Users
                    </a>

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                    <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('roles.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-user-tag mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('roles.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Roles
                    </a>

                    <a href="{{ route('permissions.index') }}" class="{{ request()->routeIs('permissions.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('permissions.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-key mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('permissions.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Permissions
                    </a>
                    @endif
                </div>
            </div>
        </li>
        @endif

        <!-- Activity Logs -->
        @if(auth()->user()->hasRole('Super Admin'))
        <li>
            <a href="{{ route('activity-logs.index') }}" class="{{ request()->routeIs('activity-logs.*') ? 'text-white border-r-3' : 'text-gray-700 hover:bg-gray-50' }} group flex items-center px-6 py-3 text-sm font-medium transition-all duration-150 ease-in-out {{ request()->routeIs('activity-logs.*') ? '' : 'hover:text-gray-900' }}" {{ request()->routeIs('activity-logs.*') ? 'style=background-color:#37a2bc;border-right-color:#37a2bc;' : '' }}>
                <i class="fas fa-history mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('activity-logs.*') ? 'text-white' : 'text-gray-400' }} {{ request()->routeIs('activity-logs.*') ? '' : 'group-hover:text-gray-600' }}"></i>
                Activity Logs
            </a>
        </li>
        @endif

        <!-- Divider -->
        <li>
            <div class="border-t border-gray-200 my-2"></div>
        </li>

        <!-- My Profile -->
        <li>
            <a href="{{ route('profile.show') }}" class="ripple-container mobile-touch {{ request()->routeIs('profile.*') ? 'text-white border-r-3' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-3 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('profile.*') ? 'style=background-color:#37a2bc;border-right-color:#37a2bc;' : '' }}>
                <i class="fas fa-user-circle mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('profile.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                My Profile
            </a>
        </li>

        <!-- Logout -->
        <li>
            <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="ripple-button mobile-touch text-gray-700 hover:bg-gray-50 hover:text-red-600 group flex items-center px-6 py-3 text-sm font-medium w-full text-left transition-all duration-150 ease-in-out">
                    <i class="fas fa-sign-out-alt mr-3 flex-shrink-0 h-5 w-5 text-gray-400 group-hover:text-red-500"></i>
                    Logout
                </button>
            </form>
        </li>

        @else
        <!-- No Role Assigned - Show only logout -->
        <li>
            <div class="px-6 py-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-center mb-2">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-lg"></i>
                    </div>
                    <p class="text-yellow-800 text-sm text-center font-medium mb-1">Access Restricted</p>
                    <p class="text-yellow-700 text-xs text-center">Contact the owner to view the dashboard</p>
                </div>
            </div>
        </li>

        <!-- Logout for users without roles -->
        <li>
            <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="text-gray-700 hover:bg-gray-50 hover:text-red-600 group flex items-center px-6 py-3 text-sm font-medium w-full text-left transition-all duration-150 ease-in-out">
                    <i class="fas fa-sign-out-alt mr-3 flex-shrink-0 h-5 w-5 text-gray-400 group-hover:text-red-500"></i>
                    Logout
                </button>
            </form>
        </li>
        @endif

    </ul>
</div>
