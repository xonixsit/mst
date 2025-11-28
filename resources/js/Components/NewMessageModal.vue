<template>
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">New Message</h3>
                    <button
                        @click="$emit('close')"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="sendMessage" class="space-y-4">
                    <!-- Client Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                        <select 
                            v-model="form.client_id" 
                            @change="updateRecipient"
                            required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900"
                        >
                            <option value="">Select client</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                {{ client.name }} ({{ client.email }})
                            </option>
                        </select>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input
                            v-model="form.subject"
                            type="text"
                            required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 placeholder-gray-500"
                            placeholder="Enter message subject"
                        >
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select 
                            v-model="form.priority"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900"
                        >
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <!-- Message Body -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea
                            v-model="form.body"
                            rows="6"
                            required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 placeholder-gray-500"
                            placeholder="Enter your message"
                        ></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3 pt-4">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="processing"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                        >
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