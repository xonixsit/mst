<template>
  <div class="expense-table">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-4">
      <div>
        <h3 class="text-lg font-medium text-gray-900">Expense Details</h3>
        <p class="text-sm text-gray-600">Add and manage your deductible expenses</p>
      </div>
      <button
        @click="openAddModal"
        type="button"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Add Expense
      </button>
    </div>

    <!-- Expenses Table -->
    <div v-if="expenses.length > 0" class="bg-white shadow overflow-hidden sm:rounded-md">
      <ul class="divide-y divide-gray-200">
        <li v-for="(expense, index) in expenses" :key="expense.id || index" class="px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
              <div class="flex items-center space-x-4">
                <!-- Category Badge -->
                <span :class="getCategoryBadgeClass(expense.category)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                  {{ expense.category }}
                </span>
                
                <!-- Expense Details -->
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-900 truncate">
                    {{ expense.particulars }}
                  </p>
                  <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span>{{ formatDate(expense.date) }}</span>
                    <span>{{ expense.taxPayer }}</span>
                    <span v-if="expense.receiptNumber" class="text-xs bg-gray-100 px-2 py-1 rounded">
                      Receipt: {{ expense.receiptNumber }}
                    </span>
                  </div>
                </div>
                
                <!-- Amount -->
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900">
                    ${{ formatAmount(expense.amount) }}
                  </p>
                  <p v-if="expense.is_deductible" class="text-xs text-green-600">
                    {{ expense.deductiblePercentage }}% deductible
                  </p>
                  <p v-else class="text-xs text-gray-500">
                    Not deductible
                  </p>
                </div>
              </div>
              
              <!-- Remarks (if any) -->
              <div v-if="expense.remarks" class="mt-2">
                <p class="text-sm text-gray-600 italic">{{ expense.remarks }}</p>
              </div>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center space-x-2 ml-4">
              <button
                @click="viewExpense(expense)"
                class="text-blue-600 hover:text-blue-900 text-sm font-medium"
              >
                View
              </button>
              <button
                @click="editExpense(expense)"
                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
              >
                Edit
              </button>
              <button
                @click="deleteExpense(expense, index)"
                class="text-red-600 hover:text-red-900 text-sm font-medium"
              >
                Delete
              </button>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No expenses</h3>
      <p class="mt-1 text-sm text-gray-500">Get started by adding your first expense.</p>
      <div class="mt-6">
        <button
          @click="openAddModal"
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Add Expense
        </button>
      </div>
    </div>

    <!-- Summary -->
    <div v-if="expenses.length > 0" class="mt-4 bg-gray-50 rounded-lg p-4">
      <div class="flex justify-between items-center text-sm">
        <span class="text-gray-600">{{ expenses.length }} expense{{ expenses.length !== 1 ? 's' : '' }}</span>
        <div class="space-x-4">
          <span class="text-gray-600">Total: <span class="font-medium text-gray-900">${{ formatAmount(totalAmount) }}</span></span>
          <span class="text-green-600">Deductible: <span class="font-medium">${{ formatAmount(deductibleAmount) }}</span></span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  expenses: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['add', 'edit', 'view', 'delete'])

const totalAmount = computed(() => {
  return props.expenses.reduce((sum, expense) => sum + parseFloat(expense.amount || 0), 0)
})

const deductibleAmount = computed(() => {
  return props.expenses.reduce((sum, expense) => {
    if (expense.is_deductible) {
      const amount = parseFloat(expense.amount || 0)
      const percentage = parseFloat(expense.deductiblePercentage || 100) / 100
      return sum + (amount * percentage)
    }
    return sum
  }, 0)
})

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
    month: 'short',
    day: 'numeric'
  })
}

const formatAmount = (amount) => {
  return parseFloat(amount || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const openAddModal = () => {
  emit('add')
}

const viewExpense = (expense) => {
  emit('view', expense)
}

const editExpense = (expense) => {
  emit('edit', expense)
}

const deleteExpense = (expense, index) => {
  if (confirm('Are you sure you want to delete this expense?')) {
    emit('delete', expense, index)
  }
}
</script>