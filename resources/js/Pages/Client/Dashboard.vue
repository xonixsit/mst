<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
          <p class="mt-1 text-sm text-gray-600">Welcome back, {{ $page.props.auth.user?.name }}</p>
        </div>
        <div class="text-sm text-gray-500">
          {{ new Date().toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
          }) }}
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Tax Information Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <DocumentTextIcon class="w-6 h-6 text-indigo-600" />
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">Tax Information</h3>
                <p class="text-sm text-gray-600">Complete your comprehensive tax details</p>
              </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
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
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                  <UserIcon class="w-6 h-6 text-blue-600" />
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">Profile</h3>
                <p class="text-sm text-gray-600">Manage your account settings</p>
              </div>
            </div>
            <div class="mt-4">
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
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                  <DocumentTextIcon class="w-6 h-6 text-green-600" />
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">Documents</h3>
                <p class="text-sm text-gray-600">Upload and manage tax documents</p>
              </div>
            </div>
            <div class="mt-4">
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
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                  <ChatBubbleLeftRightIcon class="w-6 h-6 text-purple-600" />
                </div>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">Messages</h3>
                <p class="text-sm text-gray-600">Communicate with your tax professional</p>
              </div>
            </div>
            <div class="mt-4">
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

      <!-- Status Overview -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Account Status</h2>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
              <div class="text-2xl font-bold text-indigo-600">Active</div>
              <div class="text-sm text-gray-600">Account Status</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">0</div>
              <div class="text-sm text-gray-600">Documents Uploaded</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">0</div>
              <div class="text-sm text-gray-600">Returns Completed</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600">Active</div>
              <div class="text-sm text-gray-600">Account Status</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Recent Activity</h2>
        </div>
        <div class="p-6">
          <div class="text-center py-8">
            <ClockIcon class="mx-auto w-12 h-12 text-gray-400" />
            <p class="mt-2 text-gray-600">No recent activity to display.</p>
            <p class="text-sm text-gray-500">Your activity will appear here once you start using the platform.</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { 
  UserIcon, 
  DocumentTextIcon, 
  ChatBubbleLeftRightIcon,
  ArrowRightIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'
import { Link } from '@inertiajs/vue3'
import AppLayout from '../../Layouts/AppLayout.vue'
</script>