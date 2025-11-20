<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Messages</h1>
          <p class="mt-1 text-sm text-gray-600">
            Communicate with your tax professional
            <span v-if="unreadCount > 0" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
              {{ unreadCount }} unread
            </span>
          </p>
        </div>
        <button
          @click="showComposeModal = true"
          class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          New Message
        </button>
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

      <!-- Messages List -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">
            Messages ({{ messages.total }})
          </h2>
        </div>
        
        <div v-if="messages.data.length === 0" class="p-6 text-center">
          <ChatBubbleLeftRightIcon class="mx-auto w-12 h-12 text-gray-400" />
          <p class="mt-2 text-gray-600">No messages found.</p>
          <p class="text-sm text-gray-500">Start a conversation with your tax professional.</p>
        </div>

        <div v-else class="divide-y divide-gray-200">
          <div
            v-for="message in messages.data"
            :key="message.id"
            :class="{
              'bg-blue-50': !message.is_read && message.recipient_id === $page.props.auth.user.id
            }"
            class="p-6 hover:bg-gray-50 cursor-pointer"
            @click="viewMessage(message)"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <UserIcon class="w-6 h-6 text-purple-600" />
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center space-x-2">
                    <h3 class="text-sm font-medium text-gray-900 truncate">
                      {{ message.subject }}
                    </h3>
                    <span
                      v-if="!message.is_read && message.recipient_id === $page.props.auth.user.id"
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                    >
                      New
                    </span>
                    <span
                      :class="{
                        'bg-red-100 text-red-800': message.priority === 'high',
                        'bg-yellow-100 text-yellow-800': message.priority === 'normal',
                        'bg-gray-100 text-gray-800': message.priority === 'low'
                      }"
                      class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                    >
                      {{ message.priority.charAt(0).toUpperCase() + message.priority.slice(1) }}
                    </span>
                  </div>
                  <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                    <span>
                      {{ message.sender_id === $page.props.auth.user.id ? 'To' : 'From' }}: 
                      {{ message.sender_id === $page.props.auth.user.id ? 
                        (message.recipient ? `${message.recipient.first_name} ${message.recipient.last_name}` : 'Unknown') : 
                        (message.sender ? `${message.sender.first_name} ${message.sender.last_name}` : 'Unknown') }}
                    </span>
                    <span>{{ formatDate(message.created_at) }}</span>
                  </div>
                  <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ message.body }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <button
                  v-if="!message.is_read && message.recipient_id === $page.props.auth.user.id"
                  @click.stop="markAsRead(message)"
                  class="text-indigo-600 hover:text-indigo-500 text-sm font-medium"
                >
                  Mark Read
                </button>
                <ChevronRightIcon class="w-5 h-5 text-gray-400" />
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

    <!-- Compose Modal -->
    <div v-if="showComposeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">New Message</h3>
          <form @submit.prevent="sendMessage" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
              <select
                v-model="composeForm.recipient_id"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required
              >
                <option value="">Select recipient</option>
                <option v-for="professional in taxProfessionals" :key="professional.id" :value="professional.id">
                  {{ `${professional.first_name} ${professional.last_name}` }}
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
              <input
                v-model="composeForm.subject"
                type="text"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
              <select
                v-model="composeForm.priority"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required
              >
                <option value="low">Low</option>
                <option value="normal">Normal</option>
                <option value="high">High</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
              <textarea
                v-model="composeForm.body"
                rows="5"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                maxlength="2000"
                required
              ></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeComposeModal"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="sending"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
              >
                {{ sending ? 'Sending...' : 'Send Message' }}
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