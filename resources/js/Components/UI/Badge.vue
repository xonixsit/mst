<template>
  <span :class="badgeClasses">
    <component
      v-if="icon"
      :is="icon"
      :class="iconClasses"
    />
    <slot>{{ text }}</slot>
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  text: {
    type: String,
    default: ''
  },
  
  variant: {
    type: String,
    default: 'neutral',
    validator: (value) => ['primary', 'secondary', 'success', 'warning', 'error', 'info', 'neutral'].includes(value)
  },
  
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value)
  },
  
  rounded: {
    type: String,
    default: 'full',
    validator: (value) => ['none', 'sm', 'md', 'lg', 'xl', 'full'].includes(value)
  },
  
  icon: {
    type: [Object, Function],
    default: null
  },
  
  dot: {
    type: Boolean,
    default: false
  },
  
  class: {
    type: [String, Array, Object],
    default: ''
  }
})

const baseClasses = 'inline-flex items-center font-medium'

const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-primary-100 text-primary-800',
    secondary: 'bg-secondary-100 text-secondary-800',
    success: 'bg-success-100 text-success-800',
    warning: 'bg-warning-100 text-warning-800',
    error: 'bg-error-100 text-error-800',
    info: 'bg-info-100 text-info-800',
    neutral: 'bg-neutral-100 text-neutral-800'
  }
  return variants[props.variant] || variants.neutral
})

const sizeClasses = computed(() => {
  if (props.dot) {
    const dotSizes = {
      xs: 'h-2 w-2',
      sm: 'h-2.5 w-2.5',
      md: 'h-3 w-3',
      lg: 'h-3.5 w-3.5'
    }
    return dotSizes[props.size] || dotSizes.md
  }
  
  const sizes = {
    xs: 'px-2 py-0.5 text-xs',
    sm: 'px-2.5 py-0.5 text-xs',
    md: 'px-2.5 py-0.5 text-sm',
    lg: 'px-3 py-1 text-sm'
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
  return rounded[props.rounded] || rounded.full
})

const iconClasses = computed(() => {
  const iconSizes = {
    xs: 'h-3 w-3',
    sm: 'h-3 w-3',
    md: 'h-4 w-4',
    lg: 'h-4 w-4'
  }
  
  const baseIconClasses = iconSizes[props.size] || iconSizes.md
  return `${baseIconClasses} ${props.text || $slots.default ? 'mr-1' : ''}`
})

const badgeClasses = computed(() => {
  return [
    baseClasses,
    variantClasses.value,
    sizeClasses.value,
    roundedClasses.value,
    props.class
  ].filter(Boolean).join(' ')
})
</script>