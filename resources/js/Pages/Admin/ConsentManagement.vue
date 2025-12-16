<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-blue-50 to-cyan-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-blue-100/40 to-transparent rounded-bl-full"></div>
        
        <div class="px-10 relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-blue-600 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-cyan-900 bg-clip-text text-transparent">
                Consent Management
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Track and manage user consent preferences</p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Users</p>
              <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.totalUsers }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Consents Given</p>
              <p class="text-3xl font-bold text-green-600 mt-2">{{ stats.consentsGiven }}%</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Analytics Enabled</p>
              <p class="text-3xl font-bold text-indigo-600 mt-2">{{ stats.analyticsEnabled }}%</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Marketing Enabled</p>
              <p class="text-3xl font-bold text-purple-600 mt-2">{{ stats.marketingEnabled }}%</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Consent Logs Table -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <div>
              <h2 class="text-lg font-medium text-gray-900">Recent Consent Activities</h2>
              <p class="mt-1 text-sm text-gray-600">Latest user consent actions and preferences</p>
            </div>
            <div class="flex gap-2">
              <select v-model="filterAction" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All Actions</option>
                <option value="accepted">Accepted</option>
                <option value="rejected">Rejected</option>
                <option value="withdrawn">Withdrawn</option>
                <option value="updated">Updated</option>
              </select>
              <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Search by user..." 
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm"
              />
            </div>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Consents</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Source</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">IP Address</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="log in filteredLogs" :key="log.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ log.user?.name || 'Anonymous' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'px-3 py-1 rounded-full text-xs font-medium',
                    log.action === 'accepted' ? 'bg-green-100 text-green-800' :
                    log.action === 'rejected' ? 'bg-red-100 text-red-800' :
                    log.action === 'withdrawn' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-blue-100 text-blue-800'
                  ]">
                    {{ log.action }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                  <div class="flex flex-wrap gap-1">
                    <span v-for="(value, key) in log.consents" :key="key" :class="[
                      'px-2 py-1 rounded text-xs',
                      value ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-700'
                    ]">
                      {{ key }}: {{ value ? '✓' : '✗' }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ log.source }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ formatDate(log.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono text-xs">
                  {{ log.ip_address }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Consent Summary by Type -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Consent Breakdown</h3>
          <div class="space-y-4">
            <div v-for="(count, type) in consentBreakdown" :key="type" class="flex items-center justify-between">
              <span class="text-sm font-medium text-gray-700 capitalize">{{ type }}</span>
              <div class="flex items-center gap-2">
                <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-blue-600" :style="{ width: count + '%' }"></div>
                </div>
                <span class="text-sm font-semibold text-gray-900">{{ count }}%</span>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Activity by Source</h3>
          <div class="space-y-4">
            <div v-for="(count, source) in activityBySource" :key="source" class="flex items-center justify-between">
              <span class="text-sm font-medium text-gray-700 capitalize">{{ source }}</span>
              <span class="text-sm font-semibold text-gray-900">{{ count }} actions</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const logs = ref([])
const filterAction = ref('')
const searchQuery = ref('')
const stats = ref({
  totalUsers: 0,
  consentsGiven: 0,
  analyticsEnabled: 0,
  marketingEnabled: 0,
})

const consentBreakdown = ref({
  terms: 95,
  privacy: 95,
  analytics: 65,
  marketing: 42,
  cookies: 70,
})

const activityBySource = ref({
  banner: 245,
  settings: 89,
  registration: 156,
})

onMounted(() => {
  loadConsentLogs()
})

const loadConsentLogs = async () => {
  try {
    // This would be replaced with actual API call
    logs.value = [
      {
        id: 1,
        user: { name: 'John Doe' },
        action: 'accepted',
        consents: { terms: true, privacy: true, analytics: true, marketing: false, cookies: true },
        source: 'banner',
        created_at: new Date().toISOString(),
        ip_address: '192.168.1.1',
      },
      {
        id: 2,
        user: { name: 'Jane Smith' },
        action: 'updated',
        consents: { analytics: false, marketing: true },
        source: 'settings',
        created_at: new Date(Date.now() - 3600000).toISOString(),
        ip_address: '192.168.1.2',
      },
    ]
  } catch (error) {
    console.error('Error loading consent logs:', error)
  }
}

const filteredLogs = computed(() => {
  return logs.value.filter(log => {
    const matchesAction = !filterAction.value || log.action === filterAction.value
    const matchesSearch = !searchQuery.value || 
      (log.user?.name || 'Anonymous').toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchesAction && matchesSearch
  })
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString()
}
</script>
