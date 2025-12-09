<template>
  <AppLayout title="Export Clients">
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-4">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <div class="flex items-center justify-between mb-8">
              <div>
                <h1 class="text-2xl font-medium text-gray-900">Export Client Data</h1>
                <p class="mt-2 text-sm text-gray-600">
                  Export client information in various formats with customizable templates and options.
                </p>
              </div>
              <div class="flex space-x-3">
                <Link
                  :href="route('admin.clients.index')"
                  class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150"
                >
                  Back to Clients
                </Link>
              </div>
            </div>

            <!-- Client Selection -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Select Clients to Export</h3>
              
              <div class="space-y-4">
                <div class="flex items-center space-x-4">
                  <label class="flex items-center">
                    <input
                      v-model="exportAll"
                      type="checkbox"
                      @change="handleExportAllChange"
                      class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    />
                    <span class="ml-2 text-sm text-gray-700">Export all clients</span>
                  </label>
                  
                  <div v-if="!exportAll" class="flex-1">
                    <input
                      v-model="clientSearch"
                      type="text"
                      placeholder="Search clients by name or email..."
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                      @input="searchClients"
                    />
                  </div>
                </div>

                <!-- Selected Clients Display -->
                <div v-if="selectedClients.length > 0" class="bg-blue-50 border border-blue-200 rounded-md p-4">
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-blue-900">
                      {{ selectedClients.length }} client(s) selected for export
                    </span>
                    <button
                      @click="clearSelection"
                      class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                      Clear selection
                    </button>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="client in selectedClients.slice(0, 10)"
                      :key="client.id"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      {{ client.name }}
                      <button
                        @click="removeClient(client.id)"
                        class="ml-1 text-blue-600 hover:text-blue-800"
                      >
                        ×
                      </button>
                    </span>
                    <span
                      v-if="selectedClients.length > 10"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                    >
                      +{{ selectedClients.length - 10 }} more
                    </span>
                  </div>
                </div>

                <!-- Client Search Results -->
                <div v-if="!exportAll && searchResults.length > 0" class="border border-gray-200 rounded-md max-h-60 overflow-y-auto">
                  <div
                    v-for="client in searchResults"
                    :key="client.id"
                    class="p-3 border-b border-gray-200 last:border-b-0 hover:bg-gray-50 cursor-pointer"
                    @click="toggleClient(client)"
                  >
                    <div class="flex items-center justify-between">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ client.name }}</div>
                        <div class="text-sm text-gray-500">{{ client.email }}</div>
                      </div>
                      <input
                        :checked="isClientSelected(client.id)"
                        type="checkbox"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        @click.stop
                        @change="toggleClient(client)"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Export Configuration -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Export Configuration</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Format Selection -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
                  <select
                    v-model="exportConfig.format"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  >
                    <option value="excel">Excel (.xlsx)</option>
                    <option value="csv">CSV (.csv)</option>
                    <option value="pdf">PDF (.pdf)</option>
                    <option value="json">JSON (.json)</option>
                  </select>
                </div>

                <!-- Template Selection -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Export Template</label>
                  <select
                    v-model="exportConfig.template"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  >
                    <option
                      v-for="(template, key) in availableTemplates"
                      :key="key"
                      :value="key"
                    >
                      {{ template.name }}
                    </option>
                  </select>
                  <p class="mt-1 text-xs text-gray-500">
                    {{ availableTemplates[exportConfig.template]?.description }}
                  </p>
                </div>
              </div>

              <!-- Template Details -->
              <div class="mt-6 p-4 bg-white border border-gray-200 rounded-md">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Template Fields</h4>
                <div class="text-sm text-gray-600">
                  <div v-if="exportConfig.template === 'custom'" class="space-y-2">
                    <p class="font-medium">Select fields to include:</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                      <label
                        v-for="field in customFieldOptions"
                        :key="field.value"
                        class="flex items-center"
                      >
                        <input
                          v-model="exportConfig.customFields"
                          :value="field.value"
                          type="checkbox"
                          class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        />
                        <span class="ml-2 text-xs">{{ field.label }}</span>
                      </label>
                    </div>
                  </div>
                  <div v-else>
                    <p>{{ availableTemplates[exportConfig.template]?.fields.join(', ') }}</p>
                  </div>
                </div>
              </div>

              <!-- Export Options -->
              <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Export Options</h4>
                <div class="space-y-3">
                  <label class="flex items-center">
                    <input
                      v-model="exportConfig.includeMetadata"
                      type="checkbox"
                      class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    />
                    <span class="ml-2 text-sm text-gray-600">Include export metadata</span>
                  </label>
                  <label class="flex items-center">
                    <input
                      v-model="exportConfig.includeSummary"
                      type="checkbox"
                      class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    />
                    <span class="ml-2 text-sm text-gray-600">Include summary statistics</span>
                  </label>
                  <label class="flex items-center">
                    <input
                      v-model="exportConfig.includeRelationships"
                      type="checkbox"
                      class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    />
                    <span class="ml-2 text-sm text-gray-600">Include relationship data (spouse, assets, etc.)</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Export Statistics -->
            <div v-if="exportStats" class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Export Preview</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                  <div class="text-2xl font-bold text-blue-600">{{ exportStats.total_clients }}</div>
                  <div class="text-sm text-blue-600">Total Clients</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                  <div class="text-2xl font-bold text-green-600">{{ exportStats.status_breakdown?.active || 0 }}</div>
                  <div class="text-sm text-green-600">Active</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                  <div class="text-2xl font-bold text-yellow-600">{{ exportStats.relationship_counts?.with_spouse || 0 }}</div>
                  <div class="text-sm text-yellow-600">With Spouse</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                  <div class="text-2xl font-bold text-purple-600">{{ exportStats.relationship_counts?.total_assets || 0 }}</div>
                  <div class="text-sm text-purple-600">Total Assets</div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                  <h4 class="font-medium text-gray-900 mb-2">Status Breakdown</h4>
                  <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                      <span>Active:</span>
                      <span>{{ exportStats.status_breakdown?.active || 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Inactive:</span>
                      <span>{{ exportStats.status_breakdown?.inactive || 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Archived:</span>
                      <span>{{ exportStats.status_breakdown?.archived || 0 }}</span>
                    </div>
                  </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                  <h4 class="font-medium text-gray-900 mb-2">Relationship Data</h4>
                  <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                      <span>Employees:</span>
                      <span>{{ exportStats.relationship_counts?.total_employees || 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Projects:</span>
                      <span>{{ exportStats.relationship_counts?.total_projects || 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Expenses:</span>
                      <span>{{ exportStats.relationship_counts?.total_expenses || 0 }}</span>
                    </div>
                  </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                  <h4 class="font-medium text-gray-900 mb-2">Financial Summary</h4>
                  <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                      <span>Asset Value:</span>
                      <span>${{ formatCurrency(exportStats.financial_summary?.total_asset_value || 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Expenses:</span>
                      <span>${{ formatCurrency(exportStats.financial_summary?.total_expense_amount || 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span>Invoices:</span>
                      <span>${{ formatCurrency(exportStats.financial_summary?.total_invoice_amount || 0) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
              <button
                @click="getExportPreview"
                :disabled="!canExport || processing"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150"
              >
                <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Preview Export
              </button>

              <div class="flex space-x-3">
                <button
                  @click="showScheduleModal = true"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                  Schedule Export
                </button>
                <button
                  @click="processExport"
                  :disabled="!canExport || processing"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                  <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Export Now
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Export Modal -->
    <div v-if="showScheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule Automated Export</h3>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
              <select
                v-model="scheduleConfig.frequency"
                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              >
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email Recipients</label>
              <textarea
                v-model="scheduleConfig.recipients"
                placeholder="Enter email addresses, one per line"
                rows="3"
                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              ></textarea>
            </div>
          </div>

          <div class="flex justify-end space-x-3 mt-6">
            <button
              @click="showScheduleModal = false"
              class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors"
            >
              Cancel
            </button>
            <button
              @click="scheduleExport"
              class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
            >
              Schedule
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  availableTemplates: Object,
  availableUsers: Array
})

const exportAll = ref(false)
const clientSearch = ref('')
const searchResults = ref([])
const selectedClients = ref([])
const processing = ref(false)
const exportStats = ref(null)
const showScheduleModal = ref(false)

const exportConfig = reactive({
  format: 'excel',
  template: 'comprehensive',
  customFields: [],
  includeMetadata: false,
  includeSummary: false,
  includeRelationships: true
})

const scheduleConfig = reactive({
  frequency: 'weekly',
  recipients: ''
})

const customFieldOptions = [
  { value: 'id', label: 'ID' },
  { value: 'full_name', label: 'Full Name' },
  { value: 'email', label: 'Email' },
  { value: 'phone', label: 'Phone' },
  { value: 'status', label: 'Status' },
  { value: 'address', label: 'Address' },
  { value: 'spouse_name', label: 'Spouse Name' },
  { value: 'assets_count', label: 'Assets Count' },
  { value: 'expenses_count', label: 'Expenses Count' },
  { value: 'registration_date', label: 'Registration Date' }
]

const canExport = computed(() => {
  return exportAll.value || selectedClients.value.length > 0
})

const handleExportAllChange = () => {
  if (exportAll.value) {
    selectedClients.value = []
    searchResults.value = []
    clientSearch.value = ''
  }
}

const searchClients = async () => {
  if (clientSearch.value.length < 2) {
    searchResults.value = []
    return
  }

  try {
    const response = await fetch(route('admin.clients.index', { 
      search: clientSearch.value,
      per_page: 20 
    }), {
      headers: {
        'Accept': 'application/json'
      }
    })
    
    const data = await response.json()
    searchResults.value = data.clients?.data || []
  } catch (error) {
    console.error('Search failed:', error)
  }
}

const toggleClient = (client) => {
  const index = selectedClients.value.findIndex(c => c.id === client.id)
  if (index > -1) {
    selectedClients.value.splice(index, 1)
  } else {
    selectedClients.value.push(client)
  }
}

const isClientSelected = (clientId) => {
  return selectedClients.value.some(c => c.id === clientId)
}

const removeClient = (clientId) => {
  selectedClients.value = selectedClients.value.filter(c => c.id !== clientId)
}

const clearSelection = () => {
  selectedClients.value = []
  exportAll.value = false
}

const getExportPreview = async () => {
  if (!canExport.value) return

  processing.value = true
  
  try {
    const clientIds = exportAll.value ? [] : selectedClients.value.map(c => c.id)
    
    const response = await fetch(route('admin.clients.export-stats'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ client_ids: clientIds })
    })

    const result = await response.json()
    if (result.success) {
      exportStats.value = result.statistics
    }
  } catch (error) {
    console.error('Preview failed:', error)
  } finally {
    processing.value = false
  }
}

const processExport = async () => {
  if (!canExport.value) return

  processing.value = true

  try {
    const clientIds = exportAll.value ? [] : selectedClients.value.map(c => c.id)
    
    const exportData = {
      client_ids: clientIds,
      format: exportConfig.format,
      template: exportConfig.template,
      custom_fields: exportConfig.customFields,
      include_metadata: exportConfig.includeMetadata,
      include_summary: exportConfig.includeSummary,
      include_relationships: exportConfig.includeRelationships
    }

    const response = await fetch(route('admin.clients.process-comprehensive-export'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(exportData)
    })

    if (response.ok) {
      // Handle file download
      const blob = await response.blob()
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.style.display = 'none'
      a.href = url
      
      // Extract filename from response headers
      const contentDisposition = response.headers.get('content-disposition')
      const filename = contentDisposition 
        ? contentDisposition.split('filename=')[1]?.replace(/"/g, '')
        : `clients_export.${exportConfig.format}`
      
      a.download = filename
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    } else {
      const error = await response.json()
      console.error('Export failed:', error)
    }
  } catch (error) {
    console.error('Export failed:', error)
  } finally {
    processing.value = false
  }
}

const scheduleExport = async () => {
  try {
    const recipients = scheduleConfig.recipients
      .split('\n')
      .map(email => email.trim())
      .filter(email => email)

    const scheduleData = {
      frequency: scheduleConfig.frequency,
      format: exportConfig.format,
      template: exportConfig.template,
      recipients: recipients
    }

    const response = await fetch(route('admin.clients.schedule-automated-export'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(scheduleData)
    })

    const result = await response.json()
    if (result.success) {
      showScheduleModal.value = false
      // Show success message
    }
  } catch (error) {
    console.error('Schedule failed:', error)
  }
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount)
}

onMounted(() => {
  // Auto-preview if we have selection
  if (canExport.value) {
    getExportPreview()
  }
})
</script>