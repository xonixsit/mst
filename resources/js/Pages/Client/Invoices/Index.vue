<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-amber-50 to-orange-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-amber-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-orange-100/30 to-transparent rounded-tr-full"></div>
        
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <div class="w-14 h-14 bg-gradient-to-br from-amber-500 via-amber-600 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-amber-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
            </div>
            
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-amber-900 to-orange-900 bg-clip-text text-transparent">
                My Invoices
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">View and manage your billing information</p>
              
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-amber-700">{{ stats.total_invoices }} Total</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-orange-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-orange-700">Secure Billing</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <Link
              href="/client/messages"
              class="bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
              <span class="font-semibold">Contact Support</span>
            </Link>
            
            <button
              @click="router.visit('/client/dashboard')"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
              </svg>
              <span class="font-semibold">Back to Dashboard</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <div class="space-y-8">
      <!-- Enhanced Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all duration-200 hover:shadow-2xl hover:scale-105">
          <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-blue-100 text-sm font-medium">Total Invoices</p>
                <p class="text-white text-2xl font-bold">{{ stats.total_invoices }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all duration-200 hover:shadow-2xl hover:scale-105">
          <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-green-100 text-sm font-medium">Paid Amount</p>
                <p class="text-white text-2xl font-bold">${{ formatCurrency(stats.total_paid_amount) }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all duration-200 hover:shadow-2xl hover:scale-105">
          <div class="bg-gradient-to-r from-yellow-500 to-orange-600 p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-yellow-100 text-sm font-medium">Pending Amount</p>
                <p class="text-white text-2xl font-bold">${{ formatCurrency(stats.total_pending_amount) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Filters -->
      <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xl font-bold text-gray-900">Filter & Search</h3>
              <p class="text-sm text-gray-600 mt-1">Find specific invoices quickly</p>
            </div>
            <div class="flex items-center space-x-2">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
              </svg>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-gray-700">Search Invoices</label>
              <div class="relative">
                <input
                  v-model="searchQuery"
                  @input="applyFilters"
                  type="text"
                  placeholder="Invoice number, title..."
                  class="w-full px-4 py-3 pl-10 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200"
                />
                <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-semibold text-gray-700">Status</label>
              <select
                v-model="statusFilter"
                @change="applyFilters"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 bg-white"
              >
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="sent">Sent</option>
                <option value="paid">Paid</option>
              </select>
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-semibold text-gray-700">Year</label>
              <select
                v-model="yearFilter"
                @change="applyFilters"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 bg-white"
              >
                <option value="">All Years</option>
                <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
              </select>
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-semibold text-gray-700">Actions</label>
              <button
                @click="clearFilters"
                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md"
              >
                Clear Filters
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Main Content -->
      <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-bold text-gray-900">Invoice History</h2>
              <p class="text-sm text-gray-600 mt-1">Complete list of your invoices and payments</p>
            </div>
            <div class="flex items-center space-x-2">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
            </div>
          </div>
        </div>
        <!-- Enhanced Invoices List -->
        <div v-if="invoices.data && invoices.data.length > 0" class="divide-y divide-gray-100"></div>
          <div
            v-for="invoice in invoices.data"
            :key="invoice.id"
            class="p-6 hover:bg-gradient-to-r hover:from-gray-50 hover:to-slate-50 transition-all duration-200 group"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg ring-2 ring-amber-100 group-hover:ring-amber-200 transition-all duration-200">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div>
                  <div class="flex items-center space-x-3 mb-2">
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-amber-700 transition-colors duration-200">
                      {{ invoice.invoice_number }}
                    </h3>
                    <span
                      :class="getEnhancedStatusClass(invoice.status)"
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm"
                    >
                      {{ getStatusText(invoice.status).toUpperCase() }}
                    </span>
                  </div>
                  <p class="text-base font-medium text-gray-700 mb-1">{{ invoice.title }}</p>
                  <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      <span class="font-medium">{{ formatDate(invoice.created_at) }}</span>
                    </div>
                    <div class="flex items-center space-x-2"></div>
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                      </svg>
                      <span class="font-bold text-lg text-gray-900">${{ formatCurrency(invoice.total_amount) }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-3">
                <Link
                  :href="route('client.invoices.show', invoice.id)"
                  class="bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                >
                  View Details
                </Link>
                <a
                  :href="route('client.invoices.download', invoice.id)"
                  class="bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                >
                  Download PDF
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Empty State -->
        <div v-if="!invoices.data || invoices.data.length === 0" class="p-12 text-center"></div>
          <div class="w-20 h-20 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">No invoices yet</h3>
          <p class="text-gray-600 mb-4">Your invoices will appear here once created by your tax professional.</p>
          <Link
            href="/client/messages"
            class="bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
          >
            Contact Support
          </Link>

          <!-- Pagination -->
          <div v-if="invoices.data && invoices.data.length > 0" class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ invoices.from }} to {{ invoices.to }} of {{ invoices.total }} results
              </div>
              <div class="flex space-x-2">
                <Link
                  v-if="invoices.prev_page_url"
                  :href="invoices.prev_page_url"
                  class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Previous
                </Link>
                <Link
                  v-if="invoices.next_page_url"
                  :href="invoices.next_page_url"
                  class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Next
                </Link>
              </div>
            </div>
          </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Use the global route function
const route = window.route

const props = defineProps({
  invoices: Object,
  stats: Object,
  availableYears: Array,
  filters: Object,
})

const searchQuery = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || '')
const yearFilter = ref(props.filters.year || '')

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount || 0)
}

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    sent: 'bg-blue-100 text-blue-800',
    paid: 'bg-green-100 text-green-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getEnhancedStatusClass = (status) => {
  const classes = {
    draft: 'bg-gradient-to-r from-gray-400 to-gray-500 text-white',
    sent: 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white',
    paid: 'bg-gradient-to-r from-green-500 to-emerald-500 text-white',
  }
  return classes[status] || 'bg-gradient-to-r from-gray-400 to-gray-500 text-white'
}

const getStatusText = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1)
}

const applyFilters = () => {
  router.get(route('client.invoices'), {
    search: searchQuery.value,
    status: statusFilter.value,
    year: yearFilter.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = ''
  yearFilter.value = ''
  router.get(route('client.invoices'))
}
</script>
