<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">My Documents</h1>
          <p class="mt-1 text-sm text-gray-600">Upload and manage your tax documents</p>
        </div>
        <button
          @click="showUploadModal = true"
          class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          Upload Document
        </button>
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

    <!-- Upload Modal -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Document</h3>
          <form @submit.prevent="uploadDocument" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
              <input
                ref="fileInput"
                type="file"
                @change="handleFileSelect"
                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required
              />
              <p class="mt-1 text-xs text-gray-500">Max 10MB. PDF, images, Word, Excel files allowed.</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
              <select
                v-model="uploadForm.document_type"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required
              >
                <option value="">Select Type</option>
                <option v-for="(label, value) in documentTypes" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tax Year (Optional)</label>
              <input
                v-model="uploadForm.tax_year"
                type="number"
                :min="2000"
                :max="new Date().getFullYear() + 1"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
              <textarea
                v-model="uploadForm.notes"
                rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                maxlength="500"
              ></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeUploadModal"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="uploading"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
              >
                {{ uploading ? 'Uploading...' : 'Upload' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
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
const filters = ref({ ...props.filters })

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
</script>