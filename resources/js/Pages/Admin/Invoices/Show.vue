<template>
  <AppLayout title="Invoice Details">
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
                Invoice {{ invoice.invoice_number }}
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">{{ invoice.title }}</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div :class="getStatusDotClass(invoice.status)" class="w-2 h-2 rounded-full"></div>
                  <span :class="getStatusTextClass(invoice.status)" class="text-xs font-semibold">{{ getStatusLabel(invoice.status) }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-green-700">${{ formatCurrency(invoice.total_amount) }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex items-center space-x-3">
            <Link
              :href="`/admin/invoices/${invoice.id}/edit`"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <PencilIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">Edit Invoice</span>
            </Link>
            <Link
              href="/admin/invoices"
              class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <ArrowLeftIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">Back to List</span>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-8 py-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-gray-900">Invoice Details</h3>
                <p class="text-sm text-gray-600 mt-1">Complete invoice information and billing details</p>
              </div>
              <div class="flex items-center space-x-2">
                <DocumentTextIcon class="w-5 h-5 text-gray-400" />
              </div>
            </div>
          </div>

          <div class="p-8">
            <!-- Enhanced Invoice Header -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
              <!-- Invoice Information Card -->
              <div class="bg-gradient-to-br from-emerald-50 to-green-100 rounded-xl p-6 border border-emerald-200">
                <div class="flex items-center mb-4">
                  <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
                    <DocumentTextIcon class="w-5 h-5 text-white" />
                  </div>
                  <h3 class="text-lg font-bold text-emerald-900">Invoice Information</h3>
                </div>
                <dl class="space-y-3">
                  <div class="flex items-center justify-between py-2 border-b border-emerald-200/50">
                    <dt class="text-sm font-semibold text-emerald-700">Invoice #:</dt>
                    <dd class="text-sm font-bold text-emerald-900">{{ invoice.invoice_number }}</dd>
                  </div>
                  <div class="flex items-center justify-between py-2 border-b border-emerald-200/50">
                    <dt class="text-sm font-semibold text-emerald-700">Title:</dt>
                    <dd class="text-sm font-medium text-emerald-900">{{ invoice.title }}</dd>
                  </div>
                  <div class="flex items-center justify-between py-2 border-b border-emerald-200/50">
                    <dt class="text-sm font-semibold text-emerald-700">Year:</dt>
                    <dd class="text-sm font-medium text-emerald-900">{{ invoice.invoice_year }}</dd>
                  </div>
                  <div class="flex items-center justify-between py-2 border-b border-emerald-200/50">
                    <dt class="text-sm font-semibold text-emerald-700">Status:</dt>
                    <dd>
                      <span :class="getStatusClass(invoice.status)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-sm">
                        <div :class="getStatusDotClass(invoice.status)" class="w-1.5 h-1.5 rounded-full mr-2"></div>
                        {{ getStatusLabel(invoice.status) }}
                      </span>
                    </dd>
                  </div>
                  <div class="flex items-center justify-between py-2 border-b border-emerald-200/50">
                    <dt class="text-sm font-semibold text-emerald-700">Created:</dt>
                    <dd class="text-sm font-medium text-emerald-900">{{ formatDate(invoice.created_at) }}</dd>
                  </div>
                  <div v-if="invoice.sent_at" class="flex items-center justify-between py-2 border-b border-emerald-200/50">
                    <dt class="text-sm font-semibold text-emerald-700">Sent:</dt>
                    <dd class="text-sm font-medium text-emerald-900">{{ formatDate(invoice.sent_at) }}</dd>
                  </div>
                  <div v-if="invoice.paid_at" class="flex items-center justify-between py-2">
                    <dt class="text-sm font-semibold text-emerald-700">Paid:</dt>
                    <dd class="text-sm font-medium text-emerald-900">{{ formatDate(invoice.paid_at) }}</dd>
                  </div>
                </dl>
              </div>

              <!-- Client Information Card -->
              <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200">
                <div class="flex items-center mb-4">
                  <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <UserIcon class="w-5 h-5 text-white" />
                  </div>
                  <h3 class="text-lg font-bold text-blue-900">Client Information</h3>
                </div>
                <dl class="space-y-3">
                  <div class="flex items-center justify-between py-2 border-b border-blue-200/50">
                    <dt class="text-sm font-semibold text-blue-700">Name:</dt>
                    <dd class="text-sm font-bold text-blue-900">
                      {{ invoice.client?.user?.first_name && invoice.client?.user?.last_name 
                          ? `${invoice.client.user.first_name} ${invoice.client.user.last_name}`.trim() 
                          : 'Unknown Client' }}
                    </dd>
                  </div>
                  <div class="flex items-center justify-between py-2">
                    <dt class="text-sm font-semibold text-blue-700">Email:</dt>
                    <dd class="text-sm font-medium text-blue-900">{{ invoice.send_to_email }}</dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Comments Section -->
            <div v-if="invoice.comments" class="mb-8">
              <div class="bg-gradient-to-br from-amber-50 to-yellow-100 rounded-xl p-6 border border-amber-200">
                <div class="flex items-center mb-4">
                  <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center mr-3">
                    <ChatBubbleLeftEllipsisIcon class="w-5 h-5 text-white" />
                  </div>
                  <h3 class="text-lg font-bold text-amber-900">Comments</h3>
                </div>
                <p class="text-sm text-amber-800 leading-relaxed font-medium">{{ invoice.comments }}</p>
              </div>
            </div>

            <!-- Enhanced Invoice Items -->
            <div class="mb-8">
              <div class="bg-white rounded-xl border border-gray-200 shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-4 border-b border-gray-200">
                  <div class="flex items-center">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
                      <ListBulletIcon class="w-4 h-4 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Invoice Items</h3>
                  </div>
                </div>
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gradient-to-r from-gray-50 to-slate-50">
                      <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                          Service
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                          Quantity
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                          Unit Price
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                          Total
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                      <tr v-for="item in invoice.items" :key="item.id" class="hover:bg-gradient-to-r hover:from-emerald-50 hover:to-green-50 transition-all duration-200">
                        <td class="px-6 py-5 whitespace-nowrap">
                          <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-emerald-100 to-green-200 rounded-lg flex items-center justify-center mr-3">
                              <CogIcon class="w-4 h-4 text-emerald-600" />
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ item.service_name }}</span>
                          </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                          <span class="text-sm font-medium text-gray-900">{{ item.quantity }}</span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                          <span class="text-sm font-medium text-gray-900">${{ formatCurrency(item.unit_price) }}</span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                          <span class="text-sm font-bold text-emerald-600">${{ formatCurrency(item.total_price) }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Enhanced Invoice Totals -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 border border-green-200">
              <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                  <CurrencyDollarIcon class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-lg font-bold text-green-900">Invoice Summary</h3>
              </div>
              <div class="max-w-md ml-auto space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-green-200/50">
                  <span class="text-sm font-semibold text-green-700">Subtotal:</span>
                  <span class="text-sm font-bold text-green-900">${{ formatCurrency(invoice.subtotal) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-green-200/50">
                  <span class="text-sm font-semibold text-green-700">Tax ({{ invoice.tax_rate }}%):</span>
                  <span class="text-sm font-bold text-green-900">${{ formatCurrency(invoice.tax_amount) }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-t-2 border-green-300">
                  <span class="text-lg font-bold text-green-900">Total Amount:</span>
                  <span class="text-xl font-bold text-green-600">${{ formatCurrency(invoice.total_amount) }}</span>
                </div>
              </div>
            </div>

            <!-- Enhanced Action Buttons -->
            <div class="flex flex-wrap justify-end gap-3 pt-8 border-t border-gray-200 mt-8">
              <button
                v-if="invoice.status !== 'paid'"
                @click="markAsPaid"
                class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
              >
                <CheckCircleIcon class="w-5 h-5 mr-2" />
                <span class="font-semibold">Mark as Paid</span>
              </button>
              <button
                v-if="invoice.status === 'draft'"
                @click="sendEmail"
                class="bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
              >
                <PaperAirplaneIcon class="w-5 h-5 mr-2" />
                <span class="font-semibold">Send Email</span>
              </button>
              <button
                @click="deleteInvoice"
                class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
              >
                <TrashIcon class="w-5 h-5 mr-2" />
                <span class="font-semibold">Delete Invoice</span>
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
import { 
  DocumentTextIcon,
  UserIcon,
  ChatBubbleLeftEllipsisIcon,
  ListBulletIcon,
  CogIcon,
  CurrencyDollarIcon,
  CheckCircleIcon,
  PaperAirplaneIcon,
  TrashIcon,
  PencilIcon,
  ArrowLeftIcon
} from '@heroicons/vue/24/outline'
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

const getStatusTextClass = (status) => {
  const classes = {
    draft: 'text-gray-700',
    sent: 'text-blue-700',
    paid: 'text-green-700',
    overdue: 'text-red-700',
  }
  return classes[status] || 'text-gray-700'
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