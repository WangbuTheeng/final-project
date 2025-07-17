<?php

namespace App\Http\Middleware;

use App\Services\SecurityService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    protected $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip sanitization for certain routes
        if ($this->shouldSkipSanitization($request)) {
            return $next($request);
        }

        // Sanitize input data
        $this->sanitizeRequestData($request);

        return $next($request);
    }

    /**
     * Sanitize request data.
     */
    private function sanitizeRequestData(Request $request): void
    {
        // Get all input data
        $input = $request->all();

        // Recursively sanitize the input
        $sanitized = $this->sanitizeArray($input);

        // Replace the request input with sanitized data
        $request->replace($sanitized);
    }

    /**
     * Recursively sanitize array data.
     */
    private function sanitizeArray(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value, $key);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize string value based on field type.
     */
    private function sanitizeString(string $value, string $key): string
    {
        // Skip sanitization for certain fields
        if ($this->shouldSkipField($key)) {
            return $value;
        }

        // Email fields
        if (str_contains($key, 'email')) {
            return filter_var($value, FILTER_SANITIZE_EMAIL);
        }

        // URL fields
        if (str_contains($key, 'url') || str_contains($key, 'website')) {
            return filter_var($value, FILTER_SANITIZE_URL);
        }

        // Phone fields
        if (str_contains($key, 'phone') || str_contains($key, 'mobile')) {
            return preg_replace('/[^0-9+\-\(\)\s]/', '', $value);
        }

        // Numeric fields
        if (str_contains($key, 'amount') || str_contains($key, 'price') || str_contains($key, 'cost')) {
            return preg_replace('/[^0-9.\-]/', '', $value);
        }

        // Default sanitization
        return $this->securityService->sanitizeInput($value);
    }

    /**
     * Check if sanitization should be skipped for this request.
     */
    private function shouldSkipSanitization(Request $request): bool
    {
        // Skip for API routes that handle their own validation
        if ($request->is('api/*')) {
            return true;
        }

        // Skip for file upload routes
        if ($request->hasFile('*')) {
            return true;
        }

        // Skip for specific routes
        $skipRoutes = [
            'password/*',
            'auth/*',
        ];

        foreach ($skipRoutes as $route) {
            if ($request->is($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a specific field should skip sanitization.
     */
    private function shouldSkipField(string $key): bool
    {
        $skipFields = [
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            '_token',
            '_method',
            'csrf_token',
        ];

        return in_array($key, $skipFields);
    }
}
