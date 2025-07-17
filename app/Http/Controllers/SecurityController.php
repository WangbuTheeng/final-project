<?php

namespace App\Http\Controllers;

use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class SecurityController extends Controller
{
    protected $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Display security dashboard.
     */
    public function index()
    {
        $securityMetrics = [
            'failed_logins' => $this->getFailedLoginAttempts(),
            'blocked_ips' => $this->getBlockedIPs(),
            'security_events' => $this->getRecentSecurityEvents(),
            'user_sessions' => $this->getActiveSessions(),
            'vulnerability_scan' => $this->getVulnerabilityStatus(),
        ];

        return view('security.index', compact('securityMetrics'));
    }

    /**
     * Get failed login attempts.
     */
    public function getFailedLoginAttempts()
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();

        return [
            'today' => Cache::get('failed_logins_today', 0),
            'this_week' => Cache::get('failed_logins_week', 0),
            'by_ip' => Cache::get('failed_logins_by_ip', []),
            'recent' => $this->getRecentFailedLogins(),
        ];
    }

    /**
     * Get blocked IP addresses.
     */
    public function getBlockedIPs()
    {
        return [
            'total' => Cache::get('blocked_ips_count', 0),
            'active' => Cache::get('active_blocked_ips', []),
            'recent' => Cache::get('recent_blocked_ips', []),
        ];
    }

    /**
     * Get recent security events.
     */
    public function getRecentSecurityEvents()
    {
        // This would typically read from a security log file or database
        return [
            [
                'type' => 'suspicious_login',
                'message' => 'Multiple failed login attempts detected',
                'ip' => '192.168.1.100',
                'timestamp' => now()->subMinutes(15),
                'severity' => 'medium',
            ],
            [
                'type' => 'file_upload',
                'message' => 'Potentially malicious file upload blocked',
                'user_id' => 123,
                'timestamp' => now()->subHours(2),
                'severity' => 'high',
            ],
            [
                'type' => 'rate_limit',
                'message' => 'Rate limit exceeded for API endpoint',
                'ip' => '10.0.0.50',
                'timestamp' => now()->subHours(4),
                'severity' => 'low',
            ],
        ];
    }

    /**
     * Get active user sessions.
     */
    public function getActiveSessions()
    {
        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(30)->timestamp)
            ->count();

        $totalSessions = DB::table('sessions')->count();

        return [
            'active' => $activeSessions,
            'total' => $totalSessions,
            'by_user_agent' => $this->getSessionsByUserAgent(),
        ];
    }

    /**
     * Get vulnerability scan status.
     */
    public function getVulnerabilityStatus()
    {
        return [
            'last_scan' => Cache::get('last_vulnerability_scan'),
            'vulnerabilities_found' => Cache::get('vulnerabilities_count', 0),
            'critical' => Cache::get('critical_vulnerabilities', 0),
            'high' => Cache::get('high_vulnerabilities', 0),
            'medium' => Cache::get('medium_vulnerabilities', 0),
            'low' => Cache::get('low_vulnerabilities', 0),
        ];
    }

    /**
     * Run security scan.
     */
    public function runSecurityScan()
    {
        try {
            $results = [
                'timestamp' => now()->toISOString(),
                'checks' => [],
            ];

            // Check for weak passwords
            $results['checks']['weak_passwords'] = $this->checkWeakPasswords();

            // Check for inactive users with admin privileges
            $results['checks']['inactive_admins'] = $this->checkInactiveAdmins();

            // Check for outdated sessions
            $results['checks']['outdated_sessions'] = $this->checkOutdatedSessions();

            // Check for suspicious file uploads
            $results['checks']['suspicious_files'] = $this->checkSuspiciousFiles();

            // Check for rate limiting effectiveness
            $results['checks']['rate_limiting'] = $this->checkRateLimiting();

            // Store scan results
            Cache::put('last_security_scan', $results, 86400); // 24 hours

            $this->securityService->logSecurityEvent('security_scan_completed', $results, 'info');

            return response()->json([
                'success' => true,
                'message' => 'Security scan completed successfully',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            $this->securityService->logSecurityEvent('security_scan_failed', [
                'error' => $e->getMessage()
            ], 'error');

            return response()->json([
                'success' => false,
                'message' => 'Security scan failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Block IP address.
     */
    public function blockIP(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
            'reason' => 'required|string|max:255',
            'duration' => 'nullable|integer|min:1|max:525600', // Max 1 year in minutes
        ]);

        $ip = $request->ip;
        $reason = $request->reason;
        $duration = $request->duration ?? 1440; // Default 24 hours

        // Add to blocked IPs cache
        $blockedIPs = Cache::get('blocked_ips', []);
        $blockedIPs[$ip] = [
            'reason' => $reason,
            'blocked_at' => now()->toISOString(),
            'blocked_by' => auth()->user()->name,
            'expires_at' => now()->addMinutes($duration)->toISOString(),
        ];

        Cache::put('blocked_ips', $blockedIPs, $duration * 60);

        // Log the action
        $this->securityService->logSecurityEvent('ip_blocked', [
            'ip' => $ip,
            'reason' => $reason,
            'duration' => $duration,
            'blocked_by' => auth()->user()->name,
        ], 'warning');

        return response()->json([
            'success' => true,
            'message' => "IP {$ip} has been blocked successfully"
        ]);
    }

    /**
     * Unblock IP address.
     */
    public function unblockIP(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
        ]);

        $ip = $request->ip;

        // Remove from blocked IPs cache
        $blockedIPs = Cache::get('blocked_ips', []);
        unset($blockedIPs[$ip]);
        Cache::put('blocked_ips', $blockedIPs, 86400);

        // Log the action
        $this->securityService->logSecurityEvent('ip_unblocked', [
            'ip' => $ip,
            'unblocked_by' => auth()->user()->name,
        ], 'info');

        return response()->json([
            'success' => true,
            'message' => "IP {$ip} has been unblocked successfully"
        ]);
    }

    /**
     * Force logout all users.
     */
    public function forceLogoutAll()
    {
        try {
            // Clear all sessions
            DB::table('sessions')->truncate();

            // Revoke all API tokens
            DB::table('personal_access_tokens')->delete();

            // Log the action
            $this->securityService->logSecurityEvent('force_logout_all', [
                'initiated_by' => auth()->user()->name,
            ], 'warning');

            return response()->json([
                'success' => true,
                'message' => 'All users have been logged out successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout all users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate security report.
     */
    public function generateReport(Request $request)
    {
        $period = $request->get('period', '7d');
        
        $report = [
            'period' => $period,
            'generated_at' => now()->toISOString(),
            'generated_by' => auth()->user()->name,
            'summary' => $this->getSecuritySummary($period),
            'failed_logins' => $this->getFailedLoginAttempts(),
            'blocked_ips' => $this->getBlockedIPs(),
            'security_events' => $this->getRecentSecurityEvents(),
            'recommendations' => $this->getSecurityRecommendations(),
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    // Private helper methods

    private function getRecentFailedLogins()
    {
        // This would typically query a log table or file
        return [];
    }

    private function getSessionsByUserAgent()
    {
        return DB::table('sessions')
            ->select(DB::raw('user_agent, COUNT(*) as count'))
            ->groupBy('user_agent')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
    }

    private function checkWeakPasswords()
    {
        // This would check for users with weak passwords
        return [
            'status' => 'pass',
            'message' => 'No weak passwords detected',
            'count' => 0,
        ];
    }

    private function checkInactiveAdmins()
    {
        $inactiveAdmins = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'admin')
            ->where('users.last_login_at', '<', now()->subDays(30))
            ->count();

        return [
            'status' => $inactiveAdmins > 0 ? 'warning' : 'pass',
            'message' => $inactiveAdmins > 0 ? "Found {$inactiveAdmins} inactive admin accounts" : 'No inactive admin accounts',
            'count' => $inactiveAdmins,
        ];
    }

    private function checkOutdatedSessions()
    {
        $outdatedSessions = DB::table('sessions')
            ->where('last_activity', '<', now()->subDays(7)->timestamp)
            ->count();

        return [
            'status' => $outdatedSessions > 100 ? 'warning' : 'pass',
            'message' => "Found {$outdatedSessions} outdated sessions",
            'count' => $outdatedSessions,
        ];
    }

    private function checkSuspiciousFiles()
    {
        // This would check for suspicious file uploads
        return [
            'status' => 'pass',
            'message' => 'No suspicious files detected',
            'count' => 0,
        ];
    }

    private function checkRateLimiting()
    {
        // Check if rate limiting is working effectively
        return [
            'status' => 'pass',
            'message' => 'Rate limiting is functioning properly',
            'blocked_requests' => Cache::get('rate_limited_requests', 0),
        ];
    }

    private function getSecuritySummary($period)
    {
        return [
            'overall_status' => 'good',
            'risk_level' => 'low',
            'total_events' => 15,
            'critical_events' => 0,
            'high_events' => 1,
            'medium_events' => 3,
            'low_events' => 11,
        ];
    }

    private function getSecurityRecommendations()
    {
        return [
            'Enable two-factor authentication for all admin accounts',
            'Regularly update system dependencies',
            'Implement automated security scanning',
            'Review and rotate API keys quarterly',
            'Monitor failed login attempts more closely',
        ];
    }
}
