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
    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200',
    props.active
      ? 'border-primary-500 text-primary-600'
      : 'border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300'
  ].join(' ')
})
</script>