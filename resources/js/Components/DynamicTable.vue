<template>
  <div class="dynamic-table">
    <!-- Table Header -->
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
        <p v-if="description" class="text-sm text-gray-600 mt-1">{{ description }}</p>
      </div>
      <button
        @click="addRow"
        :disabled="readonly"
        class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-4 py-2 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center"
      >
        <PlusIcon class="w-4 h-4 mr-2" />
        Add {{ rowLabel || 'Row' }}
      </button>
    </div>

    <!-- Empty State -->
    <div v-if="localData.length === 0" class="text-center py-6">
      <component :is="emptyIcon" class="mx-auto h-12 w-12 text-gray-400" />
      <h3 class="mt-2 text-sm font-medium text-gray-900">{{ emptyTitle || `No ${rowLabel?.toLowerCase() || 'items'}` }}</h3>
      <p class="mt-1 text-sm text-gray-500">{{ emptyDescription || `Get started by adding your first ${rowLabel?.toLowerCase() || 'item'}.` }}</p>
      <div class="mt-6 flex justify-center">
        <button
          @click="addRow"
          :disabled="readonly"
          class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none inline-flex items-center"
        >
          <PlusIcon class="w-4 h-4 mr-2" />
          {{ addButtonText || `Add ${rowLabel || 'Row'}` }}
        </button>
      </div>
    </div>

    <!-- Table -->
    <div v-else class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
      <table class="min-w-full divide-y divide-gray-300">
        <!-- Table Header -->
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              :class="column.headerClass"
            >
              {{ column.label }}
              <span v-if="column.required" class="text-red-500 ml-1">*</span>
            </th>
            <th scope="col" class="relative px-6 py-3">
              <span class="sr-only">Actions</span>
            </th>
          </tr>
        </thead>

        <!-- Table Body -->
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(row, rowIndex) in localData"
            :key="row.id || rowIndex"
            class="hover:bg-gray-50"
          >
            <!-- Dynamic Columns -->
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-4 whitespace-nowrap"
              :class="column.cellClass"
            >
              <!-- Text Input -->
              <input
                v-if="column.type === 'text'"
                v-model="row[column.key]"
                :type="column.inputType || 'text'"
                :placeholder="column.placeholder"
                :class="getInputClasses(rowIndex, column.key)"
                :disabled="readonly"
                :step="column.step"
                :min="column.min"
                :max="column.max"
                @blur="validateCell(rowIndex, column.key)"
                @input="handleCellInput(rowIndex, column.key, $event.target.value)"
              />

              <!-- Select Dropdown -->
              <select
                v-else-if="column.type === 'select'"
                v-model="row[column.key]"
                :class="getInputClasses(rowIndex, column.key)"
                :disabled="readonly"
                @change="handleCellInput(rowIndex, column.key, $event.target.value)"
              >
                <option value="">{{ column.placeholder || 'Select...' }}</option>
                <option
                  v-for="option in column.options"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>

              <!-- Date Input -->
              <input
                v-else-if="column.type === 'date'"
                v-model="row[column.key]"
                type="date"
                :class="getInputClasses(rowIndex, column.key)"
                :disabled="readonly"
                @blur="validateCell(rowIndex, column.key)"
                @input="handleCellInput(rowIndex, column.key, $event.target.value)"
              />

              <!-- Number Input with Currency -->
              <div
                v-else-if="column.type === 'currency'"
                class="relative"
              >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input
                  v-model="row[column.key]"
                  type="number"
                  step="0.01"
                  min="0"
                  :placeholder="column.placeholder"
                  :class="getCurrencyInputClasses(rowIndex, column.key)"
                  :disabled="readonly"
                  @blur="validateCell(rowIndex, column.key)"
                  @input="handleCellInput(rowIndex, column.key, $event.target.value)"
                />
              </div>

              <!-- Percentage Input -->
              <div
                v-else-if="column.type === 'percentage'"
                class="relative"
              >
                <input
                  v-model="row[column.key]"
                  type="number"
                  step="0.01"
                  min="0"
                  max="100"
                  :placeholder="column.placeholder"
                  :class="getInputClasses(rowIndex, column.key)"
                  :disabled="readonly"
                  @blur="validateCell(rowIndex, column.key)"
                  @input="handleCellInput(rowIndex, column.key, $event.target.value)"
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <span class="text-gray-500 sm:text-sm">%</span>
                </div>
              </div>

              <!-- Textarea -->
              <textarea
                v-else-if="column.type === 'textarea'"
                v-model="row[column.key]"
                :rows="column.rows || 2"
                :placeholder="column.placeholder"
                :class="getInputClasses(rowIndex, column.key)"
                :disabled="readonly"
                @input="handleCellInput(rowIndex, column.key, $event.target.value)"
              ></textarea>

              <!-- Error Message -->
              <p
                v-if="getCellError(rowIndex, column.key)"
                class="mt-1 text-sm text-red-600"
              >
                {{ getCellError(rowIndex, column.key) }}
              </p>
            </td>

            <!-- Actions Column -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="removeRow(rowIndex)"
                :disabled="readonly"
                class="text-red-600 hover:text-red-900 disabled:opacity-50 disabled:cursor-not-allowed"
                :title="`Remove ${rowLabel?.toLowerCase() || 'row'}`"
              >
                <TrashIcon class="w-5 h-5" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Table Footer -->
    <div v-if="localData.length > 0" class="mt-4 flex items-center justify-between text-sm text-gray-500">
      <span>{{ localData.length }} {{ localData.length === 1 ? (rowLabel?.toLowerCase() || 'item') : (rowLabel?.toLowerCase() + 's' || 'items') }}</span>
      <button
        @click="addRow"
        :disabled="readonly"
        class="inline-flex items-center text-blue-600 hover:text-blue-800 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <PlusIcon class="w-4 h-4 mr-1" />
        Add another {{ rowLabel?.toLowerCase() || 'row' }}
      </button>
    </div>
  </div>
</template>
<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  columns: {
    type: Array,
    required: true,
    validator: (columns) => {
      return columns.every(col => col.key && col.label && col.type)
    }
  },
  title: {
    type: String,
    default: 'Dynamic Table'
  },
  description: {
    type: String,
    default: ''
  },
  rowLabel: {
    type: String,
    default: 'Item'
  },
  emptyIcon: {
    type: [String, Object],
    default: () => TrashIcon
  },
  emptyTitle: {
    type: String,
    default: ''
  },
  emptyDescription: {
    type: String,
    default: ''
  },
  addButtonText: {
    type: String,
    default: ''
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  readonly: {
    type: Boolean,
    default: false
  },
  defaultRowData: {
    type: Object,
    default: () => ({})
  }
})

// Emits
const emit = defineEmits(['update:modelValue', 'update', 'validate', 'row-add', 'row-remove'])

// Local reactive data
const localData = reactive([])

// Field validation errors
const fieldErrors = reactive({})

// Computed properties
const getInputClasses = (rowIndex, columnKey) => {
  const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-50 disabled:text-gray-500'
  const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
  
  const errorKey = `${rowIndex}.${columnKey}`
  return fieldErrors[errorKey] ? `${baseClasses} ${errorClasses}` : baseClasses
}

const getCurrencyInputClasses = (rowIndex, columnKey) => {
  const baseClasses = 'block w-full pl-7 pr-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-50 disabled:text-gray-500'
  const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
  
  const errorKey = `${rowIndex}.${columnKey}`
  return fieldErrors[errorKey] ? `${baseClasses} ${errorClasses}` : baseClasses
}

// Helper methods
const getCellError = (rowIndex, columnKey) => {
  return fieldErrors[`${rowIndex}.${columnKey}`]
}

const generateRowId = () => {
  return Date.now() + Math.random().toString(36).substr(2, 9)
}

// Row management methods
const addRow = () => {
  if (props.readonly) return
  
  const newRow = {
    id: generateRowId(),
    ...props.defaultRowData
  }
  
  // Initialize all column keys with empty values
  props.columns.forEach(column => {
    if (!(column.key in newRow)) {
      newRow[column.key] = column.type === 'number' || column.type === 'currency' || column.type === 'percentage' ? '' : ''
    }
  })
  
  localData.push(newRow)
  emit('row-add', newRow)
  emitUpdate()
}

const removeRow = (index) => {
  if (props.readonly) return
  
  const removedRow = localData[index]
  localData.splice(index, 1)
  
  // Clean up errors for removed row
  Object.keys(fieldErrors).forEach(key => {
    if (key.startsWith(`${index}.`)) {
      delete fieldErrors[key]
    }
  })
  
  // Reindex errors for remaining rows
  const newErrors = {}
  Object.keys(fieldErrors).forEach(key => {
    const match = key.match(/^(\d+)\.(.+)$/)
    if (match) {
      const errorIndex = parseInt(match[1])
      const field = match[2]
      if (errorIndex > index) {
        newErrors[`${errorIndex - 1}.${field}`] = fieldErrors[key]
      } else if (errorIndex < index) {
        newErrors[key] = fieldErrors[key]
      }
    } else {
      newErrors[key] = fieldErrors[key]
    }
  })
  
  Object.keys(fieldErrors).forEach(key => delete fieldErrors[key])
  Object.assign(fieldErrors, newErrors)
  
  emit('row-remove', removedRow, index)
  emitUpdate()
}

// Validation methods
const validateCell = (rowIndex, columnKey) => {
  const column = props.columns.find(col => col.key === columnKey)
  if (!column) return true
  
  const value = localData[rowIndex][columnKey]
  const errorKey = `${rowIndex}.${columnKey}`
  let error = null

  // Required field validation
  if (column.required && (!value || (typeof value === 'string' && value.trim().length === 0))) {
    error = `${column.label} is required`
  }
  
  // Type-specific validation
  if (value && !error) {
    switch (column.type) {
      case 'currency':
      case 'number':
        if (isNaN(value) || parseFloat(value) < 0) {
          error = `${column.label} must be a valid positive number`
        }
        break
        
      case 'percentage':
        if (isNaN(value) || parseFloat(value) < 0 || parseFloat(value) > 100) {
          error = `${column.label} must be between 0 and 100`
        }
        break
        
      case 'date':
        if (new Date(value).toString() === 'Invalid Date') {
          error = `${column.label} must be a valid date`
        }
        break
        
      case 'text':
        if (column.maxLength && value.length > column.maxLength) {
          error = `${column.label} must be less than ${column.maxLength} characters`
        }
        break
    }
  }
  
  // Custom validation
  if (column.validate && typeof column.validate === 'function') {
    const customError = column.validate(value, localData[rowIndex])
    if (customError) {
      error = customError
    }
  }

  if (error) {
    fieldErrors[errorKey] = error
  } else {
    delete fieldErrors[errorKey]
  }

  // Emit validation results
  emit('validate', fieldErrors)
  
  return !error
}

// Input handlers
const handleCellInput = (rowIndex, columnKey, value) => {
  localData[rowIndex][columnKey] = value
  
  // Clear field error when user starts typing
  const errorKey = `${rowIndex}.${columnKey}`
  if (fieldErrors[errorKey]) {
    delete fieldErrors[errorKey]
  }
  
  emitUpdate()
}

const emitUpdate = () => {
  const dataToEmit = localData.map(row => ({ ...row }))
  emit('update:modelValue', dataToEmit)
  emit('update', dataToEmit)
}

// Initialize local data from props
const initializeData = () => {
  // Clear existing data
  localData.splice(0, localData.length)
  
  if (props.modelValue && Array.isArray(props.modelValue)) {
    props.modelValue.forEach(row => {
      const newRow = { ...row }
      
      // Ensure all column keys exist
      props.columns.forEach(column => {
        if (!(column.key in newRow)) {
          newRow[column.key] = column.type === 'number' || column.type === 'currency' || column.type === 'percentage' ? '' : ''
        }
      })
      
      // Add ID if not present
      if (!newRow.id) {
        newRow.id = generateRowId()
      }
      
      localData.push(newRow)
    })
  }
}

// Watch for external errors
watch(() => props.errors, (newErrors) => {
  Object.keys(fieldErrors).forEach(key => delete fieldErrors[key])
  Object.assign(fieldErrors, newErrors)
}, { deep: true })

// Watch for prop changes
watch(() => props.modelValue, initializeData, { deep: true })
watch(() => props.columns, initializeData, { deep: true })

// Initialize on mount
onMounted(() => {
  initializeData()
})
</script>

<style scoped>
.dynamic-table {
  @apply w-full;
}

/* Custom styles for form elements */
input:focus, select:focus, textarea:focus {
  @apply ring-2 ring-blue-500 ring-opacity-50;
}

/* Disabled state styling */
input:disabled, select:disabled, textarea:disabled {
  @apply cursor-not-allowed;
}

/* Table styling */
table {
  @apply border-collapse;
}

th {
  @apply bg-gray-50 border-b border-gray-200;
}

td {
  @apply border-b border-gray-200;
}

/* Row hover effects */
tbody tr:hover {
  @apply bg-gray-50;
}

/* Action button styling */
.action-button {
  @apply transition-colors duration-200;
}
</style>