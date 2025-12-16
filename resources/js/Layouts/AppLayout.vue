<template>
  <Head>
    <meta name="user-id" :content="page.props.auth.user?.id?.toString()" />
  </Head>
  <div class="h-screen bg-gradient-to-br from-neutral-50 to-neutral-100 flex overflow-hidden">
    <!-- Sidebar -->
    <div :class="[
      'hidden lg:flex lg:flex-shrink-0 transition-all duration-300',
      (sidebarCollapsed && !sidebarHovered) ? 'lg:w-16' : 'lg:w-64'
    ]"
      @mouseenter="sidebarHovered = true"
      @mouseleave="sidebarHovered = false">
      <div :class="[
        'flex flex-col h-screen transition-all duration-300',
        (sidebarCollapsed && !sidebarHovered) ? 'w-16' : 'w-64'
      ]">
        <div :class="[
          'flex flex-col flex-grow relative pt-6 pb-4 overflow-y-auto overflow-x-hidden shadow-xl backdrop-blur-sm transition-all duration-300',
          sidebarCollapsed ? 'scrollbar-none' : 'scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-transparent'
        ]"
        style="background: linear-gradient(to bottom right, rgb(30, 41, 59), rgb(15, 23, 42), rgb(17, 24, 39));">
          <!-- Subtle Pattern for Texture -->
          <div class="absolute inset-0 opacity-5"
            style="background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.4) 1px, transparent 0); background-size: 24px 24px;">
          </div>

          <!-- Theme Accent Overlays -->
          <div
            class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-bl from-primary-600/20 to-transparent rounded-bl-full pointer-events-none">
          </div>
          <div
            class="absolute bottom-0 left-0 w-40 h-40 bg-gradient-to-tr from-secondary-600/15 to-transparent rounded-tr-full pointer-events-none">
          </div>

          <!-- Border and Shadow -->
          <div
            class="absolute inset-y-0 right-0 w-px bg-gradient-to-b from-transparent via-neutral-200/60 to-transparent pointer-events-none">
          </div>

          <!-- Content Container -->
          <div class="relative z-10 flex flex-col flex-grow">
            <!-- Logo -->
            <div :class="[
              'flex items-center flex-shrink-0 mb-10 transition-all duration-300',
              sidebarCollapsed ? 'px-3 justify-center' : 'px-6'
            ]">
              <div :class="[
                'flex items-center transition-all duration-300',
                (!sidebarCollapsed || sidebarHovered) ? 'space-x-3' : 'space-x-0'
              ]">
                <div
                  class="w-10 h-10 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 rounded-xl flex items-center justify-center shadow-lg ring-2 ring-white/20 flex-shrink-0">
                  <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path
                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <div :class="[
                  'transition-all duration-300 min-w-0 overflow-hidden',
                  (!sidebarCollapsed || sidebarHovered) ? 'opacity-100 w-auto' : 'opacity-0 w-0'
                ]">
                  <h1 class="text-xl font-bold text-white drop-shadow-sm whitespace-nowrap">MySuperTax</h1>
                  <p class="text-xs text-gray-300 font-medium whitespace-nowrap">Professional Services</p>
                </div>
              </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 space-y-2">
              <a v-for="item in navigation" :key="item.name" :href="item.href" :class="[
                item.current
                  ? 'bg-white/15 text-white shadow-md border-l-4 border-primary-400'
                  : 'text-gray-300 hover:bg-white/10 hover:text-white hover:shadow-md',
                'group flex items-center text-sm font-medium rounded-lg transition-all duration-300 ease-in-out transform hover:scale-[1.02]',
                sidebarCollapsed ? 'px-2 py-3 justify-center' : 'px-4 py-3'
              ]" :title="sidebarCollapsed ? item.name : ''">
                <div :class="[
                item.current ? 'bg-white/20 text-white' : 'bg-white/10 text-gray-400 group-hover:bg-white/20 group-hover:text-white',
                'p-2 rounded-lg transition-all duration-300',
                sidebarCollapsed ? 'mr-0' : 'mr-3'
              ]">
                  <component :is="item.icon" class="h-5 w-5" />
                </div>
                <span :class="[
                  'flex-1 ml-3 transition-all duration-300 overflow-hidden whitespace-nowrap',
                  (!sidebarCollapsed || sidebarHovered) ? 'opacity-100 w-auto' : 'opacity-0 w-0'
                ]">{{ item.name }}</span>
                <span v-if="item.badge && !sidebarCollapsed"
                  class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-red-500 text-white shadow-sm animate-pulse">
                  {{ item.badge }}
                </span>
              </a>
            </nav>
            
            <!-- Toggle Button -->
            <!-- <div class="px-3 pb-4">
              <button
                @click="sidebarCollapsed = true; sidebarHovered = false"
                :class="[
                  'w-full flex items-center text-gray-300 hover:bg-white/10 hover:text-white text-sm font-medium rounded-lg transition-all duration-300',
                  (sidebarCollapsed && !sidebarHovered) ? 'px-2 py-3 justify-center' : 'px-4 py-3'
                ]"
                :title="sidebarCollapsed ? 'Collapse sidebar' : 'Collapse sidebar'"
              >
                <div class="bg-white/10 hover:bg-white/20 p-2 rounded-lg transition-all duration-300">
                  <svg v-if="!sidebarCollapsed" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                  </svg>
                  <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                  </svg>
                </div>
                <span v-if="!sidebarCollapsed" class="ml-3">Collapse</span>
              </button>
            </div> -->
          </div>
        </div>
      </div>
    </div>
    <!-- Main content area -->
    <div class="flex-1 flex flex-col min-h-0">
      <!-- Top Header Bar -->
      <div
        class="bg-gradient-to-r from-slate-700 via-slate-800 to-slate-700 border-b border-slate-600/50 flex-shrink-0 shadow-xl">
        <div class="flex items-center justify-between">
          <!-- Mobile menu button (left side) -->
          <div class="lg:hidden">
            <button @click="showMobileMenu = !showMobileMenu"
              class="p-2.5 rounded-xl text-neutral-500 hover:text-primary-600 hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 shadow-sm">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
            </button>
          </div>

          <!-- Greeting and User Menu (right side) -->
          <div class="flex items-center space-x-6 ml-auto">
            <!-- <div class="hidden sm:block">
              <div class="text-right">
                <p class="text-sm font-medium text-neutral-800">
                  {{ greeting }},
                  <span class="text-primary-700 font-semibold">{{ page.props.auth.user?.name }}</span>
                </p>
                <p class="text-xs text-neutral-500 capitalize">{{ page.props.auth.user?.role?.replace('_', ' ') }}</p>
              </div>
            </div> -->

            <!-- Notifications -->
            <!-- <div class="relative">
              <button
                class="p-2.5 rounded-xl text-neutral-500 hover:text-primary-600 hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                <BellIcon class="h-5 w-5" />
                <span
                  class="absolute -top-1 -right-1 h-4 w-4 bg-error-500 text-white text-xs rounded-full flex items-center justify-center font-semibold">3</span>
              </button>
            </div> -->

            <!-- User Menu -->
            <div class="relative">
              <button @click="showUserMenu = !showUserMenu"
                class="flex items-center space-x-3 p-2 rounded-xl hover:bg-gradient-to-r focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md">
                <div
                  class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary-500 via-primary-600 to-secondary-500 flex items-center justify-center shadow-lg ring-2 ring-primary-100">
                  <span class="text-sm font-bold text-white">
                    {{ page.props.auth.user?.name?.charAt(0).toUpperCase() }}
                  </span>
                </div>
                <div class="hidden md:block text-left">
                  <p class="text-sm font-semibold text-white">{{ page.props.auth.user?.name }}</p>
                  <p class="text-xs text-neutral-500 capitalize flex items-center">
                    <span class="w-2 h-2 bg-success-400 rounded-full mr-2"></span>
                    {{ page.props.auth.user?.role?.replace('_', ' ') }}
                  </p>
                </div>
                <svg class="h-4 w-4 text-neutral-400 transition-transform duration-200"
                  :class="{ 'rotate-180': showUserMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Mobile navigation menu -->
        <div v-if="showMobileMenu" class="lg:hidden flex-shrink-0">
          <div
            class="bg-gradient-to-b from-white to-neutral-50 border-b border-neutral-200/60 shadow-lg backdrop-blur-sm">
            <nav class="px-4 py-4 space-y-2">
              <a v-for="item in navigation" :key="item.name" :href="item.href" :class="[
                item.current
                  ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg'
                  : 'text-neutral-700 hover:bg-gradient-to-r hover:from-primary-50 hover:to-secondary-50 hover:text-primary-700',
                'group flex items-center px-4 py-3 text-base font-medium rounded-xl transition-all duration-300 transform hover:scale-[1.02]'
              ]" @click="showMobileMenu = false">
                <div :class="[
                item.current ? 'bg-white/20 text-white' : 'bg-neutral-100 text-neutral-500 group-hover:bg-primary-100 group-hover:text-primary-600',
                'p-2 rounded-lg mr-3 transition-all duration-300'
              ]">
                  <component :is="item.icon" class="h-6 w-6" />
                </div>
                <span class="flex-1">{{ item.name }}</span>
                <span v-if="item.badge"
                  class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-error-500 text-white shadow-sm">
                  {{ item.badge }}
                </span>
              </a>
            </nav>
          </div>
        </div>

        <!-- Page Header -->
        <header v-if="$slots.header"
          class="">
            <slot name="header" />
        </header>
   </div>
        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto min-h-0">
          <!-- Flash Messages -->
          <div v-if="page.props.flash?.success" class="m-4">
            <div class="bg-success-50 border border-success-200 rounded-md p-4 shadow-sm">
              <div class="flex">
                <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-3 text-sm text-success-700 font-medium">{{ page.props.flash.success }}</p>
              </div>
            </div>
          </div>

          <div v-if="page.props.flash?.error" class="m-4">
            <div class="bg-error-50 border border-error-200 rounded-md p-4 shadow-sm">
              <div class="flex">
                <svg class="w-5 h-5 text-error-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-3 text-sm text-error-700 font-medium">{{ page.props.flash.error }}</p>
              </div>
            </div>
          </div>

          <!-- Page Content -->
          <div class="p-4 sm:p-6 lg:p-8 bg-white">
            <slot />
             
          </div>
        </main>
   

      <!-- User Menu Dropdown -->
      <div v-if="showUserMenu"
        class="origin-top-right absolute right-4 top-20 w-56 rounded-xl shadow-xl bg-white ring-1 ring-neutral-200/60 z-50 backdrop-blur-sm border border-neutral-100">
        <div class="py-2">
          <div class="px-4 py-3 border-b border-neutral-100">
            <p class="text-sm font-semibold text-neutral-800">{{ page.props.auth.user?.name }}</p>
            <p class="text-xs text-neutral-500 capitalize">{{ page.props.auth.user?.role?.replace('_', ' ') }}</p>
          </div>
          <div class="py-2">
            <a v-for="item in userMenuItems" :key="item.name" :href="item.href"
              class="flex items-center px-4 py-3 text-sm text-neutral-700 hover:bg-gradient-to-r hover:from-primary-50 hover:to-secondary-50 hover:text-primary-700 transition-all duration-200 group"
              @click="item.action && item.action()">
              <div
                class="p-2 rounded-lg bg-neutral-100 text-neutral-500 group-hover:bg-primary-100 group-hover:text-primary-600 transition-all duration-200 mr-3">
                <component :is="item.icon" class="w-4 h-4" />
              </div>
              <span class="font-medium">{{ item.name }}</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Click outside to close menus -->
      <div v-if="showUserMenu || showMobileMenu" class="fixed inset-0 z-40"
        @click="showUserMenu = false; showMobileMenu = false"></div>
    </div>
  </div>
  
  <!-- Consent Banner -->
  <ConsentBanner />
</template>

<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { Head } from '@inertiajs/vue3'
  import ConsentBanner from '@/Components/ConsentBanner.vue'
  
  // Sidebar toggle state - collapsed by default
  const sidebarCollapsed = ref(true)
  const sidebarHovered = ref(false)
  import { router, usePage, Link } from '@inertiajs/vue3'
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
    ArchiveBoxIcon,
    BuildingOfficeIcon,
    DocumentCheckIcon,
    CalculatorIcon,
    PresentationChartLineIcon,
    InboxIcon,
    ShieldCheckIcon,
    UserCircleIcon,
    UserGroupIcon,
    Cog6ToothIcon
  } from '@heroicons/vue/24/outline'
  import Footer from '@/Components/Footer.vue'
  import NavLink from '@/Components/NavLink.vue'
  import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
  import Dropdown from '@/Components/Dropdown.vue'
  import DropdownLink from '@/Components/DropdownLink.vue'

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
          icon: PresentationChartLineIcon,
          current: currentPath === '/admin/dashboard'
        },
        {
          name: 'Clients',
          href: '/admin/clients',
          icon: BuildingOfficeIcon,
          current: currentPath.startsWith('/admin/clients'),
          badge: '' // Example badge
        },
        {
          name: 'Documents',
          href: '/admin/documents',
          icon: DocumentCheckIcon,
          current: currentPath.startsWith('/admin/documents')
        },
        {
          name: 'Messages',
          href: '/admin/messages',
          icon: InboxIcon,
          current: currentPath.startsWith('/admin/messages'),
          badge: page.props.navigationCounts?.unread_messages > 0 ? page.props.navigationCounts.unread_messages.toString() : null
        },
        {
          name: 'Services',
          href: '/admin/services',
          icon: CogIcon,
          current: currentPath.startsWith('/admin/services')
        },
        {
          name: 'Reports',
          href: '/admin/reports',
          icon: ChartBarIcon,
          current: currentPath.startsWith('/admin/reports')
        },
        {
          name: 'Invoices',
          href: '/admin/invoices',
          icon: CalculatorIcon,
          current: currentPath.startsWith('/admin/invoices')
        },
        {
          name: 'Support Tickets',
          href: '/admin/support-tickets',
          icon: ChatBubbleLeftRightIcon,
          current: currentPath.startsWith('/admin/support-tickets')
        },
        {
          name: 'Contact Queries',
          href: '/admin/contact-queries',
          icon: InboxIcon,
          current: currentPath.startsWith('/admin/contact-queries')
        },
        {
          name: 'Leads',
          href: '/admin/leads',
          icon: UsersIcon,
          current: currentPath.startsWith('/admin/leads')
        },
        ...(user?.role === 'admin' ? [{
          name: 'Tax Professionals',
          href: '/admin/tax-professionals',
          icon: UsersIcon,
          current: currentPath.startsWith('/admin/tax-professionals')
        }] : [])
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
          icon: UserCircleIcon,
          current: currentPath.startsWith('/client/information')
        },
        {
          name: 'Documents',
          href: '/client/documents',
          icon: DocumentCheckIcon,
          current: currentPath.startsWith('/client/documents')
        },
        {
          name: 'Messages',
          href: '/client/messages',
          icon: InboxIcon,
          current: currentPath.startsWith('/client/messages'),
          badge: (page.props.navigationCounts?.unread_messages || 0) > 0 ? (page.props.navigationCounts.unread_messages || 0).toString() : null
        },
        {
          name: 'Services',
          href: '/client/services',
          icon: CogIcon,
          current: currentPath.startsWith('/client/services')
        },

        {
          name: 'Invoices',
          href: '/client/invoices',
          icon: CalculatorIcon,
          current: currentPath.startsWith('/client/invoices')
        },
        {
          name: 'Support',
          href: '/client/support-tickets',
          icon: ChatBubbleLeftRightIcon,
          current: currentPath.startsWith('/client/support-tickets')
        },
        {
          name: 'My Profile',
          href: '/client/profile',
          icon: Cog6ToothIcon,
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
          name: user?.role === 'tax_professional' ? 'Tax Professional Profile' : 'Admin Profile',
          href: '/admin/profile',
          icon: UserCircleIcon
        },
        {
          name: 'Settings',
          href: '/admin/settings',
          icon: Cog6ToothIcon
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
          icon: UserCircleIcon
        },
        {
          name: 'Settings',
          href: '/client/settings',
          icon: Cog6ToothIcon
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
<style
 scoped>
.scrollbar-none {
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* Internet Explorer 10+ */
}

.scrollbar-none::-webkit-scrollbar {
  display: none; /* WebKit */
}

.scrollbar-thin {
  scrollbar-width: thin;
}

.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-thumb-gray-600::-webkit-scrollbar-thumb {
  background-color: rgba(75, 85, 99, 0.6);
  border-radius: 3px;
}

.scrollbar-track-transparent::-webkit-scrollbar-track {
  background: transparent;
}
</style>