<template>
  <component
    :is="tag"
    :href="href"
    :method="method"
    :as="as"
    class="block w-full px-4 py-2 text-left text-sm leading-5 text-neutral-700 hover:bg-neutral-100 focus:outline-none focus:bg-neutral-100 transition duration-150 ease-in-out"
    v-bind="$attrs"
  >
    <slot />
  </component>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  href: {
    type: String,
    default: '#'
  },
  method: {
    type: String,
    default: 'get'
  },
  as: {
    type: String,
    default: 'a'
  }
})

const tag = props.method === 'get' ? 'a' : Link
</script>