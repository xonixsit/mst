<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-indigo-50 to-purple-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-indigo-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-purple-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Profile Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-indigo-100">
              <UserCircleIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-indigo-900 to-purple-900 bg-clip-text text-transparent">
                Profile Settings
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Manage your account information and preferences</p>
              
              <!-- User Info -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-indigo-700">{{ page.props.auth.user?.name }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-purple-700">{{ (page.props.auth.user?.role || '').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <!-- Enhanced Profile Information -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center mr-3">
                <UserIcon class="w-4 h-4 text-white" />
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900">Profile Information</h2>
                <p class="text-sm text-gray-600">Update your account's profile information and email address</p>
              </div>
            </div>
          </div>
          <div class="p-8">
            <form @submit.prevent="updateProfile" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label for="name" class="block text-sm font-semibold text-gray-700">Full Name</label>
                  <input
                    id="name"
                    v-model="profileForm.name"
                    type="text"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                    required
                  />
                </div>
                <div class="space-y-2">
                  <label for="email" class="block text-sm font-semibold text-gray-700">Email Address</label>
                  <input
                    id="email"
                    v-model="profileForm.email"
                    type="email"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                    required
                  />
                </div>

                <div class="space-y-2 md:col-span-2">
                  <label for="role" class="block text-sm font-semibold text-gray-700">Role</label>
                  <div class="relative">
                    <input
                      id="role"
                      :value="(page.props.auth.user?.role || '').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"
                      type="text"
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 cursor-not-allowed"
                      disabled
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                      <LockClosedIcon class="w-4 h-4 text-gray-400" />
                    </div>
                  </div>
                </div>
              </div>
              <div class="flex justify-end pt-6 border-t border-gray-200">
                <button
                  type="submit"
                  class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center"
                  :disabled="profileForm.processing"
                >
                  <CheckIcon v-if="!profileForm.processing" class="w-5 h-5 mr-2" />
                  <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ profileForm.processing ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </form>
          </div>
        </div>

      <!-- Change Password -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Change Password</h2>
          <p class="mt-1 text-sm text-gray-600">Ensure your account is using a long, random password to stay secure.</p>
        </div>
        <div class="px-6 py-4">
          <form @submit.prevent="updatePassword" class="space-y-6">
            <div>
              <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
              <input
                id="current_password"
                v-model="passwordForm.current_password"
                type="password"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required
              />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input
                  id="password"
                  v-model="passwordForm.password"
                  type="password"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
              </div>
              <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input
                  id="password_confirmation"
                  v-model="passwordForm.password_confirmation"
                  type="password"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
              </div>
            </div>
            <div class="flex justify-end">
              <button
                type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                :disabled="passwordForm.processing"
              >
                {{ passwordForm.processing ? 'Updating...' : 'Update Password' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Account Statistics -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Account Statistics</h2>
        </div>
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="text-2xl font-bold text-indigo-600">{{ stats.totalClients }}</div>
              <div class="text-sm text-gray-600">Total Clients</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ stats.activeClients }}</div>
              <div class="text-sm text-gray-600">Active Clients</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">{{ stats.completedReturns }}</div>
              <div class="text-sm text-gray-600">Completed Returns</div>
            </div>
          </div>
        </div>
      </div>
    </div></div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'


const page = usePage()

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      totalClients: 0,
      activeClients: 0,
      completedReturns: 0
    })
  }
})

const profileForm = useForm({
  name: page.props.auth.user?.name || '',
  email: page.props.auth.user?.email || ''
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const updateProfile = () => {
  profileForm.patch('/admin/profile', {
    preserveScroll: true,
    onSuccess: () => {
      // Profile updated successfully
    }
  })
}

const updatePassword = () => {
  passwordForm.put('/admin/password', {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    }
  })
}


</script>