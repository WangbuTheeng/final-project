<!DOCTYPE html>
<html>
<head>
    <title>API Debug Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">API Debug Test</h1>
    
    <div class="space-y-4">
        <button onclick="testAuth()" class="bg-blue-500 text-white px-4 py-2 rounded">Test Auth</button>
        <button onclick="testCourses()" class="bg-green-500 text-white px-4 py-2 rounded">Test Courses</button>
        <button onclick="testTestCourses()" class="bg-purple-500 text-white px-4 py-2 rounded">Test Test-Courses</button>
    </div>
    
    <div id="results" class="mt-8 p-4 bg-gray-100 rounded">
        <h2 class="font-bold">Results:</h2>
        <pre id="output"></pre>
    </div>

    <script>
        function log(message) {
            document.getElementById('output').textContent += message + '\n';
        }

        function clearLog() {
            document.getElementById('output').textContent = '';
        }

        async function testAuth() {
            clearLog();
            log('Testing auth endpoint...');
            
            try {
                const response = await fetch('/api/test-auth', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                log('Response status: ' + response.status);
                const data = await response.json();
                log('Response data: ' + JSON.stringify(data, null, 2));
            } catch (error) {
                log('Error: ' + error.message);
            }
        }

        async function testCourses() {
            clearLog();
            log('Testing courses endpoint...');
            
            try {
                const response = await fetch('/api/courses?faculty_id=1&academic_year_id=1', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                log('Response status: ' + response.status);
                const data = await response.json();
                log('Response data: ' + JSON.stringify(data, null, 2));
            } catch (error) {
                log('Error: ' + error.message);
            }
        }

        async function testTestCourses() {
            clearLog();
            log('Testing test-courses endpoint...');
            
            try {
                const response = await fetch('/api/test-courses', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                log('Response status: ' + response.status);
                const data = await response.json();
                log('Response data: ' + JSON.stringify(data, null, 2));
            } catch (error) {
                log('Error: ' + error.message);
            }
        }
    </script>
</body>
</html>
