<template>
  <component
    :is="tag"
    :type="tag === 'button' ? type : undefined"
    :href="tag === 'a' ? href : undefined"
    :class="buttonClasses"
    :disabled="disabled || loading"
    @click="handleClick"
  >
    <div class="flex items-center justify-center">
      <!-- Loading Spinner -->
      <svg
        v-if="loading"
        class="animate-spin -ml-1 mr-2 h-4 w-4"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
      </svg>

      <!-- Icon (left) -->
      <component
        v-if="iconLeft && !loading"
        :is="iconLeft"
        :class="iconClasses"
      />

      <!-- Button Text -->
      <span v-if="$slots.default || text">
        <slot>{{ text }}</slot>
      </span>

      <!-- Icon (right) -->
      <component
        v-if="iconRight && !loading"
        :is="iconRight"
        :class="iconClasses"
      />
    </div>
  </component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  // Button content
  text: {
    type: String,
    default: ''
  },
  
  // Button behavior
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value)
  },
  
  tag: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'a', 'router-link'].includes(value)
  },
  
  href: {
    type: String,
    default: null
  },
  
  disabled: {
    type: Boolean,
    default: false
  },
  
  loading: {
    type: Boolean,
    default: false
  },
  
  // Styling
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'success', 'warning', 'error', 'info', 'outline', 'ghost'].includes(value)
  },
  
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  
  rounded: {
    type: String,
    default: 'md',
    validator: (value) => ['none', 'sm', 'md', 'lg', 'xl', 'full'].includes(value)
  },
  
  // Icons
  iconLeft: {
    type: [Object, Function],
    default: null
  },
  
  iconRight: {
    type: [Object, Function],
    default: null
  },
  
  // Custom classes
  class: {
    type: [String, Array, Object],
    default: ''
  }
})

const emit = defineEmits(['click'])

const handleClick = (event) => {
  if (!props.disabled && !props.loading) {
    emit('click', event)
  }
}

const baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed'

const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 focus:ring-primary-500 shadow-sm hover:shadow-md',
    secondary: 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200 border border-neutral-300 focus:ring-neutral-500',
    success: 'bg-success-600 text-white hover:bg-success-700 focus:ring-success-500 shadow-sm',
    warning: 'bg-warning-600 text-white hover:bg-warning-700 focus:ring-warning-500 shadow-sm',
    error: 'bg-error-600 text-white hover:bg-error-700 focus:ring-error-500 shadow-sm',
    info: 'bg-info-600 text-white hover:bg-info-700 focus:ring-info-500 shadow-sm',
    outline: 'border border-primary-300 text-primary-700 hover:bg-primary-50 focus:ring-primary-500',
    ghost: 'text-neutral-700 hover:bg-neutral-100 focus:ring-neutral-500'
  }
  return variants[props.variant] || variants.primary
})

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'px-2.5 py-1.5 text-xs',
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-2 text-sm',
    lg: 'px-4 py-2 text-base',
    xl: 'px-6 py-3 text-base'
  }
  return sizes[props.size] || sizes.md
})

const roundedClasses = computed(() => {
  const rounded = {
    none: 'rounded-none',
    sm: 'rounded-sm',
    md: 'rounded-md',
    lg: 'rounded-lg',
    xl: 'rounded-xl',
    full: 'rounded-full'
  }
  return rounded[props.rounded] || rounded.md
})

const iconClasses = computed(() => {
  const iconSizes = {
    xs: 'h-3 w-3',
    sm: 'h-4 w-4',
    md: 'h-4 w-4',
    lg: 'h-5 w-5',
    xl: 'h-5 w-5'
  }
  
  const baseIconClasses = iconSizes[props.size] || iconSizes.md
  
  if (props.iconLeft && props.iconRight) {
    return baseIconClasses
  } else if (props.iconLeft) {
    return `${baseIconClasses} ${props.text || $slots.default ? 'mr-2' : ''}`
  } else if (props.iconRight) {
    return `${baseIconClasses} ${props.text || $slots.default ? 'ml-2' : ''}`
  }
  
  return baseIconClasses
})

const buttonClasses = computed(() => {
  return [
    baseClasses,
    variantClasses.value,
    sizeClasses.value,
    roundedClasses.value,
    props.class
  ].filter(Boolean).join(' ')
})
</script>