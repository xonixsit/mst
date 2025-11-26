<template>
  <div class="pdf-viewer-container">

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center h-96 bg-gray-50">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Loading PDF...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex items-center justify-center h-96 bg-gray-50">
      <div class="text-center">
        <ExclamationTriangleIcon class="w-12 h-12 text-red-500 mx-auto" />
        <h3 class="mt-4 text-lg font-medium text-gray-900">Failed to load PDF</h3>
        <p class="mt-2 text-gray-600">{{ error }}</p>
        <div class="mt-6 space-x-3">
          <button
            @click="retryLoad"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Retry
          </button>
          <button
            @click="downloadPdf"
            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
          >
            Download Instead
          </button>
        </div>
      </div>
    </div>

    <!-- PDF Canvas Container -->
    <div
      v-else
      ref="canvasContainer"
      class="pdf-canvas-container bg-white overflow-auto"
      :style="{ height: containerHeight + 'px' }"
    >
      <div class="flex justify-center p-4">
        <canvas
          ref="pdfCanvas"
          class="border border-gray-300 shadow-lg"
          :style="{ maxWidth: '100%' }"
        ></canvas>
      </div>
    </div>

    <!-- PDF Viewer -->
    <div v-if="showFallback && !loading && !error" class="pdf-fallback">
      <iframe
        :src="pdfUrl + '#toolbar=1&navpanes=1&scrollbar=1'"
        class="w-full border border-gray-300 rounded-lg shadow-lg"
        style="height: 800px; min-height: 800px;"
        frameborder="0"
        allowfullscreen
      ></iframe>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch, nextTick } from 'vue'
import {
  ChevronLeftIcon,
  ChevronRightIcon,
  PlusIcon,
  MinusIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

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
    default: 'document.pdf'
  },
  height: {
    type: Number,
    default: 600
  }
})

// Reactive state
const loading = ref(true)
const error = ref(null)
const showFallback = ref(false)
const pdfDoc = ref(null)
const currentPage = ref(1)
const totalPages = ref(0)
const scale = ref(1)
const pageInput = ref(1)
const pdfCanvas = ref(null)
const canvasContainer = ref(null)

// Computed properties
const containerHeight = computed(() => props.height)

// PDF.js library loading
let pdfjsLib = null

// Load PDF.js library
const loadPdfJs = async () => {
  try {
    // Skip PDF.js for now and use iframe fallback directly
    console.log('Using iframe fallback for PDF viewing')
    showFallback.value = true
    loading.value = false
    return
    
    // Try to load PDF.js from CDN
    if (!window.pdfjsLib) {
      const script = document.createElement('script')
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js'
      script.onload = () => {
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js'
        initializePdf()
      }
      script.onerror = () => {
        console.warn('Failed to load PDF.js from CDN, falling back to browser viewer')
        showFallback.value = true
        loading.value = false
      }
      document.head.appendChild(script)
    } else {
      pdfjsLib = window.pdfjsLib
      initializePdf()
    }
  } catch (err) {
    console.error('Error loading PDF.js:', err)
    showFallback.value = true
    loading.value = false
  }
}

// Initialize PDF document
const initializePdf = async () => {
  try {
    pdfjsLib = window.pdfjsLib
    
    if (!pdfjsLib) {
      throw new Error('PDF.js library not available')
    }

    const loadingTask = pdfjsLib.getDocument(props.pdfUrl)
    pdfDoc.value = await loadingTask.promise
    totalPages.value = pdfDoc.value.numPages
    
    await renderCurrentPage()
    loading.value = false
  } catch (err) {
    console.error('Error loading PDF:', err)
    error.value = err.message || 'Failed to load PDF document'
    loading.value = false
  }
}

// Render current page
const renderCurrentPage = async () => {
  if (!pdfDoc.value || !pdfCanvas.value) return

  try {
    const page = await pdfDoc.value.getPage(currentPage.value)
    const viewport = page.getViewport({ scale: scale.value })
    
    const canvas = pdfCanvas.value
    const context = canvas.getContext('2d')
    
    canvas.height = viewport.height
    canvas.width = viewport.width
    
    const renderContext = {
      canvasContext: context,
      viewport: viewport
    }
    
    await page.render(renderContext).promise
    pageInput.value = currentPage.value
  } catch (err) {
    console.error('Error rendering page:', err)
    error.value = 'Failed to render PDF page'
  }
}

// Navigation methods
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    renderCurrentPage()
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    renderCurrentPage()
  }
}

const goToPage = () => {
  const page = parseInt(pageInput.value)
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    renderCurrentPage()
  } else {
    pageInput.value = currentPage.value
  }
}

// Zoom methods
const zoomIn = () => {
  if (scale.value < 2) {
    scale.value = Math.min(2, scale.value + 0.25)
    renderCurrentPage()
  }
}

const zoomOut = () => {
  if (scale.value > 0.5) {
    scale.value = Math.max(0.5, scale.value - 0.25)
    renderCurrentPage()
  }
}

const fitToWidth = async () => {
  if (!pdfDoc.value || !canvasContainer.value) return
  
  try {
    const page = await pdfDoc.value.getPage(currentPage.value)
    const viewport = page.getViewport({ scale: 1 })
    const containerWidth = canvasContainer.value.clientWidth - 32 // Account for padding
    
    scale.value = containerWidth / viewport.width
    await renderCurrentPage()
  } catch (err) {
    console.error('Error fitting to width:', err)
  }
}

// External actions
const openInNewTab = () => {
  window.open(props.pdfUrl, '_blank')
}

const downloadPdf = () => {
  const link = document.createElement('a')
  link.href = `/admin/documents/${props.documentId}/download?download=1`
  link.download = props.documentName
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const retryLoad = () => {
  error.value = null
  loading.value = true
  showFallback.value = false
  initializePdf()
}

// Keyboard navigation
const handleKeydown = (event) => {
  if (event.target.tagName === 'INPUT') return
  
  switch (event.key) {
    case 'ArrowLeft':
      event.preventDefault()
      previousPage()
      break
    case 'ArrowRight':
      event.preventDefault()
      nextPage()
      break
    case '+':
    case '=':
      event.preventDefault()
      zoomIn()
      break
    case '-':
      event.preventDefault()
      zoomOut()
      break
  }
}

// Lifecycle
onMounted(() => {
  loadPdfJs()
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})

// Watch for scale changes
watch(scale, () => {
  if (pdfDoc.value && !loading.value) {
    renderCurrentPage()
  }
})
</script>

<style scoped>
.pdf-viewer-container {
  @apply bg-white border border-gray-200 rounded-lg overflow-hidden;
}

.pdf-canvas-container {
  @apply relative;
}

.pdf-fallback iframe {
  @apply w-full;
}

/* Custom scrollbar for canvas container */
.pdf-canvas-container::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.pdf-canvas-container::-webkit-scrollbar-track {
  @apply bg-gray-100;
}

.pdf-canvas-container::-webkit-scrollbar-thumb {
  @apply bg-gray-400 rounded;
}

.pdf-canvas-container::-webkit-scrollbar-thumb:hover {
  @apply bg-gray-500;
}
</style>