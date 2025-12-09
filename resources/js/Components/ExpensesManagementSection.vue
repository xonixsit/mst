<template>
  <div class="expenses-management-section">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <!-- Section Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">Expense Details</h2>
            <p class="text-sm text-gray-600 mt-1">Add and manage your deductible expenses</p>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Category Filter -->
            <select
              v-model="selectedCategory"
              class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-gray-900"
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
            <div class="text-right mr-4">
              <p class="text-sm text-gray-500">Total Expenses</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatCurrency(totalExpensesValue) }}</p>
            </div>
            <!-- Add Expense Button -->
            <button @click="openExpenseModal()" :disabled="readonly"
              class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
              <PlusIcon class="w-4 h-4 mr-1" />
              Add Expense
            </button>
          </div>
        </div>
      </div>

      <!-- Expenses Information -->
      <div class="p-6">
        <!-- Expense Information Guidelines -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
          <div class="flex">
            <div class="flex-shrink-0">
              <InformationCircleIcon class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">
                Expense Information Guidelines
              </h3>
              <div class="mt-2 text-sm text-blue-700">
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

        <!-- Empty State -->
        <div v-if="localData.length === 0" class="text-center py-6">
          <ReceiptRefundIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No expenses</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by adding your first deductible expense.</p>
          <div class="mt-6">
            <button @click="openExpenseModal()" :disabled="readonly"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
              <PlusIcon class="w-4 h-4 mr-2" />
              Add Expense
            </button>
          </div>
        </div>

        <!-- Expenses Accordion -->
        <div v-else class="space-y-4">
          <div v-for="(expense, index) in filteredData" :key="expense.id || index"
            class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <!-- Expense Header (Accordion Toggle) -->
            <div class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50"
              @click="toggleExpense(index)">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div :class="getCategoryBadgeClass(expense?.category)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                    {{ formatCategory(expense?.category) }}
                  </div>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-gray-900">
                    {{ expense?.particulars || `Expense ${index + 1}` }}
                  </h3>
                  <p class="text-sm text-gray-500">
                    {{ formatCurrency(expense?.amount) }}
                    <span v-if="expense?.tax_payer">• {{ expense.tax_payer }}</span>
                    <span v-if="expense?.expense_date">• {{ formatDate(expense.expense_date) }}</span>
                  </p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <button @click.stop="openExpenseViewModal(expense)" 
                  class="text-blue-600 hover:text-blue-800">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
                <button @click.stop="openExpenseModal(expense)" :disabled="readonly"
                  class="text-blue-600 hover:text-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button @click.stop="confirmDeleteExpense(index)" :disabled="readonly"
                  class="text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  <TrashIcon class="w-4 h-4" />
                </button>
                <svg
                  :class="['w-5 h-5 text-gray-400 transition-transform', expandedExpenses[index] ? 'rotate-180' : '']"
                  fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>

            <!-- Expense Details (Accordion Content) -->
            <div v-show="expandedExpenses[index]" class="px-4 pb-4 border-t border-gray-100">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                <div>
                  <span class="font-medium text-gray-700">Category:</span>
                  <span class="ml-2 text-gray-900">{{ formatCategory(expense?.category) || 'Not set' }}</span>
                </div>
                
                <div>
                  <span class="font-medium text-gray-700">Amount:</span>
                  <span class="ml-2 text-gray-900">{{ formatCurrency(expense?.amount) || 'Not set' }}</span>
                </div>
                
                <div>
                  <span class="font-medium text-gray-700">Date:</span>
                  <span class="ml-2 text-gray-900">{{ formatDate(expense?.expense_date) || 'Not set' }}</span>
                </div>
                
                <div>
                  <span class="font-medium text-gray-700">Taxpayer:</span>
                  <span class="ml-2 text-gray-900">{{ expense?.tax_payer || 'Not set' }}</span>
                </div>
                
                <div v-if="expense?.spouse">
                  <span class="font-medium text-gray-700">Spouse:</span>
                  <span class="ml-2 text-gray-900">{{ expense.spouse }}</span>
                </div>
                
                <div v-if="expense?.deductible_percentage">
                  <span class="font-medium text-gray-700">Deductible:</span>
                  <span class="ml-2 text-gray-900">{{ expense.deductible_percentage }}%</span>
                </div>
                
                <div v-if="expense?.receipt_number">
                  <span class="font-medium text-gray-700">Receipt #:</span>
                  <span class="ml-2 text-gray-900">{{ expense.receipt_number }}</span>
                </div>
              </div>
              
              <div v-if="expense?.remarks" class="mt-4">
                <span class="font-medium text-gray-700">Remarks:</span>
                <p class="mt-1 text-gray-900">{{ expense.remarks }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Add Expense Button (when expenses exist) -->
        <div v-if="localData.length > 0" class="mt-6 text-center">
          <button @click="openExpenseModal()" :disabled="readonly"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
            <PlusIcon class="w-4 h-4 mr-2" />
            Add Another Expense
          </button>
        </div>

        <!-- Expense Summary with Pie Chart -->
        <div v-if="localData.length > 0" class="mt-6 bg-white rounded-lg border border-gray-200 shadow-sm">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Expense Analytics</h3>
            <p class="text-sm text-gray-600 mt-1">Visual breakdown of your expenses by category</p>
          </div>
          
          <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Pie Chart -->
              <div class="flex flex-col items-center">
                <div class="relative w-80 h-80">
                  <canvas ref="pieChartCanvas" :id="`expense-chart-${Math.random().toString(36).substr(2, 9)}`" class="max-w-full max-h-full"></canvas>
                </div>
                <div class="mt-4 text-center">
                  <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(totalExpensesValue) }}</p>
                  <p class="text-sm text-gray-500">Total Expenses</p>
                </div>
              </div>

              <!-- Summary Stats and Legend -->
              <div class="space-y-6">
                <!-- Key Metrics -->
                <div class="grid grid-cols-2 gap-4">
                  <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ localData.length }}</div>
                    <div class="text-sm text-blue-800">Total Expenses</div>
                  </div>
                  <div class="bg-green-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ expenseCategories.length }}</div>
                    <div class="text-sm text-green-800">Categories</div>
                  </div>
                </div>

                <!-- Category Breakdown -->
                <div>
                  <h4 class="text-md font-semibold text-gray-900 mb-4">Category Breakdown</h4>
                  <div class="space-y-3">
                    <div
                      v-for="(category, index) in expenseCategories"
                      :key="category.name"
                      class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                    >
                      <div class="flex items-center space-x-3">
                        <div 
                          class="w-4 h-4 rounded-full"
                          :style="{ backgroundColor: chartColors[index % chartColors.length] }"
                        ></div>
                        <div>
                          <p class="font-medium text-gray-900">{{ category.name }}</p>
                          <p class="text-sm text-gray-500">{{ category.count }} expense{{ category.count !== 1 ? 's' : '' }}</p>
                        </div>
                      </div>
                      <div class="text-right">
                        <p class="font-semibold text-gray-900">{{ formatCurrency(category.amount) }}</p>
                        <p class="text-sm text-gray-500">{{ ((category.amount / totalExpensesValue) * 100).toFixed(1) }}%</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Taxpayer vs Spouse Breakdown -->
                <div v-if="taxpayerExpenses > 0 || spouseExpenses > 0">
                  <h4 class="text-md font-semibold text-gray-900 mb-4">Taxpayer vs Spouse</h4>
                  <div class="space-y-3">
                    <div v-if="taxpayerExpenses > 0" class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                      <div class="flex items-center space-x-3">
                        <UserIcon class="w-5 h-5 text-blue-600" />
                        <span class="font-medium text-blue-900">Taxpayer</span>
                      </div>
                      <div class="text-right">
                        <p class="font-semibold text-blue-900">{{ formatCurrency(taxpayerExpenses) }}</p>
                        <p class="text-sm text-blue-700">{{ ((taxpayerExpenses / totalExpensesValue) * 100).toFixed(1) }}%</p>
                      </div>
                    </div>
                    <div v-if="spouseExpenses > 0" class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                      <div class="flex items-center space-x-3">
                        <HeartIcon class="w-5 h-5 text-purple-600" />
                        <span class="font-medium text-purple-900">Spouse</span>
                      </div>
                      <div class="text-right">
                        <p class="font-semibold text-purple-900">{{ formatCurrency(spouseExpenses) }}</p>
                        <p class="text-sm text-purple-700">{{ ((spouseExpenses / totalExpensesValue) * 100).toFixed(1) }}%</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Deductible vs Non-Deductible -->
                <div v-if="deductibleExpenses > 0 || nonDeductibleExpenses > 0">
                  <h4 class="text-md font-semibold text-gray-900 mb-4">Tax Deductibility</h4>
                  <div class="space-y-3">
                    <div v-if="deductibleExpenses > 0" class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                      <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="font-medium text-green-900">Deductible</span>
                      </div>
                      <div class="text-right">
                        <p class="font-semibold text-green-900">{{ formatCurrency(deductibleExpenses) }}</p>
                        <p class="text-sm text-green-700">{{ ((deductibleExpenses / totalExpensesValue) * 100).toFixed(1) }}%</p>
                      </div>
                    </div>
                    <div v-if="nonDeductibleExpenses > 0" class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                      <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="font-medium text-red-900">Non-Deductible</span>
                      </div>
                      <div class="text-right">
                        <p class="font-semibold text-red-900">{{ formatCurrency(nonDeductibleExpenses) }}</p>
                        <p class="text-sm text-red-700">{{ ((nonDeductibleExpenses / totalExpensesValue) * 100).toFixed(1) }}%</p>
                      </div>
                    </div>
                  </div>
                </div>
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

    <!-- Expense Modal -->
    <ExpenseModal
      :show="showExpenseModal"
      :expense="selectedExpense"
      :errors="expenseErrors"
      @close="closeExpenseModal"
      @save="saveExpense"
    />

    <!-- Expense View Modal -->
    <ExpenseViewModal
      :show="showExpenseViewModal"
      :expense="selectedExpense"
      @close="closeExpenseViewModal"
      @edit="editFromView"
    />

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeDeleteModal"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <ExclamationTriangleIcon class="h-6 w-6 text-red-600" />
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Delete Expense
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Are you sure you want to delete this expense? This action cannot be undone.
                  </p>
                  <div v-if="expenseToDelete" class="mt-3 p-3 bg-gray-50 rounded-md">
                    <p class="text-sm font-medium text-gray-900">{{ expenseToDelete.expense?.particulars }}</p>
                    <p class="text-sm text-gray-500">{{ formatCurrency(expenseToDelete.expense?.amount) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" @click="deleteExpense"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
              Delete
            </button>
            <button type="button" @click="closeDeleteModal"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import ExpenseTable from './ExpenseTable.vue'
import ExpenseModal from './ExpenseModal.vue'
import ExpenseViewModal from './ExpenseViewModal.vue'
import { 
  ReceiptPercentIcon,
  CurrencyDollarIcon,
  UserIcon,
  HeartIcon,
  InformationCircleIcon,
  ExclamationTriangleIcon,
  ReceiptRefundIcon,
  PlusIcon,
  TrashIcon
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
const expandedExpenses = reactive({})

// Field validation errors
const fieldErrors = reactive({})

// Expense modal state
const showExpenseModal = ref(false)
const showExpenseViewModal = ref(false)
const selectedExpense = ref(null)
const expenseErrors = ref({})

// Delete confirmation modal state
const showDeleteModal = ref(false)
const expenseToDelete = ref(null)

// Chart reference and instance
const pieChartCanvas = ref(null)
let chartInstance = null

// Chart colors for categories
const chartColors = [
  '#3B82F6', // Blue
  '#EF4444', // Red
  '#10B981', // Green
  '#8B5CF6', // Purple
  '#F59E0B', // Yellow
  '#EC4899', // Pink
  '#6366F1', // Indigo
  '#84CC16', // Lime
  '#F97316', // Orange
  '#06B6D4', // Cyan
]

// Default expense data structure
const defaultExpenseData = {
  category: 'miscellaneous',
  particulars: '',
  taxPayer: '',
  spouse: '',
  amount: 0,
  date: '',
  receiptNumber: '',
  deductiblePercentage: 100,
  remarks: ''
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
    key: 'receiptNumber',
    label: 'Receipt Number',
    type: 'text',
    placeholder: 'Receipt/Invoice number',
    maxLength: 255
  },
  {
    key: 'deductiblePercentage',
    label: 'Deductible %',
    type: 'percentage',
    placeholder: '0-100',
    validate: (value) => {
      if (value && (isNaN(value) || parseFloat(value) < 0 || parseFloat(value) > 100)) {
        return 'Percentage must be between 0 and 100'
      }
      return null
    }
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

const deductibleExpenses = computed(() => {
  return localData.reduce((total, expense) => {
    const amount = parseFloat(expense.amount) || 0
    const deductiblePercentage = parseFloat(expense.deductible_percentage) || 0
    return total + (amount * (deductiblePercentage / 100))
  }, 0)
})

const nonDeductibleExpenses = computed(() => {
  return totalExpensesValue.value - deductibleExpenses.value
})

const chartData = computed(() => {
  if (expenseCategories.value.length === 0) return null
  
  return {
    labels: expenseCategories.value.map(cat => cat.name),
    datasets: [{
      data: expenseCategories.value.map(cat => cat.amount),
      backgroundColor: chartColors.slice(0, expenseCategories.value.length),
      borderWidth: 2,
      borderColor: '#ffffff',
      hoverBorderWidth: 3,
      hoverBorderColor: '#ffffff'
    }]
  }
})

// Helper methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount || 0)
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatCategory = (category) => {
  if (!category) return ''
  return category.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}

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
    residency: 'bg-orange-100 text-orange-800'
  }
  return classes[category] || 'bg-gray-100 text-gray-800'
}

const toggleExpense = (index) => {
  expandedExpenses[index] = !expandedExpenses[index]
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

// Expense modal methods
const openExpenseModal = (expense = null) => {
  selectedExpense.value = expense
  expenseErrors.value = {}
  showExpenseModal.value = true
}

const closeExpenseModal = () => {
  showExpenseModal.value = false
  selectedExpense.value = null
  expenseErrors.value = {}
}

const openExpenseViewModal = (expense) => {
  selectedExpense.value = expense
  showExpenseViewModal.value = true
}

const closeExpenseViewModal = () => {
  showExpenseViewModal.value = false
  selectedExpense.value = null
}

const editFromView = (expense) => {
  closeExpenseViewModal()
  openExpenseModal(expense)
}

const saveExpense = (expenseData) => {
  // Validate expense data
  const errors = validateExpense(expenseData)
  
  if (Object.keys(errors).length > 0) {
    expenseErrors.value = errors
    return
  }

  if (expenseData.id) {
    // Update existing expense
    const index = localData.findIndex(exp => exp.id === expenseData.id)
    if (index !== -1) {
      localData[index] = { ...expenseData }
    }
  } else {
    // Add new expense
    const newExpense = {
      ...expenseData,
      id: Date.now() // Temporary ID for frontend
    }
    localData.push(newExpense)
  }

  // Emit updates
  emit('update:modelValue', [...localData])
  emit('update', [...localData])
  
  // Update chart
  setTimeout(() => {
    updateChart()
  }, 50)
  
  closeExpenseModal()
}

const confirmDeleteExpense = (index) => {
  expenseToDelete.value = { expense: localData[index], index }
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  expenseToDelete.value = null
}

const deleteExpense = () => {
  if (expenseToDelete.value) {
    localData.splice(expenseToDelete.value.index, 1)
    
    // Emit updates
    emit('update:modelValue', [...localData])
    emit('update', [...localData])
    
    // Update chart
    setTimeout(() => {
      updateChart()
    }, 50)
  }
  
  closeDeleteModal()
}

const validateExpense = (expenseData) => {
  const errors = {}

  if (!expenseData.category) {
    errors.category = 'Category is required'
  }

  if (!expenseData.particulars || expenseData.particulars.trim().length === 0) {
    errors.particulars = 'Description is required'
  }

  if (!expenseData.date) {
    errors.date = 'Date is required'
  }

  if (!expenseData.amount || parseFloat(expenseData.amount) <= 0) {
    errors.amount = 'Amount must be greater than 0'
  }

  if (!expenseData.taxPayer) {
    errors.taxPayer = 'Tax payer is required'
  }

  return errors
}

// Chart.js methods
const initializeChart = async () => {
  if (!pieChartCanvas.value || !chartData.value || chartData.value.datasets[0].data.length === 0) return
  
  try {
    // Dynamically import Chart.js with all required components
    const { Chart, ArcElement, Tooltip, Legend, DoughnutController } = await import('chart.js')
    Chart.register(ArcElement, Tooltip, Legend, DoughnutController)
    
    const ctx = pieChartCanvas.value.getContext('2d')
    
    // Destroy existing chart if it exists
    if (chartInstance) {
      chartInstance.destroy()
      chartInstance = null
    }
    
    chartInstance = new Chart(ctx, {
      type: 'doughnut',
      data: chartData.value,
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false // We'll use custom legend
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const label = context.label || ''
                const value = context.parsed
                const total = context.dataset.data.reduce((a, b) => a + b, 0)
                const percentage = ((value / total) * 100).toFixed(1)
                return `${label}: ${formatCurrency(value)} (${percentage}%)`
              }
            },
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#ffffff',
            borderWidth: 1
          }
        },
        cutout: '60%',
        animation: {
          animateRotate: true,
          duration: 1000
        }
      }
    })
  } catch (error) {
    console.error('Error initializing chart:', error)
  }
}

const updateChart = async () => {
  if (!chartData.value || chartData.value.datasets[0].data.length === 0) {
    if (chartInstance) {
      chartInstance.destroy()
      chartInstance = null
    }
    return
  }
  
  if (chartInstance && chartInstance.canvas) {
    try {
      chartInstance.data = chartData.value
      chartInstance.update('active')
    } catch (error) {
      console.error('Error updating chart:', error)
      // If update fails, reinitialize
      if (chartInstance) {
        chartInstance.destroy()
        chartInstance = null
      }
      await nextTick()
      initializeChart()
    }
  } else {
    await nextTick()
    initializeChart()
  }
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
watch(() => props.modelValue, async () => {
  initializeData()
  await nextTick()
  updateChart()
}, { deep: true })

// Watch for chart data changes
watch(chartData, async () => {
  await nextTick()
  updateChart()
}, { deep: true })

// Initialize on mount
onMounted(async () => {
  initializeData()
  await nextTick()
  if (localData.length > 0) {
    setTimeout(() => {
      initializeChart()
    }, 100) // Small delay to ensure DOM is ready
  }
})

// Cleanup on unmount
onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy()
    chartInstance = null
  }
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