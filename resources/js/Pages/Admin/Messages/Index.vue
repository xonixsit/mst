<template>
    <AppLayout title="Messages Management">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Messages Management
                </h2>
                <button
                    @click="showNewMessageModal = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium"
                >
                    New Message
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">📧</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Messages</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.total_messages }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">🔴</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Unread</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.unread_messages }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">⚡</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">High Priority</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.high_priority }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">👥</span>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">From Clients</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.client_messages }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <!-- Search -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input
                                    v-model="filters.search"
                                    @input="applyFilters"
                                    type="text"
                                    placeholder="Search subject or content..."
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            <!-- Client Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                                <select 
                                    v-model="filters.client_id" 
                                    @change="applyFilters"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Clients</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select 
                                    v-model="filters.status" 
                                    @change="applyFilters"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All</option>
                                    <option value="unread">Unread</option>
                                    <option value="read">Read</option>
                                </select>
                            </div>

                            <!-- Priority Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                <select 
                                    v-model="filters.priority" 
                                    @change="applyFilters"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All</option>
                                    <option value="low">Low</option>
                                    <option value="normal">Normal</option>
                                    <option value="high">High</option>
                                </select>
                            </div>

                            <!-- Sender Type Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sender</label>
                                <select 
                                    v-model="filters.sender_type" 
                                    @change="applyFilters"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All</option>
                                    <option value="client">Clients</option>
                                    <option value="admin">Staff</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                </div></div>
                <!-- Messages List -->
                <div class="bg-white shadow rounded-lg">
                    <!-- Bulk Actions -->
                    <div v-if="selectedMessages.length > 0" class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">
                                {{ selectedMessages.length }} message(s) selected
                            </span>
                            <div class="flex gap-2">
                                <button
                                    @click="bulkAction('mark_read')"
                                    class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700"
                                >
                                    Mark Read
                                </button>
                                <button
                                    @click="bulkAction('delete')"
                                    class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="messages.data.length === 0" class="p-6 text-center text-gray-500">
                        <div class="text-6xl mb-4">📭</div>
                        <h3 class="text-lg font-medium mb-2">No messages found</h3>
                        <p>No messages match your current filters.</p>
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div
                            v-for="message in messages.data"
                            :key="message?.id || Math.random()"
                            class="p-6 hover:bg-gray-50 transition-all duration-200 border-l-4"
                            :class="{ 
                                'bg-blue-50 border-l-blue-500': !message?.is_read,
                                'border-l-transparent': message?.is_read
                            }"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-4 flex-1">
                                    <!-- Checkbox -->
                                    <input
                                        v-model="selectedMessages"
                                        :value="message?.id"
                                        type="checkbox"
                                        class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    >

                                    <!-- Avatar -->
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                        <EnvelopeIcon class="w-5 h-5 text-white" />
                                    </div>

                                    <!-- Message Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-3">
                                            <h4 class="text-lg font-semibold text-gray-900 truncate">
                                                {{ message?.subject }}
                                            </h4>
                                            
                                            <!-- Badges -->
                                            <div class="flex items-center gap-2">
                                                <span
                                                    v-if="!message?.is_read"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 border border-blue-200"
                                                >
                                                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1.5"></div>
                                                    New
                                                </span>
                                                
                                                <span
                                                    :class="{
                                                        'bg-red-50 text-red-700 border-red-200': message?.priority === 'high',
                                                        'bg-amber-50 text-amber-700 border-amber-200': message?.priority === 'normal',
                                                        'bg-gray-50 text-gray-700 border-gray-200': message?.priority === 'low'
                                                    }"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                                >
                                                    {{ message?.priority ? message.priority.charAt(0).toUpperCase() + message.priority.slice(1) : 'Normal' }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Participants Info -->
                                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                                            <div class="grid grid-cols-3 gap-4 text-sm">
                                                <div>
                                                    <span class="font-medium text-gray-600">From:</span>
                                                    <p class="text-gray-900 mt-0.5 truncate">{{ message?.sender?.first_name ? `${message.sender.first_name} ${message.sender.last_name || ''}`.trim() : 'Unknown' }}</p>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-600">To:</span>
                                                    <p class="text-gray-900 mt-0.5 truncate">{{ message?.recipient?.first_name ? `${message.recipient.first_name} ${message.recipient.last_name || ''}`.trim() : 'Unknown' }}</p>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-600">Client:</span>
                                                    <p class="text-gray-900 mt-0.5 truncate">{{ message?.client?.user?.first_name ? `${message.client.user.first_name} ${message.client.user.last_name || ''}`.trim() : 'Unknown Client' }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Message Preview -->
                                        <p class="text-sm text-gray-700 line-clamp-2 leading-relaxed mb-3">
                                            {{ message?.body }}
                                        </p>

                                        <!-- Timestamp -->
                                        <div class="flex items-center text-xs text-gray-500">
                                            <ClockIcon class="w-4 h-4 mr-1.5" />
                                            {{ formatDate(message?.created_at) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col gap-2 ml-4">
                                    <Link
                                        v-if="message?.id"
                                        :href="`/admin/messages/${message.id}`"
                                        class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-100 transition-colors duration-200"
                                    >
                                        <EyeIcon class="w-4 h-4 mr-1.5" />
                                        View
                                    </Link>
                                    <button
                                        v-if="!message?.is_read && message?.id"
                                        @click="markAsRead(message.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 text-sm font-medium rounded-lg hover:bg-green-100 transition-colors duration-200"
                                    >
                                        <CheckIcon class="w-4 h-4 mr-1.5" />
                                        Mark Read
                                    </button>
                                    <button
                                        v-if="message?.id"
                                        @click="deleteMessage(message.id)"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors duration-200"
                                    > 
                                        <TrashIcon class="w-4 h-4 mr-1.5" />
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="messages.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ messages.from }} to {{ messages.to }} of {{ messages.total }} messages
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    v-if="messages.prev_page_url"
                                    :href="messages.prev_page_url"
                                    class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                                >
                                    Previous
                                </Link>
                                <Link
                                    v-if="messages.next_page_url"
                                    :href="messages.next_page_url"
                                    class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                                >
                                    Next
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

        <!-- New Message Modal -->
        <NewMessageModal
            v-if="showNewMessageModal"
            :clients="clients"
            @close="showNewMessageModal = false"
        />
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { 
    EnvelopeIcon, 
    EyeIcon,
    TrashIcon,
    CheckIcon,
    ClockIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'
import NewMessageModal from '@/Components/NewMessageModal.vue'

const props = defineProps({
    messages: Object,
    clients: Array,
    filters: Object,
    stats: Object
})

const showNewMessageModal = ref(false)
const selectedMessages = ref([])

const filters = reactive({
    search: props.filters.search || '',
    client_id: props.filters.client_id || '',
    status: props.filters.status || '',
    priority: props.filters.priority || '',
    sender_type: props.filters.sender_type || ''
})

const applyFilters = () => {
    router.get('/admin/messages', filters, {
        preserveState: true,
        replace: true
    })
}

const markAsRead = (messageId) => {
    if (!messageId) {
        console.error('Message ID is required')
        return
    }
    router.post(`/admin/messages/${messageId}/mark-read`, {}, {
        preserveScroll: true
    })
}

const deleteMessage = (messageId) => {
    if (!messageId) {
        console.error('Message ID is required')
        return
    }
    if (confirm('Are you sure you want to delete this message?')) {
        router.delete(`/admin/messages/${messageId}`, {
            preserveScroll: true
        })
    }
}

const bulkAction = (action) => {
    if (selectedMessages.value.length === 0) return

    const confirmMessage = action === 'delete' 
        ? 'Are you sure you want to delete the selected messages?' 
        : `Are you sure you want to ${action.replace('_', ' ')} the selected messages?`

    if (confirm(confirmMessage)) {
        router.post('/admin/messages/bulk-action', {
            action: action,
            message_ids: selectedMessages.value
        }, {
            preserveScroll: true,
            onSuccess: () => {
                selectedMessages.value = []
            }
        })
    }
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffInHours = (now - date) / (1000 * 60 * 60)
    
    if (diffInHours < 1) {
        const diffInMinutes = Math.floor((now - date) / (1000 * 60))
        return `${diffInMinutes} minute${diffInMinutes !== 1 ? 's' : ''} ago`
    } else if (diffInHours < 24) {
        const hours = Math.floor(diffInHours)
        return `${hours} hour${hours !== 1 ? 's' : ''} ago`
    } else if (diffInHours < 168) { // 7 days
        const days = Math.floor(diffInHours / 24)
        return `${days} day${days !== 1 ? 's' : ''} ago`
    } else {
        return date.toLocaleDateString()
    }
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>