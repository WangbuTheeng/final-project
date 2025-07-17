@props([
    'activities' => [],
    'showUser' => true,
    'showFilters' => true,
    'compact' => false,
    'limit' => null,
])

<div class="activity-timeline" 
     x-data="activityTimeline({{ json_encode($activities) }}, {{ $limit ?? 'null' }})"
     x-init="init()">
    
    @if($showFilters)
        <!-- Timeline Filters -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex flex-wrap items-center gap-4">
                <!-- Time Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time Range</label>
                    <select x-model="timeRange" @change="loadTimeline()" 
                            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="1">Last 24 hours</option>
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                    </select>
                </div>

                <!-- Event Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event Type</label>
                    <select x-model="eventType" @change="filterActivities()" 
                            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">All Events</option>
                        <option value="user">User</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="course">Course</option>
                        <option value="enrollment">Enrollment</option>
                        <option value="exam">Exam</option>
                        <option value="system">System</option>
                    </select>
                </div>

                <!-- Action Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Action</label>
                    <select x-model="actionFilter" @change="filterActivities()" 
                            class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">All Actions</option>
                        <option value="create">Create</option>
                        <option value="update">Update</option>
                        <option value="delete">Delete</option>
                        <option value="view">View</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                    </select>
                </div>

                <!-- Refresh Button -->
                <div class="flex items-end">
                    <button @click="loadTimeline()" 
                            :disabled="loading"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-sync-alt mr-2" :class="{ 'animate-spin': loading }"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading State -->
    <div x-show="loading" class="text-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-2 border-blue-500 border-t-transparent mx-auto"></div>
        <p class="text-gray-500 dark:text-gray-400 mt-4">Loading activities...</p>
    </div>

    <!-- Timeline -->
    <div x-show="!loading" class="space-y-8">
        <!-- Empty State -->
        <div x-show="filteredTimeline.length === 0" class="text-center py-12">
            <i class="fas fa-history text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No activities found</h3>
            <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters or check back later.</p>
        </div>

        <!-- Timeline Days -->
        <template x-for="day in filteredTimeline" :key="day.date">
            <div class="relative">
                <!-- Date Header -->
                <div class="sticky top-20 z-10 bg-white dark:bg-gray-900 py-2 mb-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-calendar-day text-white text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="day.formatted_date"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                <span x-text="day.day_name"></span> • 
                                <span x-text="day.count"></span> 
                                <span x-text="day.count === 1 ? 'activity' : 'activities'"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Activities for this day -->
                <div class="ml-6 border-l-2 border-gray-200 dark:border-gray-700 pl-6 space-y-4">
                    <template x-for="(activity, index) in day.activities" :key="activity.id">
                        <div class="relative group">
                            <!-- Timeline dot -->
                            <div class="absolute -left-8 top-2 w-4 h-4 rounded-full border-2 border-white dark:border-gray-900 shadow-lg transition-all duration-200 group-hover:scale-125"
                                 :class="{
                                     'bg-green-500': activity.color === 'green',
                                     'bg-blue-500': activity.color === 'blue',
                                     'bg-red-500': activity.color === 'red',
                                     'bg-yellow-500': activity.color === 'yellow',
                                     'bg-purple-500': activity.color === 'purple',
                                     'bg-indigo-500': activity.color === 'indigo',
                                     'bg-gray-500': activity.color === 'gray'
                                 }"></div>

                            <!-- Activity Card -->
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-xl transition-all duration-200 group-hover:transform group-hover:scale-[1.02]">
                                <div class="flex items-start space-x-3">
                                    <!-- Activity Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                             :class="{
                                                 'bg-green-100 dark:bg-green-900/30': activity.color === 'green',
                                                 'bg-blue-100 dark:bg-blue-900/30': activity.color === 'blue',
                                                 'bg-red-100 dark:bg-red-900/30': activity.color === 'red',
                                                 'bg-yellow-100 dark:bg-yellow-900/30': activity.color === 'yellow',
                                                 'bg-purple-100 dark:bg-purple-900/30': activity.color === 'purple',
                                                 'bg-indigo-100 dark:bg-indigo-900/30': activity.color === 'indigo',
                                                 'bg-gray-100 dark:bg-gray-700': activity.color === 'gray'
                                             }">
                                            <i :class="activity.icon" 
                                               class="text-sm"
                                               :class="{
                                                   'text-green-600 dark:text-green-400': activity.color === 'green',
                                                   'text-blue-600 dark:text-blue-400': activity.color === 'blue',
                                                   'text-red-600 dark:text-red-400': activity.color === 'red',
                                                   'text-yellow-600 dark:text-yellow-400': activity.color === 'yellow',
                                                   'text-purple-600 dark:text-purple-400': activity.color === 'purple',
                                                   'text-indigo-600 dark:text-indigo-400': activity.color === 'indigo',
                                                   'text-gray-600 dark:text-gray-400': activity.color === 'gray'
                                               }"></i>
                                        </div>
                                    </div>

                                    <!-- Activity Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="activity.description"></p>
                                                
                                                @if($showUser)
                                                    <div x-show="activity.user" class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                        <i class="fas fa-user mr-1"></i>
                                                        <span x-text="activity.user?.name"></span>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Time and Severity -->
                                            <div class="flex items-center space-x-2 ml-4">
                                                <!-- Severity Badge -->
                                                <span x-show="activity.severity !== 'normal'"
                                                      class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                      :class="{
                                                          'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': activity.severity === 'critical',
                                                          'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300': activity.severity === 'high',
                                                          'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': activity.severity === 'low'
                                                      }"
                                                      x-text="activity.severity.toUpperCase()">
                                                </span>

                                                <!-- Time -->
                                                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="activity.time"></span>
                                            </div>
                                        </div>

                                        <!-- Event Type and Action Tags -->
                                        <div class="mt-2 flex items-center space-x-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300"
                                                  x-text="activity.event_type"></span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                                                  x-text="activity.action"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Load More Button -->
        <div x-show="hasMore && !loading" class="text-center py-4">
            <button @click="loadMore()" 
                    class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-chevron-down mr-2"></i>
                Load More Activities
            </button>
        </div>
    </div>
</div>

<script>
function activityTimeline(initialActivities = [], limit = null) {
    return {
        timeline: [],
        filteredTimeline: [],
        loading: false,
        hasMore: false,
        timeRange: '7',
        eventType: '',
        actionFilter: '',
        page: 1,

        init() {
            if (initialActivities.length > 0) {
                this.timeline = this.groupActivitiesByDate(initialActivities);
                this.filteredTimeline = this.timeline;
            } else {
                this.loadTimeline();
            }
        },

        async loadTimeline() {
            this.loading = true;
            this.page = 1;
            
            try {
                const response = await fetch(`/activities/timeline?days=${this.timeRange}&limit=${limit || 100}`);
                const data = await response.json();
                
                this.timeline = data.timeline;
                this.hasMore = data.timeline.length >= (limit || 100);
                this.filterActivities();
            } catch (error) {
                console.error('Failed to load timeline:', error);
                window.showErrorToast('Failed to load activity timeline');
            } finally {
                this.loading = false;
            }
        },

        async loadMore() {
            this.page++;
            this.loading = true;

            try {
                const response = await fetch(`/activities/timeline?days=${this.timeRange}&page=${this.page}&limit=${limit || 100}`);
                const data = await response.json();

                // Merge new timeline data
                if (data.timeline) {
                    data.timeline.forEach(newDay => {
                        const existingDay = this.timeline.find(day => day.date === newDay.date);
                        if (existingDay) {
                            existingDay.activities.push(...newDay.activities);
                            existingDay.count += newDay.count;
                        } else {
                            this.timeline.push(newDay);
                        }
                    });

                    this.hasMore = data.timeline.length >= (limit || 100);
                    this.filterActivities();
                }
            } catch (error) {
                console.error('Failed to load more activities:', error);
                if (window.showErrorToast) {
                    window.showErrorToast('Failed to load more activities');
                }
            } finally {
                this.loading = false;
            }
        },

        filterActivities() {
            this.filteredTimeline = this.timeline.map(day => {
                const filteredActivities = day.activities.filter(activity => {
                    if (this.eventType && activity.event_type !== this.eventType) return false;
                    if (this.actionFilter && activity.action !== this.actionFilter) return false;
                    return true;
                });

                return {
                    ...day,
                    activities: filteredActivities,
                    count: filteredActivities.length
                };
            }).filter(day => day.count > 0);
        },

        groupActivitiesByDate(activities) {
            const grouped = {};
            
            activities.forEach(activity => {
                const date = activity.created_at.split('T')[0]; // Get date part
                if (!grouped[date]) {
                    grouped[date] = {
                        date: date,
                        formatted_date: new Date(date).toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        }),
                        day_name: new Date(date).toLocaleDateString('en-US', { weekday: 'long' }),
                        activities: [],
                        count: 0
                    };
                }
                
                grouped[date].activities.push({
                    ...activity,
                    time: new Date(activity.created_at).toLocaleTimeString('en-US', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    })
                });
                grouped[date].count++;
            });

            return Object.values(grouped).sort((a, b) => new Date(b.date) - new Date(a.date));
        }
    }
}
</script>

<style>
.activity-timeline {
    position: relative;
}

.activity-timeline .group:hover .absolute {
    transform: scale(1.25);
}

/* Custom scrollbar for timeline */
.activity-timeline::-webkit-scrollbar {
    width: 6px;
}

.activity-timeline::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.activity-timeline::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.activity-timeline::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
