<template>
  <div class="spouse-details-section">
    <!-- Conditional Display Message -->
    <div v-if="!showSpouse" class="bg-blue-50 border border-blue-200 rounded-lg p-6">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <InformationCircleIcon class="h-5 w-5 text-blue-400" />
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-blue-800">
            Spouse Information Not Required
          </h3>
          <div class="mt-2 text-sm text-blue-700">
            <p>Spouse details are only required when marital status is set to "Married". 
               Please update your marital status in the Personal Details section if you need to add spouse information.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Spouse Details Form -->
    <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200">
      <!-- Section Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Spouse Details</h2>
        <p class="text-sm text-gray-600 mt-1">Information about your spouse for tax filing purposes</p>
      </div>

      <!-- Form Content -->
      <div class="p-6">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Name Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="spouseFirstName" class="block text-sm font-medium text-gray-700 mb-1">
                First Name <span class="text-red-500">*</span>
              </label>
              <input
                id="spouseFirstName"
                v-model="localData.firstName"
                type="text"
                :class="inputClasses('firstName')"
                placeholder="Enter spouse's first name"
                :disabled="readonly"
                @blur="validateField('firstName')"
                @input="handleInput('firstName', $event.target.value)"
              />
              <p v-if="fieldErrors.firstName" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.firstName }}
              </p>
            </div>

            <div>
              <label for="spouseLastName" class="block text-sm font-medium text-gray-700 mb-1">
                Last Name <span class="text-red-500">*</span>
              </label>
              <input
                id="spouseLastName"
                v-model="localData.lastName"
                type="text"
                :class="inputClasses('lastName')"
                placeholder="Enter spouse's last name"
                :disabled="readonly"
                @blur="validateField('lastName')"
                @input="handleInput('lastName', $event.target.value)"
              />
              <p v-if="fieldErrors.lastName" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.lastName }}
              </p>
            </div>
          </div>

          <!-- Personal Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="spouseSSN" class="block text-sm font-medium text-gray-700 mb-1">
                Social Security Number
              </label>
              <input
                id="spouseSSN"
                v-model="localData.ssn"
                type="text"
                :class="inputClasses('ssn')"
                placeholder="123-45-6789"
                maxlength="11"
                :disabled="readonly"
                @blur="validateField('ssn')"
                @input="handleSSNInput"
              />
              <p v-if="fieldErrors.ssn" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.ssn }}
              </p>
              <p class="mt-1 text-xs text-gray-500">
                Optional but recommended for joint tax filing
              </p>
            </div>

            <div>
              <label for="spouseDateOfBirth" class="block text-sm font-medium text-gray-700 mb-1">
                Date of Birth
              </label>
              <input
                id="spouseDateOfBirth"
                v-model="localData.dateOfBirth"
                type="date"
                :class="inputClasses('dateOfBirth')"
                :disabled="readonly"
                @blur="validateField('dateOfBirth')"
                @input="handleInput('dateOfBirth', $event.target.value)"
              />
              <p v-if="fieldErrors.dateOfBirth" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.dateOfBirth }}
              </p>
            </div>
          </div>

          <!-- Contact Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="spouseEmail" class="block text-sm font-medium text-gray-700 mb-1">
                Email Address
              </label>
              <input
                id="spouseEmail"
                v-model="localData.email"
                type="email"
                :class="inputClasses('email')"
                placeholder="spouse@example.com"
                :disabled="readonly"
                @blur="validateField('email')"
                @input="handleInput('email', $event.target.value)"
              />
              <p v-if="fieldErrors.email" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.email }}
              </p>
            </div>

            <div>
              <label for="spousePhone" class="block text-sm font-medium text-gray-700 mb-1">
                Phone Number
              </label>
              <input
                id="spousePhone"
                v-model="localData.phone"
                type="tel"
                :class="inputClasses('phone')"
                placeholder="(555) 123-4567"
                :disabled="readonly"
                @blur="validateField('phone')"
                @input="handlePhoneInput"
              />
              <p v-if="fieldErrors.phone" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.phone }}
              </p>
            </div>
          </div>

          <!-- Employment Information -->
          <div>
            <label for="spouseOccupation" class="block text-sm font-medium text-gray-700 mb-1">
              Occupation
            </label>
            <input
              id="spouseOccupation"
              v-model="localData.occupation"
              type="text"
              :class="inputClasses('occupation')"
              placeholder="Enter spouse's occupation"
              :disabled="readonly"
              @input="handleInput('occupation', $event.target.value)"
            />
            <p class="mt-1 text-xs text-gray-500">
              Employment information helps with tax deductions and credits
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { InformationCircleIcon } from '@heroicons/vue/24/outline'

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
  showSpouse: {
    type: Boolean,
    default: false
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
  firstName: '',
  lastName: '',
  ssn: '',
  dateOfBirth: '',
  email: '',
  phone: '',
  occupation: ''
})

// Field validation errors
const fieldErrors = reactive({})

// Computed properties
const inputClasses = (fieldName) => {
  const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-50 disabled:text-gray-500'
  const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
  
  return fieldErrors[fieldName] ? `${baseClasses} ${errorClasses}` : baseClasses
}

// Validation methods
const validateField = (fieldName) => {
  if (!props.showSpouse) return true
  
  const value = localData[fieldName]
  let error = null

  switch (fieldName) {
    case 'firstName':
      if (!value || value.trim().length === 0) {
        error = 'Spouse first name is required'
      } else if (value.length > 50) {
        error = 'First name must be less than 50 characters'
      }
      break

    case 'lastName':
      if (!value || value.trim().length === 0) {
        error = 'Spouse last name is required'
      } else if (value.length > 50) {
        error = 'Last name must be less than 50 characters'
      }
      break

    case 'ssn':
      if (value && !/^\d{3}-\d{2}-\d{4}$/.test(value)) {
        error = 'SSN must be in format 123-45-6789'
      }
      break

    case 'dateOfBirth':
      if (value) {
        const birthDate = new Date(value)
        const today = new Date()
        if (birthDate >= today) {
          error = 'Date of birth must be in the past'
        }
      }
      break

    case 'email':
      if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        error = 'Please enter a valid email address'
      }
      break

    case 'phone':
      if (value && !/^[\+]?[1-9][\d]{0,15}$/.test(value.replace(/\D/g, ''))) {
        error = 'Please enter a valid phone number'
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
  
  // Emit update
  emit('update:modelValue', { ...localData })
  emit('update', { ...localData })
}

const handleSSNInput = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  
  // Format SSN as user types
  if (value.length >= 6) {
    value = `${value.slice(0, 3)}-${value.slice(3, 5)}-${value.slice(5, 9)}`
  } else if (value.length >= 4) {
    value = `${value.slice(0, 3)}-${value.slice(3)}`
  }
  
  localData.ssn = value
  event.target.value = value
  
  handleInput('ssn', value)
}

const handlePhoneInput = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  
  // Format phone as user types (US format)
  if (value.length >= 7) {
    value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`
  } else if (value.length >= 4) {
    value = `(${value.slice(0, 3)}) ${value.slice(3)}`
  } else if (value.length >= 1) {
    value = `(${value}`
  }
  
  localData.phone = value
  event.target.value = value
  
  handleInput('phone', value)
}

const handleSubmit = () => {
  if (!props.showSpouse) return
  
  // Validate required fields when spouse section is visible
  const requiredFields = ['firstName', 'lastName']
  
  let isValid = true
  requiredFields.forEach(field => {
    if (!validateField(field)) {
      isValid = false
    }
  })
  
  if (isValid) {
    emit('submit', { ...localData })
  }
}

// Clear data when spouse section is hidden
const clearDataWhenHidden = () => {
  if (!props.showSpouse) {
    Object.keys(localData).forEach(key => {
      localData[key] = ''
    })
    Object.keys(fieldErrors).forEach(key => delete fieldErrors[key])
    
    // Emit cleared data
    emit('update:modelValue', { ...localData })
    emit('update', { ...localData })
    emit('validate', {})
  }
}

// Initialize local data from props
const initializeData = () => {
  if (props.modelValue && props.showSpouse) {
    Object.keys(localData).forEach(key => {
      if (props.modelValue[key] !== undefined) {
        localData[key] = props.modelValue[key]
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

// Watch for showSpouse changes
watch(() => props.showSpouse, (newValue) => {
  if (newValue) {
    initializeData()
  } else {
    clearDataWhenHidden()
  }
}, { immediate: true })

// Initialize on mount
onMounted(() => {
  if (props.showSpouse) {
    initializeData()
  }
})
</script>

<style scoped>
.spouse-details-section {
  @apply max-w-none;
}

/* Transition effects for conditional display */
.spouse-details-section > div {
  transition: all 0.3s ease-in-out;
}

/* Custom styles for form elements */
input:focus, select:focus {
  @apply ring-2 ring-blue-500 ring-opacity-50;
}

/* Disabled state styling */
input:disabled {
  @apply cursor-not-allowed;
}
</style>