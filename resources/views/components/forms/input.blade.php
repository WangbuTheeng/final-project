@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => '',
    'help' => '',
    'icon' => '',
    'floating' => true,
    'size' => 'md', // sm, md, lg
    'variant' => 'default', // default, filled, outlined
])

@php
    $inputId = $name . '_' . uniqid();
    $hasError = !empty($error) || $errors->has($name);
    $errorMessage = !empty($error) ? $error : $errors->first($name);
    $hasValue = !empty($value) || !empty(old($name));
    
    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-3 text-base',
        'lg' => 'px-5 py-4 text-lg'
    ];
    
    $variantClasses = [
        'default' => 'bg-white border-gray-300 focus:border-blue-500 focus:ring-blue-500',
        'filled' => 'bg-gray-50 border-gray-200 focus:bg-white focus:border-blue-500 focus:ring-blue-500',
        'outlined' => 'bg-transparent border-2 border-gray-300 focus:border-blue-500 focus:ring-0'
    ];
@endphp

<div class="form-group relative" x-data="{ 
    focused: false, 
    hasValue: {{ $hasValue ? 'true' : 'false' }},
    updateValue(event) {
        this.hasValue = event.target.value.length > 0;
    }
}">
    <!-- Input Container -->
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                <i class="{{ $icon }} {{ $hasError ? 'text-red-400' : 'text-gray-400' }} transition-colors duration-200"></i>
            </div>
        @endif
        
        <input
            type="{{ $type }}"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $floating ? '' : $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            @focus="focused = true"
            @blur="focused = false"
            @input="updateValue($event)"
            class="
                peer w-full rounded-xl border transition-all duration-200 ease-in-out
                {{ $sizeClasses[$size] }}
                {{ $variantClasses[$variant] }}
                {{ $icon ? ($floating ? 'pl-11' : 'pl-10') : '' }}
                {{ $floating ? 'placeholder-transparent' : '' }}
                {{ $hasError ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : '' }}
                {{ $disabled ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}
                focus:outline-none focus:ring-2 focus:ring-opacity-20
                dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:border-blue-400
            "
            {{ $attributes }}
        >
        
        @if($floating && $label)
            <!-- Floating Label -->
            <label 
                for="{{ $inputId }}"
                class="
                    absolute left-4 transition-all duration-200 ease-in-out pointer-events-none
                    {{ $icon ? 'left-11' : 'left-4' }}
                    {{ $hasError ? 'text-red-600' : 'text-gray-500' }}
                    peer-focus:text-blue-600 peer-focus:text-sm peer-focus:-translate-y-6 peer-focus:scale-90
                    dark:text-gray-400 dark:peer-focus:text-blue-400
                "
                :class="{
                    'text-sm -translate-y-6 scale-90 text-blue-600': focused || hasValue,
                    'text-base translate-y-0 scale-100': !focused && !hasValue
                }"
                style="top: 50%; transform-origin: left center; margin-top: -0.5rem;"
            >
                {{ $label }}
                @if($required)
                    <span class="text-red-500 ml-1">*</span>
                @endif
            </label>
        @endif
    </div>
    
    @if(!$floating && $label)
        <!-- Traditional Label -->
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-1">*</span>
            @endif
        </label>
    @endif
    
    <!-- Error Message -->
    @if($hasError)
        <div class="mt-2 flex items-center text-sm text-red-600 dark:text-red-400">
            <i class="fas fa-exclamation-circle mr-2 flex-shrink-0"></i>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif
    
    <!-- Help Text -->
    @if($help && !$hasError)
        <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
            <i class="fas fa-info-circle mr-2 flex-shrink-0"></i>
            <span>{{ $help }}</span>
        </div>
    @endif
    
    <!-- Success State (when no errors and has value) -->
    <div 
        x-show="hasValue && !{{ $hasError ? 'true' : 'false' }}" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
        style="top: {{ $floating ? '0' : ($label ? '2rem' : '0') }};"
    >
        <i class="fas fa-check-circle text-green-500 text-sm"></i>
    </div>
</div>
