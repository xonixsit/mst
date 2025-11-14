<template>
  <AppLayout title="Invoice Details">
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
          <!-- Header -->
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
              <div>
                <h2 class="text-2xl font-bold text-gray-900">Invoice {{ invoice.invoice_number }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ formatDate(invoice.created_at) }}</p>
              </div>
              <div class="flex space-x-3">
                <Link
                  :href="route('client.invoices')"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Back to Invoices
                </Link>
                <a
                  :href="route('client.invoices.download', invoice.id)"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                  </svg>
                  Download PDF
                </a>
              </div>
            </div>
          </div>

          <!-- Invoice Content -->
          <div class="p-6">
            <!-- Status Badge -->
            <div class="mb-6">
              <span :class="getStatusClass(invoice.status)" class="px-3 py-1 text-sm font-semibold rounded-full">
                {{ getStatusText(invoice.status) }}
              </span>
              <span v-if="invoice.paid_at" class="ml-2 text-sm text-gray-600">
                Paid on {{ formatDate(invoice.paid_at) }}
              </span>
            </div>

            <!-- Client Information -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Bill To:</h3>
              <div class="text-sm text-gray-700">
                <p class="font-medium">{{ invoice.client.first_name }} {{ invoice.client.last_name }}</p>
                <p>{{ invoice.client.email }}</p>
                <p v-if="invoice.client.phone">{{ invoice.client.phone }}</p>
                <div v-if="invoice.client.street_no" class="mt-2">
                  <p>{{ invoice.client.street_no }} {{ invoice.client.apartment_no }}</p>
                  <p>{{ invoice.client.city }}, {{ invoice.client.state }} {{ invoice.client.zip_code }}</p>
                </div>
              </div>
            </div>

            <!-- Invoice Details -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Invoice Details:</h3>
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <p class="text-gray-600">Invoice Number:</p>
                  <p class="font-medium text-gray-900">{{ invoice.invoice_number }}</p>
                </div>
                <div>
                  <p class="text-gray-600">Invoice Year:</p>
                  <p class="font-medium text-gray-900">{{ invoice.invoice_year || 'N/A' }}</p>
                </div>
                <div>
                  <p class="text-gray-600">Issue Date:</p>
                  <p class="font-medium text-gray-900">{{ formatDate(invoice.created_at) }}</p>
                </div>
                <div v-if="invoice.sent_at">
                  <p class="text-gray-600">Sent Date:</p>
                  <p class="font-medium text-gray-900">{{ formatDate(invoice.sent_at) }}</p>
                </div>
              </div>
            </div>

            <!-- Invoice Title -->
            <div v-if="invoice.title" class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ invoice.title }}</h3>
            </div>

            <!-- Comments -->
            <div v-if="invoice.comments" class="mb-6 bg-gray-50 p-4 rounded-md">
              <h4 class="text-sm font-medium text-gray-900 mb-2">Notes:</h4>
              <p class="text-sm text-gray-700">{{ invoice.comments }}</p>
            </div>

            <!-- Invoice Items -->
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Services:</h3>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                      <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                      <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                      <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in invoice.items" :key="item.id">
                      <td class="px-4 py-3 text-sm text-gray-900">{{ item.service_name }}</td>
                      <td class="px-4 py-3 text-sm text-right text-gray-900">{{ item.quantity }}</td>
                      <td class="px-4 py-3 text-sm text-right text-gray-900">${{ formatCurrency(item.unit_price) }}</td>
                      <td class="px-4 py-3 text-sm font-medium text-right text-gray-900">${{ formatCurrency(item.total_price) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Totals -->
            <div class="border-t border-gray-200 pt-4">
              <div class="flex justify-end">
                <div class="w-64 space-y-2">
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium text-gray-900">${{ formatCurrency(invoice.subtotal) }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tax ({{ invoice.tax_rate }}%):</span>
                    <span class="font-medium text-gray-900">${{ formatCurrency(invoice.tax_amount) }}</span>
                  </div>
                  <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                    <span class="text-gray-900">Total:</span>
                    <span class="text-blue-600">${{ formatCurrency(invoice.total_amount) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment Status Message -->
            <div v-if="invoice.status === 'paid'" class="mt-6 bg-green-50 border border-green-200 rounded-md p-4">
              <div class="flex">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-green-800">Payment Received</h3>
                  <p class="mt-1 text-sm text-green-700">This invoice has been paid. Thank you for your payment!</p>
                </div>
              </div>
            </div>

            <div v-else-if="invoice.status === 'sent'" class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
              <div class="flex">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-blue-800">Payment Pending</h3>
                  <p class="mt-1 text-sm text-blue-700">This invoice is awaiting payment. Please contact your tax professional for payment options.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  invoice: Object,
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
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

const getStatusText = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1)
}
</script>
