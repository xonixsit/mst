<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-purple-50 to-indigo-50"></div>
        <div
          class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-purple-100/40 to-transparent rounded-bl-full">
        </div>
        <div
          class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-indigo-100/30 to-transparent rounded-tr-full">
        </div>

        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Dynamic Document Icon -->
            <div
              class="w-16 h-16 bg-gradient-to-br from-purple-500 via-purple-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-purple-100">
              <component :is="documentIcon" class="w-8 h-8 text-white" />
            </div>

            <!-- Title Section -->
            <div>
              <h1
                class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-purple-900 to-indigo-900 bg-clip-text text-transparent break-words">
                {{ document.name }}
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium break-words">
                Uploaded by {{ document.client?.user?.first_name }} {{ document.client?.user?.last_name }}
                on {{ formatDate(document.created_at) }}
              </p>

              <!-- Document Info -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-purple-700">{{ document.document_type?.replace('_', ' ') ||
                    'Document' }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-indigo-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-indigo-700">{{ formatFileSize(document.file_size) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
            <button @click="downloadDocument"
              class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center justify-center text-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group font-semibold">
              <ArrowDownTrayIcon class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:translate-y-1" />
              <span>Download</span>
            </button>
            <button @click="router.visit('/admin/documents')"
              class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl flex items-center justify-center text-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group font-semibold">
              <ArrowLeftIcon class="w-5 h-5 mr-2" />
              <span>Back to Documents</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <!-- Enhanced Document Info Panel -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                <InformationCircleIcon class="w-4 h-4 text-white" />
              </div>
              <h3 class="text-lg font-bold text-gray-900">Document Information</h3>
            </div>
          </div>
          <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-4 border border-blue-200">
                <dt class="text-sm font-semibold text-blue-700 mb-2">Client</dt>
                <dd class="text-sm font-bold text-blue-900">
                  {{ document.client?.user?.first_name }} {{ document.client?.user?.last_name }}
                </dd>
              </div>
              <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-xl p-4 border border-purple-200">
                <dt class="text-sm font-semibold text-purple-700 mb-2">Document Type</dt>
                <dd class="text-sm font-bold text-purple-900 capitalize">{{ document.document_type?.replace('_', ' ') }}
                </dd>
              </div>
              <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-4 border border-green-200">
                <dt class="text-sm font-semibold text-green-700 mb-2">Tax Year</dt>
                <dd class="text-sm font-bold text-green-900">{{ document.tax_year || 'N/A' }}</dd>
              </div>
              <div class="bg-gradient-to-br from-amber-50 to-orange-100 rounded-xl p-4 border border-amber-200">
                <dt class="text-sm font-semibold text-amber-700 mb-2">Status</dt>
                <dd class="mt-1">
                  <span class="inline-flex items-center px-3 py-2 text-sm font-bold rounded-xl shadow-sm"
                    :class="getStatusClass(document.status)">
                    <CheckCircleIcon v-if="document.status === 'approved'" class="w-4 h-4 mr-2 flex-shrink-0" />
                    <XCircleIcon v-else-if="document.status === 'rejected'" class="w-4 h-4 mr-2 flex-shrink-0" />
                    <ClockIcon v-else class="w-4 h-4 mr-2 flex-shrink-0" />
                    <span class="whitespace-nowrap">
                      {{ displayStatus }}
                    </span>
                  </span>
                </dd>
              </div>
              <div class="bg-gradient-to-br from-cyan-50 to-blue-100 rounded-xl p-4 border border-cyan-200">
                <dt class="text-sm font-semibold text-cyan-700 mb-2">File Size</dt>
                <dd class="text-sm font-bold text-cyan-900">{{ formatFileSize(document.file_size) }}</dd>
              </div>
              <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-4 border border-gray-200">
                <dt class="text-sm font-semibold text-gray-700 mb-2">Uploaded</dt>
                <dd class="text-sm font-bold text-gray-900">{{ formatDate(document.created_at) }}</dd>
              </div>
            </div>

            <!-- Enhanced Status Update Section -->
            <div class="mt-8 pt-8 border-t border-gray-200">
              <div class="bg-gradient-to-br from-indigo-50 to-purple-100 rounded-xl p-6 border border-indigo-200">
                <div class="flex items-center mb-6">
                  <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center mr-3">
                    <ClockIcon class="w-4 h-4 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-indigo-900">Update Document Status</h4>
                </div>
                <div class="space-y-6">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-indigo-700">New Status</label>
                      <select v-model="newStatus"
                        class="w-full px-4 py-3 border-2 border-indigo-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white text-gray-900">
                        <option value="pending">🕐 Pending Review</option>
                        <option value="approved">✅ Approved</option>
                        <option value="rejected">❌ Rejected</option>
                      </select>
                    </div>
                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-indigo-700">Notes (Optional)</label>
                      <input v-model="statusNotes" type="text" placeholder="Add review notes or comments..."
                        class="w-full px-4 py-3 border-2 border-indigo-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-gray-900" />
                    </div>
                  </div>
                  <div class="flex justify-end">
                    <button @click="updateStatus" :disabled="updating"
                      class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center">
                      <CheckCircleIcon v-if="!updating" class="w-5 h-5 mr-2" />
                      <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                      </svg>
                      {{ updating ? 'Updating Status...' : 'Update Status' }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Document Viewer -->
      <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
              <DocumentTextIcon class="w-4 h-4 text-white" />
            </div>
            <h3 class="text-lg font-bold text-gray-900">Document Preview</h3>
          </div>
        </div>

        <div class="p-8">
          <!-- Enhanced PDF Viewer -->
          <div v-if="isPDF" class="pdf-viewer-simple">
            <iframe :src="pdfViewerUrl + '#toolbar=1&navpanes=1&scrollbar=1'"
              class="w-full border-2 border-gray-200 rounded-xl shadow-xl" style="height: 800px; min-height: 800px;"
              frameborder="0" allowfullscreen></iframe>
          </div>

          <!-- Image Viewer -->
          <div v-else-if="isImage" class="image-viewer bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-sm font-medium text-gray-900">Image Preview</h4>
              <div class="flex items-center space-x-2">
                <button @click="zoomOut" :disabled="zoom <= 0.5"
                  class="p-2 text-gray-600 hover:text-gray-900 disabled:opacity-50 bg-white border border-gray-300 rounded">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                  </svg>
                </button>
                <span class="text-sm text-gray-600 px-2">{{ Math.round(zoom * 100) }}%</span>
                <button @click="zoomIn" :disabled="zoom >= 3"
                  class="p-2 text-gray-600 hover:text-gray-900 disabled:opacity-50 bg-white border border-gray-300 rounded">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                  </svg>
                </button>
                <button @click="resetZoom"
                  class="px-3 py-2 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">
                  Reset
                </button>
                <button @click="downloadDocument"
                  class="px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                  Download
                </button>
              </div>
            </div>
            <div class="overflow-auto max-h-96 bg-white rounded border border-gray-200 p-4">
              <img :src="documentUrl" :alt="document.name"
                :style="{ transform: `scale(${zoom})`, transformOrigin: 'top left' }"
                class="max-w-full h-auto mx-auto transition-transform duration-200" @load="onDocumentLoad"
                @error="onDocumentError" />
            </div>
          </div>

          <!-- Unsupported File Type -->
          <div v-else class="unsupported-file bg-gray-50 rounded-lg">
            <div class="text-center py-16">
              <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
              </svg>
              <h3 class="mt-4 text-lg font-medium text-gray-900">Preview not available</h3>
              <p class="mt-2 text-sm text-gray-500">
                This file type ({{ document.mime_type || 'unknown' }}) cannot be previewed in the browser.
              </p>
              <div class="mt-6">
                <button @click="downloadDocument"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium">
                  Download to View
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
  import { ref, computed, onMounted, nextTick } from 'vue'
  import { router } from '@inertiajs/vue3'
  import {
    DocumentTextIcon,
    ArrowDownTrayIcon,
    ArrowLeftIcon,
    InformationCircleIcon,
    CheckCircleIcon,
    XCircleIcon,
    ClockIcon,
    PhotoIcon,
    DocumentArrowUpIcon,
    ReceiptRefundIcon,
    BanknotesIcon,
    HomeIcon,
    IdentificationIcon,
    AcademicCapIcon,
    HeartIcon,
    BriefcaseIcon
  } from '@heroicons/vue/24/outline'
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

      const documentIcon = computed(() => {
        const docType = props.document?.document_type?.toLowerCase()
        const fileName = props.document?.name?.toLowerCase()

        // Check for image files
        if (isImage.value) {
          return PhotoIcon
        }

        // Check for specific document types
        if (docType?.includes('tax') || fileName?.includes('tax')) {
          return ReceiptRefundIcon
        }
        if (docType?.includes('bank') || docType?.includes('financial') || fileName?.includes('bank')) {
          return BanknotesIcon
        }
        if (docType?.includes('mortgage') || docType?.includes('property') || fileName?.includes('mortgage')) {
          return HomeIcon
        }
        if (docType?.includes('id') || docType?.includes('passport') || docType?.includes('license')) {
          return IdentificationIcon
        }
        if (docType?.includes('education') || docType?.includes('diploma') || docType?.includes('certificate')) {
          return AcademicCapIcon
        }
        if (docType?.includes('medical') || docType?.includes('health') || fileName?.includes('medical')) {
          return HeartIcon
        }
        if (docType?.includes('employment') || docType?.includes('payroll') || docType?.includes('w2') || docType?.includes('1099')) {
          return BriefcaseIcon
        }
        if (docType?.includes('upload') || fileName?.includes('upload')) {
          return DocumentArrowUpIcon
        }

        // Default to document icon
        return DocumentTextIcon
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
        documentIcon,
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