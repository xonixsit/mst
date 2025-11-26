<template>
  <AppLayout>
    <template #header>
      <div class="flex flex-col space-y-4 lg:space-y-0 lg:flex-row lg:items-start lg:justify-between">
        <div class="min-w-0 flex-1 lg:pr-4">
          <h1 class="text-2xl font-bold text-gray-900 break-words">{{ document.name }}</h1>
          <p class="mt-1 text-sm text-gray-600 break-words">
            Uploaded by {{ document.client?.user?.first_name }} {{ document.client?.user?.last_name }} 
            on {{ formatDate(document.created_at) }}
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 flex-shrink-0 lg:ml-4">
          <button
            @click="downloadDocument"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium flex items-center justify-center transition-colors duration-200 whitespace-nowrap"
          >
            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download
          </button>
          <button
            @click="router.visit('/admin/documents')"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200 whitespace-nowrap"
          >
            Back to Documents
          </button>
        </div>
        </div>
    </template>

    <div class="document-detail max-w-7xl mx-auto">
      <!-- Document Info Panel -->
      <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Document Information</h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <dt class="text-sm font-medium text-gray-500">Client</dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ document.client?.user?.first_name }} {{ document.client?.user?.last_name }}
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Document Type</dt>
              <dd class="mt-1 text-sm text-gray-900 capitalize">{{ document.document_type?.replace('_', ' ') }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Tax Year</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ document.tax_year || 'N/A' }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Status</dt>
              <dd class="mt-2">
                <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg" :class="getStatusClass(document.status)">
                  <svg v-if="document.status === 'approved'" class="w-4 h-4 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                  </svg>
                  <svg v-else-if="document.status === 'rejected'" class="w-4 h-4 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                  </svg>
                  <svg v-else class="w-4 h-4 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                  </svg>
                  <span class="whitespace-nowrap">
                    {{ displayStatus }}
                  </span>
                </span>
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">File Size</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatFileSize(document.file_size) }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Uploaded</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ formatDate(document.created_at) }}</dd>
            </div>
          </div>
          
          <!-- Status Update Section -->
          <div class="mt-8 pt-6 border-t border-gray-200">
            <h4 class="text-lg font-medium text-gray-900 mb-6">Update Document Status</h4>
            <div class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                  <select
                    v-model="newStatus"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="pending">🕐 Pending Review</option>
                    <option value="approved">✅ Approved</option>
                    <option value="rejected">❌ Rejected</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                  <input
                    v-model="statusNotes"
                    type="text"
                    placeholder="Add review notes or comments..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
              </div>
              <div class="flex justify-end">
                <button
                  @click="updateStatus"
                  :disabled="updating"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                >
                  {{ updating ? 'Updating Status...' : 'Update Status' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Document Viewer -->
      <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Document Preview</h3>
        </div>
        
        <div class="p-6">
          <!-- PDF Viewer -->
          <div v-if="isPDF" class="pdf-viewer-simple">
            <iframe
              :src="pdfViewerUrl + '#toolbar=1&navpanes=1&scrollbar=1'"
              class="w-full border border-gray-300 rounded-lg shadow-lg"
              style="height: 800px; min-height: 800px;"
              frameborder="0"
              allowfullscreen
            ></iframe>
          </div>
          
          <!-- Image Viewer -->
          <div v-else-if="isImage" class="image-viewer bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-sm font-medium text-gray-900">Image Preview</h4>
              <div class="flex items-center space-x-2">
                <button
                  @click="zoomOut"
                  :disabled="zoom <= 0.5"
                  class="p-2 text-gray-600 hover:text-gray-900 disabled:opacity-50 bg-white border border-gray-300 rounded"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                  </svg>
                </button>
                <span class="text-sm text-gray-600 px-2">{{ Math.round(zoom * 100) }}%</span>
                <button
                  @click="zoomIn"
                  :disabled="zoom >= 3"
                  class="p-2 text-gray-600 hover:text-gray-900 disabled:opacity-50 bg-white border border-gray-300 rounded"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                  </svg>
                </button>
                <button
                  @click="resetZoom"
                  class="px-3 py-2 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50"
                >
                  Reset
                </button>
                <button
                  @click="downloadDocument"
                  class="px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                  Download
                </button>
              </div>
            </div>
            <div class="overflow-auto max-h-96 bg-white rounded border border-gray-200 p-4">
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
          <div v-else class="unsupported-file bg-gray-50 rounded-lg">
            <div class="text-center py-16">
              <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <h3 class="mt-4 text-lg font-medium text-gray-900">Preview not available</h3>
              <p class="mt-2 text-sm text-gray-500">
                This file type ({{ document.mime_type || 'unknown' }}) cannot be previewed in the browser.
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
    </div></div>
  </AppLayout>
</template>

<script>
import { ref, computed, onMounted, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

export default {
  name: 'AdminDocumentDetail',
  components: {
    AppLayout
  },
  props: {
    document: Object
  },
  setup(props) {
    const zoom = ref(1)
    const newStatus = ref(props.document.status)
    const statusNotes = ref('')
    const updating = ref(false)
    const loading = ref(true)

    const documentUrl = computed(() => {
      return `/admin/documents/${props.document.id}/download`
    })

    const pdfViewerUrl = computed(() => {
      if (isPDF.value) {
        // Use the download endpoint for PDF viewing to ensure proper access control
        return `/admin/documents/${props.document.id}/download`
      }
      return documentUrl.value
    })

    const isPDF = computed(() => {
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
      return props.document.mime_type?.startsWith('image/') ||
             /\.(jpg|jpeg|png|gif|bmp|webp)$/i.test(props.document.name)
    })

    const displayStatus = computed(() => {
      const status = props.document?.status
      if (!status) return 'Pending'
      return status.charAt(0).toUpperCase() + status.slice(1)
    })

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const formatFileSize = (bytes) => {
      if (!bytes) return 'Unknown'
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(1024))
      return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
    }

    const getStatusClass = (status) => {
      const classes = {
        pending: 'bg-yellow-50 text-yellow-700 border border-yellow-200',
        approved: 'bg-green-50 text-green-700 border border-green-200',
        rejected: 'bg-red-50 text-red-700 border border-red-200'
      }
      return classes[status] || 'bg-gray-50 text-gray-700 border border-gray-200'
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

    const onDocumentLoad = () => {
      console.log('Document loaded successfully')
      loading.value = false
    }

    const onDocumentError = (error) => {
      console.error('Document load error:', error)
      loading.value = false
    }

    onMounted(() => {
      loading.value = false
    })

    const downloadDocument = () => {
      const downloadUrl = `/admin/documents/${props.document.id}/download?download=1`
      const filename = props.document.original_name || props.document.name || 'document.pdf'
      
      // Simple and reliable approach - direct window location
      // This ensures the browser handles the download properly
      const link = document.createElement('a')
      link.href = downloadUrl
      link.download = filename
      link.style.display = 'none'
      
      // Add to DOM, click, and remove
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
    }

    const updateStatus = async () => {
      updating.value = true
      
      try {
        await router.patch(`/admin/documents/${props.document.id}/status`, {
          status: newStatus.value,
          notes: statusNotes.value
        }, {
          preserveState: true,
          onSuccess: () => {
            // Update local document status
            props.document.status = newStatus.value
            statusNotes.value = ''
          }
        })
      } catch (error) {
        console.error('Failed to update status:', error)
      } finally {
        updating.value = false
      }
    }

    return {
      zoom,
      newStatus,
      statusNotes,
      updating,
      loading,
      documentUrl,
      pdfViewerUrl,
      isPDF,
      isImage,
      displayStatus,
      formatDate,
      formatFileSize,
      getStatusClass,
      zoomIn,
      zoomOut,
      resetZoom,
      onDocumentLoad,
      onDocumentError,
      downloadDocument,
      updateStatus,
      router
    }
  }
}
</script>

<style scoped>
.document-detail {
  @apply px-4 sm:px-6 lg:px-8 py-6 bg-gray-50 min-h-screen;
}

.image-viewer img {
  @apply shadow-lg rounded transition-transform duration-200;
}
</style>