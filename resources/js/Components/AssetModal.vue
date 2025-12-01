<template>
  <div v-if="show" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-start justify-center p-4 pt-8 overflow-y-auto">
    <div class="relative w-full max-w-3xl bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all flex flex-col mb-8">
      <!-- Enhanced Header -->
      <div class="bg-gradient-to-r from-slate-50 via-green-50 to-emerald-50 px-6 py-5 border-b border-gray-200 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-16 bg-gradient-to-bl from-green-100/40 to-transparent rounded-bl-full"></div>
        <div class="relative flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 via-green-900 to-emerald-900 bg-clip-text text-transparent">
                {{ isEditing ? 'Edit Asset' : 'Add New Asset' }}
              </h3>
              <p class="text-sm text-gray-600 font-medium">{{ isEditing ? 'Update asset details and information' : 'Add a new business or personal asset' }}</p>
            </div>
          </div>
          <button
            @click="closeModal"
            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200"
          >
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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-blue-900">Basic Information</h4>
            </div>
            
            <div class="space-y-6">
              <!-- Asset Name and Type -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label for="asset_name" class="block text-sm font-semibold text-blue-700">
                    Asset Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="asset_name"
                    v-model="formData.asset_name"
                    type="text"
                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-900 placeholder-gray-500"
                    placeholder="Enter asset name"
                    required
                  />
                  <p v-if="errors.asset_name" class="mt-1 text-sm text-red-600">{{ errors.asset_name }}</p>
                </div>

                <div class="space-y-2">
                  <label for="asset_type" class="block text-sm font-semibold text-blue-700">
                    Asset Type
                  </label>
                  <select
                    id="asset_type"
                    v-model="formData.asset_type"
                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white text-gray-900"
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
            </div>
          </div>

          <!-- Purchase Information Section -->
          <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-green-900">Purchase Information</h4>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="date_of_purchase" class="block text-sm font-semibold text-green-700">
                  Purchase Date <span class="text-red-500">*</span>
                </label>
                <input
                  id="date_of_purchase"
                  v-model="formData.date_of_purchase"
                  type="date"
                  class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-gray-900"
                  required
                />
                <p v-if="errors.date_of_purchase" class="mt-1 text-sm text-red-600">{{ errors.date_of_purchase }}</p>
              </div>

              <div class="space-y-2">
                <label for="percentage_used_in_business" class="block text-sm font-semibold text-green-700">
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
                    class="w-full px-4 py-3 pr-8 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-gray-900"
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
          </div>

          <!-- Financial Information Section -->
          <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-purple-900">Financial Information</h4>
            </div>
            
            <div class="space-y-6">
              <!-- Cost and Reimbursement -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label for="cost_of_acquisition" class="block text-sm font-semibold text-purple-700">
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
                      class="w-full pl-8 pr-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900"
                      placeholder="0.00"
                      required
                    />
                  </div>
                  <p v-if="errors.cost_of_acquisition" class="mt-1 text-sm text-red-600">{{ errors.cost_of_acquisition }}</p>
                </div>

                <div class="space-y-2">
                  <label for="any_reimbursement" class="block text-sm font-semibold text-purple-700">
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
                      class="w-full pl-8 pr-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900"
                      placeholder="0.00"
                    />
                  </div>
                  <p v-if="errors.any_reimbursement" class="mt-1 text-sm text-red-600">{{ errors.any_reimbursement }}</p>
                </div>
              </div>

              <!-- Current Value and Depreciation -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label for="current_value" class="block text-sm font-semibold text-purple-700">
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
                      class="w-full pl-8 pr-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900"
                      placeholder="0.00"
                    />
                  </div>
                  <p v-if="errors.current_value" class="mt-1 text-sm text-red-600">{{ errors.current_value }}</p>
                </div>

                <div class="space-y-2">
                  <label for="depreciation_rate" class="block text-sm font-semibold text-purple-700">
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
                      class="w-full px-4 py-3 pr-8 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900"
                      placeholder="0.00"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm">%</span>
                    </div>
                  </div>
                  <p v-if="errors.depreciation_rate" class="mt-1 text-sm text-red-600">{{ errors.depreciation_rate }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Additional Details Section -->
          <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-gray-900">Additional Details</h4>
            </div>
            
            <div class="space-y-2">
              <label for="description" class="block text-sm font-semibold text-gray-700">
                Description
              </label>
              <textarea
                id="description"
                v-model="formData.description"
                rows="3"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200 text-gray-900 placeholder-gray-500 resize-none"
                placeholder="Additional details about the asset..."
              ></textarea>
              <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
            </div>
          </div>
        </form>
      </div>

      <!-- Enhanced Actions -->
      <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 bg-white px-8 pb-8">
        <button
          type="button"
          @click="closeModal"
          class="bg-gradient-to-r from-gray-500 to-slate-600 hover:from-gray-600 hover:to-slate-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
          Cancel
        </button>
        <button
          type="button"
          @click="handleSubmit"
          class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          {{ isEditing ? 'Update' : 'Add' }} Asset
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