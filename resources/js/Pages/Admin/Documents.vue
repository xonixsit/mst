<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Document Management</h1>
          <p class="mt-1 text-sm text-gray-600">Review and manage client documents</p>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Filters -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
          <div class="md:col-span-2 lg:col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input
              v-model="filters.search"
              @input="applyFilters"
              type="text"
              placeholder="Search documents..."
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
            <select
              v-model="filters.client_id"
              @change="applyFilters"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">All Clients</option>
              <option v-for="client in clients" :key="client.id" :value="client.id">
                {{ client.first_name }} {{ client.last_name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
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
          <div class="flex items-end md:col-span-2 lg:col-span-3 xl:col-span-1">
            <button
              @click="clearFilters"
              class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              Clear
            </button>
          </div>
        </div>
      </div>

      <!-- Documents Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">
            Documents ({{ documents.total }})
          </h2>
        </div>
        
        <div v-if="documents.data.length === 0" class="p-6 text-center">
          <DocumentTextIcon class="mx-auto w-12 h-12 text-gray-400" />
          <p class="mt-2 text-gray-600">No documents found.</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Document
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Client
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Tax Year
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Uploaded
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="document in documents.data" :key="document.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <DocumentTextIcon class="w-5 h-5 text-gray-400 mr-3" />
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ document.name }}</div>
                      <div class="text-sm text-gray-500">{{ document.formatted_file_size }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">
                    {{ document.client?.first_name }} {{ document.client?.last_name }}
                  </div>
                  <div class="text-sm text-gray-500">{{ document.client?.email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm text-gray-900">{{ documentTypes[document.document_type] }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ document.tax_year || '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
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
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(document.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <button
                      @click="viewDocument(document)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      View
                    </button>
                    <button
                      @click="showStatusModal(document)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Update Status
                    </button>
                    <button
                      @click="deleteDocument(document)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
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

    <!-- Status Update Modal -->
    <div v-if="showStatusUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Update Document Status</h3>
          <form @submit.prevent="updateStatus" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="statusForm.status"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required
              >
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
              <textarea
                v-model="statusForm.notes"
                rows="3"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                maxlength="500"
              ></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeStatusModal"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="updating"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
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
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { DocumentTextIcon } from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  documents: Object,
  filters: Object,
  clients: Array,
  taxYears: Array,
  documentTypes: Object
})

const showStatusUpdateModal = ref(false)
const updating = ref(false)
const selectedDocument = ref(null)
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
  router.get(`/admin/documents/${document.id}`)
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
  router.get('/admin/documents', filters.value, {
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
</script>