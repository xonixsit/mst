<template>
  <AppLayout title="Edit Invoice">
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h2 class="text-xl font-semibold text-gray-900">Edit Invoice #{{ invoice.invoice_number }}</h2>
              <Link
                href="/admin/invoices"
                class="text-gray-600 hover:text-gray-900"
              >
                ← Back to Invoices
              </Link>
            </div>
          </div>

          <form @submit.prevent="submitForm" class="p-6 space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Invoice Assign To *
                </label>
                <select
                  v-model="form.client_id"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.client_id }"
                  required
                >
                  <option value="">Select Client</option>
                  <option v-for="client in clients" :key="client.id" :value="client.id">
                    {{ client.first_name }} {{ client.last_name }} ({{ client.email }})
                  </option>
                </select>
                <div v-if="form.errors.client_id" class="text-red-500 text-sm mt-1">
                  {{ form.errors.client_id }}
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Send to Email *
                </label>
                <input
                  v-model="form.send_to_email"
                  type="email"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.send_to_email }"
                  required
                />
                <div v-if="form.errors.send_to_email" class="text-red-500 text-sm mt-1">
                  {{ form.errors.send_to_email }}
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Invoice Title *
                </label>
                <input
                  v-model="form.title"
                  type="text"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.title }"
                  required
                />
                <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">
                  {{ form.errors.title }}
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Invoice Year *
                </label>
                <select
                  v-model="form.invoice_year"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.invoice_year }"
                  required
                >
                  <option value="2024">2024</option>
                  <option value="2025">2025</option>
                  <option value="2026">2026</option>
                </select>
                <div v-if="form.errors.invoice_year" class="text-red-500 text-sm mt-1">
                  {{ form.errors.invoice_year }}
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Status *
                </label>
                <select
                  v-model="form.status"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.status }"
                  required
                >
                  <option value="draft">Draft</option>
                  <option value="sent">Sent</option>
                  <option value="paid">Paid</option>
                  <option value="overdue">Overdue</option>
                </select>
                <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">
                  {{ form.errors.status }}
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Comments
              </label>
              <textarea
                v-model="form.comments"
                rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Additional comments or notes..."
              ></textarea>
            </div>

            <!-- Line Items -->
            <div>
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Invoice Items</h3>
                <button
                  type="button"
                  @click="addItem"
                  class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-sm"
                >
                  Add More Item
                </button>
              </div>

              <div class="space-y-4">
                <div
                  v-for="(item, index) in form.items"
                  :key="index"
                  class="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 border border-gray-200 rounded-md"
                >
                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Service Name *
                    </label>
                    <select
                      v-model="item.service_name"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      @change="updateItemPrice(index)"
                      required
                    >
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
                    <input
                      v-model.number="item.quantity"
                      type="number"
                      min="1"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      @input="calculateItemTotal(index)"
                      required
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Unit Price *
                    </label>
                    <input
                      v-model.number="item.unit_price"
                      type="number"
                      step="0.01"
                      min="0"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      @input="calculateItemTotal(index)"
                      required
                    />
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
                    <button
                      v-if="form.items.length > 1"
                      type="button"
                      @click="removeItem(index)"
                      class="ml-2 text-red-600 hover:text-red-900 p-1"
                    >
                      ✕
                    </button>
                  </div>
                </div>
              </div>

              <div v-if="form.errors.items" class="text-red-500 text-sm mt-2">
                {{ form.errors.items }}
              </div>
            </div>

            <!-- Totals -->
            <div class="border-t pt-6">
              <div class="max-w-md ml-auto space-y-2">
                <div class="flex justify-between">
                  <span class="text-gray-600">Subtotal:</span>
                  <span class="font-medium">${{ formatCurrency(subtotal) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Tax (18%):</span>
                  <span class="font-medium">${{ formatCurrency(taxAmount) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-2">
                  <span>Total:</span>
                  <span>${{ formatCurrency(totalAmount) }}</span>
                </div>
                <div class="text-sm text-gray-500 italic">
                  Tax will be auto calculated 18% once saved
                </div>
              </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
              <Link
                href="/admin/invoices"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md"
              >
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md disabled:opacity-50"
              >
                <span v-if="form.processing">Updating...</span>
                <span v-else>Update</span>
              </button>
              <button
                type="button"
                @click="saveAndEmail"
                :disabled="form.processing"
                class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-md disabled:opacity-50"
              >
                <span v-if="form.processing">Updating...</span>
                <span v-else>Update & Email</span>
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