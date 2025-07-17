<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class SecurityService
{
    /**
     * Check for suspicious login attempts.
     */
    public function checkSuspiciousLogin(Request $request, User $user = null): array
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $email = $request->input('email');
        
        $suspiciousFactors = [];
        $riskScore = 0;

        // Check for multiple failed attempts from same IP
        $failedAttempts = RateLimiter::attempts('login-attempts:' . $ip);
        if ($failedAttempts >= 3) {
            $suspiciousFactors[] = 'Multiple failed login attempts from IP';
            $riskScore += 30;
        }

        // Check for login from new location (simplified)
        if ($user && $this->isNewLocation($user, $ip)) {
            $suspiciousFactors[] = 'Login from new location';
            $riskScore += 20;
        }

        // Check for unusual user agent
        if ($this->isUnusualUserAgent($userAgent)) {
            $suspiciousFactors[] = 'Unusual user agent detected';
            $riskScore += 15;
        }

        // Check for known malicious IPs (simplified)
        if ($this->isMaliciousIP($ip)) {
            $suspiciousFactors[] = 'Login from known malicious IP';
            $riskScore += 50;
        }

        // Check for rapid login attempts
        if ($this->hasRapidLoginAttempts($email)) {
            $suspiciousFactors[] = 'Rapid login attempts detected';
            $riskScore += 25;
        }

        return [
            'is_suspicious' => $riskScore >= 40,
            'risk_score' => $riskScore,
            'factors' => $suspiciousFactors,
            'requires_2fa' => $riskScore >= 60,
            'should_block' => $riskScore >= 80,
        ];
    }

    /**
     * Generate secure password with specific requirements.
     */
    public function generateSecurePassword(int $length = 12): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        // Ensure at least one character from each set
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Fill the rest randomly
        $allChars = $lowercase . $uppercase . $numbers . $symbols;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password
        return str_shuffle($password);
    }

    /**
     * Validate password strength.
     */
    public function validatePasswordStrength(string $password): array
    {
        $score = 0;
        $feedback = [];

        // Length check
        if (strlen($password) >= 8) {
            $score += 20;
        } else {
            $feedback[] = 'Password should be at least 8 characters long';
        }

        if (strlen($password) >= 12) {
            $score += 10;
        }

        // Character variety checks
        if (preg_match('/[a-z]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = 'Password should contain lowercase letters';
        }

        if (preg_match('/[A-Z]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = 'Password should contain uppercase letters';
        }

        if (preg_match('/[0-9]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = 'Password should contain numbers';
        }

        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = 'Password should contain special characters';
        }

        // Common password check
        if ($this->isCommonPassword($password)) {
            $score -= 30;
            $feedback[] = 'Password is too common';
        }

        // Sequential characters check
        if ($this->hasSequentialCharacters($password)) {
            $score -= 10;
            $feedback[] = 'Avoid sequential characters';
        }

        $strength = 'weak';
        if ($score >= 80) {
            $strength = 'very_strong';
        } elseif ($score >= 60) {
            $strength = 'strong';
        } elseif ($score >= 40) {
            $strength = 'medium';
        }

        return [
            'score' => max(0, $score),
            'strength' => $strength,
            'feedback' => $feedback,
            'is_acceptable' => $score >= 60,
        ];
    }

    /**
     * Sanitize user input to prevent XSS.
     */
    public function sanitizeInput(string $input): string
    {
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);
        
        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove potentially dangerous tags
        $input = strip_tags($input);
        
        return trim($input);
    }

    /**
     * Validate and sanitize file uploads.
     */
    public function validateFileUpload($file, array $allowedTypes = [], int $maxSize = 5242880): array
    {
        $errors = [];

        if (!$file || !$file->isValid()) {
            $errors[] = 'Invalid file upload';
            return ['valid' => false, 'errors' => $errors];
        }

        // Check file size (default 5MB)
        if ($file->getSize() > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size';
        }

        // Check file type
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        if (!empty($allowedTypes)) {
            $allowedMimes = [];
            $allowedExtensions = [];

            foreach ($allowedTypes as $type) {
                switch ($type) {
                    case 'image':
                        $allowedMimes = array_merge($allowedMimes, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
                        $allowedExtensions = array_merge($allowedExtensions, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        break;
                    case 'document':
                        $allowedMimes = array_merge($allowedMimes, ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
                        $allowedExtensions = array_merge($allowedExtensions, ['pdf', 'doc', 'docx']);
                        break;
                    case 'spreadsheet':
                        $allowedMimes = array_merge($allowedMimes, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
                        $allowedExtensions = array_merge($allowedExtensions, ['xls', 'xlsx']);
                        break;
                }
            }

            if (!in_array($mimeType, $allowedMimes) || !in_array($extension, $allowedExtensions)) {
                $errors[] = 'File type not allowed';
            }
        }

        // Check for executable files
        $dangerousExtensions = ['php', 'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js'];
        if (in_array($extension, $dangerousExtensions)) {
            $errors[] = 'Executable files are not allowed';
        }

        // Scan file content for malicious patterns
        if ($this->containsMaliciousContent($file)) {
            $errors[] = 'File contains potentially malicious content';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'safe_filename' => $this->generateSafeFilename($file),
        ];
    }

    /**
     * Generate CSRF token.
     */
    public function generateCSRFToken(): string
    {
        return Str::random(40);
    }

    /**
     * Validate CSRF token.
     */
    public function validateCSRFToken(string $token, string $sessionToken): bool
    {
        return hash_equals($sessionToken, $token);
    }

    /**
     * Log security event.
     */
    public function logSecurityEvent(string $event, array $data = [], string $level = 'warning'): void
    {
        Log::channel('security')->{$level}($event, array_merge($data, [
            'timestamp' => now()->toISOString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
        ]));
    }

    /**
     * Check if IP is from a new location for user.
     */
    private function isNewLocation(User $user, string $ip): bool
    {
        $knownIPs = Cache::get("user_known_ips:{$user->id}", []);
        return !in_array($ip, $knownIPs);
    }

    /**
     * Check if user agent is unusual.
     */
    private function isUnusualUserAgent(string $userAgent): bool
    {
        // Check for common bot patterns
        $botPatterns = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 'java'
        ];

        foreach ($botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is known to be malicious.
     */
    private function isMaliciousIP(string $ip): bool
    {
        // In a real implementation, this would check against threat intelligence feeds
        $knownMaliciousIPs = Cache::get('malicious_ips', []);
        return in_array($ip, $knownMaliciousIPs);
    }

    /**
     * Check for rapid login attempts.
     */
    private function hasRapidLoginAttempts(string $email): bool
    {
        $key = "rapid_login:{$email}";
        $attempts = Cache::get($key, 0);
        
        Cache::put($key, $attempts + 1, 300); // 5 minutes
        
        return $attempts >= 5; // More than 5 attempts in 5 minutes
    }

    /**
     * Check if password is commonly used.
     */
    private function isCommonPassword(string $password): bool
    {
        $commonPasswords = [
            'password', '123456', '123456789', 'qwerty', 'abc123', 'password123',
            'admin', 'letmein', 'welcome', 'monkey', '1234567890'
        ];

        return in_array(strtolower($password), $commonPasswords);
    }

    /**
     * Check for sequential characters in password.
     */
    private function hasSequentialCharacters(string $password): bool
    {
        $sequences = ['123', 'abc', 'qwe', 'asd', 'zxc'];
        
        foreach ($sequences as $sequence) {
            if (stripos($password, $sequence) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check file for malicious content.
     */
    private function containsMaliciousContent($file): bool
    {
        $content = file_get_contents($file->getPathname());
        
        // Check for PHP tags in non-PHP files
        if ($file->getClientOriginalExtension() !== 'php' && 
            (strpos($content, '<?php') !== false || strpos($content, '<?=') !== false)) {
            return true;
        }

        // Check for script tags
        if (stripos($content, '<script') !== false) {
            return true;
        }

        // Check for suspicious patterns
        $maliciousPatterns = [
            'eval(', 'exec(', 'system(', 'shell_exec(', 'passthru(',
            'base64_decode(', 'gzinflate(', 'str_rot13('
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate safe filename.
     */
    private function generateSafeFilename($file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        
        // Add timestamp to prevent conflicts
        $timestamp = time();
        
        return "{$filename}_{$timestamp}.{$extension}";
    }
}
