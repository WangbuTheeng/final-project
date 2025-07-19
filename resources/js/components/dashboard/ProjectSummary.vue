<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Project Overview</h3>
      <button @click="refreshData" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
        <i class="fas fa-sync-alt mr-1" :class="{ 'animate-spin': isRefreshing }"></i>
        Refresh
      </button>
    </div>

    <!-- Academic Year Progress -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Academic Year Progress</span>
        <span class="text-sm text-gray-500">{{ projectSummary.academicYear.progress }}%</span>
      </div>
      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <div 
          class="bg-primary-600 h-2 rounded-full transition-all duration-300"
          :style="{ width: projectSummary.academicYear.progress + '%' }"
        ></div>
      </div>
      <div class="flex justify-between text-xs text-gray-500 mt-1">
        <span>{{ projectSummary.academicYear.startDate }}</span>
        <span>{{ projectSummary.academicYear.endDate }}</span>
      </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <!-- Enrollment Utilization -->
      <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
          {{ projectSummary.enrollment.utilizationRate }}%
        </div>
        <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">Capacity Used</div>
        <div class="text-xs text-gray-500 mt-1">
          {{ projectSummary.enrollment.currentEnrollments }}/{{ projectSummary.enrollment.totalCapacity }}
        </div>
      </div>

      <!-- Collection Rate -->
      <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
          {{ projectSummary.financial.collectionRate }}%
        </div>
        <div class="text-xs text-green-600 dark:text-green-400 font-medium">Collection Rate</div>
        <div class="text-xs text-gray-500 mt-1">This Month</div>
      </div>

      <!-- Course Completion -->
      <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
          {{ projectSummary.academic.completionRate }}%
        </div>
        <div class="text-xs text-purple-600 dark:text-purple-400 font-medium">Completion Rate</div>
        <div class="text-xs text-gray-500 mt-1">Current Term</div>
      </div>

      <!-- Average Grade -->
      <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
          {{ projectSummary.academic.averageGrade }}
        </div>
        <div class="text-xs text-orange-600 dark:text-orange-400 font-medium">Average Grade</div>
        <div class="text-xs text-gray-500 mt-1">All Courses</div>
      </div>
    </div>

    <!-- Financial Summary -->
    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
      <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Financial Summary</h4>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</span>
          <span class="font-semibold text-gray-900 dark:text-white">
            ₦{{ formatCurrency(projectSummary.financial.totalRevenue) }}
          </span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-600 dark:text-gray-400">Monthly Revenue</span>
          <span class="font-semibold text-gray-900 dark:text-white">
            ₦{{ formatCurrency(projectSummary.financial.monthlyRevenue) }}
          </span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-600 dark:text-gray-400">Outstanding</span>
          <span class="font-semibold text-red-600 dark:text-red-400">
            ₦{{ formatCurrency(projectSummary.financial.outstandingAmount) }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  projectSummary: {
    type: Object,
    required: true
  }
})

const isRefreshing = ref(false)

const refreshData = async () => {
  isRefreshing.value = true
  try {
    // Emit refresh event to parent
    emit('refresh')
    await new Promise(resolve => setTimeout(resolve, 1000))
  } finally {
    isRefreshing.value = false
  }
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-NG').format(amount)
}

const emit = defineEmits(['refresh'])
</script>