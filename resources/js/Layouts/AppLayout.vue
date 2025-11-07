<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <div class="hidden lg:flex lg:flex-shrink-0">
      <div class="flex flex-col w-64">
        <div class="flex flex-col flex-grow bg-white border-r border-gray-200 pt-5 pb-4 overflow-y-auto">
          <!-- Logo -->
          <div class="flex items-center flex-shrink-0 px-4">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h1 class="text-xl font-bold text-gray-900">MySuperTax</h1>
            </div>
          </div>

          <!-- Navigation -->
          <nav class="mt-8 flex-1 px-2 space-y-1">
            <a
              v-for="item in navigation"
              :key="item.name"
              :href="item.href"
              :class="[
                item.current
                  ? 'bg-indigo-50 border-r-4 border-indigo-500 text-indigo-700'
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                'group flex items-center px-2 py-2 text-sm font-medium rounded-l-md transition-colors duration-200'
              ]"
            >
              <component
                :is="item.icon"
                :class="[
                  item.current ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500',
                  'mr-3 flex-shrink-0 h-5 w-5'
                ]"
              />
              {{ item.name }}
              <span
                v-if="item.badge"
                class="ml-auto inline-block py-0.5 px-2 text-xs font-medium rounded-full bg-indigo-100 text-indigo-600"
              >
                {{ item.badge }}
              </span>
            </a>
          </nav>


        </div>
      </div>
    </div>



    <!-- Main content area -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Header Bar -->
      <div class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex items-center justify-between">
          <!-- Mobile menu button (left side) -->
          <div class="lg:hidden">
            <button
              @click="showMobileMenu = !showMobileMenu"
              class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
            </button>
          </div>

          <!-- Greeting and User Menu (right side) -->
          <div class="flex items-center space-x-4 ml-auto">
            <!-- Greeting -->
            <div class="hidden sm:block">
              <p class="text-sm text-gray-600">
                {{ greeting }}, 
                <span class="font-medium text-gray-900">{{ page.props.auth.user?.name }}</span>
              </p>
            </div>

            <!-- User Menu -->
            <div class="relative">
              <button
                @click="showUserMenu = !showUserMenu"
                class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200"
              >
                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                  <span class="text-sm font-medium text-indigo-700">
                    {{ page.props.auth.user?.name?.charAt(0).toUpperCase() }}
                  </span>
                </div>
                <div class="hidden md:block text-left">
                  <p class="text-sm font-medium text-gray-700">{{ page.props.auth.user?.name }}</p>
                  <p class="text-xs text-gray-500 capitalize">{{ page.props.auth.user?.role?.replace('_', ' ') }}</p>
                </div>
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile navigation menu -->
      <div v-if="showMobileMenu" class="lg:hidden">
        <div class="bg-white border-b border-gray-200">
          <nav class="px-2 py-2 space-y-1">
            <a
              v-for="item in navigation"
              :key="item.name"
              :href="item.href"
              :class="[
                item.current
                  ? 'bg-indigo-50 text-indigo-700'
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                'group flex items-center px-2 py-2 text-base font-medium rounded-md'
              ]"
              @click="showMobileMenu = false"
            >
              <component
                :is="item.icon"
                :class="[
                  item.current ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500',
                  'mr-3 flex-shrink-0 h-6 w-6'
                ]"
              />
              {{ item.name }}
              <span
                v-if="item.badge"
                class="ml-auto inline-block py-0.5 px-2 text-xs font-medium rounded-full bg-indigo-100 text-indigo-600"
              >
                {{ item.badge }}
              </span>
            </a>
          </nav>
        </div>
      </div>

      <!-- Page Header -->
      <header v-if="$slots.header" class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
          <slot name="header" />
        </div>
      </header>

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto">
        <!-- Flash Messages -->
        <div v-if="page.props.flash?.success" class="m-4">
          <div class="bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
              <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <p class="ml-3 text-sm text-green-700">{{ page.props.flash.success }}</p>
            </div>
          </div>
        </div>

        <div v-if="page.props.flash?.error" class="m-4">
          <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
              <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <p class="ml-3 text-sm text-red-700">{{ page.props.flash.error }}</p>
            </div>
          </div>
        </div>

        <!-- Page Content -->
        <div class="p-4 sm:p-6 lg:p-8">
          <slot />
        </div>
      </main>
    </div>

    <!-- User Menu Dropdown -->
    <div 
      v-if="showUserMenu"
      class="origin-top-right absolute right-4 top-16 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
    >
      <div class="py-1">
        <a 
          v-for="item in userMenuItems" 
          :key="item.name"
          :href="item.href"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200"
          @click="item.action && item.action()"
        >
          <div class="flex items-center">
            <component :is="item.icon" class="w-4 h-4 mr-3" />
            {{ item.name }}
          </div>
        </a>
      </div>
    </div>

    <!-- Click outside to close menus -->
    <div 
      v-if="showUserMenu || showMobileMenu" 
      class="fixed inset-0 z-40" 
      @click="showUserMenu = false; showMobileMenu = false"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { 
  HomeIcon, 
  UsersIcon, 
  DocumentTextIcon, 
  CogIcon,
  UserIcon,
  ArrowRightOnRectangleIcon,
  ChartBarIcon,
  FolderIcon,
  CalendarIcon,
  BellIcon,
  CurrencyDollarIcon,
  ClipboardDocumentListIcon,
  ChatBubbleLeftRightIcon,
  ArchiveBoxIcon
} from '@heroicons/vue/24/outline'

const page = usePage()

const showUserMenu = ref(false)
const showMobileMenu = ref(false)

const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour < 12) return 'Good morning'
  if (hour < 17) return 'Good afternoon'
  return 'Good evening'
})

const navigation = computed(() => {
  const user = page.props.auth.user
  const currentPath = window.location.pathname
  
  if (user?.role === 'admin' || user?.role === 'tax_professional') {
    // Admin/Tax Professional Navigation
    return [
      { 
        name: 'Dashboard', 
        href: '/admin/dashboard', 
        icon: HomeIcon, 
        current: currentPath === '/admin/dashboard'
      },
      { 
        name: 'Clients', 
        href: '/admin/clients', 
        icon: UsersIcon, 
        current: currentPath.startsWith('/admin/clients'),
        badge: '12' // Example badge
      },
      { 
        name: 'Documents', 
        href: '/admin/documents', 
        icon: FolderIcon, 
        current: currentPath.startsWith('/admin/documents')
      },
      { 
        name: 'Reports', 
        href: '/admin/reports', 
        icon: ChartBarIcon, 
        current: currentPath.startsWith('/admin/reports')
      },
      { 
        name: 'Calendar', 
        href: '/admin/calendar', 
        icon: CalendarIcon, 
        current: currentPath.startsWith('/admin/calendar')
      },
      { 
        name: 'Invoices', 
        href: '/admin/invoices', 
        icon: CurrencyDollarIcon, 
        current: currentPath.startsWith('/admin/invoices')
      },
      { 
        name: 'Tasks', 
        href: '/admin/tasks', 
        icon: ClipboardDocumentListIcon, 
        current: currentPath.startsWith('/admin/tasks')
      },
      { 
        name: 'Audit Logs', 
        href: '/admin/audit', 
        icon: DocumentTextIcon, 
        current: currentPath.startsWith('/admin/audit')
      },
      { 
        name: 'Data Archival', 
        href: '/admin/data-archival', 
        icon: ArchiveBoxIcon, 
        current: currentPath.startsWith('/admin/data-archival')
      }
    ]
  } else {
    // Client Navigation
    return [
      { 
        name: 'Dashboard', 
        href: '/client/dashboard', 
        icon: HomeIcon, 
        current: currentPath === '/client/dashboard'
      },
      { 
        name: 'My Information', 
        href: '/client/information', 
        icon: UserIcon, 
        current: currentPath.startsWith('/client/information')
      },
      { 
        name: 'Documents', 
        href: '/client/documents', 
        icon: FolderIcon, 
        current: currentPath.startsWith('/client/documents')
      },
      { 
        name: 'Messages', 
        href: '/client/messages', 
        icon: ChatBubbleLeftRightIcon, 
        current: currentPath.startsWith('/client/messages'),
        badge: '3' // Example unread messages
      },
      { 
        name: 'Tax Returns', 
        href: '/client/tax-returns', 
        icon: DocumentTextIcon, 
        current: currentPath.startsWith('/client/tax-returns')
      },
      { 
        name: 'Invoices', 
        href: '/client/invoices', 
        icon: CurrencyDollarIcon, 
        current: currentPath.startsWith('/client/invoices')
      },
      { 
        name: 'My Profile', 
        href: '/client/profile', 
        icon: CogIcon, 
        current: currentPath === '/client/profile'
      }
    ]
  }
})

const userMenuItems = computed(() => {
  const user = page.props.auth.user
  
  if (user?.role === 'admin' || user?.role === 'tax_professional') {
    return [
      {
        name: 'Admin Profile', 
        href: '/admin/profile', 
        icon: UserIcon 
      },
      { 
        name: 'Settings', 
        href: '/admin/settings', 
        icon: CogIcon 
      },
      { 
        name: 'Notifications', 
        href: '/admin/notifications', 
        icon: BellIcon 
      },
      { 
        name: 'Sign out', 
        href: '#', 
        icon: ArrowRightOnRectangleIcon,
        action: () => {
          router.post('/admin/logout')
        }
      }
    ]
  } else {
    return [
      {
        name: 'My Profile', 
        href: '/client/profile', 
        icon: UserIcon 
      },
      { 
        name: 'Settings', 
        href: '/client/settings', 
        icon: CogIcon 
      },
      { 
        name: 'Notifications', 
        href: '/client/notifications', 
        icon: BellIcon 
      },
      { 
        name: 'Sign out', 
        href: '#', 
        icon: ArrowRightOnRectangleIcon,
        action: () => {
          router.post('/client/logout')
        }
      }
    ]
  }
})
</script>