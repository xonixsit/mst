<template>
  <div class="pdf-viewer-container h-full">
    <div v-if="loading" class="flex items-center justify-center h-full">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Loading PDF...</p>
      </div>
    </div>
    
    <div v-else-if="error" class="flex items-center justify-center h-full">
      <div class="text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Failed to load PDF</h3>
        <p class="text-gray-600">{{ error }}</p>
      </div>
    </div>
    
    <iframe
      v-else
      :src="pdfUrl"
      class="w-full h-full border-0"
      :style="{ height: height + 'px' }"
      @load="onLoad"
      @error="onError"
    ></iframe>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  pdfUrl: {
    type: String,
    required: true
  },
  documentId: {
    type: String,
    required: true
  },
  documentName: {
    type: String,
    default: 'Document'
  },
  height: {
    type: Number,
    default: 600
  }
})

const loading = ref(true)
const error = ref(null)

const pdfUrl = computed(() => {
  if (!props.pdfUrl) return ''
  
  // Add PDF viewer parameters for better display
  const url = new URL(props.pdfUrl, window.location.origin)
  url.searchParams.set('view', 'FitH')
  url.searchParams.set('toolbar', '1')
  url.searchParams.set('navpanes', '0')
  
  return url.toString()
})

const onLoad = () => {
  loading.value = false
  error.value = null
}

const onError = () => {
  loading.value = false
  error.value = 'Unable to display PDF. Please download the file to view it.'
}

onMounted(() => {
  // Set a timeout to handle cases where the iframe doesn't trigger load/error events
  setTimeout(() => {
    if (loading.value) {
      loading.value = false
    }
  }, 5000)
})
</script>

<style scoped>
.pdf-viewer-container {
  background-color: #f8f9fa;
}
</style>