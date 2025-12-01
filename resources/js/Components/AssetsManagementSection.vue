<template>
  <div class="assets-management-section">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <!-- Section Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">Assets Management</h2>
            <p class="text-sm text-gray-600 mt-1">Track business and personal assets for tax purposes</p>
          </div>
          <div class="flex items-center space-x-2">
            <!-- Total Assets Value -->
            <div class="text-right">
              <p class="text-sm text-gray-500">Total Asset Value</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatCurrency(totalAssetValue) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Assets Information -->
      <div class="p-6">
        <!-- Information Banner -->
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

        <!-- Dynamic Assets Table -->
        <DynamicTable
          v-model="localData"
          :columns="assetColumns"
          :errors="fieldErrors"
          :readonly="readonly"
          title="Asset Details"
          description="Business and personal assets for tax purposes"
          row-label="Asset"
          :empty-icon="CurrencyDollarIcon"
          :default-row-data="defaultAssetData"
          empty-title="No assets"
          empty-description="Get started by adding your first business or personal asset."
          add-button-text="Add Asset"
          @update="handleAssetsUpdate"
          @validate="handleAssetsValidation"
          @row-add="handleAssetAdd"
          @row-remove="handleAssetRemove"
        />

        <!-- Asset Summary -->
        <div v-if="localData.length > 0" class="mt-6 bg-gray-50 rounded-lg p-4">
          <h3 class="text-md font-medium text-gray-900 mb-4">Asset Summary</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total Assets -->
            <div class="bg-white rounded-lg p-4 border border-gray-200">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <CurrencyDollarIcon class="h-8 w-8 text-green-500" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Total Assets</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ localData.length }}</p>
                </div>
              </div>
            </div>

            <!-- Total Value -->
            <div class="bg-white rounded-lg p-4 border border-gray-200">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <BanknotesIcon class="h-8 w-8 text-blue-500" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Total Value</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(totalAssetValue) }}</p>
                </div>
              </div>
            </div>

            <!-- Business Use Value -->
            <div class="bg-white rounded-lg p-4 border border-gray-200">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <BuildingOfficeIcon class="h-8 w-8 text-purple-500" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Business Use Value</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(businessUseValue) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Asset Categories Breakdown -->
          <div v-if="assetCategories.length > 0" class="mt-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Asset Categories</h4>
            <div class="space-y-2">
              <div
                v-for="category in assetCategories"
                :key="category.name"
                class="flex items-center justify-between py-2 px-3 bg-white rounded border border-gray-200"
              >
                <span class="text-sm text-gray-700">{{ category.name }}</span>
                <div class="flex items-center space-x-4">
                  <span class="text-sm text-gray-500">{{ category.count }} assets</span>
                  <span class="text-sm font-medium text-gray-900">{{ formatCurrency(category.value) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tax Implications -->
        <div v-if="localData.length > 0" class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-yellow-800">
                Tax Implications
              </h3>
              <div class="mt-2 text-sm text-yellow-700">
                <ul class="list-disc list-inside space-y-1">
                  <li>Assets used for business may qualify for depreciation deductions</li>
                  <li>Mixed-use assets (personal and business) require accurate percentage tracking</li>
                  <li>Asset sales may result in capital gains or losses</li>
                  <li>Consult with your tax professional for specific asset treatment</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import DynamicTable from './DynamicTable.vue'
import { 
  CurrencyDollarIcon, 
  BanknotesIcon, 
  BuildingOfficeIcon,
  InformationCircleIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

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

// Field validation errors
const fieldErrors = reactive({})

// Default asset data structure
const defaultAssetData = {
  assetName: '',
  dateOfPurchase: '',
  percentageUsedInBusiness: 0,
  costOfAcquisition: 0,
  anyReimbursement: 0,
  category: 'equipment',
  description: ''
}

// Asset table columns configuration
const assetColumns = [
  {
    key: 'assetName',
    label: 'Asset Name',
    type: 'text',
    required: true,
    placeholder: 'Enter asset name',
    maxLength: 100
  },
  {
    key: 'category',
    label: 'Category',
    type: 'select',
    required: true,
    placeholder: 'Select category',
    options: [
      { value: 'equipment', label: 'Equipment' },
      { value: 'furniture', label: 'Furniture' },
      { value: 'vehicle', label: 'Vehicle' },
      { value: 'computer', label: 'Computer/Technology' },
      { value: 'machinery', label: 'Machinery' },
      { value: 'building', label: 'Building/Property' },
      { value: 'software', label: 'Software' },
      { value: 'other', label: 'Other' }
    ]
  },
  {
    key: 'dateOfPurchase',
    label: 'Purchase Date',
    type: 'date',
    required: true
  },
  {
    key: 'costOfAcquisition',
    label: 'Cost of Acquisition',
    type: 'currency',
    required: true,
    placeholder: '0.00',
    validate: (value) => {
      if (value && (isNaN(value) || parseFloat(value) <= 0)) {
        return 'Cost must be a positive number'
      }
      return null
    }
  },
  {
    key: 'percentageUsedInBusiness',
    label: 'Business Use %',
    type: 'percentage',
    required: true,
    placeholder: '0',
    validate: (value) => {
      if (value && (isNaN(value) || parseFloat(value) < 0 || parseFloat(value) > 100)) {
        return 'Percentage must be between 0 and 100'
      }
      return null
    }
  },
  {
    key: 'anyReimbursement',
    label: 'Reimbursement',
    type: 'currency',
    placeholder: '0.00',
    validate: (value, row) => {
      if (value && parseFloat(value) > parseFloat(row.costOfAcquisition || 0)) {
        return 'Reimbursement cannot exceed cost of acquisition'
      }
      return null
    }
  },
  {
    key: 'description',
    label: 'Description',
    type: 'textarea',
    placeholder: 'Additional details about the asset...',
    rows: 2
  }
]

// Computed properties
const totalAssetValue = computed(() => {
  return localData.reduce((total, asset) => {
    const cost = parseFloat(asset.costOfAcquisition) || 0
    const reimbursement = parseFloat(asset.anyReimbursement) || 0
    return total + (cost - reimbursement)
  }, 0)
})

const businessUseValue = computed(() => {
  return localData.reduce((total, asset) => {
    const cost = parseFloat(asset.costOfAcquisition) || 0
    const reimbursement = parseFloat(asset.anyReimbursement) || 0
    const businessPercentage = parseFloat(asset.percentageUsedInBusiness) || 0
    const netValue = cost - reimbursement
    return total + (netValue * businessPercentage / 100)
  }, 0)
})

const assetCategories = computed(() => {
  const categories = {}
  
  localData.forEach(asset => {
    const category = asset.category || 'other'
    const categoryLabel = assetColumns.find(col => col.key === 'category')
      ?.options.find(opt => opt.value === category)?.label || 'Other'
    
    if (!categories[category]) {
      categories[category] = {
        name: categoryLabel,
        count: 0,
        value: 0
      }
    }
    
    categories[category].count++
    const cost = parseFloat(asset.costOfAcquisition) || 0
    const reimbursement = parseFloat(asset.anyReimbursement) || 0
    categories[category].value += (cost - reimbursement)
  })
  
  return Object.values(categories).sort((a, b) => b.value - a.value)
})

// Helper methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount || 0)
}

// Event handlers
const handleAssetsUpdate = (assets) => {
  // Update local data
  localData.splice(0, localData.length, ...assets)
  
  // Emit updates
  emit('update:modelValue', [...assets])
  emit('update', [...assets])
}

const handleAssetsValidation = (errors) => {
  // Update field errors
  Object.keys(fieldErrors).forEach(key => delete fieldErrors[key])
  Object.assign(fieldErrors, errors)
  
  // Emit validation results
  emit('validate', errors)
}

const handleAssetAdd = (newAsset) => {
  // Additional logic when asset is added
  console.log('Asset added:', newAsset)
}

const handleAssetRemove = (removedAsset, index) => {
  // Additional logic when asset is removed
  console.log('Asset removed:', removedAsset, 'at index:', index)
}

// Initialize local data from props
const initializeData = () => {
  // Clear existing data
  localData.splice(0, localData.length)
  
  if (props.modelValue && Array.isArray(props.modelValue)) {
    localData.push(...props.modelValue.map(asset => ({ ...asset })))
  }
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
.assets-management-section {
  @apply max-w-none;
}

/* Summary card styling */
.summary-card {
  @apply transition-shadow duration-200;
}

.summary-card:hover {
  @apply shadow-md;
}
</style>