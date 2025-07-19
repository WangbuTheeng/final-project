<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
      <h3 class="text-xl font-bold text-white flex items-center">
        <ChartBarIcon class="w-6 h-6 mr-2" />
        {{ title }}
      </h3>
      <p class="text-blue-100 text-sm mt-1">{{ subtitle }}</p>
    </div>

    <!-- Content -->
    <div class="p-6">
      <!-- Metrics Grid -->
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div 
          v-for="metric in metrics" 
          :key="metric.label"
          class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
        >
          <div class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ metric.value }}
          </div>
          <div class="text-sm text-gray-600 dark:text-gray-400">
            {{ metric.label }}
          </div>
          <div v-if="metric.change" class="text-xs mt-1" :class="getChangeColor(metric.change)">
            {{ metric.change }}
          </div>
        </div>
      </div>

      <!-- Progress Bar (if provided) -->
      <div v-if="progress !== null" class="mb-4">
        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
          <span>Progress</span>
          <span>{{ progress }}%</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div 
            class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-500"
            :style="{ width: progress + '%' }"
          ></div>
        </div>
      </div>

      <!-- Status Indicators -->
      <div v-if="statusItems && statusItems.length" class="space-y-2">
        <div 
          v-for="item in statusItems" 
          :key="item.label"
          class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
        >
          <span class="text-sm text-gray-600 dark:text-gray-400">{{ item.label }}</span>
          <span class="text-sm font-medium" :class="getStatusColor(item.status)">
            {{ item.value }}
          </span>
        </div>
      </div>

      <!-- Action Button -->
      <div v-if="actionLabel" class="mt-6">
        <button 
          @click="$emit('action')"
          class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-2 px-4 rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-200 font-medium"
        >
          {{ actionLabel }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ChartBarIcon } from '@heroicons/vue/24/outline'

defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  metrics: {
    type: Array,
    default: () => []
  },
  progress: {
    type: Number,
    default: null
  },
  statusItems: {
    type: Array,
    default: () => []
  },
  actionLabel: {
    type: String,
    default: ''
  }
})

defineEmits(['action'])

const getChangeColor = (change) => {
  if (change.startsWith('+')) {
    return 'text-green-600 dark:text-green-400'
  } else if (change.startsWith('-')) {
    return 'text-red-600 dark:text-red-400'
  }
  return 'text-gray-600 dark:text-gray-400'
}

const getStatusColor = (status) => {
  const statusColors = {
    'completed': 'text-green-600 dark:text-green-400',
    'active': 'text-blue-600 dark:text-blue-400',
    'optimized': 'text-green-600 dark:text-green-400',
    'healthy': 'text-green-600 dark:text-green-400',
    'warning': 'text-yellow-600 dark:text-yellow-400',
    'error': 'text-red-600 dark:text-red-400',
    'pending': 'text-orange-600 dark:text-orange-400'
  }
  return statusColors[status?.toLowerCase()] || 'text-gray-600 dark:text-gray-400'
}
</script>
