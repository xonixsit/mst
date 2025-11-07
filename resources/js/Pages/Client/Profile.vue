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
          <p class="mt-1 text-sm text-gray-600">Update your account's profile information and email address.</p>
        </div>
        <div class="px-6 py-4">
          <form @submit.prevent="updateProfile" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input
                  id="name"
                  v-model="profileForm.name"
                  type="text"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
              </div>
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input
                  id="email"
                  v-model="profileForm.email"
                  type="email"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  required
                />
              </div>
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input
                  id="phone"
                  v-model="profileForm.phone"
                  type="tel"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
              <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Account Type</label>
                <input
                  id="role"
                  value="Client"
                  type="text"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50"
                  disabled
                />
              </div>
            </div>
            <div class="flex justify-end">
              <button
                type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                :disabled="profileForm.processing"
              >
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

      <!-- Communication Preferences -->
      <CommunicationPreferences :client="clientData" />

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
  name: page.props.auth.user.name,
  email: page.props.auth.user.email,
  phone: page.props.auth.user.phone || ''
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const updateProfile = () => {
  profileForm.patch('/client/profile', {
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