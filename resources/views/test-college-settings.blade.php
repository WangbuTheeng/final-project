<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test College Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Test College Settings Update</h1>
        
        <form action="{{ route('college-settings.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label for="college_name" class="block text-sm font-medium text-gray-700 mb-2">
                    College Name
                </label>
                <input type="text" name="college_name" id="college_name" 
                       value="Bajra International College"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <div>
                <label for="college_address" class="block text-sm font-medium text-gray-700 mb-2">
                    College Address
                </label>
                <textarea name="college_address" id="college_address" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          required>Kathmandu, Nepal</textarea>
            </div>

            <div>
                <label for="college_website" class="block text-sm font-medium text-gray-700 mb-2">
                    Website URL
                </label>
                <input type="text" name="college_website" id="college_website" 
                       value="www.bajracollege.edu.np"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="college_phone" class="block text-sm font-medium text-gray-700 mb-2">
                    Phone
                </label>
                <input type="text" name="college_phone" id="college_phone" 
                       value="+977-1-4444444"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="college_email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input type="email" name="college_email" id="college_email" 
                       value="info@bajracollege.edu.np"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="grading_system" class="block text-sm font-medium text-gray-700 mb-2">
                    Grading System
                </label>
                <select name="grading_system" id="grading_system" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="both" selected>Both Percentage & GPA</option>
                    <option value="percentage">Percentage Only</option>
                    <option value="gpa">GPA Only</option>
                </select>
            </div>

            <div>
                <label for="pass_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                    Pass Percentage
                </label>
                <input type="number" name="pass_percentage" id="pass_percentage" 
                       value="40"
                       min="0" max="100" step="0.01"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="space-y-2">
                <div class="flex items-center">
                    <input type="checkbox" name="show_grade_points" id="show_grade_points" checked
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="show_grade_points" class="ml-2 block text-sm text-gray-700">
                        Show Grade Points
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="show_percentage" id="show_percentage" checked
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="show_percentage" class="ml-2 block text-sm text-gray-700">
                        Show Percentage
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="marksheet_settings[show_logo]" id="show_logo" checked
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="show_logo" class="ml-2 block text-sm text-gray-700">
                        Show College Logo
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="marksheet_settings[show_signatures]" id="show_signatures" checked
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="show_signatures" class="ml-2 block text-sm text-gray-700">
                        Show Authority Signatures
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="marksheet_settings[show_issue_date]" id="show_issue_date" checked
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="show_issue_date" class="ml-2 block text-sm text-gray-700">
                        Show Issue Date
                    </label>
                </div>
            </div>

            <div>
                <label for="watermark_text" class="block text-sm font-medium text-gray-700 mb-2">
                    Watermark Text
                </label>
                <input type="text" name="marksheet_settings[watermark_text]" id="watermark_text" 
                       value="OFFICIAL"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Update Settings
                </button>
            </div>
        </form>
    </div>
</body>
</html>
