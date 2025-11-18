<template>
  <div class="personal-details-section">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <!-- Section Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Personal Details</h2>
        <p class="text-sm text-gray-600 mt-1">Basic personal information and contact details</p>
      </div>

      <!-- Form Content -->
      <div class="p-6">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Name Information -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label for="firstName" class="flex items-center text-sm font-medium text-gray-700 mb-1">
                First Name
                <span class="text-xs text-gray-500 ml-1">(Update in Profile)</span>
              </label>
              <input
                id="firstName"
                v-model="localData.firstName"
                type="text"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 cursor-not-allowed"
                placeholder="Enter first name"
                readonly
                disabled
              />
              <p class="mt-1 text-xs text-gray-500">
                Name fields can be updated in your <a href="/client/profile" class="text-blue-600 hover:text-blue-800 underline">Profile</a> section.
              </p>
            </div>

            <div>
              <label for="middleName" class="block text-sm font-medium text-gray-700 mb-1">
                Middle Name
                <span class="text-xs text-gray-500 ml-1">(Update in Profile)</span>
              </label>
              <input
                id="middleName"
                v-model="localData.middleName"
                type="text"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 cursor-not-allowed"
                placeholder="Enter middle name"
                readonly
                disabled
              />
            </div>

            <div>
              <label for="lastName" class="flex items-center text-sm font-medium text-gray-700 mb-1">
                Last Name
                <span class="text-xs text-gray-500 ml-1">(Update in Profile)</span>
              </label>
              <input
                id="lastName"
                v-model="localData.lastName"
                type="text"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 cursor-not-allowed"
                placeholder="Enter last name"
                readonly
                disabled
              />
            </div>
          </div>

          <!-- Contact Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email Address <span class="text-red-500">*</span>
                <span class="text-xs text-gray-500 ml-1">(Contact admin to change)</span>
              </label>
              <input
                id="email"
                v-model="localData.email"
                type="email"
                :class="inputClasses('email') + ' bg-gray-50 cursor-not-allowed'"
                placeholder="Enter email address"
                readonly
                disabled
              />
              <p class="mt-1 text-xs text-gray-500">
                Your email address is used for account login and cannot be changed here. Contact your tax professional to update this information.
              </p>
            </div>

            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                Phone Number <span class="text-red-500">*</span>
              </label>
              <input
                id="phone"
                v-model="localData.phone"
                type="tel"
                :class="inputClasses('phone')"
                placeholder="(555) 123-4567"
                maxlength="14"
                @blur="validateField('phone')"
                @input="handlePhoneInputMask('phone', $event)"
              />
              <p v-if="fieldErrors.phone" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.phone }}
              </p>
            </div>
          </div>

          <!-- Personal Information -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label for="ssn" class="flex items-center text-sm font-medium text-gray-700 mb-1">
                Social Security Number <span class="text-red-500">*</span>
                <HelpTooltip 
                  text="Your 9-digit Social Security Number in format 123-45-6789. This is required for tax filing and must match your Social Security card exactly."
                  class="ml-1"
                />
              </label>
              <input
                id="ssn"
                v-model="localData.ssn"
                type="text"
                :class="inputClasses('ssn')"
                placeholder="123-45-6789"
                maxlength="11"
                @blur="validateField('ssn')"
                @input="handleSSNInput"
              />
              <p v-if="fieldErrors.ssn" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.ssn }}
              </p>
            </div>

            <div>
              <label for="dateOfBirth" class="block text-sm font-medium text-gray-700 mb-1">
                Date of Birth <span class="text-red-500">*</span>
              </label>
              <input
                id="dateOfBirth"
                v-model="localData.dateOfBirth"
                type="date"
                :class="inputClasses('dateOfBirth')"
                @blur="validateField('dateOfBirth')"
                @input="handleInput('dateOfBirth', $event.target.value)"
              />
              <p v-if="fieldErrors.dateOfBirth" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.dateOfBirth }}
              </p>
            </div>

            <div>
              <label for="maritalStatus" class="flex items-center text-sm font-medium text-gray-700 mb-1">
                Marital Status <span class="text-red-500">*</span>
                <HelpTooltip 
                  text="Your marital status as of December 31st of the tax year. If married, you'll need to provide spouse information in the next section."
                  class="ml-1"
                />
              </label>
              <select
                id="maritalStatus"
                v-model="localData.maritalStatus"
                :class="inputClasses('maritalStatus')"
                @change="handleMaritalStatusChange"
              >
                <option value="">Select marital status</option>
                <option value="single">Single</option>
                <option value="married">Married</option>
                <option value="divorced">Divorced</option>
                <option value="widowed">Widowed</option>
              </select>
              <p v-if="fieldErrors.maritalStatus" class="mt-1 text-sm text-red-600">
                {{ fieldErrors.maritalStatus }}
              </p>
            </div>
          </div>

          <!-- Employment and Insurance -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="occupation" class="block text-sm font-medium text-gray-700 mb-1">
                Occupation
              </label>
              <input
                id="occupation"
                v-model="localData.occupation"
                type="text"
                :class="inputClasses('occupation')"
                placeholder="Enter occupation"
                @input="handleInput('occupation', $event.target.value)"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Insurance Coverage
              </label>
              <div class="flex items-center space-x-4 mt-2">
                <label class="flex items-center">
                  <input
                    v-model="localData.insuranceCovered"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    @change="handleInput('insuranceCovered', $event.target.checked)"
                  />
                  <span class="ml-2 text-sm text-gray-700">I have insurance coverage</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Address Information -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-md font-medium text-gray-900 mb-4">Address Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label for="streetNo" class="block text-sm font-medium text-gray-700 mb-1">
                  Street Number/Address <span class="text-red-500">*</span>
                </label>
                <input
                  id="streetNo"
                  v-model="localData.streetNo"
                  type="text"
                  :class="inputClasses('streetNo')"
                  placeholder="123 Main Street"
                  @blur="validateField('streetNo')"
                  @input="handleInput('streetNo', $event.target.value)"
                />
                <p v-if="fieldErrors.streetNo" class="mt-1 text-sm text-red-600">
                  {{ fieldErrors.streetNo }}
                </p>
              </div>

              <div>
                <label for="apartmentNo" class="block text-sm font-medium text-gray-700 mb-1">
                  Apartment/Unit Number
                </label>
                <input
                  id="apartmentNo"
                  v-model="localData.apartmentNo"
                  type="text"
                  :class="inputClasses('apartmentNo')"
                  placeholder="Apt 4B"
                  @input="handleInput('apartmentNo', $event.target.value)"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label for="zipCode" class="block text-sm font-medium text-gray-700 mb-1">
                  ZIP Code <span class="text-red-500">*</span>
                </label>
                <input
                  id="zipCode"
                  v-model="localData.zipCode"
                  type="text"
                  :class="inputClasses('zipCode')"
                  placeholder="12345"
                  maxlength="10"
                  @blur="validateField('zipCode')"
                  @input="handleInput('zipCode', $event.target.value)"
                />
                <p v-if="fieldErrors.zipCode" class="mt-1 text-sm text-red-600">
                  {{ fieldErrors.zipCode }}
                </p>
              </div>

              <div>
                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                  City <span class="text-red-500">*</span>
                </label>
                <input
                  id="city"
                  v-model="localData.city"
                  type="text"
                  :class="inputClasses('city')"
                  placeholder="New York"
                  @blur="validateField('city')"
                  @input="handleInput('city', $event.target.value)"
                />
                <p v-if="fieldErrors.city" class="mt-1 text-sm text-red-600">
                  {{ fieldErrors.city }}
                </p>
              </div>

              <div>
                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                  State <span class="text-red-500">*</span>
                </label>
                <select
                  id="state"
                  v-model="localData.state"
                  :class="inputClasses('state')"
                  @change="handleInput('state', $event.target.value)"
                >
                  <option value="">Select state</option>
                  <option v-for="state in usStates" :key="state.code" :value="state.code">
                    {{ state.name }}
                  </option>
                </select>
                <p v-if="fieldErrors.state" class="mt-1 text-sm text-red-600">
                  {{ fieldErrors.state }}
                </p>
              </div>

              <div>
                <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                  Country <span class="text-red-500">*</span>
                </label>
                <input
                  id="country"
                  v-model="localData.country"
                  type="text"
                  :class="inputClasses('country')"
                  placeholder="United States"
                  @input="handleInput('country', $event.target.value)"
                />
              </div>
            </div>
          </div>

          <!-- Additional Contact Information -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-md font-medium text-gray-900 mb-4">Additional Contact Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="mobileNumber" class="block text-sm font-medium text-gray-700 mb-1">
                  Mobile Number
                </label>
                <input
                  id="mobileNumber"
                  v-model="localData.mobileNumber"
                  type="tel"
                  :class="inputClasses('mobileNumber')"
                  placeholder="(555) 123-4567"
                  maxlength="14"
                  @input="handlePhoneInputMask('mobileNumber', $event)"
                />
              </div>

              <div>
                <label for="workNumber" class="block text-sm font-medium text-gray-700 mb-1">
                  Work Number
                </label>
                <input
                  id="workNumber"
                  v-model="localData.workNumber"
                  type="tel"
                  :class="inputClasses('workNumber')"
                  placeholder="(555) 987-6543"
                  maxlength="14"
                  @input="handlePhoneInputMask('workNumber', $event)"
                />
              </div>
            </div>
          </div>

          <!-- Immigration Information -->
          <div 
            v-if="showImmigrationFields"
            class="border-t border-gray-200 pt-6"
          >
            <h3 class="text-md font-medium text-gray-900 mb-4">Immigration Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label for="visaStatus" class="block text-sm font-medium text-gray-700 mb-1">
                  Visa Status
                </label>
                <select
                  id="visaStatus"
                  v-model="localData.visaStatus"
                  :class="inputClasses('visaStatus')"
                  @change="handleInput('visaStatus', $event.target.value)"
                >
                  <option value="">Select visa status</option>
                  <option 
                    v-for="option in visaStatusOptions" 
                    :key="option.value" 
                    :value="option.value"
                  >
                    {{ option.label }}
                  </option>
                </select>
              </div>

              <div>
                <label for="date_of_entry_us" class="block text-sm font-medium text-gray-700 mb-1">
                  Date of Entry to US
                  <span v-if="localData.visaStatus === 'citizen'" class="text-xs text-gray-500 ml-1">(Not applicable for US Citizens)</span>
                </label>
                <input
                  id="date_of_entry_us"
                  v-model="localData.date_of_entry_us"
                  type="date"
                  :class="inputClasses('date_of_entry_us')"
                  :disabled="localData.visaStatus === 'citizen'"
                  @input="handleInput('date_of_entry_us', $event.target.value)"
                />
                <p v-if="localData.visaStatus === 'citizen'" class="mt-1 text-xs text-gray-500">
                  This field is not required for US Citizens
                </p>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import HelpTooltip from '@/Components/HelpTooltip.vue'
import { formatPhoneNumber, handlePhoneInput, isValidPhoneNumber, getPhoneDisplayFormat } from '@/Utils/PhoneMask.js'

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
  },
  visaStatusOptions: {
    type: Array,
    default: () => ([
      { value: '', label: 'Select visa status' },
      { value: 'citizen', label: 'US Citizen' },
      { value: 'permanent_resident', label: 'Permanent Resident' },
      { value: 'h1b', label: 'H-1B' },
      { value: 'f1', label: 'F-1 Student' },
      { value: 'j1', label: 'J-1 Exchange' },
      { value: 'l1', label: 'L-1 Intracompany' },
      { value: 'other', label: 'Other' }
    ])
  }
})

// Emits
const emit = defineEmits(['update:modelValue', 'update', 'validate'])

// Local reactive data
const localData = reactive({
  firstName: '',
  middleName: '',
  lastName: '',
  email: '',
  phone: '',
  ssn: '',
  dateOfBirth: '',
  maritalStatus: '',
  occupation: '',
  insuranceCovered: false,
  streetNo: '',
  apartmentNo: '',
  zipCode: '',
  city: '',
  state: '',
  country: 'United States',
  mobileNumber: '',
  workNumber: '',
  visaStatus: '',
  date_of_entry_us: ''
})

// Field validation errors
const fieldErrors = reactive({})

// US States data
const usStates = [
  { code: 'AL', name: 'Alabama' },
  { code: 'AK', name: 'Alaska' },
  { code: 'AZ', name: 'Arizona' },
  { code: 'AR', name: 'Arkansas' },
  { code: 'CA', name: 'California' },
  { code: 'CO', name: 'Colorado' },
  { code: 'CT', name: 'Connecticut' },
  { code: 'DE', name: 'Delaware' },
  { code: 'FL', name: 'Florida' },
  { code: 'GA', name: 'Georgia' },
  { code: 'HI', name: 'Hawaii' },
  { code: 'ID', name: 'Idaho' },
  { code: 'IL', name: 'Illinois' },
  { code: 'IN', name: 'Indiana' },
  { code: 'IA', name: 'Iowa' },
  { code: 'KS', name: 'Kansas' },
  { code: 'KY', name: 'Kentucky' },
  { code: 'LA', name: 'Louisiana' },
  { code: 'ME', name: 'Maine' },
  { code: 'MD', name: 'Maryland' },
  { code: 'MA', name: 'Massachusetts' },
  { code: 'MI', name: 'Michigan' },
  { code: 'MN', name: 'Minnesota' },
  { code: 'MS', name: 'Mississippi' },
  { code: 'MO', name: 'Missouri' },
  { code: 'MT', name: 'Montana' },
  { code: 'NE', name: 'Nebraska' },
  { code: 'NV', name: 'Nevada' },
  { code: 'NH', name: 'New Hampshire' },
  { code: 'NJ', name: 'New Jersey' },
  { code: 'NM', name: 'New Mexico' },
  { code: 'NY', name: 'New York' },
  { code: 'NC', name: 'North Carolina' },
  { code: 'ND', name: 'North Dakota' },
  { code: 'OH', name: 'Ohio' },
  { code: 'OK', name: 'Oklahoma' },
  { code: 'OR', name: 'Oregon' },
  { code: 'PA', name: 'Pennsylvania' },
  { code: 'RI', name: 'Rhode Island' },
  { code: 'SC', name: 'South Carolina' },
  { code: 'SD', name: 'South Dakota' },
  { code: 'TN', name: 'Tennessee' },
  { code: 'TX', name: 'Texas' },
  { code: 'UT', name: 'Utah' },
  { code: 'VT', name: 'Vermont' },
  { code: 'VA', name: 'Virginia' },
  { code: 'WA', name: 'Washington' },
  { code: 'WV', name: 'West Virginia' },
  { code: 'WI', name: 'Wisconsin' },
  { code: 'WY', name: 'Wyoming' }
]

// Computed properties
const showImmigrationFields = computed(() => {
  // Always show immigration fields for tax consulting purposes
  return true
})

const inputClasses = (fieldName) => {
  const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm'
  const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
  
  return fieldErrors[fieldName] ? `${baseClasses} ${errorClasses}` : baseClasses
}

// Validation methods
const validateField = (fieldName) => {
  const value = localData[fieldName]
  let error = null

  switch (fieldName) {
    case 'firstName':
    case 'lastName':
      if (!value || value.trim().length === 0) {
        error = `${fieldName === 'firstName' ? 'First' : 'Last'} name is required`
      } else if (value.length > 50) {
        error = `${fieldName === 'firstName' ? 'First' : 'Last'} name must be less than 50 characters`
      }
      break

    case 'email':
      if (!value) {
        error = 'Email address is required'
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        error = 'Please enter a valid email address'
      }
      break

    case 'phone':
      if (!value) {
        error = 'Phone number is required'
      } else if (!isValidPhoneNumber(value)) {
        error = 'Please enter a valid 10-digit phone number'
      }
      break

    case 'mobileNumber':
    case 'workNumber':
      if (value && !isValidPhoneNumber(value)) {
        error = 'Please enter a valid 10-digit phone number'
      }
      break

    case 'ssn':
      if (!value) {
        error = 'Social Security Number is required'
      } else if (!/^\d{3}-\d{2}-\d{4}$/.test(value)) {
        error = 'SSN must be in format 123-45-6789'
      }
      break

    case 'dateOfBirth':
      if (!value) {
        error = 'Date of birth is required'
      } else {
        const birthDate = new Date(value)
        const today = new Date()
        if (birthDate >= today) {
          error = 'Date of birth must be in the past'
        }
      }
      break

    case 'streetNo':
      if (!value || value.trim().length === 0) {
        error = 'Street address is required'
      }
      break

    case 'zipCode':
      if (!value) {
        error = 'ZIP code is required'
      } else if (!/^\d{5}(-\d{4})?$/.test(value)) {
        error = 'Please enter a valid ZIP code'
      }
      break

    case 'city':
      if (!value || value.trim().length === 0) {
        error = 'City is required'
      }
      break

    case 'state':
      if (!value) {
        error = 'State is required'
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

const handlePhoneInputMask = (fieldName, event) => {
  handlePhoneInput(event, (formattedValue) => {
    localData[fieldName] = formattedValue
    handleInput(fieldName, formattedValue)
  })
}

const handleMaritalStatusChange = (event) => {
  const value = event.target.value
  handleInput('maritalStatus', value)
  
  // Emit marital status change for spouse section visibility
  emit('marital-status-change', value)
}

const handleSubmit = () => {
  // Validate all required fields
  const requiredFields = ['firstName', 'lastName', 'email', 'phone', 'ssn', 'dateOfBirth', 'streetNo', 'zipCode', 'city', 'state']
  
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

// Initialize local data from props
const initializeData = () => {
  if (props.modelValue) {
    Object.keys(localData).forEach(key => {
      if (props.modelValue[key] !== undefined) {
        let value = props.modelValue[key]
        
        // Format phone numbers on initialization
        if (['phone', 'mobileNumber', 'workNumber'].includes(key) && value) {
          value = getPhoneDisplayFormat(value)
        }
        
        localData[key] = value
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
.personal-details-section {
  @apply max-w-none;
}

/* Custom styles for form elements */
input:focus, select:focus {
  @apply ring-2 ring-blue-500 ring-opacity-50;
}

.required-field::after {
  content: ' *';
  @apply text-red-500;
}
</style>