<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                {{ isEditing ? 'Edit Expense' : 'Add New Expense' }}
              </h3>
              
              <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Two Column Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Left Column -->
                  <div class="space-y-4">
                    <!-- Category -->
                    <div>
                      <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                        Category <span class="text-red-500">*</span>
                      </label>
                      <select
                        id="category"
                        v-model="formData.category"
                        :class="inputClasses('category')"
                        required
                      >
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

                    <!-- Date -->
                    <div>
                      <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                        Date <span class="text-red-500">*</span>
                      </label>
                      <input
                        id="date"
                        v-model="formData.date"
                        type="date"
                        :class="inputClasses('date')"
                        required
                      />
                      <p v-if="errors.date" class="mt-1 text-sm text-red-600">{{ errors.date }}</p>
                    </div>

                    <!-- Tax Payer -->
                    <div>
                      <label for="taxPayer" class="block text-sm font-medium text-gray-700 mb-1">
                        Tax Payer <span class="text-red-500">*</span>
                      </label>
                      <input
                        id="taxPayer"
                        v-model="formData.taxPayer"
                        type="text"
                        :class="inputClasses('taxPayer')"
                        placeholder="Enter taxpayer name"
                        required
                      />
                      <p v-if="errors.taxPayer" class="mt-1 text-sm text-red-600">{{ errors.taxPayer }}</p>
                    </div>

                    <!-- Spouse (Optional) -->
                    <div>
                      <label for="spouse" class="block text-sm font-medium text-gray-700 mb-1">
                        Spouse
                      </label>
                      <input
                        id="spouse"
                        v-model="formData.spouse"
                        type="text"
                        :class="inputClasses('spouse')"
                        placeholder="Enter spouse name (if applicable)"
                      />
                      <p v-if="errors.spouse" class="mt-1 text-sm text-red-600">{{ errors.spouse }}</p>
                    </div>

                    <!-- Deductible Checkbox -->
                    <div class="flex items-center space-x-3">
                      <input
                        id="is_deductible"
                        v-model="formData.is_deductible"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                      />
                      <label for="is_deductible" class="text-sm text-gray-700">
                        Tax deductible
                      </label>
                    </div>
                  </div>

                  <!-- Right Column -->
                  <div class="space-y-4">
                    <!-- Amount -->
                    <div>
                      <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                        Amount <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input
                          id="amount"
                          v-model="formData.amount"
                          type="number"
                          step="0.01"
                          min="0"
                          :class="amountInputClasses"
                          placeholder="0.00"
                          required
                        />
                      </div>
                      <p v-if="errors.amount" class="mt-1 text-sm text-red-600">{{ errors.amount }}</p>
                    </div>

                    <!-- Receipt Number -->
                    <div>
                      <label for="receiptNumber" class="block text-sm font-medium text-gray-700 mb-1">
                        Receipt Number
                      </label>
                      <input
                        id="receiptNumber"
                        v-model="formData.receiptNumber"
                        type="text"
                        :class="inputClasses('receiptNumber')"
                        placeholder="Enter receipt number (optional)"
                      />
                      <p v-if="errors.receiptNumber" class="mt-1 text-sm text-red-600">{{ errors.receiptNumber }}</p>
                    </div>

                    <!-- Deductible Percentage (if deductible) -->
                    <div v-if="formData.is_deductible">
                      <label for="deductiblePercentage" class="block text-sm font-medium text-gray-700 mb-1">
                        Deductible Percentage
                      </label>
                      <div class="relative">
                        <input
                          id="deductiblePercentage"
                          v-model="formData.deductiblePercentage"
                          type="number"
                          min="0"
                          max="100"
                          step="0.01"
                          :class="inputClasses('deductiblePercentage')"
                          placeholder="100"
                        />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                          <span class="text-gray-500 sm:text-sm">%</span>
                        </div>
                      </div>
                      <p v-if="errors.deductiblePercentage" class="mt-1 text-sm text-red-600">{{ errors.deductiblePercentage }}</p>
                    </div>

                    <!-- Empty space when deductible is false to maintain alignment -->
                    <div v-else class="h-16"></div>
                  </div>
                </div>

                <!-- Full Width Fields -->
                <div class="space-y-4">
                  <!-- Description (Full Width) -->
                  <div>
                    <label for="particulars" class="block text-sm font-medium text-gray-700 mb-1">
                      Description <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="particulars"
                      v-model="formData.particulars"
                      type="text"
                      :class="inputClasses('particulars')"
                      placeholder="Enter expense description"
                      required
                    />
                    <p v-if="errors.particulars" class="mt-1 text-sm text-red-600">{{ errors.particulars }}</p>
                  </div>

                  <!-- Remarks (Full Width) -->
                  <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">
                      Remarks
                    </label>
                    <textarea
                      id="remarks"
                      v-model="formData.remarks"
                      rows="3"
                      :class="inputClasses('remarks')"
                      placeholder="Additional notes about this expense..."
                    ></textarea>
                    <p v-if="errors.remarks" class="mt-1 text-sm text-red-600">{{ errors.remarks }}</p>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Modal footer -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            type="button"
            @click="handleSubmit"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            {{ isEditing ? 'Update' : 'Add' }} Expense
          </button>
          <button
            type="button"
            @click="closeModal"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Cancel
          </button>
        </div>
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
  const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm'
  const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
  
  return props.errors[fieldName] ? `${baseClasses} ${errorClasses}` : baseClasses
}

const amountInputClasses = computed(() => {
  const baseClasses = 'block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm'
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