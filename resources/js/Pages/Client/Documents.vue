<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-green-50 to-emerald-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-green-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-emerald-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
            <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2 pr-2 pl-2">
          <div class="flex items-center space-x-4">
            <!-- Document Management Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-green-900 to-emerald-900 bg-clip-text text-transparent">
                My Documents
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Upload and manage your tax documents securely</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-green-700">Secure Upload</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-emerald-700">Encrypted Storage</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <button
              @click="showUploadModal = true"
              class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
              </svg>
              <span class="font-semibold">Upload Document</span>
            </button>
            
            <Link
              href="/client/dashboard"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
              </svg>
              <span class="font-semibold">Back to Dashboard</span>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Filters -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
            <select
              v-model="filters.type"
              @change="applyFilters"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">All Types</option>
              <option v-for="(label, value) in documentTypes" :key="value" :value="value">
                {{ label }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tax Year</label>
            <select
              v-model="filters.year"
              @change="applyFilters"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">All Years</option>
              <option v-for="year in taxYears" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select
              v-model="filters.status"
              @change="applyFilters"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Documents List -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">
            Documents ({{ documents.total }})
          </h2>
        </div>
        
        <div v-if="documents.data.length === 0" class="p-6 text-center">
          <DocumentTextIcon class="mx-auto w-12 h-12 text-gray-400" />
          <p class="mt-2 text-gray-600">No documents found.</p>
          <p class="text-sm text-gray-500">Upload your first document to get started.</p>
        </div>

        <div v-else class="divide-y divide-gray-200">
          <div
            v-for="document in documents.data"
            :key="document.id"
            class="p-6 hover:bg-gray-50"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <DocumentTextIcon class="w-6 h-6 text-blue-600" />
                  </div>
                </div>
                <div>
                  <h3 class="text-sm font-medium text-gray-900">{{ document.name }}</h3>
                  <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                    <span>{{ documentTypes[document.document_type] }}</span>
                    <span v-if="document.tax_year">{{ document.tax_year }}</span>
                    <span>{{ document.formatted_file_size }}</span>
                    <span>{{ formatDate(document.created_at) }}</span>
                  </div>
                  <p v-if="document.notes" class="mt-1 text-sm text-gray-600">{{ document.notes }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-3">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800': document.status === 'pending',
                    'bg-green-100 text-green-800': document.status === 'approved',
                    'bg-red-100 text-red-800': document.status === 'rejected'
                  }"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                >
                  {{ document.status.charAt(0).toUpperCase() + document.status.slice(1) }}
                </span>
                <button
                  @click="downloadDocument(document)"
                  class="text-indigo-600 hover:text-indigo-500 font-medium text-sm"
                >
                  Download
                </button>
                <button
                  v-if="document.status === 'pending'"
                  @click="deleteDocument(document)"
                  class="text-red-600 hover:text-red-500 font-medium text-sm"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="documents.last_page > 1" class="px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing {{ documents.from }} to {{ documents.to }} of {{ documents.total }} results
            </div>
            <div class="flex space-x-2">
              <button
                v-for="page in paginationPages"
                :key="page"
                @click="goToPage(page)"
                :class="{
                  'bg-indigo-600 text-white': page === documents.current_page,
                  'bg-white text-gray-700 hover:bg-gray-50': page !== documents.current_page
                }"
                class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium"
              >
                {{ page }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Upload Modal -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-start justify-center p-4 pt-8 overflow-y-auto">
      <div class="relative w-full max-w-2xl bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all my-8">
        <!-- Enhanced Header -->
        <div class="bg-gradient-to-r from-slate-50 via-green-50 to-emerald-50 px-8 py-6 border-b border-gray-200 relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-16 bg-gradient-to-bl from-green-100/40 to-transparent rounded-bl-full"></div>
          <div class="relative flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg ring-4 ring-green-100">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-green-900 to-emerald-900 bg-clip-text text-transparent">
                  Upload Document
                </h3>
                <p class="text-sm text-gray-600 font-medium">Securely upload your tax documents for processing</p>
              </div>
            </div>
            <button @click="closeUploadModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Enhanced Form -->
        <div class="p-8">
          <form @submit.prevent="uploadDocument" class="space-y-8">
            <!-- File Upload Section -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200">
              <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <h4 class="text-lg font-bold text-blue-900">File Selection</h4>
              </div>
              
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-blue-700">Choose File <span class="text-red-500">*</span></label>
                <div class="relative">
                  <input
                    ref="fileInput"
                    type="file"
                    @change="handleFileSelect"
                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200"
                    required
                  />
                </div>
                <div class="flex items-center space-x-2 text-xs text-blue-600">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="font-medium">Max 10MB. All file formats supported (PDF, images, Word, Excel, etc.)</span>
                </div>
              </div>
            </div>

            <!-- Document Details Section -->
            <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-6 border border-purple-200">
              <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a4 4 0 01-4-4V7a4 4 0 014-4z"></path>
                  </svg>
                </div>
                <h4 class="text-lg font-bold text-purple-900">Document Details</h4>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-purple-700">Document Type <span class="text-red-500">*</span></label>
                  <select
                    v-model="uploadForm.document_type"
                    class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900"
                    required
                  >
                    <option value="">Select Type</option>
                    <option v-for="(label, value) in documentTypes" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-purple-700">Tax Year (Optional)</label>
                  <input
                    v-model="uploadForm.tax_year"
                    type="number"
                    :min="2000"
                    :max="new Date().getFullYear() + 1"
                    class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900 placeholder-gray-500"
                    placeholder="e.g., 2024"
                  />
                </div>
              </div>
            </div>

            <!-- Notes Section -->
            <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200">
              <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                  </svg>
                </div>
                <h4 class="text-lg font-bold text-gray-900">Additional Notes</h4>
              </div>
              
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Notes (Optional)</label>
                <textarea
                  v-model="uploadForm.notes"
                  rows="4"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200 text-gray-900 placeholder-gray-500 resize-none"
                  maxlength="500"
                  placeholder="Add any additional context or notes about this document..."
                ></textarea>
                <div class="text-xs text-gray-500 text-right">{{ uploadForm.notes?.length || 0 }}/500 characters</div>
              </div>
            </div>
          </form>
        </div>

        <!-- Enhanced Footer -->
        <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-8 py-6 border-t border-gray-200 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="closeUploadModal"
            class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md"
          >
            Cancel
          </button>
          <button
            type="submit"
            @click="uploadDocument"
            :disabled="uploading"
            class="w-full sm:w-auto bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
          >
            <div class="flex items-center justify-center">
              <svg v-if="uploading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
              </svg>
              {{ uploading ? 'Uploading...' : 'Upload Document' }}
            </div>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { DocumentTextIcon } from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  documents: Object,
  filters: Object,
  taxYears: Array,
  documentTypes: Object
})

const showUploadModal = ref(false)
const uploading = ref(false)
const filters = ref({ type: '', year: '', status: '', ...props.filters })

const uploadForm = ref({
  file: null,
  document_type: '',
  tax_year: '',
  notes: ''
})

const paginationPages = computed(() => {
  const pages = []
  const current = props.documents.current_page
  const last = props.documents.last_page
  
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  return pages
})

const handleFileSelect = (event) => {
  uploadForm.value.file = event.target.files[0]
}

const uploadDocument = async () => {
  if (!uploadForm.value.file) return
  
  uploading.value = true
  
  const formData = new FormData()
  formData.append('file', uploadForm.value.file)
  formData.append('document_type', uploadForm.value.document_type)
  if (uploadForm.value.tax_year) {
    formData.append('tax_year', uploadForm.value.tax_year)
  }
  if (uploadForm.value.notes) {
    formData.append('notes', uploadForm.value.notes)
  }
  
  router.post('/client/documents', formData, {
    onSuccess: () => {
      closeUploadModal()
    },
    onFinish: () => {
      uploading.value = false
    }
  })
}

const closeUploadModal = () => {
  showUploadModal.value = false
  uploadForm.value = {
    file: null,
    document_type: '',
    tax_year: '',
    notes: ''
  }
}

const downloadDocument = (document) => {
  window.location.href = `/client/documents/${document.id}/download`
}

const deleteDocument = (document) => {
  if (confirm('Are you sure you want to delete this document?')) {
    router.delete(`/client/documents/${document.id}`)
  }
}

const applyFilters = () => {
  router.get('/client/documents', filters.value, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  filters.value = { type: '', year: '', status: '' }
  applyFilters()
}

const goToPage = (page) => {
  router.get('/client/documents', { ...filters.value, page }, {
    preserveState: true,
    replace: true
  })
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

onMounted(() => {
  // Load documents on initial page load if no data is present
  if (!props.documents || props.documents.data.length === 0) {
    applyFilters()
  }
})
</script>