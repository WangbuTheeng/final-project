import { createApp } from 'vue'
import DashboardLayout from './components/dashboard/DashboardLayout.vue'
import StatsCard from './components/ui/StatsCard.vue'
import ChartComponent from './components/ui/ChartComponent.vue'

// Create Vue app for dashboard
const app = createApp({
    components: {
        DashboardLayout,
        StatsCard,
        ChartComponent
    },
    data() {
        return {
            dashboardData: window.dashboardData || {}
        }
    },
    async mounted() {
        // Load dashboard data if not provided
        if (!window.dashboardData || Object.keys(window.dashboardData).length === 0) {
            await this.loadDashboardData()
        }
    },
    methods: {
        async loadDashboardData() {
            try {
                const response = await fetch('/api/dashboard/data', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                
                if (response.ok) {
                    this.dashboardData = await response.json()
                } else {
                    console.error('Failed to load dashboard data:', response.statusText)
                }
            } catch (error) {
                console.error('Error loading dashboard data:', error)
            }
        },
        
        async refreshDashboard() {
            await this.loadDashboardData()
        }
    }
})

// Mount the app if the element exists
const dashboardElement = document.getElementById('modern-dashboard')
if (dashboardElement) {
    app.mount('#modern-dashboard')
}

// Export for global access
window.DashboardApp = app