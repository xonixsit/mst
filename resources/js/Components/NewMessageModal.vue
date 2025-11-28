<template>
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all">
            <!-- Enhanced Header -->
            <div class="bg-gradient-to-r from-slate-50 via-cyan-50 to-blue-50 px-6 py-5 border-b border-gray-200 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-16 bg-gradient-to-bl from-cyan-100/40 to-transparent rounded-bl-full"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <EnvelopeIcon class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 via-cyan-900 to-blue-900 bg-clip-text text-transparent">New Message</h3>
                            <p class="text-sm text-gray-600 font-medium">Send a message to your client</p>
                        </div>
                    </div>
                    <button
                        @click="$emit('close')"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200"
                    >
                        <XMarkIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <!-- Enhanced Form -->
            <div class="p-8">
                <form @submit.prevent="sendMessage" class="space-y-6">
                    <!-- Client Selection -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Client</label>
                        <select 
                            v-model="form.client_id" 
                            @change="updateRecipient"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white text-gray-900"
                        >
                            <option value="">Select client</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                {{ client.name }} ({{ client.email }})
                            </option>
                        </select>
                    </div>

                    <!-- Subject -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Subject</label>
                        <input
                            v-model="form.subject"
                            type="text"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 text-gray-900 placeholder-gray-500"
                            placeholder="Enter message subject"
                        >
                    </div>

                    <!-- Priority -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Priority</label>
                        <select 
                            v-model="form.priority"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white text-gray-900"
                        >
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <!-- Message Body -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Message</label>
                        <textarea
                            v-model="form.body"
                            rows="6"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 text-gray-900 placeholder-gray-500 resize-none"
                            placeholder="Enter your message"
                        ></textarea>
                    </div>

                    <!-- Enhanced Actions -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="bg-gradient-to-r from-gray-500 to-slate-600 hover:from-gray-600 hover:to-slate-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center"
                        >
                            <XMarkIcon class="w-4 h-4 mr-2" />
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="processing"
                            class="bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center"
                        >
                            <PaperAirplaneIcon v-if="!processing" class="w-4 h-4 mr-2" />
                            <svg v-else class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="processing">Sending...</span>
                            <span v-else>Send Message</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { 
    EnvelopeIcon, 
    XMarkIcon, 
    PaperAirplaneIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
    clients: Array
})

const emit = defineEmits(['close'])

const processing = ref(false)

const form = reactive({
    client_id: '',
    recipient_id: '',
    subject: '',
    body: '',
    priority: 'normal'
})

const updateRecipient = () => {
    // Find the selected client and get their user ID
    const selectedClient = props.clients.find(client => client.id == form.client_id)
    if (selectedClient) {
        // We need to get the user_id for this client from the backend
        // For now, we'll let the backend handle this mapping
        form.recipient_id = selectedClient.id
    }
}

const sendMessage = () => {
    processing.value = true
    
    router.post('/admin/messages', form, {
        onSuccess: () => {
            emit('close')
        },
        onError: () => {
            processing.value = false
        },
        onFinish: () => {
            processing.value = false
        }
    })
}
</script>