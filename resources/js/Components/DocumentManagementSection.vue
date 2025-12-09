<template>
  <div class="space-y-6">
    <!-- Document Upload Section -->
    <div class="bg-gradient-to-br from-purple-50 to-indigo-100 rounded-xl p-6 border border-purple-200">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
            <CloudArrowUpIcon class="w-5 h-5 text-white" />
          </div>
          <div>
            <h3 class="text-lg font-bold text-purple-900">Upload Documents</h3>
            <p class="text-sm text-purple-700">Add new documents for this client</p>
          </div>
        </div>
      </div>

      <!-- Upload Form -->
      <form @submit.prevent="uploadDocument" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-purple-800 mb-2">Document Type</label>
            <select
              v-model="uploadForm.document_type"
              class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900"
              required
            >
              <option value="">Select document type</option>
              <option v-for="(label, value) in documentTypes" :key="value" :value="value">
                {{ label }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-purple-800 mb-2">Tax Year</label>
            <select
              v-model="uploadForm.tax_year"
              class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900"
            >
              <option value="">Select tax year</option>
              <option v-for="year in taxYears" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-purple-800 mb-2">Notes (Optional)</label>
          <textarea
            v-model="uploadForm.notes"
            rows="3"
            placeholder="Add any notes about this document..."
            class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none text-gray-900"
            maxlength="500"
          ></textarea>
        </div>

        <!-- File Upload Area -->
        <div class="relative">
          <input
            ref="fileInput"
            type="file"
            @change="handleFileSelect"
            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.txt,.xls,.xlsx"
            multiple
            class="hidden"
          />
          
          <div
            @click="$refs.fileInput.click()"
            @dragover.prevent
            @drop.prevent="handleFileDrop"
            class="border-2 border-dashed border-purple-300 rounded-xl p-8 text-center cursor-pointer hover:border-purple-400 hover:bg-purple-50 transition-all duration-200"
          >
            <CloudArrowUpIcon class="w-12 h-12 text-purple-400 mx-auto mb-4" />
            <p class="text-purple-700 font-semibold mb-2">Click to upload or drag and drop</p>
            <p class="text-sm text-purple-600">PDF, DOC, DOCX, JPG, PNG, GIF, TXT, XLS, XLSX (Max 10MB each)</p>
          </div>
        </div>

        <!-- Selected Files -->
        <div v-if="selectedFiles.length > 0" class="space-y-2">
          <h4 class="text-sm font-semibold text-purple-800">Selected Files:</h4>
          <div class="space-y-2">
            <div
              v-for="(file, index) in selectedFiles"
              :key="index"
              class="flex items-center justify-between bg-white p-3 rounded-lg border border-purple-200"
            >
              <div class="flex items-center space-x-3">
                <DocumentTextIcon class="w-5 h-5 text-purple-600" />
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ file.name }}</p>
                  <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</p>
                </div>
              </div>
              <button
                type="button"
                @click="removeFile(index)"
                class="text-red-500 hover:text-red-700 p-1"
              >
                <XMarkIcon class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>

        <!-- Upload Button -->
        <div class="flex justify-end">
          <button
            type="submit"
            :disabled="uploading || selectedFiles.length === 0"
            class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
          >
            <CloudArrowUpIcon v-if="!uploading" class="w-5 h-5" />
            <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ uploading ? 'Uploading...' : 'Upload Documents' }}</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Existing Documents -->
    <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
      <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
              <DocumentTextIcon class="w-4 h-4 text-white" />
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Client Documents</h3>
              <p class="text-sm text-gray-600">{{ documents.length }} documents uploaded</p>
            </div>
          </div>
          <button
            @click="refreshDocuments"
            class="text-purple-600 hover:text-purple-800 text-sm font-medium"
          >
            Refresh
          </button>
        </div>
      </div>

      <div v-if="documents.length === 0" class="p-12 text-center">
        <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-indigo-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
          <DocumentTextIcon class="w-8 h-8 text-purple-600" />
        </div>
        <p class="text-gray-500 font-medium">No documents uploaded yet</p>
        <p class="text-gray-400 text-sm mt-1">Upload documents using the form above</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Document
              </th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Type
              </th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Tax Year
              </th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Uploaded
              </th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-for="document in documents" :key="document.id" class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-50 transition-all duration-200 group">
              <td class="px-6 py-5 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-indigo-200 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                    <DocumentTextIcon class="w-5 h-5 text-purple-600" />
                  </div>
                  <div>
                    <div class="text-sm font-semibold text-gray-900">{{ document.name }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ formatFileSize(document.file_size) }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-5 whitespace-nowrap">
                <span class="text-sm font-medium text-gray-900">{{ documentTypes[document.document_type] || document.document_type }}</span>
              </td>
              <td class="px-6 py-5 whitespace-nowrap">
                <span class="text-sm font-semibold text-gray-900">{{ document.tax_year || '-' }}</span>
              </td>
              <td class="px-6 py-5 whitespace-nowrap">
                <span
                  :class="{
                    'bg-amber-100 text-amber-800 border border-amber-200': document.status === 'pending',
                    'bg-emerald-100 text-emerald-800 border border-emerald-200': document.status === 'approved',
                    'bg-rose-100 text-rose-800 border border-rose-200': document.status === 'rejected'
                  }"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm"
                >
                  <span v-if="document.status === 'approved'" class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-2"></span>
                  <span v-else-if="document.status === 'rejected'" class="w-1.5 h-1.5 bg-rose-400 rounded-full mr-2"></span>
                  <span v-else class="w-1.5 h-1.5 bg-amber-400 rounded-full mr-2"></span>
                  {{ document.status.charAt(0).toUpperCase() + document.status.slice(1) }}
                </span>
              </td>
              <td class="px-6 py-5 whitespace-nowrap">
                <span class="text-sm text-gray-600 font-medium">{{ formatDate(document.created_at) }}</span>
              </td>
              <td class="px-6 py-5 text-center">
                <div class="flex justify-center space-x-2">
                  <button
                    @click="viewDocument(document)"
                    class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                    title="View Document"
                  >
                    <EyeIcon class="w-3 h-3" />
                  </button>
                  <button
                    @click="updateDocumentStatus(document)"
                    class="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                    title="Update Status"
                  >
                    <PencilIcon class="w-3 h-3" />
                  </button>
                  <button
                    @click="deleteDocument(document)"
                    class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                    title="Delete Document"
                  >
                    <TrashIcon class="w-3 h-3" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PDF Viewer Modal -->
    <div v-if="showPdfViewer" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4">
      <div class="relative w-full max-w-4xl bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <div>
            <h3 class="text-lg font-bold text-gray-900">{{ selectedDocumentForView?.name }}</h3>
            <p class="text-sm text-gray-600 mt-1">{{ selectedDocumentForView?.formatted_file_size }}</p>
          </div>
          <button
            @click="closePdfViewer"
            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200"
          >
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>
        
        <!-- PDF Viewer -->
        <div class="flex-1 overflow-hidden">
          <PdfViewer
            v-if="selectedDocumentForView"
            :pdf-url="`/admin/documents/${selectedDocumentForView.id}/download`"
            :document-id="selectedDocumentForView.id.toString()"
            :document-name="selectedDocumentForView.name"
            :height="600"
          />
        </div>
      </div>
    </div>

    <!-- Document View Modal -->
    <DocumentViewModal
      :show="showDocumentModal"
      :document="selectedDocument"
      @close="closeDocumentModal"
    />

    <!-- Status Update Modal -->
    <div v-if="showStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-6 border w-96 shadow-2xl rounded-2xl bg-white">
        <div class="mt-3">
          <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-indigo-200 rounded-xl flex items-center justify-center">
              <PencilIcon class="w-5 h-5 text-purple-600" />
            </div>
            <h3 class="text-xl font-bold text-gray-900">Update Document Status</h3>
          </div>
          
          <div class="space-y-6">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
              <select
                v-model="statusForm.status"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white"
                required
              >
                <option value="pending">Pending Review</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Notes (Optional)</label>
              <textarea
                v-model="statusForm.notes"
                rows="4"
                placeholder="Add any notes about this status change..."
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"
                maxlength="500"
              ></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeStatusModal"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-200"
              >
                Cancel
              </button>
              <button
                type="button"
                @click="submitStatusUpdate"
                :disabled="updatingStatus"
                class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ updatingStatus ? 'Updating...' : 'Update Status' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import {
  CloudArrowUpIcon,
  DocumentTextIcon,
  XMarkIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'
import PdfViewer from '@/Components/PdfViewer.vue'


const props = defineProps({
  clientId: {
    type: Number,
    required: true
  },
  documentTypes: {
    type: Object,
    default: () => ({
      'w2': 'W-2 Forms',
      '1099': '1099 Forms', 
      'receipts': 'Receipts',
      'bank_statements': 'Bank Statements',
      'tax_returns': 'Tax Returns',
      'id_documents': 'ID Documents',
      'other': 'Other'
    })
  },
  taxYears: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update'])

// Reactive state
const documents = ref([])
const selectedFiles = ref([])
const uploading = ref(false)
const showDocumentModal = ref(false)
const showStatusModal = ref(false)
const showPdfViewer = ref(false)
const selectedDocument = ref(null)
const selectedDocumentForView = ref(null)
const updatingStatus = ref(false)

const uploadForm = ref({
  document_type: '',
  tax_year: '',
  notes: ''
})

const statusForm = ref({
  status: '',
  notes: ''
})

// File handling
const handleFileSelect = (event) => {
  const files = Array.from(event.target.files)
  addFiles(files)
}

const handleFileDrop = (event) => {
  const files = Array.from(event.dataTransfer.files)
  addFiles(files)
}

const addFiles = (files) => {
  const validFiles = files.filter(file => {
    const maxSize = 10 * 1024 * 1024 // 10MB
    const allowedTypes = [
      'application/pdf',
      'application/msword',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'image/jpeg',
      'image/jpg',
      'image/png',
      'image/gif',
      'text/plain',
      'application/vnd.ms-excel',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ]
    
    return file.size <= maxSize && (allowedTypes.includes(file.type) || /\.(pdf|doc|docx|jpg|jpeg|png|gif|txt|xls|xlsx)$/i.test(file.name))
  })
  
  selectedFiles.value = [...selectedFiles.value, ...validFiles]
}

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1)
}

const formatFileSize = (bytes) => {
  if (!bytes) return 'Unknown'
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Document operations
const uploadDocument = async () => {
  if (selectedFiles.value.length === 0) return
  
  uploading.value = true
  
  try {
    const formData = new FormData()
    
    selectedFiles.value.forEach((file, index) => {
      formData.append(`files[${index}]`, file)
    })
    
    formData.append('client_id', props.clientId)
    formData.append('document_type', uploadForm.value.document_type)
    formData.append('tax_year', uploadForm.value.tax_year || '')
    formData.append('notes', uploadForm.value.notes || '')
    
    console.log('Uploading with client_id:', props.clientId)
    
    const response = await axios.post('/admin/documents/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    console.log('Upload successful:', response.data)
    
    // Reset form
    selectedFiles.value = []
    uploadForm.value = {
      document_type: '',
      tax_year: '',
      notes: ''
    }
    
    // Refresh documents
    await loadDocuments()
    
    emit('update')
    
    // Show success message
    alert('Documents uploaded successfully!')
    
  } catch (error) {
    console.error('Upload failed:', error)
    
    let errorMessage = 'Upload failed. Please try again.'
    
    if (error.response) {
      console.error('Error response:', error.response.data)
      
      if (error.response.status === 422) {
        // Validation errors
        const errors = error.response.data.errors || {}
        const errorMessages = Object.values(errors).flat()
        errorMessage = errorMessages.length > 0 ? errorMessages.join(', ') : 'Validation failed'
      } else if (error.response.data.message) {
        errorMessage = error.response.data.message
      }
    }
    
    alert(errorMessage)
  } finally {
    uploading.value = false
  }
}

const loadDocuments = async () => {
  try {
    // First try to get documents by client ID
    const response = await axios.get(`/admin/clients/${props.clientId}/documents`)
    console.log('Documents loaded from API:', response.data.documents)
    documents.value = response.data.documents || []
  } catch (error) {
    console.error('Failed to load documents by client:', error)
    // Fallback: try to load from documents endpoint with user_id filter
    try {
      const fallbackResponse = await axios.get(`/admin/documents?user_id=${props.clientId}`)
      console.log('Documents loaded from fallback:', fallbackResponse.data.documents)
      documents.value = fallbackResponse.data.documents || []
    } catch (fallbackError) {
      console.error('Fallback load also failed:', fallbackError)
      documents.value = []
    }
  }
}

const refreshDocuments = () => {
  loadDocuments()
}

const viewDocument = (document) => {
  // Open document in PDF viewer modal
  if (!document || !document.id) {
    console.error('Invalid document:', document)
    alert('Unable to view document. Invalid document data.')
    return
  }
  
  console.log('Viewing document:', document.id, document.file_path)
  selectedDocumentForView.value = document
  showPdfViewer.value = true
}

const closePdfViewer = () => {
  showPdfViewer.value = false
  selectedDocumentForView.value = null
}

const closeDocumentModal = () => {
  showDocumentModal.value = false
  selectedDocument.value = null
}

const updateDocumentStatus = (document) => {
  console.log('Selected document for status update:', document)
  selectedDocument.value = document
  statusForm.value = {
    status: document.status,
    notes: document.notes || ''
  }
  showStatusModal.value = true
}

const closeStatusModal = () => {
  showStatusModal.value = false
  selectedDocument.value = null
  statusForm.value = { status: '', notes: '' }
}

const submitStatusUpdate = async () => {
  updatingStatus.value = true
  
  try {
    const documentId = selectedDocument.value.id
    const url = `/admin/documents/${documentId}/status`
    
    console.log('Updating document status:', { documentId, url, data: statusForm.value })
    
    const response = await fetch(url, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify(statusForm.value)
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
    console.log('Status update response:', data)
    
    // Update the document in the local list
    const docIndex = documents.value.findIndex(d => d.id === documentId)
    if (docIndex !== -1) {
      documents.value[docIndex].status = statusForm.value.status
      documents.value[docIndex].notes = statusForm.value.notes
    }
    
    closeStatusModal()
    emit('update')
  } catch (error) {
    console.error('Status update failed:', error)
  } finally {
    updatingStatus.value = false
  }
}

const deleteDocument = async (document) => {
  if (confirm(`Are you sure you want to delete "${document.name}"? This action cannot be undone.`)) {
    try {
      console.log('Deleting document:', document.id)
      
      const response = await axios.delete(`/admin/documents/${document.id}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      
      console.log('Delete successful:', response.data)
      
      // Refresh documents list
      await loadDocuments()
      emit('update')
      
      alert('Document deleted successfully!')
      
    } catch (error) {
      console.error('Delete failed:', error)
      
      let errorMessage = 'Delete failed. Please try again.'
      
      if (error.response) {
        console.error('Error response:', error.response.data)
        
        if (error.response.data.message) {
          errorMessage = error.response.data.message
        } else if (error.response.status === 404) {
          errorMessage = 'Document not found.'
        } else if (error.response.status === 403) {
          errorMessage = 'You do not have permission to delete this document.'
        }
      }
      
      alert(errorMessage)
    }
  }
}

// Load documents on mount
onMounted(() => {
  loadDocuments()
})
</script>