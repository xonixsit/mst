<template>
  <div>
    <!-- Loading state -->
    <div v-if="loading" class="flex items-center justify-center p-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-2 text-gray-600">{{ loadingText }}</span>
    </div>
    
    <!-- Error state -->
    <div v-else-if="error" class="p-4 bg-red-50 border border-red-200 rounded-md">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Loading Error</h3>
          <p class="mt-1 text-sm text-red-700">{{ error }}</p>
          <button 
            @click="retry" 
            class="mt-2 text-sm bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded"
          >
            Retry
          </button>
        </div>
      </div>
    </div>
    
    <!-- Loaded component -->
    <component 
      v-else-if="loadedComponent" 
      :is="loadedComponent" 
      v-bind="componentProps"
      @error="handleComponentError"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, defineAsyncComponent } from 'vue'

const props = defineProps({
  componentLoader: {
    type: Function,
    required: true
  },
  componentProps: {
    type: Object,
    default: () => ({})
  },
  loadingText: {
    type: String,
    default: 'Loading...'
  },
  delay: {
    type: Number,
    default: 0
  }
})

const loading = ref(true)
const error = ref(null)
const loadedComponent = ref(null)

const loadComponent = async () => {
  try {
    loading.value = true
    error.value = null
    
    // Add delay if specified
    if (props.delay > 0) {
      await new Promise(resolve => setTimeout(resolve, props.delay))
    }
    
    const component = await props.componentLoader()
    loadedComponent.value = defineAsyncComponent(() => Promise.resolve(component))
    
  } catch (err) {
    error.value = err.message || 'Failed to load component'
    console.error('LazyLoader error:', err)
  } finally {
    loading.value = false
  }
}

const retry = () => {
  loadComponent()
}

const handleComponentError = (err) => {
  error.value = err.message || 'Component error occurred'
}

onMounted(() => {
  loadComponent()
})
</script>