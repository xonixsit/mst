<template>
  <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-start justify-center p-4 pt-8 overflow-y-auto">
    <div class="relative w-full max-w-2xl bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all flex flex-col mb-8">
      <!-- Enhanced Header -->
      <div class="bg-gradient-to-r from-slate-50 via-green-50 to-emerald-50 px-6 py-5 border-b border-gray-200 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-16 bg-gradient-to-bl from-green-100/40 to-transparent rounded-bl-full"></div>
        <div class="relative flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
              <CreditCardIcon class="w-5 h-5 text-white" />
            </div>
            <div>
              <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 via-green-900 to-emerald-900 bg-clip-text text-transparent">Record Payment</h3>
              <p class="text-sm text-gray-600 font-medium">Mark invoice {{ invoice.invoice_number }} as paid</p>
            </div>
          </div>
          <button
            @click="$emit('close')"
            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200"
          >
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Enhanced Form -->
      <div class="p-8 max-h-[70vh] overflow-y-auto">
        <form @submit.prevent="recordPayment" class="space-y-6">
          <!-- Payment Method -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Payment Method *</label>
            <select
              v-model="form.payment_method"
              required
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-gray-900"
            >
              <option value="">Select Payment Method</option>
              <option value="cash">Cash</option>
              <option value="check">Check</option>
              <option value="credit_card">Credit Card</option>
              <option value="debit_card">Debit Card</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="online_payment">Online Payment</option>
              <option value="other">Other</option>
            </select>
          </div>

          <!-- Transaction ID -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Transaction ID / Reference Number</label>
            <input
              v-model="form.transaction_id"
              type="text"
              placeholder="Enter transaction ID, check number, or reference"
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-gray-900"
            >
          </div>

          <!-- Payment Date -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Payment Date *</label>
            <input
              v-model="form.payment_date"
              type="date"
              required
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-gray-900"
            >
          </div>

          <!-- Amount Paid -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Amount Paid *</label>
            <div class="relative">
              <span class="absolute left-4 top-3.5 text-gray-500 font-medium">$</span>
              <input
                v-model="form.amount_paid"
                type="number"
                step="0.01"
                min="0"
                required
                class="w-full pl-8 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-gray-900"
                :placeholder="formatCurrency(invoice.total_amount)"
              >
            </div>
            <p class="text-xs text-gray-500">Invoice total: ${{ formatCurrency(invoice.total_amount) }}</p>
          </div>

          <!-- Payment Notes -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Payment Notes (Optional)</label>
            <textarea
              v-model="form.payment_notes"
              rows="3"
              placeholder="Add any additional notes about this payment..."
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-gray-900 placeholder-gray-500 resize-none"
            ></textarea>
          </div>

          <!-- Payment Summary -->
          <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center mb-4">
              <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                <CurrencyDollarIcon class="w-5 h-5 text-white" />
              </div>
              <h3 class="text-lg font-bold text-green-900">Payment Summary</h3>
            </div>
            
            <div class="space-y-3">
              <div class="flex justify-between items-center py-2 border-b border-green-200/50">
                <span class="text-sm font-semibold text-green-700">Invoice Number:</span>
                <span class="text-sm font-bold text-green-900">{{ invoice.invoice_number }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-green-200/50">
                <span class="text-sm font-semibold text-green-700">Invoice Total:</span>
                <span class="text-sm font-bold text-green-900">${{ formatCurrency(invoice.total_amount) }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-green-200/50">
                <span class="text-sm font-semibold text-green-700">Amount Being Paid:</span>
                <span class="text-sm font-bold text-green-900">${{ formatCurrency(form.amount_paid || 0) }}</span>
              </div>
              <div class="flex justify-between items-center py-3 border-t-2 border-green-300">
                <span class="text-lg font-bold text-green-900">Status After Payment:</span>
                <span class="text-lg font-bold text-green-600">PAID</span>
              </div>
            </div>
          </div>

          <!-- Enhanced Action Buttons -->
          <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 bg-white sticky bottom-0 -mx-8 px-8 -mb-8 pb-8">
            <button
              type="button"
              @click="$emit('close')"
              class="bg-gradient-to-r from-gray-500 to-slate-600 hover:from-gray-600 hover:to-slate-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center"
            >
              <XMarkIcon class="w-4 h-4 mr-2" />
              Cancel
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center"
            >
              <CheckCircleIcon v-if="!processing" class="w-4 h-4 mr-2" />
              <svg v-else class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-if="processing">Recording Payment...</span>
              <span v-else>Record Payment</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
  CreditCardIcon,
  XMarkIcon,
  CurrencyDollarIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  invoice: Object
})

const emit = defineEmits(['close', 'paid'])

const processing = ref(false)

const form = reactive({
  payment_method: '',
  transaction_id: '',
  payment_date: new Date().toISOString().split('T')[0],
  amount_paid: props.invoice?.total_amount || 0,
  payment_notes: ''
})

const formatCurrency = (amount) => {
  return parseFloat(amount || 0).toFixed(2)
}

const recordPayment = () => {
  if (!props.invoice?.id) {
    console.error('Invoice ID is missing')
    return
  }
  
  processing.value = true
  
  router.post(`/admin/invoices/${props.invoice.id}/mark-paid`, form, {
    onSuccess: () => {
      emit('paid')
      emit('close')
    },
    onError: (errors) => {
      console.error('Failed to record payment:', errors)
      processing.value = false
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>