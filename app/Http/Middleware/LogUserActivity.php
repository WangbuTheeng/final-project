<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (auth()->check()) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * Log user activity
     */
    private function logActivity(Request $request, Response $response)
    {
        // Skip logging for certain routes to avoid noise
        $skipRoutes = [
            'activity-logs.*',
            'api.*',
            'livewire.*',
            '*.css',
            '*.js',
            '*.png',
            '*.jpg',
            '*.jpeg',
            '*.gif',
            '*.svg',
            '*.ico',
            '*.woff',
            '*.woff2',
            '*.ttf',
            '*.eot'
        ];

        $currentRoute = $request->route() ? $request->route()->getName() : null;
        
        foreach ($skipRoutes as $pattern) {
            if ($currentRoute && fnmatch($pattern, $currentRoute)) {
                return;
            }
        }

        // Skip if it's an AJAX request for certain actions
        if ($request->ajax() && in_array($request->method(), ['GET'])) {
            return;
        }

        // Determine the action description
        $description = $this->getActionDescription($request);
        
        if (!$description) {
            return; // Skip if we can't determine a meaningful description
        }

        // Prepare properties
        $properties = [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'route_name' => $currentRoute,
            'status_code' => $response->getStatusCode(),
            'session_id' => session()->getId(),
        ];

        // Add request data for certain methods
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $requestData = $request->except(['password', 'password_confirmation', '_token', '_method']);
            if (!empty($requestData)) {
                $properties['request_data'] = $requestData;
            }
        }

        // Add query parameters for GET requests
        if ($request->method() === 'GET' && $request->query()) {
            $properties['query_params'] = $request->query();
        }

        // Log the activity
        activity()
            ->causedBy(auth()->user())
            ->withProperties($properties)
            ->useLog('user_activity')
            ->log($description);
    }

    /**
     * Get action description based on request
     */
    private function getActionDescription(Request $request): ?string
    {
        $method = $request->method();
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;
        $uri = $request->getPathInfo();

        // Handle specific route patterns
        if ($routeName) {
            $routeParts = explode('.', $routeName);
            $resource = $routeParts[0] ?? '';
            $action = $routeParts[1] ?? '';

            switch ($action) {
                case 'index':
                    return "Viewed {$resource} list";
                case 'show':
                    return "Viewed {$resource} details";
                case 'create':
                    return "Accessed {$resource} creation form";
                case 'store':
                    return "Created new {$resource}";
                case 'edit':
                    return "Accessed {$resource} edit form";
                case 'update':
                    return "Updated {$resource}";
                case 'destroy':
                    return "Deleted {$resource}";
                default:
                    return "Performed {$action} on {$resource}";
            }
        }

        // Handle dashboard and other common routes
        if (str_contains($uri, 'dashboard')) {
            return 'Accessed dashboard';
        }

        if (str_contains($uri, 'login')) {
            return $method === 'POST' ? 'Attempted login' : 'Accessed login page';
        }

        if (str_contains($uri, 'logout')) {
            return 'Logged out';
        }

        if (str_contains($uri, 'profile')) {
            return $method === 'GET' ? 'Viewed profile' : 'Updated profile';
        }

        // Handle API endpoints
        if (str_contains($uri, '/api/')) {
            return "API {$method} request to {$uri}";
        }

        // Generic descriptions based on HTTP method
        switch ($method) {
            case 'GET':
                return "Accessed page: {$uri}";
            case 'POST':
                return "Submitted form: {$uri}";
            case 'PUT':
            case 'PATCH':
                return "Updated resource: {$uri}";
            case 'DELETE':
                return "Deleted resource: {$uri}";
            default:
                return "Performed {$method} request: {$uri}";
        }
    }
}
