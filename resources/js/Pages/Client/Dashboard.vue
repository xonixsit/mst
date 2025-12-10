<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-blue-50 to-indigo-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-blue-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-indigo-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row px-10 lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Client Dashboard Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"></path>
              </svg>
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent">
                Client Dashboard
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Welcome back, {{ $page.props.auth.user?.name || $page.props.auth.user?.first_name || 'User' }}</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-green-700">Account Active</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-blue-700">{{ greeting }}</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons and Date -->
          <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <div class="bg-white/80 backdrop-blur-sm px-4 py-2 rounded-xl border border-gray-200 shadow-sm">
              <div class="text-sm font-medium text-gray-600">Today</div>
              <div class="text-xs text-gray-500">
                {{ new Date().toLocaleDateString('en-US', { 
                  weekday: 'long', 
                  year: 'numeric', 
                  month: 'long', 
                  day: 'numeric' 
                }) }}
              </div>
            </div>
            
            <Link
              href="/client/information"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span class="font-semibold">Update Information</span>
            </Link>
            
            <Link
              href="/client/documents"
              class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:-translate-y-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
              </svg>
              <span class="font-semibold">Upload Documents</span>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Tax Information Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200 h-48 flex flex-col">
          <div class="p-6 flex-1 flex flex-col">
            <div class="flex items-center mb-4">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <DocumentTextIcon class="w-6 h-6 text-indigo-600" />
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">Tax Information</h3>
                <p class="text-sm text-gray-600 h-10 flex items-center">Complete your comprehensive tax details</p>
              </div>
            </div>
            <div class="mt-auto">
              <Link 
                href="/client/information" 
                class="inline-flex items-center text-indigo-600 hover:text-indigo-500 font-medium transition-colors duration-200"
              >
                Complete Info
                <ArrowRightIcon class="ml-1 w-4 h-4" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Profile Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200 h-48 flex flex-col">
          <div class="p-6 flex-1 flex flex-col">
            <div class="flex items-center mb-4">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                  <UserIcon class="w-6 h-6 text-blue-600" />
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">Profile</h3>
                <p class="text-sm text-gray-600 h-10 flex items-center">Manage your account settings</p>
              </div>
            </div>
            <div class="mt-auto">
              <a 
                href="/client/profile" 
                class="inline-flex items-center text-blue-600 hover:text-blue-500 font-medium transition-colors duration-200"
              >
                View Profile
                <ArrowRightIcon class="ml-1 w-4 h-4" />
              </a>
            </div>
          </div>
        </div>

        <!-- Documents Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200 h-48 flex flex-col">
          <div class="p-6 flex-1 flex flex-col">
            <div class="flex items-center mb-4">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                  <DocumentTextIcon class="w-6 h-6 text-green-600" />
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">Documents</h3>
                <p class="text-sm text-gray-600 h-10 flex items-center">Upload and manage tax documents</p>
              </div>
            </div>
            <div class="mt-auto">
              <Link 
                href="/client/documents" 
                class="inline-flex items-center text-green-600 hover:text-green-500 font-medium transition-colors duration-200"
              >
                View Documents
                <ArrowRightIcon class="ml-1 w-4 h-4" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Messages Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200 h-48 flex flex-col">
          <div class="p-6 flex-1 flex flex-col">
            <div class="flex items-center mb-4">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                  <ChatBubbleLeftRightIcon class="w-6 h-6 text-purple-600" />
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">Messages</h3>
                <p class="text-sm text-gray-600 h-10 flex items-center">Communicate with your tax professional</p>
              </div>
            </div>
            <div class="mt-auto">
              <Link 
                href="/client/messages" 
                class="inline-flex items-center text-purple-600 hover:text-purple-500 font-medium transition-colors duration-200"
              >
                View Messages
                <ArrowRightIcon class="ml-1 w-4 h-4" />
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { 
  UserIcon, 
  DocumentTextIcon, 
  ChatBubbleLeftRightIcon,
  ArrowRightIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'

const greeting = computed(() => {
  const hour = new Date().getHours()
  if (hour < 12) return 'Good morning'
  if (hour < 17) return 'Good afternoon'
  return 'Good evening'
})
</script>