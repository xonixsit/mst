<template>
  <AppLayout title="Invoice Management">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Total Invoices</div>
            <div class="text-2xl font-bold text-gray-900">{{ stats.total_invoices }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Paid Invoices</div>
            <div class="text-2xl font-bold text-blue-600">{{ stats.paid_invoices }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Pending Invoices</div>
            <div class="text-2xl font-bold text-pink-600">{{ stats.pending_invoices }}</div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-sm font-medium text-gray-500">Total Revenue</div>
            <div class="text-2xl font-bold text-green-600">${{ formatCurrency(stats.total_revenue) }}</div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-semibold text-gray-900">Invoices</h2>
              <Link
                href="/admin/invoices/create"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
              >
                Create Invoice
              </Link>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                  v-model="filters.search"
                  type="text"
                  placeholder="Search invoices..."
                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  @input="debouncedSearch"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                <select
                  v-model="filters.client_id"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  @change="applyFilters"
                >
                  <option value="">All Clients</option>
                  <option v-for="client in clients" :key="client.id" :value="client.id">
                    {{ client.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select
                  v-model="filters.status"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  @change="applyFilters"
                >
                  <option value="">All Status</option>
                  <option value="draft">Draft</option>
                  <option value="sent">Sent</option>
                  <option value="paid">Paid</option>
                  <option value="overdue">Overdue</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input
                  v-model="filters.date_from"
                  type="date"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  @change="applyFilters"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input
                  v-model="filters.date_to"
                  type="date"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  @change="applyFilters"
                />
              </div>
            </div>
          </div>

          <!-- Invoice Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Invoice #
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Client
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Title
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Amount
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ invoice.invoice_number }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ invoice.client?.user?.first_name && invoice.client?.user?.last_name 
                        ? `${invoice.client.user.first_name} ${invoice.client.user.last_name}`.trim() 
                        : 'Unknown Client' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ invoice.title }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${{ formatCurrency(invoice.total_amount) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(invoice.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                      {{ getStatusLabel(invoice.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDate(invoice.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    <Link
                      :href="`/admin/invoices/${invoice.id}`"
                      class="text-gray-600 hover:text-gray-900"
                    >
                      View
                    </Link>
                    <Link
                      :href="`/admin/invoices/${invoice.id}/edit`"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Edit
                    </Link>
                    <button
                      v-if="invoice.status !== 'paid'"
                      @click="markAsPaid(invoice)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Mark Paid
                    </button>
                    <button
                      v-if="invoice.status === 'draft'"
                      @click="sendEmail(invoice)"
                      class="text-orange-600 hover:text-orange-900"
                    >
                      Send
                    </button>
                    <button
                      @click="deleteInvoice(invoice)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ invoices.from }} to {{ invoices.to }} of {{ invoices.total }} results
              </div>
              <div class="flex space-x-2">
                <Link
                  v-for="link in invoices.links"
                  :key="link.label"
                  :href="link.url"
                  :class="[
                    'px-3 py-2 text-sm rounded-md',
                    link.active
                      ? 'bg-blue-600 text-white'
                      : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                  ]"
                  v-html="link.label"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
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
    draft: 'bg-gray-100 text-gray-800',
    sent: 'bg-blue-100 text-blue-800',
    paid: 'bg-green-100 text-green-800',
    overdue: 'bg-red-100 text-red-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
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

const markAsPaid = (invoice) => {
  if (confirm('Mark this invoice as paid?')) {
    router.post(`/admin/invoices/${invoice.id}/mark-paid`)
  }
}

const sendEmail = (invoice) => {
  if (confirm('Send this invoice via email?')) {
    router.post(`/admin/invoices/${invoice.id}/send-email`)
  }
}

const deleteInvoice = (invoice) => {
  if (confirm('Are you sure you want to delete this invoice?')) {
    router.delete(`/admin/invoices/${invoice.id}`)
  }
}
</script>