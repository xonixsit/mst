<template>
  <div class="employee-information-section">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <!-- Section Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Employee Information</h2>
        <p class="text-sm text-gray-600 mt-1">Employment details for tax preparation and deductions</p>
      </div>

      <!-- Form Content -->
      <div class="p-6">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Employer Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="employerName" class="block text-sm font-medium text-gray-700 mb-1">
                Employer Name <span class="text-red-500">*</span>
              </label>
              <input
                id="employerName"
                v-model="localData.employerName"
                type="text"
                :class="inputClasses('employerName')"
                placeholder="Enter employer name"
                :disabled="readonly"
                @blur="validateField('employerName')"
                @input="handleInput('employerName', $event.target.value)"
              />
              <p v-if="fieldErrors.employerName" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.employerName }}
              </p>
            </div>

            <div>
              <label for="position" class="block text-sm font-medium text-gray-700 mb-1">
                Job Title/Position <span class="text-red-500">*</span>
              </label>
              <input
                id="position"
                v-model="localData.position"
                type="text"
                :class="inputClasses('position')"
                placeholder="Enter job title"
                :disabled="readonly"
                @blur="validateField('position')"
                @input="handleInput('position', $event.target.value)"
              />
              <p v-if="fieldErrors.position" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.position }}
              </p>
            </div>
          </div>

          <!-- Employment Dates and Status -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">
                Start Date
              </label>
              <input
                id="startDate"
                v-model="localData.startDate"
                type="date"
                :class="inputClasses('startDate')"
                :disabled="readonly"
                @blur="validateField('startDate')"
                @input="handleInput('startDate', $event.target.value)"
              />
              <p v-if="fieldErrors.startDate" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.startDate }}
              </p>
            </div>

            <div>
              <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">
                End Date
              </label>
              <input
                id="endDate"
                v-model="localData.endDate"
                type="date"
                :class="inputClasses('endDate')"
                :disabled="readonly"
                @input="handleInput('endDate', $event.target.value)"
              />
              <p class="mt-1 text-xs text-gray-500">
                Leave blank if currently employed
              </p>
            </div>

            <div>
              <label for="employmentType" class="block text-sm font-medium text-gray-700 mb-1">
                Employment Status
              </label>
              <select
                id="employmentType"
                v-model="localData.employmentType"
                :class="inputClasses('employmentType')"
                :disabled="readonly"
                @change="handleInput('employmentType', $event.target.value)"
              >
                <option value="">Select status</option>
                <option value="full-time">Full-time</option>
                <option value="part-time">Part-time</option>
                <option value="contract">Contract</option>
                <option value="temporary">Temporary</option>
                <option value="terminated">Terminated</option>
              </select>
            </div>
          </div>

          <!-- Compensation Information -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-md font-medium text-gray-900 mb-4">Compensation Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="salary" class="block text-sm font-medium text-gray-700 mb-1">
                  Annual Salary/Wage
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">$</span>
                  </div>
                  <input
                    id="salary"
                    v-model="localData.salary"
                    type="number"
                    step="0.01"
                    min="0"
                    :class="salaryInputClasses"
                    placeholder="0.00"
                    :disabled="readonly"
                    @blur="validateField('salary')"
                    @input="handleInput('salary', $event.target.value)"
                  />
                </div>
                <p v-if="fieldErrors.salary" class="mt-1 text-sm text-red-600">
                  {{ fieldErrors.salary }}
                </p>
              </div>

              <div>
                <label for="payFrequency" class="block text-sm font-medium text-gray-700 mb-1">
                  Pay Frequency
                </label>
                <select
                  id="payFrequency"
                  v-model="localData.payFrequency"
                  :class="inputClasses('payFrequency')"
                  :disabled="readonly"
                  @change="handleInput('payFrequency', $event.target.value)"
                >
                  <option value="">Select frequency</option>
                  <option value="weekly">Weekly</option>
                  <option value="bi-weekly">Bi-weekly</option>
                  <option value="semi-monthly">Semi-monthly</option>
                  <option value="monthly">Monthly</option>
                  <option value="annually">Annually</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Benefits Information -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-md font-medium text-gray-900 mb-4">Benefits Information</h3>
            
            <div class="space-y-4">
              <!-- Health Insurance -->
              <div class="flex items-center space-x-3">
                <input
                  id="healthInsurance"
                  v-model="localData.benefits.healthInsurance"
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  :disabled="readonly"
                  @change="handleBenefitChange('healthInsurance', $event.target.checked)"
                />
                <label for="healthInsurance" class="text-sm text-gray-700">
                  Health Insurance provided by employer
                </label>
              </div>

              <!-- Retirement Plan -->
              <div class="flex items-center space-x-3">
                <input
                  id="retirementPlan"
                  v-model="localData.benefits.retirementPlan"
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  :disabled="readonly"
                  @change="handleBenefitChange('retirementPlan', $event.target.checked)"
                />
                <label for="retirementPlan" class="text-sm text-gray-700">
                  401(k) or other retirement plan
                </label>
              </div>

              <!-- Dental Insurance -->
              <div class="flex items-center space-x-3">
                <input
                  id="dentalInsurance"
                  v-model="localData.benefits.dentalInsurance"
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  :disabled="readonly"
                  @change="handleBenefitChange('dentalInsurance', $event.target.checked)"
                />
                <label for="dentalInsurance" class="text-sm text-gray-700">
                  Dental Insurance
                </label>
              </div>

              <!-- Vision Insurance -->
              <div class="flex items-center space-x-3">
                <input
                  id="visionInsurance"
                  v-model="localData.benefits.visionInsurance"
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  :disabled="readonly"
                  @change="handleBenefitChange('visionInsurance', $event.target.checked)"
                />
                <label for="visionInsurance" class="text-sm text-gray-700">
                  Vision Insurance
                </label>
              </div>
            </div>
          </div>

          <!-- Employer Address Information -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-md font-medium text-gray-900 mb-4">Employer Address</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="md:col-span-2">
                <label for="employerAddress" class="block text-sm font-medium text-gray-700 mb-1">
                  Street Address
                </label>
                <input
                  id="employerAddress"
                  v-model="localData.employerAddress"
                  type="text"
                  :class="inputClasses('employerAddress')"
                  placeholder="Enter employer street address"
                  :disabled="readonly"
                  @input="handleInput('employerAddress', $event.target.value)"
                />
              </div>

              <div>
                <label for="employerCity" class="block text-sm font-medium text-gray-700 mb-1">
                  City
                </label>
                <input
                  id="employerCity"
                  v-model="localData.employerCity"
                  type="text"
                  :class="inputClasses('employerCity')"
                  placeholder="Enter city"
                  :disabled="readonly"
                  @input="handleInput('employerCity', $event.target.value)"
                />
              </div>

              <div>
                <label for="employerState" class="block text-sm font-medium text-gray-700 mb-1">
                  State
                </label>
                <input
                  id="employerState"
                  v-model="localData.employerState"
                  type="text"
                  :class="inputClasses('employerState')"
                  placeholder="Enter state"
                  maxlength="2"
                  :disabled="readonly"
                  @input="handleInput('employerState', $event.target.value)"
                />
              </div>

              <div>
                <label for="employerZipCode" class="block text-sm font-medium text-gray-700 mb-1">
                  ZIP Code
                </label>
                <input
                  id="employerZipCode"
                  v-model="localData.employerZipCode"
                  type="text"
                  :class="inputClasses('employerZipCode')"
                  placeholder="Enter ZIP code"
                  :disabled="readonly"
                  @input="handleInput('employerZipCode', $event.target.value)"
                />
              </div>
            </div>
          </div>

          <!-- Additional Information -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-md font-medium text-gray-900 mb-4">Additional Information</h3>
            
            <div>
              <label for="workLocation" class="block text-sm font-medium text-gray-700 mb-1">
                Work Location
              </label>
              <select
                id="workLocation"
                v-model="localData.workLocation"
                :class="inputClasses('workLocation')"
                :disabled="readonly"
                @change="handleInput('workLocation', $event.target.value)"
              >
                <option value="">Select work location</option>
                <option value="office">Office</option>
                <option value="remote">Remote</option>
                <option value="hybrid">Hybrid</option>
                <option value="field">Field work</option>
                <option value="multiple">Multiple locations</option>
              </select>
            </div>

            <div class="mt-4">
              <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                Additional Notes
              </label>
              <textarea
                id="notes"
                v-model="localData.notes"
                rows="3"
                :class="inputClasses('notes')"
                placeholder="Any additional employment information relevant for tax preparation..."
                :disabled="readonly"
                @input="handleInput('notes', $event.target.value)"
              ></textarea>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'

// Props
const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({})
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
const localData = reactive({
  id: null,
  employerName: '',
  position: '',
  startDate: '',
  endDate: '',
  employmentType: '',
  salary: '',
  payFrequency: '',
  workLocation: '',
  notes: '',
  employerAddress: '',
  employerCity: '',
  employerState: '',
  employerZipCode: '',
  benefits: {
    healthInsurance: false,
    retirementPlan: false,
    dentalInsurance: false,
    visionInsurance: false
  }
})

// Field validation errors
const fieldErrors = reactive({})

// Computed properties
const inputClasses = (fieldName) => {
  const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-50 disabled:text-gray-500'
  const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
  
  return fieldErrors[fieldName] ? `${baseClasses} ${errorClasses}` : baseClasses
}

const salaryInputClasses = computed(() => {
  const baseClasses = 'block w-full pl-7 pr-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-50 disabled:text-gray-500'
  const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
  
  return fieldErrors.salary ? `${baseClasses} ${errorClasses}` : baseClasses
})

// Validation methods
const validateField = (fieldName) => {
  console.log('fieldName',fieldName);
  const value = localData[fieldName]
  let error = null

  switch (fieldName) {
    case 'employerName':
      if (!value || value.trim().length === 0) {
        error = 'Employer name is required'
      } else if (value.length > 100) {
        error = 'Employer name must be less than 100 characters'
      }
      break

    case 'position':
      if (!value || value.trim().length === 0) {
        error = 'Job title/position is required'
      } else if (value.length > 100) {
        error = 'Position must be less than 100 characters'
      }
      break

    case 'startDate':
      if (value) {
        const startDate = new Date(value)
        const today = new Date()
        if (startDate > today) {
          error = 'Start date cannot be in the future'
        }
      }
      break

    case 'salary':
      if (value && (isNaN(value) || parseFloat(value) < 0)) {
        error = 'Salary must be a valid positive number'
      }
      break
  }

  if (error) {
    fieldErrors[fieldName] = error
  } else {
    delete fieldErrors[fieldName]
  }

  // Emit validation results
  emit('validate', fieldErrors)
  
  return !error
}

// Input handlers
const handleInput = (fieldName, value) => {
  localData[fieldName] = value
  
  // Clear field error when user starts typing
  if (fieldErrors[fieldName]) {
    delete fieldErrors[fieldName]
  }
  
  // Debug logging
  console.log('EmployeeInformationSection handleInput:', {
    fieldName,
    value,
    localData: { ...localData }
  })
  
  // Emit update
  emit('update:modelValue', { ...localData })
  emit('update', { ...localData })
}

const handleBenefitChange = (benefitName, checked) => {
  localData.benefits[benefitName] = checked
  
  // Emit update
  emit('update:modelValue', { ...localData })
  emit('update', { ...localData })
}

const handleSubmit = () => {
  // Validate required fields
  const requiredFields = ['employerName', 'position']
  
  let isValid = true
  requiredFields.forEach(field => {
    console.log('field', field);
    if (!validateField(field)) {
      isValid = false
    }
  })
  console.log('localData',...localData);
  if (isValid) {
    emit('submit', { ...localData })
  }
}

// Initialize local data from props
const initializeData = () => {
  if (props.modelValue) {
    Object.keys(localData).forEach(key => {
      if (props.modelValue[key] !== undefined) {
        if (key === 'benefits' && typeof props.modelValue[key] === 'object') {
          localData[key] = { ...localData[key], ...props.modelValue[key] }
        } else {
          localData[key] = props.modelValue[key]
        }
      }
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

// Initialize on mount
onMounted(() => {
  initializeData()
})
</script>

<style scoped>
.employee-information-section {
  @apply max-w-none;
}

/* Custom styles for form elements */
input:focus, select:focus, textarea:focus {
  @apply ring-2 ring-blue-500 ring-opacity-50;
}

/* Disabled state styling */
input:disabled, select:disabled, textarea:disabled {
  @apply cursor-not-allowed;
}

/* Checkbox styling */
input[type="checkbox"]:focus {
  @apply ring-2 ring-blue-500 ring-opacity-50;
}
</style>