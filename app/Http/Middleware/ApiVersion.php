<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersion
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $version = 'v1'): Response
    {
        // Set the API version in the request
        $request->attributes->set('api_version', $version);
        
        // Get version from header or default
        $requestedVersion = $this->getRequestedVersion($request);
        
        // Validate version
        if (!$this->isValidVersion($requestedVersion)) {
            return response()->json([
                'success' => false,
                'message' => 'Unsupported API version',
                'supported_versions' => $this->getSupportedVersions(),
                'requested_version' => $requestedVersion,
            ], 400);
        }

        // Check if version is deprecated
        if ($this->isDeprecatedVersion($requestedVersion)) {
            $response = $next($request);
            
            return $response->withHeaders([
                'X-API-Deprecated' => 'true',
                'X-API-Sunset' => $this->getDeprecationDate($requestedVersion),
                'X-API-Migration-Guide' => url('/docs/api/migration/' . $requestedVersion),
            ]);
        }

        $response = $next($request);

        // Add version headers
        return $response->withHeaders([
            'X-API-Version' => $requestedVersion,
            'X-API-Latest-Version' => $this->getLatestVersion(),
        ]);
    }

    /**
     * Get the requested API version from headers or URL.
     */
    protected function getRequestedVersion(Request $request): string
    {
        // Check Accept header first
        $acceptHeader = $request->header('Accept');
        if ($acceptHeader && preg_match('/application\/vnd\.college\.v(\d+)\+json/', $acceptHeader, $matches)) {
            return 'v' . $matches[1];
        }

        // Check X-API-Version header
        $versionHeader = $request->header('X-API-Version');
        if ($versionHeader) {
            return $versionHeader;
        }

        // Check URL path
        $path = $request->path();
        if (preg_match('/^api\/(v\d+)\//', $path, $matches)) {
            return $matches[1];
        }

        // Default to v1
        return 'v1';
    }

    /**
     * Check if the version is valid.
     */
    protected function isValidVersion(string $version): bool
    {
        return in_array($version, $this->getSupportedVersions());
    }

    /**
     * Get supported API versions.
     */
    protected function getSupportedVersions(): array
    {
        return ['v1', 'v2']; // Add new versions as they're released
    }

    /**
     * Check if the version is deprecated.
     */
    protected function isDeprecatedVersion(string $version): bool
    {
        $deprecatedVersions = [
            // 'v1' => '2024-12-31', // Example: v1 deprecated on Dec 31, 2024
        ];

        return array_key_exists($version, $deprecatedVersions);
    }

    /**
     * Get deprecation date for a version.
     */
    protected function getDeprecationDate(string $version): ?string
    {
        $deprecatedVersions = [
            // 'v1' => '2024-12-31',
        ];

        return $deprecatedVersions[$version] ?? null;
    }

    /**
     * Get the latest API version.
     */
    protected function getLatestVersion(): string
    {
        $versions = $this->getSupportedVersions();
        return end($versions);
    }
}
