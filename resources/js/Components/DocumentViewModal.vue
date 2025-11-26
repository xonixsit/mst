<template>
  <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative min-h-screen flex items-center justify-center p-4">
      <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-screen overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <div>
            <h3 class="text-lg font-medium text-gray-900">{{ document?.name }}</h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ document?.client?.user?.first_name }} {{ document?.client?.user?.last_name }} • 
              {{ formatFileSize(document?.file_size) }}
            </p>
          </div>
          <div class="flex items-center space-x-2">
            <button
              @click="downloadDocument"
              class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
            >
              Download
            </button>
            <button
              @click="$emit('close')"
              class="p-2 text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="relative" style="height: calc(100vh - 200px);">
          <!-- PDF Viewer -->
          <PdfViewer
            v-if="isPDF"
            :pdf-url="pdfViewerUrl"
            :document-id="document.id.toString()"
            :document-name="document.name"
            :height="600"
          />
          
          <!-- Image Viewer -->
          <div v-else-if="isImage" class="h-full bg-gray-50 p-4">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-sm font-medium text-gray-900">Image Preview</h4>
              <div class="flex items-center space-x-2">
                <button
                  @click="zoomOut"
                  :disabled="zoom <= 0.5"
                  class="p-2 text-gray-600 hover:text-gray-900 disabled:opacity-50 bg-white border border-gray-300 rounded"
                >
                  <MinusIcon class="w-4 h-4" />
                </button>
                <span class="text-sm text-gray-600 px-2">{{ Math.round(zoom * 100) }}%</span>
                <button
                  @click="zoomIn"
                  :disabled="zoom >= 3"
                  class="p-2 text-gray-600 hover:text-gray-900 disabled:opacity-50 bg-white border border-gray-300 rounded"
                >
                  <PlusIcon class="w-4 h-4" />
                </button>
                <button
                  @click="resetZoom"
                  class="px-3 py-2 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50"
                >
                  Reset
                </button>
              </div>
            </div>
            <div class="overflow-auto h-full bg-white rounded border border-gray-200 p-4">
              <img
                :src="documentUrl"
                :alt="document.name"
                :style="{ transform: `scale(${zoom})`, transformOrigin: 'top left' }"
                class="max-w-full h-auto mx-auto transition-transform duration-200"
                @load="onDocumentLoad"
                @error="onDocumentError"
              />
            </div>
          </div>
          
          <!-- Unsupported File Type -->
          <div v-else class="h-full bg-gray-50 flex items-center justify-center">
            <div class="text-center">
              <DocumentIcon class="mx-auto h-16 w-16 text-gray-400" />
              <h3 class="mt-4 text-lg font-medium text-gray-900">Preview not available</h3>
              <p class="mt-2 text-sm text-gray-500">
                This file type ({{ document?.mime_type || 'unknown' }}) cannot be previewed in the browser.
              </p>
              <div class="mt-6">
                <button
                  @click="downloadDocument"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium"
                >
                  Download to View
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { XMarkIcon, PlusIcon, MinusIcon, DocumentIcon } from '@heroicons/vue/24/outline'
import PdfViewer from '@/Components/PdfViewer.vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  document: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close'])

const zoom = ref(1)

const documentUrl = computed(() => {
  if (!props.document) return ''
  return `/admin/documents/${props.document.id}/download`
})

const pdfViewerUrl = computed(() => {
  if (!props.document) return ''
  return `/admin/documents/${props.document.id}/download`
})

const isPDF = computed(() => {
  if (!props.document) return false
  
  // Check mime_type first
  if (props.document.mime_type === 'application/pdf') {
    return true
  }
  
  // Check file extension from name
  if (props.document.name) {
    const fileExtension = props.document.name.toLowerCase().split('.').pop()
    if (fileExtension === 'pdf') {
      return true
    }
  }
  
  // Check file extension from file_path
  if (props.document.file_path) {
    const fileExtension = props.document.file_path.toLowerCase().split('.').pop()
    if (fileExtension === 'pdf') {
      return true
    }
  }
  
  return false
})

const isImage = computed(() => {
  if (!props.document) return false
  return props.document.mime_type?.startsWith('image/') ||
         /\.(jpg|jpeg|png|gif|bmp|webp)$/i.test(props.document.name)
})

const formatFileSize = (bytes) => {
  if (!bytes) return 'Unknown'
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

const zoomIn = () => {
  if (zoom.value < 3) {
    zoom.value = Math.min(3, zoom.value + 0.25)
  }
}

const zoomOut = () => {
  if (zoom.value > 0.5) {
    zoom.value = Math.max(0.5, zoom.value - 0.25)
  }
}

const resetZoom = () => {
  zoom.value = 1
}

const downloadDocument = () => {
  if (!props.document) return
  
  const downloadUrl = `/admin/documents/${props.document.id}/download?download=1`
  const filename = props.document.original_name || props.document.name || 'document.pdf'
  
  // Simple and reliable approach
  const link = document.createElement('a')
  link.href = downloadUrl
  link.download = filename
  link.style.display = 'none'
  
  // Add to DOM, click, and remove
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const onDocumentLoad = () => {
  console.log('Document loaded successfully')
}

const onDocumentError = (error) => {
  console.error('Document load error:', error)
}

// Reset zoom when document changes
watch(() => props.document, () => {
  zoom.value = 1
})

// Handle escape key
const handleEscape = (event) => {
  if (event.key === 'Escape' && props.show) {
    emit('close')
  }
}

// Add/remove event listener for escape key
watch(() => props.show, (newShow) => {
  if (newShow) {
    document.addEventListener('keydown', handleEscape)
    document.body.style.overflow = 'hidden'
  } else {
    document.removeEventListener('keydown', handleEscape)
    document.body.style.overflow = ''
  }
})
</script>

<style scoped>
/* Ensure modal is above everything */
.fixed {
  z-index: 9999;
}
</style>