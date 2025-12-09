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
            <!-- Message Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 via-cyan-600 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-cyan-100">
              <EnvelopeIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-cyan-900 to-blue-900 bg-clip-text text-transparent">
                {{ message.subject }}
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">
                {{ message.sender_id === page.props.auth.user.id ? 'To: ' + getUserName(message.recipient) : 'From: ' + getUserName(message.sender) }}
              </p>
              
              <!-- Message Info -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-cyan-700">Client: {{ message.client?.user ? getUserName(message.client.user) : 'Unknown Client' }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div :class="{
                    'bg-red-400': message.priority === 'high',
                    'bg-yellow-400': message.priority === 'normal',
                    'bg-gray-400': message.priority === 'low'
                  }" class="w-2 h-2 rounded-full"></div>
                  <span :class="{
                    'text-red-700': message.priority === 'high',
                    'text-yellow-700': message.priority === 'normal',
                    'text-gray-700': message.priority === 'low'
                  }" class="text-xs font-semibold">{{ message.priority.charAt(0).toUpperCase() + message.priority.slice(1) }} Priority</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex items-center space-x-3">
            <button
              @click="$inertia.visit('/admin/messages')"
              class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group font-semibold"
            >
              <ArrowLeftIcon class="w-5 h-5 mr-2" />
              <span>Back to Messages</span>
            </button>
            <button
              @click="showReplyModal = true"
              class="bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group font-semibold"
            >
              <ChatBubbleLeftRightIcon class="w-5 h-5 mr-2" />
              <span>Reply</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-4">
        <!-- Enhanced Conversation History -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-cyan-500 rounded-lg flex items-center justify-center mr-3">
                <ChatBubbleLeftRightIcon class="w-4 h-4 text-white" />
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900">Message Thread</h3>
                <p class="text-sm text-gray-600">{{ (conversation || [message]).length }} message{{ (conversation || [message]).length !== 1 ? 's' : '' }} in this conversation</p>
              </div>
            </div>
          </div>
          
          <div class="p-8 space-y-6">
            <div 
              v-for="(msg, index) in (conversation || [message])" 
              :key="msg.id"
              class="relative"
            >
              <!-- Message Card -->
              <div 
                class="bg-gradient-to-br rounded-2xl shadow-lg border-2 transition-all duration-300 hover:shadow-xl"
                :class="{ 
                  'from-cyan-50 to-blue-100 border-cyan-200 ring-2 ring-cyan-300': msg.id === message.id,
                  'from-gray-50 to-slate-100 border-gray-200': msg.id !== message.id
                }"
              >
                <!-- Message Header -->
                <div class="px-6 py-5 border-b border-gray-200">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                      <div 
                        class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg"
                        :class="msg.sender_id === page.props.auth.user.id ? 'bg-gradient-to-br from-blue-500 to-indigo-600' : 'bg-gradient-to-br from-purple-500 to-pink-600'"
                      >
                        <UserIcon class="w-6 h-6 text-white" />
                      </div>
                      <div>
                        <h3 class="text-lg font-bold text-gray-900">
                          {{ msg.sender_id === page.props.auth.user.id ? 'You' : getUserName(msg.sender) }}
                        </h3>
                        <div class="flex items-center space-x-3 mt-1">
                          <p class="text-sm font-medium text-gray-600">{{ formatDate(msg.created_at) }}</p>
                          <div v-if="!msg.is_read && msg.recipient_id === page.props.auth.user.id" class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                            <span class="text-xs font-semibold text-blue-700">Unread</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center space-x-3">
                      <span
                        :class="{
                          'bg-red-100 text-red-800 border-red-200': msg.priority === 'high',
                          'bg-yellow-100 text-yellow-800 border-yellow-200': msg.priority === 'normal',
                          'bg-gray-100 text-gray-800 border-gray-200': msg.priority === 'low'
                        }"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-sm"
                      >
                        <div :class="{
                          'bg-red-500': msg.priority === 'high',
                          'bg-yellow-500': msg.priority === 'normal',
                          'bg-gray-500': msg.priority === 'low'
                        }" class="w-1.5 h-1.5 rounded-full mr-2"></div>
                        {{ msg.priority.charAt(0).toUpperCase() + msg.priority.slice(1) }}
                      </span>
                    </div>
                  </div>
                </div>
          
                <!-- Message Content -->
                <div class="px-6 py-6">
                  <h4 class="text-lg font-bold text-gray-900 mb-4">{{ msg.subject }}</h4>
                  <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-inner">
                    <p class="whitespace-pre-wrap text-gray-800 leading-relaxed">{{ msg.body }}</p>
                  </div>
                </div>

                <!-- Message Footer -->
                <div v-if="msg.read_at" class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-100 border-t border-gray-200 rounded-b-2xl">
                  <div class="flex items-center space-x-2">
                    <CheckCircleIcon class="w-4 h-4 text-green-600" />
                    <p class="text-sm font-medium text-green-800">
                      Read on {{ formatDate(msg.read_at) }}
                    </p>
                  </div>
                </div>
              </div>
              
              <!-- Thread Connector -->
              <div v-if="index < (conversation || [message]).length - 1" class="flex justify-center my-4">
                <div class="w-px h-8 bg-gradient-to-b from-cyan-300 to-blue-400"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Reply Modal -->
    <div v-if="showReplyModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
      <div class="relative bg-white shadow-2xl rounded-2xl border border-gray-100 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200 rounded-t-2xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-cyan-500 rounded-lg flex items-center justify-center mr-3">
                <ChatBubbleLeftRightIcon class="w-4 h-4 text-white" />
              </div>
              <h3 class="text-lg font-bold text-gray-900">Reply to Message</h3>
            </div>
            <button
              @click="closeReplyModal"
              class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <div class="p-8">
          <form @submit.prevent="sendReply" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">To</label>
                <input
                  :value="getReplyRecipientName()"
                  type="text"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 cursor-not-allowed"
                  disabled
                />
              </div>
              
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Subject</label>
                <input
                  :value="'Re: ' + message.subject"
                  type="text"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 cursor-not-allowed"
                  disabled
                />
              </div>
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-semibold text-gray-700">Your Reply</label>
              <textarea
                v-model="replyForm.body"
                rows="8"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 resize-none text-gray-900"
                maxlength="2000"
                required
                placeholder="Type your reply here..."
              ></textarea>
              <div class="flex justify-between items-center">
                <p class="text-xs text-gray-500">{{ replyForm.body.length }}/2000 characters</p>
              </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
              <button
                type="button"
                @click="closeReplyModal"
                class="bg-gradient-to-r from-gray-500 to-slate-600 hover:from-gray-600 hover:to-slate-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="replying || !replyForm.body.trim()"
                class="bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center"
              >
                <svg v-if="replying" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <ChatBubbleLeftRightIcon v-else class="w-5 h-5 mr-2" />
                {{ replying ? 'Sending Reply...' : 'Send Reply' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { 
  ArrowLeftIcon, 
  UserIcon, 
  EnvelopeIcon,
  ChatBubbleLeftRightIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  message: Object,
  conversation: Array
})

const page = usePage()
const showReplyModal = ref(false)
const replying = ref(false)

const replyForm = ref({
  body: ''
})

const sendReply = () => {
  if (!replyForm.value.body || !replyForm.value.body.trim()) {
    alert('Please enter a reply message')
    return
  }
  
  replying.value = true
  
  router.post(`/admin/messages/${props.message.id}/reply`, {
    body: replyForm.value.body.trim(),
    client_id: props.message.client?.id
  }, {
    onSuccess: () => {
      closeReplyModal()
      router.reload()
    },
    onError: (errors) => {
      alert('Error sending reply: ' + (errors.error || 'Unknown error'))
    },
    onFinish: () => {
      replying.value = false
    }
  })
}

const closeReplyModal = () => {
  showReplyModal.value = false
  replyForm.value = { body: '' }
}

const getReplyRecipientName = () => {
  if (props.message.sender_id === props.message.recipient_id) {
    return getUserName(props.message.sender)
  }
  
  if (props.message.sender_id === page.props.auth.user.id) {
    return getUserName(props.message.recipient)
  } else {
    return getUserName(props.message.sender)
  }
}

const getUserName = (user) => {
  if (!user) return 'Unknown User'
  
  // Use the name field from users table
  if (user.name) return user.name
  
  // Try to construct name from first/last name fields
  const nameParts = []
  if (user.first_name) nameParts.push(user.first_name)
  if (user.last_name) nameParts.push(user.last_name)
  
  if (nameParts.length > 0) {
    return nameParts.join(' ')
  }
  
  // Fallback to email
  if (user.email) return user.email
  
  return 'Unknown User'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>