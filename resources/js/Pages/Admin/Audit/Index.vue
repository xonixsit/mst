<template>
  <AppLayout title="Audit Logs">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Header -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <div>
                <h2 class="text-2xl font-bold text-gray-900">Audit Trail</h2>
                <p class="mt-1 text-sm text-gray-600">
                  Track all system activities and data changes for compliance
                </p>
              </div>
              <div class="flex space-x-3">
                <button
                  @click="showComplianceReport = true"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <DocumentChartBarIcon class="w-4 h-4 mr-2" />
                  Compliance Report
                </button>
                <button
                  @click="exportLogs"
                  class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <ArrowDownTrayIcon class="w-4 h-4 mr-2" />
                  Export CSV
                </button>
              </div>
            </div>
          </div>

          <!-- Filters -->
          <div class="p-6 bg-gray-50 border-b border-gray-200">
            <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <!-- Search -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                  v-model="filterForm.search"
                  type="text"
                  placeholder="Search users, events, models..."
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>

              <!-- Event Type -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                <select
                  v-model="filterForm.event"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Events</option>
                  <option v-for="event in eventTypes" :key="event.value" :value="event.value">
                    {{ event.label }}
                  </option>
                </select>
              </div>

              <!-- Model Type -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Model Type</label>
                <select
                  v-model="filterForm.model_type"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Models</option>
                  <option v-for="model in modelTypes" :key="model.value" :value="model.value">
                    {{ model.label }}
                  </option>
                </select>
              </div>

              <!-- User -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                <select
                  v-model="filterForm.user_id"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Users</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }}
                  </option>
                </select>
              </div>

              <!-- Date Range -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input
                  v-model="filterForm.start_date"
                  type="date"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input
                  v-model="filterForm.end_date"
                  type="date"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>

              <!-- Per Page -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Per Page</label>
                <select
                  v-model="filterForm.per_page"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </div>

              <!-- Filter Actions -->
              <div class="flex items-end space-x-2">
                <button
                  type="submit"
                  class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                  Apply Filters
                </button>
                <button
                  type="button"
                  @click="clearFilters"
                  class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
                >
                  Clear
                </button>
              </div>
            </form>
          </div>

          <!-- Audit Logs Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date/Time
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    User
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Event
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Model
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Description
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    IP Address
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="log in auditLogs.data" :key="log.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDateTime(log.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-8 w-8">
                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                          <UserIcon class="h-4 w-4 text-gray-600" />
                        </div>
                      </div>
                      <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">
                          {{ log.user ? log.user.name : 'System' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getEventBadgeClass(log.event)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                      {{ formatEvent(log.event) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ getModelName(log.auditable_type) }} #{{ log.auditable_id }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                    {{ log.description }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ log.ip_address }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <Link
                      :href="route('admin.audit.show', log.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      View Details
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ auditLogs.from }} to {{ auditLogs.to }} of {{ auditLogs.total }} results
              </div>
              <div class="flex space-x-2">
                <Link
                  v-if="auditLogs.prev_page_url"
                  :href="auditLogs.prev_page_url"
                  class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Previous
                </Link>
                <Link
                  v-if="auditLogs.next_page_url"
                  :href="auditLogs.next_page_url"
                  class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Next
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Compliance Report Modal -->
    <ComplianceReportModal
      v-if="showComplianceReport"
      @close="showComplianceReport = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ComplianceReportModal from '@/Components/ComplianceReportModal.vue'
import {
  DocumentChartBarIcon,
  ArrowDownTrayIcon,
  UserIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  auditLogs: Object,
  filters: Object,
  users: Array,
  modelTypes: Array,
  eventTypes: Array,
})

const showComplianceReport = ref(false)

const filterForm = reactive({
  search: props.filters.search || '',
  event: props.filters.event || '',
  model_type: props.filters.model_type || '',
  user_id: props.filters.user_id || '',
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || '',
  per_page: props.filters.per_page || 25,
})

const applyFilters = () => {
  router.get(route('admin.audit.index'), filterForm, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  Object.keys(filterForm).forEach(key => {
    if (key === 'per_page') {
      filterForm[key] = 25
    } else {
      filterForm[key] = ''
    }
  })
  applyFilters()
}

const exportLogs = () => {
  const params = new URLSearchParams(filterForm).toString()
  window.open(route('admin.audit.export') + '?' + params, '_blank')
}

const formatDateTime = (dateTime) => {
  return new Date(dateTime).toLocaleString()
}

const formatEvent = (event) => {
  return event.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const getModelName = (modelType) => {
  return modelType.split('\\').pop()
}

const getEventBadgeClass = (event) => {
  const classes = {
    created: 'bg-green-100 text-green-800',
    updated: 'bg-blue-100 text-blue-800',
    deleted: 'bg-red-100 text-red-800',
    status_changed: 'bg-yellow-100 text-yellow-800',
    assigned: 'bg-purple-100 text-purple-800',
    bulk_email: 'bg-indigo-100 text-indigo-800',
    bulk_status_update: 'bg-orange-100 text-orange-800',
  }
  return classes[event] || 'bg-gray-100 text-gray-800'
}
</script>