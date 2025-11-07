<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
          <p class="mt-1 text-sm text-gray-600">Stay updated with your latest notifications</p>
        </div>
        <div class="flex space-x-3">
          <button
            @click="markAllAsRead"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
          >
            Mark All as Read
          </button>
          <button
            @click="clearAll"
            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm"
          >
            Clear All
          </button>
        </div>
      </div>
    </template>

    <div class="max-w-4xl mx-auto">
      <!-- Filter Tabs -->
      <div class="mb-6">
        <nav class="flex space-x-8">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            :class="[
              activeTab === tab.key
                ? 'border-indigo-500 text-indigo-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
            ]"
          >
            {{ tab.name }}
            <span
              v-if="tab.count > 0"
              :class="[
                activeTab === tab.key
                  ? 'bg-indigo-100 text-indigo-600'
                  : 'bg-gray-100 text-gray-900',
                'ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium'
              ]"
            >
              {{ tab.count }}
            </span>
          </button>
        </nav>
      </div>

      <!-- Notifications List -->
      <div class="bg-white shadow rounded-lg">
        <div v-if="filteredNotifications.length === 0" class="p-8 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-12"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
          <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
        </div>

        <ul v-else class="divide-y divide-gray-200">
          <li
            v-for="notification in filteredNotifications"
            :key="notification.id"
            :class="[
              'p-4 hover:bg-gray-50 transition-colors duration-200',
              !notification.read ? 'bg-blue-50' : ''
            ]"
          >
            <div class="flex items-start space-x-3">
              <div class="flex-shrink-0">
                <div
                  :class="[
                    'w-8 h-8 rounded-full flex items-center justify-center',
                    getNotificationColor(notification.type)
                  ]"
                >
                  <component :is="getNotificationIcon(notification.type)" class="w-4 h-4" />
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <p class="text-sm font-medium text-gray-900">
                    {{ notification.title }}
                  </p>
                  <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500">
                      {{ formatDate(notification.created_at) }}
                    </span>
                    <div v-if="!notification.read" class="w-2 h-2 bg-blue-600 rounded-full"></div>
                  </div>
                </div>
                <p class="mt-1 text-sm text-gray-600">
                  {{ notification.message }}
                </p>
                <div v-if="notification.action_url" class="mt-2">
                  <a
                    :href="notification.action_url"
                    class="text-sm text-indigo-600 hover:text-indigo-500"
                  >
                    {{ notification.action_text || 'View Details' }} →
                  </a>
                </div>
              </div>
              <div class="flex-shrink-0">
                <button
                  @click="toggleRead(notification)"
                  class="text-gray-400 hover:text-gray-600 focus:outline-none"
                >
                  <svg v-if="!notification.read" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>
          </li>
        </ul>
      </div>

      <!-- Load More -->
      <div v-if="hasMore" class="mt-6 text-center">
        <button
          @click="loadMore"
          class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          :disabled="loading"
        >
          {{ loading ? 'Loading...' : 'Load More' }}
        </button>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
  BellIcon,
  UserIcon,
  DocumentTextIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  InformationCircleIcon
} from '@heroicons/vue/24/outline'

const activeTab = ref('all')
const loading = ref(false)
const hasMore = ref(true)

// Mock notifications data - in real app this would come from props
const notifications = ref([
  {
    id: 1,
    type: 'client_update',
    title: 'Client Information Updated',
    message: 'John Doe has updated their personal information.',
    read: false,
    created_at: '2024-01-15T10:30:00Z',
    action_url: '/admin/clients/1',
    action_text: 'View Client'
  },
  {
    id: 2,
    type: 'system',
    title: 'System Maintenance Scheduled',
    message: 'Scheduled maintenance will occur on January 20th from 2-4 AM EST.',
    read: false,
    created_at: '2024-01-14T15:45:00Z'
  },
  {
    id: 3,
    type: 'success',
    title: 'Tax Return Completed',
    message: 'Tax return for Jane Smith has been successfully processed.',
    read: true,
    created_at: '2024-01-13T09:15:00Z',
    action_url: '/admin/clients/2/tax-returns',
    action_text: 'View Return'
  },
  {
    id: 4,
    type: 'warning',
    title: 'Document Missing',
    message: 'W-2 form is missing for client Michael Johnson.',
    read: true,
    created_at: '2024-01-12T14:20:00Z',
    action_url: '/admin/clients/3/documents'
  }
])

const tabs = computed(() => [
  { key: 'all', name: 'All', count: notifications.value.length },
  { key: 'unread', name: 'Unread', count: notifications.value.filter(n => !n.read).length },
  { key: 'client_update', name: 'Client Updates', count: notifications.value.filter(n => n.type === 'client_update').length },
  { key: 'system', name: 'System', count: notifications.value.filter(n => n.type === 'system').length }
])

const filteredNotifications = computed(() => {
  if (activeTab.value === 'all') return notifications.value
  if (activeTab.value === 'unread') return notifications.value.filter(n => !n.read)
  return notifications.value.filter(n => n.type === activeTab.value)
})

const getNotificationIcon = (type) => {
  const icons = {
    client_update: UserIcon,
    system: InformationCircleIcon,
    success: CheckCircleIcon,
    warning: ExclamationTriangleIcon,
    document: DocumentTextIcon,
    default: BellIcon
  }
  return icons[type] || icons.default
}

const getNotificationColor = (type) => {
  const colors = {
    client_update: 'bg-blue-100 text-blue-600',
    system: 'bg-gray-100 text-gray-600',
    success: 'bg-green-100 text-green-600',
    warning: 'bg-yellow-100 text-yellow-600',
    document: 'bg-purple-100 text-purple-600',
    default: 'bg-gray-100 text-gray-600'
  }
  return colors[type] || colors.default
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))
  
  if (diffInHours < 1) return 'Just now'
  if (diffInHours < 24) return `${diffInHours}h ago`
  if (diffInHours < 48) return 'Yesterday'
  return date.toLocaleDateString()
}

const toggleRead = (notification) => {
  notification.read = !notification.read
  
  const user = $page.props.auth.user
  const baseUrl = (user?.role === 'admin' || user?.role === 'tax_professional') ? '/admin' : '/client'
  
  router.patch(`${baseUrl}/notifications/${notification.id}/toggle-read`, {}, {
    preserveScroll: true
  })
}

const markAllAsRead = () => {
  notifications.value.forEach(n => n.read = true)
  
  const user = $page.props.auth.user
  const baseUrl = (user?.role === 'admin' || user?.role === 'tax_professional') ? '/admin' : '/client'
  
  router.post(`${baseUrl}/notifications/mark-all-read`, {}, {
    preserveScroll: true
  })
}

const clearAll = () => {
  if (confirm('Are you sure you want to clear all notifications?')) {
    notifications.value = []
    
    const user = $page.props.auth.user
    const baseUrl = (user?.role === 'admin' || user?.role === 'tax_professional') ? '/admin' : '/client'
    
    router.delete(`${baseUrl}/notifications/clear-all`, {
      preserveScroll: true
    })
  }
}

const loadMore = () => {
  loading.value = true
  // In real app, make API call to load more notifications
  setTimeout(() => {
    loading.value = false
    hasMore.value = false
  }, 1000)
}
</script>