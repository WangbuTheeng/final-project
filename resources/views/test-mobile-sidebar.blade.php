@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Mobile Sidebar Test</h1>
        
        <div class="space-y-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-blue-900 mb-2">Desktop View (≥1024px)</h2>
                <p class="text-blue-800">The sidebar should be permanently visible on the left side.</p>
            </div>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-green-900 mb-2">Mobile View (<1024px)</h2>
                <p class="text-green-800 mb-3">The sidebar should be hidden, and you should see:</p>
                <ul class="list-disc list-inside text-green-700 space-y-1">
                    <li>A hamburger menu button (☰) in the top-left corner</li>
                    <li>A red test button in the bottom-right corner</li>
                    <li>Tap either button to open the sidebar</li>
                    <li>Tap outside the sidebar or the X button to close it</li>
                </ul>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-yellow-900 mb-2">Testing Instructions</h2>
                <ol class="list-decimal list-inside text-yellow-800 space-y-1">
                    <li>Resize your browser window to be narrower than 1024px</li>
                    <li>Or use browser dev tools (F12) and toggle device simulation</li>
                    <li>Look for the hamburger button (☰) in the top navigation</li>
                    <li>Click the hamburger button to open the sidebar</li>
                    <li>Click outside the sidebar to close it</li>
                </ol>
            </div>
            
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Current Screen Size</h2>
                <p class="text-gray-700">
                    <span class="font-mono bg-gray-200 px-2 py-1 rounded" id="screen-size">Loading...</span>
                </p>
                <p class="text-sm text-gray-600 mt-2">
                    Mobile sidebar should appear when width < 1024px
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function updateScreenSize() {
    const screenSizeElement = document.getElementById('screen-size');
    if (screenSizeElement) {
        const width = window.innerWidth;
        const height = window.innerHeight;
        const isMobile = width < 1024;
        screenSizeElement.textContent = `${width}x${height} (${isMobile ? 'Mobile' : 'Desktop'})`;
        screenSizeElement.className = `font-mono px-2 py-1 rounded ${isMobile ? 'bg-green-200 text-green-800' : 'bg-blue-200 text-blue-800'}`;
    }
}

// Update screen size on load and resize
document.addEventListener('DOMContentLoaded', updateScreenSize);
window.addEventListener('resize', updateScreenSize);
</script>
@endsection
