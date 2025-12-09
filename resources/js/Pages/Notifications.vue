<template>
  <AppLayout title="Notifications">
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-emerald-50 to-green-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-emerald-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-green-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Notification Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 via-emerald-600 to-green-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-emerald-100">
              <BellIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-emerald-900 to-green-900 bg-clip-text text-transparent">
                Notifications
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Stay updated with your latest notifications</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-emerald-700">{{ notifications.length }} Total</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-blue-700">{{ notifications.filter(n => !n.read).length }} Unread</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex items-center space-x-3">
            <button
              @click="markAllAsRead"
              class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <CheckCircleIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">Mark All Read</span>
            </button>
            <button
              @click="clearAll"
              class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <TrashIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">Clear All</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-4">
        <!-- Enhanced Filter Tabs -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden mb-8">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-gray-900">Filter Notifications</h3>
                <p class="text-sm text-gray-600 mt-1">Organize your notifications by category</p>
              </div>
              <div class="flex items-center space-x-2">
                <BellIcon class="w-5 h-5 text-gray-400" />
              </div>
            </div>
          </div>
          
          <div class="p-6">
            <nav class="flex flex-wrap gap-2">
              <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="activeTab = tab.key"
                :class="[
                  activeTab === tab.key
                    ? 'bg-gradient-to-r from-emerald-500 to-green-600 text-white shadow-lg'
                    : 'bg-white text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 border border-gray-200 hover:border-emerald-200',
                  'px-4 py-2 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center space-x-2'
                ]"
              >
                <span>{{ tab.name }}</span>
                <span
                  v-if="tab.count > 0"
                  :class="[
                    activeTab === tab.key
                      ? 'bg-white/20 text-white'
                      : 'bg-emerald-100 text-emerald-700',
                    'px-2 py-0.5 rounded-full text-xs font-bold'
                  ]"
                >
                  {{ tab.count }}
                </span>
              </button>
            </nav>
          </div>
        </div>

        <!-- Enhanced Notifications List -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div v-if="filteredNotifications.length === 0" class="p-12 text-center">
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-green-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
              <BellIcon class="w-8 h-8 text-emerald-600" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No notifications found</h3>
            <p class="text-gray-500">You're all caught up! No notifications match your current filter.</p>
          </div>

          <ul v-else class="divide-y divide-gray-100">
            <li
              v-for="notification in filteredNotifications"
              :key="notification.id"
              :class="[
                'p-6 hover:bg-gradient-to-r hover:from-emerald-50 hover:to-green-50 transition-all duration-200 group',
                !notification.read ? 'bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400' : ''
              ]"
            >
              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                  <div
                    :class="[
                      'w-12 h-12 rounded-xl flex items-center justify-center shadow-lg',
                      getNotificationColor(notification.type)
                    ]"
                  >
                    <component :is="getNotificationIcon(notification.type)" class="w-6 h-6" />
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="text-lg font-bold text-gray-900">
                      {{ notification.title }}
                    </h4>
                    <div class="flex items-center space-x-3">
                      <span class="text-sm font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                        {{ formatDate(notification.created_at) }}
                      </span>
                      <div v-if="!notification.read" class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                    </div>
                  </div>
                  <p class="text-sm text-gray-700 leading-relaxed mb-3">
                    {{ notification.message }}
                  </p>
                  <div v-if="notification.action_url" class="flex items-center justify-between">
                    <a
                      :href="notification.action_url"
                      class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 flex items-center"
                    >
                      <span>{{ notification.action_text || 'View Details' }}</span>
                      <ArrowRightIcon class="w-4 h-4 ml-2" />
                    </a>
                  </div>
                </div>
                <div class="flex-shrink-0">
                  <button
                    @click="toggleRead(notification)"
                    :class="[
                      'p-2 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500',
                      !notification.read 
                        ? 'text-blue-600 hover:bg-blue-100 hover:text-blue-700' 
                        : 'text-gray-400 hover:bg-gray-100 hover:text-gray-600'
                    ]"
                  >
                    <CheckCircleIcon v-if="!notification.read" class="w-5 h-5" />
                    <XMarkIcon v-else class="w-5 h-5" />
                  </button>
                </div>
              </div>
            </li>
          </ul>
      </div>

        </div>

        <!-- Enhanced Load More -->
        <div v-if="hasMore" class="mt-8 text-center">
          <button
            @click="loadMore"
            class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center mx-auto"
            :disabled="loading"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ loading ? 'Loading...' : 'Load More Notifications' }}</span>
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
  InformationCircleIcon,
  TrashIcon,
  ArrowRightIcon,
  XMarkIcon
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
    client_update: 'bg-gradient-to-br from-blue-100 to-indigo-200 text-blue-600',
    system: 'bg-gradient-to-br from-gray-100 to-slate-200 text-gray-600',
    success: 'bg-gradient-to-br from-green-100 to-emerald-200 text-green-600',
    warning: 'bg-gradient-to-br from-yellow-100 to-amber-200 text-yellow-600',
    document: 'bg-gradient-to-br from-purple-100 to-violet-200 text-purple-600',
    default: 'bg-gradient-to-br from-gray-100 to-slate-200 text-gray-600'
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