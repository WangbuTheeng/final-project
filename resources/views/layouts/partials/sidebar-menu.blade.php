<!-- College CMS Header - Only show on desktop, mobile has header in sidebar container -->
<div class="hidden lg:block p-4 sm:p-6 text-white" style="background-color: #37a2bc;">
    <div class="text-center mb-3 sm:mb-4">
        <h2 class="text-lg sm:text-xl font-bold">College CMS</h2>
        <div class="w-12 sm:w-16 h-0.5 bg-white mx-auto mt-2 opacity-50"></div>
    </div>
    <div class="flex items-center space-x-2 sm:space-x-3">
        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-lg flex items-center justify-center flex-shrink-0">
            <span class="font-bold text-base sm:text-lg" style="color: #37a2bc;">{{ substr(auth()->user()->name, 0, 1) }}</span>
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="font-semibold text-base sm:text-lg truncate">{{ auth()->user()->name }}</h3>
            <p class="text-white text-xs sm:text-sm opacity-90 truncate">
                @if(!auth()->user()->roles()->exists())
                    NO ROLE ASSIGNED
                @elseif(auth()->user()->hasRole('Super Admin'))
                    SUPER ADMIN
                @elseif(auth()->user()->hasRole('Admin'))
                    ADMIN
                @elseif(auth()->user()->hasRole('Teacher'))
                    TEACHER
                @elseif(auth()->user()->hasRole('Examiner'))
                    EXAMINER
                @elseif(auth()->user()->hasRole('Accountant'))
                    ACCOUNTANT
                @else
                    USER
                @endif
            </p>
        </div>
    </div>
</div>

<!-- Mobile User Info -->
<div class="lg:hidden p-4 border-b border-gray-200 bg-gray-50">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
            <span class="font-bold text-sm text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
        </div>
        <div class="min-w-0 flex-1">
            <h3 class="font-semibold text-sm text-gray-900 truncate">{{ auth()->user()->name }}</h3>
            <p class="text-xs text-gray-500 truncate">
                @if(!auth()->user()->roles()->exists())
                    No Role Assigned
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
    </div>
</div>

<!-- Navigation Menu -->
<div class="py-3 sm:py-4">
    <ul class="space-y-1">
        @if(auth()->user()->roles()->exists())
        <li>
            <!-- Dashboard - visible to all users -->
            <a href="{{ route('dashboard') }}" class="ripple-container mobile-touch {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-500' : 'text-gray-700 hover:bg-gray-50' }} group flex items-center px-4 py-3 text-sm font-medium transition-all duration-150 ease-in-out {{ request()->routeIs('dashboard') ? '' : 'hover:text-gray-900' }} rounded-r-lg mx-2">
                <i class="fas fa-tachometer-alt mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400' }} {{ request()->routeIs('dashboard') ? '' : 'group-hover:text-gray-600' }}"></i>
                <span class="truncate">Dashboard</span>
            </a>
        </li>

        <!-- Academic Structure -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher'))
        <li>
            <div x-data="{ open: {{ request()->routeIs('academic-years.*', 'faculties.*', 'courses.*', 'classes.*', 'departments.*', 'subjects.*', 'college-settings.*', 'grading-systems.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button
                    @click="open = !open"
                    class="ripple-button mobile-touch {{ request()->routeIs('academic-years.*', 'faculties.*', 'courses.*', 'classes.*', 'departments.*', 'subjects.*', 'college-settings.*', 'grading-systems.*') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-500' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group w-full flex items-center px-4 py-3 text-sm font-medium transition-all duration-150 ease-in-out rounded-r-lg mx-2"
                >
                    <i class="fas fa-university mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('academic-years.*', 'faculties.*', 'courses.*', 'classes.*', 'departments.*', 'subjects.*', 'college-settings.*', 'grading-systems.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="flex-1 text-left truncate">Academic Structure</span>
                    <i class="fas transition-transform duration-200 text-sm {{ request()->routeIs('academic-years.*', 'faculties.*', 'courses.*', 'classes.*', 'departments.*', 'subjects.*', 'college-settings.*', 'grading-systems.*') ? 'text-blue-600' : 'text-gray-400' }}" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
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

                    @if(auth()->user()->can('view-courses') || auth()->user()->hasRole('Teacher'))
                    <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('courses.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-book mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('courses.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Courses
                    </a>
                    @endif

                    @can('view-classes')
                    <a href="{{ route('classes.index') }}" class="{{ request()->routeIs('classes.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('classes.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-chalkboard mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('classes.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Classes
                    </a>
                    @endcan

                    @if(auth()->user()->can('view-subjects') || auth()->user()->hasRole('Teacher'))
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

                    @if(auth()->user()->can('view-students') || auth()->user()->hasRole('Teacher'))
                    <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.index') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('students.index') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-list mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('students.index') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        All Students
                    </a>
                    @endif

                    @can('manage-students')
                    <a href="{{ route('students.create') }}" class="{{ request()->routeIs('students.create') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('students.create') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-user-plus mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('students.create') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Add New Student
                    </a>
                    @endcan

                    
                    @can('manage-enrollments')
                    <a href="{{ route('enrollments.index') }}" class="{{ request()->routeIs('enrollments.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('enrollments.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-clipboard-list mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('enrollments.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Enrollments
                    </a>

                    <a href="{{ route('enrollments.bulk-create') }}" class="{{ request()->routeIs('enrollments.bulk-create') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('enrollments.bulk-create') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-users-cog mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('enrollments.bulk-create') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Bulk Enrollment
                    </a>
                    @endcan

                    @if(auth()->user()->can('view-students') || auth()->user()->hasRole('Teacher'))
                    <a href="#" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out">
                        <i class="fas fa-chart-line mr-3 flex-shrink-0 h-4 w-4 text-gray-400 group-hover:text-gray-600"></i>
                        Student Reports
                    </a>
                    @endif

                    @can('manage-students')
                    <a href="#" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out">
                        <i class="fas fa-upload mr-3 flex-shrink-0 h-4 w-4 text-gray-400 group-hover:text-gray-600"></i>
                        Bulk Import
                    </a>
                    @endcan
                </div>
            </div>
        </li>
        @endif



        <!-- Exam Management -->
        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Teacher') || auth()->user()->hasRole('Examiner'))
        <li>
            <div x-data="{ open: {{ request()->routeIs('exams.*', 'bulk-marks.*', 'grades.*', 'marks.*', 'marksheets.*', 'results.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button
                    @click="open = !open"
                    class="{{ request()->routeIs('exams.*', 'bulk-marks.*', 'grades.*', 'marks.*', 'marksheets.*', 'results.*') ? 'text-white border-r-3' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }} group w-full flex items-center px-6 py-3 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('exams.*', 'bulk-marks.*', 'grades.*', 'marks.*', 'marksheets.*', 'results.*') ? 'style=background-color:#37a2bc;border-right-color:#37a2bc;' : '' }}
                >
                    <i class="fas fa-file-alt mr-3 flex-shrink-0 h-5 w-5 {{ request()->routeIs('exams.*', 'bulk-marks.*', 'grades.*', 'marks.*', 'marksheets.*', 'results.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="flex-1 text-left">Exam Management</span>
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
                     {{ request()->routeIs('exams.*', 'bulk-marks.*', 'grades.*', 'marks.*', 'marksheets.*', 'results.*') ? '' : 'style="display: none;"' }}>

                    <a href="{{ route('exams.index') }}" class="{{ request()->routeIs('exams.*') && !request()->routeIs('bulk-marks.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('exams.*') && !request()->routeIs('bulk-marks.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-file-alt mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('exams.*') && !request()->routeIs('bulk-marks.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        @if(auth()->user()->hasRole('Teacher'))
                            Upcoming Exams
                        @else
                            Exams
                        @endif
                    </a>

                    @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                    <a href="{{ route('bulk-marks.index') }}" class="{{ request()->routeIs('bulk-marks.*') ? 'text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-6 py-2 text-sm font-medium transition-all duration-150 ease-in-out" {{ request()->routeIs('bulk-marks.*') ? 'style=background-color:#37a2bc;' : '' }}>
                        <i class="fas fa-table mr-3 flex-shrink-0 h-4 w-4 {{ request()->routeIs('bulk-marks.*') ? 'text-white' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                        Bulk Marks Entry
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
        @if(auth()->user()->can('view-finances') && !auth()->user()->hasRole('Admin'))
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
        @if(auth()->user()->hasRole('Super Admin'))
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

                    @if(auth()->user()->hasRole('Super Admin'))
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
