<template>
  <AppLayout title="Reports Management">
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-cyan-50 to-blue-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-cyan-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-blue-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2 pr-2 pl-2">
          <div class="flex items-center space-x-4">
            <!-- Document Management Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-cyan-900 to-blue-900 bg-clip-text text-transparent">
                Reports Management
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Generate and download comprehensive business reports</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-cyan-700">{{ stats.total_clients }} Total Clients</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-green-700">${{ formatCurrency(stats.total_revenue) }} Revenue</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-4">
        <!-- Enhanced Report Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Client Summary Report -->
          <div class="relative overflow-hidden bg-gradient-to-br from-cyan-50 via-cyan-100 to-cyan-200 rounded-xl shadow-lg border border-cyan-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-cyan-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-4 bg-cyan-500 rounded-xl shadow-lg group-hover:bg-cyan-600 transition-colors duration-300">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="mb-4">
                <h3 class="text-xl font-bold text-cyan-900 mb-2">Client Summary</h3>
                <p class="text-sm text-cyan-700 font-medium">Overview of all clients and their activity</p>
              </div>
              <button
                @click="showReportModal('client-summary')"
                class="w-full bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
              >
                Generate Report
              </button>
            </div>
          </div>

          <!-- Financial Report -->
          <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-emerald-100 to-emerald-200 rounded-xl shadow-lg border border-emerald-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-emerald-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-4 bg-emerald-500 rounded-xl shadow-lg group-hover:bg-emerald-600 transition-colors duration-300">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="mb-4">
                <h3 class="text-xl font-bold text-emerald-900 mb-2">Financial Report</h3>
                <p class="text-sm text-emerald-700 font-medium">Revenue, invoices, and payment analysis</p>
              </div>
              <button
                @click="showReportModal('financial')"
                class="w-full bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
              >
                Generate Report
              </button>
            </div>
          </div>

          <!-- Document Activity Report -->
          <div class="relative overflow-hidden bg-gradient-to-br from-purple-50 via-purple-100 to-purple-200 rounded-xl shadow-lg border border-purple-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-purple-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-4 bg-purple-500 rounded-xl shadow-lg group-hover:bg-purple-600 transition-colors duration-300">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
              </div>
              <div class="mb-4">
                <h3 class="text-xl font-bold text-purple-900 mb-2">Document Activity</h3>
                <p class="text-sm text-purple-700 font-medium">Document uploads and review status</p>
              </div>
              <button
                @click="showReportModal('document-activity')"
                class="w-full bg-gradient-to-r from-purple-600 to-violet-600 hover:from-purple-700 hover:to-violet-700 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
              >
                Generate Report
              </button>
            </div>
          </div>

          <!-- Communication Report -->
          <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 via-amber-100 to-amber-200 rounded-xl shadow-lg border border-amber-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-amber-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between mb-4">
                <div class="p-4 bg-amber-500 rounded-xl shadow-lg group-hover:bg-amber-600 transition-colors duration-300">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                  </svg>
                </div>
              </div>
              <div class="mb-4">
                <h3 class="text-xl font-bold text-amber-900 mb-2">Communication</h3>
                <p class="text-sm text-amber-700 font-medium">Messages and client interactions</p>
              </div>
              <button
                @click="showReportModal('communication')"
                class="w-full bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
              >
                Generate Report
              </button>
            </div>
          </div>
        </div>

        <!-- Enhanced Quick Statistics -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-gray-900">Quick Statistics</h3>
                <p class="text-sm text-gray-600 mt-1">Overview of your business metrics</p>
              </div>
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
            </div>
          </div>
          
          <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <!-- Total Clients -->
              <div class="relative overflow-hidden bg-gradient-to-br from-cyan-50 via-cyan-100 to-cyan-200 rounded-xl p-6 border border-cyan-200/50 hover:shadow-lg transition-all duration-300 group">
                <div class="absolute top-0 right-0 w-14 h-14 bg-gradient-to-bl from-cyan-300/30 to-transparent rounded-bl-full"></div>
                <div class="relative text-center">
                  <div class="text-3xl font-bold text-cyan-900 mb-2">{{ stats.total_clients }}</div>
                  <div class="text-sm font-semibold text-cyan-700 uppercase tracking-wide">Total Clients</div>
                </div>
              </div>

              <!-- Total Revenue -->
              <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-emerald-100 to-emerald-200 rounded-xl p-6 border border-emerald-200/50 hover:shadow-lg transition-all duration-300 group">
                <div class="absolute top-0 right-0 w-14 h-14 bg-gradient-to-bl from-emerald-300/30 to-transparent rounded-bl-full"></div>
                <div class="relative text-center">
                  <div class="text-3xl font-bold text-emerald-900 mb-2">${{ formatCurrency(stats.total_revenue) }}</div>
                  <div class="text-sm font-semibold text-emerald-700 uppercase tracking-wide">Total Revenue</div>
                </div>
              </div>

              <!-- Documents Processed -->
              <div class="relative overflow-hidden bg-gradient-to-br from-purple-50 via-purple-100 to-purple-200 rounded-xl p-6 border border-purple-200/50 hover:shadow-lg transition-all duration-300 group">
                <div class="absolute top-0 right-0 w-14 h-14 bg-gradient-to-bl from-purple-300/30 to-transparent rounded-bl-full"></div>
                <div class="relative text-center">
                  <div class="text-3xl font-bold text-purple-900 mb-2">{{ stats.total_documents }}</div>
                  <div class="text-sm font-semibold text-purple-700 uppercase tracking-wide">Documents Processed</div>
                </div>
              </div>

              <!-- Messages Exchanged -->
              <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 via-amber-100 to-amber-200 rounded-xl p-6 border border-amber-200/50 hover:shadow-lg transition-all duration-300 group">
                <div class="absolute top-0 right-0 w-14 h-14 bg-gradient-to-bl from-amber-300/30 to-transparent rounded-bl-full"></div>
                <div class="relative text-center">
                  <div class="text-3xl font-bold text-amber-900 mb-2">{{ stats.total_messages }}</div>
                  <div class="text-sm font-semibold text-amber-700 uppercase tracking-wide">Messages Exchanged</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Report Generation Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
      <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100 w-full max-w-lg mx-auto">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xl font-bold text-gray-900">Generate {{ getReportTitle(currentReport) }}</h3>
              <p class="text-sm text-gray-600 mt-1">Configure your report parameters</p>
            </div>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
          <form @submit.prevent="generateReport" class="space-y-6">
          
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