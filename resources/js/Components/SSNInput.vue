<template>
  <div class="relative">
    <input
      ref="inputRef"
      :type="showValue ? 'text' : 'password'"
      :value="modelValue"
      :placeholder="placeholder"
      :class="inputClasses"
      :disabled="disabled"
      :readonly="readonly"
      @input="handleInput"
      @focus="handleFocus"
      @blur="handleBlur"
      @keydown="handleKeydown"
    />
    
    <!-- Toggle visibility button -->
    <button
      type="button"
      @click="toggleVisibility"
      class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
      :disabled="disabled"
    >
      <EyeIcon v-if="!showValue" class="h-5 w-5" />
      <EyeSlashIcon v-else class="h-5 w-5" />
    </button>
    
    <!-- Security indicator -->
    <div v-if="showSecurityIndicator && hasValue" class="mt-1 flex items-center text-xs text-green-600">
      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
      </svg>
      Encrypted and secure
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  modelValue: {
    type: String,
    required: true
  },
  placeholder: {
    type: String,
    default: '123-45-6789'
  },
  inputClasses: {
    type: String,
    default: 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-gray-900 placeholder-gray-500 pr-10'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  showSecurityIndicator: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur', 'input'])

const inputRef = ref(null)
const showValue = ref(false)

// Computed properties
const hasValue = computed(() => {
  return props.modelValue && props.modelValue.length > 0
})

// Event handlers
const handleInput = (event) => {
  const value = event.target.value
  // Format SSN with dashes
  const formatted = formatSSN(value)
  event.target.value = formatted
  emit('update:modelValue', formatted)
  emit('input', formatted)
}

const handleFocus = (event) => {
  emit('focus', event)
}

const handleBlur = (event) => {
  emit('blur', event)
}

const handleKeydown = (event) => {
  // Allow backspace, delete, tab, escape, enter
  if ([8, 9, 27, 13, 46].includes(event.keyCode)) {
    return
  }
  
  // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
  if (event.ctrlKey && [65, 67, 86, 88].includes(event.keyCode)) {
    return
  }
  
  // Allow arrow keys
  if (event.keyCode >= 37 && event.keyCode <= 40) {
    return
  }
  
  // Only allow numbers
  if (event.keyCode < 48 || event.keyCode > 57) {
    event.preventDefault()
  }
}

const toggleVisibility = () => {
  showValue.value = !showValue.value
}

// Format SSN with dashes
const formatSSN = (value) => {
  // Remove all non-digits
  const digits = value.replace(/\D/g, '')
  
  // Limit to 9 digits
  const limited = digits.substring(0, 9)
  
  // Add dashes
  if (limited.length >= 6) {
    return `${limited.substring(0, 3)}-${limited.substring(3, 5)}-${limited.substring(5)}`
  } else if (limited.length >= 4) {
    return `${limited.substring(0, 3)}-${limited.substring(3)}`
  } else {
    return limited
  }
}
</script>