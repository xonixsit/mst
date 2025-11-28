<template>
  <div class="relative">
    <input
      :id="inputId"
      ref="inputRef"
      :value="displayValue"
      type="text"
      :class="inputClasses"
      :placeholder="placeholder"
      :maxlength="11"
      :disabled="disabled"
      :readonly="readonly"
      @input="handleInput"
      @focus="handleFocus"
      @blur="handleBlur"
      @keydown="handleKeydown"
    />
    
    <!-- Security indicator -->
    <div 
      v-if="showSecurityIndicator && hasValue && !isFocused"
      class="absolute inset-y-0 right-0 flex items-center pr-3"
    >
      <div class="flex items-center space-x-1">
        <EyeSlashIcon class="h-4 w-4 text-gray-400" />
        <span class="text-xs text-gray-500">Secured</span>
      </div>
    </div>
    
    <!-- Show/Hide toggle for debugging (only in development) -->
    <button
      v-if="showToggle && hasValue"
      type="button"
      @click="toggleMask"
      class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
    >
      <EyeIcon v-if="isMasked" class="h-4 w-4" />
      <EyeSlashIcon v-else class="h-4 w-4" />
    </button>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'
import { 
  maskSSN, 
  shouldMaskSSN, 
  formatSSNInput, 
  getSSNDisplayValue,
  isValidSSN 
} from '@/Utils/SSNMask.js'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  inputId: {
    type: String,
    required: true
  },
  placeholder: {
    type: String,
    default: '123-45-6789'
  },
  inputClasses: {
    type: String,
    default: 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-gray-900 placeholder-gray-500'
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
  },
  showToggle: {
    type: Boolean,
    default: false // Only enable in development if needed
  },
  autoMask: {
    type: Boolean,
    default: true
  },
  isPreMasked: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur', 'input'])

const inputRef = ref(null)
const isFocused = ref(false)
const isMasked = ref(true)
const actualValue = ref(props.modelValue || '')

// Computed properties
const hasValue = computed(() => {
  return actualValue.value && actualValue.value.length > 0
})

const displayValue = computed(() => {
  if (!props.autoMask) {
    return actualValue.value
  }
  
  // If the value is pre-masked from backend and we're not focused, show it as-is
  if (props.isPreMasked && actualValue.value.includes('*') && !isFocused.value) {
    return actualValue.value
  }
  
  // If focused and we have a masked value, show placeholder for editing
  if (isFocused.value && actualValue.value.includes('*')) {
    return ''
  }
  
  return getSSNDisplayValue(
    actualValue.value, 
    isFocused.value || !isMasked.value, 
    hasValue.value
  )
})

// Event handlers
const handleInput = (event) => {
  const rawValue = event.target.value
  
  // If user is typing over a masked value, clear it first
  if (rawValue.includes('*')) {
    // If they're trying to edit a masked value, clear it
    if (rawValue.length > actualValue.value.length || !isFocused.value) {
      event.target.value = ''
      actualValue.value = ''
      emit('update:modelValue', '')
      emit('input', '')
      return
    }
  }
  
  // Format the input
  const formattedValue = formatSSNInput(rawValue)
  actualValue.value = formattedValue
  
  // Update the input field
  event.target.value = formattedValue
  
  // Emit the actual value
  emit('update:modelValue', formattedValue)
  emit('input', formattedValue)
}

const handleFocus = (event) => {
  isFocused.value = true
  
  // Show actual value when focused
  nextTick(() => {
    if (inputRef.value && props.autoMask) {
      inputRef.value.value = actualValue.value
    }
  })
  
  emit('focus', event)
}

const handleBlur = (event) => {
  isFocused.value = false
  isMasked.value = true
  
  // Validate and format on blur
  if (actualValue.value) {
    const formattedValue = formatSSNInput(actualValue.value)
    if (formattedValue !== actualValue.value) {
      actualValue.value = formattedValue
      emit('update:modelValue', formattedValue)
    }
  }
  
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
  if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
    event.preventDefault()
  }
}

const toggleMask = () => {
  isMasked.value = !isMasked.value
  
  nextTick(() => {
    if (inputRef.value) {
      inputRef.value.value = displayValue.value
    }
  })
}

// Watch for external value changes
watch(() => props.modelValue, (newValue) => {
  if (newValue !== actualValue.value) {
    // If we receive a masked value from backend, store it but don't emit it back
    actualValue.value = newValue || ''
    
    // If it's a masked value, we'll show it as masked but won't emit changes
    // until user actually edits it
  }
})

// Initialize
actualValue.value = props.modelValue || ''
</script>

<style scoped>
/* Ensure the input field handles the masked display properly */
input[type="text"] {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  letter-spacing: 0.05em;
}

/* Style for masked text */
input[type="text"]:not(:focus) {
  color: #374151; /* gray-700 */
}

/* Security indicator styling */
.security-indicator {
  pointer-events: none;
}
</style>