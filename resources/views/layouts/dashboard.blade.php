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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Alpine.js (from CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS (from CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                        'display': ['Poppins', 'Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#37a2bc',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                        accent: {
                            50: '#fef7ff',
                            100: '#fceeff',
                            200: '#f8d4fe',
                            300: '#f2b1fc',
                            400: '#e879f9',
                            500: '#d946ef',
                            600: '#c026d3',
                            700: '#a21caf',
                            800: '#86198f',
                            900: '#701a75',
                        },
                        success: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        warning: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        },
                        danger: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        },
                        'dashboard-bg': '#f8fafc',
                        'brand': '#37a2bc',
                        'brand-light': '#4db3cc',
                        'brand-dark': '#2d8299',
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                        'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                        'mesh-gradient': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        'brand-gradient': 'linear-gradient(135deg, #37a2bc 0%, #4db3cc 50%, #2d8299 100%)',
                        'sidebar-gradient': 'linear-gradient(180deg, #37a2bc 0%, #2d8299 100%)',
                    },
                    boxShadow: {
                        'soft-xs': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                        'soft-sm': '0 2px 4px 0 rgba(0, 0, 0, 0.06)',
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04)',
                        'soft-md': '0 6px 10px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04)',
                        'soft-lg': '0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04)',
                        'soft-xl': '0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                        'soft-2xl': '0 25px 50px -12px rgba(0, 0, 0, 0.15)',
                        'inner-soft': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.06)',
                        'glow': '0 0 20px rgba(55, 162, 188, 0.3)',
                        'glow-lg': '0 0 30px rgba(55, 162, 188, 0.4)',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'fade-in-up': 'fadeInUp 0.4s ease-out',
                        'fade-in-down': 'fadeInDown 0.4s ease-out',
                        'slide-in-right': 'slideInRight 0.3s ease-out',
                        'slide-in-left': 'slideInLeft 0.3s ease-out',
                        'scale-in': 'scaleIn 0.2s ease-out',
                        'bounce-gentle': 'bounceGentle 2s infinite',
                        'pulse-soft': 'pulseSoft 2s infinite',
                        'float': 'float 3s ease-in-out infinite',
                        'shimmer': 'shimmer 2s linear infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeInDown: {
                            '0%': { opacity: '0', transform: 'translateY(-20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideInRight: {
                            '0%': { opacity: '0', transform: 'translateX(100%)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        slideInLeft: {
                            '0%': { opacity: '0', transform: 'translateX(-100%)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        scaleIn: {
                            '0%': { opacity: '0', transform: 'scale(0.9)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        },
                        bounceGentle: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-5px)' },
                        },
                        pulseSoft: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.8' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        shimmer: {
                            '0%': { backgroundPosition: '-200% 0' },
                            '100%': { backgroundPosition: '200% 0' },
                        },
                    },
                    backdropBlur: {
                        'xs': '2px',
                    },
                },
            },
        }
    </script>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div class="min-h-screen relative bg-gray-50">
        <!-- Background Pattern -->
        <div class="fixed inset-0 z-0 opacity-30 hidden lg:block">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 via-indigo-50/30 to-purple-50/50"></div>
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, rgba(59, 130, 246, 0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);"></div>
        </div>

        <!-- Mobile sidebar backdrop -->
        <div x-data="{ sidebarOpen: false }" class="relative z-10">
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false; document.body.classList.remove('mobile-sidebar-open')"
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
                class="fixed inset-y-0 left-0 z-50 w-80 max-w-sm bg-white shadow-2xl lg:hidden"
                style="display: none;"
            >
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200" style="background-color: #37a2bc;">
                    <h2 class="text-lg font-bold text-white">College CMS</h2>
                    <button @click="sidebarOpen = false; document.body.classList.remove('mobile-sidebar-open')" class="ripple-button mobile-touch touch-feedback p-2 text-white hover:bg-white/20 rounded-lg transition-all duration-200">
                        <span class="sr-only">Close sidebar</span>
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <!-- Mobile sidebar content -->
                <div class="overflow-y-auto h-[calc(100vh-4rem)] bg-white">
                    @include('layouts.partials.sidebar-menu')
                </div>
            </div>

            <!-- Static sidebar for desktop -->
            <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-72 lg:flex-col z-30">
                <div class="flex flex-col flex-1 min-h-0 bg-white/95 backdrop-blur-xl shadow-soft-2xl border-r border-gray-200/50 h-full">
                    <div class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden bg-gradient-to-b from-white/90 to-gray-50/90">
                        @include('layouts.partials.sidebar-menu')
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="w-full lg:pl-72 relative z-20">
                <!-- Top navigation -->
                @include('layouts.partials.top-navigation')

                <!-- Main content area -->
                <main class="p-4 sm:p-6 lg:p-8 animate-fade-in-up min-h-screen bg-gray-50">
                    <!-- Success Messages -->
                    @if(session('success'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="mb-6 px-6 py-4 rounded-xl bg-gradient-to-r from-success-50 to-emerald-50 text-success-800 border border-success-200 shadow-soft-lg backdrop-blur-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-success-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-check text-success-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-success-900">Success!</p>
                                    <p class="text-sm text-success-700">{{ session('success') }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-success-600 hover:text-success-800 hover:bg-success-100 p-2 rounded-lg transition-all duration-200">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Status Messages -->
                    @if(session('status'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="mb-6 px-6 py-4 rounded-xl bg-gradient-to-r from-primary-50 to-blue-50 text-primary-800 border border-primary-200 shadow-soft-lg backdrop-blur-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-info text-primary-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-primary-900">Information</p>
                                    <p class="text-sm text-primary-700">{{ session('status') }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-primary-600 hover:text-primary-800 hover:bg-primary-100 p-2 rounded-lg transition-all duration-200">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Error Messages -->
                    @if(session('error'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="mb-6 px-6 py-4 rounded-xl bg-gradient-to-r from-danger-50 to-red-50 text-danger-800 border border-danger-200 shadow-soft-lg backdrop-blur-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-danger-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-exclamation-triangle text-danger-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-danger-900">Error!</p>
                                    <p class="text-sm text-danger-700">{{ session('error') }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-danger-600 hover:text-danger-800 hover:bg-danger-100 p-2 rounded-lg transition-all duration-200">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Warning Messages -->
                    @if(session('warning'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="mb-6 px-6 py-4 rounded-xl bg-gradient-to-r from-warning-50 to-yellow-50 text-warning-800 border border-warning-200 shadow-soft-lg backdrop-blur-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-warning-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-exclamation-triangle text-warning-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-warning-900">Warning!</p>
                                    <p class="text-sm text-warning-700">{{ session('warning') }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-warning-600 hover:text-warning-800 hover:bg-warning-100 p-2 rounded-lg transition-all duration-200">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Info Messages -->
                    @if(session('info'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="mb-6 px-6 py-4 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-800 border border-blue-200 shadow-soft-lg backdrop-blur-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-info-circle text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-blue-900">Information</p>
                                    <p class="text-sm text-blue-700">{{ session('info') }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-blue-600 hover:text-blue-800 hover:bg-blue-100 p-2 rounded-lg transition-all duration-200">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Validation Errors -->
                    @if($errors->any())
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="mb-6 px-6 py-4 rounded-xl bg-gradient-to-r from-danger-50 to-red-50 text-danger-800 border border-danger-200 shadow-soft-lg backdrop-blur-sm">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-danger-100 rounded-full flex items-center justify-center mr-4 mt-0.5">
                                    <i class="fas fa-exclamation-circle text-danger-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-danger-900 mb-2">Please correct the following errors:</p>
                                    <ul class="space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li class="flex items-start">
                                                <i class="fas fa-circle text-danger-400 text-xs mt-1.5 mr-2 flex-shrink-0"></i>
                                                <span class="text-sm text-danger-700">{{ $error }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button @click="show = false" class="text-danger-600 hover:text-danger-800 hover:bg-danger-100 p-2 rounded-lg transition-all duration-200 flex-shrink-0">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Responsive Utilities -->
    @include('layouts.partials.responsive-utilities')

    <!-- Scripts -->
    @stack('scripts')

    <!-- Sidebar Enhancement Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure sidebar is visible on desktop
        const desktopSidebar = document.querySelector('.lg\\:fixed.lg\\:inset-y-0.lg\\:flex.lg\\:w-72');
        if (desktopSidebar) {
            desktopSidebar.style.display = 'flex';
            desktopSidebar.style.position = 'fixed';
            desktopSidebar.style.width = '18rem';
            desktopSidebar.style.zIndex = '30';
        }

        // Ensure College CMS header is visible
        const cmsHeader = document.querySelector('.college-cms-header');
        if (cmsHeader) {
            cmsHeader.style.display = 'block';
            cmsHeader.style.visibility = 'visible';
            cmsHeader.style.opacity = '1';
        }



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
                        // Remove body scroll lock
                        document.body.classList.remove('mobile-sidebar-open');
                    }
                }
            });
        });

        // Handle mobile sidebar state changes
        function handleSidebarStateChange() {
            if (window.Alpine) {
                const sidebarComponent = document.querySelector('[x-data*="sidebarOpen"]');
                if (sidebarComponent && sidebarComponent._x_dataStack) {
                    const isOpen = sidebarComponent._x_dataStack[0].sidebarOpen;
                    document.body.classList.toggle('mobile-sidebar-open', isOpen);
                }
            }
        }

        // Watch for sidebar state changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                    handleSidebarStateChange();
                }
            });
        });

        // Start observing mobile sidebar
        const mobileSidebar = document.querySelector('.fixed.inset-y-0.left-0.z-50.w-80');
        if (mobileSidebar) {
            observer.observe(mobileSidebar, { attributes: true });
        }

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

        // Responsive behavior enhancements
        function handleResponsiveChanges() {
            const isMobile = window.innerWidth < 640;
            const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
            const isDesktop = window.innerWidth >= 1024;

            // Add responsive classes to body
            document.body.classList.toggle('mobile-view', isMobile);
            document.body.classList.toggle('tablet-view', isTablet);
            document.body.classList.toggle('desktop-view', isDesktop);

            // Auto-close mobile sidebar on desktop
            if (isDesktop && window.Alpine) {
                const sidebarComponent = document.querySelector('[x-data*="sidebarOpen"]');
                if (sidebarComponent && sidebarComponent._x_dataStack) {
                    sidebarComponent._x_dataStack[0].sidebarOpen = false;
                    // Remove body scroll lock when closing sidebar
                    document.body.classList.remove('mobile-sidebar-open');
                }
            }

            // Force mobile layout fixes
            if (isMobile || isTablet) {
                // Ensure main content takes full width
                const mainContent = document.querySelector('.w-full.lg\\:pl-72');
                if (mainContent) {
                    mainContent.style.paddingLeft = '0';
                    mainContent.style.marginLeft = '0';
                    mainContent.style.width = '100%';
                    mainContent.classList.add('mobile-content');
                }

                // Hide desktop sidebar
                const desktopSidebar = document.querySelector('.hidden.lg\\:fixed, .lg\\:fixed.lg\\:inset-y-0.lg\\:flex.lg\\:w-72');
                if (desktopSidebar) {
                    desktopSidebar.style.display = 'none';
                    desktopSidebar.style.visibility = 'hidden';
                }

                // Ensure main element is full width
                const mainElement = document.querySelector('main');
                if (mainElement) {
                    mainElement.style.width = '100%';
                    mainElement.style.marginLeft = '0';
                    mainElement.style.paddingLeft = '1rem';
                    mainElement.style.paddingRight = '1rem';
                }
            }

            // Adjust table responsiveness
            const tables = document.querySelectorAll('table');
            tables.forEach(table => {
                const wrapper = table.closest('.overflow-x-auto');
                if (wrapper) {
                    wrapper.classList.toggle('responsive-table', isMobile || isTablet);
                }
            });

            // Adjust grid responsiveness
            const grids = document.querySelectorAll('.grid');
            grids.forEach(grid => {
                if (grid.classList.contains('grid-cols-4') ||
                    grid.classList.contains('grid-cols-3') ||
                    grid.classList.contains('grid-cols-2')) {
                    grid.classList.toggle('responsive-grid', isMobile || isTablet);
                }
            });
        }

        // Initial call and resize listener
        handleResponsiveChanges();
        window.addEventListener('resize', handleResponsiveChanges);

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

        // Initialize ripple effects
        initializeRippleEffects();

        // Initialize mobile touch enhancements
        initializeMobileTouchEnhancements();
    });

    // Ripple Effect Functions
    function initializeRippleEffects() {
        // Add ripple effect to buttons and clickable elements
        const rippleElements = document.querySelectorAll('.ripple-container, .ripple-button, button, .group, a[href]');

        rippleElements.forEach(element => {
            // Skip if already has ripple
            if (element.hasAttribute('data-ripple-initialized')) return;

            element.setAttribute('data-ripple-initialized', 'true');

            // Add ripple classes if not present
            if (!element.classList.contains('ripple-container') && !element.classList.contains('ripple-button')) {
                element.classList.add('ripple-container');
            }

            element.addEventListener('click', function(e) {
                createRipple(e, this);
            });
        });
    }

    function createRipple(event, element) {
        // Remove existing ripples
        const existingRipples = element.querySelectorAll('.ripple-wave');
        existingRipples.forEach(ripple => ripple.remove());

        // Create ripple element
        const ripple = document.createElement('span');
        ripple.classList.add('ripple-wave');

        // Get element dimensions and position
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        // Set ripple styles
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
            z-index: 1;
        `;

        // Add dark ripple for light backgrounds
        if (element.classList.contains('bg-white') || element.classList.contains('bg-gray-50') || element.classList.contains('bg-gray-100')) {
            ripple.style.background = 'rgba(0, 0, 0, 0.1)';
        }

        // Add colored ripples based on element classes
        if (element.classList.contains('bg-blue-500') || element.classList.contains('bg-blue-600')) {
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
        } else if (element.classList.contains('bg-green-500') || element.classList.contains('bg-green-600')) {
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
        } else if (element.classList.contains('bg-red-500') || element.classList.contains('bg-red-600')) {
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
        }

        // Ensure element has relative positioning
        if (getComputedStyle(element).position === 'static') {
            element.style.position = 'relative';
        }

        // Ensure element has overflow hidden
        element.style.overflow = 'hidden';

        // Add ripple to element
        element.appendChild(ripple);

        // Remove ripple after animation
        setTimeout(() => {
            if (ripple.parentNode) {
                ripple.parentNode.removeChild(ripple);
            }
        }, 600);
    }

    function initializeMobileTouchEnhancements() {
        // Add mobile touch classes to interactive elements
        const isMobile = window.innerWidth < 1024;

        if (isMobile) {
            const touchElements = document.querySelectorAll('button, a[href], .group, [role="button"]');

            touchElements.forEach(element => {
                element.classList.add('mobile-touch', 'touch-feedback');

                // Add minimum touch target size for buttons
                if (element.tagName === 'BUTTON') {
                    element.classList.add('mobile-button');
                }
            });
        }
    }
    </script>

    <style>
    /* Enhanced sidebar styles */
    .active-menu-item {
        background: linear-gradient(135deg, #37a2bc 0%, #4db3cc 100%) !important;
        color: white !important;
        border-right: 4px solid #37a2bc !important;
        box-shadow: 0 4px 12px rgba(55, 162, 188, 0.3) !important;
        transform: translateX(4px) !important;
    }

    /* College CMS Header Styles */
    .college-cms-header {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: relative !important;
        z-index: 10 !important;
    }

    .college-cms-header h2 {
        color: white !important;
        font-weight: bold !important;
        font-size: 1.125rem !important;
        text-align: center !important;
    }



    /* Smooth transitions for all sidebar elements */
    .sidebar-item,
    .group {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Enhanced hover effects */
    .group:hover {
        transform: translateX(4px) scale(1.02);
        background: linear-gradient(135deg, rgba(55, 162, 188, 0.1) 0%, rgba(77, 179, 204, 0.05) 100%);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(55, 162, 188, 0.15);
    }

    /* Focus styles for accessibility */
    button:focus,
    a:focus {
        outline: 2px solid #37a2bc;
        outline-offset: 2px;
        border-radius: 8px;
    }

    /* Enhanced loading animation */
    .loading-skeleton {
        background: linear-gradient(90deg,
            rgba(240, 240, 240, 0.8) 25%,
            rgba(220, 220, 220, 0.8) 50%,
            rgba(240, 240, 240, 0.8) 75%);
        background-size: 200% 100%;
        animation: loading 2s ease-in-out infinite;
        border-radius: 8px;
    }

    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Ripple animation */
    @keyframes ripple-animation {
        0% {
            transform: scale(0);
            opacity: 1;
        }
        100% {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Glass morphism effect */
    .glass {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #37a2bc, #4db3cc);
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #2d8299, #37a2bc);
    }

    /* Enhanced card styles */
    .card-enhanced {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-enhanced:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    /* Gradient text */
    .gradient-text {
        background: linear-gradient(135deg, #37a2bc 0%, #4db3cc 50%, #2d8299 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Floating animation */
    .float-animation {
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    /* Pulse glow effect */
    .pulse-glow {
        animation: pulse-glow 2s infinite;
    }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(55, 162, 188, 0.3); }
        50% { box-shadow: 0 0 30px rgba(55, 162, 188, 0.6); }
    }

    /* Enhanced Responsive Design */

    /* Mobile optimizations */
    @media (max-width: 640px) {
        .sidebar-text {
            font-size: 0.875rem;
        }

        .group:hover {
            transform: translateX(1px) scale(1.005);
        }

        /* Compact spacing for mobile */
        .mobile-compact {
            padding: 0.5rem !important;
        }

        /* Hide non-essential elements on mobile */
        .mobile-hidden {
            display: none !important;
        }

        /* Adjust font sizes for mobile */
        .mobile-text-sm {
            font-size: 0.75rem !important;
        }

        .mobile-text-xs {
            font-size: 0.625rem !important;
        }

        /* Fix mobile layout */
        body {
            overflow-x: hidden;
        }

        /* Ensure main content takes full width on mobile */
        .lg\\:pl-72 {
            padding-left: 0 !important;
        }

        /* Mobile sidebar fixes */
        .mobile-sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            z-index: 50 !important;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-sidebar.open {
            transform: translateX(0);
        }

        /* Prevent body scroll when mobile sidebar is open */
        body.mobile-sidebar-open {
            overflow: hidden;
        }

        /* Mobile navigation improvements */
        .mobile-nav-button {
            display: flex !important;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            background: transparent;
            border: none;
            color: #6b7280;
            transition: all 0.2s ease;
        }

        .mobile-nav-button:hover {
            color: #37a2bc;
            background-color: rgba(55, 162, 188, 0.1);
        }

        /* Mobile content adjustments */
        .mobile-content {
            width: 100% !important;
            padding-left: 0 !important;
            margin-left: 0 !important;
        }
    }

    /* Tablet optimizations */
    @media (min-width: 641px) and (max-width: 1024px) {
        .sidebar-text {
            font-size: 0.875rem;
        }

        .group:hover {
            transform: translateX(2px) scale(1.01);
        }

        /* Tablet-specific adjustments */
        .tablet-compact {
            padding: 0.75rem !important;
        }
    }

    /* Desktop optimizations */
    @media (min-width: 1025px) {
        .desktop-enhanced {
            transition: all 0.3s ease;
        }

        .desktop-enhanced:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .card-enhanced {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    }

    /* Ensure sidebar is visible on desktop */
    @media (min-width: 1024px) {
        .lg\\:fixed {
            position: fixed !important;
        }

        .lg\\:flex {
            display: flex !important;
        }

        .lg\\:w-72 {
            width: 18rem !important;
        }

        .lg\\:pl-72 {
            padding-left: 18rem !important;
        }

        .hidden.lg\\:fixed.lg\\:inset-y-0.lg\\:flex.lg\\:w-72.lg\\:flex-col {
            display: flex !important;
            position: fixed !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            width: 18rem !important;
            flex-direction: column !important;
            z-index: 30 !important;
        }

        .college-cms-header {
            display: block !important;
            visibility: visible !important;
        }
    }

    /* Mobile layout fixes */
    @media (max-width: 1023px) {
        /* Force hide desktop sidebar on mobile/tablet */
        .hidden.lg\\:fixed,
        .lg\\:fixed.lg\\:inset-y-0.lg\\:flex.lg\\:w-72.lg\\:flex-col {
            display: none !important;
            visibility: hidden !important;
        }

        /* Ensure main content container takes full width */
        .w-full.lg\\:pl-72 {
            width: 100% !important;
            padding-left: 0 !important;
            margin-left: 0 !important;
            transform: translateX(0) !important;
        }

        /* Remove any left padding from main content */
        .lg\\:pl-72 {
            padding-left: 0 !important;
        }

        /* Ensure main content area is full width */
        main {
            width: 100% !important;
            margin-left: 0 !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            max-width: 100% !important;
        }

        /* Mobile sidebar positioning */
        .fixed.inset-y-0.left-0.z-50 {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            z-index: 50 !important;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        /* Mobile sidebar backdrop */
        .fixed.inset-0.z-40 {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 40 !important;
        }

        /* Force full width layout on mobile */
        body.mobile-view .w-full,
        body.tablet-view .w-full {
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Override any desktop-specific positioning */
        .relative.z-10 {
            position: relative !important;
            z-index: 10 !important;
        }

        /* Ensure content doesn't get pushed by sidebar */
        .min-h-screen {
            width: 100% !important;
        }
    }

    /* Responsive table improvements */
    @media (max-width: 768px) {
        .responsive-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .responsive-table table {
            min-width: 600px;
        }

        .responsive-table th,
        .responsive-table td {
            padding: 0.5rem !important;
            font-size: 0.75rem !important;
        }

        /* Additional mobile fixes for very small screens */
        .mobile-nav-button {
            padding: 0.5rem !important;
        }

        /* Ensure mobile sidebar is full height */
        .fixed.inset-y-0.left-0.z-50.w-80 {
            width: 85vw !important;
            max-width: 320px !important;
        }

        /* Mobile content padding adjustments */
        main.p-4 {
            padding: 0.75rem !important;
        }

        /* Top navigation height adjustment for mobile */
        .sticky.top-0.z-30.flex.h-16 {
            height: 3.5rem !important;
        }
    }

    /* Extra small mobile devices */
    @media (max-width: 480px) {
        .fixed.inset-y-0.left-0.z-50.w-80 {
            width: 90vw !important;
            max-width: 280px !important;
        }

        main {
            padding: 0.5rem !important;
        }

        .mobile-nav-button {
            padding: 0.375rem !important;
        }
    }

    /* Mobile content styling */
    .mobile-content {
        width: 100% !important;
        padding-left: 0 !important;
        margin-left: 0 !important;
        max-width: 100% !important;
    }

    /* Force mobile layout on small screens */
    @media (max-width: 1023px) {
        .mobile-content,
        .mobile-content main {
            width: 100% !important;
            padding-left: 1rem !important;
            padding-right: 1rem !important;
            margin-left: 0 !important;
        }

        /* Override any Tailwind classes that might interfere */
        .lg\\:pl-72.mobile-content {
            padding-left: 1rem !important;
        }

        /* Ensure dashboard content is properly positioned */
        .w-full.lg\\:pl-72.mobile-content {
            transform: translateX(0) !important;
        }
    }

    /* Ripple Effect Styles */
    .ripple-container {
        position: relative;
        overflow: hidden;
        transform: translate3d(0, 0, 0);
    }

    .ripple-container:before {
        content: "";
        display: block;
        position: absolute;
        left: 50%;
        top: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .ripple-container:active:before {
        width: 300px;
        height: 300px;
        transition: width 0s, height 0s;
    }

    /* Enhanced ripple for buttons */
    .ripple-button {
        position: relative;
        overflow: hidden;
        transform: translate3d(0, 0, 0);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .ripple-button:before {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
        pointer-events: none;
        z-index: 1;
    }

    .ripple-button:active:before {
        width: 200px;
        height: 200px;
        transition: width 0s, height 0s;
    }

    /* Dark ripple for light backgrounds */
    .ripple-dark:before {
        background: rgba(0, 0, 0, 0.1) !important;
    }

    /* Colored ripples */
    .ripple-blue:before {
        background: rgba(59, 130, 246, 0.3) !important;
    }

    .ripple-green:before {
        background: rgba(34, 197, 94, 0.3) !important;
    }

    .ripple-red:before {
        background: rgba(239, 68, 68, 0.3) !important;
    }

    /* Ripple animation */
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Touch feedback enhancement */
    .touch-feedback {
        transition: all 0.15s ease;
        transform: scale(1);
    }

    .touch-feedback:active {
        transform: scale(0.95);
    }

    /* Mobile-specific touch enhancements */
    @media (max-width: 1023px) {
        .mobile-touch {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }

        .mobile-touch:active {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Enhanced button feedback for mobile */
        .mobile-button {
            min-height: 44px;
            min-width: 44px;
            touch-action: manipulation;
        }
    }

    /* Responsive grid improvements */
    @media (max-width: 640px) {
        .responsive-grid {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }
    }

    @media (min-width: 641px) and (max-width: 768px) {
        .responsive-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 1.5rem !important;
        }
    }

    /* Responsive card improvements */
    .responsive-card {
        transition: all 0.3s ease;
    }

    @media (max-width: 640px) {
        .responsive-card {
            padding: 1rem !important;
            margin: 0.5rem 0 !important;
        }

        .responsive-card h3 {
            font-size: 1rem !important;
        }

        .responsive-card p {
            font-size: 0.875rem !important;
        }
    }

    /* Print styles */
    @media print {
        .sidebar, .top-navigation {
            display: none !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }

        .responsive-card {
            break-inside: avoid;
            margin-bottom: 1rem;
        }
    }
    </style>
</body>
</html>