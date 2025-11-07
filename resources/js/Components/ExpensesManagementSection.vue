<template>
  <div class="expenses-management-section">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <!-- Section Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">Expenses Management</h2>
            <p class="text-sm text-gray-600 mt-1">Track deductible business and personal expenses</p>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Category Filter -->
            <select
              v-model="selectedCategory"
              class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              @change="filterByCategory"
            >
              <option value="">All Categories</option>
              <option value="miscellaneous">Miscellaneous</option>
              <option value="residency">Residency</option>
              <option value="business">Business</option>
              <option value="medical">Medical</option>
              <option value="education">Education</option>
              <option value="charitable">Charitable</option>
            </select>
            <!-- Total Expenses Value -->
            <div class="text-right">
              <p class="text-sm text-gray-500">Total Expenses</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatCurrency(totalExpensesValue) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Expenses Information -->
      <div class="p-6">
        <!-- Information Banner -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <InformationCircleIcon class="h-5 w-5 text-green-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-green-800">
                Expense Tracking Guidelines
              </h3>
              <div class="mt-2 text-sm text-green-700">
                <ul class="list-disc list-inside space-y-1">
                  <li>Keep detailed records and receipts for all business expenses</li>
                  <li>Separate personal and business expenses for accurate deductions</li>
                  <li>Include both taxpayer and spouse expenses when applicable</li>
                  <li>Categorize expenses properly for maximum tax benefits</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Dynamic Expenses Table -->
        <DynamicTable
          v-model="filteredData"
          :columns="expenseColumns"
          :errors="fieldErrors"
          :readonly="readonly"
          title="Expense Details"
          description="Add and manage your deductible expenses"
          row-label="Expense"
          :empty-icon="ReceiptPercentIcon"
          :default-row-data="defaultExpenseData"
          @update="handleExpensesUpdate"
          @validate="handleExpensesValidation"
          @row-add="handleExpenseAdd"
          @row-remove="handleExpenseRemove"
        />

        <!-- Expense Summary -->
        <div v-if="localData.length > 0" class="mt-6 bg-gray-50 rounded-lg p-4">
          <h3 class="text-md font-medium text-gray-900 mb-4">Expense Summary</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Expenses -->
            <div class="bg-white rounded-lg p-4 border border-gray-200">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <ReceiptTaxIcon class="h-8 w-8 text-red-500" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Total Expenses</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ localData.length }}</p>
                </div>
              </div>
            </div>

            <!-- Total Amount -->
            <div class="bg-white rounded-lg p-4 border border-gray-200">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <CurrencyDollarIcon class="h-8 w-8 text-green-500" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Total Amount</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(totalExpensesValue) }}</p>
                </div>
              </div>
            </div>

            <!-- Taxpayer Expenses -->
            <div class="bg-white rounded-lg p-4 border border-gray-200">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <UserIcon class="h-8 w-8 text-blue-500" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Taxpayer</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(taxpayerExpenses) }}</p>
                </div>
              </div>
            </div>

            <!-- Spouse Expenses -->
            <div class="bg-white rounded-lg p-4 border border-gray-200">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <HeartIcon class="h-8 w-8 text-purple-500" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-500">Spouse</p>
                  <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(spouseExpenses) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Expense Categories Breakdown -->
          <div v-if="expenseCategories.length > 0" class="mt-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Expense Categories</h4>
            <div class="space-y-2">
              <div
                v-for="category in expenseCategories"
                :key="category.name"
                class="flex items-center justify-between py-2 px-3 bg-white rounded border border-gray-200"
              >
                <span class="text-sm text-gray-700">{{ category.name }}</span>
                <div class="flex items-center space-x-4">
                  <span class="text-sm text-gray-500">{{ category.count }} expenses</span>
                  <span class="text-sm font-medium text-gray-900">{{ formatCurrency(category.amount) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Monthly Breakdown Chart -->
          <div v-if="monthlyBreakdown.length > 0" class="mt-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Monthly Breakdown</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2">
              <div
                v-for="month in monthlyBreakdown"
                :key="month.month"
                class="bg-white rounded border border-gray-200 p-3 text-center"
              >
                <p class="text-xs text-gray-500">{{ month.month }}</p>
                <p class="text-sm font-medium text-gray-900">{{ formatCurrency(month.amount) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tax Deduction Information -->
        <div v-if="localData.length > 0" class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <ExclamationTriangleIcon class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">
                Tax Deduction Information
              </h3>
              <div class="mt-2 text-sm text-blue-700">
                <ul class="list-disc list-inside space-y-1">
                  <li>Business expenses are generally fully deductible</li>
                  <li>Personal expenses may have limitations or require itemization</li>
                  <li>Medical expenses must exceed 7.5% of AGI to be deductible</li>
                  <li>Charitable contributions have specific documentation requirements</li>
                  <li>Consult with your tax professional for specific deduction rules</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template><script 
setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import DynamicTable from './DynamicTable.vue'
import { 
  ReceiptPercentIcon,
  CurrencyDollarIcon,
  UserIcon,
  HeartIcon,
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
const selectedCategory = ref('')

// Field validation errors
const fieldErrors = reactive({})

// Default expense data structure
const defaultExpenseData = {
  category: 'miscellaneous',
  particulars: '',
  taxPayer: '',
  spouse: '',
  amount: 0,
  date: '',
  remarks: '',
  deductible: true
}

// Expense table columns configuration
const expenseColumns = [
  {
    key: 'category',
    label: 'Category',
    type: 'select',
    required: true,
    placeholder: 'Select category',
    options: [
      { value: 'miscellaneous', label: 'Miscellaneous' },
      { value: 'residency', label: 'Residency' },
      { value: 'business', label: 'Business' },
      { value: 'medical', label: 'Medical' },
      { value: 'education', label: 'Education' },
      { value: 'charitable', label: 'Charitable' },
      { value: 'travel', label: 'Travel' },
      { value: 'office', label: 'Office Supplies' },
      { value: 'professional', label: 'Professional Services' },
      { value: 'other', label: 'Other' }
    ]
  },
  {
    key: 'particulars',
    label: 'Particulars',
    type: 'text',
    required: true,
    placeholder: 'Describe the expense',
    maxLength: 200
  },
  {
    key: 'date',
    label: 'Date',
    type: 'date',
    required: true
  },
  {
    key: 'amount',
    label: 'Amount',
    type: 'currency',
    required: true,
    placeholder: '0.00',
    validate: (value) => {
      if (value && (isNaN(value) || parseFloat(value) <= 0)) {
        return 'Amount must be a positive number'
      }
      return null
    }
  },
  {
    key: 'taxPayer',
    label: 'Taxpayer',
    type: 'text',
    placeholder: 'Taxpayer name',
    maxLength: 100
  },
  {
    key: 'spouse',
    label: 'Spouse',
    type: 'text',
    placeholder: 'Spouse name (if applicable)',
    maxLength: 100
  },
  {
    key: 'deductible',
    label: 'Deductible',
    type: 'select',
    options: [
      { value: true, label: 'Yes' },
      { value: false, label: 'No' }
    ]
  },
  {
    key: 'remarks',
    label: 'Remarks',
    type: 'textarea',
    placeholder: 'Additional notes or comments...',
    rows: 2
  }
]

// Computed properties
const filteredData = computed(() => {
  if (!selectedCategory.value) {
    return localData
  }
  return localData.filter(expense => expense.category === selectedCategory.value)
})

const totalExpensesValue = computed(() => {
  return localData.reduce((total, expense) => {
    return total + (parseFloat(expense.amount) || 0)
  }, 0)
})

const taxpayerExpenses = computed(() => {
  return localData.reduce((total, expense) => {
    if (expense.taxPayer && expense.taxPayer.trim()) {
      return total + (parseFloat(expense.amount) || 0)
    }
    return total
  }, 0)
})

const spouseExpenses = computed(() => {
  return localData.reduce((total, expense) => {
    if (expense.spouse && expense.spouse.trim()) {
      return total + (parseFloat(expense.amount) || 0)
    }
    return total
  }, 0)
})

const expenseCategories = computed(() => {
  const categories = {}
  
  localData.forEach(expense => {
    const category = expense.category || 'other'
    const categoryLabel = expenseColumns.find(col => col.key === 'category')
      ?.options.find(opt => opt.value === category)?.label || 'Other'
    
    if (!categories[category]) {
      categories[category] = {
        name: categoryLabel,
        count: 0,
        amount: 0
      }
    }
    
    categories[category].count++
    categories[category].amount += (parseFloat(expense.amount) || 0)
  })
  
  return Object.values(categories).sort((a, b) => b.amount - a.amount)
})

const monthlyBreakdown = computed(() => {
  const months = {}
  const monthNames = [
    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
  ]
  
  localData.forEach(expense => {
    if (expense.date) {
      const date = new Date(expense.date)
      const monthKey = `${date.getFullYear()}-${date.getMonth()}`
      const monthLabel = `${monthNames[date.getMonth()]} ${date.getFullYear()}`
      
      if (!months[monthKey]) {
        months[monthKey] = {
          month: monthLabel,
          amount: 0
        }
      }
      
      months[monthKey].amount += (parseFloat(expense.amount) || 0)
    }
  })
  
  return Object.values(months).sort((a, b) => {
    const aDate = new Date(a.month)
    const bDate = new Date(b.month)
    return aDate - bDate
  })
})

// Helper methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount || 0)
}

const filterByCategory = () => {
  // The filtering is handled by the computed property filteredData
  // This method can be used for additional logic if needed
}

// Event handlers
const handleExpensesUpdate = (expenses) => {
  // Update local data (only if not filtering)
  if (!selectedCategory.value) {
    localData.splice(0, localData.length, ...expenses)
  } else {
    // Update the original data while maintaining the filter
    const otherCategoryExpenses = localData.filter(expense => expense.category !== selectedCategory.value)
    localData.splice(0, localData.length, ...otherCategoryExpenses, ...expenses)
  }
  
  // Emit updates
  emit('update:modelValue', [...localData])
  emit('update', [...localData])
}

const handleExpensesValidation = (errors) => {
  // Update field errors
  Object.keys(fieldErrors).forEach(key => delete fieldErrors[key])
  Object.assign(fieldErrors, errors)
  
  // Emit validation results
  emit('validate', errors)
}

const handleExpenseAdd = (newExpense) => {
  // Additional logic when expense is added
  console.log('Expense added:', newExpense)
}

const handleExpenseRemove = (removedExpense, index) => {
  // Additional logic when expense is removed
  console.log('Expense removed:', removedExpense, 'at index:', index)
}

// Initialize local data from props
const initializeData = () => {
  // Clear existing data
  localData.splice(0, localData.length)
  
  if (props.modelValue && Array.isArray(props.modelValue)) {
    localData.push(...props.modelValue.map(expense => ({ ...expense })))
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
.expenses-management-section {
  @apply max-w-none;
}

/* Summary card styling */
.summary-card {
  @apply transition-shadow duration-200;
}

.summary-card:hover {
  @apply shadow-md;
}

/* Monthly breakdown styling */
.monthly-item {
  @apply transition-colors duration-200;
}

.monthly-item:hover {
  @apply bg-gray-100;
}
</style>