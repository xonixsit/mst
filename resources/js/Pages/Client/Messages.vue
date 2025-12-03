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
            <!-- Messages Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 via-purple-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-purple-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-purple-900 to-indigo-900 bg-clip-text text-transparent">
                Messages
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">
                Communicate with your tax professional
                <span v-if="unreadCount > 0" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                  {{ unreadCount }} unread
                </span>
              </p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-purple-700">Secure Communication</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-indigo-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-indigo-700">Real-time Updates</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <button
              @click="showComposeModal = true"
              class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              <span class="font-semibold">New Message</span>
            </button>
            
            <Link
              href="/client/dashboard"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
              </svg>
              <span class="font-semibold">Back to Dashboard</span>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Filters -->
      <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select
              v-model="filters.status"
              @change="applyFilters"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">All Messages</option>
              <option value="unread">Unread</option>
              <option value="read">Read</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
            <select
              v-model="filters.priority"
              @change="applyFilters"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">All Priorities</option>
              <option value="high">High</option>
              <option value="normal">Normal</option>
              <option value="low">Low</option>
            </select>
          </div>
          <div class="flex items-end">
            <button
              @click="clearFilters"
              class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              Clear Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Enhanced Messages List -->
      <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-bold text-gray-900">Message Center</h2>
              <p class="text-sm text-gray-600 mt-1">{{ messages.total }} total messages</p>
            </div>
            <div class="flex items-center space-x-2">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
            </div>
          </div>
        </div>
        
        <div v-if="messages.data.length === 0" class="p-12 text-center">
          <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <ChatBubbleLeftRightIcon class="w-10 h-10 text-purple-600" />
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">No messages yet</h3>
          <p class="text-gray-600 mb-4">Start a conversation with your tax professional.</p>
          <button
            @click="showComposeModal = true"
            class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
          >
            Send First Message
          </button>
        </div>

        <div v-else class="divide-y divide-gray-100">
          <div
            v-for="message in messages.data"
            :key="message.id"
            :class="{
              'bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400': !message.is_read && message.recipient_id === $page.props.auth.user.id
            }"
            class="p-6 hover:bg-gradient-to-r hover:from-gray-50 hover:to-slate-50 cursor-pointer transition-all duration-200 group"
            @click="viewMessage(message)"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg ring-2 ring-purple-100 group-hover:ring-purple-200 transition-all duration-200">
                    <UserIcon class="w-6 h-6 text-white" />
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-3 mb-2">
                    <h3 class="text-base font-bold text-gray-900 truncate group-hover:text-purple-700 transition-colors duration-200">
                      {{ message.subject }}
                    </h3>
                    <span
                      v-if="!message.is_read && message.recipient_id === $page.props.auth.user.id"
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-sm animate-pulse"
                    >
                      NEW
                    </span>
                    <span
                      :class="{
                        'bg-gradient-to-r from-red-500 to-rose-500 text-white': message.priority === 'high',
                        'bg-gradient-to-r from-yellow-500 to-orange-500 text-white': message.priority === 'normal',
                        'bg-gradient-to-r from-gray-400 to-gray-500 text-white': message.priority === 'low'
                      }"
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm"
                    >
                      {{ message.priority.toUpperCase() }}
                    </span>
                  </div>
                  <div class="flex items-center space-x-4 text-sm text-gray-600 mb-2">
                    <div class="flex items-center space-x-2">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      <span class="font-medium">
                        {{ message.sender_id === $page.props.auth.user.id ? 'To' : 'From' }}: 
                        {{ message.sender_id === $page.props.auth.user.id ? 
                          (message.recipient ? `${message.recipient.first_name} ${message.recipient.last_name}` : 'Unknown') : 
                          (message.sender ? `${message.sender.first_name} ${message.sender.last_name}` : 'Unknown') }}
                      </span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <span>{{ formatDate(message.created_at) }}</span>
                    </div>
                  </div>
                  <p class="text-sm text-gray-700 line-clamp-2 leading-relaxed">{{ message.body }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-3">
                <button
                  v-if="!message.is_read && message.recipient_id === $page.props.auth.user.id"
                  @click.stop="markAsRead(message)"
                  class="bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                >
                  Mark Read
                </button>
                <div class="p-2 rounded-lg bg-gray-100 group-hover:bg-purple-100 transition-all duration-200">
                  <ChevronRightIcon class="w-5 h-5 text-gray-400 group-hover:text-purple-600 transition-colors duration-200" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="messages.last_page > 1" class="px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing {{ messages.from }} to {{ messages.to }} of {{ messages.total }} results
            </div>
            <div class="flex space-x-2">
              <button
                v-for="page in paginationPages"
                :key="page"
                @click="goToPage(page)"
                :class="{
                  'bg-indigo-600 text-white': page === messages.current_page,
                  'bg-white text-gray-700 hover:bg-gray-50': page !== messages.current_page
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

    <!-- Enhanced Compose Modal -->
    <div v-if="showComposeModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-start justify-center p-4 pt-8 overflow-y-auto">
      <div class="relative w-full max-w-2xl bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all my-8">
        <!-- Enhanced Header -->
        <div class="bg-gradient-to-r from-slate-50 via-purple-50 to-indigo-50 px-8 py-6 border-b border-gray-200 relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-16 bg-gradient-to-bl from-purple-100/40 to-transparent rounded-bl-full"></div>
          <div class="relative flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg ring-4 ring-purple-100">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-purple-900 to-indigo-900 bg-clip-text text-transparent">
                  New Message
                </h3>
                <p class="text-sm text-gray-600 font-medium">Send a secure message to your tax professional</p>
              </div>
            </div>
            <button @click="closeComposeModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Enhanced Form -->
        <div class="p-8">
          <form @submit.prevent="sendMessage" class="space-y-8">
            <!-- Recipient & Priority Section -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200">
              <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <h4 class="text-lg font-bold text-blue-900">Recipient & Priority</h4>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-blue-700">To <span class="text-red-500">*</span></label>
                  <select
                    v-model="composeForm.recipient_id"
                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white text-gray-900"
                    required
                  >
                    <option value="">Select recipient</option>
                    <option v-for="professional in taxProfessionals" :key="professional.id" :value="professional.id">
                      {{ `${professional.first_name} ${professional.last_name}` }}
                    </option>
                  </select>
                </div>

                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-blue-700">Priority <span class="text-red-500">*</span></label>
                  <select
                    v-model="composeForm.priority"
                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white text-gray-900"
                    required
                  >
                    <option value="low">Low Priority</option>
                    <option value="normal">Normal Priority</option>
                    <option value="high">High Priority</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Subject Section -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 border border-green-200">
              <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a1.994 1.994 0 01-1.414.586H7a4 4 0 01-4-4V7a4 4 0 014-4z"></path>
                  </svg>
                </div>
                <h4 class="text-lg font-bold text-green-900">Subject</h4>
              </div>
              
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-green-700">Subject <span class="text-red-500">*</span></label>
                <input
                  v-model="composeForm.subject"
                  type="text"
                  class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-gray-900 placeholder-gray-500"
                  placeholder="Enter message subject"
                  required
                />
              </div>
            </div>

            <!-- Message Section -->
            <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200">
              <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center mr-3">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                  </svg>
                </div>
                <h4 class="text-lg font-bold text-gray-900">Message Content</h4>
              </div>
              
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Message <span class="text-red-500">*</span></label>
                <textarea
                  v-model="composeForm.body"
                  rows="6"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200 text-gray-900 placeholder-gray-500 resize-none"
                  maxlength="2000"
                  placeholder="Type your message here..."
                  required
                ></textarea>
                <div class="text-xs text-gray-500 text-right">{{ composeForm.body?.length || 0 }}/2000 characters</div>
              </div>
            </div>
          </form>
        </div>

        <!-- Enhanced Footer -->
        <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-8 py-6 border-t border-gray-200 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="closeComposeModal"
            class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md"
          >
            Cancel
          </button>
          <button
            type="submit"
            @click="sendMessage"
            :disabled="sending"
            class="w-full sm:w-auto bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
          >
            <div class="flex items-center justify-center">
              <svg v-if="sending" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
              </svg>
              {{ sending ? 'Sending...' : 'Send Message' }}
            </div>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
  ChatBubbleLeftRightIcon, 
  UserIcon, 
  ChevronRightIcon 
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  messages: Object,
  filters: Object,
  taxProfessionals: Array,
  unreadCount: Number
})

const showComposeModal = ref(false)
const sending = ref(false)
const filters = ref({ ...props.filters })

const composeForm = ref({
  recipient_id: '',
  subject: '',
  body: '',
  priority: 'normal'
})

const paginationPages = computed(() => {
  const pages = []
  const current = props.messages.current_page
  const last = props.messages.last_page
  
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  return pages
})

const sendMessage = () => {
  sending.value = true
  
  router.post('/client/messages', composeForm.value, {
    onSuccess: () => {
      closeComposeModal()
    },
    onFinish: () => {
      sending.value = false
    }
  })
}

const closeComposeModal = () => {
  showComposeModal.value = false
  composeForm.value = {
    recipient_id: '',
    subject: '',
    body: '',
    priority: 'normal'
  }
}

const viewMessage = (message) => {
  router.get(`/client/messages/${message.id}`)
}

const markAsRead = (message) => {
  router.patch(`/client/messages/${message.id}/read`)
}

const applyFilters = () => {
  router.get('/client/messages', filters.value, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  filters.value = { status: '', priority: '' }
  applyFilters()
}

const goToPage = (page) => {
  router.get('/client/messages', { ...filters.value, page }, {
    preserveState: true,
    replace: true
  })
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>