<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="w-full">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Expense Details
                </h3>
                <button
                  @click="editExpense"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  Edit
                </button>
              </div>
              
              <div v-if="expense" class="space-y-4">
                <!-- Category and Amount -->
                <div class="flex items-center justify-between">
                  <span :class="getCategoryBadgeClass(expense.category)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                    {{ expense.category }}
                  </span>
                  <div class="text-right">
                    <p class="text-lg font-semibold text-gray-900">
                      ${{ formatAmount(expense.amount) }}
                    </p>
                    <p v-if="expense.is_deductible" class="text-sm text-green-600">
                      {{ expense.deductiblePercentage }}% deductible
                    </p>
                    <p v-else class="text-sm text-gray-500">
                      Not deductible
                    </p>
                  </div>
                </div>

                <!-- Description -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                  <p class="text-sm text-gray-900">{{ expense.particulars }}</p>
                </div>

                <!-- Date and Tax Payer -->
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <p class="text-sm text-gray-900">{{ formatDate(expense.date) }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax Payer</label>
                    <p class="text-sm text-gray-900 capitalize">{{ expense.taxPayer }}</p>
                  </div>
                </div>

                <!-- Receipt Number (if available) -->
                <div v-if="expense.receiptNumber">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Receipt Number</label>
                  <p class="text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded">{{ expense.receiptNumber }}</p>
                </div>

                <!-- Deductible Information -->
                <div v-if="expense.is_deductible" class="bg-green-50 border border-green-200 rounded-md p-3">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div class="ml-3">
                      <h4 class="text-sm font-medium text-green-800">Tax Deductible</h4>
                      <div class="mt-1 text-sm text-green-700">
                        <p>{{ expense.deductiblePercentage }}% of this expense ({{ formatDeductibleAmount(expense) }}) is tax deductible.</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Remarks (if available) -->
                <div v-if="expense.remarks">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                  <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ expense.remarks }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Modal footer -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            type="button"
            @click="closeModal"
            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  expense: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'edit'])

const getCategoryBadgeClass = (category) => {
  const classes = {
    business: 'bg-blue-100 text-blue-800',
    medical: 'bg-red-100 text-red-800',
    education: 'bg-green-100 text-green-800',
    charitable: 'bg-purple-100 text-purple-800',
    travel: 'bg-yellow-100 text-yellow-800',
    office: 'bg-indigo-100 text-indigo-800',
    professional: 'bg-pink-100 text-pink-800',
    miscellaneous: 'bg-gray-100 text-gray-800',
    other: 'bg-gray-100 text-gray-800'
  }
  return classes[category] || classes.other
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatAmount = (amount) => {
  return parseFloat(amount || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const formatDeductibleAmount = (expense) => {
  if (!expense.is_deductible) return '$0.00'
  
  const amount = parseFloat(expense.amount || 0)
  const percentage = parseFloat(expense.deductiblePercentage || 100) / 100
  const deductibleAmount = amount * percentage
  
  return '$' + deductibleAmount.toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const closeModal = () => {
  emit('close')
}

const editExpense = () => {
  emit('edit', props.expense)
}
</script>