<template>
  <AppLayout title="Invoice Details">
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h2 class="text-xl font-semibold text-gray-900">Invoice Details</h2>
              <div class="flex space-x-2">
                <Link
                  :href="`/admin/invoices/${invoice.id}/edit`"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm"
                >
                  Edit
                </Link>
                <Link
                  href="/admin/invoices"
                  class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm"
                >
                  Back to List
                </Link>
              </div>
            </div>
          </div>

          <div class="p-6">
            <!-- Invoice Header -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Invoice Information</h3>
                <dl class="space-y-2">
                  <div class="flex">
                    <dt class="w-32 text-sm font-medium text-gray-500">Invoice #:</dt>
                    <dd class="text-sm text-gray-900">{{ invoice.invoice_number }}</dd>
                  </div>
                  <div class="flex">
                    <dt class="w-32 text-sm font-medium text-gray-500">Title:</dt>
                    <dd class="text-sm text-gray-900">{{ invoice.title }}</dd>
                  </div>
                  <div class="flex">
                    <dt class="w-32 text-sm font-medium text-gray-500">Year:</dt>
                    <dd class="text-sm text-gray-900">{{ invoice.invoice_year }}</dd>
                  </div>
                  <div class="flex">
                    <dt class="w-32 text-sm font-medium text-gray-500">Status:</dt>
                    <dd>
                      <span :class="getStatusClass(invoice.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                        {{ getStatusLabel(invoice.status) }}
                      </span>
                    </dd>
                  </div>
                  <div class="flex">
                    <dt class="w-32 text-sm font-medium text-gray-500">Created:</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(invoice.created_at) }}</dd>
                  </div>
                  <div v-if="invoice.sent_at" class="flex">
                    <dt class="w-32 text-sm font-medium text-gray-500">Sent:</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(invoice.sent_at) }}</dd>
                  </div>
                  <div v-if="invoice.paid_at" class="flex">
                    <dt class="w-32 text-sm font-medium text-gray-500">Paid:</dt>
                    <dd class="text-sm text-gray-900">{{ formatDate(invoice.paid_at) }}</dd>
                  </div>
                </dl>
              </div>

              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Client Information</h3>
                <dl class="space-y-2">
                  <div class="flex">
                    <dt class="w-32 text-sm font-medium text-gray-500">Name:</dt>
                    <dd class="text-sm text-gray-900">
                      {{ invoice.client?.user?.first_name && invoice.client?.user?.last_name 
                          ? `${invoice.client.user.first_name} ${invoice.client.user.last_name}`.trim() 
                          : 'Unknown Client' }}
                    </dd>
                  </div>
                  <div class="flex">
                    <dt class="w-32 text-sm font-medium text-gray-500">Email:</dt>
                    <dd class="text-sm text-gray-900">{{ invoice.send_to_email }}</dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Comments -->
            <div v-if="invoice.comments" class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-2">Comments</h3>
              <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-md">{{ invoice.comments }}</p>
            </div>

            <!-- Invoice Items -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Invoice Items</h3>
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Service
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Quantity
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Unit Price
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in invoice.items" :key="item.id">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ item.service_name }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ item.quantity }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${{ formatCurrency(item.unit_price) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${{ formatCurrency(item.total_price) }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Invoice Totals -->
            <div class="border-t pt-6">
              <div class="max-w-md ml-auto space-y-2">
                <div class="flex justify-between">
                  <span class="text-gray-600">Subtotal:</span>
                  <span class="font-medium">${{ formatCurrency(invoice.subtotal) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Tax ({{ invoice.tax_rate }}%):</span>
                  <span class="font-medium">${{ formatCurrency(invoice.tax_amount) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-2">
                  <span>Total:</span>
                  <span>${{ formatCurrency(invoice.total_amount) }}</span>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t mt-6">
              <button
                v-if="invoice.status !== 'paid'"
                @click="markAsPaid"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm"
              >
                Mark as Paid
              </button>
              <button
                v-if="invoice.status === 'draft'"
                @click="sendEmail"
                class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-sm"
              >
                Send Email
              </button>
              <button
                @click="deleteInvoice"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm"
              >
                Delete Invoice
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  invoice: Object,
})

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

const markAsPaid = () => {
  if (confirm('Mark this invoice as paid?')) {
    router.post(`/admin/invoices/${props.invoice.id}/mark-paid`)
  }
}

const sendEmail = () => {
  if (confirm('Send this invoice via email?')) {
    router.post(`/admin/invoices/${props.invoice.id}/send-email`)
  }
}

const deleteInvoice = () => {
  if (confirm('Are you sure you want to delete this invoice?')) {
    router.delete(`/admin/invoices/${props.invoice.id}`)
  }
}
</script>