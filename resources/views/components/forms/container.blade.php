@props([
    'title' => '',
    'subtitle' => '',
    'icon' => '',
    'method' => 'POST',
    'action' => '',
    'multipart' => false,
    'steps' => [],
    'currentStep' => 1,
    'showProgress' => false,
])

@php
    $isMultiStep = !empty($steps);
    $totalSteps = count($steps);
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-200">
    @if($title || $isMultiStep)
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 px-6 py-6 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @if($icon)
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="{{ $icon }} text-white text-xl"></i>
                        </div>
                    @endif
                    <div>
                        @if($title)
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h2>
                        @endif
                        @if($subtitle)
                            <p class="text-gray-600 dark:text-gray-300 mt-1">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
                
                @if($isMultiStep)
                    <!-- Step Indicator -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                            Step {{ $currentStep }} of {{ $totalSteps }}
                        </span>
                        <div class="w-16 h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                            <div 
                                class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-300 ease-out"
                                style="width: {{ ($currentStep / $totalSteps) * 100 }}%"
                            ></div>
                        </div>
                    </div>
                @endif
            </div>
            
            @if($isMultiStep && $showProgress)
                <!-- Progress Steps -->
                <div class="mt-6">
                    <div class="flex items-center justify-between">
                        @foreach($steps as $index => $step)
                            @php
                                $stepNumber = $index + 1;
                                $isActive = $stepNumber == $currentStep;
                                $isCompleted = $stepNumber < $currentStep;
                                $isUpcoming = $stepNumber > $currentStep;
                            @endphp
                            
                            <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                                <!-- Step Circle -->
                                <div class="relative">
                                    <div class="
                                        w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-200
                                        {{ $isCompleted ? 'bg-green-500 text-white' : '' }}
                                        {{ $isActive ? 'bg-blue-500 text-white ring-4 ring-blue-200 dark:ring-blue-800' : '' }}
                                        {{ $isUpcoming ? 'bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400' : '' }}
                                    ">
                                        @if($isCompleted)
                                            <i class="fas fa-check"></i>
                                        @else
                                            {{ $stepNumber }}
                                        @endif
                                    </div>
                                    
                                    <!-- Step Label -->
                                    <div class="absolute top-12 left-1/2 transform -translate-x-1/2 whitespace-nowrap">
                                        <span class="text-xs font-medium {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ $step['title'] ?? "Step $stepNumber" }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Connector Line -->
                                @if(!$loop->last)
                                    <div class="flex-1 h-0.5 mx-4 {{ $stepNumber < $currentStep ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600' }} transition-colors duration-200"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
    
    <!-- Form Content -->
    <form 
        method="{{ $method }}" 
        action="{{ $action }}"
        {{ $multipart ? 'enctype=multipart/form-data' : '' }}
        x-data="formHandler()"
        @submit="handleSubmit"
        {{ $attributes->except(['class']) }}
        class="relative {{ $attributes->get('class', '') }}"
    >
        @if($method !== 'GET')
            @csrf
        @endif
        
        @if(in_array($method, ['PUT', 'PATCH', 'DELETE']))
            @method($method)
        @endif
        
        <!-- Loading Overlay -->
        <div 
            x-show="loading" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm z-50 flex items-center justify-center"
            style="display: none;"
        >
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-8 w-8 border-2 border-blue-500 border-t-transparent"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Processing...</span>
            </div>
        </div>
        
        <!-- Form Fields -->
        <div class="p-6 space-y-6">
            {{ $slot }}
        </div>
        
        <!-- Form Actions -->
        @if(isset($actions))
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between">
                {{ $actions }}
            </div>
        @endif
    </form>
</div>

<script>
function formHandler() {
    return {
        loading: false,
        
        handleSubmit(event) {
            // Add loading state
            this.loading = true;
            
            // Add any custom validation or processing here
            
            // Form will submit normally unless prevented
        },
        
        // Method to manually set loading state
        setLoading(state) {
            this.loading = state;
        }
    }
}
</script>
