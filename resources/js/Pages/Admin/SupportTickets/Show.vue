<template>
  <AppLayout title="Support Ticket Details">
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-blue-50 to-cyan-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-blue-100/40 to-transparent rounded-bl-full">
        </div>
        <div
          class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-cyan-100/30 to-transparent rounded-tr-full">
        </div>

        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Support Icon -->
            <div
              class="w-14 h-14 bg-gradient-to-br from-blue-500 via-blue-600 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>

            <!-- Title Section -->
            <div>
              <h1
                class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-cyan-900 bg-clip-text text-transparent">
                {{ ticket.ticket_number }}
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">{{ ticket.subject }}</p>

              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div :class="[
                    'w-2 h-2 rounded-full',
                    ticket.status === 'open' ? 'bg-yellow-400' : ticket.status === 'in_progress' ? 'bg-purple-400' : ticket.status === 'resolved' ? 'bg-green-400' : 'bg-slate-400'
                  ]"></div>
                  <span :class="[
                    'text-xs font-semibold',
                    ticket.status === 'open' ? 'text-yellow-700' : ticket.status === 'in_progress' ? 'text-purple-700' : ticket.status === 'resolved' ? 'text-green-700' : 'text-slate-700'
                  ]">{{ formatStatus(ticket.status) }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div :class="[
                    'w-2 h-2 rounded-full',
                    ticket.priority === 'urgent' ? 'bg-red-400' : ticket.priority === 'high' ? 'bg-orange-400' : ticket.priority === 'medium' ? 'bg-yellow-400' : 'bg-blue-400'
                  ]"></div>
                  <span :class="[
                    'text-xs font-semibold',
                    ticket.priority === 'urgent' ? 'text-red-700' : ticket.priority === 'high' ? 'text-orange-700' : ticket.priority === 'medium' ? 'text-yellow-700' : 'text-blue-700'
                  ]">{{ formatPriority(ticket.priority) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Button -->
          <div class="flex items-center">
            <Link href="/admin/support-tickets"
              class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="font-semibold">Back to Tickets</span>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Details -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-8 py-6 border-b border-gray-200">
                <div class="mb-6">
                  <h3 class="text-xl font-bold text-gray-900">Ticket Details</h3>
                  <p class="text-sm text-gray-600 mt-1">Complete ticket information and status</p>
                </div>
                
                <!-- Ticket Info in Header -->
                <div class="grid grid-cols-3 gap-4 text-sm">
                  <div>
                    <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Ticket Number</p>
                    <p class="font-mono text-gray-900 font-semibold">{{ ticket.ticket_number }}</p>
                  </div>
                  <div>
                    <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Category</p>
                    <p class="text-gray-900 capitalize font-semibold">{{ ticket.category }}</p>
                  </div>
                  <div>
                    <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Created By</p>
                    <p class="text-gray-900 font-semibold">{{ ticket.user?.first_name }} {{ ticket.user?.last_name }}</p>
                  </div>
                </div>
              </div>
              <div class="p-8">
                <div v-if="ticket.resolved_at" class="grid grid-cols-1 gap-6 mb-8">
                  <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 border border-green-200">
                    <p class="text-sm font-semibold text-green-700 uppercase tracking-wide">Resolved</p>
                    <p class="text-lg font-bold text-green-900 mt-2">{{ formatDate(ticket.resolved_at) }}</p>
                  </div>
                </div>

                <div class="border-t border-gray-200 pt-8">
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
                  <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ ticket.description }}</p>
                </div>
              </div>
            </div>

            <!-- Conversation Section -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-8 py-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Conversation</h3>
              </div>

              <div class="p-8">
                <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
                  <div v-for="reply in ticket.replies" :key="reply.id"
                    class="flex gap-4 pb-4 border-b border-gray-200 last:border-b-0">
                    <div
                      class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center flex-shrink-0">
                      <span class="text-white font-bold text-sm">{{ reply.user?.first_name?.charAt(0) || 'U' }}</span>
                    </div>
                    <div class="flex-1">
                      <div class="flex items-center gap-2 mb-2">
                        <p class="font-semibold text-gray-900">{{ reply.user?.first_name }} {{ reply.user?.last_name }}
                        </p>
                        <span v-if="isStaff(reply.user)"
                          class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded">Staff</span>
                        <span class="text-xs text-gray-500">{{ formatDate(reply.created_at) }}</span>
                      </div>
                      <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ reply.message }}</p>
                    </div>
                  </div>
                </div>

                <!-- Reply Form -->
                <form @submit.prevent="submitReply" class="border-t border-gray-200 pt-6">
                  <textarea v-model="replyForm.message" placeholder="Add a reply..." rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white resize-none"
                    :class="{ 'border-red-500 bg-red-50': replyForm.errors.message }" />
                  <p v-if="replyForm.errors.message" class="text-red-600 text-sm mt-2">{{ replyForm.errors.message }}
                  </p>
                  <button type="submit" :disabled="replyForm.processing"
                    class="mt-4 px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed font-semibold">
                    {{ replyForm.processing ? 'Sending...' : 'Send Reply' }}
                  </button>
                </form>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Status Update -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-4 border-b border-gray-200">
                <h4 class="font-semibold text-gray-900">Update Status</h4>
              </div>
              <div class="p-6 space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                  <select v-model="statusForm.status" @change="updateStatus"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Priority</label>
                  <select v-model="priorityForm.priority" @change="updatePriority"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Assign To</label>
                  <select v-model="assignForm.assigned_to" @change="updateAssignment"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Unassigned</option>
                    <option v-for="professional in taxProfessionals" :key="professional.id" :value="professional.id">
                      {{ professional.first_name }} {{ professional.last_name }}
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Assigned To -->
            <div v-if="ticket.assigned_to" class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-4 border-b border-gray-200">
                <h4 class="font-semibold text-gray-900">Assigned To</h4>
              </div>
              <div class="p-6">
                <div class="flex items-center gap-3">
                  <div
                    class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ ticket.assigned_to?.first_name?.charAt(0) || 'A'
                      }}</span>
                  </div>
                  <div>
                    <p class="font-semibold text-gray-900">{{ ticket.assigned_to?.first_name }} {{
                      ticket.assigned_to?.last_name }}</p>
                    <p class="text-xs text-gray-600">{{ ticket.assigned_to?.email }}</p>
                  </div>
                </div>
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
    taxProfessionals: Array,
  })

  const ticket = props.ticket

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

  const statusForm = useForm({
    status: ticket.status,
  })

  const priorityForm = useForm({
    priority: ticket.priority,
  })

  const assignForm = useForm({
    assigned_to: ticket.assigned_to?.id || '',
  })

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

  const isStaff = (user) => {
    return user.role === 'admin' || user.role === 'tax_professional'
  }

  const updateStatus = () => {
    statusForm.patch(`/admin/support-tickets/${ticket.id}/status`)
  }

  const updatePriority = () => {
    priorityForm.patch(`/admin/support-tickets/${ticket.id}/priority`)
  }

  const updateAssignment = () => {
    assignForm.patch(`/admin/support-tickets/${ticket.id}/assign`)
  }

  const submitReply = () => {
    replyForm.post(`/admin/support-tickets/${ticket.id}/reply`, {
      onSuccess: () => {
        replyForm.reset()
        window.location.reload()
      }
    })
  }
</script>