@extends('layouts.dashboard')

@section('title', $title ?? 'Coming Soon')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
        <div class="w-24 h-24 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-tools text-blue-600 dark:text-blue-400 text-3xl"></i>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $title ?? 'Coming Soon' }}</h1>
        
        <p class="text-gray-600 dark:text-gray-300 mb-8">
            {{ $message ?? 'This feature is currently under development and will be available soon.' }}
        </p>
        
        <div class="space-y-3">
            <a href="{{ route('dashboard') }}" 
               class="w-full inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-home mr-2"></i>
                Back to Dashboard
            </a>
            
            <button onclick="history.back()" 
                    class="w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Go Back
            </button>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Need this feature urgently? 
                <a href="mailto:support@college.edu" class="text-blue-600 dark:text-blue-400 hover:underline">Contact support</a>
            </p>
        </div>
    </div>
</div>
@endsection
