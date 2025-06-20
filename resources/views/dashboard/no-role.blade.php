<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'College CMS') }} - Access Restricted</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
                        'brand': '#37a2bc',
                        'brand-dark': '#2d8299',
                        'brand-light': '#4bb3cc',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .shadow-soft {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Main Card -->
        <div class="glass-effect rounded-2xl shadow-soft p-8 text-center">
            <!-- Logo/Icon -->
            <div class="mb-8">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-brand to-brand-dark rounded-full flex items-center justify-center shadow-soft animate-float">
                    <i class="fas fa-university text-3xl text-white"></i>
                </div>
            </div>

            <!-- College CMS Title -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">College CMS</h1>
                <div class="w-16 h-0.5 bg-brand mx-auto opacity-50"></div>
            </div>

            <!-- User Info -->
            <div class="mb-8">
                <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <span class="text-xl font-bold text-gray-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</h2>
                <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
            </div>

            <!-- Access Restricted Message -->
            <div class="mb-8">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-center mb-2">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl animate-pulse-slow"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">Access Restricted</h3>
                    <p class="text-yellow-700 text-sm leading-relaxed">
                        Contact the owner to view the dashboard (+977 981818181818)
                    </p>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-center mb-2">
                        <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                    </div>
                    <p class="text-blue-700 text-sm">
                        Your account has been created successfully, but you need to be assigned a role by the system administrator to access the dashboard features.
                    </p>
                </div>
            </div>

            <!-- Logout Button -->
            <div class="space-y-4">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full bg-gradient-to-r from-brand to-brand-dark hover:from-brand-dark hover:to-brand text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 ease-in-out transform hover:scale-105 shadow-soft hover:shadow-lg">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>
                
                <!-- Contact Information (Optional) -->
                <div class="text-xs text-gray-500 mt-4">
                    <p>Need help? Contact your system administrator</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                © {{ date('Y') }} College CMS. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Background Animation -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-32 w-80 h-80 bg-brand opacity-5 rounded-full animate-pulse-slow"></div>
        <div class="absolute -bottom-40 -left-32 w-80 h-80 bg-brand-dark opacity-5 rounded-full animate-pulse-slow" style="animation-delay: 1s;"></div>
    </div>
</body>
</html>
