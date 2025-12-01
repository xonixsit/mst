<template>
  <AppLayout title="Create Invoice">
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-emerald-50 to-green-50"></div>
        <div
          class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-emerald-100/40 to-transparent rounded-bl-full">
        </div>
        <div
          class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-green-100/30 to-transparent rounded-tr-full">
        </div>

        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Create Invoice Icon -->
            <div
              class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-emerald-600 to-green-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-emerald-100">
              <DocumentPlusIcon class="w-8 h-8 text-white" />
            </div>

            <!-- Title Section -->
            <div>
              <h1
                class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-emerald-900 to-green-900 bg-clip-text text-transparent">
                Create New Invoice
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Generate professional invoices for your clients</p>

              <!-- Progress Indicator -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-emerald-700">Invoice Creation</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-green-700">${{ formatCurrency(totalAmount) }} Total</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Button -->
          <div class="flex items-center">
            <Link href="/admin/invoices"
              class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group">
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
                <h3 class="text-xl font-bold text-gray-900">Invoice Details</h3>
                <p class="text-sm text-gray-600 mt-1">Complete the form below to create a new invoice</p>
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
                  <select v-model="form.client_id"
                    class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.client_id }"
                    required>
                    <option value="">Select Client</option>
                    <option v-for="client in clients" :key="client.id" :value="client.id">
                      {{ client.name }} ({{ client.email }})
                    </option>
                  </select>
                  <div v-if="form.errors.client_id" class="text-red-600 text-sm font-medium">
                    {{ form.errors.client_id }}
                  </div>

                  <!-- Client Contact Information -->
                  <div v-if="form.client_id" class="mt-3 p-3 bg-emerald-100 rounded-lg border border-emerald-200">
                    <h4 class="text-sm font-semibold text-emerald-700 mb-2">Client Contact</h4>
                    <div class="text-xs text-emerald-600 space-y-1">
                      <div v-if="selectedClient?.phone">Phone: {{ selectedClient.phone }}</div>
                      <div v-if="selectedClient?.mobile_number">Mobile: {{ selectedClient.mobile_number }}</div>
                    </div>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-emerald-700">
                    Send to Email *
                  </label>
                  <input v-model="form.send_to_email" type="email"
                    class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.send_to_email }"
                    required />
                  <div v-if="form.errors.send_to_email" class="text-red-600 text-sm font-medium">
                    {{ form.errors.send_to_email }}
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-emerald-700">
                    Invoice Title *
                  </label>
                  <input v-model="form.title" type="text"
                    class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.title }" required />
                  <div v-if="form.errors.title" class="text-red-600 text-sm font-medium">
                    {{ form.errors.title }}
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-emerald-700">
                    Invoice Year *
                  </label>
                  <select v-model="form.invoice_year"
                    class="w-full px-4 py-3 border-2 border-emerald-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': form.errors.invoice_year }"
                    required>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                  </select>
                  <div v-if="form.errors.invoice_year" class="text-red-600 text-sm font-medium">
                    {{ form.errors.invoice_year }}
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
                <textarea v-model="form.comments" rows="4"
                  class="w-full px-4 py-3 border-2 border-amber-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 resize-none"
                  placeholder="Additional comments or notes about this invoice..."></textarea>
              </div>

              <!-- Client Financial Summary -->
              <div v-if="clientFinancialSummary" class="bg-blue-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Client Financial Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                  <div class="bg-white rounded-lg p-3">
                    <div class="text-sm font-medium text-gray-600">Total Invoiced</div>
                    <div class="text-lg font-bold text-blue-600">${{
                      formatCurrency(clientFinancialSummary.total_invoiced) }}</div>
                  </div>
                  <div class="bg-white rounded-lg p-3">
                    <div class="text-sm font-medium text-gray-600">Total Paid</div>
                    <div class="text-lg font-bold text-green-600">${{ formatCurrency(clientFinancialSummary.total_paid)
                      }}</div>
                  </div>
                  <div class="bg-white rounded-lg p-3">
                    <div class="text-sm font-medium text-gray-600">Outstanding</div>
                    <div class="text-lg font-bold text-red-600">${{
                      formatCurrency(clientFinancialSummary.total_outstanding) }}</div>
                  </div>
                  <div class="bg-white rounded-lg p-3">
                    <div class="text-sm font-medium text-gray-600">Invoice Count</div>
                    <div class="text-lg font-bold text-gray-900">{{ clientFinancialSummary.invoice_count }}</div>
                  </div>
                </div>

                <!-- Recent Invoices -->
                <div v-if="clientFinancialSummary.recent_invoices && clientFinancialSummary.recent_invoices.length > 0">
                  <h4 class="text-md font-medium text-gray-900 mb-2">Recent Invoices</h4>
                  <div class="bg-white rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                          <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="invoice in clientFinancialSummary.recent_invoices" :key="invoice.id">
                          <td class="px-3 py-2 text-sm text-gray-900">{{ invoice.invoice_number }}</td>
                          <td class="px-3 py-2 text-sm text-gray-900">{{ invoice.title }}</td>
                          <td class="px-3 py-2 text-sm text-gray-900">${{ formatCurrency(invoice.total_amount) }}</td>
                          <td class="px-3 py-2 text-sm">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full"
                              :class="getStatusClass(invoice.status)">
                              {{ invoice.status }}
                            </span>
                          </td>
                          <td class="px-3 py-2 text-sm text-gray-900">{{ formatDate(invoice.created_at) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
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
                    <button type="button" @click="addItem"
                      class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center">
                      <PlusIcon class="w-4 h-4 mr-2" />
                      Add Item
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="p-6 space-y-6">

              <div v-for="(item, index) in form.items" :key="index"
                class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200 hover:shadow-md transition-all duration-200">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Service Name *
                    </label>
                    <select v-model="item.service_name"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      @change="updateItemPrice(index)" required>
                      <option value="">Select Service</option>
                      <option v-for="service in defaultServices" :key="service.name" :value="service.name">
                        {{ service.name }}
                      </option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Quantity *
                    </label>
                    <input v-model.number="item.quantity" type="number" min="1"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      @input="calculateItemTotal(index)" required />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Unit Price *
                    </label>
                    <input v-model.number="item.unit_price" type="number" step="0.01" min="0"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      @input="calculateItemTotal(index)" required />
                  </div>

                  <div class="flex items-end">
                    <div class="flex-1">
                      <label class="block text-sm font-medium text-gray-700 mb-1">
                        Total
                      </label>
                      <div class="text-lg font-medium text-gray-900">
                        ${{ formatCurrency(item.quantity * item.unit_price) }}
                      </div>
                    </div>
                    <button v-if="form.items.length > 1" type="button" @click="removeItem(index)"
                      class="ml-2 text-red-600 hover:text-red-900 p-1">
                      ✕
                    </button>
                  </div>
                </div>
              </div>

              <div v-if="form.errors.items" class="text-red-500 text-sm mt-2">
                {{ form.errors.items }}
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
              <Link href="/admin/invoices"
                class="bg-gradient-to-r from-gray-500 to-slate-600 hover:from-gray-600 hover:to-slate-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group">
              <XMarkIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">Cancel</span>
              </Link>
              <button type="submit" :disabled="form.processing"
                class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                <CheckIcon class="w-5 h-5 mr-2" />
                <span class="font-semibold">
                  <span v-if="form.processing">Creating...</span>
                  <span v-else>Create Invoice</span>
                </span>
              </button>
              <button type="button" @click="saveAndEmail" :disabled="form.processing"
                class="bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                <PaperAirplaneIcon class="w-5 h-5 mr-2" />
                <span class="font-semibold">
                  <span v-if="form.processing">Creating...</span>
                  <span v-else">Create & Email</span>
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Invoice Email Modal -->
    <InvoiceEmailModal v-if="showEmailModal && createdInvoice" :invoice="createdInvoice" @close="showEmailModal = false"
      @sent="onEmailSent" />
  </AppLayout>
</template>

<script setup>
  import { computed, watch, ref } from 'vue'
  import { Link, useForm } from '@inertiajs/vue3'
  import {
    DocumentPlusIcon,
    DocumentTextIcon,
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
  import InvoiceEmailModal from '@/Components/InvoiceEmailModal.vue'

  const props = defineProps({
    clients: Array,
    defaultServices: Array,
    preselectedClient: Object,
    clientFinancialSummary: Object,
    currentYear: Number,
    createdInvoice: Object,
    showEmailModal: Boolean,
  })

  const clientFinancialSummary = ref(props.clientFinancialSummary)
  const showEmailModal = ref(props.showEmailModal || false)
  const createdInvoice = ref(props.createdInvoice || null)

  const form = useForm({
    client_id: props.preselectedClient?.id || '',
    title: '',
    comments: '',
    send_to_email: props.preselectedClient?.email || '',
    invoice_year: props.currentYear,
    status: 'draft',
    items: [
      {
        service_name: '',
        quantity: 1,
        unit_price: 0,
      }
    ],
  })

  // Watch for client selection to auto-fill email and load financial summary
  watch(() => form.client_id, async (newClientId) => {
    if (newClientId) {
      const client = props.clients.find(c => c.id == newClientId)
      if (client) {
        form.send_to_email = client.email

        // Load client financial summary
        try {
          const response = await fetch(`/admin/clients/${newClientId}/financial-summary`)
          const data = await response.json()
          clientFinancialSummary.value = data.financial_summary
        } catch (error) {
          console.error('Failed to load client financial summary:', error)
        }
      }
    } else {
      clientFinancialSummary.value = null
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

  const selectedClient = computed(() => {
    if (form.client_id) {
      return props.clients.find(c => c.id == form.client_id)
    }
    return null
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

  const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit'
    })
  }

  const getStatusClass = (status) => {
    const classes = {
      draft: 'bg-gray-100 text-gray-800',
      sent: 'bg-blue-100 text-blue-800',
      paid: 'bg-green-100 text-green-800',
      overdue: 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
  }

  const submitForm = () => {
    form.post('/admin/invoices')
  }

  const saveAndEmail = () => {
    form.transform(data => ({
      ...data,
      send_email: true
    })).post('/admin/invoices')
  }

  const onEmailSent = () => {
    showEmailModal.value = false
    // Redirect to invoices list or show success message
    window.location.href = '/admin/invoices'
  }
</script>