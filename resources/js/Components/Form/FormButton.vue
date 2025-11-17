<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="buttonClasses"
    @click="$emit('click', $event)"
  >
    <!-- Loading spinner -->
    <svg 
      v-if="loading" 
      class="animate-spin -ml-1 mr-3 h-5 w-5" 
      fill="none" 
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    
    <!-- Icon -->
    <component 
      v-else-if="icon" 
      :is="icon" 
      class="w-5 h-5"
      :class="{ 'mr-2': $slots.default }"
    />
    
    <!-- Button text -->
    <span v-if="$slots.default">
      <slot />
    </span>
    <span v-else-if="loading && loadingText">
      {{ loadingText }}
    </span>
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'button'
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'danger', 'ghost', 'link'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  loadingText: {
    type: String,
    default: null
  },
  fullWidth: {
    type: Boolean,
    default: false
  },
  icon: {
    type: [Object, Function],
    default: null
  }
})

defineEmits(['click'])

const buttonClasses = computed(() => {
  const baseClasses = [
    'inline-flex items-center justify-center font-medium rounded-md',
    'focus:outline-none focus:ring-2 focus:ring-offset-2',
    'transition-all duration-200 ease-in-out',
    'disabled:opacity-50 disabled:cursor-not-allowed'
  ]
  
  // Size classes
  const sizeClasses = {
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-2 text-sm',
    lg: 'px-6 py-3 text-base'
  }
  baseClasses.push(sizeClasses[props.size])
  
  // Variant classes
  const variantClasses = {
    primary: [
      'text-white bg-indigo-600 border border-transparent',
      'hover:bg-indigo-700 focus:ring-indigo-500',
      'disabled:hover:bg-indigo-600'
    ],
    secondary: [
      'text-indigo-700 bg-indigo-100 border border-transparent',
      'hover:bg-indigo-200 focus:ring-indigo-500',
      'disabled:hover:bg-indigo-100'
    ],
    danger: [
      'text-white bg-red-600 border border-transparent',
      'hover:bg-red-700 focus:ring-red-500',
      'disabled:hover:bg-red-600'
    ],
    ghost: [
      'text-gray-700 bg-white border border-gray-300',
      'hover:bg-gray-50 focus:ring-indigo-500',
      'disabled:hover:bg-white'
    ],
    link: [
      'text-indigo-600 bg-transparent border-none p-0',
      'hover:text-indigo-500 focus:ring-0 underline',
      'disabled:hover:text-indigo-600'
    ]
  }
  baseClasses.push(...variantClasses[props.variant])
  
  // Full width
  if (props.fullWidth) {
    baseClasses.push('w-full')
  }
  
  return baseClasses.join(' ')
})
</script>