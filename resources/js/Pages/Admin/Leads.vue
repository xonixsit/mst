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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-cyan-900 bg-clip-text text-transparent">
                Leads Management
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Track and manage prospective clients</p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm font-medium">Total Leads</p>
          <p class="text-3xl font-bold text-gray-900 mt-2">{{ leads.length }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm font-medium">New</p>
          <p class="text-3xl font-bold text-blue-600 mt-2">{{ leads.filter(l => l.status === 'new').length }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm font-medium">Contacted</p>
          <p class="text-3xl font-bold text-indigo-600 mt-2">{{ leads.filter(l => l.status === 'contacted').length }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-gray-600 text-sm font-medium">Converted</p>
          <p class="text-3xl font-bold text-green-600 mt-2">{{ leads.filter(l => l.status === 'converted').length }}</p>
        </div>
      </div>

      <!-- Leads Table -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 space-y-3">
          <div class="flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">All Leads</h2>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Search leads..." 
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm w-64"
            />
          </div>
          <div class="flex gap-2 flex-wrap">
            <button 
              @click="filterStatus = ''"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                filterStatus === '' 
                  ? 'bg-blue-600 text-white shadow-md' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              All Status
            </button>
            <button 
              @click="filterStatus = 'new'"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                filterStatus === 'new' 
                  ? 'bg-blue-100 text-blue-800 border-2 border-blue-600' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              New
            </button>
            <button 
              @click="filterStatus = 'contacted'"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                filterStatus === 'contacted' 
                  ? 'bg-indigo-100 text-indigo-800 border-2 border-indigo-600' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              Contacted
            </button>
            <button 
              @click="filterStatus = 'converted'"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                filterStatus === 'converted' 
                  ? 'bg-green-100 text-green-800 border-2 border-green-600' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              Converted
            </button>
            <button 
              @click="filterStatus = 'rejected'"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                filterStatus === 'rejected' 
                  ? 'bg-red-100 text-red-800 border-2 border-red-600' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              Rejected
            </button>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Phone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="lead in paginatedLeads" :key="lead.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ lead.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ lead.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ lead.phone }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <select 
                    :value="lead.status"
                    @change="updateLeadStatus(lead.id, $event.target.value)"
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-medium',
                      lead.status === 'new' ? 'bg-blue-100 text-blue-800' :
                      lead.status === 'contacted' ? 'bg-indigo-100 text-indigo-800' :
                      lead.status === 'converted' ? 'bg-green-100 text-green-800' :
                      'bg-red-100 text-red-800'
                    ]"
                  >
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="converted">Converted</option>
                    <option value="rejected">Rejected</option>
                  </select>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatDate(lead.created_at) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <button @click="selectedLead = lead" class="text-blue-600 hover:text-blue-800 font-medium">View</button>
                  <button class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
          <div class="text-sm text-gray-600">
            Showing <span class="font-medium">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> to <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filteredLeads.length) }}</span> of <span class="font-medium">{{ filteredLeads.length }}</span> leads
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
    </div>

    <!-- Lead Detail Modal - HFI Compliant -->
    <div v-if="selectedLead" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="lead-modal-title">
      <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full flex flex-col max-h-[90vh]">
        <!-- Header with Clear Title and Close Button -->
          <div class="inset-0 bg-gradient-to-r from-blue-500 via-blue-500 to-cyan-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center flex-shrink-0">
          <div>
            <h2 id="lead-modal-title" class="text-lg font-semibold text-gray-900">Lead Information</h2>
            <p class="text-xs text-gray-500 mt-0.5">Review and manage lead details</p>
          </div>
          <button 
            @click="selectedLead = null" 
            class="text-gray-400 hover:text-gray-600 transition-colors p-1 flex-shrink-0"
            aria-label="Close modal"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <!-- Content with Clear Information Hierarchy - Scrollable -->
        <div class="overflow-y-auto flex-1 px-6 py-4 space-y-4">
          <!-- Primary Information Section -->
          <section class="space-y-2">
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wide">Contact Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-600">Full Name</label>
                <p class="text-sm text-gray-900">{{ selectedLead.name }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600">Email Address</label>
                <p class="text-sm text-gray-900 break-all">{{ selectedLead.email }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600">Phone Number</label>
                <p class="text-sm text-gray-900">{{ selectedLead.phone || 'Not provided' }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600">Date Received</label>
                <p class="text-sm text-gray-900">{{ formatDate(selectedLead.created_at) }}</p>
              </div>
            </div>
          </section>

          <!-- Status Section -->
          <section class="border-t border-gray-200 pt-3 space-y-2">
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wide">Status</h3>
            <span :class="[
              'inline-block px-2 py-1 rounded text-xs font-medium',
              selectedLead.status === 'new' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
              selectedLead.status === 'contacted' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' :
              selectedLead.status === 'converted' ? 'bg-green-50 text-green-700 border border-green-200' :
              'bg-red-50 text-red-700 border border-red-200'
            ]">
              {{ selectedLead.status.charAt(0).toUpperCase() + selectedLead.status.slice(1) }}
            </span>
          </section>

          <!-- Additional Information Section -->
          <section v-if="selectedLead.source || selectedLead.notes" class="border-t border-gray-200 pt-3 space-y-2">
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wide">Additional Details</h3>
            <div v-if="selectedLead.source">
              <label class="block text-xs font-medium text-gray-600">Lead Source</label>
              <p class="text-sm text-gray-900">{{ selectedLead.source }}</p>
            </div>
            <div v-if="selectedLead.notes">
              <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
              <div class="bg-gray-50 rounded p-2 border border-gray-200 max-h-40 overflow-y-auto">
                <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ selectedLead.notes }}</p>
              </div>
            </div>
          </section>
          
          <!-- Assignment Section -->
          <section v-if="isAdmin" class="border-t border-gray-200 pt-3 space-y-2">
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wide">Assignment</h3>
            <div v-if="selectedLead.assigned_to" class="bg-blue-50 border border-blue-200 rounded p-2 mb-2">
              <p class="text-xs font-medium text-blue-900">Currently Assigned To</p>
              <p class="text-xs text-blue-800 mt-0.5">{{ getAssignedProfessional(selectedLead.assigned_to) }}</p>
            </div>
            <div>
              <label for="professional-select" class="block text-xs font-medium text-gray-600 mb-1">Assign to Tax Professional</label>
              <select 
                id="professional-select"
                v-model="selectedProfessional"
                class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option :value="null">Select a professional...</option>
                <option v-for="prof in taxProfessionals" :key="prof.id" :value="prof.id">
                  {{ prof.first_name }} {{ prof.last_name }}
                </option>
              </select>
            </div>
          </section>
        </div>

        <!-- Action Buttons - Fixed at Bottom - HFI Compliant -->
        <div class="border-t border-gray-200 px-6 py-3 flex-shrink-0 bg-white flex gap-2 justify-end">
          <button 
            @click="selectedLead = null"
            class="px-3 py-2.5 border border-gray-300 text-gray-700 rounded text-sm font-medium hover:bg-gray-50 transition-colors"
          >
            Close
          </button>
          <button 
            @click="assignLead"
            :disabled="!selectedProfessional"
            class="px-3 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white rounded text-sm font-medium transition-colors"
          >
            Assign
          </button>
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
const selectedLead = ref(null)
const currentPage = ref(1)
const itemsPerPage = 10

const props = defineProps({
  leads: {
    type: Array,
    default: () => [],
  },
  taxProfessionals: {
    type: Array,
    default: () => [],
  },
})

const leads = ref(props.leads)
const isAdmin = ref(true) // Check if current user is admin
const selectedProfessional = ref(null)

const filteredLeads = computed(() => {
  return leads.value.filter(lead => {
    const matchesStatus = !filterStatus.value || lead.status === filterStatus.value
    const matchesSearch = !searchQuery.value || 
      lead.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      lead.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchesStatus && matchesSearch
  })
})

const paginatedLeads = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredLeads.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredLeads.value.length / itemsPerPage)
})

const goToPage = (page) => {
  currentPage.value = Math.max(1, Math.min(page, totalPages.value))
}

const updateLeadStatus = async (id, status) => {
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
    
    const response = await fetch(`/admin/leads/${id}/status`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken,
      },
      body: JSON.stringify({ status }),
    })

    const data = await response.json()
    
    if (response.ok && data.success) {
      const lead = leads.value.find(l => l.id === id)
      if (lead) {
        lead.status = status
      }
    } else {
      console.error('Failed to update lead status:', data.message)
    }
  } catch (error) {
    console.error('Error updating lead status:', error)
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const assignLead = async () => {
  if (!selectedProfessional.value || !selectedLead.value) return

  try {
    const response = await fetch(`/admin/leads/${selectedLead.value.id}/assign`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content,
      },
      body: JSON.stringify({ assigned_to: selectedProfessional.value }),
    })

    const data = await response.json()
    
    if (response.ok && data.success) {
      const lead = leads.value.find(l => l.id === selectedLead.value.id)
      if (lead) {
        lead.assigned_to = selectedProfessional.value
        selectedLead.value.assigned_to = selectedProfessional.value
      }
      selectedLead.value = null
    }
  } catch (error) {
    console.error('Error assigning lead:', error)
  }
}

const getAssignedProfessional = (assignedTo) => {
  const prof = props.taxProfessionals.find(p => p.id === assignedTo)
  return prof ? `${prof.first_name} ${prof.last_name}` : 'Unassigned'
}
</script>
