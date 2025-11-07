<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Client Management</h1>
          <p class="mt-1 text-sm text-gray-600">Manage all client information and operations</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="showExportModal = true"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export
          </button>
          <button
            @click="router.visit('/admin/clients/create')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Client
          </button>
        </div>
      </div>
    </template>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Clients</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total_clients }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Active Clients</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.active_clients }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-yellow-100 rounded-lg">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">New This Month</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.new_this_month }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-red-100 rounded-lg">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Archived</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.archived_clients }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow mb-6">
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchForm.search"
              type="text"
              placeholder="Search by name, email, phone..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @input="debouncedSearch"
            />
          </div>

          <!-- Status Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="searchForm.status"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="applyFilters"
            >
              <option value="">All Statuses</option>
              <option v-for="(label, value) in statusOptions" :key="value" :value="value">
                {{ label }}
              </option>
            </select>
          </div>

          <!-- Date Range -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Registration Date From</label>
            <input
              v-model="searchForm.date_from"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="applyFilters"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">To</label>
            <input
              v-model="searchForm.date_to"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="applyFilters"
            />
          </div>
        </div>

        <div class="flex justify-between items-center mt-4">
          <button
            @click="clearFilters"
            class="text-gray-600 hover:text-gray-800 text-sm"
          >
            Clear Filters
          </button>
          
          <!-- Bulk Actions -->
          <div v-if="selectedClients.length > 0" class="flex items-center space-x-3">
            <span class="text-sm text-gray-600">{{ selectedClients.length }} selected</span>
            <button
              @click="showBulkModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"
            >
              Bulk Actions
            </button>
            <button
              @click="clearSelection"
              class="text-gray-600 hover:text-gray-800 text-sm"
            >
              Clear Selection
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Client Table -->
    <div class="bg-white rounded-lg shadow">
      <div class="p-6">
        <div class="flex justify-between items-center mb-4">
          <div class="flex items-center space-x-4">
            <label class="text-sm text-gray-600">Show</label>
            <select
              v-model="perPage"
              class="px-3 py-1 border border-gray-300 rounded text-sm"
              @change="changePerPage"
            >
              <option v-for="option in perPageOptions" :key="option" :value="option">
                {{ option }}
              </option>
            </select>
            <label class="text-sm text-gray-600">entries</label>
          </div>
          
          <div class="text-sm text-gray-600">
            Showing {{ clients.from || 0 }} to {{ clients.to || 0 }} of {{ clients.total || 0 }} results
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left">
                  <input
                    type="checkbox"
                    :checked="allSelected"
                    @change="toggleSelectAll"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                </th>
                <th
                  v-for="(label, key) in sortOptions"
                  :key="key"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                  @click="sortBy(key)"
                >
                  <div class="flex items-center">
                    {{ label }}
                    <svg
                      v-if="currentSort === key"
                      class="w-4 h-4 ml-1"
                      :class="sortDirection === 'asc' ? 'transform rotate-180' : ''"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="client in clients.data"
                :key="client.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4">
                  <input
                    type="checkbox"
                    :value="client.id"
                    v-model="selectedClients"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ client.id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ client.first_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ client.last_name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ client.email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ client.phone }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(client.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusClass(client.status)"
                  >
                    {{ statusOptions[client.status] }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button
                      @click="viewClient(client.id)"
                      class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-xs"
                      title="View"
                    >
                      View
                    </button>
                    <button
                      @click="editClient(client.id)"
                      class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs"
                      title="Edit"
                    >
                      Edit
                    </button>
                    <button
                      @click="viewTaxDocs(client.id)"
                      class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs"
                      title="Tax Docs"
                    >
                      Docs
                    </button>
                    <button
                      @click="viewInvoices(client.id)"
                      class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded text-xs"
                      title="Invoices"
                    >
                      Invoice
                    </button>
                    <button
                      @click="deleteClient(client.id)"
                      class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs"
                      title="Delete"
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
        <div v-if="clients.last_page > 1" class="mt-6 flex justify-between items-center">
          <div class="text-sm text-gray-600">
            Page {{ clients.current_page }} of {{ clients.last_page }}
          </div>
          <div class="flex space-x-2">
            <button
              v-for="page in paginationPages"
              :key="page"
              @click="goToPage(page)"
              class="px-3 py-2 text-sm rounded"
              :class="page === clients.current_page 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'"
            >
              {{ page }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Export Modal -->
    <div v-if="showExportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-lg font-medium mb-4">Export Clients</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
            <select v-model="exportFormat" class="w-full px-3 py-2 border border-gray-300 rounded-md">
              <option value="excel">Excel (.xlsx)</option>
              <option value="csv">CSV (.csv)</option>
              <option value="pdf">PDF (.pdf)</option>
            </select>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              @click="showExportModal = false"
              class="px-4 py-2 text-gray-600 hover:text-gray-800"
            >
              Cancel
            </button>
            <button
              @click="exportClients"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
            >
              Export
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Operations Modal -->
    <BulkOperationsModal
      :show="showBulkModal"
      :selected-clients="selectedClients"
      :users="availableUsers"
      @close="showBulkModal = false"
      @execute="handleBulkOperation"
    />
  </AppLayout>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import BulkOperationsModal from '../../../Components/BulkOperationsModal.vue'
import AppLayout from '../../../Layouts/AppLayout.vue'

export default {
  name: 'AdminClientsIndex',
  components: {
    BulkOperationsModal,
    AppLayout
  },
  props: {
    clients: Object,
    filters: Object,
    stats: Object,
    sortOptions: Object,
    statusOptions: Object,
    perPageOptions: Array,
    availableUsers: {
      type: Array,
      default: () => []
    }
  },
  setup(props) {
    const searchForm = ref({
      search: props.filters.search || '',
      status: props.filters.status || '',
      date_from: props.filters.date_from || '',
      date_to: props.filters.date_to || ''
    })

    const selectedClients = ref([])
    const bulkAction = ref('')
    const currentSort = ref(props.filters.sort_by || 'created_at')
    const sortDirection = ref(props.filters.sort_direction || 'desc')
    const perPage = ref(props.filters.per_page || 25)
    const showExportModal = ref(false)
    const showBulkModal = ref(false)
    const exportFormat = ref('excel')

    const allSelected = computed(() => {
      return props.clients.data.length > 0 && 
             selectedClients.value.length === props.clients.data.length
    })

    const paginationPages = computed(() => {
      const pages = []
      const current = props.clients.current_page
      const last = props.clients.last_page
      
      for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
        pages.push(i)
      }
      
      return pages
    })

    const debouncedSearch = debounce(() => {
      applyFilters()
    }, 300)

    const applyFilters = () => {
      router.get(route('admin.clients.index'), {
        ...searchForm.value,
        sort_by: currentSort.value,
        sort_direction: sortDirection.value,
        per_page: perPage.value
      }, {
        preserveState: true,
        preserveScroll: true
      })
    }

    const clearFilters = () => {
      searchForm.value = {
        search: '',
        status: '',
        date_from: '',
        date_to: ''
      }
      applyFilters()
    }

    const sortBy = (column) => {
      if (currentSort.value === column) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
      } else {
        currentSort.value = column
        sortDirection.value = 'asc'
      }
      applyFilters()
    }

    const changePerPage = () => {
      applyFilters()
    }

    const goToPage = (page) => {
      router.get(route('admin.clients.index'), {
        ...searchForm.value,
        sort_by: currentSort.value,
        sort_direction: sortDirection.value,
        per_page: perPage.value,
        page: page
      }, {
        preserveState: true,
        preserveScroll: true
      })
    }

    const toggleSelectAll = () => {
      if (allSelected.value) {
        selectedClients.value = []
      } else {
        selectedClients.value = props.clients.data.map(client => client.id)
      }
    }

    const handleBulkOperation = (operationData) => {
      if (!operationData.operation || selectedClients.value.length === 0) return

      const confirmMessage = `Are you sure you want to ${operationData.operation.replace('_', ' ')} ${selectedClients.value.length} clients?`
      
      if (operationData.operation === 'delete' || confirm(confirmMessage)) {
        // Handle export operations differently
        if (operationData.operation === 'export_selected') {
          const params = new URLSearchParams({
            client_ids: operationData.client_ids,
            operation: operationData.operation,
            'data[format]': operationData.data.format || 'excel'
          })
          
          window.open(`${route('admin.clients.bulk-operation')}?${params}`, '_blank')
          showBulkModal.value = false
          selectedClients.value = []
          return
        }

        router.post(route('admin.clients.bulk-operation'), operationData, {
          onSuccess: (response) => {
            selectedClients.value = []
            showBulkModal.value = false
            
            // Show success message
            if (response.props?.flash?.success || response.data?.message) {
              alert(response.props?.flash?.success || response.data?.message || 'Operation completed successfully')
            }
          },
          onError: (errors) => {
            console.error('Bulk operation failed:', errors)
            alert('Operation failed. Please try again.')
          }
        })
      }
    }

    const clearSelection = () => {
      selectedClients.value = []
    }

    const exportClients = () => {
      const params = new URLSearchParams({
        ...searchForm.value,
        format: exportFormat.value
      })
      
      window.open(`${route('admin.clients.export')}?${params}`, '_blank')
      showExportModal.value = false
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      })
    }

    const getStatusClass = (status) => {
      const classes = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-yellow-100 text-yellow-800',
        archived: 'bg-red-100 text-red-800'
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    }

    const viewClient = (id) => {
      router.visit(`/admin/clients/${id}`)
    }

    const editClient = (id) => {
      router.visit(`/admin/clients/${id}/edit`)
    }

    const viewTaxDocs = (id) => {
      router.visit(`/admin/clients/${id}/tax-docs`)
    }

    const viewInvoices = (id) => {
      router.visit(`/admin/clients/${id}/invoices`)
    }

    const deleteClient = (id) => {
      if (confirm('Are you sure you want to delete this client? This action cannot be undone.')) {
        router.delete(route('admin.clients.destroy', id))
      }
    }

    return {
      searchForm,
      selectedClients,
      bulkAction,
      currentSort,
      sortDirection,
      perPage,
      showExportModal,
      showBulkModal,
      exportFormat,
      allSelected,
      paginationPages,
      debouncedSearch,
      applyFilters,
      clearFilters,
      sortBy,
      changePerPage,
      goToPage,
      toggleSelectAll,
      handleBulkOperation,
      clearSelection,
      exportClients,
      formatDate,
      getStatusClass,
      viewClient,
      editClient,
      viewTaxDocs,
      viewInvoices,
      deleteClient
    }
  }
}
</script>

<style scoped>
.admin-clients-index {
  @apply p-6 bg-gray-50 min-h-screen;
}
</style>