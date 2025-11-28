<template>
  <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-start justify-center p-4 pt-8 overflow-y-auto">
    <div class="relative w-full max-w-2xl bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all flex flex-col mb-8">
      <!-- Enhanced Header -->
      <div class="bg-gradient-to-r from-slate-50 via-emerald-50 to-green-50 px-6 py-5 border-b border-gray-200 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-16 bg-gradient-to-bl from-emerald-100/40 to-transparent rounded-bl-full"></div>
        <div class="relative flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
              <PaperAirplaneIcon class="w-5 h-5 text-white" />
            </div>
            <div>
              <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 via-emerald-900 to-green-900 bg-clip-text text-transparent">Send Invoice</h3>
              <p class="text-sm text-gray-600 font-medium">Email invoice {{ invoice.invoice_number }} to client</p>
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
        <form @submit.prevent="sendInvoiceEmail" class="space-y-6">
          <!-- Recipient Email -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Recipient Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-gray-900"
              placeholder="client@example.com"
            >
          </div>

          <!-- Subject -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Subject</label>
            <input
              v-model="form.subject"
              type="text"
              required
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-gray-900"
            >
          </div>

          <!-- Payment Options -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200">
            <div class="flex items-center mb-4">
              <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                <CreditCardIcon class="w-4 h-4 text-white" />
              </div>
              <h4 class="text-lg font-bold text-blue-900">Payment Options</h4>
            </div>
            
            <div class="space-y-4">
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-blue-700">Payment Link (Optional)</label>
                <input
                  v-model="form.payment_link"
                  type="url"
                  placeholder="https://your-payment-gateway.com/invoice/..."
                  class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-900"
                >
                <p class="text-xs text-blue-600">Enter a custom payment link for this invoice</p>
              </div>
              
              <div v-if="form.payment_link" class="space-y-3">
                <div class="text-sm text-blue-600">
                  <p class="font-medium">Payment methods available:</p>
                  <ul class="list-disc list-inside mt-1 space-y-1">
                    <li>Credit/Debit Cards (Visa, MasterCard, Amex)</li>
                    <li>Bank Transfer (ACH)</li>
                    <li>PayPal</li>
                  </ul>
                </div>
                
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-blue-700">Payment Due Date</label>
                  <input
                    v-model="form.due_date"
                    type="date"
                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-900"
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Custom Message -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Custom Message (Optional)</label>
            <textarea
              v-model="form.message"
              rows="4"
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 text-gray-900 placeholder-gray-500 resize-none"
              placeholder="Add a personal message to your client..."
            ></textarea>
          </div>

          <!-- Email Preview -->
          <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200">
            <div class="flex items-center mb-4">
              <div class="w-8 h-8 bg-gray-500 rounded-lg flex items-center justify-center mr-3">
                <EyeIcon class="w-4 h-4 text-white" />
              </div>
              <h4 class="text-lg font-bold text-gray-900">Email Preview</h4>
            </div>
            
            <div class="bg-white rounded-lg p-4 border border-gray-200 text-sm">
              <div class="border-b border-gray-200 pb-3 mb-3">
                <p><strong>To:</strong> {{ form.email }}</p>
                <p><strong>Subject:</strong> {{ form.subject }}</p>
              </div>
              
              <div class="space-y-3">
                <p>Dear {{ invoice?.client?.user?.first_name || 'Valued Client' }},</p>
                
                <p v-if="form.message" class="bg-blue-50 p-3 rounded border-l-4 border-blue-400">
                  {{ form.message }}
                </p>
                
                <p>Please find attached your invoice <strong>{{ invoice?.invoice_number || 'N/A' }}</strong> for the amount of <strong>${{ formatCurrency(invoice?.total_amount || 0) }}</strong>.</p>
                
                <div v-if="form.payment_link" class="bg-green-50 p-4 rounded-lg border border-green-200">
                  <p class="font-semibold text-green-800 mb-2">💳 Pay Online Securely</p>
                  <p class="text-green-700 text-sm">Click the payment link below to pay securely online using your preferred payment method.</p>
                  <div class="mt-2 p-2 bg-white rounded border">
                    <a :href="form.payment_link" class="text-blue-600 text-sm font-medium hover:underline" target="_blank">
                      {{ form.payment_link }}
                    </a>
                  </div>
                  <p v-if="form.due_date" class="text-green-600 text-sm mt-2">
                    <strong>Due Date:</strong> {{ formatDate(form.due_date) }}
                  </p>
                </div>
                
                <p>Thank you for your business!</p>
                <p>Best regards,<br>Your Tax Professional</p>
              </div>
            </div>
          </div>

          <!-- Enhanced Actions -->
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
              class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center"
            >
              <PaperAirplaneIcon v-if="!processing" class="w-4 h-4 mr-2" />
              <svg v-else class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-if="processing">Sending Invoice...</span>
              <span v-else>Send Invoice</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
  PaperAirplaneIcon, 
  XMarkIcon, 
  CreditCardIcon,
  EyeIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  invoice: Object
})

const emit = defineEmits(['close', 'sent'])

const processing = ref(false)

const formatCurrency = (amount) => {
  return parseFloat(amount || 0).toFixed(2)
}

const form = reactive({
  email: props.invoice?.send_to_email || '',
  subject: `Invoice ${props.invoice?.invoice_number || 'N/A'} - $${formatCurrency(props.invoice?.total_amount || 0)}`,
  message: '',
  payment_link: '',
  due_date: ''
})

// Set default due date to 30 days from now
const defaultDueDate = new Date()
defaultDueDate.setDate(defaultDueDate.getDate() + 30)
form.due_date = defaultDueDate.toISOString().split('T')[0]

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const sendInvoiceEmail = () => {
  if (!props.invoice?.id) {
    console.error('Invoice ID is missing')
    return
  }
  
  processing.value = true
  
  router.post(`/admin/invoices/${props.invoice.id}/send-email`, form, {
    onSuccess: () => {
      emit('sent')
      emit('close')
    },
    onError: (errors) => {
      console.error('Failed to send invoice:', errors)
      processing.value = false
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>