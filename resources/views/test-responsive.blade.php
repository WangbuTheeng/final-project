@extends('layouts.dashboard')

@section('title', 'Mobile Responsive Test')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-soft-lg p-6 border border-gray-200/50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Mobile Responsive Test</h1>
                <p class="text-gray-600">Testing mobile responsiveness and sidebar behavior</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fas fa-mobile-alt mr-2"></i>
                    Responsive Ready
                </span>
            </div>
        </div>
    </div>

    <!-- Screen Size Indicator -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">Current Screen Size</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="block sm:hidden bg-red-100 border border-red-300 rounded-lg p-3 text-center">
                <i class="fas fa-mobile-alt text-red-600 text-xl mb-2"></i>
                <p class="text-red-800 font-medium">Mobile View</p>
                <p class="text-red-600 text-sm">< 640px</p>
            </div>
            <div class="hidden sm:block lg:hidden bg-yellow-100 border border-yellow-300 rounded-lg p-3 text-center">
                <i class="fas fa-tablet-alt text-yellow-600 text-xl mb-2"></i>
                <p class="text-yellow-800 font-medium">Tablet View</p>
                <p class="text-yellow-600 text-sm">640px - 1024px</p>
            </div>
            <div class="hidden lg:block bg-green-100 border border-green-300 rounded-lg p-3 text-center">
                <i class="fas fa-desktop text-green-600 text-xl mb-2"></i>
                <p class="text-green-800 font-medium">Desktop View</p>
                <p class="text-green-600 text-sm">≥ 1024px</p>
            </div>
        </div>
    </div>

    <!-- Mobile Features Test -->
    <div class="bg-white rounded-xl shadow-soft-lg p-6 border border-gray-200/50">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Mobile Features</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Mobile Sidebar Toggle</span>
                <button 
                    class="lg:hidden px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    @click="sidebarOpen = true; document.body.classList.add('mobile-sidebar-open')"
                >
                    <i class="fas fa-bars mr-2"></i>
                    Open Sidebar
                </button>
                <span class="hidden lg:inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-200 text-gray-600">
                    <i class="fas fa-desktop mr-1"></i>
                    Desktop Mode
                </span>
            </div>
            
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Responsive Grid</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                    <div class="w-8 h-8 bg-blue-500 rounded"></div>
                    <div class="w-8 h-8 bg-green-500 rounded"></div>
                    <div class="w-8 h-8 bg-yellow-500 rounded hidden sm:block"></div>
                    <div class="w-8 h-8 bg-red-500 rounded hidden lg:block"></div>
                </div>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Touch-Friendly Buttons with Ripple</span>
                <div class="flex space-x-2">
                    <button class="ripple-button mobile-touch touch-feedback px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="ripple-button mobile-touch touch-feedback px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Ripple Effect Examples</span>
                <div class="flex flex-wrap gap-2">
                    <button class="ripple-button ripple-blue mobile-touch px-3 py-2 bg-blue-500 text-white rounded-lg text-sm">
                        Blue Ripple
                    </button>
                    <button class="ripple-button ripple-green mobile-touch px-3 py-2 bg-green-500 text-white rounded-lg text-sm">
                        Green Ripple
                    </button>
                    <button class="ripple-button ripple-red mobile-touch px-3 py-2 bg-red-500 text-white rounded-lg text-sm">
                        Red Ripple
                    </button>
                    <button class="ripple-button ripple-dark mobile-touch px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm">
                        Dark Ripple
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ripple Effect Demonstration -->
    <div class="bg-white rounded-xl shadow-soft-lg p-6 border border-gray-200/50">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ripple Effect Demonstration</h3>
        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Card with ripple -->
                <div class="ripple-container mobile-touch p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg cursor-pointer">
                    <h4 class="font-semibold mb-2">Interactive Card</h4>
                    <p class="text-sm opacity-90">Click me to see ripple effect</p>
                </div>

                <!-- Button group with different ripples -->
                <div class="space-y-2">
                    <button class="ripple-button mobile-touch w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-star mr-2"></i>
                        Purple Button
                    </button>
                    <button class="ripple-button mobile-touch w-full px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        <i class="fas fa-heart mr-2"></i>
                        Orange Button
                    </button>
                </div>

                <!-- List with ripple items -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="ripple-container mobile-touch p-3 hover:bg-gray-50 border-b border-gray-200 cursor-pointer">
                        <div class="flex items-center">
                            <i class="fas fa-user text-gray-400 mr-3"></i>
                            <span class="text-gray-700">Profile Settings</span>
                        </div>
                    </div>
                    <div class="ripple-container mobile-touch p-3 hover:bg-gray-50 border-b border-gray-200 cursor-pointer">
                        <div class="flex items-center">
                            <i class="fas fa-bell text-gray-400 mr-3"></i>
                            <span class="text-gray-700">Notifications</span>
                        </div>
                    </div>
                    <div class="ripple-container mobile-touch p-3 hover:bg-gray-50 cursor-pointer">
                        <div class="flex items-center">
                            <i class="fas fa-cog text-gray-400 mr-3"></i>
                            <span class="text-gray-700">Settings</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Action Buttons -->
            <div class="flex justify-center space-x-4 pt-4">
                <button class="ripple-button mobile-touch w-14 h-14 bg-pink-500 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="ripple-button mobile-touch w-14 h-14 bg-indigo-500 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="ripple-button mobile-touch w-14 h-14 bg-teal-500 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <i class="fas fa-share"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Table Test -->
    <div class="bg-white rounded-xl shadow-soft-lg p-6 border border-gray-200/50">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Responsive Table</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">John Doe</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">john@example.com</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Admin</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Jane Smith</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">jane@example.com</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Teacher</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Instructions -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-yellow-900 mb-3">
            <i class="fas fa-info-circle mr-2"></i>
            Testing Instructions
        </h3>
        <ul class="space-y-2 text-yellow-800">
            <li class="flex items-start">
                <i class="fas fa-circle text-yellow-600 text-xs mt-2 mr-3 flex-shrink-0"></i>
                <span>On mobile devices (< 640px), the sidebar should be hidden and accessible via the hamburger menu</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-circle text-yellow-600 text-xs mt-2 mr-3 flex-shrink-0"></i>
                <span>The mobile sidebar should overlay the content without causing horizontal scrolling</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-circle text-yellow-600 text-xs mt-2 mr-3 flex-shrink-0"></i>
                <span>On desktop (≥ 1024px), the sidebar should be fixed and visible</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-circle text-yellow-600 text-xs mt-2 mr-3 flex-shrink-0"></i>
                <span>Content should be responsive and adapt to different screen sizes</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-circle text-yellow-600 text-xs mt-2 mr-3 flex-shrink-0"></i>
                <span>Ripple effects should appear when clicking/tapping buttons and interactive elements</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-circle text-yellow-600 text-xs mt-2 mr-3 flex-shrink-0"></i>
                <span>Touch feedback (scale animation) should activate on mobile devices when pressing elements</span>
            </li>
        </ul>
    </div>
</div>

<script>
// Add some JavaScript to show current viewport width
function updateViewportInfo() {
    const width = window.innerWidth;
    console.log('Current viewport width:', width + 'px');
    
    // You can add more debugging info here if needed
    if (width < 640) {
        console.log('Mobile view active');
    } else if (width < 1024) {
        console.log('Tablet view active');
    } else {
        console.log('Desktop view active');
    }
}

// Update on load and resize
window.addEventListener('load', updateViewportInfo);
window.addEventListener('resize', updateViewportInfo);
</script>
@endsection
