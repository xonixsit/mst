<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
          <p class="mt-1 text-sm text-gray-600">Manage your personal information and account settings</p>
        </div>
      </div>
    </template>

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Profile Information -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
          <p class="mt-1 text-sm text-gray-600">
            Update your basic account information. For detailed tax information, visit 
            <a href="/client/information" class="text-blue-600 hover:text-blue-800 underline">My Information</a> page.
          </p>
        </div>
        <div class="px-6 py-4">
          <form @submit.prevent="updateProfile" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                <input
                  id="first_name"
                  v-model="profileForm.first_name"
                  type="text"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
                <div v-if="profileForm.errors.first_name" class="mt-1 text-sm text-red-600">
                  {{ profileForm.errors.first_name }}
                </div>
              </div>
              <div>
                <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                <input
                  id="middle_name"
                  v-model="profileForm.middle_name"
                  type="text"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
                <div v-if="profileForm.errors.middle_name" class="mt-1 text-sm text-red-600">
                  {{ profileForm.errors.middle_name }}
                </div>
              </div>
              <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input
                  id="last_name"
                  v-model="profileForm.last_name"
                  type="text"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
                <div v-if="profileForm.errors.last_name" class="mt-1 text-sm text-red-600">
                  {{ profileForm.errors.last_name }}
                </div>
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">
                  Email Address
                  <span class="text-xs text-gray-500 ml-1">(Contact admin to change)</span>
                </label>
                <div class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900">
                  {{ clientData?.email }}
                </div>
                <p class="mt-1 text-xs text-gray-500">
                  Your email address is used for account login and cannot be changed here. Contact your tax professional to update this information.
                </p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">
                  Phone Number
                  <span class="text-xs text-gray-500 ml-1">(Update in Information tab)</span>
                </label>
                <div class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-900">
                  {{ clientData?.phone || 'Not provided' }}
                </div>
                <p class="mt-1 text-xs text-gray-500">
                  Phone number can be updated in the <a href="/client/information" class="text-blue-600 hover:text-blue-800 underline">My Information</a> section.
                </p>
              </div>
            </div>
            <div class="flex justify-end">
              <button
                type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                :disabled="profileForm.processing"
              >
                {{ profileForm.processing ? 'Updating...' : 'Update Profile' }}
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

      <!-- Communication Preferences -->
      <CommunicationPreferences :client="communicationPreferences" />

      <!-- Account Information -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Account Information</h2>
        </div>
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="text-sm font-medium text-gray-700">Member Since</h3>
              <p class="mt-1 text-sm text-gray-900">{{ formatDate(page.props.auth.user.created_at) }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-700">Account Status</h3>
              <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                Active
              </span>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-700">Tax Professional</h3>
              <p class="mt-1 text-sm text-gray-900">{{ assignedProfessional || 'Not assigned' }}</p>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-700">Last Login</h3>
              <p class="mt-1 text-sm text-gray-900">{{ formatDate(page.props.auth.user.last_login_at) || 'Never' }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import CommunicationPreferences from '@/Components/CommunicationPreferences.vue'

const props = defineProps({
  clientData: {
    type: Object,
    default: () => ({})
  },
  communicationPreferences: {
    type: Object,
    default: () => ({
      email_notifications_enabled: true,
      sms_notifications_enabled: false,
      preferred_communication_method: 'email',
      communication_preferences: {
        email_notifications: true,
        document_notifications: true,
        invoice_notifications: true,
        reminder_notifications: true,
        notification_frequency: 'immediate'
      }
    })
  }
})

const page = usePage()
const assignedProfessional = ref('John Smith, CPA') // This would come from props in real app

const profileForm = useForm({
  first_name: props.clientData?.first_name || '',
  middle_name: props.clientData?.middle_name || '',
  last_name: props.clientData?.last_name || ''
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const updateProfile = () => {
  profileForm.put('/client/profile', {
    preserveScroll: true,
    onSuccess: () => {
      // Profile updated successfully
    }
  })
}

const updatePassword = () => {
  passwordForm.put('/client/password', {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    }
  })
}

const formatDate = (dateString) => {
  if (!dateString) return 'Never'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>