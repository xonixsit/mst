<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-cyan-50 to-blue-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-cyan-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-blue-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Support Ticket Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 via-cyan-600 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-cyan-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
            
            <!-- Title Section -->
            <div>
              <Link href="/client/support-tickets" class="inline-flex items-center text-cyan-600 hover:text-cyan-700 mb-2 font-medium transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Tickets
              </Link>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-cyan-900 to-blue-900 bg-clip-text text-transparent">
                {{ ticket.ticket_number }}
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">{{ ticket.subject }}</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-3 mt-3">
                <span :class="[
                  'px-3 py-1 text-xs font-semibold rounded-full',
                  statusColors[ticket.status]
                ]">
                  {{ formatStatus(ticket.status) }}
                </span>
                <span :class="[
                  'px-3 py-1 text-xs font-semibold rounded-full',
                  priorityColors[ticket.priority]
                ]">
                  {{ formatPriority(ticket.priority) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Ticket Details Card -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket Details</h3>
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div>
                <p class="text-gray-600 text-sm font-medium">Category</p>
                <p class="text-gray-900 font-semibold mt-1">{{ formatCategory(ticket.category) }}</p>
              </div>
              <div>
                <p class="text-gray-600 text-sm font-medium">Created</p>
                <p class="text-gray-900 font-semibold mt-1">{{ formatDate(ticket.created_at) }}</p>
              </div>
              <div v-if="ticket.assigned_to">
                <p class="text-gray-600 text-sm font-medium">Assigned To</p>
                <p class="text-gray-900 font-semibold mt-1">{{ ticket.assigned_to.first_name }} {{ ticket.assigned_to.last_name }}</p>
              </div>
              <div v-if="ticket.resolved_at">
                <p class="text-gray-600 text-sm font-medium">Resolved</p>
                <p class="text-gray-900 font-semibold mt-1">{{ formatDate(ticket.resolved_at) }}</p>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
              <h4 class="text-base font-semibold text-gray-900 mb-3">Description</h4>
              <p class="text-gray-700 whitespace-pre-wrap">{{ ticket.description }}</p>
            </div>
          </div>

          <!-- Conversation Card -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Conversation</h3>

            <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
              <div v-for="reply in ticket.replies" :key="reply.id" class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                  <span class="text-white font-bold text-sm">{{ reply.user.first_name.charAt(0) }}</span>
                </div>
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <p class="font-semibold text-gray-900">{{ reply.user.first_name }} {{ reply.user.last_name }}</p>
                    <span class="text-xs text-gray-500">{{ formatDate(reply.created_at) }}</span>
                  </div>
                  <p class="text-gray-700 whitespace-pre-wrap text-sm">{{ reply.message }}</p>
                </div>
              </div>
              <div v-if="!ticket.replies || ticket.replies.length === 0" class="text-center py-8">
                <p class="text-gray-500">No replies yet</p>
              </div>
            </div>

            <!-- Reply Form -->
            <form @submit.prevent="submitReply" class="border-t border-gray-200 pt-4">
              <textarea v-model="replyForm.message" placeholder="Add a reply..." rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all duration-200"
                :class="{ 'border-red-500': replyForm.errors.message }" />
              <p v-if="replyForm.errors.message" class="text-red-600 text-sm mt-1">{{ replyForm.errors.message }}</p>
              <button type="submit" :disabled="replyForm.processing"
                class="mt-3 px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white rounded-lg hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                {{ replyForm.processing ? 'Sending...' : 'Send Reply' }}
              </button>
            </form>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
          <div class="bg-white rounded-lg shadow p-4">
            <h4 class="font-semibold text-gray-900 mb-3">Ticket Info</h4>
            <div class="space-y-3 text-sm">
              <div>
                <p class="text-gray-600">Ticket Number</p>
                <p class="font-mono text-gray-900">{{ ticket.ticket_number }}</p>
              </div>
              <div>
                <p class="text-gray-600">Status</p>
                <p class="text-gray-900 capitalize">{{ ticket.status.replace('_', ' ') }}</p>
              </div>
              <div>
                <p class="text-gray-600">Priority</p>
                <p class="text-gray-900 capitalize">{{ ticket.priority }}</p>
              </div>
              <div>
                <p class="text-gray-600">Category</p>
                <p class="text-gray-900 capitalize">{{ ticket.category }}</p>
              </div>
            </div>
          </div>

          <div v-if="ticket.assigned_to" class="bg-white rounded-lg shadow p-4">
            <h4 class="font-semibold text-gray-900 mb-3">Assigned To</h4>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                <span class="text-white font-bold text-sm">{{ ticket.assigned_to.first_name.charAt(0) }}</span>
              </div>
              <div>
                <p class="font-semibold text-gray-900">{{ ticket.assigned_to.first_name }} {{ ticket.assigned_to.last_name }}</p>
                <p class="text-xs text-gray-600">{{ ticket.assigned_to.email }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
  import { useForm } from '@inertiajs/vue3'
  import { Link } from '@inertiajs/vue3'
  import AppLayout from '@/Layouts/AppLayout.vue'

  const props = defineProps({
    ticket: Object,
  })

  const statusColors = {
    open: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-amber-100 text-amber-800',
    resolved: 'bg-emerald-100 text-emerald-800',
    closed: 'bg-gray-100 text-gray-800',
  }

  const priorityColors = {
    low: 'bg-emerald-100 text-emerald-800',
    medium: 'bg-amber-100 text-amber-800',
    high: 'bg-orange-100 text-orange-800',
    urgent: 'bg-red-100 text-red-800',
  }

  const replyForm = useForm({
    message: '',
  })

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
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })
  }

  const submitReply = () => {
    console.log('Ticket object:', props.ticket)
    console.log('Ticket ID:', props.ticket?.id)
    console.log('All ticket keys:', Object.keys(props.ticket || {}))
    
    const ticketId = props.ticket?.id
    if (!ticketId) {
      console.error('Ticket ID is missing:', props.ticket)
      alert('Error: Unable to submit reply. Ticket ID not found.')
      return
    }
    replyForm.post(`/client/support-tickets/${ticketId}/reply`, {
      onSuccess: () => {
        replyForm.reset()
        window.location.reload()
      }
    })
  }
</script>
