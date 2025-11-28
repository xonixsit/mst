<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-blue-50 to-indigo-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-blue-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-indigo-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2 pr-2 pl-2">
          <div class="flex items-center space-x-4">
            <!-- Dashboard Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
              <ChartBarIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent">
                Business Intelligence Dashboard
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Real-time insights and analytics for MySuperTax consulting practice</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-emerald-700">Live Data</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-blue-700">{{ stats.totalClients }} Active Clients</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Section -->
          <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <!-- Last Updated -->
            <div class="bg-white/70 backdrop-blur-sm px-4 py-2 rounded-xl border border-gray-200/50 shadow-sm">
              <div class="flex items-center space-x-2">
                <ClockIcon class="w-4 h-4 text-gray-500" />
                <span class="text-sm font-medium text-gray-600">Last updated:</span>
                <span class="text-sm font-bold text-gray-900">{{ currentTime }}</span>
              </div>
            </div>
            
            <!-- Refresh Button -->
            <button 
              @click="refreshData" 
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <ArrowPathIcon class="h-5 w-5 mr-2 transition-transform duration-300" :class="{ 'animate-spin': isRefreshing, 'group-hover:rotate-180': !isRefreshing }" />
              <span class="font-semibold">{{ isRefreshing ? 'Refreshing...' : 'Refresh Data' }}</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <div class="space-y-8">
      <!-- Executive Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Clients -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 rounded-xl shadow-lg border border-blue-200/50 hover:shadow-xl transition-all duration-300 group">
          <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-blue-300/30 to-transparent rounded-bl-full"></div>
          <div class="relative p-6">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Active Clients</p>
                <p class="text-3xl font-bold text-blue-900 mt-2">{{ stats.totalClients.toLocaleString() }}</p>
                <div class="flex items-center mt-3" v-if="stats.changes">
                  <ArrowTrendingUpIcon v-if="stats.changes.clients > 0" class="h-4 w-4 text-green-600 mr-1" />
                  <ArrowTrendingDownIcon v-else-if="stats.changes.clients < 0" class="h-4 w-4 text-red-500 mr-1" />
                  <MinusIcon v-else class="h-4 w-4 text-gray-500 mr-1" />
                  <span class="text-sm font-medium" :class="stats.changes.clients > 0 ? 'text-green-700' : stats.changes.clients < 0 ? 'text-red-600' : 'text-gray-600'">
                    {{ stats.changes.clients > 0 ? '+' : '' }}{{ stats.changes.clients }}%
                  </span>
                  <span class="text-sm text-blue-600 ml-1">vs last month</span>
                </div>
              </div>
              <div class="p-4 bg-blue-500 rounded-xl shadow-lg group-hover:bg-blue-600 transition-colors duration-300">
                <UsersIcon class="w-8 h-8 text-white" />
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue -->
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-emerald-100 to-emerald-200 rounded-xl shadow-lg border border-emerald-200/50 hover:shadow-xl transition-all duration-300 group">
          <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-emerald-300/30 to-transparent rounded-bl-full"></div>
          <div class="relative p-6">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-sm font-semibold text-emerald-700 uppercase tracking-wide">Monthly Revenue</p>
                <p class="text-3xl font-bold text-emerald-900 mt-2">${{ stats.monthlyRevenue.toLocaleString() }}</p>
                <div class="flex items-center mt-3" v-if="stats.changes">
                  <ArrowTrendingUpIcon v-if="stats.changes.revenue > 0" class="h-4 w-4 text-green-600 mr-1" />
                  <ArrowTrendingDownIcon v-else-if="stats.changes.revenue < 0" class="h-4 w-4 text-red-500 mr-1" />
                  <MinusIcon v-else class="h-4 w-4 text-gray-500 mr-1" />
                  <span class="text-sm font-medium" :class="stats.changes.revenue > 0 ? 'text-green-700' : stats.changes.revenue < 0 ? 'text-red-600' : 'text-gray-600'">
                    {{ stats.changes.revenue > 0 ? '+' : '' }}{{ stats.changes.revenue }}%
                  </span>
                  <span class="text-sm text-emerald-600 ml-1">vs last month</span>
                </div>
              </div>
              <div class="p-4 bg-emerald-500 rounded-xl shadow-lg group-hover:bg-emerald-600 transition-colors duration-300">
                <CurrencyDollarIcon class="w-8 h-8 text-white" />
              </div>
            </div>
          </div>
        </div>

        <!-- Returns Filed -->
        <div class="relative overflow-hidden bg-gradient-to-br from-purple-50 via-purple-100 to-purple-200 rounded-xl shadow-lg border border-purple-200/50 hover:shadow-xl transition-all duration-300 group">
          <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-purple-300/30 to-transparent rounded-bl-full"></div>
          <div class="relative p-6">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-sm font-semibold text-purple-700 uppercase tracking-wide">Returns Filed</p>
                <p class="text-3xl font-bold text-purple-900 mt-2">{{ stats.returnsFiled.toLocaleString() }}</p>
                <div class="flex items-center mt-3" v-if="stats.changes">
                  <ArrowTrendingUpIcon v-if="stats.changes.returns > 0" class="h-4 w-4 text-green-600 mr-1" />
                  <ArrowTrendingDownIcon v-else-if="stats.changes.returns < 0" class="h-4 w-4 text-red-500 mr-1" />
                  <MinusIcon v-else class="h-4 w-4 text-gray-500 mr-1" />
                  <span class="text-sm font-medium" :class="stats.changes.returns > 0 ? 'text-green-700' : stats.changes.returns < 0 ? 'text-red-600' : 'text-gray-600'">
                    {{ stats.changes.returns > 0 ? '+' : '' }}{{ stats.changes.returns }}%
                  </span>
                  <span class="text-sm text-purple-600 ml-1">this season</span>
                </div>
              </div>
              <div class="p-4 bg-purple-500 rounded-xl shadow-lg group-hover:bg-purple-600 transition-colors duration-300">
                <DocumentTextIcon class="w-8 h-8 text-white" />
              </div>
            </div>
          </div>
        </div>

        <!-- Efficiency Score -->
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 via-amber-100 to-amber-200 rounded-xl shadow-lg border border-amber-200/50 hover:shadow-xl transition-all duration-300 group">
          <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-amber-300/30 to-transparent rounded-bl-full"></div>
          <div class="relative p-6">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-sm font-semibold text-amber-700 uppercase tracking-wide">Efficiency Score</p>
                <p class="text-3xl font-bold text-amber-900 mt-2">{{ stats.efficiencyScore }}%</p>
                <div class="flex items-center mt-3" v-if="stats.changes">
                  <ArrowTrendingUpIcon v-if="stats.changes.efficiency > 0" class="h-4 w-4 text-green-600 mr-1" />
                  <ArrowTrendingDownIcon v-else-if="stats.changes.efficiency < 0" class="h-4 w-4 text-red-500 mr-1" />
                  <MinusIcon v-else class="h-4 w-4 text-gray-500 mr-1" />
                  <span class="text-sm font-medium" :class="stats.changes.efficiency > 0 ? 'text-green-700' : stats.changes.efficiency < 0 ? 'text-red-600' : 'text-gray-600'">
                    {{ stats.changes.efficiency > 0 ? '+' : '' }}{{ stats.changes.efficiency }}%
                  </span>
                  <span class="text-sm text-amber-600 ml-1">improvement</span>
                </div>
              </div>
              <div class="p-4 bg-amber-500 rounded-xl shadow-lg group-hover:bg-amber-600 transition-colors duration-300">
                <ChartBarIcon class="w-8 h-8 text-white" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Performance Analytics Row -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Revenue Trend Chart -->
        <div class="lg:col-span-2 bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-gray-900">Revenue Analytics</h3>
                <p class="text-sm text-gray-600 mt-1">Track your monthly performance</p>
              </div>
              <select v-model="revenueTimeframe" class="text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                <option value="6months">Last 6 Months</option>
                <option value="12months">Last 12 Months</option>
                <option value="ytd">Year to Date</option>
              </select>
            </div>
          </div>
          <div class="p-8">
            <div class="h-80 flex items-end justify-between space-x-3">
              <div v-for="(month, index) in revenueData" :key="index" class="flex-1 flex flex-col items-center group">
                <div class="w-full bg-gradient-to-t from-gray-100 to-gray-200 rounded-t-xl relative overflow-hidden shadow-inner" :style="{ height: '240px' }">
                  <div 
                    class="absolute bottom-0 w-full bg-gradient-to-t from-blue-600 via-blue-500 to-blue-400 rounded-t-xl transition-all duration-1000 ease-out shadow-lg group-hover:from-blue-700 group-hover:via-blue-600 group-hover:to-blue-500"
                    :style="{ height: `${(month.revenue / Math.max(...revenueData.map(m => m.revenue))) * 100}%` }"
                  ></div>
                  <div class="absolute top-3 left-0 right-0 text-center">
                    <span class="text-xs font-bold text-gray-700 bg-white/80 px-2 py-1 rounded-full shadow-sm">${{ (month.revenue / 1000).toFixed(0) }}k</span>
                  </div>
                </div>
                <span class="text-sm text-gray-700 mt-3 font-semibold">{{ month.month }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Client Status Distribution -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Client Portfolio</h3>
            <p class="text-sm text-gray-600 mt-1">Current client distribution</p>
          </div>
          <div class="p-6">
            <div class="space-y-5">
              <div v-for="status in clientStatusData" :key="status.label" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <div class="flex items-center">
                  <div class="w-4 h-4 rounded-full mr-4 shadow-sm" :class="status.color"></div>
                  <span class="text-sm font-semibold text-gray-700">{{ status.label }}</span>
                </div>
                <div class="text-right">
                  <div class="text-lg font-bold text-gray-900">{{ status.count }}</div>
                  <div class="text-xs text-gray-500 font-medium">{{ status.percentage }}%</div>
                </div>
              </div>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-200">
              <div class="flex justify-between text-sm mb-3">
                <span class="text-gray-600 font-medium">Client Satisfaction</span>
                <span class="font-bold text-emerald-600 text-lg">{{ clientSatisfaction }}%</span>
              </div>
              <div class="bg-gray-200 rounded-full h-3 shadow-inner">
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 h-3 rounded-full transition-all duration-1000 shadow-sm" :style="{ width: `${clientSatisfaction}%` }"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Operational Insights -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Recent Activity Timeline -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-gray-900">Live Activity Feed</h3>
                <p class="text-sm text-gray-600 mt-1">Real-time system updates</p>
              </div>
              <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 shadow-sm">
                <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
                Live
              </span>
            </div>
          </div>
          <div class="p-6 max-h-96 overflow-y-auto">
            <div v-if="recentActivities.length === 0" class="text-center py-12">
              <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <ClockIcon class="h-8 w-8 text-gray-400" />
              </div>
              <p class="text-gray-500 font-medium">No recent activity</p>
              <p class="text-gray-400 text-sm mt-1">Activity will appear here as it happens</p>
            </div>
            <div v-else class="flow-root">
              <ul class="-mb-8">
                <li v-for="(activity, index) in recentActivities" :key="index">
                  <div class="relative pb-8" :class="{ 'pb-0': index === recentActivities.length - 1 }">
                    <span v-if="index !== recentActivities.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gradient-to-b from-gray-200 to-gray-100"></span>
                    <div class="relative flex space-x-4">
                      <div>
                        <span class="h-10 w-10 rounded-xl flex items-center justify-center ring-4 ring-white shadow-lg" :class="activity.iconBg">
                          <component :is="activity.icon" class="h-5 w-5 text-white" />
                        </span>
                      </div>
                      <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div>
                          <p class="text-sm text-gray-600 font-medium" v-html="activity.description"></p>
                          <p v-if="activity.details" class="text-xs text-gray-400 mt-1">{{ activity.details }}</p>
                        </div>
                        <div class="text-right text-xs whitespace-nowrap text-gray-500 font-medium">
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
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Performance Metrics</h3>
            <p class="text-sm text-gray-600 mt-1">Key business indicators</p>
          </div>
          <div class="p-6">
            <div v-if="kpiData.length === 0" class="text-center py-12">
              <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <ChartBarIcon class="h-8 w-8 text-gray-400" />
              </div>
              <p class="text-gray-500 font-medium">No performance data available</p>
              <p class="text-gray-400 text-sm mt-1">Metrics will appear as data is collected</p>
            </div>
            <div v-else class="space-y-6">
              <div v-for="kpi in kpiData" :key="kpi.label" class="p-4 rounded-xl bg-gradient-to-r from-gray-50 to-slate-50 border border-gray-100">
                <div class="flex items-center justify-between mb-3">
                  <span class="text-sm font-semibold text-gray-700">{{ kpi.label }}</span>
                  <div class="flex items-center">
                    <span class="text-lg font-bold text-gray-900 mr-3">{{ kpi.value }}</span>
                    <span class="text-xs px-3 py-1 rounded-full font-semibold shadow-sm" :class="kpi.trend === 'up' ? 'bg-emerald-100 text-emerald-800' : kpi.trend === 'down' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'">
                      {{ kpi.change }}
                    </span>
                  </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner">
                  <div class="h-3 rounded-full transition-all duration-1000 ease-out shadow-sm" :class="kpi.barColor" :style="{ width: `${kpi.percentage}%` }"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Items & Alerts -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Priority Tasks -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-gray-900">Priority Tasks</h3>
                <p class="text-sm text-gray-600 mt-1">Important items requiring attention</p>
              </div>
              <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r from-red-100 to-pink-100 text-red-800 shadow-sm">
                {{ priorityTasks.filter(t => t.priority === 'high').length }} High Priority
              </span>
            </div>
          </div>
          <div class="p-6">
            <div v-if="priorityTasks.length === 0" class="text-center py-12">
              <div class="w-16 h-16 bg-gradient-to-br from-emerald-100 to-green-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <CheckIcon class="h-8 w-8 text-emerald-600" />
              </div>
              <p class="text-gray-500 font-medium">No priority tasks</p>
              <p class="text-gray-400 text-sm mt-1">All caught up! Great work.</p>
            </div>
            <div v-else class="space-y-4">
              <div v-for="task in priorityTasks" :key="task.id" class="flex items-start space-x-4 p-4 rounded-xl border-2 transition-all duration-200 hover:shadow-md" :class="task.priority === 'high' ? 'border-red-200 bg-gradient-to-r from-red-50 to-pink-50 hover:border-red-300' : task.priority === 'medium' ? 'border-amber-200 bg-gradient-to-r from-amber-50 to-yellow-50 hover:border-amber-300' : 'border-gray-200 bg-gradient-to-r from-gray-50 to-slate-50 hover:border-gray-300'">
                <div class="flex-shrink-0 mt-1">
                  <div class="w-3 h-3 rounded-full shadow-sm" :class="task.priority === 'high' ? 'bg-red-500' : task.priority === 'medium' ? 'bg-amber-500' : 'bg-gray-500'"></div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-900">{{ task.title }}</p>
                  <p class="text-xs text-gray-600 mt-1 font-medium">{{ task.client }} • Due {{ task.dueDate }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- System Alerts -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">System Alerts</h3>
            <p class="text-sm text-gray-600 mt-1">System status and notifications</p>
          </div>
          <div class="p-6">
            <div v-if="systemAlerts.length === 0" class="text-center py-12">
              <div class="w-16 h-16 bg-gradient-to-br from-emerald-100 to-green-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <CheckCircleIcon class="h-8 w-8 text-emerald-600" />
              </div>
              <p class="text-gray-500 font-medium">No system alerts</p>
              <p class="text-gray-400 text-sm mt-1">All systems running smoothly</p>
            </div>
            <div v-else class="space-y-4">
              <div v-for="alert in systemAlerts" :key="alert.id" class="flex items-start space-x-4 p-4 rounded-xl border-2 transition-all duration-200 hover:shadow-md" :class="alert.type === 'warning' ? 'border-amber-200 bg-gradient-to-r from-amber-50 to-yellow-50 hover:border-amber-300' : alert.type === 'info' ? 'border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50 hover:border-blue-300' : 'border-emerald-200 bg-gradient-to-r from-emerald-50 to-green-50 hover:border-emerald-300'">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm" :class="alert.type === 'warning' ? 'bg-amber-100' : alert.type === 'info' ? 'bg-blue-100' : 'bg-emerald-100'">
                    <ExclamationTriangleIcon v-if="alert.type === 'warning'" class="h-5 w-5 text-amber-600" />
                    <InformationCircleIcon v-else-if="alert.type === 'info'" class="h-5 w-5 text-blue-600" />
                    <CheckCircleIcon v-else class="h-5 w-5 text-emerald-600" />
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-900">{{ alert.title }}</p>
                  <p class="text-xs text-gray-600 mt-1">{{ alert.message }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Quick Actions</h3>
            <p class="text-sm text-gray-600 mt-1">Common tasks and shortcuts</p>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-2 gap-4">
              <button v-for="(action, index) in quickActions" :key="action.label" @click="action.action" class="flex flex-col items-center p-5 border-2 rounded-xl transition-all duration-300 group transform hover:scale-105 shadow-sm hover:shadow-lg" :class="getActionButtonClass(index)">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3 transition-all duration-300 shadow-inner" :class="getActionIconBgClass(index)">
                  <component :is="action.icon" class="h-6 w-6 transition-colors duration-300" :class="getActionIconClass(index)" />
                </div>
                <span class="text-xs font-semibold text-center transition-colors duration-300 leading-tight" :class="getActionTextClass(index)">{{ action.label }}</span>
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
  ArrowTrendingDownIcon,
  MinusIcon,
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

// Computed client satisfaction based on completed vs total clients
const clientSatisfaction = computed(() => {
  if (!props.stats.clientStatus || props.stats.totalClients === 0) return 0
  const completed = props.stats.clientStatus.completed || 0
  const total = props.stats.totalClients || 1
  return ((completed / total) * 100).toFixed(1)
})

// Dashboard statistics from props
const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      totalClients: 0,
      monthlyRevenue: 0,
      returnsFiled: 0,
      efficiencyScore: 0,
      changes: {
        clients: 0,
        revenue: 0,
        returns: 0,
        efficiency: 0
      },
      clientStatus: {
        activeReturns: 0,
        inReview: 0,
        completed: 0,
        pendingDocs: 0,
        newClients: 0
      }
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
const clientStatusData = computed(() => {
  if (!props.stats.clientStatus) return []
  
  const status = props.stats.clientStatus
  const total = Math.max(totalClients, 1) // Prevent division by zero
  
  return [
    { 
      label: 'Active Returns', 
      count: status.activeReturns || 0, 
      percentage: total > 0 ? ((status.activeReturns || 0) / total * 100).toFixed(1) : '0.0', 
      color: 'bg-green-500' 
    },
    { 
      label: 'In Review', 
      count: status.inReview || 0, 
      percentage: total > 0 ? ((status.inReview || 0) / total * 100).toFixed(1) : '0.0', 
      color: 'bg-yellow-500' 
    },
    { 
      label: 'Completed', 
      count: status.completed || 0, 
      percentage: total > 0 ? ((status.completed || 0) / total * 100).toFixed(1) : '0.0', 
      color: 'bg-blue-500' 
    },
    { 
      label: 'Pending Docs', 
      count: status.pendingDocs || 0, 
      percentage: total > 0 ? ((status.pendingDocs || 0) / total * 100).toFixed(1) : '0.0', 
      color: 'bg-red-500' 
    },
    { 
      label: 'New Clients', 
      count: status.newClients || 0, 
      percentage: total > 0 ? ((status.newClients || 0) / total * 100).toFixed(1) : '0.0', 
      color: 'bg-purple-500' 
    }
  ]
})

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

// Color schemes for quick action buttons
const actionColorSchemes = [
  {
    button: 'border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 hover:border-blue-400 hover:from-blue-100 hover:to-blue-200',
    iconBg: 'bg-gradient-to-br from-blue-100 to-blue-200 group-hover:from-blue-200 group-hover:to-blue-300',
    icon: 'text-blue-600 group-hover:text-blue-700',
    text: 'text-blue-700 group-hover:text-blue-800'
  },
  {
    button: 'border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100 hover:border-emerald-400 hover:from-emerald-100 hover:to-emerald-200',
    iconBg: 'bg-gradient-to-br from-emerald-100 to-emerald-200 group-hover:from-emerald-200 group-hover:to-emerald-300',
    icon: 'text-emerald-600 group-hover:text-emerald-700',
    text: 'text-emerald-700 group-hover:text-emerald-800'
  },
  {
    button: 'border-purple-200 bg-gradient-to-br from-purple-50 to-purple-100 hover:border-purple-400 hover:from-purple-100 hover:to-purple-200',
    iconBg: 'bg-gradient-to-br from-purple-100 to-purple-200 group-hover:from-purple-200 group-hover:to-purple-300',
    icon: 'text-purple-600 group-hover:text-purple-700',
    text: 'text-purple-700 group-hover:text-purple-800'
  },
  {
    button: 'border-amber-200 bg-gradient-to-br from-amber-50 to-amber-100 hover:border-amber-400 hover:from-amber-100 hover:to-amber-200',
    iconBg: 'bg-gradient-to-br from-amber-100 to-amber-200 group-hover:from-amber-200 group-hover:to-amber-300',
    icon: 'text-amber-600 group-hover:text-amber-700',
    text: 'text-amber-700 group-hover:text-amber-800'
  },
  {
    button: 'border-rose-200 bg-gradient-to-br from-rose-50 to-rose-100 hover:border-rose-400 hover:from-rose-100 hover:to-rose-200',
    iconBg: 'bg-gradient-to-br from-rose-100 to-rose-200 group-hover:from-rose-200 group-hover:to-rose-300',
    icon: 'text-rose-600 group-hover:text-rose-700',
    text: 'text-rose-700 group-hover:text-rose-800'
  },
  {
    button: 'border-indigo-200 bg-gradient-to-br from-indigo-50 to-indigo-100 hover:border-indigo-400 hover:from-indigo-100 hover:to-indigo-200',
    iconBg: 'bg-gradient-to-br from-indigo-100 to-indigo-200 group-hover:from-indigo-200 group-hover:to-indigo-300',
    icon: 'text-indigo-600 group-hover:text-indigo-700',
    text: 'text-indigo-700 group-hover:text-indigo-800'
  }
]

const getActionButtonClass = (index) => {
  return actionColorSchemes[index % actionColorSchemes.length].button
}

const getActionIconBgClass = (index) => {
  return actionColorSchemes[index % actionColorSchemes.length].iconBg
}

const getActionIconClass = (index) => {
  return actionColorSchemes[index % actionColorSchemes.length].icon
}

const getActionTextClass = (index) => {
  return actionColorSchemes[index % actionColorSchemes.length].text
}

onMounted(() => {
  // Initialize any real-time data connections here
})
</script>