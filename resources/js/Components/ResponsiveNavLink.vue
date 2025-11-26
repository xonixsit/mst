<template>
  <component
    :is="tag"
    :href="href"
    :class="classes"
    v-bind="$attrs"
  >
    <slot />
  </component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  href: {
    type: String,
    required: true
  },
  active: {
    type: Boolean,
    default: false
  },
  tag: {
    type: String,
    default: 'a'
  }
})

const classes = computed(() => {
  return [
    'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition-colors duration-200',
    props.active
      ? 'border-primary-500 text-primary-700 bg-primary-50'
      : 'border-transparent text-neutral-600 hover:text-neutral-800 hover:bg-neutral-50 hover:border-neutral-300'
  ].join(' ')
})
</script>