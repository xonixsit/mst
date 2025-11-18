<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <button
            @click="$inertia.visit('/admin/messages')"
            class="text-gray-500 hover:text-gray-700"
          >
            <ArrowLeftIcon class="w-5 h-5" />
          </button>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ message.subject }}</h1>
            <p class="mt-1 text-sm text-gray-600">
              {{ message.sender_id === page.props.auth.user.id ? 'To' : 'From' }}: 
              {{ message.sender_id === page.props.auth.user.id ? getUserName(message.recipient) : getUserName(message.sender) }}
            </p>
            <p class="text-sm text-gray-500">
              Client: {{ message.client?.user ? getUserName(message.client.user) : 'Unknown' }}
            </p>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <span
            :class="{
              'bg-red-100 text-red-800': message.priority === 'high',
              'bg-yellow-100 text-yellow-800': message.priority === 'normal',
              'bg-gray-100 text-gray-800': message.priority === 'low'
            }"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
          >
            {{ message.priority.charAt(0).toUpperCase() + message.priority.slice(1) }} Priority
          </span>
          <button
            @click="showReplyModal = true"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            Reply
          </button>
        </div>
      </div>
    </template>

    <div class="max-w-4xl mx-auto">
      <!-- Conversation History -->
      <div class="space-y-4">
        <div 
          v-for="msg in (conversation || [message])" 
          :key="msg.id"
          class="bg-white shadow rounded-lg"
          :class="{ 'ring-2 ring-blue-500': msg.id === message.id }"
        >
          <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4">
                <div 
                  class="w-10 h-10 rounded-full flex items-center justify-center"
                  :class="msg.sender_id === page.props.auth.user.id ? 'bg-blue-100' : 'bg-purple-100'"
                >
                  <UserIcon 
                    class="w-6 h-6"
                    :class="msg.sender_id === page.props.auth.user.id ? 'text-blue-600' : 'text-purple-600'"
                  />
                </div>
                <div>
                  <h3 class="text-sm font-medium text-gray-900">
                    {{ msg.sender_id === page.props.auth.user.id ? 'You' : getUserName(msg.sender) }}
                  </h3>
                  <p class="text-sm text-gray-500">{{ formatDate(msg.created_at) }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <div v-if="!msg.is_read && msg.recipient_id === page.props.auth.user.id">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Unread
                  </span>
                </div>
                <span
                  :class="{
                    'bg-red-100 text-red-800': msg.priority === 'high',
                    'bg-yellow-100 text-yellow-800': msg.priority === 'normal',
                    'bg-gray-100 text-gray-800': msg.priority === 'low'
                  }"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                >
                  {{ msg.priority }}
                </span>
              </div>
            </div>
          </div>
          
          <div class="px-6 py-6">
            <h4 class="text-sm font-medium text-gray-900 mb-2">{{ msg.subject }}</h4>
            <div class="prose max-w-none">
              <p class="whitespace-pre-wrap text-gray-700">{{ msg.body }}</p>
            </div>
          </div>

          <div v-if="msg.read_at" class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            <p class="text-xs text-gray-500">
              Read on {{ formatDate(msg.read_at) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Reply Modal -->
    <div v-if="showReplyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Reply to Message</h3>
          <form @submit.prevent="sendReply" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
              <input
                :value="getReplyRecipientName()"
                type="text"
                class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50"
                disabled
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
              <input
                :value="'Re: ' + message.subject"
                type="text"
                class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50"
                disabled
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Reply</label>
              <textarea
                v-model="replyForm.body"
                rows="5"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                maxlength="2000"
                required
                placeholder="Type your reply here..."
              ></textarea>

            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeReplyModal"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="replying"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
              >
                {{ replying ? 'Sending...' : 'Send Reply' }}
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
  if (!replyForm.value.body || !replyForm.value.body.trim()) {
    alert('Please enter a reply message')
    return
  }
  
  replying.value = true
  
  router.post(`/admin/messages/${props.message.id}/reply`, {
    body: replyForm.value.body.trim()
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
  if (!user) return 'Unknown'
  
  if (user.name) return user.name
  
  const nameParts = []
  if (user.first_name) nameParts.push(user.first_name)
  if (user.middle_name) nameParts.push(user.middle_name)
  if (user.last_name) nameParts.push(user.last_name)
  
  if (nameParts.length > 0) {
    return nameParts.join(' ')
  }
  
  return user.email || `User ${user.id}` || 'Unknown'
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