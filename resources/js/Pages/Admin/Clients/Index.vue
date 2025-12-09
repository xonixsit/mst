<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-emerald-50 to-blue-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-emerald-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-blue-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2 pr-2 pl-2">
          <div class="flex items-center space-x-4">
            <!-- Client Management Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-emerald-900 to-blue-900 bg-clip-text text-transparent">
                Client Management
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Comprehensive client information and operations center</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-emerald-700">{{ stats.total_clients }} Total Clients</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-blue-700">{{ stats.active_clients }} Active</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <button
              @click="showCreateModal = true"
              class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              <span class="font-semibold">Add New Client</span>
            </button>
            
            <button
              @click="showExportModal = true"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span class="font-semibold">Export Data</span>
            </button>
          </div>
        </div>
      </div>
    </template>


    <!-- Enhanced Filters and Search -->
    <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden mb-8">
      <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-xl font-bold text-gray-900">Search & Filter Clients</h3>
            <p class="text-sm text-gray-600 mt-1">Find and manage your client database</p>
          </div>
          <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Search -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Search Clients</label>
            <div class="relative">
              <input
                v-model="searchForm.search"
                type="text"
                placeholder="Name, email, phone..."
                class="w-full px-4 py-3 pl-10 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                @input="debouncedSearch"
              />
              <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>

          <!-- Status Filter -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Client Status</label>
            <select
              v-model="searchForm.status"
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white"
              @change="applyFilters"
            >
              <option value="">All Statuses</option>
              <option v-for="(label, value) in statusOptions" :key="value" :value="value">
                {{ label }}
              </option>
            </select>
          </div>

          <!-- Date Range From -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Registration From</label>
            <input
              v-model="searchForm.date_from"
              type="date"
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
              @change="applyFilters"
            />
          </div>
          
          <!-- Date Range To -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">Registration To</label>
            <input
              v-model="searchForm.date_to"
              type="date"
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
              @change="applyFilters"
            />
          </div>
        </div>

        <!-- Action Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mt-8 pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
          <button
            @click="clearFilters"
            class="flex items-center space-x-2 text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Clear All Filters</span>
          </button>
          
          <!-- Bulk Actions -->
          <div v-if="selectedClients.length > 0" class="flex items-center space-x-4">
            <div class="bg-blue-50 px-4 py-2 rounded-lg">
              <span class="text-sm font-semibold text-blue-700">{{ selectedClients.length }} clients selected</span>
            </div>
            <button
              @click="showBulkModal = true"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg"
            >
              Bulk Actions
            </button>
            <button
              @click="clearSelection"
              class="text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200"
            >
              Clear Selection
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Client Table -->
    <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
      <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
          <div>
            <h3 class="text-xl font-bold text-gray-900">Client Directory</h3>
            <p class="text-sm text-gray-600 mt-1">Comprehensive client information and management</p>
          </div>
          
          <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 bg-white px-3 py-2 rounded-lg border border-gray-200 shadow-sm">
              <label class="text-sm font-medium text-gray-600">Show</label>
              <select
                v-model="perPage"
                class="border-0 bg-transparent text-sm font-semibold text-gray-900 focus:ring-0 focus:outline-none"
                @change="changePerPage"
              >
                <option v-for="option in perPageOptions" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>
              <label class="text-sm font-medium text-gray-600">entries</label>
            </div>
            
            <div class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
              <span class="text-sm font-medium text-gray-600">
                Showing <span class="font-bold text-gray-900">{{ clients.from || 0 }}</span> to 
                <span class="font-bold text-gray-900">{{ clients.to || 0 }}</span> of 
                <span class="font-bold text-blue-600">{{ clients.total || 0 }}</span> results
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>  
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-4 text-left">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                  class="w-4 h-4 rounded border-2 border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 transition-all duration-200"
                />
              </th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                <div class="flex items-center space-x-1">
                  <span>ID</span>
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                  </svg>
                </div>
              </th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">First Name</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Last Name</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Phone</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Registration</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr
              v-for="client in clients.data"
              :key="client.id"
              class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200 group"
            >
              <td class="px-6 py-5">
                <input
                  type="checkbox"
                  :value="client.id"
                  v-model="selectedClients"
                  class="w-4 h-4 rounded border-2 border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0 transition-all duration-200"
                />
              </td>
              <td class="px-6 py-5 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-xs font-bold text-blue-700">#{{ client.id }}</span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-5 whitespace-nowrap">
                <div class="text-sm font-semibold text-gray-900">{{ client.user?.first_name || '-' }}</div>
              </td>
              <td class="px-6 py-5 whitespace-nowrap">
                <div class="text-sm font-semibold text-gray-900">{{ client.user?.last_name || '-' }}</div>
              </td>
              <td class="px-6 py-5 whitespace-nowrap">
                <div class="text-sm text-gray-900 font-medium">{{ client.user?.email || '-' }}</div>
              </td>
              <td class="px-6 py-5 whitespace-nowrap">
                <div class="text-sm text-gray-900 font-medium">{{ client.phone || '-' }}</div>
              </td>
              <td class="px-6 py-5 whitespace-nowrap">
                <div class="text-sm text-gray-600 font-medium">{{ formatDate(client.created_at) }}</div>
              </td>
              <td class="px-6 py-5 whitespace-nowrap">
                <span
                  class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full shadow-sm"
                  :class="getStatusClass(client.status)"
                >
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="getStatusDotClass(client.status)"></span>
                  {{ statusOptions[client.status] }}
                </span>
              </td>
              <td class="px-6 py-5 text-center">
                <div class="flex justify-center space-x-2">
                  <button
                    @click="viewClient(client.id)"
                    class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                    title="View Client"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                  <button
                    @click="editClient(client.id)"
                    class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                    title="Edit Client"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button
                    @click="viewTaxDocs(client.user?.id || client.user_id)"
                    class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                    title="View Documents"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                  </button>
                  <button
                    @click="viewInvoices(client.user_id || client.user?.id)"
                    class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                    title="View Invoices"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                  </button>
                  <button
                    @click="deleteClient(client.id)"
                    class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                    title="Delete Client"
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

        <!-- Pagination -->
        <div v-if="clients.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
          <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600 font-medium">
              Page {{ clients.current_page }} of {{ clients.last_page }}
            </div>
            <div class="flex space-x-2"></div>
              <button
                v-for="page in paginationPages"
                :key="page"
                @click="goToPage(page)"
                class="px-4 py-2 text-sm rounded-lg font-semibold transition-all duration-200"
                :class="page === clients.current_page 
                  ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md' 
                  : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 shadow-sm'"
              >
                {{ page }}
              </button>
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

    <!-- Create Client User Modal -->
    <CreateClientUserModal
      :is-open="showCreateModal"
      @close="showCreateModal = false"
      @submit="handleCreateClientSubmit"
    />
  </AppLayout>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import { debounce } from 'lodash'
import BulkOperationsModal from '../../../Components/BulkOperationsModal.vue'
import CreateClientUserModal from '../../../Components/CreateClientUserModal.vue'
import AppLayout from '../../../Layouts/AppLayout.vue'

export default {
  name: 'AdminClientsIndex',
  components: {
    BulkOperationsModal,
    CreateClientUserModal,
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
    const showCreateModal = ref(false)
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
      // Create a form and submit it to maintain authentication
      const form = document.createElement('form')
      form.method = 'GET'
      form.action = route('admin.clients.export')
      form.target = '_blank'
      
      // Add search parameters
      Object.entries(searchForm.value).forEach(([key, value]) => {
        if (value) {
          const input = document.createElement('input')
          input.type = 'hidden'
          input.name = key
          input.value = value
          form.appendChild(input)
        }
      })
      
      // Add format parameter
      const formatInput = document.createElement('input')
      formatInput.type = 'hidden'
      formatInput.name = 'format'
      formatInput.value = exportFormat.value
      form.appendChild(formatInput)
      
      // Submit form
      document.body.appendChild(form)
      form.submit()
      document.body.removeChild(form)
      
      showExportModal.value = false
    }

    const handleCreateClientSubmit = (userData) => {
      // Create user account first, then client
      axios.post('/admin/clients', {
        personal: {
          firstName: userData.first_name,
          middleName: userData.middle_name,
          lastName: userData.last_name,
          email: userData.email
        },
        spouse: {},
        employee: [{}],
        projects: [],
        assets: [],
        expenses: [],
        createAccount: true,
        sendCredentials: true
      }).then(response => {
        showCreateModal.value = false
        // Refresh the clients list
        router.reload()
        alert('Client created successfully! Credentials have been sent to their email.')
      }).catch(error => {
        console.error('Error creating client:', error.response?.data || error)
        alert('Error creating client: ' + (error.response?.data?.message || 'Please try again.'))
      })
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
        active: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
        inactive: 'bg-amber-100 text-amber-800 border border-amber-200',
        archived: 'bg-rose-100 text-rose-800 border border-rose-200'
      }
      return classes[status] || 'bg-gray-100 text-gray-800 border border-gray-200'
    }

    const getStatusDotClass = (status) => {
      const classes = {
        active: 'bg-emerald-400',
        inactive: 'bg-amber-400',
        archived: 'bg-rose-400'
      }
      return classes[status] || 'bg-gray-400'
    }

    const viewClient = (id) => {
      router.visit(`/admin/clients/${id}`)
    }

    const editClient = (id) => {
      router.visit(`/admin/clients/${id}/edit`)
    }

    const viewTaxDocs = (userId) => {
      router.visit(`/admin/documents?user_id=${userId}`)
    }

    const viewInvoices = (userId) => {
      router.visit(`/admin/invoices?user_id=${userId}`)
    }

    const deleteClient = (id) => {
      if (confirm('Are you sure you want to delete this client? This action cannot be undone.')) {
        router.delete(route('admin.clients.destroy', id))
      }
    }

    return {
      router,
      searchForm,
      selectedClients,
      bulkAction,
      currentSort,
      sortDirection,
      perPage,
      showExportModal,
      showBulkModal,
      showCreateModal,
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
      handleCreateClientSubmit,
      formatDate,
      getStatusClass,
      getStatusDotClass,
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