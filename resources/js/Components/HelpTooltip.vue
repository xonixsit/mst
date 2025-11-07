<template>
  <div class="relative inline-block">
    <button
      @mouseenter="showTooltip = true"
      @mouseleave="showTooltip = false"
      @focus="showTooltip = true"
      @blur="showTooltip = false"
      type="button"
      class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600"
    >
      <QuestionMarkCircleIcon class="w-4 h-4" />
    </button>
    
    <!-- Tooltip -->
    <div
      v-if="showTooltip"
      class="absolute z-10 w-64 p-2 mt-1 text-sm text-white bg-gray-900 rounded-lg shadow-lg"
      :class="position === 'left' ? 'right-0' : 'left-0'"
    >
      <div class="relative">
        {{ text }}
        <!-- Arrow -->
        <div
          class="absolute w-2 h-2 bg-gray-900 transform rotate-45"
          :class="position === 'left' ? 'top-0 right-2 -mt-1' : 'top-0 left-2 -mt-1'"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { QuestionMarkCircleIcon } from '@heroicons/vue/24/outline'

// Props
defineProps({
  text: {
    type: String,
    required: true
  },
  position: {
    type: String,
    default: 'right',
    validator: (value) => ['left', 'right'].includes(value)
  }
})

// State
const showTooltip = ref(false)
</script>
</template>