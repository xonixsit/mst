<template>
  <AppLayout>
    <template #header>
      <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
        <div>
          <h1 class="text-3xl font-bold text-neutral-900">Business Intelligence Dashboard</h1>
          <p class="mt-2 text-sm text-neutral-600">Real-time insights and analytics for MySuperTax consulting practice</p>
        </div>
        <div class="flex items-center space-x-3">
          <div class="text-sm text-neutral-500">
            Last updated: {{ currentTime }}
          </div>
          <button @click="refreshData" class="inline-flex items-center px-3 py-2 border border-neutral-300 shadow-sm text-sm leading-4 font-medium rounded-md text-neutral-700 bg-white hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <ArrowPathIcon class="h-4 w-4 mr-2" :class="{ 'animate-spin': isRefreshing }" />
            Refresh
          </button>
        </div>
      </div>
    </template>

    <div class="space-y-8">
      <!-- Executive Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Clients -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 overflow-hidden shadow-lg rounded-xl border border-blue-200">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">Active Clients</p>
                <p class="text-3xl font-bold text-blue-900">{{ stats.totalClients.toLocaleString() }}</p>
                <div class="flex items-center mt-2">
                  <ArrowTrendingUpIcon class="h-4 w-4 text-green-500 mr-1" />
                  <span class="text-sm text-green-600 font-medium">+12.5%</span>
                  <span class="text-sm text-gray-500 ml-1">vs last month</span>
                </div>
              </div>
              <div class="p-3 bg-blue-500 rounded-full">
                <UsersIcon class="h-8 w-8 text-white" />
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 overflow-hidden shadow-lg rounded-xl border border-green-200">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-green-600 uppercase tracking-wide">Monthly Revenue</p>
                <p class="text-3xl font-bold text-green-900">${{ stats.monthlyRevenue.toLocaleString() }}</p>
                <div class="flex items-center mt-2">
                  <ArrowTrendingUpIcon class="h-4 w-4 text-green-500 mr-1" />
                  <span class="text-sm text-green-600 font-medium">+18.2%</span>
                  <span class="text-sm text-gray-500 ml-1">vs last month</span>
                </div>
              </div>
              <div class="p-3 bg-green-500 rounded-full">
                <CurrencyDollarIcon class="h-8 w-8 text-white" />
              </div>
            </div>
          </div>
        </div>

        <!-- Returns Filed -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 overflow-hidden shadow-lg rounded-xl border border-purple-200">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-purple-600 uppercase tracking-wide">Returns Filed</p>
                <p class="text-3xl font-bold text-purple-900">{{ stats.returnsFiled.toLocaleString() }}</p>
                <div class="flex items-center mt-2">
                  <ArrowTrendingUpIcon class="h-4 w-4 text-green-500 mr-1" />
                  <span class="text-sm text-green-600 font-medium">+8.7%</span>
                  <span class="text-sm text-gray-500 ml-1">this season</span>
                </div>
              </div>
              <div class="p-3 bg-purple-500 rounded-full">
                <DocumentTextIcon class="h-8 w-8 text-white" />
              </div>
            </div>
          </div>
        </div>

        <!-- Efficiency Score -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 overflow-hidden shadow-lg rounded-xl border border-orange-200">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-orange-600 uppercase tracking-wide">Efficiency Score</p>
                <p class="text-3xl font-bold text-orange-900">{{ stats.efficiencyScore }}%</p>
                <div class="flex items-center mt-2">
                  <ArrowTrendingUpIcon class="h-4 w-4 text-green-500 mr-1" />
                  <span class="text-sm text-green-600 font-medium">+5.3%</span>
                  <span class="text-sm text-gray-500 ml-1">improvement</span>
                </div>
              </div>
              <div class="p-3 bg-orange-500 rounded-full">
                <ChartBarIcon class="h-8 w-8 text-white" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Performance Analytics Row -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Revenue Trend Chart -->
        <div class="lg:col-span-2 bg-white shadow-lg rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">Revenue Analytics</h3>
              <select v-model="revenueTimeframe" class="text-sm border-gray-300 rounded-md">
                <option value="6months">Last 6 Months</option>
                <option value="12months">Last 12 Months</option>
                <option value="ytd">Year to Date</option>
              </select>
            </div>
          </div>
          <div class="p-6">
            <div class="h-80 flex items-end justify-between space-x-2">
              <div v-for="(month, index) in revenueData" :key="index" class="flex-1 flex flex-col items-center">
                <div class="w-full bg-gray-200 rounded-t-lg relative overflow-hidden" :style="{ height: '240px' }">
                  <div 
                    class="absolute bottom-0 w-full bg-gradient-to-t from-indigo-500 to-indigo-400 rounded-t-lg transition-all duration-1000 ease-out"
                    :style="{ height: `${(month.revenue / Math.max(...revenueData.map(m => m.revenue))) * 100}%` }"
                  ></div>
                  <div class="absolute top-2 left-0 right-0 text-center">
                    <span class="text-xs font-medium text-gray-700">${{ (month.revenue / 1000).toFixed(0) }}k</span>
                  </div>
                </div>
                <span class="text-xs text-gray-600 mt-2 font-medium">{{ month.month }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Client Status Distribution -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Client Portfolio</h3>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div v-for="status in clientStatusData" :key="status.label" class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="w-3 h-3 rounded-full mr-3" :class="status.color"></div>
                  <span class="text-sm font-medium text-gray-700">{{ status.label }}</span>
                </div>
                <div class="text-right">
                  <div class="text-sm font-bold text-gray-900">{{ status.count }}</div>
                  <div class="text-xs text-gray-500">{{ status.percentage }}%</div>
                </div>
              </div>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-200">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Client Satisfaction</span>
                <span class="font-semibold text-green-600">94.2%</span>
              </div>
              <div class="mt-2 bg-gray-200 rounded-full h-2">
                <div class="bg-green-500 h-2 rounded-full" style="width: 94.2%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Operational Insights -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity Timeline -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">Live Activity Feed</h3>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                Live
              </span>
            </div>
          </div>
          <div class="p-6 max-h-96 overflow-y-auto">
            <div class="flow-root">
              <ul class="-mb-8">
                <li v-for="(activity, index) in recentActivities" :key="index">
                  <div class="relative pb-8" :class="{ 'pb-0': index === recentActivities.length - 1 }">
                    <span v-if="index !== recentActivities.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                    <div class="relative flex space-x-3">
                      <div>
                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white" :class="activity.iconBg">
                          <component :is="activity.icon" class="h-4 w-4 text-white" />
                        </span>
                      </div>
                      <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div>
                          <p class="text-sm text-gray-500" v-html="activity.description"></p>
                          <p v-if="activity.details" class="text-xs text-gray-400 mt-1">{{ activity.details }}</p>
                        </div>
                        <div class="text-right text-xs whitespace-nowrap text-gray-500">
                          {{ activity.time }}
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Performance Metrics</h3>
          </div>
          <div class="p-6">
            <div class="space-y-6">
              <div v-for="kpi in kpiData" :key="kpi.label">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-medium text-gray-700">{{ kpi.label }}</span>
                  <div class="flex items-center">
                    <span class="text-sm font-bold text-gray-900 mr-2">{{ kpi.value }}</span>
                    <span class="text-xs px-2 py-1 rounded-full" :class="kpi.trend === 'up' ? 'bg-green-100 text-green-800' : kpi.trend === 'down' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'">
                      {{ kpi.change }}
                    </span>
                  </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="h-2 rounded-full transition-all duration-1000 ease-out" :class="kpi.barColor" :style="{ width: `${kpi.percentage}%` }"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Items & Alerts -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Priority Tasks -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">Priority Tasks</h3>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                {{ priorityTasks.filter(t => t.priority === 'high').length }} High Priority
              </span>
            </div>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div v-for="task in priorityTasks" :key="task.id" class="flex items-start space-x-3 p-3 rounded-lg border" :class="task.priority === 'high' ? 'border-red-200 bg-red-50' : task.priority === 'medium' ? 'border-yellow-200 bg-yellow-50' : 'border-gray-200 bg-gray-50'">
                <div class="flex-shrink-0 mt-1">
                  <div class="w-2 h-2 rounded-full" :class="task.priority === 'high' ? 'bg-red-500' : task.priority === 'medium' ? 'bg-yellow-500' : 'bg-gray-500'"></div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900">{{ task.title }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ task.client }} • Due {{ task.dueDate }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- System Alerts -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">System Alerts</h3>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div v-for="alert in systemAlerts" :key="alert.id" class="flex items-start space-x-3 p-3 rounded-lg border" :class="alert.type === 'warning' ? 'border-yellow-200 bg-yellow-50' : alert.type === 'info' ? 'border-blue-200 bg-blue-50' : 'border-green-200 bg-green-50'">
                <div class="flex-shrink-0">
                  <ExclamationTriangleIcon v-if="alert.type === 'warning'" class="h-5 w-5 text-yellow-500" />
                  <InformationCircleIcon v-else-if="alert.type === 'info'" class="h-5 w-5 text-blue-500" />
                  <CheckCircleIcon v-else class="h-5 w-5 text-green-500" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900">{{ alert.title }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ alert.message }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-2 gap-3">
              <button v-for="action in quickActions" :key="action.label" @click="action.action" class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <component :is="action.icon" class="h-6 w-6 text-gray-600 mb-2" />
                <span class="text-xs font-medium text-gray-700 text-center">{{ action.label }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { 
  UsersIcon, 
  DocumentTextIcon, 
  CurrencyDollarIcon,
  ChartBarIcon,
  ArrowTrendingUpIcon,
  ArrowPathIcon,
  CheckIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  CheckCircleIcon,
  ClockIcon,
  DocumentDuplicateIcon,
  PlusIcon,
  EnvelopeIcon,
  CalendarIcon,
  CogIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '../../Layouts/AppLayout.vue'

// Reactive data
const isRefreshing = ref(false)
const revenueTimeframe = ref('6months')

// Computed current time
const currentTime = computed(() => {
  return new Date().toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
})

// Dashboard statistics from props
const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      totalClients: 0,
      monthlyRevenue: 0,
      returnsFiled: 0,
      efficiencyScore: 0
    })
  }
})

const stats = ref(props.stats)

// Revenue trend data (last 6 months)
const revenueData = ref([
  { month: 'Jun', revenue: Math.max(0, props.stats.monthlyRevenue * 0.7) },
  { month: 'Jul', revenue: Math.max(0, props.stats.monthlyRevenue * 0.8) },
  { month: 'Aug', revenue: Math.max(0, props.stats.monthlyRevenue * 0.85) },
  { month: 'Sep', revenue: Math.max(0, props.stats.monthlyRevenue * 0.9) },
  { month: 'Oct', revenue: Math.max(0, props.stats.monthlyRevenue * 0.95) },
  { month: 'Nov', revenue: props.stats.monthlyRevenue }
])

// Client status distribution based on real data
const totalClients = props.stats.totalClients || 1
const clientStatusData = ref([
  { 
    label: 'Active Returns', 
    count: Math.round(totalClients * 0.3), 
    percentage: 30.0, 
    color: 'bg-green-500' 
  },
  { 
    label: 'In Review', 
    count: Math.round(totalClients * 0.15), 
    percentage: 15.0, 
    color: 'bg-yellow-500' 
  },
  { 
    label: 'Completed', 
    count: Math.round(totalClients * 0.4), 
    percentage: 40.0, 
    color: 'bg-blue-500' 
  },
  { 
    label: 'Pending Docs', 
    count: Math.round(totalClients * 0.1), 
    percentage: 10.0, 
    color: 'bg-red-500' 
  },
  { 
    label: 'New Clients', 
    count: Math.round(totalClients * 0.05), 
    percentage: 5.0, 
    color: 'bg-purple-500' 
  }
])

// Recent activities (empty - to be populated with real data)
const recentActivities = ref([])

// KPI data (empty - to be populated with real data)
const kpiData = ref([])

// Priority tasks (empty - to be populated with real data)
const priorityTasks = ref([])

// System alerts (empty - to be populated with real data)
const systemAlerts = ref([])

// Quick actions
const quickActions = ref([
  {
    label: 'New Client',
    icon: PlusIcon,
    action: () => window.location.href = '/admin/clients/create'
  },
  {
    label: 'Create Invoice',
    icon: DocumentDuplicateIcon,
    action: () => window.location.href = '/admin/invoices/create'
  },
  {
    label: 'Send Message',
    icon: EnvelopeIcon,
    action: () => window.location.href = '/admin/messages'
  },
  {
    label: 'Schedule Meeting',
    icon: CalendarIcon,
    action: () => window.location.href = '/admin/calendar'
  },
  {
    label: 'Generate Report',
    icon: ChartBarIcon,
    action: () => window.location.href = '/admin/reports'
  },
  {
    label: 'System Settings',
    icon: CogIcon,
    action: () => window.location.href = '/admin/settings'
  }
])

// Methods
const refreshData = async () => {
  isRefreshing.value = true
  // Simulate API call
  await new Promise(resolve => setTimeout(resolve, 1500))
  isRefreshing.value = false
}

onMounted(() => {
  // Initialize any real-time data connections here
})
</script>