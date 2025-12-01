<template>
  <div class="asset-details-section">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <!-- Section Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">Asset Details</h2>
            <p class="text-sm text-gray-600 mt-1">Business and personal assets for tax purposes</p>
          </div>
          <button @click="addAsset" :disabled="readonly"
            class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-4 py-2 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center">
            <PlusIcon class="w-4 h-4 mr-2" />
            Add Asset
          </button>
        </div>
      </div>

      <!-- Assets List -->
      <div class="p-6">
        <!-- Asset Information Guidelines -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <InformationCircleIcon class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">
                Asset Information Guidelines
              </h3>
              <div class="mt-2 text-sm text-blue-700">
                <ul class="list-disc list-inside space-y-1">
                  <li>Include all business-related assets and equipment</li>
                  <li>Percentage used in business affects tax deductions</li>
                  <li>Keep receipts and documentation for all asset purchases</li>
                  <li>Reimbursements reduce the deductible amount</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- Empty State -->
        <div v-if="localData.length === 0" class="text-center py-12">
          <BuildingOfficeIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No assets</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by adding your first business or personal asset.</p>
          <div class="mt-6">
            <button @click="addAsset" :disabled="readonly"
              class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none inline-flex items-center">
              <PlusIcon class="w-4 h-4 mr-2" />
              Add Asset
            </button>
          </div>
        </div>

        <!-- Assets Accordion -->
        <div v-else class="space-y-4">
          <div v-for="(asset, index) in localData" :key="asset.id || index"
            class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <!-- Asset Header (Accordion Toggle) -->
            <div class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50"
              @click="toggleAsset(index)">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div :class="getAssetTypeBadgeClass(asset?.asset_type)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                    {{ formatAssetType(asset?.asset_type) }}
                  </div>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-gray-900">
                    {{ asset?.asset_name || `Asset ${index + 1}` }}
                  </h3>
                  <p class="text-sm text-gray-500">
                    {{ formatCurrency(asset?.cost_of_acquisition) }}
                    <span v-if="asset?.percentage_used_in_business">• {{ asset.percentage_used_in_business }}% business use</span>
                    <span v-if="asset?.date_of_purchase">• Purchased {{ formatDate(asset.date_of_purchase) }}</span>
                  </p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <button @click.stop="editAsset(asset)" :disabled="readonly"
                  class="text-blue-600 hover:text-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button @click.stop="confirmDeleteAsset(index)" :disabled="readonly"
                  class="text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  <TrashIcon class="w-4 h-4" />
                </button>
                <svg
                  :class="['w-5 h-5 text-gray-400 transition-transform', expandedAssets[index] ? 'rotate-180' : '']"
                  fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>

            <!-- Asset Details (Accordion Content) -->
            <div v-if="expandedAssets[index]" class="border-t border-gray-200 p-4 bg-gray-50">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                <!-- Asset Type -->
                <div>
                  <span class="font-medium text-gray-700">Type:</span>
                  <span class="ml-2 text-gray-900 capitalize">{{ formatAssetType(asset?.asset_type) || 'Not specified' }}</span>
                </div>
                
                <!-- Purchase Date -->
                <div>
                  <span class="font-medium text-gray-700">Purchase Date:</span>
                  <span class="ml-2 text-gray-900">{{ formatDate(asset?.date_of_purchase) || 'Not set' }}</span>
                </div>
                
                <!-- Business Use -->
                <div>
                  <span class="font-medium text-gray-700">Business Use:</span>
                  <span class="ml-2 text-gray-900">{{ asset?.percentage_used_in_business || 0 }}%</span>
                </div>
                
                <!-- Cost of Acquisition -->
                <div>
                  <span class="font-medium text-gray-700">Acquisition Cost:</span>
                  <span class="ml-2 text-gray-900">{{ formatCurrency(asset?.cost_of_acquisition) }}</span>
                </div>
                
                <!-- Reimbursement -->
                <div>
                  <span class="font-medium text-gray-700">Reimbursement:</span>
                  <span class="ml-2 text-gray-900">{{ formatCurrency(asset?.any_reimbursement) || 'None' }}</span>
                </div>
                
                <!-- Net Cost -->
                <div>
                  <span class="font-medium text-gray-700">Net Cost:</span>
                  <span class="ml-2 text-gray-900">{{ calculateNetCost(asset) }}</span>
                </div>
                
                <!-- Business Cost -->
                <div>
                  <span class="font-medium text-gray-700">Business Cost:</span>
                  <span class="ml-2 text-gray-900">{{ calculateBusinessCost(asset) }}</span>
                </div>
                
                <!-- Personal Cost -->
                <div>
                  <span class="font-medium text-gray-700">Personal Cost:</span>
                  <span class="ml-2 text-gray-900">{{ calculatePersonalCost(asset) }}</span>
                </div>
                
                <!-- Current Value -->
                <div>
                  <span class="font-medium text-gray-700">Current Value:</span>
                  <span class="ml-2 text-gray-900">{{ formatCurrency(asset?.current_value) || 'Not set' }}</span>
                </div>
                
                <!-- Depreciation Rate -->
                <div>
                  <span class="font-medium text-gray-700">Depreciation Rate:</span>
                  <span class="ml-2 text-gray-900">{{ asset?.depreciation_rate ? `${asset.depreciation_rate}%` : 'Not set' }}</span>
                </div>
                
                <!-- Age -->
                <div>
                  <span class="font-medium text-gray-700">Age:</span>
                  <span class="ml-2 text-gray-900">{{ calculateAssetAge(asset) }}</span>
                </div>
                
                <!-- Value Change -->
                <div v-if="asset?.current_value && asset?.cost_of_acquisition">
                  <span class="font-medium text-gray-700">Value Change:</span>
                  <span :class="getValueChangeClass(asset)" class="ml-2">
                    {{ getValueChange(asset) }}
                  </span>
                </div>
                <div v-else>
                  <span class="font-medium text-gray-700">Value Change:</span>
                  <span class="ml-2 text-gray-500">Not available</span>
                </div>
              </div>
              
              <!-- Description -->
              <div class="mt-4">
                <span class="font-medium text-gray-700">Description:</span>
                <p class="mt-1 text-gray-900">{{ asset?.description || 'No description provided' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Asset Modal -->
  <AssetModal :show="showAssetModal" :asset="selectedAsset" :errors="assetErrors" @close="closeAssetModal"
    @save="saveAsset" />

  <!-- Delete Confirmation Modal -->
  <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeDeleteModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Delete Asset
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Are you sure you want to delete this asset? This action cannot be undone and will permanently remove the asset from your records.
                </p>
                <div v-if="assetToDelete !== null && localData[assetToDelete]" class="mt-3 p-3 bg-gray-50 rounded-md">
                  <p class="text-sm font-medium text-gray-900">{{ localData[assetToDelete].asset_name }}</p>
                  <p class="text-sm text-gray-600">{{ formatCurrency(localData[assetToDelete].cost_of_acquisition) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            type="button"
            @click="removeAsset(assetToDelete)"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Delete Asset
          </button>
          <button
            type="button"
            @click="closeDeleteModal"
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
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { PlusIcon, TrashIcon, BuildingOfficeIcon, InformationCircleIcon } from '@heroicons/vue/24/outline'
import AssetModal from './AssetModal.vue'

// Props
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  readonly: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['update:modelValue', 'update', 'validate'])

// Local reactive data
const localData = reactive([])
const expandedAssets = reactive({})

// Modal state
const showAssetModal = ref(false)
const selectedAsset = ref(null)
const assetErrors = ref({})

// Delete confirmation modal state
const showDeleteModal = ref(false)
const assetToDelete = ref(null)

// Field validation errors
const fieldErrors = reactive({})

// Helper methods
const formatCurrency = (amount) => {
  if (!amount || amount === 0) return '$0.00'
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatAssetType = (type) => {
  if (!type) return ''
  return type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const getAssetTypeBadgeClass = (type) => {
  const classes = {
    equipment: 'bg-blue-100 text-blue-800',
    vehicle: 'bg-green-100 text-green-800',
    furniture: 'bg-purple-100 text-purple-800',
    computer: 'bg-indigo-100 text-indigo-800',
    machinery: 'bg-orange-100 text-orange-800',
    building: 'bg-red-100 text-red-800',
    land: 'bg-yellow-100 text-yellow-800',
    other: 'bg-gray-100 text-gray-800'
  }
  return classes[type] || classes.other
}

const calculateNetCost = (asset) => {
  if (!asset?.cost_of_acquisition) return '$0.00'
  const cost = parseFloat(asset.cost_of_acquisition)
  const reimbursement = parseFloat(asset.any_reimbursement || 0)
  return formatCurrency(cost - reimbursement)
}

const calculateBusinessCost = (asset) => {
  if (!asset?.cost_of_acquisition || !asset?.percentage_used_in_business) return '$0.00'
  const cost = parseFloat(asset.cost_of_acquisition)
  const businessPercent = parseFloat(asset.percentage_used_in_business) / 100
  return formatCurrency(cost * businessPercent)
}

const calculatePersonalCost = (asset) => {
  if (!asset?.cost_of_acquisition || !asset?.percentage_used_in_business) return formatCurrency(asset?.cost_of_acquisition || 0)
  const cost = parseFloat(asset.cost_of_acquisition)
  const personalPercent = (100 - parseFloat(asset.percentage_used_in_business)) / 100
  return formatCurrency(cost * personalPercent)
}

const calculateAssetAge = (asset) => {
  if (!asset?.date_of_purchase) return 'Unknown'
  const purchaseDate = new Date(asset.date_of_purchase)
  const today = new Date()
  const ageInYears = Math.floor((today - purchaseDate) / (365.25 * 24 * 60 * 60 * 1000))
  const ageInMonths = Math.floor((today - purchaseDate) / (30.44 * 24 * 60 * 60 * 1000))
  
  if (ageInYears > 0) {
    return `${ageInYears} year${ageInYears !== 1 ? 's' : ''}`
  } else if (ageInMonths > 0) {
    return `${ageInMonths} month${ageInMonths !== 1 ? 's' : ''}`
  } else {
    return 'Less than a month'
  }
}

const getValueChange = (asset) => {
  if (!asset?.current_value || !asset?.cost_of_acquisition) return 'N/A'
  const current = parseFloat(asset.current_value)
  const original = parseFloat(asset.cost_of_acquisition)
  const change = current - original
  const percentage = ((change / original) * 100).toFixed(1)
  return `${formatCurrency(change)} (${change >= 0 ? '+' : ''}${percentage}%)`
}

const getValueChangeClass = (asset) => {
  if (!asset?.current_value || !asset?.cost_of_acquisition) return 'text-gray-600'
  const current = parseFloat(asset.current_value)
  const original = parseFloat(asset.cost_of_acquisition)
  return current >= original ? 'text-green-600' : 'text-red-600'
}

// Asset management methods
const addAsset = () => {
  if (props.readonly) return
  selectedAsset.value = null
  assetErrors.value = {}
  showAssetModal.value = true
}

const editAsset = (asset) => {
  if (props.readonly) return
  selectedAsset.value = asset
  assetErrors.value = {}
  showAssetModal.value = true
}

const closeAssetModal = () => {
  showAssetModal.value = false
  selectedAsset.value = null
  assetErrors.value = {}
}

const saveAsset = (assetData) => {
  // Validate asset data
  const errors = validateAsset(assetData)
  if (Object.keys(errors).length > 0) {
    assetErrors.value = errors
    return
  }

  if (assetData.id && localData.find(a => a.id === assetData.id)) {
    // Update existing asset
    const index = localData.findIndex(a => a.id === assetData.id)
    if (index !== -1) {
      localData[index] = { ...assetData }
    }
  } else {
    // Add new asset
    const newAsset = {
      ...assetData,
      id: Date.now() // Temporary ID for frontend
    }
    localData.push(newAsset)
  }

  emitUpdate()
  closeAssetModal()
}

const validateAsset = (assetData) => {
  const errors = {}

  if (!assetData.asset_name || assetData.asset_name.trim().length === 0) {
    errors.asset_name = 'Asset name is required'
  }

  if (!assetData.date_of_purchase) {
    errors.date_of_purchase = 'Purchase date is required'
  }

  if (!assetData.percentage_used_in_business && assetData.percentage_used_in_business !== 0) {
    errors.percentage_used_in_business = 'Business use percentage is required'
  } else if (assetData.percentage_used_in_business < 0 || assetData.percentage_used_in_business > 100) {
    errors.percentage_used_in_business = 'Business use percentage must be between 0 and 100'
  }

  if (!assetData.cost_of_acquisition || assetData.cost_of_acquisition <= 0) {
    errors.cost_of_acquisition = 'Cost of acquisition is required and must be greater than 0'
  }

  return errors
}

const toggleAsset = (index) => {
  expandedAssets[index] = !expandedAssets[index]
}

const confirmDeleteAsset = (index) => {
  if (props.readonly) return
  assetToDelete.value = index
  showDeleteModal.value = true
}

const removeAsset = (index) => {
  if (props.readonly) return

  localData.splice(index, 1)

  // Clean up errors for removed asset
  Object.keys(fieldErrors).forEach(key => {
    if (key.startsWith(`assets.${index}.`)) {
      delete fieldErrors[key]
    }
  })

  // Reindex errors for remaining assets
  const newErrors = {}
  Object.keys(fieldErrors).forEach(key => {
    const match = key.match(/^assets\.(\d+)\.(.+)$/)
    if (match) {
      const errorIndex = parseInt(match[1])
      const field = match[2]
      if (errorIndex > index) {
        newErrors[`assets.${errorIndex - 1}.${field}`] = fieldErrors[key]
      } else if (errorIndex < index) {
        newErrors[key] = fieldErrors[key]
      }
    } else {
      newErrors[key] = fieldErrors[key]
    }
  })

  Object.keys(fieldErrors).forEach(key => delete fieldErrors[key])
  Object.assign(fieldErrors, newErrors)

  emitUpdate()
  closeDeleteModal()
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  assetToDelete.value = null
}

const formatDateForBackend = (date) => {
  if (!date) return null
  
  try {
    let dateObj
    
    // Handle different date formats
    if (typeof date === 'string') {
      // If it's already in YYYY-MM-DD format, return as is
      if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
        return date
      }
      dateObj = new Date(date)
    } else if (date instanceof Date) {
      dateObj = date
    } else {
      return null
    }
    
    // Check if the date is valid
    if (isNaN(dateObj.getTime())) {
      console.warn('Invalid date provided:', date)
      return null
    }
    
    // Return in YYYY-MM-DD format for backend validation
    const year = dateObj.getFullYear()
    const month = String(dateObj.getMonth() + 1).padStart(2, '0')
    const day = String(dateObj.getDate()).padStart(2, '0')
    
    return `${year}-${month}-${day}`
  } catch (error) {
    console.error('Error formatting date for backend:', error, date)
    return null
  }
}

const emitUpdate = () => {
  const dataToEmit = localData.map(asset => ({
    ...asset,
    date_of_purchase: formatDateForBackend(asset.date_of_purchase)
  }))
  emit('update:modelValue', dataToEmit)
  emit('update', dataToEmit)
}

// Initialize local data from props
const initializeData = () => {
  // Clear existing data
  localData.splice(0, localData.length)

  if (props.modelValue && Array.isArray(props.modelValue)) {
    props.modelValue.forEach(asset => {
      localData.push({ ...asset })
    })
  }

  // Initialize expanded state for existing assets
  localData.forEach((_, index) => {
    if (expandedAssets[index] === undefined) {
      expandedAssets[index] = false
    }
  })
}

// Watch for external errors
watch(() => props.errors, (newErrors) => {
  Object.keys(fieldErrors).forEach(key => delete fieldErrors[key])
  Object.assign(fieldErrors, newErrors)
}, { deep: true })

// Watch for prop changes
watch(() => props.modelValue, initializeData, { deep: true })

// Initialize on mount
onMounted(() => {
  initializeData()
})
</script>

<style scoped>
.asset-details-section {
  @apply max-w-none;
}

/* Custom styles for form elements */
input:focus,
select:focus,
textarea:focus {
  @apply ring-2 ring-blue-500 ring-opacity-50;
}

/* Disabled state styling */
input:disabled,
select:disabled,
textarea:disabled {
  @apply cursor-not-allowed;
}

/* Asset card styling */
.asset-card {
  transition: all 0.2s ease-in-out;
}

.asset-card:hover {
  @apply shadow-md;
}
</style>