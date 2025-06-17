@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Grading System</h1>
            <p class="mt-1 text-sm text-gray-500">Create a new custom grading system with grade scales</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('grading-systems.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Grading Systems
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Create Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Grading System Information</h3>
            <p class="mt-1 text-sm text-gray-500">Fill in the grading system details and define grade scales</p>
        </div>

        <form method="POST" action="{{ route('grading-systems.store') }}" class="p-6">
            @csrf

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        System Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-300 @enderror"
                           placeholder="e.g., TU Grading System"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        System Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           value="{{ old('code') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('code') border-red-300 @enderror"
                           placeholder="e.g., TU"
                           required>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Unique identifier for this grading system</p>
                </div>

                <!-- Order Sequence -->
                <div>
                    <label for="order_sequence" class="block text-sm font-medium text-gray-700 mb-2">
                        Display Order <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="order_sequence" 
                           id="order_sequence" 
                           value="{{ old('order_sequence', 1) }}"
                           min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('order_sequence') border-red-300 @enderror"
                           required>
                    @error('order_sequence')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Default System -->
                <div class="flex items-center">
                    <input type="checkbox"
                           name="is_default"
                           id="is_default"
                           value="1"
                           {{ old('is_default') ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_default" class="ml-2 block text-sm text-gray-900">
                        Set as Default Grading System
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-8">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description <span class="text-gray-400">(Optional)</span>
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-300 @enderror"
                          placeholder="Describe this grading system...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Grade Scales Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-medium text-gray-900">Grade Scales</h4>
                    <button type="button"
                            id="add-grade-scale"
                            class="inline-flex items-center px-3 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        <i class="fas fa-plus mr-2"></i>
                        Add Grade Scale
                    </button>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Grade Scale Guidelines</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Define grade letters (A+, A, B+, B, C+, C, D, F, etc.)</li>
                                    <li>Set percentage ranges that don't overlap</li>
                                    <li>Assign grade points for GPA calculation</li>
                                    <li>Add descriptive remarks for each grade</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="grade-scales-container">
                    <!-- Grade scales will be added here dynamically -->
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('grading-systems.index') }}"
                   class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>
                    Create Grading System
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addGradeScaleBtn = document.getElementById('add-grade-scale');
    const gradeScalesContainer = document.getElementById('grade-scales-container');
    let gradeScaleIndex = 0;

    // Add initial grade scale
    addGradeScale();

    addGradeScaleBtn.addEventListener('click', function() {
        addGradeScale();
    });

    function addGradeScale() {
        const gradeScaleHtml = `
            <div class="grade-scale-item border border-gray-200 rounded-md p-4 mb-4" data-index="${gradeScaleIndex}">
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-sm font-medium text-gray-900">Grade Scale #${gradeScaleIndex + 1}</h5>
                    <button type="button" class="remove-grade-scale text-red-600 hover:text-red-800" onclick="removeGradeScale(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Grade Letter <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="grade_scales[${gradeScaleIndex}][grade_letter]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="A+"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Min % <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="grade_scales[${gradeScaleIndex}][min_percent]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               min="0" max="100" step="0.01"
                               placeholder="80"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Max % <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="grade_scales[${gradeScaleIndex}][max_percent]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               min="0" max="100" step="0.01"
                               placeholder="100"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Grade Points <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="grade_scales[${gradeScaleIndex}][grade_point]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               min="0" max="10" step="0.01"
                               placeholder="4.0"
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <input type="text" 
                               name="grade_scales[${gradeScaleIndex}][description]" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Excellent">
                    </div>
                </div>
            </div>
        `;
        
        gradeScalesContainer.insertAdjacentHTML('beforeend', gradeScaleHtml);
        gradeScaleIndex++;
    }

    window.removeGradeScale = function(button) {
        const gradeScaleItem = button.closest('.grade-scale-item');
        if (gradeScalesContainer.children.length > 1) {
            gradeScaleItem.remove();
        } else {
            alert('At least one grade scale is required.');
        }
    };
});
</script>
@endpush
@endsection
