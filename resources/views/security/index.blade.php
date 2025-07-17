@extends('layouts.dashboard')

@section('title', 'Security Dashboard')

@section('content')
<div class="space-y-6" x-data="securityDashboard()">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Security Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Monitor and manage system security</p>
        </div>
        
        <div class="mt-4 lg:mt-0 flex items-center space-x-3">
            <!-- Security Scan Button -->
            <button @click="runSecurityScan()" 
                    :disabled="scanning"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-shield-alt mr-2" :class="{ 'animate-spin': scanning }"></i>
                <span x-text="scanning ? 'Scanning...' : 'Run Security Scan'"></span>
            </button>
            
            <!-- Generate Report Button -->
            <button @click="generateReport()"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200">
                <i class="fas fa-file-alt mr-2"></i>
                Generate Report
            </button>
        </div>
    </div>

    <!-- Security Status Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Overall Security Status -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Security Status</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Good</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-shield-alt text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-green-500">●</span>
                    <span class="ml-2 text-gray-600 dark:text-gray-300">All systems secure</span>
                </div>
            </div>
        </div>

        <!-- Failed Login Attempts -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Failed Logins Today</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="metrics.failed_logins?.today || 0"></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-gray-500 dark:text-gray-400">This week: </span>
                    <span class="ml-1 font-medium" x-text="metrics.failed_logins?.this_week || 0"></span>
                </div>
            </div>
        </div>

        <!-- Blocked IPs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Blocked IPs</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="metrics.blocked_ips?.total || 0"></p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-ban text-red-600 dark:text-red-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Active: </span>
                    <span class="ml-1 font-medium" x-text="(metrics.blocked_ips?.active || []).length"></span>
                </div>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Active Sessions</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="metrics.user_sessions?.active || 0"></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Total: </span>
                    <span class="ml-1 font-medium" x-text="metrics.user_sessions?.total || 0"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Events and Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Security Events -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Security Events</h3>
            <div class="space-y-4">
                <template x-for="event in (metrics.security_events || [])" :key="event.timestamp">
                    <div class="flex items-start p-3 rounded-lg border" 
                         :class="{
                             'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20': event.severity === 'high',
                             'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20': event.severity === 'medium',
                             'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20': event.severity === 'low'
                         }">
                        <div class="flex-shrink-0 mr-3">
                            <i class="fas fa-exclamation-circle" 
                               :class="{
                                   'text-red-500': event.severity === 'high',
                                   'text-yellow-500': event.severity === 'medium',
                                   'text-blue-500': event.severity === 'low'
                               }"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium" x-text="event.message"></p>
                            <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span x-text="event.type"></span>
                                <span class="mx-2">•</span>
                                <span x-text="formatTime(event.timestamp)"></span>
                                <span x-show="event.ip" class="mx-2">•</span>
                                <span x-show="event.ip" x-text="event.ip"></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="!metrics.security_events || metrics.security_events.length === 0" 
                     class="text-center py-8 text-gray-500 dark:text-gray-400">
                    No recent security events
                </div>
            </div>
        </div>

        <!-- Security Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Security Actions</h3>
            <div class="space-y-4">
                <!-- Block IP -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Block IP Address</h4>
                    <div class="space-y-3">
                        <input type="text" 
                               x-model="blockIpForm.ip"
                               placeholder="Enter IP address"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <input type="text" 
                               x-model="blockIpForm.reason"
                               placeholder="Reason for blocking"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <button @click="blockIP()" 
                                :disabled="!blockIpForm.ip || !blockIpForm.reason"
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white rounded-lg">
                            Block IP
                        </button>
                    </div>
                </div>

                <!-- Force Logout All -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Emergency Actions</h4>
                    <button @click="forceLogoutAll()" 
                            class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg">
                        Force Logout All Users
                    </button>
                </div>

                <!-- Vulnerability Scan -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Vulnerability Status</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Last Scan:</span>
                            <span x-text="metrics.vulnerability_scan?.last_scan || 'Never'"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Vulnerabilities:</span>
                            <span x-text="metrics.vulnerability_scan?.vulnerabilities_found || 0"></span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blocked IPs Management -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Blocked IP Addresses</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Blocked By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expires</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(details, ip) in (metrics.blocked_ips?.active || {})" :key="ip">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="ip"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" x-text="details.reason"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" x-text="details.blocked_by"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300" x-text="formatTime(details.expires_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="unblockIP(ip)" 
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    Unblock
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            
            <div x-show="Object.keys(metrics.blocked_ips?.active || {}).length === 0" 
                 class="text-center py-8 text-gray-500 dark:text-gray-400">
                No blocked IP addresses
            </div>
        </div>
    </div>
</div>

<script>
function securityDashboard() {
    return {
        metrics: @json($securityMetrics),
        scanning: false,
        blockIpForm: {
            ip: '',
            reason: ''
        },

        async runSecurityScan() {
            this.scanning = true;
            try {
                const response = await fetch('/security/scan', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    window.showSuccessToast('Security scan completed successfully');
                    // Refresh metrics
                    this.refreshMetrics();
                } else {
                    window.showErrorToast(result.message);
                }
            } catch (error) {
                window.showErrorToast('Security scan failed');
            } finally {
                this.scanning = false;
            }
        },

        async blockIP() {
            try {
                const response = await fetch('/security/block-ip', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.blockIpForm)
                });

                const result = await response.json();
                
                if (result.success) {
                    window.showSuccessToast(result.message);
                    this.blockIpForm = { ip: '', reason: '' };
                    this.refreshMetrics();
                } else {
                    window.showErrorToast(result.message);
                }
            } catch (error) {
                window.showErrorToast('Failed to block IP');
            }
        },

        async unblockIP(ip) {
            try {
                const response = await fetch('/security/unblock-ip', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ ip })
                });

                const result = await response.json();
                
                if (result.success) {
                    window.showSuccessToast(result.message);
                    this.refreshMetrics();
                } else {
                    window.showErrorToast(result.message);
                }
            } catch (error) {
                window.showErrorToast('Failed to unblock IP');
            }
        },

        async forceLogoutAll() {
            if (!confirm('Are you sure you want to force logout all users? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch('/security/force-logout-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    window.showSuccessToast(result.message);
                } else {
                    window.showErrorToast(result.message);
                }
            } catch (error) {
                window.showErrorToast('Failed to logout all users');
            }
        },

        async generateReport() {
            try {
                const response = await fetch('/security/report');
                const result = await response.json();
                
                if (result.success) {
                    // Download or display report
                    window.showSuccessToast('Security report generated successfully');
                } else {
                    window.showErrorToast('Failed to generate report');
                }
            } catch (error) {
                window.showErrorToast('Failed to generate report');
            }
        },

        async refreshMetrics() {
            // Refresh security metrics
            window.location.reload();
        },

        formatTime(timestamp) {
            return new Date(timestamp).toLocaleString();
        }
    }
}
</script>
@endsection
