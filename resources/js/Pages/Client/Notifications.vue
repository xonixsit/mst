<template>
    <AppLayout title="Notifications">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Notifications
                <span v-if="unreadCount > 0" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ unreadCount }} unread
                </span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-wrap gap-4">
                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select 
                                    v-model="filters.status" 
                                    @change="applyFilters"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All</option>
                                    <option value="unread">Unread</option>
                                    <option value="read">Read</option>
                                </select>
                            </div>

                            <!-- Type Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select 
                                    v-model="filters.type" 
                                    @change="applyFilters"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Types</option>
                                    <option v-for="(label, key) in notificationTypes" :key="key" :value="key">
                                        {{ label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-end gap-2">
                                <button
                                    v-if="unreadCount > 0"
                                    @click="markAllAsRead"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    Mark All Read
                                </button>
                                <button
                                    @click="deleteAll"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                >
                                    Delete All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="notifications.data.length === 0" class="p-6 text-center text-gray-500">
                        <div class="text-6xl mb-4">🔔</div>
                        <h3 class="text-lg font-medium mb-2">No notifications</h3>
                        <p>You're all caught up! No notifications to display.</p>
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div
                            v-for="notification in notifications.data"
                            :key="notification.id"
                            class="p-6 hover:bg-gray-50 transition-colors duration-150"
                            :class="{ 'bg-blue-50': !notification.read_at }"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <!-- Notification Icon -->
                                        <div class="flex-shrink-0">
                                            <div 
                                                class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-medium"
                                                :class="getNotificationIconClass(notification.type)"
                                            >
                                                {{ getNotificationIcon(notification.type) }}
                                            </div>
                                        </div>

                                        <!-- Notification Title -->
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">
                                                {{ getNotificationTitle(notification) }}
                                            </h4>
                                            <p class="text-xs text-gray-500">
                                                {{ formatDate(notification.created_at) }}
                                            </p>
                                        </div>

                                        <!-- Unread Indicator -->
                                        <div v-if="!notification.read_at" class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                    </div>

                                    <!-- Notification Content -->
                                    <div class="ml-11">
                                        <p class="text-sm text-gray-700">
                                            {{ getNotificationMessage(notification) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2 ml-4">
                                    <button
                                        v-if="!notification.read_at"
                                        @click="markAsRead(notification.id)"
                                        class="text-blue-600 hover:text-blue-800 text-sm"
                                        title="Mark as read"
                                    >
                                        Mark Read
                                    </button>
                                    <button
                                        @click="deleteNotification(notification.id)"
                                        class="text-red-600 hover:text-red-800 text-sm"
                                        title="Delete notification"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="notifications.last_page > 1" class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ notifications.from }} to {{ notifications.to }} of {{ notifications.total }} notifications
                            </div>
                            <div class="flex gap-2">
                                <Link
                                    v-if="notifications.prev_page_url"
                                    :href="notifications.prev_page_url"
                                    class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                                >
                                    Previous
                                </Link>
                                <Link
                                    v-if="notifications.next_page_url"
                                    :href="notifications.next_page_url"
                                    class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                                >
                                    Next
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    notifications: Object,
    filters: Object,
    unreadCount: Number,
    notificationTypes: Object
})

const filters = reactive({
    status: props.filters.status || '',
    type: props.filters.type || ''
})

const applyFilters = () => {
    router.get(route('client.notifications.index'), filters, {
        preserveState: true,
        replace: true
    })
}

const markAsRead = (notificationId) => {
    router.post(route('client.notifications.mark-read', notificationId), {}, {
        preserveScroll: true
    })
}

const markAllAsRead = () => {
    router.post(route('client.notifications.mark-all-read'), {}, {
        preserveScroll: true
    })
}

const deleteNotification = (notificationId) => {
    if (confirm('Are you sure you want to delete this notification?')) {
        router.delete(route('client.notifications.destroy', notificationId), {
            preserveScroll: true
        })
    }
}

const deleteAll = () => {
    if (confirm('Are you sure you want to delete all notifications? This action cannot be undone.')) {
        router.delete(route('client.notifications.destroy-all'), {
            preserveScroll: true
        })
    }
}

const getNotificationIcon = (type) => {
    const icons = {
        'DocumentApproved': '✓',
        'DocumentRejected': '✗',
        'InvoiceCreated': '$',
        'InvoicePaid': '💰',
        'ClientWelcome': '👋',
        'ClientReviewRequest': '📝',
        'MessageReceived': '💬'
    }
    
    const notificationType = type.split('\\').pop()
    return icons[notificationType] || '🔔'
}

const getNotificationIconClass = (type) => {
    const classes = {
        'DocumentApproved': 'bg-green-500',
        'DocumentRejected': 'bg-red-500',
        'InvoiceCreated': 'bg-blue-500',
        'InvoicePaid': 'bg-green-500',
        'ClientWelcome': 'bg-purple-500',
        'ClientReviewRequest': 'bg-yellow-500',
        'MessageReceived': 'bg-indigo-500'
    }
    
    const notificationType = type.split('\\').pop()
    return classes[notificationType] || 'bg-gray-500'
}

const getNotificationTitle = (notification) => {
    const data = JSON.parse(notification.data)
    return data.title || data.subject || 'Notification'
}

const getNotificationMessage = (notification) => {
    const data = JSON.parse(notification.data)
    return data.message || data.body || 'You have a new notification'
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