@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, success, warning, danger, ghost, outline
    'size' => 'md', // sm, md, lg, xl
    'icon' => '',
    'iconPosition' => 'left', // left, right
    'loading' => false,
    'disabled' => false,
    'fullWidth' => false,
    'rounded' => 'lg', // sm, md, lg, xl, full
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
        'xl' => 'px-8 py-4 text-lg'
    ];
    
    $roundedClasses = [
        'sm' => 'rounded-md',
        'md' => 'rounded-lg',
        'lg' => 'rounded-xl',
        'xl' => 'rounded-2xl',
        'full' => 'rounded-full'
    ];
    
    $variantClasses = [
        'primary' => 'bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-lg hover:shadow-xl focus:ring-blue-500 transform hover:scale-105',
        'secondary' => 'bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white shadow-lg hover:shadow-xl focus:ring-gray-500 transform hover:scale-105',
        'success' => 'bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white shadow-lg hover:shadow-xl focus:ring-green-500 transform hover:scale-105',
        'warning' => 'bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-white shadow-lg hover:shadow-xl focus:ring-yellow-500 transform hover:scale-105',
        'danger' => 'bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white shadow-lg hover:shadow-xl focus:ring-red-500 transform hover:scale-105',
        'ghost' => 'bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 focus:ring-gray-500',
        'outline' => 'bg-transparent border-2 border-gray-300 hover:border-gray-400 text-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:border-gray-500 focus:ring-gray-500'
    ];
    
    $classes = implode(' ', [
        $baseClasses,
        $sizeClasses[$size],
        $roundedClasses[$rounded],
        $variantClasses[$variant],
        $fullWidth ? 'w-full' : '',
        $disabled || $loading ? 'pointer-events-none' : ''
    ]);
@endphp

<button
    type="{{ $type }}"
    {{ $disabled || $loading ? 'disabled' : '' }}
    class="{{ $classes }}"
    x-data="{ 
        loading: {{ $loading ? 'true' : 'false' }},
        ripple(event) {
            const button = event.currentTarget;
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;
            
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            button.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        }
    }"
    @click="ripple($event)"
    {{ $attributes }}
>
    <!-- Loading Spinner -->
    <div x-show="loading" class="mr-2">
        <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
    </div>
    
    <!-- Left Icon -->
    @if($icon && $iconPosition === 'left')
        <i class="{{ $icon }} {{ $slot->isEmpty() ? '' : 'mr-2' }}" x-show="!loading"></i>
    @endif
    
    <!-- Button Text -->
    @if(!$slot->isEmpty())
        <span x-show="!loading">{{ $slot }}</span>
        <span x-show="loading">Processing...</span>
    @endif
    
    <!-- Right Icon -->
    @if($icon && $iconPosition === 'right')
        <i class="{{ $icon }} {{ $slot->isEmpty() ? '' : 'ml-2' }}" x-show="!loading"></i>
    @endif
</button>

<style>
@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>
