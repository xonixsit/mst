<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-blue-50 to-cyan-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-blue-100/40 to-transparent rounded-bl-full"></div>
        
        <div class="px-10 relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-blue-600 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
            </div>
            
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-cyan-900 bg-clip-text text-transparent">
                Contact Queries
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Manage incoming contact form submissions</p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm font-medium">Total Queries</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ queries.length }}</p>
        </div>
        <!-- <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm font-medium">Unread</p>
          <p class="text-3xl font-bold text-red-600 mt-2">{{ queries.filter(q => !q.read).length }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm font-medium">Replied</p>
          <p class="text-3xl font-bold text-green-600 mt-2">{{ queries.filter(q => q.replied).length }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm font-medium">Pending</p>
          <p class="text-3xl font-bold text-yellow-600 mt-2">{{ queries.filter(q => !q.replied).length }}</p>
        </div> -->
      </div>

      <!-- Queries Table -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">All Queries</h2>
            <div class="flex gap-2">
              <!-- <select v-model="filterStatus" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All Status</option>
                <option value="unread">Unread</option>
                <option value="replied">Replied</option>
                <option value="pending">Pending</option>
              </select> -->
              <input 
                v-model="searchQuery" 
                type="text" 
                placeholder="Search queries..." 
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm"
              />
            </div>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Subject</th>
                <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th> -->
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="query in paginatedQueries" :key="query.id" :class="['hover:bg-gray-50', !query.read ? 'bg-blue-50' : '']">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ query.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ query.email }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ query.subject }}</td>
                <!-- <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'px-3 py-1 rounded-full text-xs font-medium',
                    !query.read ? 'bg-red-100 text-red-800' :
                    query.replied ? 'bg-green-100 text-green-800' :
                    'bg-yellow-100 text-yellow-800'
                  ]">
                    {{ !query.read ? 'Unread' : query.replied ? 'Replied' : 'Pending' }}
                  </span>
                </td> -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatDate(query.created_at) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <button @click="viewQuery(query)" class="text-blue-600 hover:text-blue-800 font-medium">View</button>
                  <button class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
          <div class="text-sm text-gray-600">
            Showing <span class="font-medium">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> to <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filteredQueries.length) }}</span> of <span class="font-medium">{{ filteredQueries.length }}</span> queries
          </div>
          <div class="flex gap-2">
            <button 
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3 py-2 border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              Previous
            </button>
            <div class="flex gap-1">
              <button 
                v-for="page in totalPages" 
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'px-3 py-2 rounded text-sm font-medium transition-colors',
                  currentPage === page 
                    ? 'bg-blue-600 text-white' 
                    : 'border border-gray-300 text-gray-700 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
            </div>
            <button 
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-3 py-2 border border-gray-300 rounded text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Query Detail Modal - HFI Compliant -->
      <div v-if="selectedQuery" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="query-modal-title">
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-[90vh] flex flex-col">
          <!-- Header with Clear Title and Close Button -->
          <div class="inset-0 bg-gradient-to-r from-blue-500 via-blue-500 to-cyan-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center flex-shrink-0">
        <div class="">
              <h2 id="query-modal-title" class="text-lg font-semibold text-white-900">Contact Query</h2>
              <p class="text-sm text-gray-500 mt-1">Review and respond to customer inquiry</p>
            </div>
            <button 
              @click="selectedQuery = null" 
              class="text-gray-400 hover:text-gray-600 transition-colors p-1"
              aria-label="Close modal"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <!-- Scrollable Content Area -->
          <div class="overflow-y-auto flex-1 px-6 py-6 space-y-6">
            <!-- Status Alert -->
            <!-- <div :class="[
              'rounded-md p-3 border',
              !selectedQuery.read ? 'bg-red-50 border-red-200' :
              selectedQuery.replied ? 'bg-green-50 border-green-200' :
              'bg-yellow-50 border-yellow-200'
            ]">
              <p class="text-xs font-medium" :class="[
                !selectedQuery.read ? 'text-red-900' :
                selectedQuery.replied ? 'text-green-900' :
                'text-yellow-900'
              ]">
                Status: 
                <span class="font-semibold">
                  {{ !selectedQuery.read ? 'Unread' : selectedQuery.replied ? 'Replied' : 'Pending Response' }}
                </span>
              </p>
            </div> -->

            <!-- Contact Information Section -->
            <section class="space-y-4">
              <h3 class="text-sm font-semibold text-gray-900">From</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">Full Name</label>
                  <p class="text-sm text-gray-900">{{ selectedQuery.name }}</p>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">Email Address</label>
                  <p class="text-sm text-gray-900 break-all">{{ selectedQuery.email }}</p>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">Phone Number</label>
                  <p class="text-sm text-gray-900">{{ selectedQuery.phone || 'Not provided' }}</p>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-600 mb-1">Date Received</label>
                  <p class="text-sm text-gray-900">{{ formatDate(selectedQuery.created_at) }}</p>
                </div>
              </div>
            </section>

            <!-- Message Content Section with Custom Scroll -->
            <section class="border-t border-gray-200 pt-6 space-y-4">
              <h3 class="text-sm font-semibold text-gray-900">Message</h3>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-2">Subject</label>
                <p class="text-sm font-medium text-gray-900">{{ selectedQuery.subject }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 mb-2">Message Content</label>
                <div class="bg-gray-50 rounded-md p-4 border border-gray-200 max-h-48 overflow-y-auto">
                  <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ selectedQuery.message }}</p>
                </div>
              </div>
            </section>
          </div>

          <!-- Fixed Footer with Actions - HFI Compliant -->
          <!-- <div class="border-t border-gray-200 px-6 py-3 flex-shrink-0 bg-white">
            <div class="flex gap-2">
              <button 
                @click="selectedQuery = null"
                class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded text-sm font-medium hover:bg-gray-50 transition-colors"
              >
                Close
              </button>
              <button 
                @click="markAsRead(selectedQuery.id)"
                :class="[
                  'flex-1 px-3 py-2 rounded text-sm font-medium transition-colors',
                  selectedQuery.read 
                    ? 'bg-gray-100 text-gray-600 cursor-default' 
                    : 'bg-blue-600 hover:bg-blue-700 text-white'
                ]"
                :disabled="selectedQuery.read"
              >
                {{ selectedQuery.read ? '✓ Read' : 'Read' }}
              </button>
              <button 
                @click="markAsReplied(selectedQuery.id)"
                :class="[
                  'flex-1 px-3 py-2 rounded text-sm font-medium transition-colors',
                  selectedQuery.replied 
                    ? 'bg-gray-100 text-gray-600 cursor-default' 
                    : 'bg-green-600 hover:bg-green-700 text-white'
                ]"
                :disabled="selectedQuery.replied"
              >
                {{ selectedQuery.replied ? '✓ Replied' : 'Replied' }}
              </button>
            </div>
          </div> -->
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, defineProps } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const filterStatus = ref('')
const searchQuery = ref('')
const selectedQuery = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const props = defineProps({
  queries: {
    type: Array,
    default: () => [],
  },
})

const queries = ref(props.queries)

const filteredQueries = computed(() => {
  return queries.value.filter(query => {
    let matchesStatus = true
    if (filterStatus.value === 'unread') matchesStatus = !query.read
    else if (filterStatus.value === 'replied') matchesStatus = query.replied
    else if (filterStatus.value === 'pending') matchesStatus = !query.replied

    const matchesSearch = !searchQuery.value || 
      query.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      query.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      query.subject.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    return matchesStatus && matchesSearch
  })
})

const paginatedQueries = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredQueries.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredQueries.value.length / itemsPerPage)
})

const goToPage = (page) => {
  currentPage.value = Math.max(1, Math.min(page, totalPages.value))
}

const viewQuery = (query) => {
  selectedQuery.value = query
  markAsRead(query.id)
}

const markAsRead = async (id) => {
  try {
    const response = await fetch(`/admin/contact-queries/${id}/read`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content,
      },
    })

    if (response.ok) {
      const query = queries.value.find(q => q.id === id)
      if (query) query.read = true
    }
  } catch (error) {
    console.error('Error marking as read:', error)
  }
}

const markAsReplied = async (id) => {
  try {
    const response = await fetch(`/admin/contact-queries/${id}/replied`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content,
      },
    })

    if (response.ok) {
      const query = queries.value.find(q => q.id === id)
      if (query) {
        query.replied = true
        query.read = true
      }
    }
  } catch (error) {
    console.error('Error marking as replied:', error)
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}
</script>
