<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Test - College CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
             @click="sidebarOpen = false"
             style="display: none;">
        </div>

        <!-- Mobile Sidebar -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 z-50 w-80 max-w-sm bg-white shadow-2xl lg:hidden"
             style="display: none;">
            
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 bg-blue-600">
                <h2 class="text-lg font-bold text-white">College CMS</h2>
                <button @click="sidebarOpen = false" class="p-2 text-white hover:bg-white/20 rounded-lg">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- User Info -->
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <span class="font-bold text-sm text-white">A</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm text-gray-900">Admin User</h3>
                        <p class="text-xs text-gray-500">Super Admin</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <div class="overflow-y-auto h-[calc(100vh-8rem)]">
                <nav class="p-4 space-y-2">
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 bg-blue-50 border-r-4 border-blue-500 rounded-r-lg">
                        <i class="fas fa-tachometer-alt mr-3 text-blue-600"></i>
                        Dashboard
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-university mr-3 text-gray-400"></i>
                        Academic Structure
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-user-graduate mr-3 text-gray-400"></i>
                        Student Management
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-file-alt mr-3 text-gray-400"></i>
                        Exam Management
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-rupee-sign mr-3 text-gray-400"></i>
                        Finance Management
                    </a>
                </nav>
            </div>
        </div>

        <!-- Desktop Sidebar -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-72 lg:flex-col z-30">
            <div class="flex flex-col flex-1 bg-white shadow-lg border-r border-gray-200">
                <!-- Desktop sidebar content would go here -->
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-full lg:pl-72">
            <!-- Top Navigation -->
            <div class="sticky top-0 z-30 flex h-16 bg-white shadow-sm border-b border-gray-200">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" class="px-4 text-gray-500 hover:text-gray-700 lg:hidden">
                    <i class="fas fa-bars text-lg"></i>
                </button>

                <!-- Search and other nav items -->
                <div class="flex justify-between flex-1 px-4">
                    <div class="flex items-center flex-1 max-w-lg">
                        <input type="text" placeholder="Search..." class="block w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="p-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-bell"></i>
                        </button>
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                <span class="text-sm font-semibold text-white">A</span>
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700">Admin User</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="p-4 bg-gray-50 min-h-screen">
                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                    <p class="text-gray-600">Welcome to your college management system</p>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-blue-100">
                                <i class="fas fa-user-graduate text-xl text-blue-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 uppercase">TOTAL STUDENTS</p>
                                <p class="text-2xl font-bold text-gray-900">1,234</p>
                                <p class="text-xs text-gray-500">Active students</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-green-100">
                                <i class="fas fa-chalkboard text-xl text-green-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 uppercase">ACTIVE CLASSES</p>
                                <p class="text-2xl font-bold text-gray-900">45</p>
                                <p class="text-xs text-gray-500">Current semester</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-yellow-100">
                                <i class="fas fa-file-alt text-xl text-yellow-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 uppercase">UPCOMING EXAMS</p>
                                <p class="text-2xl font-bold text-gray-900">8</p>
                                <p class="text-xs text-gray-500">Next 30 days</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-purple-100">
                                <i class="fas fa-users text-xl text-purple-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 uppercase">TOTAL USERS</p>
                                <p class="text-2xl font-bold text-gray-900">156</p>
                                <p class="text-xs text-gray-500">System users</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Finance Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-sm p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium uppercase">THIS MONTH</p>
                                <p class="text-2xl font-bold">NRs 92,500.00</p>
                                <p class="text-xs text-green-100">No change from last month</p>
                            </div>
                            <div class="p-3 rounded-lg bg-white bg-opacity-20">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-sm p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium uppercase">OUTSTANDING</p>
                                <p class="text-2xl font-bold">NRs 15,000.00</p>
                                <p class="text-xs text-blue-100">5 pending invoices</p>
                            </div>
                            <div class="p-3 rounded-lg bg-white bg-opacity-20">
                                <i class="fas fa-exclamation-triangle text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-sm p-4 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100 text-sm font-medium uppercase">OVERDUE</p>
                                <p class="text-2xl font-bold">0</p>
                                <p class="text-xs text-red-100">Invoices past due date</p>
                            </div>
                            <div class="p-3 rounded-lg bg-white bg-opacity-20">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-lg bg-blue-100">
                                <i class="fas fa-file-invoice text-lg text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-900">Create Invoice</h3>
                                <p class="text-xs text-gray-500">Generate new invoice</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Students</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-center text-gray-500 py-8">No recent students to display</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-center text-gray-500 py-8">No recent payments to display</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
