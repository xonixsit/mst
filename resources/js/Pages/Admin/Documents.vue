<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-purple-50 to-indigo-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-purple-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-indigo-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Document Management Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 via-purple-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-purple-100">
              <DocumentTextIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-purple-900 to-indigo-900 bg-clip-text text-transparent">
                Document Management
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Review and manage client documents efficiently</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-purple-700">{{ documents.total }} Total Documents</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-indigo-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-indigo-700">Real-time Processing</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Enhanced Filters -->
      <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xl font-bold text-gray-900">Search & Filter Documents</h3>
              <p class="text-sm text-gray-600 mt-1">Find and manage your document library</p>
            </div>
            <div class="flex items-center space-x-2">
              <DocumentTextIcon class="w-5 h-5 text-gray-400" />
            </div>
          </div>
        </div>
        
        <div class="p-8">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
            <div class="md:col-span-2 lg:col-span-1 space-y-2">
              <label class="block text-sm font-semibold text-gray-700">Search Documents</label>
              <div class="relative">
                <input
                  v-model="filters.search"
                  @input="applyFilters"
                  type="text"
                  placeholder="Document name, client..."
                  class="w-full px-4 py-3 pl-10 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900"
                />
                <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-gray-700">Client</label>
              <select
                v-model="filters.client_id"
                @change="applyFilters"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900"
              >
                <option value="">All Clients</option>
                <option v-for="client in clients" :key="client.id" :value="client.id">
                  {{ client.first_name }} {{ client.last_name }}
                </option>
              </select>
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-gray-700">Document Type</label>
              <select
                v-model="filters.type"
                @change="applyFilters"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900"
              >
                <option value="">All Types</option>
                <option v-for="(label, value) in documentTypes" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-gray-700">Tax Year</label>
              <select
                v-model="filters.year"
                @change="applyFilters"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900"
              >
                <option value="">All Years</option>
                <option v-for="year in taxYears" :key="year" :value="year">
                  {{ year }}
                </option>
              </select>
            </div>
            
            <div class="space-y-2">
              <label class="block text-sm font-semibold text-gray-700">Status</label>
              <select
                v-model="filters.status"
                @change="applyFilters"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900"
              >
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
            
            <div class="flex items-end md:col-span-2 lg:col-span-3 xl:col-span-1">
              <button
                @click="clearFilters"
                class="w-full bg-gradient-to-r from-gray-100 to-slate-100 hover:from-gray-200 hover:to-slate-200 text-gray-700 px-4 py-3 rounded-xl font-semibold transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center space-x-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Clear Filters</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Documents Table -->
      <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xl font-bold text-gray-900">Document Library</h3>
              <p class="text-sm text-gray-600 mt-1">{{ documents.total }} documents in your system</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
              <span class="text-sm font-semibold text-purple-600">{{ documents.total }} Total</span>
            </div>
          </div>
        </div>
        
        <div v-if="documents.data.length === 0" class="p-12 text-center">
          <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-indigo-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
            <DocumentTextIcon class="w-8 h-8 text-purple-600" />
          </div>
          <p class="text-gray-500 font-medium">No documents found</p>
          <p class="text-gray-400 text-sm mt-1">Documents will appear here as they are uploaded</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  Document
                </th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                  Client
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
              <tr v-for="document in documents.data" :key="document.id" class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-50 transition-all duration-200 group">
                <td class="px-6 py-5 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-indigo-200 rounded-xl flex items-center justify-center mr-4 shadow-sm">
                      <DocumentTextIcon class="w-5 h-5 text-purple-600" />
                    </div>
                    <div>
                      <div class="text-sm font-semibold text-gray-900">{{ document.name }}</div>
                      <div class="text-xs text-gray-500 font-medium">{{ document.formatted_file_size }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-5 whitespace-nowrap">
                  <div class="text-sm font-semibold text-gray-900">
                    {{ document.client?.user?.first_name }} {{ document.client?.user?.last_name }}
                  </div>
                  <div class="text-xs text-gray-500 font-medium">{{ document.client?.user?.email }}</div>
                </td>
                <td class="px-6 py-5 whitespace-nowrap">
                  <span class="text-sm font-medium text-gray-900">{{ documentTypes[document.document_type] }}</span>
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
                      title="View Details"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    <button
                      @click="showStatusModal(document)"
                      class="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                      title="Update Status"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click="deleteDocument(document)"
                      class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                      title="Delete Document"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Enhanced Pagination -->
        <div v-if="documents.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
          <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600 font-medium">
              Showing <span class="font-bold text-gray-900">{{ documents.from }}</span> to 
              <span class="font-bold text-gray-900">{{ documents.to }}</span> of 
              <span class="font-bold text-purple-600">{{ documents.total }}</span> results
            </div>
            <div class="flex space-x-2">
              <button
                v-for="page in paginationPages"
                :key="page"
                @click="goToPage(page)"
                class="px-4 py-2 text-sm rounded-lg font-semibold transition-all duration-200"
                :class="page === documents.current_page 
                  ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md' 
                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 shadow-sm'"
              >
                {{ page }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Document View Modal -->
    <DocumentViewModal
      :show="showDocumentModal"
      :document="selectedDocumentForView"
      @close="closeDocumentModal"
    />

    <!-- Enhanced Status Update Modal -->
    <div v-if="showStatusUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-6 border w-96 shadow-2xl rounded-2xl bg-white">
        <div class="mt-3">
          <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-indigo-200 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Update Document Status</h3>
          </div>
          
          <form @submit.prevent="updateStatus" class="space-y-6">
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
                type="submit"
                :disabled="updating"
                class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ updating ? 'Updating...' : 'Update Status' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { DocumentTextIcon } from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'
import DocumentViewModal from '@/Components/DocumentViewModal.vue'

const props = defineProps({
  documents: Object,
  filters: Object,
  clients: Array,
  taxYears: Array,
  documentTypes: Object
})

const showStatusUpdateModal = ref(false)
const showDocumentModal = ref(false)
const updating = ref(false)
const selectedDocument = ref(null)
const selectedDocumentForView = ref(null)
const filters = ref({ ...props.filters })

const statusForm = ref({
  status: '',
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

const viewDocument = (document) => {
  console.log('Viewing document:', document)
  if (document && document.id) {
    router.get(`/admin/documents/${document.id}`)
  } else {
    console.error('Document or document ID is missing:', document)
  }
}

const viewDocumentModal = (document) => {
  selectedDocumentForView.value = document
  showDocumentModal.value = true
}

const closeDocumentModal = () => {
  showDocumentModal.value = false
  selectedDocumentForView.value = null
}

const showStatusModal = (document) => {
  selectedDocument.value = document
  statusForm.value = {
    status: document.status,
    notes: document.notes || ''
  }
  showStatusUpdateModal.value = true
}

const closeStatusModal = () => {
  showStatusUpdateModal.value = false
  selectedDocument.value = null
  statusForm.value = { status: '', notes: '' }
}

const updateStatus = () => {
  updating.value = true
  
  router.patch(`/admin/documents/${selectedDocument.value.id}/status`, statusForm.value, {
    onSuccess: () => {
      closeStatusModal()
    },
    onFinish: () => {
      updating.value = false
    }
  })
}

const deleteDocument = (document) => {
  if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
    router.delete(`/admin/documents/${document.id}`)
  }
}

const applyFilters = () => {
  // Clean up empty filters
  const cleanFilters = Object.fromEntries(
    Object.entries(filters.value).filter(([key, value]) => value !== '' && value !== null)
  )
  
  router.get('/admin/documents', cleanFilters, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  filters.value = { search: '', client_id: '', type: '', year: '', status: '' }
  applyFilters()
}

const goToPage = (page) => {
  router.get('/admin/documents', { ...filters.value, page }, {
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

// Handle initial URL parameters
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const clientId = urlParams.get('client_id')
  
  if (clientId && !filters.value.client_id) {
    filters.value.client_id = clientId
    applyFilters()
  }
})
</script>