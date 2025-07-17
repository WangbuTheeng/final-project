@extends('layouts.dashboard')

@section('title', 'Performance Dashboard')

@section('content')
<div class="space-y-6" x-data="performanceDashboard()">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Performance Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">Monitor and optimize system performance</p>
        </div>
        
        <div class="mt-4 lg:mt-0 flex items-center space-x-3">
            <!-- Refresh Button -->
            <button @click="refreshMetrics()" 
                    :disabled="loading"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-sync-alt mr-2" :class="{ 'animate-spin': loading }"></i>
                Refresh
            </button>
            
            <!-- Optimize Button -->
            <button @click="showOptimizeModal = true"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors duration-200">
                <i class="fas fa-rocket mr-2"></i>
                Optimize
            </button>
        </div>
    </div>

    <!-- Real-time Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Memory Usage -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Memory Usage</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="metrics.system?.memory_usage?.current || 'N/A'"></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-memory text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Peak: </span>
                    <span class="ml-1 font-medium" x-text="metrics.system?.memory_usage?.peak || 'N/A'"></span>
                </div>
            </div>
        </div>

        <!-- Cache Hit Ratio -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Cache Hit Ratio</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        <span x-text="metrics.cache?.hit_ratio || 0"></span>%
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tachometer-alt text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                         :style="`width: ${metrics.cache?.hit_ratio || 0}%`"></div>
                </div>
            </div>
        </div>

        <!-- Database Connections -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">DB Connections</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="metrics.database?.connection_stats?.current_connections || 0"></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-database text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Max: </span>
                    <span class="ml-1 font-medium" x-text="metrics.database?.connection_stats?.max_connections || 'N/A'"></span>
                </div>
            </div>
        </div>

        <!-- Disk Usage -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Disk Usage</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">
                        <span x-text="metrics.system?.disk_usage?.percentage || 0"></span>%
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-hdd text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Free: </span>
                    <span class="ml-1 font-medium" x-text="metrics.system?.disk_usage?.free || 'N/A'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Cache Performance -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cache Performance</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Driver</span>
                    <span class="font-medium" x-text="metrics.cache?.driver || 'N/A'"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Memory Usage</span>
                    <span class="font-medium" x-text="metrics.cache?.memory_usage || 'N/A'"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-300">Key Count</span>
                    <span class="font-medium" x-text="metrics.cache?.key_count || 0"></span>
                </div>
                
                <!-- Cache Actions -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex space-x-2">
                        <button @click="clearCache()" 
                                class="flex-1 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg">
                            Clear Cache
                        </button>
                        <button @click="warmCache()" 
                                class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">
                            Warm Cache
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Performance -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Database Performance</h3>
            <div class="space-y-4">
                <!-- Table Sizes -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Largest Tables</h4>
                    <div class="space-y-2">
                        <template x-for="table in (metrics.database?.table_sizes || []).slice(0, 5)" :key="table.table_name">
                            <div class="flex items-center justify-between text-sm">
                                <span x-text="table.table_name"></span>
                                <span class="font-medium" x-text="table.size_mb + ' MB'"></span>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Database Actions -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button @click="optimizeDatabase()" 
                            class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg">
                        Optimize Database
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Optimization Suggestions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Optimization Suggestions</h3>
        <div x-show="recommendations.length === 0" class="text-gray-500 dark:text-gray-400">
            No recommendations at this time. System performance is optimal.
        </div>
        <div x-show="recommendations.length > 0" class="space-y-3">
            <template x-for="recommendation in recommendations" :key="recommendation">
                <div class="flex items-start p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <i class="fas fa-lightbulb text-yellow-500 mt-0.5 mr-3"></i>
                    <span class="text-sm text-yellow-800 dark:text-yellow-200" x-text="recommendation"></span>
                </div>
            </template>
        </div>
    </div>

    <!-- Optimization Modal -->
    <div x-show="showOptimizeModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         @click.away="showOptimizeModal = false"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                        System Optimization
                    </h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" x-model="optimizations" value="cache" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Warm up cache</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="optimizations" value="database" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Optimize database tables</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="optimizations" value="cleanup" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Clean up old data</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" x-model="optimizations" value="storage" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Optimize storage</span>
                        </label>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="runOptimization()" 
                            :disabled="optimizations.length === 0 || optimizing"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:bg-gray-400">
                        <span x-show="!optimizing">Run Optimization</span>
                        <span x-show="optimizing">Optimizing...</span>
                    </button>
                    <button @click="showOptimizeModal = false" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function performanceDashboard() {
    return {
        metrics: @json($metrics),
        recommendations: [],
        loading: false,
        showOptimizeModal: false,
        optimizations: [],
        optimizing: false,

        async refreshMetrics() {
            this.loading = true;
            try {
                const response = await fetch('/performance/realtime-metrics');
                const data = await response.json();
                
                // Update metrics
                Object.assign(this.metrics, data);
                
                window.showSuccessToast('Metrics refreshed');
            } catch (error) {
                window.showErrorToast('Failed to refresh metrics');
            } finally {
                this.loading = false;
            }
        },

        async clearCache() {
            try {
                const response = await fetch('/performance/clear-cache', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    window.showSuccessToast('Cache cleared successfully');
                    this.refreshMetrics();
                } else {
                    window.showErrorToast(result.error);
                }
            } catch (error) {
                window.showErrorToast('Failed to clear cache');
            }
        },

        async warmCache() {
            try {
                const response = await fetch('/performance/optimize', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ optimizations: ['cache'] })
                });

                const result = await response.json();
                
                if (result.success) {
                    window.showSuccessToast('Cache warmed up successfully');
                    this.refreshMetrics();
                } else {
                    window.showErrorToast(result.error);
                }
            } catch (error) {
                window.showErrorToast('Failed to warm cache');
            }
        },

        async optimizeDatabase() {
            try {
                const response = await fetch('/performance/optimize', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ optimizations: ['database'] })
                });

                const result = await response.json();
                
                if (result.success) {
                    window.showSuccessToast('Database optimized successfully');
                    this.refreshMetrics();
                } else {
                    window.showErrorToast(result.error);
                }
            } catch (error) {
                window.showErrorToast('Failed to optimize database');
            }
        },

        async runOptimization() {
            this.optimizing = true;
            try {
                const response = await fetch('/performance/optimize', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ optimizations: this.optimizations })
                });

                const result = await response.json();
                
                if (result.success) {
                    window.showSuccessToast('Optimization completed successfully');
                    this.showOptimizeModal = false;
                    this.optimizations = [];
                    this.refreshMetrics();
                } else {
                    window.showErrorToast(result.error);
                }
            } catch (error) {
                window.showErrorToast('Optimization failed');
            } finally {
                this.optimizing = false;
            }
        }
    }
}
</script>
@endsection
