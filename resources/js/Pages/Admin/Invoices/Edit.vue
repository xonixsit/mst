<template>
  <AppLayout title="Edit Invoice">
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-emerald-50 to-green-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-emerald-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-green-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Edit Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-emerald-600 to-green-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-emerald-100">
              <PencilIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-emerald-900 to-green-900 bg-clip-text text-transparent">
                Edit Invoice #{{ invoice.invoice_number }}
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Modify invoice details and billing information</p>
              
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
          
          <!-- Action Button -->
          <div class="flex items-center">
            <Link
              href="/admin/invoices"
              class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <ArrowLeftIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">Back to Invoices</span>
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
                <h3 class="text-xl font-bold text-gray-900">Invoice Form</h3>
                <p class="text-sm text-gray-600 mt-1">Update invoice information and line items</p>
              </div>
              <div class="flex items-center space-x-2">
                <DocumentTextIcon class="w-5 h-5 text-gray-400" />
              </div>
            </div>
          </div>

          <form @submit.prevent="submitForm" class="p-8 space-y-8">
            <!-- Enhanced Basic Information -->
            <div class="bg-gradient-to-br from-emerald-50 to-green-100 rounded-xl p-6 border border-emerald-200">
              <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
                  <DocumentTextIcon class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-lg font-bold text-emerald-900">Basic Information</h3>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-emerald-700">
                    Invoice Assign To *
                  </label>
                  <select
                    v-model="form.client_id"
                    class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.client_id }"
                    required
                  >
                    <option value="">Select Client</option>
                    <option v-for="client in clients" :key="client.id" :value="client.id">
                      {{ client.first_name }} {{ client.last_name }} ({{ client.email }})
                    </option>
                  </select>
                  <div v-if="form.errors.client_id" class="text-red-600 text-sm font-medium">
                    {{ form.errors.client_id }}
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-emerald-700">
                    Send to Email *
                  </label>
                  <input
                    v-model="form.send_to_email"
                    type="email"
                    class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.send_to_email }"
                    required
                  />
                  <div v-if="form.errors.send_to_email" class="text-red-600 text-sm font-medium">
                    {{ form.errors.send_to_email }}
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-emerald-700">
                    Invoice Title *
                  </label>
                  <input
                    v-model="form.title"
                    type="text"
                    class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.title }"
                    required
                  />
                  <div v-if="form.errors.title" class="text-red-600 text-sm font-medium">
                    {{ form.errors.title }}
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-emerald-700">
                    Invoice Year *
                  </label>
                  <select
                    v-model="form.invoice_year"
                    class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.invoice_year }"
                    required
                  >
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                  </select>
                  <div v-if="form.errors.invoice_year" class="text-red-600 text-sm font-medium">
                    {{ form.errors.invoice_year }}
                  </div>
                </div>

                <div class="space-y-2 md:col-span-2">
                  <label class="block text-sm font-semibold text-emerald-700">
                    Status *
                  </label>
                  <select
                    v-model="form.status"
                    class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.status }"
                    required
                  >
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                  </select>
                  <div v-if="form.errors.status" class="text-red-600 text-sm font-medium">
                    {{ form.errors.status }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Comments Section -->
            <div class="bg-gradient-to-br from-amber-50 to-yellow-100 rounded-xl p-6 border border-amber-200">
              <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center mr-3">
                  <ChatBubbleLeftEllipsisIcon class="w-5 h-5 text-white" />
                </div>
                <h3 class="text-lg font-bold text-amber-900">Comments</h3>
              </div>
              <textarea
                v-model="form.comments"
                rows="4"
                class="w-full px-4 py-3 border-2 border-amber-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 resize-none"
                placeholder="Additional comments or notes about this invoice..."
              ></textarea>
            </div>

            <!-- Enhanced Line Items -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-lg overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
                      <ListBulletIcon class="w-4 h-4 text-white" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Invoice Items</h3>
                  </div>
                  <button
                    type="button"
                    @click="addItem"
                    class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center"
                  >
                    <PlusIcon class="w-4 h-4 mr-2" />
                    Add Item
                  </button>
                </div>
              </div>

              <div class="p-6 space-y-6">
                <div
                  v-for="(item, index) in form.items"
                  :key="index"
                  class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200 hover:shadow-md transition-all duration-200"
                >
                  <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2 space-y-2">
                      <label class="block text-sm font-semibold text-gray-700">
                        Service Name *
                      </label>
                      <select
                        v-model="item.service_name"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white"
                        @change="updateItemPrice(index)"
                        required
                      >
                        <option value="">Select Service</option>
                        <option v-for="service in defaultServices" :key="service.name" :value="service.name">
                          {{ service.name }}
                        </option>
                      </select>
                    </div>

                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-gray-700">
                        Quantity *
                      </label>
                      <input
                        v-model.number="item.quantity"
                        type="number"
                        min="1"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                        @input="calculateItemTotal(index)"
                        required
                      />
                    </div>

                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-gray-700">
                        Unit Price *
                      </label>
                      <input
                        v-model.number="item.unit_price"
                        type="number"
                        step="0.01"
                        min="0"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                        @input="calculateItemTotal(index)"
                        required
                      />
                    </div>

                    <div class="flex items-end justify-between">
                      <div class="flex-1 space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                          Total
                        </label>
                        <div class="text-lg font-bold text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-200">
                          ${{ formatCurrency(item.quantity * item.unit_price) }}
                        </div>
                      </div>
                      <button
                        v-if="form.items.length > 1"
                        type="button"
                        @click="removeItem(index)"
                        class="ml-3 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white p-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                      >
                        <TrashIcon class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                </div>

                <div v-if="form.errors.items" class="text-red-600 text-sm font-medium bg-red-50 p-3 rounded-lg border border-red-200">
                  {{ form.errors.items }}
                </div>
              </div>
            </div>

            <!-- Enhanced Totals -->
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
                  <span class="text-sm font-bold text-green-900">${{ formatCurrency(subtotal) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-green-200/50">
                  <span class="text-sm font-semibold text-green-700">Tax (18%):</span>
                  <span class="text-sm font-bold text-green-900">${{ formatCurrency(taxAmount) }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-t-2 border-green-300">
                  <span class="text-lg font-bold text-green-900">Total Amount:</span>
                  <span class="text-xl font-bold text-green-600">${{ formatCurrency(totalAmount) }}</span>
                </div>
                <div class="text-xs text-green-600 italic bg-green-100 p-2 rounded-lg border border-green-200">
                  Tax will be auto calculated at 18% once saved
                </div>
              </div>
            </div>

            <!-- Enhanced Submit Buttons -->
            <div class="flex flex-wrap justify-end gap-3 pt-8 border-t border-gray-200">
              <Link
                href="/admin/invoices"
                class="bg-gradient-to-r from-gray-500 to-slate-600 hover:from-gray-600 hover:to-slate-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
              >
                <XMarkIcon class="w-5 h-5 mr-2" />
                <span class="font-semibold">Cancel</span>
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
              >
                <CheckIcon class="w-5 h-5 mr-2" />
                <span class="font-semibold">
                  <span v-if="form.processing">Updating...</span>
                  <span v-else>Update Invoice</span>
                </span>
              </button>
              <button
                type="button"
                @click="saveAndEmail"
                :disabled="form.processing"
                class="bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
              >
                <PaperAirplaneIcon class="w-5 h-5 mr-2" />
                <span class="font-semibold">
                  <span v-if="form.processing">Updating...</span>
                  <span v-else>Update & Email</span>
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { 
  DocumentTextIcon,
  PencilIcon,
  ArrowLeftIcon,
  ChatBubbleLeftEllipsisIcon,
  ListBulletIcon,
  PlusIcon,
  TrashIcon,
  CurrencyDollarIcon,
  XMarkIcon,
  CheckIcon,
  PaperAirplaneIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  invoice: Object,
  clients: Array,
  defaultServices: Array,
})

const form = useForm({
  client_id: props.invoice.client_id,
  title: props.invoice.title,
  comments: props.invoice.comments || '',
  send_to_email: props.invoice.send_to_email,
  invoice_year: props.invoice.invoice_year,
  status: props.invoice.status,
  items: props.invoice.items.map(item => ({
    service_name: item.service_name,
    quantity: item.quantity,
    unit_price: parseFloat(item.unit_price),
  })),
})

// Watch for client selection to auto-fill email
watch(() => form.client_id, (newClientId) => {
  if (newClientId) {
    const client = props.clients.find(c => c.id == newClientId)
    if (client) {
      form.send_to_email = client.email
    }
  }
})

const subtotal = computed(() => {
  return form.items.reduce((sum, item) => {
    return sum + (item.quantity * item.unit_price)
  }, 0)
})

const taxAmount = computed(() => {
  return subtotal.value * 0.18
})

const totalAmount = computed(() => {
  return subtotal.value + taxAmount.value
})

const addItem = () => {
  form.items.push({
    service_name: '',
    quantity: 1,
    unit_price: 0,
  })
}

const removeItem = (index) => {
  form.items.splice(index, 1)
}

const updateItemPrice = (index) => {
  const item = form.items[index]
  const service = props.defaultServices.find(s => s.name === item.service_name)
  if (service) {
    item.unit_price = service.price
  }
}

const calculateItemTotal = (index) => {
  // This is handled by the computed total display
}

const formatCurrency = (amount) => {
  return parseFloat(amount || 0).toFixed(2)
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

const submitForm = () => {
  form.put(`/admin/invoices/${props.invoice.id}`)
}

const saveAndEmail = () => {
  form.transform(data => ({
    ...data,
    send_email: true
  })).put(`/admin/invoices/${props.invoice.id}`)
}
</script>