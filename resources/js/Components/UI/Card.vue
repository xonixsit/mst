<template>
  <div :class="cardClasses">
    <!-- Card Header -->
    <div v-if="$slots.header || title" :class="headerClasses">
      <slot name="header">
        <div class="flex items-center justify-between">
          <h3 v-if="title" class="text-lg font-semibold text-neutral-900">
            {{ title }}
          </h3>
          <div v-if="$slots.headerActions" class="flex items-center space-x-2">
            <slot name="headerActions" />
          </div>
        </div>
      </slot>
    </div>

    <!-- Card Body -->
    <div v-if="$slots.default" :class="bodyClasses">
      <slot />
    </div>

    <!-- Card Footer -->
    <div v-if="$slots.footer" :class="footerClasses">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'bordered', 'elevated', 'flat'].includes(value)
  },
  
  padding: {
    type: String,
    default: 'md',
    validator: (value) => ['none', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  
  rounded: {
    type: String,
    default: 'lg',
    validator: (value) => ['none', 'sm', 'md', 'lg', 'xl', '2xl'].includes(value)
  },
  
  hover: {
    type: Boolean,
    default: false
  },
  
  class: {
    type: [String, Array, Object],
    default: ''
  }
})

const baseClasses = 'bg-white transition-all duration-200'

const variantClasses = computed(() => {
  const variants = {
    default: 'border border-neutral-200 shadow-sm',
    bordered: 'border-2 border-neutral-200',
    elevated: 'shadow-lg border border-neutral-100',
    flat: 'border-0'
  }
  return variants[props.variant] || variants.default
})

const roundedClasses = computed(() => {
  const rounded = {
    none: 'rounded-none',
    sm: 'rounded-sm',
    md: 'rounded-md',
    lg: 'rounded-lg',
    xl: 'rounded-xl',
    '2xl': 'rounded-2xl'
  }
  return rounded[props.rounded] || rounded.lg
})

const hoverClasses = computed(() => {
  return props.hover ? 'hover:shadow-md hover:-translate-y-0.5 cursor-pointer' : ''
})

const paddingClasses = computed(() => {
  const paddings = {
    none: '',
    sm: 'p-3',
    md: 'p-4',
    lg: 'p-6',
    xl: 'p-8'
  }
  return paddings[props.padding] || paddings.md
})

const headerClasses = computed(() => {
  const basePadding = props.padding === 'none' ? 'px-4 py-3' : 'px-6 py-4'
  return `${basePadding} border-b border-neutral-200 bg-neutral-50 ${props.rounded === 'lg' ? 'rounded-t-lg' : props.rounded === 'xl' ? 'rounded-t-xl' : props.rounded === '2xl' ? 'rounded-t-2xl' : ''}`
})

const bodyClasses = computed(() => {
  if (props.padding === 'none') {
    return 'p-0'
  }
  
  const paddings = {
    sm: 'p-3',
    md: 'p-4',
    lg: 'p-6',
    xl: 'p-8'
  }
  return paddings[props.padding] || paddings.md
})

const footerClasses = computed(() => {
  const basePadding = props.padding === 'none' ? 'px-4 py-3' : 'px-6 py-4'
  return `${basePadding} border-t border-neutral-200 bg-neutral-50 ${props.rounded === 'lg' ? 'rounded-b-lg' : props.rounded === 'xl' ? 'rounded-b-xl' : props.rounded === '2xl' ? 'rounded-b-2xl' : ''}`
})

const cardClasses = computed(() => {
  return [
    baseClasses,
    variantClasses.value,
    roundedClasses.value,
    hoverClasses.value,
    props.class
  ].filter(Boolean).join(' ')
})
</script>