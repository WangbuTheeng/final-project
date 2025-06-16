@extends('layouts.dashboard')

@section('title', 'College Settings')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">College Settings</h1>
            <p class="text-gray-600 mt-1">Manage college information, signatures, and marksheet settings</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('college-settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic College Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="college_name" class="block text-sm font-medium text-gray-700 mb-2">
                        College Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="college_name" id="college_name" 
                           value="{{ old('college_name', $settings->college_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('college_name') border-red-300 @enderror"
                           required>
                    @error('college_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="college_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number
                    </label>
                    <input type="text" name="college_phone" id="college_phone" 
                           value="{{ old('college_phone', $settings->college_phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('college_phone') border-red-300 @enderror">
                    @error('college_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="college_address" class="block text-sm font-medium text-gray-700 mb-2">
                        College Address <span class="text-red-500">*</span>
                    </label>
                    <textarea name="college_address" id="college_address" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('college_address') border-red-300 @enderror"
                              required>{{ old('college_address', $settings->college_address) }}</textarea>
                    @error('college_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="college_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input type="email" name="college_email" id="college_email" 
                           value="{{ old('college_email', $settings->college_email) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('college_email') border-red-300 @enderror">
                    @error('college_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="college_website" class="block text-sm font-medium text-gray-700 mb-2">
                        Website URL
                    </label>
                    <input type="url" name="college_website" id="college_website" 
                           value="{{ old('college_website', $settings->college_website) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('college_website') border-red-300 @enderror">
                    @error('college_website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Logo Upload -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">College Logo</h2>
            
            <div class="space-y-4">
                @if($settings->logo_path)
                    <div class="flex items-center space-x-4">
                        <img src="{{ Storage::url($settings->logo_path) }}" alt="College Logo" class="h-16 w-16 object-contain border border-gray-300 rounded">
                        <div>
                            <p class="text-sm text-gray-600">Current logo</p>
                            <button type="button" onclick="deleteFile('logo_path')" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                        </div>
                    </div>
                @endif
                
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload New Logo
                    </label>
                    <input type="file" name="logo" id="logo" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('logo') border-red-300 @enderror">
                    <p class="mt-1 text-sm text-gray-500">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</p>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Marksheet Header/Footer -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Marksheet Content</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="result_header" class="block text-sm font-medium text-gray-700 mb-2">
                        Result Header Text
                    </label>
                    <textarea name="result_header" id="result_header" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('result_header') border-red-300 @enderror"
                              placeholder="e.g., Office of the Controller of Examinations">{{ old('result_header', $settings->result_header) }}</textarea>
                    @error('result_header')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="result_footer" class="block text-sm font-medium text-gray-700 mb-2">
                        Result Footer Text
                    </label>
                    <textarea name="result_footer" id="result_footer" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('result_footer') border-red-300 @enderror"
                              placeholder="e.g., This is a computer generated marksheet. No signature required.">{{ old('result_footer', $settings->result_footer) }}</textarea>
                    @error('result_footer')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Authority Signatures -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Authority Signatures</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Principal -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-800">Principal</h3>
                    <div>
                        <label for="principal_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Principal Name
                        </label>
                        <input type="text" name="principal_name" id="principal_name"
                               value="{{ old('principal_name', $settings->principal_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    @if($settings->principal_signature_path)
                        <div class="flex items-center space-x-4">
                            <img src="{{ Storage::url($settings->principal_signature_path) }}" alt="Principal Signature" class="h-12 w-24 object-contain border border-gray-300 rounded">
                            <button type="button" onclick="deleteFile('principal_signature_path')" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                        </div>
                    @endif

                    <div>
                        <label for="principal_signature" class="block text-sm font-medium text-gray-700 mb-2">
                            Principal Signature
                        </label>
                        <input type="file" name="principal_signature" id="principal_signature" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Max size: 1MB</p>
                    </div>
                </div>

                <!-- Exam Controller -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-800">Exam Controller</h3>
                    <div>
                        <label for="exam_controller_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Exam Controller Name
                        </label>
                        <input type="text" name="exam_controller_name" id="exam_controller_name"
                               value="{{ old('exam_controller_name', $settings->exam_controller_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    @if($settings->exam_controller_signature_path)
                        <div class="flex items-center space-x-4">
                            <img src="{{ Storage::url($settings->exam_controller_signature_path) }}" alt="Exam Controller Signature" class="h-12 w-24 object-contain border border-gray-300 rounded">
                            <button type="button" onclick="deleteFile('exam_controller_signature_path')" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                        </div>
                    @endif

                    <div>
                        <label for="exam_controller_signature" class="block text-sm font-medium text-gray-700 mb-2">
                            Exam Controller Signature
                        </label>
                        <input type="file" name="exam_controller_signature" id="exam_controller_signature" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Max size: 1MB</p>
                    </div>
                </div>

                <!-- Registrar -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-800">Registrar</h3>
                    <div>
                        <label for="registrar_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Registrar Name
                        </label>
                        <input type="text" name="registrar_name" id="registrar_name"
                               value="{{ old('registrar_name', $settings->registrar_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    @if($settings->registrar_signature_path)
                        <div class="flex items-center space-x-4">
                            <img src="{{ Storage::url($settings->registrar_signature_path) }}" alt="Registrar Signature" class="h-12 w-24 object-contain border border-gray-300 rounded">
                            <button type="button" onclick="deleteFile('registrar_signature_path')" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                        </div>
                    @endif

                    <div>
                        <label for="registrar_signature" class="block text-sm font-medium text-gray-700 mb-2">
                            Registrar Signature
                        </label>
                        <input type="file" name="registrar_signature" id="registrar_signature" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Max size: 1MB</p>
                    </div>
                </div>

                <!-- Class Teacher -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-800">Class Teacher</h3>
                    <div>
                        <label for="class_teacher_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Class Teacher Name
                        </label>
                        <input type="text" name="class_teacher_name" id="class_teacher_name"
                               value="{{ old('class_teacher_name', $settings->class_teacher_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    @if($settings->class_teacher_signature_path)
                        <div class="flex items-center space-x-4">
                            <img src="{{ Storage::url($settings->class_teacher_signature_path) }}" alt="Class Teacher Signature" class="h-12 w-24 object-contain border border-gray-300 rounded">
                            <button type="button" onclick="deleteFile('class_teacher_signature_path')" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                        </div>
                    @endif

                    <div>
                        <label for="class_teacher_signature" class="block text-sm font-medium text-gray-700 mb-2">
                            Class Teacher Signature
                        </label>
                        <input type="file" name="class_teacher_signature" id="class_teacher_signature" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Max size: 1MB</p>
                    </div>
                </div>

                <!-- HOD -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-800">Head of Department</h3>
                    <div>
                        <label for="hod_name" class="block text-sm font-medium text-gray-700 mb-2">
                            HOD Name
                        </label>
                        <input type="text" name="hod_name" id="hod_name"
                               value="{{ old('hod_name', $settings->hod_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    @if($settings->hod_signature_path)
                        <div class="flex items-center space-x-4">
                            <img src="{{ Storage::url($settings->hod_signature_path) }}" alt="HOD Signature" class="h-12 w-24 object-contain border border-gray-300 rounded">
                            <button type="button" onclick="deleteFile('hod_signature_path')" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                        </div>
                    @endif

                    <div>
                        <label for="hod_signature" class="block text-sm font-medium text-gray-700 mb-2">
                            HOD Signature
                        </label>
                        <input type="file" name="hod_signature" id="hod_signature" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Max size: 1MB</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grading System -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Grading System</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="grading_system" class="block text-sm font-medium text-gray-700 mb-2">
                        Grading System <span class="text-red-500">*</span>
                    </label>
                    <select name="grading_system" id="grading_system"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="percentage" {{ old('grading_system', $settings->grading_system) == 'percentage' ? 'selected' : '' }}>Percentage Only</option>
                        <option value="gpa" {{ old('grading_system', $settings->grading_system) == 'gpa' ? 'selected' : '' }}>GPA Only</option>
                        <option value="both" {{ old('grading_system', $settings->grading_system) == 'both' ? 'selected' : '' }}>Both Percentage & GPA</option>
                    </select>
                </div>

                <div>
                    <label for="pass_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                        Pass Percentage <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="pass_percentage" id="pass_percentage"
                           value="{{ old('pass_percentage', $settings->pass_percentage) }}"
                           min="0" max="100" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="md:col-span-2">
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input type="checkbox" name="show_grade_points" id="show_grade_points"
                                   {{ old('show_grade_points', $settings->show_grade_points) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="show_grade_points" class="ml-2 block text-sm text-gray-700">
                                Show Grade Points on Marksheet
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="show_percentage" id="show_percentage"
                                   {{ old('show_percentage', $settings->show_percentage) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="show_percentage" class="ml-2 block text-sm text-gray-700">
                                Show Percentage on Marksheet
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marksheet Display Settings -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Marksheet Display Settings</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="checkbox" name="marksheet_settings[show_logo]" id="show_logo"
                               {{ old('marksheet_settings.show_logo', $settings->marksheet_settings['show_logo'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="show_logo" class="ml-2 block text-sm text-gray-700">
                            Show College Logo
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="marksheet_settings[show_signatures]" id="show_signatures"
                               {{ old('marksheet_settings.show_signatures', $settings->marksheet_settings['show_signatures'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="show_signatures" class="ml-2 block text-sm text-gray-700">
                            Show Authority Signatures
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="marksheet_settings[show_issue_date]" id="show_issue_date"
                               {{ old('marksheet_settings.show_issue_date', $settings->marksheet_settings['show_issue_date'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="show_issue_date" class="ml-2 block text-sm text-gray-700">
                            Show Issue Date
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="marksheet_settings[show_grading_scale]" id="show_grading_scale"
                               {{ old('marksheet_settings.show_grading_scale', $settings->marksheet_settings['show_grading_scale'] ?? false) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="show_grading_scale" class="ml-2 block text-sm text-gray-700">
                            Show Grading Scale
                        </label>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="checkbox" name="marksheet_settings[show_qr_code]" id="show_qr_code"
                               {{ old('marksheet_settings.show_qr_code', $settings->marksheet_settings['show_qr_code'] ?? false) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="show_qr_code" class="ml-2 block text-sm text-gray-700">
                            Show QR Code
                        </label>
                    </div>

                    <div>
                        <label for="watermark_text" class="block text-sm font-medium text-gray-700 mb-2">
                            Watermark Text
                        </label>
                        <input type="text" name="marksheet_settings[watermark_text]" id="watermark_text"
                               value="{{ old('marksheet_settings.watermark_text', $settings->marksheet_settings['watermark_text'] ?? 'OFFICIAL') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Save Settings
            </button>
        </div>
    </form>
</div>

<script>
function deleteFile(field) {
    if (confirm('Are you sure you want to delete this file?')) {
        fetch('{{ route("college-settings.delete-file") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ field: field })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting file: ' + data.message);
            }
        });
    }
}

// Add form submission debugging
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form is being submitted...');
            const formData = new FormData(form);

            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
        });
    }
});
</script>
@endsection
