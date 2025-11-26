<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
          <p class="mt-1 text-sm text-gray-600">Generate and download comprehensive business reports</p>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Report Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Client Summary Report -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-lg bg-blue-100">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-gray-900">Client Summary</h3>
              <p class="text-sm text-gray-600">Overview of all clients and their activity</p>
            </div>
          </div>
          <div class="mt-4">
            <button
              @click="showReportModal('client-summary')"
              class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
              Generate Report
            </button>
          </div>
        </div>

        <!-- Financial Report -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-lg bg-green-100">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-gray-900">Financial Report</h3>
              <p class="text-sm text-gray-600">Revenue, invoices, and payment analysis</p>
            </div>
          </div>
          <div class="mt-4">
            <button
              @click="showReportModal('financial')"
              class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
            >
              Generate Report
            </button>
          </div>
        </div>

        <!-- Document Activity Report -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-lg bg-purple-100">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-gray-900">Document Activity</h3>
              <p class="text-sm text-gray-600">Document uploads and review status</p>
            </div>
          </div>
          <div class="mt-4">
            <button
              @click="showReportModal('document-activity')"
              class="w-full bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700"
            >
              Generate Report
            </button>
          </div>
        </div>

        <!-- Communication Report -->
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-3 rounded-lg bg-orange-100">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-gray-900">Communication</h3>
              <p class="text-sm text-gray-600">Messages and client interactions</p>
            </div>
          </div>
          <div class="mt-4">
            <button
              @click="showReportModal('communication')"
              class="w-full bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700"
            >
              Generate Report
            </button>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Quick Statistics</h2>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">{{ stats.total_clients }}</div>
              <div class="text-sm text-gray-600">Total Clients</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">${{ formatCurrency(stats.total_revenue) }}</div>
              <div class="text-sm text-gray-600">Total Revenue</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600">{{ stats.total_documents }}</div>
              <div class="text-sm text-gray-600">Documents Processed</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-orange-600">{{ stats.total_messages }}</div>
              <div class="text-sm text-gray-600">Messages Exchanged</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Report Generation Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Generate {{ getReportTitle(currentReport) }}</h3>
          
          <form @submit.prevent="generateReport">
            <!-- Date Range -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
              
              <!-- Quick Date Range Buttons -->
              <div class="grid grid-cols-4 gap-2 mb-3">
                <button
                  type="button"
                  @click="setDateRange('3months')"
                  class="px-3 py-2 text-xs bg-gray-100 hover:bg-gray-200 rounded-md border"
                  :class="{ 'bg-blue-100 border-blue-300': selectedRange === '3months' }"
                >
                  Last 3 Months
                </button>
                <button
                  type="button"
                  @click="setDateRange('6months')"
                  class="px-3 py-2 text-xs bg-gray-100 hover:bg-gray-200 rounded-md border"
                  :class="{ 'bg-blue-100 border-blue-300': selectedRange === '6months' }"
                >
                  Last 6 Months
                </button>
                <button
                  type="button"
                  @click="setDateRange('12months')"
                  class="px-3 py-2 text-xs bg-gray-100 hover:bg-gray-200 rounded-md border"
                  :class="{ 'bg-blue-100 border-blue-300': selectedRange === '12months' }"
                >
                  Last 12 Months
                </button>
                <button
                  type="button"
                  @click="setDateRange('custom')"
                  class="px-3 py-2 text-xs bg-gray-100 hover:bg-gray-200 rounded-md border"
                  :class="{ 'bg-blue-100 border-blue-300': selectedRange === 'custom' }"
                >
                  Custom
                </button>
              </div>

              <!-- Custom Date Inputs -->
              <div v-if="selectedRange === 'custom'" class="grid grid-cols-2 gap-2">
                <input
                  v-model="reportForm.start_date"
                  type="date"
                  class="border border-gray-300 rounded-md px-3 py-2 text-sm"
                  placeholder="Start Date"
                />
                <input
                  v-model="reportForm.end_date"
                  type="date"
                  class="border border-gray-300 rounded-md px-3 py-2 text-sm"
                />
              </div>

              <!-- Display Selected Range -->
              <div v-if="selectedRange !== 'custom'" class="text-sm text-gray-600 mt-2">
                <span class="font-medium">Selected Period:</span>
                {{ formatDateRange() }}
              </div>
            </div>

            <!-- Status Filter (for applicable reports) -->
            <div v-if="currentReport === 'client-summary'" class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Client Status</label>
              <select
                v-model="reportForm.status"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
              >
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="archived">Archived</option>
              </select>
            </div>

            <div v-if="currentReport === 'document-activity'" class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Document Status</label>
              <select
                v-model="reportForm.status"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
              >
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

            <!-- Format Selection -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
              <div class="grid grid-cols-3 gap-2">
                <label class="flex items-center">
                  <input
                    v-model="reportForm.format"
                    type="radio"
                    value="pdf"
                    class="mr-2"
                  />
                  PDF
                </label>
                <label class="flex items-center">
                  <input
                    v-model="reportForm.format"
                    type="radio"
                    value="excel"
                    class="mr-2"
                  />
                  Excel
                </label>
                <label class="flex items-center">
                  <input
                    v-model="reportForm.format"
                    type="radio"
                    value="csv"
                    class="mr-2"
                  />
                  CSV
                </label>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="closeModal"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="generating"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
              >
                {{ generating ? 'Generating...' : 'Generate Report' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  name: 'AdminReportsIndex',
  components: {
    AppLayout
  },
  setup() {
    const showModal = ref(false)
    const currentReport = ref('')
    const generating = ref(false)
    const selectedRange = ref('3months')
    const stats = ref({
      total_clients: 0,
      total_revenue: 0,
      total_documents: 0,
      total_messages: 0
    })

    const reportForm = ref({
      start_date: '',
      end_date: '',
      status: '',
      format: 'pdf'
    })

    const showReportModal = (reportType) => {
      currentReport.value = reportType
      selectedRange.value = '3months'
      reportForm.value = {
        start_date: '',
        end_date: '',
        status: '',
        format: 'pdf'
      }
      setDateRange('3months') // Set default to last 3 months
      showModal.value = true
    }

    const closeModal = () => {
      showModal.value = false
      currentReport.value = ''
      selectedRange.value = '3months'
    }

    const setDateRange = (range) => {
      selectedRange.value = range
      
      if (range === 'custom') {
        // Clear dates for custom range - user will set them manually
        reportForm.value.start_date = ''
        reportForm.value.end_date = ''
        return
      }
      
      const today = new Date()
      const endDate = today.toISOString().split('T')[0]
      
      let startDate
      const startDateObj = new Date()
      
      switch (range) {
        case '3months':
          startDateObj.setMonth(startDateObj.getMonth() - 3)
          startDate = startDateObj.toISOString().split('T')[0]
          break
        case '6months':
          startDateObj.setMonth(startDateObj.getMonth() - 6)
          startDate = startDateObj.toISOString().split('T')[0]
          break
        case '12months':
          startDateObj.setFullYear(startDateObj.getFullYear() - 1)
          startDate = startDateObj.toISOString().split('T')[0]
          break
      }
      
      reportForm.value.start_date = startDate
      reportForm.value.end_date = endDate
    }

    const formatDateRange = () => {
      if (selectedRange.value === 'custom') return ''
      
      const ranges = {
        '3months': 'Last 3 Months',
        '6months': 'Last 6 Months', 
        '12months': 'Last 12 Months'
      }
      
      const rangeText = ranges[selectedRange.value]
      if (reportForm.value.start_date && reportForm.value.end_date) {
        return `${rangeText} (${reportForm.value.start_date} to ${reportForm.value.end_date})`
      }
      return rangeText
    }

    const getReportTitle = (reportType) => {
      const titles = {
        'client-summary': 'Client Summary Report',
        'financial': 'Financial Report',
        'document-activity': 'Document Activity Report',
        'communication': 'Communication Report'
      }
      return titles[reportType] || 'Report'
    }

    const generateReport = async () => {
      generating.value = true
      
      try {
        const url = `/admin/reports/${currentReport.value}`
        
        // Prepare parameters including date range
        const params = {
          ...reportForm.value,
          date_range: selectedRange.value
        }
        
        const queryString = new URLSearchParams(params)
        
        // Create a temporary link to trigger download
        const link = document.createElement('a')
        link.href = `${url}?${queryString}`
        link.download = true
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        
        closeModal()
      } catch (error) {
        console.error('Error generating report:', error)
        alert('Failed to generate report. Please try again.')
      } finally {
        generating.value = false
      }
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(amount || 0)
    }

    const loadStats = async () => {
      try {
        const response = await fetch('/admin/reports/stats')
        const data = await response.json()
        stats.value = data
      } catch (error) {
        console.error('Error loading stats:', error)
      }
    }

    onMounted(() => {
      loadStats()
    })

    return {
      showModal,
      currentReport,
      generating,
      selectedRange,
      stats,
      reportForm,
      showReportModal,
      closeModal,
      setDateRange,
      formatDateRange,
      getReportTitle,
      generateReport,
      formatCurrency
    }
  }
}
</script>