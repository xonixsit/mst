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

    <div class="space-y-8">
      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Documents Uploaded -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-blue-100 text-sm font-medium">Documents Uploaded</p>
              <p class="text-3xl font-bold">12</p>
              <p class="text-blue-200 text-xs mt-1">+3 this month</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
              <DocumentArrowUpIcon class="w-6 h-6 text-white" />
            </div>
          </div>
        </div>

        <!-- Messages Exchanged -->
        <div class="bg-gradient-to-r from-emerald-500 to-green-600 rounded-2xl p-6 text-white shadow-xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-emerald-100 text-sm font-medium">Messages</p>
              <p class="text-3xl font-bold">8</p>
              <p class="text-emerald-200 text-xs mt-1">2 unread</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
              <ChatBubbleLeftRightIcon class="w-6 h-6 text-white" />
            </div>
          </div>
        </div>

        <!-- Account Status -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl p-6 text-white shadow-xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-purple-100 text-sm font-medium">Account Status</p>
              <p class="text-3xl font-bold">Active</p>
              <p class="text-purple-200 text-xs mt-1">All systems ready</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Quick Actions Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Tax Information Card -->
        <div class="group relative bg-gradient-to-br from-indigo-50 via-white to-indigo-50 overflow-hidden shadow-xl rounded-2xl border border-indigo-100 hover:shadow-2xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1">
          <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-indigo-200/30 to-transparent rounded-bl-full"></div>
          <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-indigo-100/40 to-transparent rounded-tr-full"></div>
          
          <div class="relative p-6 h-48 flex flex-col">
            <div class="flex items-start justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg ring-4 ring-indigo-100 group-hover:ring-indigo-200 transition-all duration-300">
                <DocumentTextIcon class="w-6 h-6 text-white" />
              </div>
              <div class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></div>
            </div>
            
            <div class="flex-1">
              <h3 class="text-lg font-bold text-gray-900 mb-2">Tax Information</h3>
              <p class="text-sm text-gray-600 leading-relaxed">Complete your comprehensive tax details and personal information</p>
            </div>
            
            <div class="mt-auto pt-4">
              <Link 
                href="/client/information" 
                class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-semibold transition-all duration-200 group-hover:translate-x-1"
              >
                Complete Info
                <ArrowRightIcon class="ml-2 w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Profile Card -->
        <div class="group relative bg-gradient-to-br from-blue-50 via-white to-cyan-50 overflow-hidden shadow-xl rounded-2xl border border-blue-100 hover:shadow-2xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1">
          <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-blue-200/30 to-transparent rounded-bl-full"></div>
          <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-cyan-100/40 to-transparent rounded-tr-full"></div>
          
          <div class="relative p-6 h-48 flex flex-col">
            <div class="flex items-start justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg ring-4 ring-blue-100 group-hover:ring-blue-200 transition-all duration-300">
                <UserIcon class="w-6 h-6 text-white" />
              </div>
              <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
            </div>
            
            <div class="flex-1">
              <h3 class="text-lg font-bold text-gray-900 mb-2">Profile</h3>
              <p class="text-sm text-gray-600 leading-relaxed">Manage your account settings and preferences</p>
            </div>
            
            <div class="mt-auto pt-4">
              <a 
                href="/client/profile" 
                class="inline-flex items-center text-blue-600 hover:text-blue-700 font-semibold transition-all duration-200 group-hover:translate-x-1"
              >
                View Profile
                <ArrowRightIcon class="ml-2 w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
              </a>
            </div>
          </div>
        </div>

        <!-- Documents Card -->
        <div class="group relative bg-gradient-to-br from-emerald-50 via-white to-green-50 overflow-hidden shadow-xl rounded-2xl border border-emerald-100 hover:shadow-2xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1">
          <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-emerald-200/30 to-transparent rounded-bl-full"></div>
          <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-green-100/40 to-transparent rounded-tr-full"></div>
          
          <div class="relative p-6 h-48 flex flex-col">
            <div class="flex items-start justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg ring-4 ring-emerald-100 group-hover:ring-emerald-200 transition-all duration-300">
                <DocumentArrowUpIcon class="w-6 h-6 text-white" />
              </div>
              <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
            </div>
            
            <div class="flex-1">
              <h3 class="text-lg font-bold text-gray-900 mb-2">Documents</h3>
              <p class="text-sm text-gray-600 leading-relaxed">Upload and manage your tax documents securely</p>
            </div>
            
            <div class="mt-auto pt-4">
              <Link 
                href="/client/documents" 
                class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold transition-all duration-200 group-hover:translate-x-1"
              >
                View Documents
                <ArrowRightIcon class="ml-2 w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
              </Link>
            </div>
          </div>
        </div>

        <!-- Messages Card -->
        <div class="group relative bg-gradient-to-br from-purple-50 via-white to-indigo-50 overflow-hidden shadow-xl rounded-2xl border border-purple-100 hover:shadow-2xl transition-all duration-300 transform hover:scale-105 hover:-translate-y-1">
          <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-purple-200/30 to-transparent rounded-bl-full"></div>
          <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-indigo-100/40 to-transparent rounded-tr-full"></div>
          
          <div class="relative p-6 h-48 flex flex-col">
            <div class="flex items-start justify-between mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg ring-4 ring-purple-100 group-hover:ring-purple-200 transition-all duration-300">
                <ChatBubbleLeftRightIcon class="w-6 h-6 text-white" />
              </div>
              <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
            </div>
            
            <div class="flex-1">
              <h3 class="text-lg font-bold text-gray-900 mb-2">Messages</h3>
              <p class="text-sm text-gray-600 leading-relaxed">Communicate with your tax professional securely</p>
            </div>
            
            <div class="mt-auto pt-4">
              <Link 
                href="/client/messages" 
                class="inline-flex items-center text-purple-600 hover:text-purple-700 font-semibold transition-all duration-200 group-hover:translate-x-1"
              >
                View Messages
                <ArrowRightIcon class="ml-2 w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" />
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity Section -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gradient-to-br from-gray-500 to-slate-600 rounded-lg flex items-center justify-center">
                <ClockIcon class="w-4 h-4 text-white" />
              </div>
              <h3 class="text-lg font-bold text-gray-900">Recent Activity</h3>
            </div>
            <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Last 7 days</span>
          </div>
        </div>
        
        <div class="p-6">
          <div class="space-y-4">
            <!-- Activity Item 1 -->
            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <DocumentArrowUpIcon class="w-5 h-5 text-white" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">Document uploaded successfully</p>
                <p class="text-xs text-gray-600 mt-1">W-2 Form for 2023 tax year</p>
                <p class="text-xs text-blue-600 mt-1">2 hours ago</p>
              </div>
            </div>

            <!-- Activity Item 2 -->
            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl border border-emerald-100">
              <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <ChatBubbleLeftRightIcon class="w-5 h-5 text-white" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">New message received</p>
                <p class="text-xs text-gray-600 mt-1">Your tax professional sent you an update</p>
                <p class="text-xs text-emerald-600 mt-1">1 day ago</p>
              </div>
            </div>

            <!-- Activity Item 3 -->
            <div class="flex items-start space-x-4 p-4 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-100">
              <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <UserIcon class="w-5 h-5 text-white" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900">Profile information updated</p>
                <p class="text-xs text-gray-600 mt-1">Contact details and preferences saved</p>
                <p class="text-xs text-purple-600 mt-1">3 days ago</p>
              </div>
            </div>
          </div>
          
          <div class="mt-6 text-center">
            <Link 
              href="/client/activity" 
              class="inline-flex items-center text-gray-600 hover:text-gray-700 font-medium transition-colors duration-200"
            >
              View all activity
              <ArrowRightIcon class="ml-1 w-4 h-4" />
            </Link>
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
  DocumentArrowUpIcon,
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