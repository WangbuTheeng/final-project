<template>
  <div class="dashboard-container min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ greeting }}, {{ user.first_name }}!
          </h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">
            Here's what's happening with your {{ roleText }} dashboard today.
          </p>
        </div>
        
        <!-- Theme Toggle -->
        <div class="flex items-center space-x-4">
          <button
            @click="toggleTheme"
            class="p-2 rounded-lg bg-white dark:bg-gray-800 shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200 dark:border-gray-700"
          >
            <SunIcon v-if="isDark" class="w-5 h-5 text-yellow-500" />
            <MoonIcon v-else class="w-5 h-5 text-gray-600 dark:text-gray-400" />
          </button>
          
          <!-- Refresh Button -->
          <button
            @click="refreshDashboard"
            :disabled="isRefreshing"
            class="p-2 rounded-lg bg-white dark:bg-gray-800 shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200 dark:border-gray-700 disabled:opacity-50"
          >
            <ArrowPathIcon 
              :class="{ 'animate-spin': isRefreshing }"
              class="w-5 h-5 text-gray-600 dark:text-gray-400" 
            />
          </button>
        </div>
      </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <StatsCard
        v-for="stat in stats"
        :key="stat.id"
        :title="stat.title"
        :value="stat.value"
        :icon="stat.icon"
        :color="stat.color"
        :trend="stat.trend"
        :actions="stat.actions"
        @action="handleStatAction"
      />
    </div>

    <!-- Charts and Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Chart Widget -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ chartTitle }}
          </h3>
          <select 
            v-model="chartPeriod"
            @change="updateChartData"
            class="text-sm border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
          >
            <option value="7">Last 7 days</option>
            <option value="30">Last 30 days</option>
            <option value="90">Last 3 months</option>
          </select>
        </div>
        <ChartComponent 
          :data="chartData" 
          :type="chartType"
          :options="chartOptions"
          :height="300"
        />
      </div>
      
      <!-- Recent Activities -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Recent Activities
        </h3>
        <div class="space-y-4 max-h-80 overflow-y-auto">
          <div 
            v-for="activity in recentActivities"
            :key="activity.id"
            class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            <div :class="getActivityIconBg(activity.type)" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center">
              <component :is="getActivityIcon(activity.type)" class="w-4 h-4 text-white" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm text-gray-900 dark:text-white">
                {{ activity.description }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ formatTime(activity.created_at) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
        Quick Actions
      </h3>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <button
          v-for="action in quickActions"
          :key="action.id"
          @click="handleQuickAction(action)"
          class="flex flex-col items-center p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group"
        >
          <component 
            :is="getActionIcon(action.icon)" 
            class="w-8 h-8 text-blue-500 mb-2 group-hover:scale-110 transition-transform" 
          />
          <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
            {{ action.label }}
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import StatsCard from '../ui/StatsCard.vue'
import ChartComponent from '../ui/ChartComponent.vue'
import {
  SunIcon,
  MoonIcon,
  ArrowPathIcon,
  UserPlusIcon,
  DocumentPlusIcon,
  CurrencyDollarIcon,
  ChartBarIcon,
  AcademicCapIcon,
  UserGroupIcon,
  ClipboardDocumentListIcon,
  BookOpenIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  dashboardData: {
    type: Object,
    default: () => ({})
  }
})

const { props: pageProps } = usePage()
const user = computed(() => pageProps.auth.user)

const isDark = ref(false)
const isRefreshing = ref(false)
const chartPeriod = ref('30')

const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour < 12) return 'Good morning'
  if (hour < 18) return 'Good afternoon'
  return 'Good evening'
})

const roleText = computed(() => {
  const roleMap = {
    'admin': 'admin',
    'super_admin': 'admin',
    'teacher': 'teacher',
    'student': 'student'
  }
  return roleMap[user.value.role] || 'user'
})

const stats = computed(() => props.dashboardData?.stats || [])
const chartData = computed(() => props.dashboardData?.chartData || getDefaultChartData())
const chartType = computed(() => props.dashboardData?.chartType || 'line')
const chartTitle = computed(() => props.dashboardData?.chartTitle || 'Analytics Overview')
const recentActivities = computed(() => props.dashboardData?.recentActivities || [])
const quickActions = computed(() => props.dashboardData?.quickActions || getDefaultQuickActions())

const chartOptions = computed(() => ({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'top'
    }
  },
  scales: {
    y: {
      beginAtZero: true
    }
  }
}))

const toggleTheme = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

const refreshDashboard = async () => {
  isRefreshing.value = true
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    // In real implementation, you would reload the dashboard data
    window.location.reload()
  } finally {
    isRefreshing.value = false
  }
}

const updateChartData = () => {
  // In real implementation, you would fetch new data based on the period
  console.log('Updating chart data for period:', chartPeriod.value)
}

const handleStatAction = (actionId) => {
  console.log('Stat action:', actionId)
  // Handle stat card actions
}

const handleQuickAction = (action) => {
  if (action.url) {
    window.location.href = action.url
  } else if (action.route) {
    // Handle Inertia route navigation
    console.log('Navigate to:', action.route)
  }
}

const getActivityIcon = (type) => {
  const iconMap = {
    enrollment: UserPlusIcon,
    grade: AcademicCapIcon,
    payment: CurrencyDollarIcon,
    course: BookOpenIcon,
    default: ClipboardDocumentListIcon
  }
  return iconMap[type] || iconMap.default
}

const getActivityIconBg = (type) => {
  const bgMap = {
    enrollment: 'bg-blue-500',
    grade: 'bg-green-500',
    payment: 'bg-yellow-500',
    course: 'bg-purple-500',
    default: 'bg-gray-500'
  }
  return bgMap[type] || bgMap.default
}

const getActionIcon = (iconName) => {
  const iconMap = {
    'user-plus': UserPlusIcon,
    'document-plus': DocumentPlusIcon,
    'currency-dollar': CurrencyDollarIcon,
    'chart-bar': ChartBarIcon,
    'academic-cap': AcademicCapIcon,
    'user-group': UserGroupIcon,
    'clipboard': ClipboardDocumentListIcon,
    'book': BookOpenIcon
  }
  return iconMap[iconName] || ClipboardDocumentListIcon
}

const getDefaultChartData = () => ({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
  datasets: [{
    label: 'Enrollments',
    data: [12, 19, 3, 5, 2, 3],
    borderColor: 'rgb(99, 102, 241)',
    backgroundColor: 'rgba(99, 102, 241, 0.1)',
    tension: 0.4
  }]
})

const getDefaultQuickActions = () => [
  { id: 1, label: 'Add Student', icon: 'user-plus', url: '/students/create' },
  { id: 2, label: 'New Course', icon: 'book', url: '/courses/create' },
  { id: 3, label: 'View Reports', icon: 'chart-bar', url: '/reports' },
  { id: 4, label: 'Manage Users', icon: 'user-group', url: '/users' }
]

const formatTime = (timestamp) => {
  return new Date(timestamp).toLocaleString()
}

onMounted(() => {
  // Initialize theme
  const savedTheme = localStorage.getItem('theme')
  isDark.value = savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)
  document.documentElement.classList.toggle('dark', isDark.value)
})
</script>

<style scoped>
.dashboard-container {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>