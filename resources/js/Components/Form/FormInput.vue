<template>
  <div class="space-y-1">
    <label 
      v-if="label" 
      :for="id" 
      class="block text-sm font-medium text-gray-700"
      :class="{ 'text-red-700': hasError }"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
    
    <div class="relative">
      <input
        :id="id"
        :name="name"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :autocomplete="autocomplete"
        :class="inputClasses"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur')"
        @focus="$emit('focus')"
      />
      
      <!-- Icon -->
      <div v-if="icon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <component :is="icon" class="h-5 w-5 text-gray-400" />
      </div>
      
      <!-- Loading spinner -->
      <div v-if="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
        <svg class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
    </div>
    
    <!-- Error message -->
    <p v-if="hasError" class="text-sm text-red-600 flex items-center">
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      {{ error }}
    </p>
    
    <!-- Help text -->
    <p v-if="help && !hasError" class="text-sm text-gray-500">
      {{ help }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  name: {
    type: String,
    default: null
  },
  label: {
    type: String,
    default: null
  },
  type: {
    type: String,
    default: 'text'
  },
  modelValue: {
    type: [String, Number],
    default: ''
  },
  placeholder: {
    type: String,
    default: null
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  autocomplete: {
    type: String,
    default: null
  },
  error: {
    type: String,
    default: null
  },
  help: {
    type: String,
    default: null
  },
  icon: {
    type: Object,
    default: null
  }
})

defineEmits(['update:modelValue', 'blur', 'focus'])

const hasError = computed(() => !!props.error)

const inputClasses = computed(() => {
  const baseClasses = [
    'block w-full px-3 py-2 border rounded-md shadow-sm',
    'placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0',
    'sm:text-sm transition-colors duration-200'
  ]
  
  if (props.icon) {
    baseClasses.push('pl-10')
  }
  
  if (props.loading) {
    baseClasses.push('pr-10')
  }
  
  if (hasError.value) {
    baseClasses.push(
      'border-red-300 text-red-900 placeholder-red-300',
      'focus:ring-red-500 focus:border-red-500'
    )
  } else if (props.disabled) {
    baseClasses.push(
      'bg-gray-50 border-gray-200 text-gray-500 cursor-not-allowed'
    )
  } else {
    baseClasses.push(
      'border-gray-300 text-gray-900',
      'focus:ring-indigo-500 focus:border-indigo-500',
      'hover:border-gray-400'
    )
  }
  
  return baseClasses.join(' ')
})
</script>