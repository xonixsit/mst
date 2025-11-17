<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                {{ isEditing ? 'Edit Asset' : 'Add New Asset' }}
              </h3>
              
              <form @submit.prevent="handleSubmit" class="space-y-4">
                <!-- Asset Name and Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="asset_name" class="block text-sm font-medium text-gray-700 mb-1">
                      Asset Name <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="asset_name"
                      v-model="formData.asset_name"
                      type="text"
                      :class="inputClasses('asset_name')"
                      placeholder="Enter asset name"
                      required
                    />
                    <p v-if="errors.asset_name" class="mt-1 text-sm text-red-600">{{ errors.asset_name }}</p>
                  </div>

                  <div>
                    <label for="asset_type" class="block text-sm font-medium text-gray-700 mb-1">
                      Asset Type
                    </label>
                    <select
                      id="asset_type"
                      v-model="formData.asset_type"
                      :class="inputClasses('asset_type')"
                    >
                      <option value="">Select asset type</option>
                      <option value="equipment">Equipment</option>
                      <option value="vehicle">Vehicle</option>
                      <option value="furniture">Furniture</option>
                      <option value="computer">Computer/Technology</option>
                      <option value="machinery">Machinery</option>
                      <option value="building">Building</option>
                      <option value="land">Land</option>
                      <option value="other">Other</option>
                    </select>
                    <p v-if="errors.asset_type" class="mt-1 text-sm text-red-600">{{ errors.asset_type }}</p>
                  </div>
                </div>

                <!-- Purchase Date and Business Use -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="date_of_purchase" class="block text-sm font-medium text-gray-700 mb-1">
                      Purchase Date <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="date_of_purchase"
                      v-model="formData.date_of_purchase"
                      type="date"
                      :class="inputClasses('date_of_purchase')"
                      required
                    />
                    <p v-if="errors.date_of_purchase" class="mt-1 text-sm text-red-600">{{ errors.date_of_purchase }}</p>
                  </div>

                  <div>
                    <label for="percentage_used_in_business" class="block text-sm font-medium text-gray-700 mb-1">
                      Business Use % <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <input
                        id="percentage_used_in_business"
                        v-model="formData.percentage_used_in_business"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        :class="inputClasses('percentage_used_in_business')"
                        placeholder="0.00"
                        required
                      />
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">%</span>
                      </div>
                    </div>
                    <p v-if="errors.percentage_used_in_business" class="mt-1 text-sm text-red-600">{{ errors.percentage_used_in_business }}</p>
                  </div>
                </div>

                <!-- Cost and Reimbursement -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="cost_of_acquisition" class="block text-sm font-medium text-gray-700 mb-1">
                      Cost of Acquisition <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input
                        id="cost_of_acquisition"
                        v-model="formData.cost_of_acquisition"
                        type="number"
                        min="0"
                        step="0.01"
                        :class="inputClasses('cost_of_acquisition') + ' pl-7'"
                        placeholder="0.00"
                        required
                      />
                    </div>
                    <p v-if="errors.cost_of_acquisition" class="mt-1 text-sm text-red-600">{{ errors.cost_of_acquisition }}</p>
                  </div>

                  <div>
                    <label for="any_reimbursement" class="block text-sm font-medium text-gray-700 mb-1">
                      Reimbursement
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input
                        id="any_reimbursement"
                        v-model="formData.any_reimbursement"
                        type="number"
                        min="0"
                        step="0.01"
                        :class="inputClasses('any_reimbursement') + ' pl-7'"
                        placeholder="0.00"
                      />
                    </div>
                    <p v-if="errors.any_reimbursement" class="mt-1 text-sm text-red-600">{{ errors.any_reimbursement }}</p>
                  </div>
                </div>

                <!-- Current Value and Depreciation -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="current_value" class="block text-sm font-medium text-gray-700 mb-1">
                      Current Value
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input
                        id="current_value"
                        v-model="formData.current_value"
                        type="number"
                        min="0"
                        step="0.01"
                        :class="inputClasses('current_value') + ' pl-7'"
                        placeholder="0.00"
                      />
                    </div>
                    <p v-if="errors.current_value" class="mt-1 text-sm text-red-600">{{ errors.current_value }}</p>
                  </div>

                  <div>
                    <label for="depreciation_rate" class="block text-sm font-medium text-gray-700 mb-1">
                      Depreciation Rate
                    </label>
                    <div class="relative">
                      <input
                        id="depreciation_rate"
                        v-model="formData.depreciation_rate"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        :class="inputClasses('depreciation_rate')"
                        placeholder="0.00"
                      />
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">%</span>
                      </div>
                    </div>
                    <p v-if="errors.depreciation_rate" class="mt-1 text-sm text-red-600">{{ errors.depreciation_rate }}</p>
                  </div>
                </div>

                <!-- Description -->
                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                  </label>
                  <textarea
                    id="description"
                    v-model="formData.description"
                    rows="3"
                    :class="inputClasses('description')"
                    placeholder="Additional details about the asset..."
                  ></textarea>
                  <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
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
            {{ isEditing ? 'Update' : 'Add' }} Asset
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
  asset: {
    type: Object,
    default: null
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'save'])

const isEditing = computed(() => props.asset && props.asset.id)

const formData = reactive({
  id: null,
  asset_name: '',
  asset_type: '',
  date_of_purchase: '',
  percentage_used_in_business: '',
  cost_of_acquisition: '',
  any_reimbursement: '',
  current_value: '',
  depreciation_rate: '',
  description: ''
})

const inputClasses = (fieldName) => {
  const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm'
  const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
  
  return props.errors[fieldName] ? `${baseClasses} ${errorClasses}` : baseClasses
}

const resetForm = () => {
  Object.assign(formData, {
    id: null,
    asset_name: '',
    asset_type: '',
    date_of_purchase: '',
    percentage_used_in_business: '',
    cost_of_acquisition: '',
    any_reimbursement: '',
    current_value: '',
    depreciation_rate: '',
    description: ''
  })
}

const formatDateForInput = (date) => {
  if (!date) return ''
  
  // Handle different date formats
  let dateObj
  if (typeof date === 'string') {
    dateObj = new Date(date)
  } else if (date instanceof Date) {
    dateObj = date
  } else {
    return ''
  }
  
  // Return in YYYY-MM-DD format for HTML date input
  return dateObj.toISOString().split('T')[0]
}

const loadAsset = () => {
  if (props.asset) {
    Object.assign(formData, {
      id: props.asset.id,
      asset_name: props.asset.asset_name || '',
      asset_type: props.asset.asset_type || '',
      date_of_purchase: formatDateForInput(props.asset.date_of_purchase),
      percentage_used_in_business: props.asset.percentage_used_in_business || '',
      cost_of_acquisition: props.asset.cost_of_acquisition || '',
      any_reimbursement: props.asset.any_reimbursement || '',
      current_value: props.asset.current_value || '',
      depreciation_rate: props.asset.depreciation_rate || '',
      description: props.asset.description || ''
    })
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
    loadAsset()
  }
})

watch(() => props.asset, loadAsset, { deep: true })
</script>