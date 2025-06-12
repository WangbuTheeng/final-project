<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Tailwind and Alpine</title>
    
    <!-- Tailwind CSS direct CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Tailwind Test</h1>
        
        <div class="bg-blue-100 text-blue-800 p-4 rounded-md mb-4">
            This is a Tailwind styled box
        </div>
        
        <div x-data="{ open: false }" class="mb-4">
            <button 
                @click="open = !open" 
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
            >
                Toggle Alpine Panel
            </button>
            
            <div x-show="open" class="mt-4 p-4 bg-indigo-100 rounded-md">
                This panel is controlled by Alpine.js
            </div>
        </div>
    </div>
</body>
</html>
