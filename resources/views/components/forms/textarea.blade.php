@props([
    'label' => '',
    'name' => '',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => '',
    'help' => '',
    'rows' => 4,
    'floating' => true,
    'size' => 'md', // sm, md, lg
    'variant' => 'default', // default, filled, outlined
    'autoResize' => false,
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
    charCount: {{ strlen(old($name, $value)) }},
    
    updateValue(event) {
        this.hasValue = event.target.value.length > 0;
        this.charCount = event.target.value.length;
        
        @if($autoResize)
        // Auto-resize functionality
        event.target.style.height = 'auto';
        event.target.style.height = event.target.scrollHeight + 'px';
        @endif
    },
    
    init() {
        @if($autoResize)
        this.$nextTick(() => {
            const textarea = this.$refs.textarea;
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        });
        @endif
    }
}">
    @if(!$floating && $label)
        <!-- Traditional Label -->
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-1">*</span>
            @endif
        </label>
    @endif
    
    <!-- Textarea Container -->
    <div class="relative">
        <textarea
            id="{{ $inputId }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $floating ? '' : $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            @focus="focused = true"
            @blur="focused = false"
            @input="updateValue($event)"
            x-ref="textarea"
            class="
                peer w-full rounded-xl border transition-all duration-200 ease-in-out resize-vertical
                {{ $sizeClasses[$size] }}
                {{ $variantClasses[$variant] }}
                {{ $floating ? 'placeholder-transparent' : '' }}
                {{ $hasError ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : '' }}
                {{ $disabled ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}
                {{ $autoResize ? 'resize-none overflow-hidden' : '' }}
                focus:outline-none focus:ring-2 focus:ring-opacity-20
                dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:border-blue-400
            "
            {{ $attributes }}
        >{{ old($name, $value) }}</textarea>
        
        @if($floating && $label)
            <!-- Floating Label -->
            <label 
                for="{{ $inputId }}"
                class="
                    absolute left-4 top-4 transition-all duration-200 ease-in-out pointer-events-none
                    {{ $hasError ? 'text-red-600' : 'text-gray-500' }}
                    peer-focus:text-blue-600 peer-focus:text-sm peer-focus:-translate-y-6 peer-focus:scale-90
                    dark:text-gray-400 dark:peer-focus:text-blue-400
                "
                :class="{
                    'text-sm -translate-y-6 scale-90 text-blue-600': focused || hasValue,
                    'text-base translate-y-0 scale-100': !focused && !hasValue
                }"
                style="transform-origin: left top;"
            >
                {{ $label }}
                @if($required)
                    <span class="text-red-500 ml-1">*</span>
                @endif
            </label>
        @endif
        
        <!-- Character Counter -->
        @if($attributes->has('maxlength'))
            <div class="absolute bottom-2 right-3 text-xs text-gray-400 dark:text-gray-500">
                <span x-text="charCount"></span>/<span>{{ $attributes->get('maxlength') }}</span>
            </div>
        @endif
    </div>
    
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
    
    <!-- Success State -->
    <div 
        x-show="hasValue && !{{ $hasError ? 'true' : 'false' }}" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        class="absolute top-3 right-3 pointer-events-none"
    >
        <i class="fas fa-check-circle text-green-500 text-sm"></i>
    </div>
</div>
