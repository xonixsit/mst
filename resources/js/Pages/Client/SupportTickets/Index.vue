<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-emerald-50 to-teal-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-emerald-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-teal-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Support Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-emerald-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-emerald-900 to-teal-900 bg-clip-text text-transparent">
                Support Tickets
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Manage your support requests and track resolutions</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-emerald-700">{{ stats.open }} Open</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-teal-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-teal-700">{{ stats.resolved }} Resolved</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Button -->
          <div class="flex items-center">
            <Link
              href="/client/support-tickets/create"
              class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              <span class="font-semibold">New Ticket</span>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-4">
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <!-- Total Tickets -->
          <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 rounded-xl shadow-lg border border-blue-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-blue-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Total Tickets</p>
                  <p class="text-3xl font-bold text-blue-900 mt-2">{{ stats.total }}</p>
                </div>
                <div class="p-4 bg-blue-500 rounded-xl shadow-lg group-hover:bg-blue-600 transition-colors duration-300">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Open -->
          <div class="relative overflow-hidden bg-gradient-to-br from-yellow-50 via-yellow-100 to-yellow-200 rounded-xl shadow-lg border border-yellow-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-yellow-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-yellow-700 uppercase tracking-wide">Open</p>
                  <p class="text-3xl font-bold text-yellow-900 mt-2">{{ stats.open }}</p>
                </div>
                <div class="p-4 bg-yellow-500 rounded-xl shadow-lg group-hover:bg-yellow-600 transition-colors duration-300">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- In Progress -->
          <div class="relative overflow-hidden bg-gradient-to-br from-purple-50 via-purple-100 to-purple-200 rounded-xl shadow-lg border border-purple-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-purple-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-purple-700 uppercase tracking-wide">In Progress</p>
                  <p class="text-3xl font-bold text-purple-900 mt-2">{{ stats.in_progress }}</p>
                </div>
                <div class="p-4 bg-purple-500 rounded-xl shadow-lg group-hover:bg-purple-600 transition-colors duration-300">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Resolved -->
          <div class="relative overflow-hidden bg-gradient-to-br from-green-50 via-green-100 to-green-200 rounded-xl shadow-lg border border-green-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-green-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-green-700 uppercase tracking-wide">Resolved</p>
                  <p class="text-3xl font-bold text-green-900 mt-2">{{ stats.resolved }}</p>
                </div>
                <div class="p-4 bg-green-500 rounded-xl shadow-lg group-hover:bg-green-600 transition-colors duration-300">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tickets List -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
      <div v-if="tickets.data.length === 0" class="p-8 text-center">
        <svg class="w-12 h-12 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-slate-600 font-medium">No support tickets yet</p>
        <p class="text-slate-500 text-sm mt-1">Create a new ticket to get started</p>
      </div>

      <div v-else class="divide-y divide-slate-200">
        <Link v-for="ticket in tickets.data" :key="ticket.id" :href="`/client/support-tickets/${ticket.id}`" class="block p-4 hover:bg-slate-50 transition-colors duration-200">
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-3 mb-2">
                <span class="text-sm font-mono text-slate-600">{{ ticket.ticket_number }}</span>
                <span :class="[
                  'px-2 py-1 text-xs font-semibold rounded-full',
                  statusColors[ticket.status]
                ]">
                  {{ formatStatus(ticket.status) }}
                </span>
                <span :class="[
                  'px-2 py-1 text-xs font-semibold rounded-full',
                  priorityColors[ticket.priority]
                ]">
                  {{ formatPriority(ticket.priority) }}
                </span>
              </div>
              <h3 class="text-lg font-semibold text-slate-900 truncate">{{ ticket.subject }}</h3>
              <p class="text-slate-600 text-sm mt-1 line-clamp-2">{{ ticket.description }}</p>
              <div class="flex items-center gap-4 mt-3 text-xs text-slate-500">
                <span>{{ formatCategory(ticket.category) }}</span>
                <span>{{ formatDate(ticket.created_at) }}</span>
              </div>
            </div>
            <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </Link>
      </div>
    </div>

        <!-- Pagination -->
        <div v-if="tickets.links" class="flex items-center justify-center gap-2 mt-8">
          <Link v-for="link in tickets.links" :key="link.label" :href="link.url" :class="[
            'px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-300',
            link.active
              ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg'
              : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400'
          ]" v-html="link.label" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  tickets: Object,
  stats: Object,
})

const statusColors = {
  open: 'bg-yellow-100 text-yellow-800',
  in_progress: 'bg-purple-100 text-purple-800',
  resolved: 'bg-green-100 text-green-800',
  closed: 'bg-slate-100 text-slate-800',
}

const priorityColors = {
  low: 'bg-blue-100 text-blue-800',
  medium: 'bg-orange-100 text-orange-800',
  high: 'bg-red-100 text-red-800',
  urgent: 'bg-red-200 text-red-900',
}

const formatStatus = (status) => {
  return status.replace('_', ' ').charAt(0).toUpperCase() + status.slice(1).replace('_', ' ')
}

const formatPriority = (priority) => {
  return priority.charAt(0).toUpperCase() + priority.slice(1)
}

const formatCategory = (category) => {
  return category.charAt(0).toUpperCase() + category.slice(1)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
</script>
