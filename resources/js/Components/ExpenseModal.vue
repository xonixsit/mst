<template>
  <div v-if="show"
    class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-start justify-center p-4 pt-8 overflow-y-auto">
    <div
      class="relative w-full max-w-4xl bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all flex flex-col mb-8">
      <!-- Enhanced Header -->
      <div
        class="bg-gradient-to-r from-slate-50 via-red-50 to-rose-50 px-6 py-5 border-b border-gray-200 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-16 bg-gradient-to-bl from-red-100/40 to-transparent rounded-bl-full">
        </div>
        <div class="relative flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div
              class="w-10 h-10 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
              </svg>
            </div>
            <div>
              <h3
                class="text-xl font-bold bg-gradient-to-r from-gray-900 via-red-900 to-rose-900 bg-clip-text text-transparent">
                {{ isEditing ? 'Edit Expense' : 'Add New Expense' }}
              </h3>
              <p class="text-sm text-gray-600 font-medium">{{ isEditing ? 'Update expense details and information' :
                'Add a new business or personal expense' }}</p>
            </div>
          </div>
          <button @click="closeModal"
            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Enhanced Form -->
      <div class="p-8 max-h-[70vh] overflow-y-auto">
        <form @submit.prevent="handleSubmit" class="space-y-8">

          <!-- Basic Information Section -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-blue-900">Basic Information</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Category -->
            <div class="space-y-2">
              <label for="category" class="block text-sm font-semibold text-blue-700">
                Category <span class="text-red-500">*</span>
              </label>
              <select id="category" v-model="formData.category"
                class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white text-gray-900"
                required>
                <option value="">Select category</option>
                <option value="miscellaneous">Miscellaneous</option>
                <option value="residency">Residency</option>
                <option value="business">Business</option>
                <option value="medical">Medical</option>
                <option value="education">Education</option>
                <option value="charitable">Charitable</option>
                <option value="travel">Travel</option>
                <option value="office">Office</option>
                <option value="professional">Professional</option>
                <option value="other">Other</option>
              </select>
              <p v-if="errors.category" class="mt-1 text-sm text-red-600">{{ errors.category }}</p>
            </div>

            <!-- Amount -->
            <div class="space-y-2">
              <label for="amount" class="block text-sm font-semibold text-blue-700">
                Amount <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input id="amount" v-model="formData.amount" type="number" step="0.01" min="0"
                  class="w-full pl-8 pr-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-900"
                  placeholder="0.00" required />
              </div>
              <p v-if="errors.amount" class="mt-1 text-sm text-red-600">{{ errors.amount }}</p>
            </div>
          </div>
          </div>
          <!-- Date and Receipt Section -->
          <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-green-900">Date & Receipt</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Date -->
              <div class="space-y-2">
                <label for="date" class="block text-sm font-semibold text-green-700">
                  Date <span class="text-red-500">*</span>
                </label>
                <input id="date" v-model="formData.date" type="date"
                  class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-gray-900"
                  required />
                <p v-if="errors.date" class="mt-1 text-sm text-red-600">{{ errors.date }}</p>
              </div>

              <!-- Receipt Number -->
              <div class="space-y-2">
                <label for="receiptNumber" class="block text-sm font-semibold text-green-700">
                  Receipt Number
                </label>
                <input id="receiptNumber" v-model="formData.receiptNumber" type="text"
                  class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-gray-900 placeholder-gray-500"
                  placeholder="Enter receipt number (optional)" />
                <p v-if="errors.receiptNumber" class="mt-1 text-sm text-red-600">{{ errors.receiptNumber }}</p>
              </div>
            </div>
          </div>

          <!-- Taxpayer Information Section -->
          <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-purple-900">Taxpayer Information</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Tax Payer -->
              <div class="space-y-2">
                <label for="taxPayer" class="block text-sm font-semibold text-purple-700">
                  Tax Payer <span class="text-red-500">*</span>
                </label>
                <input id="taxPayer" v-model="formData.taxPayer" type="text"
                  class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900 placeholder-gray-500"
                  placeholder="Enter taxpayer name" required />
                <p v-if="errors.taxPayer" class="mt-1 text-sm text-red-600">{{ errors.taxPayer }}</p>
              </div>

              <!-- Spouse (Optional) -->
              <div class="space-y-2">
                <label for="spouse" class="block text-sm font-semibold text-purple-700">
                  Spouse
                </label>
                <input id="spouse" v-model="formData.spouse" type="text"
                  class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900 placeholder-gray-500"
                  placeholder="Enter spouse name (if applicable)" />
                <p v-if="errors.spouse" class="mt-1 text-sm text-red-600">{{ errors.spouse }}</p>
              </div>
            </div>
          </div>

          <!-- Tax Deduction Section -->
          <div class="bg-gradient-to-br from-orange-50 to-amber-100 rounded-xl p-6 border border-orange-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                  </path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-orange-900">Tax Deduction</h4>
            </div>

            <div class="space-y-6">
              <!-- Deductible Checkbox -->
              <div class="flex items-center space-x-3">
                <input id="is_deductible" v-model="formData.is_deductible" type="checkbox"
                  class="w-4 h-4 rounded border-orange-300 text-orange-600 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50" />
                <label for="is_deductible" class="text-sm font-semibold text-orange-700">
                  Tax deductible
                </label>
              </div>

              <!-- Deductible Percentage (if deductible) -->
              <div v-if="formData.is_deductible" class="space-y-2">
                <label for="deductiblePercentage" class="block text-sm font-semibold text-orange-700">
                  Deductible Percentage
                </label>
                <div class="relative">
                  <input id="deductiblePercentage" v-model="formData.deductiblePercentage" type="number" min="0" max="100"
                    step="0.01"
                    class="w-full px-4 py-3 pr-8 border-2 border-orange-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 text-gray-900"
                    placeholder="100" />
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">%</span>
                  </div>
                </div>
                <p v-if="errors.deductiblePercentage" class="mt-1 text-sm text-red-600">{{ errors.deductiblePercentage }}
                </p>
              </div>
            </div>
          </div> 

          <!-- Description & Remarks Section -->
          <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-gray-900">Description & Notes</h4>
            </div>

            <div class="space-y-6">
              <!-- Description -->
              <div class="space-y-2">
                <label for="particulars" class="block text-sm font-semibold text-gray-700">
                  Description <span class="text-red-500">*</span>
                </label>
                <input id="particulars" v-model="formData.particulars" type="text"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200 text-gray-900 placeholder-gray-500"
                  placeholder="Enter detailed expense description" required />
                <p v-if="errors.particulars" class="mt-1 text-sm text-red-600">{{ errors.particulars }}</p>
              </div>

              <!-- Remarks -->
              <div class="space-y-2">
                <label for="remarks" class="block text-sm font-semibold text-gray-700">
                  Additional Notes
                </label>
                <textarea id="remarks" v-model="formData.remarks" rows="4"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200 text-gray-900 placeholder-gray-500 resize-none"
                  placeholder="Add any additional notes, context, or details about this expense..."></textarea>
                <p v-if="errors.remarks" class="mt-1 text-sm text-red-600">{{ errors.remarks }}</p>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Enhanced Modal Footer -->
      <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-8 py-6 border-t border-gray-200 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
        <button type="button" @click="closeModal"
          class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md">
          Cancel
        </button>
        <button type="button" @click="handleSubmit"
          class="w-full sm:w-auto bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 group">
          <div class="flex items-center justify-center">
            <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ isEditing ? 'Update Expense' : 'Add Expense' }}
          </div>
        </button>
      </div>
  </div>
</div>
</template>

<script setup>
  import { ref, reactive, computed, watch } from 'vue'

  const props = defineProps({
    show: {
      type: Boolean,
      default: false
    },
    expense: {
      type: Object,
      default: null
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  })

  const emit = defineEmits(['close', 'save'])

  const isEditing = computed(() => props.expense && props.expense.id)

  const formData = reactive({
    id: null,
    category: '',
    particulars: '',
    date: '',
    amount: '',
    taxPayer: '',
    spouse: '',
    receiptNumber: '',
    is_deductible: true,
    deductiblePercentage: 100,
    remarks: ''
  })

  const inputClasses = (fieldName) => {
    const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-gray-900 placeholder-gray-500'
    const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'

    return props.errors[fieldName] ? `${baseClasses} ${errorClasses}` : baseClasses
  }

  const amountInputClasses = computed(() => {
    const baseClasses = 'block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-gray-900 placeholder-gray-500'
    const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'

    return props.errors.amount ? `${baseClasses} ${errorClasses}` : baseClasses
  })

  const resetForm = () => {
    Object.assign(formData, {
      id: null,
      category: '',
      particulars: '',
      date: '',
      amount: '',
      taxPayer: '',
      spouse: '',
      receiptNumber: '',
      is_deductible: true,
      deductiblePercentage: 100,
      remarks: ''
    })
  }

  const loadExpense = () => {
    if (props.expense) {
      Object.assign(formData, props.expense)
    } else {
      resetForm()
    }
  }

  const handleSubmit = () => {
    emit('save', { ...formData })
  }

  const closeModal = () => {
    emit('close')
  }

  watch(() => props.show, (newValue) => {
    if (newValue) {
      loadExpense()
    }
  })

  watch(() => props.expense, loadExpense, { deep: true })
</script>