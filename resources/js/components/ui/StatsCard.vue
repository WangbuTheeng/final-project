<template>
  <div :class="cardClasses" class="stats-card group cursor-pointer">
    <div class="p-6">
      <div class="flex items-center justify-between">
        <div class="flex-1">
          <p class="text-sm font-medium opacity-80 mb-1">{{ title }}</p>
          <p class="text-3xl font-bold mb-2">{{ formattedValue }}</p>
          <div v-if="trend" class="flex items-center">
            <component 
              :is="trendIcon" 
              :class="trendIconClasses"
              class="w-4 h-4 mr-1"
            />
            <span :class="trendTextClasses" class="text-sm font-medium">
              {{ trendText }}
            </span>
          </div>
        </div>
        <div class="bg-white bg-opacity-20 rounded-lg p-3 group-hover:scale-110 transition-transform duration-200">
          <component :is="iconComponent" class="w-8 h-8 text-white" />
        </div>
      </div>
    </div>
    
    <!-- Quick Actions -->
    <div v-if="actions && actions.length > 0" class="px-6 pb-4">
      <div class="flex space-x-2">
        <button
          v-for="action in actions"
          :key="action.id"
          @click="$emit('action', action.id)"
          class="px-3 py-1 text-xs font-medium bg-white bg-opacity-20 hover:bg-opacity-30 rounded-md transition-colors"
        >
          {{ action.label }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { 
  ArrowTrendingUpIcon as TrendingUpIcon, 
  ArrowTrendingDownIcon as TrendingDownIcon, 
  MinusIcon,
  UserGroupIcon,
  AcademicCapIcon,
  CurrencyDollarIcon,
  ChartBarIcon,
  BookOpenIcon,
  ClipboardDocumentListIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  title: String,
  value: [String, Number],
  icon: {
    type: String,
    default: 'chart'
  },
  color: {
    type: String,
    default: 'blue'
  },
  trend: {
    type: Object,
    default: null
  },
  actions: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['action'])

const iconMap = {
  users: UserGroupIcon,
  academic: AcademicCapIcon,
  money: CurrencyDollarIcon,
  chart: ChartBarIcon,
  book: BookOpenIcon,
  clipboard: ClipboardDocumentListIcon
}

const iconComponent = computed(() => {
  return iconMap[props.icon] || ChartBarIcon
})

const cardClasses = computed(() => {
  const baseClasses = 'rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1'
  const colorClasses = {
    blue: 'bg-gradient-to-r from-blue-500 to-blue-600 text-white',
    green: 'bg-gradient-to-r from-green-500 to-green-600 text-white',
    purple: 'bg-gradient-to-r from-purple-500 to-purple-600 text-white',
    orange: 'bg-gradient-to-r from-orange-500 to-orange-600 text-white',
    red: 'bg-gradient-to-r from-red-500 to-red-600 text-white',
    indigo: 'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white',
    pink: 'bg-gradient-to-r from-pink-500 to-pink-600 text-white',
    teal: 'bg-gradient-to-r from-teal-500 to-teal-600 text-white'
  }
  
  return `${baseClasses} ${colorClasses[props.color] || colorClasses.blue}`
})

const formattedValue = computed(() => {
  if (typeof props.value === 'number') {
    return props.value.toLocaleString()
  }
  return props.value
})

const trendIcon = computed(() => {
  if (!props.trend) return null
  
  if (props.trend.direction === 'up') return TrendingUpIcon
  if (props.trend.direction === 'down') return TrendingDownIcon
  return MinusIcon
})

const trendIconClasses = computed(() => {
  if (!props.trend) return ''
  
  return {
    'text-green-200': props.trend.direction === 'up',
    'text-red-200': props.trend.direction === 'down',
    'text-gray-200': props.trend.direction === 'neutral'
  }
})

const trendTextClasses = computed(() => {
  if (!props.trend) return ''
  
  return {
    'text-green-200': props.trend.direction === 'up',
    'text-red-200': props.trend.direction === 'down',
    'text-gray-200': props.trend.direction === 'neutral'
  }
})

const trendText = computed(() => {
  if (!props.trend) return ''
  
  const percentage = Math.abs(props.trend.percentage)
  const direction = props.trend.direction === 'up' ? 'increase' : 
                   props.trend.direction === 'down' ? 'decrease' : 'no change'
  
  return `${percentage}% ${direction} from last month`
})
</script>

<style scoped>
.stats-card {
  background-size: 200% 200%;
  animation: gradient-shift 6s ease infinite;
}

@keyframes gradient-shift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>