@props([
    'label' => '',
    'name' => '',
    'value' => '',
    'options' => [],
    'placeholder' => 'Select an option',
    'required' => false,
    'disabled' => false,
    'error' => '',
    'help' => '',
    'icon' => '',
    'floating' => true,
    'size' => 'md', // sm, md, lg
    'variant' => 'default', // default, filled, outlined
    'searchable' => false,
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
    open: false,
    search: '',
    selectedValue: '{{ old($name, $value) }}',
    selectedText: '',
    options: {{ json_encode($options) }},
    filteredOptions: {{ json_encode($options) }},
    
    init() {
        this.updateSelectedText();
        this.filteredOptions = this.options;
    },
    
    updateValue(value, text) {
        this.selectedValue = value;
        this.selectedText = text;
        this.hasValue = value.length > 0;
        this.open = false;
        this.search = '';
        this.filteredOptions = this.options;
        this.$refs.hiddenInput.value = value;
        this.$refs.hiddenInput.dispatchEvent(new Event('change'));
    },
    
    updateSelectedText() {
        const option = this.options.find(opt => opt.value == this.selectedValue);
        this.selectedText = option ? option.text : '';
        this.hasValue = this.selectedValue.length > 0;
    },
    
    filterOptions() {
        if (!this.search) {
            this.filteredOptions = this.options;
            return;
        }
        this.filteredOptions = this.options.filter(option => 
            option.text.toLowerCase().includes(this.search.toLowerCase())
        );
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
    
    <!-- Select Container -->
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                <i class="{{ $icon }} {{ $hasError ? 'text-red-400' : 'text-gray-400' }} transition-colors duration-200"></i>
            </div>
        @endif
        
        <!-- Hidden Input -->
        <input 
            type="hidden" 
            name="{{ $name }}" 
            x-ref="hiddenInput"
            :value="selectedValue"
            {{ $required ? 'required' : '' }}
        >
        
        @if($searchable)
            <!-- Searchable Select -->
            <div class="relative">
                <button
                    type="button"
                    @click="open = !open"
                    @focus="focused = true"
                    @blur="focused = false"
                    class="
                        peer w-full rounded-xl border transition-all duration-200 ease-in-out text-left
                        {{ $sizeClasses[$size] }}
                        {{ $variantClasses[$variant] }}
                        {{ $icon ? 'pl-11' : '' }}
                        {{ $hasError ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : '' }}
                        {{ $disabled ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'cursor-pointer' }}
                        focus:outline-none focus:ring-2 focus:ring-opacity-20
                        dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:border-blue-400
                    "
                    {{ $disabled ? 'disabled' : '' }}
                >
                    <span x-text="selectedText || '{{ $placeholder }}'" 
                          :class="{ 'text-gray-500': !selectedText }"></span>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" 
                           :class="{ 'rotate-180': open }"></i>
                    </span>
                </button>
                
                <!-- Dropdown -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     @click.away="open = false"
                     class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-xl py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none"
                     style="display: none;">
                    
                    <!-- Search Input -->
                    <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-600">
                        <input 
                            type="text" 
                            x-model="search"
                            @input="filterOptions()"
                            placeholder="Search options..."
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                    </div>
                    
                    <!-- Options -->
                    <template x-for="option in filteredOptions" :key="option.value">
                        <button
                            type="button"
                            @click="updateValue(option.value, option.text)"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150"
                            :class="{ 'bg-blue-50 text-blue-600 dark:bg-blue-900 dark:text-blue-300': selectedValue == option.value }"
                            x-text="option.text"
                        ></button>
                    </template>
                    
                    <!-- No Results -->
                    <div x-show="filteredOptions.length === 0" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                        No options found
                    </div>
                </div>
            </div>
        @else
            <!-- Standard Select -->
            <select
                id="{{ $inputId }}"
                name="{{ $name }}"
                @focus="focused = true"
                @blur="focused = false"
                @change="hasValue = $event.target.value.length > 0"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                class="
                    peer w-full rounded-xl border transition-all duration-200 ease-in-out
                    {{ $sizeClasses[$size] }}
                    {{ $variantClasses[$variant] }}
                    {{ $icon ? 'pl-11' : '' }}
                    {{ $hasError ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : '' }}
                    {{ $disabled ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'cursor-pointer' }}
                    focus:outline-none focus:ring-2 focus:ring-opacity-20
                    dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:border-blue-400
                "
                {{ $attributes }}
            >
                @if($placeholder)
                    <option value="">{{ $placeholder }}</option>
                @endif
                @foreach($options as $option)
                    <option value="{{ $option['value'] }}" {{ old($name, $value) == $option['value'] ? 'selected' : '' }}>
                        {{ $option['text'] }}
                    </option>
                @endforeach
            </select>
        @endif
        
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
        class="absolute inset-y-0 right-8 flex items-center pointer-events-none"
        style="top: {{ $floating ? '0' : ($label ? '2rem' : '0') }};"
    >
        <i class="fas fa-check-circle text-green-500 text-sm"></i>
    </div>
</div>
