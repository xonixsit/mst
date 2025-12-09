<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-blue-50 to-cyan-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-blue-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-cyan-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Create Ticket Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-blue-600 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-cyan-900 bg-clip-text text-transparent">
                Create Support Ticket
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Create a new support ticket for a client</p>
            </div>
          </div>
          
          <!-- Action Button -->
          <div class="flex items-center">
            <Link
              href="/admin/support-tickets"
              class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
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
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-4">
        <!-- Form -->
        <form @submit.prevent="submit" class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 space-y-6">
          <!-- Client -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">Client</label>
            <select
              v-model="form.client_id"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white"
              :class="{ 'border-red-500 bg-red-50': errors.client_id }"
            >
              <option value="">Select a client</option>
              <option v-for="client in clients" :key="client.id" :value="client.id">
                {{ client.first_name }} {{ client.last_name }} ({{ client.email }})
              </option>
            </select>
            <p v-if="errors.client_id" class="text-red-600 text-sm mt-2">{{ errors.client_id }}</p>
          </div>

          <!-- Subject -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">Subject</label>
            <input
              v-model="form.subject"
              type="text"
              placeholder="Brief description of the issue"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white"
              :class="{ 'border-red-500 bg-red-50': errors.subject }"
            />
            <p v-if="errors.subject" class="text-red-600 text-sm mt-2">{{ errors.subject }}</p>
          </div>

          <!-- Category -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">Category</label>
            <select
              v-model="form.category"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white"
              :class="{ 'border-red-500 bg-red-50': errors.category }"
            >
              <option value="">Select a category</option>
              <option value="billing">Billing</option>
              <option value="technical">Technical</option>
              <option value="general">General</option>
              <option value="documentation">Documentation</option>
            </select>
            <p v-if="errors.category" class="text-red-600 text-sm mt-2">{{ errors.category }}</p>
          </div>

          <!-- Priority -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">Priority</label>
            <select
              v-model="form.priority"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white"
              :class="{ 'border-red-500 bg-red-50': errors.priority }"
            >
              <option value="">Select priority</option>
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
            <p v-if="errors.priority" class="text-red-600 text-sm mt-2">{{ errors.priority }}</p>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">Description</label>
            <textarea
              v-model="form.description"
              placeholder="Provide detailed information about the issue..."
              rows="6"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 hover:bg-white resize-none"
              :class="{ 'border-red-500 bg-red-50': errors.description }"
            />
            <p v-if="errors.description" class="text-red-600 text-sm mt-2">{{ errors.description }}</p>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-6 border-t border-gray-200">
            <button
              type="submit"
              :disabled="processing"
              class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed font-semibold transform hover:scale-105"
            >
              {{ processing ? 'Creating...' : 'Create Ticket' }}
            </button>
            <Link href="/admin/support-tickets" class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded-lg transition-all duration-200 font-semibold text-center">
              Cancel
            </Link>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  clients: Array,
})

const form = useForm({
  client_id: '',
  subject: '',
  category: '',
  priority: 'medium',
  description: '',
})

const errors = form.errors
const processing = form.processing

const submit = () => {
  form.post('/admin/support-tickets')
}
</script>
