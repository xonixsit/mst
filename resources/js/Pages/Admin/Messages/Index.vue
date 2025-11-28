<template>
    <AppLayout title="Messages Management">
        <template #header>
            <div class="relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-cyan-50 to-blue-50"></div>
                <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-cyan-100/40 to-transparent rounded-bl-full"></div>
                <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-blue-100/30 to-transparent rounded-tr-full"></div>
                
                <!-- Content -->
                <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
                    <div class="flex items-center space-x-4">
                        <!-- Messages Icon -->
                        <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 via-cyan-600 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-cyan-100">
                            <EnvelopeIcon class="w-8 h-8 text-white" />
                        </div>
                        
                        <!-- Title Section -->
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-cyan-900 to-blue-900 bg-clip-text text-transparent">
                                Messages Management
                            </h1>
                            <p class="mt-2 text-sm text-gray-600 font-medium">Manage client communications and messages</p>
                            
                            <!-- Status Indicators -->
                            <div class="flex items-center space-x-4 mt-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></div>
                                    <span class="text-xs font-semibold text-cyan-700">{{ stats.total_messages }} Total Messages</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                                    <span class="text-xs font-semibold text-red-700">{{ stats.unread_messages }} Unread</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <div class="flex items-center">
                        <button
                            @click="showNewMessageModal = true"
                            class="bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
                        >
                            <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="font-semibold">New Message</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Enhanced Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Messages -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-cyan-50 via-cyan-100 to-cyan-200 rounded-xl shadow-lg border border-cyan-200/50 hover:shadow-xl transition-all duration-300 group">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-cyan-300/30 to-transparent rounded-bl-full"></div>
                        <div class="relative p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-cyan-700 uppercase tracking-wide">Total Messages</p>
                                    <p class="text-3xl font-bold text-cyan-900 mt-2">{{ stats.total_messages }}</p>
                                </div>
                                <div class="p-4 bg-cyan-500 rounded-xl shadow-lg group-hover:bg-cyan-600 transition-colors duration-300">
                                    <EnvelopeIcon class="w-8 h-8 text-white" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Unread Messages -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-red-50 via-red-100 to-red-200 rounded-xl shadow-lg border border-red-200/50 hover:shadow-xl transition-all duration-300 group">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-red-300/30 to-transparent rounded-bl-full"></div>
                        <div class="relative p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-red-700 uppercase tracking-wide">Unread</p>
                                    <p class="text-3xl font-bold text-red-900 mt-2">{{ stats.unread_messages }}</p>
                                </div>
                                <div class="p-4 bg-red-500 rounded-xl shadow-lg group-hover:bg-red-600 transition-colors duration-300">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- High Priority -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 via-amber-100 to-amber-200 rounded-xl shadow-lg border border-amber-200/50 hover:shadow-xl transition-all duration-300 group">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-amber-300/30 to-transparent rounded-bl-full"></div>
                        <div class="relative p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-amber-700 uppercase tracking-wide">High Priority</p>
                                    <p class="text-3xl font-bold text-amber-900 mt-2">{{ stats.high_priority }}</p>
                                </div>
                                <div class="p-4 bg-amber-500 rounded-xl shadow-lg group-hover:bg-amber-600 transition-colors duration-300">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- From Clients -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-emerald-100 to-emerald-200 rounded-xl shadow-lg border border-emerald-200/50 hover:shadow-xl transition-all duration-300 group">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-emerald-300/30 to-transparent rounded-bl-full"></div>
                        <div class="relative p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-emerald-700 uppercase tracking-wide">From Clients</p>
                                    <p class="text-3xl font-bold text-emerald-900 mt-2">{{ stats.client_messages }}</p>
                                </div>
                                <div class="p-4 bg-emerald-500 rounded-xl shadow-lg group-hover:bg-emerald-600 transition-colors duration-300">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Filters -->
                <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Search & Filter Messages</h3>
                                <p class="text-sm text-gray-600 mt-1">Find and manage your message communications</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <EnvelopeIcon class="w-5 h-5 text-gray-400" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
                            <!-- Search -->
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Search Messages</label>
                                <div class="relative">
                                    <input
                                        v-model="filters.search"
                                        @input="applyFilters"
                                        type="text"
                                        placeholder="Subject, content, sender..."
                                        class="w-full px-4 py-3 pl-10 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200"
                                    />
                                    <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Client Filter -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Client</label>
                                <select 
                                    v-model="filters.client_id" 
                                    @change="applyFilters"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white"
                                >
                                    <option value="">All Clients</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Status</label>
                                <select 
                                    v-model="filters.status" 
                                    @change="applyFilters"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white"
                                >
                                    <option value="">All Status</option>
                                    <option value="unread">Unread</option>
                                    <option value="read">Read</option>
                                </select>
                            </div>

                            <!-- Priority Filter -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Priority</label>
                                <select 
                                    v-model="filters.priority" 
                                    @change="applyFilters"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white"
                                >
                                    <option value="">All Priorities</option>
                                    <option value="low">Low</option>
                                    <option value="normal">Normal</option>
                                    <option value="high">High</option>
                                </select>
                            </div>

                            <!-- Sender Type Filter -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Sender</label>
                                <select 
                                    v-model="filters.sender_type" 
                                    @change="applyFilters"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 bg-white"
                                >
                                    <option value="">All Senders</option>
                                    <option value="client">Clients</option>
                                    <option value="admin">Staff</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                </div></div>
                <!-- Enhanced Messages List -->
                <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Message Inbox</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ messages.total }} messages in your inbox</p>
                            </div>
                            <div class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                                <span class="text-sm font-semibold text-cyan-600">{{ messages.total }} Total</span>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Bulk Actions -->
                    <div v-if="selectedMessages.length > 0" class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <CheckIcon class="w-4 h-4 text-blue-600" />
                                </div>
                                <span class="text-sm font-semibold text-blue-700">
                                    {{ selectedMessages.length }} message{{ selectedMessages.length !== 1 ? 's' : '' }} selected
                                </span>
                            </div>
                            <div class="flex space-x-3">
                                <button
                                    @click="bulkAction('mark_read')"
                                    class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md"
                                >
                                    Mark Read
                                </button>
                                <button
                                    @click="bulkAction('delete')"
                                    class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="messages.data.length === 0" class="p-12 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-cyan-100 to-blue-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                            <EnvelopeIcon class="w-8 h-8 text-cyan-600" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No messages found</h3>
                        <p class="text-gray-500">No messages match your current filters or you have no messages yet.</p>
                    </div>

                    <div v-else class="divide-y divide-gray-100">
                        <div
                            v-for="message in messages.data"
                            :key="message?.id || Math.random()"
                            class="p-6 hover:bg-gradient-to-r hover:from-cyan-50 hover:to-blue-50 transition-all duration-200 border-l-4 group"
                            :class="{ 
                                'bg-gradient-to-r from-blue-50 to-indigo-50 border-l-blue-500': !message?.is_read,
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
                                        class="mt-2 w-4 h-4 rounded border-2 border-gray-300 text-cyan-600 focus:ring-2 focus:ring-cyan-500 focus:ring-offset-0 transition-all duration-200"
                                    >

                                    <!-- Avatar -->
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-100 to-blue-200 flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <EnvelopeIcon class="w-6 h-6 text-cyan-600" />
                                    </div>

                                    <!-- Message Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-4">
                                            <h4 class="text-lg font-bold text-gray-900 truncate">
                                                {{ message?.subject }}
                                            </h4>
                                            
                                            <!-- Badges -->
                                            <div class="flex items-center gap-2">
                                                <span
                                                    v-if="!message?.is_read"
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200 shadow-sm"
                                                >
                                                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                                                    New
                                                </span>
                                                
                                                <span
                                                    :class="{
                                                        'bg-red-100 text-red-800 border-red-200': message?.priority === 'high',
                                                        'bg-amber-100 text-amber-800 border-amber-200': message?.priority === 'normal',
                                                        'bg-gray-100 text-gray-800 border-gray-200': message?.priority === 'low'
                                                    }"
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-sm"
                                                >
                                                    {{ message?.priority ? message.priority.charAt(0).toUpperCase() + message.priority.slice(1) : 'Normal' }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Participants Info -->
                                        <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl p-4 mb-4 border border-gray-100">
                                            <div class="grid grid-cols-3 gap-4 text-sm">
                                                <div>
                                                    <span class="font-semibold text-gray-600">From:</span>
                                                    <p class="text-gray-900 mt-1 truncate font-medium">{{ message?.sender?.first_name ? `${message.sender.first_name} ${message.sender.last_name || ''}`.trim() : 'Unknown' }}</p>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-600">To:</span>
                                                    <p class="text-gray-900 mt-1 truncate font-medium">{{ message?.recipient?.first_name ? `${message.recipient.first_name} ${message.recipient.last_name || ''}`.trim() : 'Unknown' }}</p>
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-600">Client:</span>
                                                    <p class="text-gray-900 mt-1 truncate font-medium">{{ message?.client?.user?.first_name ? `${message.client.user.first_name} ${message.client.user.last_name || ''}`.trim() : 'Unknown Client' }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Message Preview -->
                                        <p class="text-sm text-gray-700 line-clamp-2 leading-relaxed mb-4 font-medium">
                                            {{ message?.body }}
                                        </p>

                                        <!-- Timestamp -->
                                        <div class="flex items-center text-xs text-gray-500 font-medium">
                                            <ClockIcon class="w-4 h-4 mr-2" />
                                            {{ formatDate(message?.created_at) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col gap-2 ml-4">
                                    <Link
                                        v-if="message?.id"
                                        :href="`/admin/messages/${message.id}`"
                                        class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center"
                                    >
                                        <EyeIcon class="w-3 h-3 mr-1.5" />
                                        View
                                    </Link>
                                    <button
                                        v-if="!message?.is_read && message?.id"
                                        @click="markAsRead(message.id)"
                                        class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center"
                                    >
                                        <CheckIcon class="w-3 h-3 mr-1.5" />
                                        Mark Read
                                    </button>
                                    <button
                                        v-if="message?.id"
                                        @click="deleteMessage(message.id)"
                                        class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-3 py-2 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center"
                                    > 
                                        <TrashIcon class="w-3 h-3 mr-1.5" />
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