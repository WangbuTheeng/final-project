<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'College CMS') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ url('/') }}" class="text-xl font-semibold text-gray-900">
                                {{ config('app.name', 'College CMS') }}
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    {{ __('Dashboard') }}
                                </a>
                                
                                @can('view-users')
                                <a href="{{ route('users.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    {{ __('Users') }}
                                </a>
                                @endcan
                                
                                @can('view-roles')
                                <a href="{{ route('roles.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    {{ __('Roles') }}
                                </a>
                                @endcan
                                
                                @can('manage-settings')
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                        {{ __('Settings') }}
                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="settingsDropdown">
                                            <a href="{{ route('academic-years.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                {{ __('Academic Years') }}
                                            </a>
                                            <!-- Add more settings links as needed -->
                                        </div>
                                    </div>
                                </div>
                                @endcan
                                
                                @can('view-exams')
                                <a href="{{ route('exams.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    {{ __('Exams') }}
                                </a>
                                @endcan

                                <!-- Nepal University Examinations -->
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                        🇳🇵 Examinations
                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                        <div class="py-1" role="menu" aria-orientation="vertical">
                                            <a href="{{ route('examinations.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                All Examinations
                                            </a>
                                            <a href="{{ route('examinations.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                Schedule Exam
                                            </a>
                                            <div class="border-t border-gray-100"></div>
                                            <a href="{{ route('examinations.index', ['assessment_type' => 'internal']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                Internal Assessment (40%)
                                            </a>
                                            <a href="{{ route('examinations.index', ['assessment_type' => 'final']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                Final Examination (60%)
                                            </a>
                                            <a href="{{ route('examinations.index', ['exam_type' => 'supplementary']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                Supplementary Exams
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                @can('view-finances')
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                        {{ __('Finance') }}
                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="financeDropdown">
                                            <a href="{{ route('finance.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                {{ __('Dashboard') }}
                                            </a>
                                            <a href="{{ route('finance.fees.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                {{ __('Fees') }}
                                            </a>
                                            <a href="{{ route('finance.invoices.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                {{ __('Invoices') }}
                                            </a>
                                            <a href="{{ route('finance.payments.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                {{ __('Payments') }}
                                            </a>
                                            @can('manage-salaries')
                                            <a href="{{ route('finance.salaries.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                {{ __('Salaries') }}
                                            </a>
                                            @endcan
                                            <a href="{{ route('finance.expenses.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                {{ __('Expenses') }}
                                            </a>
                                            @can('view-financial-reports')
                                            <a href="{{ route('finance.reports.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                {{ __('Reports') }}
                                            </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                                @endcan
                            @endauth
                        </div>
                    </div>

                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <a class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium" href="{{ route('login') }}">{{ __('Login') }}</a>
                            @endif

                            @if (Route::has('register'))
                                <a class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium ml-4" href="{{ route('register') }}">{{ __('Register') }}</a>
                            @endif
                        @else
                            <div x-data="{ open: false }" class="relative ml-3">
                                <div>
                                    <button @click="open = !open" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Open user menu</span>
                                        <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </button>
                                </div>
                                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" style="display: none;">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        <i class="fas fa-user-circle mr-2"></i> {{ __('My Profile') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                            <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                    <div class="-mr-2 flex items-center sm:hidden">
                        <!-- Mobile menu button -->
                        <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu, show/hide based on menu state. -->
            <div class="sm:hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Dashboard') }}
                        </a>
                        @can('view-users')
                        <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Users') }}
                        </a>
                        @endcan
                        @can('view-roles')
                        <a href="{{ route('roles.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Roles') }}
                        </a>
                        @endcan
                        @can('manage-settings')
                        <a href="{{ route('academic-years.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Academic Years') }}
                        </a>
                        @endcan
                        @can('view-exams')
                        <a href="{{ route('exams.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Exams') }}
                        </a>
                        @endcan
                        @can('view-finances')
                        <a href="{{ route('finance.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Finance Dashboard') }}
                        </a>
                        <a href="{{ route('finance.fees.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Fees') }}
                        </a>
                        <a href="{{ route('finance.invoices.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Invoices') }}
                        </a>
                        <a href="{{ route('finance.payments.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Payments') }}
                        </a>
                        @can('manage-salaries')
                        <a href="{{ route('finance.salaries.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Salaries') }}
                        </a>
                        @endcan
                        <a href="{{ route('finance.expenses.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Expenses') }}
                        </a>
                        @can('view-financial-reports')
                        <a href="{{ route('finance.reports.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            {{ __('Reports') }}
                        </a>
                        @endcan
                        @endcan
                    @endauth
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    @auth
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <span class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-sm font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                            <i class="fas fa-user-circle mr-2"></i> {{ __('My Profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Logout') }}
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="">
            @yield('content')
        </main>
    </div>
</body>
</html>
