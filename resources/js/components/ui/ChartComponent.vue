<template>
  <div class="chart-container">
    <canvas ref="chartCanvas" :id="chartId"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  Filler
} from 'chart.js'

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  Filler
)

const props = defineProps({
  type: {
    type: String,
    default: 'line',
    validator: (value) => ['line', 'bar', 'doughnut', 'pie', 'area'].includes(value)
  },
  data: {
    type: Object,
    required: true
  },
  options: {
    type: Object,
    default: () => ({})
  },
  height: {
    type: Number,
    default: 300
  }
})

const chartCanvas = ref(null)
const chartInstance = ref(null)
const chartId = ref(`chart-${Math.random().toString(36).substr(2, 9)}`)

const defaultOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'top',
      labels: {
        usePointStyle: true,
        padding: 20,
        font: {
          size: 12,
          family: 'Inter, sans-serif'
        }
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#fff',
      bodyColor: '#fff',
      borderColor: 'rgba(255, 255, 255, 0.1)',
      borderWidth: 1,
      cornerRadius: 8,
      displayColors: true,
      titleFont: {
        size: 14,
        weight: 'bold'
      },
      bodyFont: {
        size: 13
      }
    }
  },
  scales: {
    x: {
      grid: {
        display: false
      },
      ticks: {
        font: {
          size: 11,
          family: 'Inter, sans-serif'
        },
        color: '#6B7280'
      }
    },
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(107, 114, 128, 0.1)'
      },
      ticks: {
        font: {
          size: 11,
          family: 'Inter, sans-serif'
        },
        color: '#6B7280'
      }
    }
  }
}

const createChart = () => {
  if (!chartCanvas.value) return

  const ctx = chartCanvas.value.getContext('2d')
  
  // Destroy existing chart
  if (chartInstance.value) {
    chartInstance.value.destroy()
  }

  // Prepare chart data based on type
  let chartData = { ...props.data }
  let chartType = props.type

  // Handle area chart (line chart with fill)
  if (props.type === 'area') {
    chartType = 'line'
    chartData.datasets = chartData.datasets.map(dataset => ({
      ...dataset,
      fill: true,
      backgroundColor: dataset.backgroundColor || 'rgba(99, 102, 241, 0.1)',
      borderColor: dataset.borderColor || 'rgba(99, 102, 241, 1)',
      tension: 0.4
    }))
  }

  // Apply default styling based on chart type
  if (chartType === 'line') {
    chartData.datasets = chartData.datasets.map(dataset => ({
      ...dataset,
      borderWidth: 3,
      pointRadius: 4,
      pointHoverRadius: 6,
      tension: 0.4
    }))
  }

  if (chartType === 'bar') {
    chartData.datasets = chartData.datasets.map(dataset => ({
      ...dataset,
      borderRadius: 4,
      borderSkipped: false
    }))
  }

  // Merge options
  const mergedOptions = {
    ...defaultOptions,
    ...props.options
  }

  // Remove scales for doughnut and pie charts
  if (['doughnut', 'pie'].includes(chartType)) {
    delete mergedOptions.scales
  }

  chartInstance.value = new ChartJS(ctx, {
    type: chartType,
    data: chartData,
    options: mergedOptions
  })
}

const updateChart = () => {
  if (chartInstance.value) {
    chartInstance.value.data = props.data
    chartInstance.value.update('active')
  }
}

onMounted(async () => {
  await nextTick()
  createChart()
})

onUnmounted(() => {
  if (chartInstance.value) {
    chartInstance.value.destroy()
  }
})

watch(() => props.data, updateChart, { deep: true })
watch(() => props.type, createChart)
watch(() => props.options, createChart, { deep: true })
</script>

<style scoped>
.chart-container {
  position: relative;
  height: v-bind(height + 'px');
  width: 100%;
}

canvas {
  max-width: 100%;
  height: auto;
}
</style>