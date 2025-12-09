<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-purple-50 to-indigo-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-purple-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-indigo-100/30 to-transparent rounded-tr-full"></div>
        
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <button
              @click="$inertia.visit('/client/messages')"
              class="w-12 h-12 bg-gradient-to-br from-gray-500 to-slate-600 rounded-xl flex items-center justify-center shadow-lg ring-4 ring-gray-100 hover:ring-gray-200 transition-all duration-200"
            >
              <ArrowLeftIcon class="w-6 h-6 text-white" />
            </button>
            
            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 via-purple-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-purple-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
            </div>
            
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-purple-900 to-indigo-900 bg-clip-text text-transparent">
                {{ message.subject }}
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">
                Conversation with {{ message.sender_id === page.props.auth.user.id ? getUserName(message.recipient) : getUserName(message.sender) }}
              </p>
              
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-purple-700">Secure Thread</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-indigo-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-indigo-700">Real-time</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <span
              :class="{
                'bg-gradient-to-r from-red-500 to-rose-500 text-white': message.priority === 'high',
                'bg-gradient-to-r from-yellow-500 to-orange-500 text-white': message.priority === 'normal',
                'bg-gradient-to-r from-gray-400 to-gray-500 text-white': message.priority === 'low'
              }"
              class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold shadow-lg"
            >
              {{ message.priority.toUpperCase() }} PRIORITY
            </span>
            <button
              @click="showReplyModal = true"
              class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
              </svg>
              <span class="font-semibold">Reply</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-4xl mx-auto space-y-6">
      <div class="space-y-6">
        <div 
          v-for="msg in (conversation || [message])" 
          :key="msg.id"
          class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all duration-200 hover:shadow-2xl"
          :class="{ 'ring-4 ring-purple-200 bg-gradient-to-r from-purple-50 to-indigo-50': msg.id === message.id }"
        >
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div 
                  class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg ring-2 transition-all duration-200"
                  :class="msg.sender_id === page.props.auth.user.id ? 
                    'bg-gradient-to-br from-blue-500 to-indigo-600 ring-blue-100' : 
                    'bg-gradient-to-br from-purple-500 to-indigo-600 ring-purple-100'"
                >
                  <UserIcon class="w-6 h-6 text-white" />
                </div>
                <div>
                  <h3 class="text-base font-bold text-gray-900">
                    {{ msg.sender_id === page.props.auth.user.id ? 'You' : getUserName(msg.sender) }}
                  </h3>
                  <div class="flex items-center space-x-2 mt-1">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-gray-600 font-medium">{{ formatDate(msg.created_at) }}</p>
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-3">
                <div v-if="!msg.is_read && msg.recipient_id === page.props.auth.user.id">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-sm animate-pulse">
                    UNREAD
                  </span>
                </div>
                <span
                  :class="{
                    'bg-gradient-to-r from-red-500 to-rose-500 text-white': msg.priority === 'high',
                    'bg-gradient-to-r from-yellow-500 to-orange-500 text-white': msg.priority === 'normal',
                    'bg-gradient-to-r from-gray-400 to-gray-500 text-white': msg.priority === 'low'
                  }"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm"
                >
                  {{ msg.priority.toUpperCase() }}
                </span>
              </div>
            </div>
          </div>
          
          <div class="px-8 py-6">
            <h4 class="text-lg font-bold text-gray-900 mb-4 border-l-4 border-purple-400 pl-4">{{ msg.subject }}</h4>
            <div class="prose max-w-none">
              <p class="whitespace-pre-wrap text-gray-700 leading-relaxed text-base">{{ msg.body }}</p>
            </div>
          </div>

          <div v-if="msg.read_at" class="px-8 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-t border-green-200">
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <p class="text-sm text-green-700 font-medium">
                Read on {{ formatDate(msg.read_at) }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Reply Modal -->
    <div v-if="showReplyModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-start justify-center p-4 pt-8 overflow-y-auto">
      <div class="relative w-full max-w-2xl bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all my-8">
        <div class="bg-gradient-to-r from-slate-50 via-purple-50 to-indigo-50 px-8 py-6 border-b border-gray-200 relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-16 bg-gradient-to-bl from-purple-100/40 to-transparent rounded-bl-full"></div>
          <div class="relative flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg ring-4 ring-purple-100">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-purple-900 to-indigo-900 bg-clip-text text-transparent">
                  Reply to Message
                </h3>
                <p class="text-sm text-gray-600 font-medium">Send a secure reply</p>
              </div>
            </div>
            <button @click="closeReplyModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-8">
          <form @submit.prevent="sendReply" class="space-y-6">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200">
              <div class="grid grid-cols-1 gap-4">
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-blue-700">To</label>
                  <input
                    :value="getReplyRecipientName()"
                    type="text"
                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl bg-blue-50 text-gray-700 font-medium"
                    disabled
                  />
                </div>
                
                <div class="space-y-2">
                  <label class="block text-sm font-semibold text-blue-700">Subject</label>
                  <input
                    :value="'Re: ' + message.subject"
                    type="text"
                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl bg-blue-50 text-gray-700 font-medium"
                    disabled
                  />
                </div>
              </div>
            </div>

            <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200">
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Reply <span class="text-red-500">*</span></label>
                <textarea
                  v-model="replyForm.body"
                  rows="6"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900 placeholder-gray-500 resize-none"
                  maxlength="2000"
                  placeholder="Type your reply here..."
                  required
                ></textarea>
                <div class="text-xs text-gray-500 text-right">{{ replyForm.body?.length || 0 }}/2000 characters</div>
              </div>
            </div>
          </form>
        </div>

        <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-8 py-6 border-t border-gray-200 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4">
          <button
            type="button"
            @click="closeReplyModal"
            class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md"
          >
            Cancel
          </button>
          <button
            type="submit"
            @click="sendReply"
            :disabled="replying"
            class="w-full sm:w-auto bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
          >
            <div class="flex items-center justify-center">
              <svg v-if="replying" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
              </svg>
              {{ replying ? 'Sending...' : 'Send Reply' }}
            </div>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { ArrowLeftIcon, UserIcon } from '@heroicons/vue/24/outline'
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
  replying.value = true
  
  // Debug logging
  console.log('Sending reply:', {
    url: `/client/messages/${props.message.id}/reply`,
    data: replyForm.value,
    messageId: props.message.id,
    currentUser: page.props.auth.user
  })
  
  router.post(`/client/messages/${props.message.id}/reply`, replyForm.value, {
    onSuccess: (page) => {
      console.log('Reply successful')
      closeReplyModal()
      // Refresh the current page to show the new reply
      router.reload()
    },
    onError: (errors) => {
      console.log('Reply errors:', errors)
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
  // If the current user is the sender, reply to the recipient
  // If the current user is the recipient, reply to the sender
  if (props.message.sender_id === props.message.recipient_id) {
    // Edge case: message to self
    return getUserName(props.message.sender)
  }
  
  if (props.message.sender_id === page.props.auth.user.id) {
    // Current user sent this message, so reply to the recipient
    return getUserName(props.message.recipient)
  } else {
    // Current user received this message, so reply to the sender
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