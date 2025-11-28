<template>
  <AppLayout title="Invoice Management">
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-emerald-50 to-green-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-emerald-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-green-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Invoice Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-emerald-600 to-green-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-emerald-100">
              <DocumentTextIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-emerald-900 to-green-900 bg-clip-text text-transparent">
                Invoice Management
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Create, track, and manage client invoices</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-emerald-700">{{ stats.total_invoices }} Total Invoices</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-green-700">${{ formatCurrency(stats.total_revenue) }} Revenue</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Button -->
          <div class="flex items-center">
            <Link
              href="/admin/invoices/create"
              class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              <span class="font-semibold">Create Invoice</span>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Enhanced Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Invoices -->
          <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-emerald-100 to-emerald-200 rounded-xl shadow-lg border border-emerald-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-emerald-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-emerald-700 uppercase tracking-wide">Total Invoices</p>
                  <p class="text-3xl font-bold text-emerald-900 mt-2">{{ stats.total_invoices }}</p>
                </div>
                <div class="p-4 bg-emerald-500 rounded-xl shadow-lg group-hover:bg-emerald-600 transition-colors duration-300">
                  <DocumentTextIcon class="w-8 h-8 text-white" />
                </div>
              </div>
            </div>
          </div>

          <!-- Paid Invoices -->
          <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 rounded-xl shadow-lg border border-blue-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-blue-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Paid Invoices</p>
                  <p class="text-3xl font-bold text-blue-900 mt-2">{{ stats.paid_invoices }}</p>
                </div>
                <div class="p-4 bg-blue-500 rounded-xl shadow-lg group-hover:bg-blue-600 transition-colors duration-300">
                  <CheckCircleIcon class="w-8 h-8 text-white" />
                </div>
              </div>
            </div>
          </div>

          <!-- Pending Invoices -->
          <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 via-amber-100 to-amber-200 rounded-xl shadow-lg border border-amber-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-amber-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-amber-700 uppercase tracking-wide">Pending Invoices</p>
                  <p class="text-3xl font-bold text-amber-900 mt-2">{{ stats.pending_invoices }}</p>
                </div>
                <div class="p-4 bg-amber-500 rounded-xl shadow-lg group-hover:bg-amber-600 transition-colors duration-300">
                  <ClockIcon class="w-8 h-8 text-white" />
                </div>
              </div>
            </div>
          </div>

          <!-- Total Revenue -->
          <div class="relative overflow-hidden bg-gradient-to-br from-green-50 via-green-100 to-green-200 rounded-xl shadow-lg border border-green-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-green-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-green-700 uppercase tracking-wide">Total Revenue</p>
                  <p class="text-3xl font-bold text-green-900 mt-2">${{ formatCurrency(stats.total_revenue) }}</p>
                </div>
                <div class="p-4 bg-green-500 rounded-xl shadow-lg group-hover:bg-green-600 transition-colors duration-300">
                  <CurrencyDollarIcon class="w-8 h-8 text-white" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Filters -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden mb-8">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-gray-900">Search & Filter Invoices</h3>
                <p class="text-sm text-gray-600 mt-1">Find and manage your invoice records</p>
              </div>
              <div class="flex items-center space-x-2">
                <DocumentTextIcon class="w-5 h-5 text-gray-400" />
              </div>
            </div>
          </div>
          
          <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
              <!-- Search -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Search Invoices</label>
                <div class="relative">
                  <input
                    v-model="filters.search"
                    @input="debouncedSearch"
                    type="text"
                    placeholder="Invoice #, client, title..."
                    class="w-full px-4 py-3 pl-10 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                  />
                  <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </div>
              </div>

              <!-- Client Filter -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Client</label>
                <select 
                  v-model="filters.client_id" 
                  @change="applyFilters"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white"
                >
                  <option value="">All Clients</option>
                  <option v-for="client in clients" :key="client.id" :value="client.id">
                    {{ client.name }}
                  </option>
                </select>
              </div>

              <!-- Status Filter -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Status</label>
                <select 
                  v-model="filters.status" 
                  @change="applyFilters"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white"
                >
                  <option value="">All Status</option>
                  <option value="draft">Draft</option>
                  <option value="sent">Sent</option>
                  <option value="paid">Paid</option>
                  <option value="overdue">Overdue</option>
                </select>
              </div>

              <!-- From Date Filter -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">From Date</label>
                <input
                  v-model="filters.date_from"
                  @change="applyFilters"
                  type="date"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                />
              </div>

              <!-- To Date Filter -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">To Date</label>
                <input
                  v-model="filters.date_to"
                  @change="applyFilters"
                  type="date"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Invoice Table -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-gray-900">Invoice Records</h3>
                <p class="text-sm text-gray-600 mt-1">{{ invoices.total }} invoices in your system</p>
              </div>
              <div class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-sm font-semibold text-emerald-600">{{ invoices.total }} Total</span>
              </div>
            </div>
          </div>

          <div v-if="invoices.data.length === 0" class="p-12 text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-emerald-100 to-green-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
              <DocumentTextIcon class="w-8 h-8 text-emerald-600" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No invoices found</h3>
            <p class="text-gray-500">No invoices match your current filters or you have no invoices yet.</p>
          </div>

          <!-- Enhanced Invoice Table -->
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
              <thead class="bg-gradient-to-r from-gray-50 to-slate-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Invoice #
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Client
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Title
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Amount
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Date
                  </th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-50">
                <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-gradient-to-r hover:from-emerald-50 hover:to-green-50 transition-all duration-200 group">
                  <td class="px-6 py-5 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-gradient-to-br from-emerald-100 to-green-200 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                        <DocumentTextIcon class="w-5 h-5 text-emerald-600" />
                      </div>
                      <span class="text-sm font-bold text-gray-900">{{ invoice.invoice_number }}</span>
                    </div>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <div class="text-sm font-semibold text-gray-900">
                      {{ getClientName(invoice) }}
                    </div>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ invoice.title }}</div>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <div class="text-sm font-bold text-green-600">${{ formatCurrency(invoice.total_amount) }}</div>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <span :class="getStatusClass(invoice.status)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-sm">
                      <div :class="getStatusDotClass(invoice.status)" class="w-1.5 h-1.5 rounded-full mr-2"></div>
                      {{ getStatusLabel(invoice.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <div class="flex items-center text-sm text-gray-600">
                      <ClockIcon class="w-4 h-4 mr-2" />
                      {{ formatDate(invoice.created_at) }}
                    </div>
                  </td>
                  <td class="px-6 py-5 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                      <Link
                        :href="`/admin/invoices/${invoice.id}`"
                        class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center"
                      >
                        <EyeIcon class="w-3 h-3 mr-1" />
                        View
                      </Link>
                      <Link
                        :href="`/admin/invoices/${invoice.id}/edit`"
                        class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center"
                      >
                        <PencilIcon class="w-3 h-3 mr-1" />
                        Edit
                      </Link>
                      <!-- Send Email - Only for draft invoices -->
                      <button
                        v-if="invoice.status === 'draft'"
                        @click="sendEmail(invoice)"
                        class="bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center"
                      >
                        <PaperAirplaneIcon class="w-3 h-3 mr-1" />
                        Send
                      </button>
                      
                      <!-- Resend Email - Only for sent invoices -->
                      <button
                        v-if="invoice.status === 'sent'"
                        @click="sendEmail(invoice)"
                        class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center"
                      >
                        <PaperAirplaneIcon class="w-3 h-3 mr-1" />
                        Resend
                      </button>
                      
                      <!-- Mark as Paid - Only for sent invoices -->
                      <button
                        v-if="invoice.status === 'sent'"
                        @click="markAsPaid(invoice)"
                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center"
                      >
                        <CheckCircleIcon class="w-3 h-3 mr-1" />
                        Mark Paid
                      </button>
                      <button
                        @click="deleteInvoice(invoice)"
                        class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center"
                      >
                        <TrashIcon class="w-3 h-3 mr-1" />
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Enhanced Pagination -->
          <div v-if="invoices.last_page > 1" class="px-6 py-5 border-t border-gray-100 bg-gradient-to-r from-gray-50 to-slate-50">
            <div class="flex items-center justify-between">
              <div class="text-sm font-medium text-gray-700">
                Showing <span class="font-bold text-emerald-600">{{ invoices.from }}</span> to <span class="font-bold text-emerald-600">{{ invoices.to }}</span> of <span class="font-bold text-emerald-600">{{ invoices.total }}</span> invoices
              </div>
              <div class="flex items-center space-x-2">
                <Link
                  v-if="invoices.prev_page_url"
                  :href="invoices.prev_page_url"
                  class="bg-white hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 px-4 py-2 rounded-lg text-sm font-semibold border border-gray-200 hover:border-emerald-200 transition-all duration-200 shadow-sm hover:shadow-md"
                >
                  Previous
                </Link>
                <div class="flex space-x-1">
                  <Link
                    v-for="link in invoices.links.slice(1, -1)"
                    :key="link.label"
                    :href="link.url"
                    :class="[
                      'px-3 py-2 text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm',
                      link.active
                        ? 'bg-gradient-to-r from-emerald-500 to-green-600 text-white shadow-md'
                        : 'bg-white text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 border border-gray-200 hover:border-emerald-200 hover:shadow-md'
                    ]"
                    v-html="link.label"
                  />
                </div>
                <Link
                  v-if="invoices.next_page_url"
                  :href="invoices.next_page_url"
                  class="bg-white hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 px-4 py-2 rounded-lg text-sm font-semibold border border-gray-200 hover:border-emerald-200 transition-all duration-200 shadow-sm hover:shadow-md"
                >
                  Next
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Invoice Email Modal -->
    <InvoiceEmailModal
      v-if="showEmailModal && selectedInvoiceForEmail"
      :invoice="selectedInvoiceForEmail"
      @close="showEmailModal = false; selectedInvoiceForEmail = null"
      @sent="onEmailSent"
    />

    <!-- Payment Modal -->
    <PaymentModal
      v-if="showPaymentModal && selectedInvoice"
      :invoice="selectedInvoice"
      @close="showPaymentModal = false; selectedInvoice = null"
      @paid="onPaymentRecorded"
    />
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { 
  DocumentTextIcon,
  CheckCircleIcon,
  ClockIcon,
  CurrencyDollarIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  PaperAirplaneIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'
import InvoiceEmailModal from '@/Components/InvoiceEmailModal.vue'
import PaymentModal from '@/Components/PaymentModal.vue'
import { debounce } from 'lodash'

const props = defineProps({
  invoices: Object,
  clients: Array,
  stats: Object,
  filters: Object,
})

const filters = reactive({
  search: props.filters.search || '',
  client_id: props.filters.client_id || '',
  status: props.filters.status || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
})

const debouncedSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  router.get('/admin/invoices', filters, {
    preserveState: true,
    replace: true,
  })
}

const formatCurrency = (amount) => {
  return parseFloat(amount).toFixed(2)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800 border-gray-200',
    sent: 'bg-blue-100 text-blue-800 border-blue-200',
    paid: 'bg-green-100 text-green-800 border-green-200',
    overdue: 'bg-red-100 text-red-800 border-red-200',
  }
  return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200'
}

const getStatusDotClass = (status) => {
  const classes = {
    draft: 'bg-gray-500',
    sent: 'bg-blue-500',
    paid: 'bg-green-500',
    overdue: 'bg-red-500',
  }
  return classes[status] || 'bg-gray-500'
}

const getStatusLabel = (status) => {
  const labels = {
    draft: 'Draft',
    sent: 'Sent',
    paid: 'Paid',
    overdue: 'Overdue',
  }
  return labels[status] || status
}

const showPaymentModal = ref(false)
const selectedInvoice = ref(null)

const markAsPaid = (invoice) => {
  selectedInvoice.value = invoice
  showPaymentModal.value = true
}

const onPaymentRecorded = () => {
  // Refresh the page to show updated status
  router.reload()
  selectedInvoice.value = null
}

const showEmailModal = ref(false)
const selectedInvoiceForEmail = ref(null)

const sendEmail = (invoice) => {
  selectedInvoiceForEmail.value = invoice
  showEmailModal.value = true
}

const onEmailSent = () => {
  showEmailModal.value = false
  selectedInvoiceForEmail.value = null
  // Refresh the page to show updated status
  router.reload()
}

const deleteInvoice = (invoice) => {
  if (confirm('Are you sure you want to delete this invoice?')) {
    router.delete(`/admin/invoices/${invoice.id}`)
  }
}

const getClientName = (invoice) => {
  // Try different possible client data structures
  if (invoice.client?.user?.first_name && invoice.client?.user?.last_name) {
    return `${invoice.client.user.first_name} ${invoice.client.user.last_name}`.trim()
  }
  if (invoice.client?.first_name && invoice.client?.last_name) {
    return `${invoice.client.first_name} ${invoice.client.last_name}`.trim()
  }
  if (invoice.client?.name) {
    return invoice.client.name
  }
  if (invoice.client_name) {
    return invoice.client_name
  }
  return 'Client Name Not Available'
}
</script>