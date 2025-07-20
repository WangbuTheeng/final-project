<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CollegeSetting;
use Illuminate\Support\Facades\Storage;

class CollegeSettingController extends Controller
{
    /**
     * Display the college settings form
     */
    public function index()
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to College Settings.');
        }
        
        $settings = CollegeSetting::getSettings();
        
        return view('college-settings.index', compact('settings'));
    }

    /**
     * Update college settings
     */
    public function update(Request $request)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to update College Settings.');
        }

        // Debug: Log all request data
        \Log::info('College Settings Request Data:', $request->all());

        $request->validate([
            'college_name' => ['required', 'string', 'max:255'],
            'college_address' => ['required', 'string'],
            'college_phone' => ['nullable', 'string', 'max:20'],
            'college_email' => ['nullable', 'email', 'max:255'],
            'college_website' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'result_header' => ['nullable', 'string'],
            'result_footer' => ['nullable', 'string'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'principal_signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'],
            'exam_controller_name' => ['nullable', 'string', 'max:255'],
            'exam_controller_signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'],
            'registrar_name' => ['nullable', 'string', 'max:255'],
            'registrar_signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'],
            'class_teacher_name' => ['nullable', 'string', 'max:255'],
            'class_teacher_signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'],
            'hod_name' => ['nullable', 'string', 'max:255'],
            'hod_signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'],
            'grading_system' => ['required', 'in:percentage,gpa,both'],
            'pass_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'show_grade_points' => ['nullable'],
            'show_percentage' => ['nullable'],
            'marksheet_settings' => ['nullable'],
        ]);

        $settings = CollegeSetting::getSettings();
        
        // Handle file uploads
        $data = $request->except(['logo', 'principal_signature', 'exam_controller_signature', 'registrar_signature', 'class_teacher_signature', 'hod_signature']);

        // Normalize website URL
        if (!empty($data['college_website'])) {
            $data['college_website'] = $this->normalizeWebsiteUrl($data['college_website']);
        }
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            
            $logoPath = $request->file('logo')->store('college/logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        // Handle signature uploads
        $signatureFields = [
            'principal_signature' => 'principal_signature_path',
            'exam_controller_signature' => 'exam_controller_signature_path',
            'registrar_signature' => 'registrar_signature_path',
            'class_teacher_signature' => 'class_teacher_signature_path',
            'hod_signature' => 'hod_signature_path',
        ];

        foreach ($signatureFields as $inputField => $dbField) {
            if ($request->hasFile($inputField)) {
                // Delete old signature if exists
                if ($settings->$dbField && Storage::disk('public')->exists($settings->$dbField)) {
                    Storage::disk('public')->delete($settings->$dbField);
                }
                
                $signaturePath = $request->file($inputField)->store('college/signatures', 'public');
                $data[$dbField] = $signaturePath;
            }
        }

        // Handle checkboxes
        $data['show_grade_points'] = $request->has('show_grade_points');
        $data['show_percentage'] = $request->has('show_percentage');

        // Handle marksheet settings - fix checkbox handling
        $marksheetSettingsInput = $request->input('marksheet_settings', []);
        $marksheetSettings = [
            'show_logo' => isset($marksheetSettingsInput['show_logo']),
            'show_signatures' => isset($marksheetSettingsInput['show_signatures']),
            'show_qr_code' => isset($marksheetSettingsInput['show_qr_code']),
            'watermark_text' => $marksheetSettingsInput['watermark_text'] ?? 'OFFICIAL',
            'show_issue_date' => isset($marksheetSettingsInput['show_issue_date']),
            'show_grading_scale' => isset($marksheetSettingsInput['show_grading_scale']),
        ];
        $data['marksheet_settings'] = $marksheetSettings;

        // Debug: Log the data being updated
        \Log::info('College Settings Update Data:', $data);

        try {
            $settings->update($data);
            \Log::info('College settings updated successfully');

            return redirect()->route('college-settings.index')
                ->with('success', 'College settings updated successfully!');
        } catch (\Exception $e) {
            \Log::error('College Settings Update Error: ' . $e->getMessage());

            return redirect()->route('college-settings.index')
                ->with('error', 'Failed to update college settings: ' . $e->getMessage());
        }
    }

    /**
     * Delete uploaded file
     */
    public function deleteFile(Request $request)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to delete College Setting files.');
        }

        $request->validate([
            'field' => ['required', 'in:logo_path,principal_signature_path,exam_controller_signature_path,registrar_signature_path,class_teacher_signature_path,hod_signature_path'],
        ]);

        $settings = CollegeSetting::getSettings();
        $field = $request->field;

        if ($settings->$field && Storage::disk('public')->exists($settings->$field)) {
            Storage::disk('public')->delete($settings->$field);
            $settings->update([$field => null]);
            
            return response()->json(['success' => true, 'message' => 'File deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'File not found']);
    }

    /**
     * Normalize website URL to ensure it's properly formatted
     */
    private function normalizeWebsiteUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        // Remove any whitespace
        $url = trim($url);

        // If URL doesn't start with http:// or https://, add https://
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }

        // Validate the URL format
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        // If validation fails, try without protocol for basic domain validation
        $urlWithoutProtocol = preg_replace('/^https?:\/\//', '', $url);
        if (preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]?\.[a-zA-Z]{2,}$/', $urlWithoutProtocol) ||
            preg_match('/^www\.[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]?\.[a-zA-Z]{2,}$/', $urlWithoutProtocol)) {
            return 'https://' . $urlWithoutProtocol;
        }

        // Return the original URL if we can't normalize it
        return $url;
    }
}
