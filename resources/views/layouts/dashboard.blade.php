<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'College CMS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js (from CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS (from CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#37a2bc',
                            600: '#2d8299',
                            700: '#256b7a',
                            800: '#1e5661',
                            900: '#1a4851',
                            950: '#0f2a30',
                        },
                        'dashboard-bg': '#f3f4f6',
                        'brand': '#37a2bc',
                        'brand-light': '#4db3cc',
                        'brand-dark': '#2d8299',
                    },
                    boxShadow: {
                        'soft-sm': '0 2px 4px 0 rgba(0, 0, 0, 0.05)',
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                        'soft-md': '0 6px 10px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                        'soft-lg': '0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.2s ease-in-out',
                        'slide-in-right': 'slideInRight 0.3s ease-in-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideInRight: {
                            '0%': { transform: 'translateX(100%)' },
                            '100%': { transform: 'translateX(0)' },
                        },
                    },
                },
            },
        }
    </script>
</head>
<body class="font-sans antialiased" style="background-color: #f3f4f6;">
    <div class="min-h-screen">
        <!-- Mobile sidebar backdrop -->
        <div x-data="{ sidebarOpen: false }">
            <div 
                x-show="sidebarOpen" 
                x-transition:enter="transition-opacity ease-linear duration-300" 
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" 
                x-transition:leave="transition-opacity ease-linear duration-300" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0" 
                class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false"
                style="display: none;"
            ></div>

            <!-- Mobile sidebar -->
            <div 
                x-show="sidebarOpen" 
                x-transition:enter="transition ease-in-out duration-300 transform" 
                x-transition:enter-start="-translate-x-full" 
                x-transition:enter-end="translate-x-0" 
                x-transition:leave="transition ease-in-out duration-300 transform" 
                x-transition:leave-start="translate-x-0" 
                x-transition:leave-end="-translate-x-full" 
                class="fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-soft-lg lg:hidden animate-slide-in-right"
                style="display: none;"
            >
                <div class="flex items-center justify-end h-16 px-4 border-b border-gray-100">
                    <button @click="sidebarOpen = false" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-full transition-colors duration-150 ease-in-out">
                        <span class="sr-only">Close sidebar</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <!-- Mobile sidebar content -->
                <div class="overflow-y-auto h-[calc(100vh-4rem)] px-1">
                    @include('layouts.partials.sidebar-menu')
                </div>
            </div>

            <!-- Static sidebar for desktop -->
            <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-72 lg:flex-col">
                <div class="flex flex-col flex-1 min-h-0 bg-white shadow-soft-lg h-full">
                    <div class="flex flex-col flex-1 overflow-y-auto">
                        @include('layouts.partials.sidebar-menu')
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="lg:pl-72">
                <!-- Top navigation -->
                @include('layouts.partials.top-navigation')

                <!-- Main content area -->
                <main class="p-4 lg:p-8 animate-fade-in" style="background-color: #f3f4f6;">
                    <!-- Success Messages -->
                    @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 px-4 py-3 rounded-md bg-green-50 text-green-800 border-l-4 border-green-500 shadow-soft-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-green-600 hover:text-green-800 ml-4">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Status Messages -->
                    @if(session('status'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 px-4 py-3 rounded-md bg-green-50 text-green-800 border-l-4 border-green-500 shadow-soft-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <span>{{ session('status') }}</span>
                            </div>
                            <button @click="show = false" class="text-green-600 hover:text-green-800 ml-4">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Error Messages -->
                    @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" class="mb-6 px-4 py-3 rounded-md bg-red-50 text-red-800 border-l-4 border-red-500 shadow-soft-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button @click="show = false" class="text-red-600 hover:text-red-800 ml-4">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Warning Messages -->
                    @if(session('warning'))
                    <div class="mb-6 px-4 py-3 rounded-md bg-yellow-50 text-yellow-800 border-l-4 border-yellow-500 shadow-soft-sm">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                            <span>{{ session('warning') }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Info Messages -->
                    @if(session('info'))
                    <div class="mb-6 px-4 py-3 rounded-md bg-blue-50 text-blue-800 border-l-4 border-blue-500 shadow-soft-sm">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                            <span>{{ session('info') }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Validation Errors -->
                    @if($errors->any())
                    <div class="mb-6 px-4 py-3 rounded-md bg-red-50 text-red-800 border-l-4 border-red-500 shadow-soft-sm">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
                            <div>
                                <p class="font-medium mb-2">Please correct the following errors:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @stack('scripts')

    <!-- Sidebar Enhancement Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-close mobile sidebar when clicking on a link
        const mobileLinks = document.querySelectorAll('.lg\\:hidden a[href]');
        const sidebarToggle = document.querySelector('[x-data] button');

        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Close mobile sidebar
                if (window.Alpine) {
                    const sidebarComponent = document.querySelector('[x-data*="sidebarOpen"]');
                    if (sidebarComponent && sidebarComponent._x_dataStack) {
                        sidebarComponent._x_dataStack[0].sidebarOpen = false;
                    }
                }
            });
        });

        // Smooth scrolling for sidebar navigation
        const sidebarContainer = document.querySelector('.overflow-y-auto');
        if (sidebarContainer) {
            sidebarContainer.style.scrollBehavior = 'smooth';
        }

        // Highlight active menu items
        const currentPath = window.location.pathname;
        const menuItems = document.querySelectorAll('a[href]');

        menuItems.forEach(item => {
            if (item.getAttribute('href') === currentPath) {
                item.classList.add('active-menu-item');
            }
        });

        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            // Alt + M to toggle mobile sidebar
            if (e.altKey && e.key === 'm') {
                e.preventDefault();
                if (window.Alpine) {
                    const sidebarComponent = document.querySelector('[x-data*="sidebarOpen"]');
                    if (sidebarComponent && sidebarComponent._x_dataStack) {
                        sidebarComponent._x_dataStack[0].sidebarOpen = !sidebarComponent._x_dataStack[0].sidebarOpen;
                    }
                }
            }
        });

        // Add tooltips for collapsed sidebar items (future enhancement)
        const sidebarItems = document.querySelectorAll('.sidebar-item');
        sidebarItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                // Future: Show tooltip on hover
            });
        });

        // Persist sidebar state in localStorage
        const sidebarState = localStorage.getItem('sidebarState');
        if (sidebarState) {
            try {
                const state = JSON.parse(sidebarState);
                // Apply saved state to expandable menus
                Object.keys(state).forEach(key => {
                    const element = document.querySelector(`[x-data*="${key}"]`);
                    if (element && element._x_dataStack) {
                        element._x_dataStack[0].open = state[key];
                    }
                });
            } catch (e) {
                console.log('Error loading sidebar state:', e);
            }
        }

        // Save sidebar state on changes
        const expandableMenus = document.querySelectorAll('[x-data*="open"]');
        expandableMenus.forEach(menu => {
            const button = menu.querySelector('button');
            if (button) {
                button.addEventListener('click', function() {
                    setTimeout(() => {
                        const state = {};
                        expandableMenus.forEach(m => {
                            if (m._x_dataStack && m._x_dataStack[0].hasOwnProperty('open')) {
                                const key = m.getAttribute('x-data').match(/open:\s*(\w+)/)?.[1] || 'open';
                                state[key] = m._x_dataStack[0].open;
                            }
                        });
                        localStorage.setItem('sidebarState', JSON.stringify(state));
                    }, 100);
                });
            }
        });
    });
    </script>

    <style>
    /* Enhanced sidebar styles */
    .active-menu-item {
        background-color: #37a2bc !important;
        color: white !important;
        border-right: 3px solid #37a2bc !important;
    }

    /* Smooth transitions for all sidebar elements */
    .sidebar-item,
    .group {
        transition: all 0.2s ease-in-out;
    }

    /* Hover effects */
    .group:hover {
        transform: translateX(2px);
    }

    /* Focus styles for accessibility */
    button:focus,
    a:focus {
        outline: 2px solid #37a2bc;
        outline-offset: 2px;
    }

    /* Loading animation for dynamic content */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Responsive improvements */
    @media (max-width: 1024px) {
        .sidebar-text {
            font-size: 0.875rem;
        }
    }
    </style>
</body>
</html>