<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
          <p class="mt-1 text-sm text-gray-600">Manage your application preferences and settings</p>
        </div>
      </div>
    </template>

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Notification Preferences -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Notification Preferences</h2>
          <p class="mt-1 text-sm text-gray-600">Choose how you want to be notified about important updates.</p>
        </div>
        <div class="px-6 py-4 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-gray-900">Email Notifications</h3>
              <p class="text-sm text-gray-600">Receive notifications via email</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                v-model="settings.emailNotifications"
                type="checkbox"
                class="sr-only peer"
                @change="updateSettings"
              />
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
          </div>
          
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-gray-900">Client Updates</h3>
              <p class="text-sm text-gray-600">Get notified when clients update their information</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                v-model="settings.clientUpdates"
                type="checkbox"
                class="sr-only peer"
                @change="updateSettings"
              />
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
          </div>

          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-gray-900">System Alerts</h3>
              <p class="text-sm text-gray-600">Important system notifications and alerts</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                v-model="settings.systemAlerts"
                type="checkbox"
                class="sr-only peer"
                @change="updateSettings"
              />
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
          </div>
        </div>
      </div>

      <!-- Display Preferences -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Display Preferences</h2>
          <p class="mt-1 text-sm text-gray-600">Customize how the application looks and feels.</p>
        </div>
        <div class="px-6 py-4 space-y-4">
          <div>
            <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
            <select
              id="timezone"
              v-model="settings.timezone"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              @change="updateSettings"
            >
              <option value="America/New_York">Eastern Time (ET)</option>
              <option value="America/Chicago">Central Time (CT)</option>
              <option value="America/Denver">Mountain Time (MT)</option>
              <option value="America/Los_Angeles">Pacific Time (PT)</option>
            </select>
          </div>

          <div>
            <label for="dateFormat" class="block text-sm font-medium text-gray-700">Date Format</label>
            <select
              id="dateFormat"
              v-model="settings.dateFormat"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              @change="updateSettings"
            >
              <option value="MM/DD/YYYY">MM/DD/YYYY</option>
              <option value="DD/MM/YYYY">DD/MM/YYYY</option>
              <option value="YYYY-MM-DD">YYYY-MM-DD</option>
            </select>
          </div>

          <div>
            <label for="itemsPerPage" class="block text-sm font-medium text-gray-700">Items per Page</label>
            <select
              id="itemsPerPage"
              v-model="settings.itemsPerPage"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
              @change="updateSettings"
            >
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Security Settings -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Security Settings</h2>
          <p class="mt-1 text-sm text-gray-600">Manage your account security preferences.</p>
        </div>
        <div class="px-6 py-4 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-gray-900">Two-Factor Authentication</h3>
              <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
            </div>
            <button
              class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
            >
              Enable 2FA
            </button>
          </div>

          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-sm font-medium text-gray-900">Session Timeout</h3>
              <p class="text-sm text-gray-600">Automatically log out after period of inactivity</p>
            </div>
            <select
              v-model="settings.sessionTimeout"
              class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
              @change="updateSettings"
            >
              <option value="30">30 minutes</option>
              <option value="60">1 hour</option>
              <option value="120">2 hours</option>
              <option value="240">4 hours</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <div class="flex justify-end">
        <button
          @click="saveSettings"
          class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          :disabled="saving"
        >
          {{ saving ? 'Saving...' : 'Save Settings' }}
        </button>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const saving = ref(false)

const settings = reactive({
  emailNotifications: true,
  clientUpdates: true,
  systemAlerts: true,
  timezone: 'America/New_York',
  dateFormat: 'MM/DD/YYYY',
  itemsPerPage: '25',
  sessionTimeout: '60'
})

const updateSettings = () => {
  // Auto-save on change
  saveSettings()
}

const saveSettings = () => {
  saving.value = true
  
  const user = $page.props.auth.user
  const baseUrl = (user?.role === 'admin' || user?.role === 'tax_professional') ? '/admin' : '/client'
  
  router.post(`${baseUrl}/settings`, settings, {
    preserveScroll: true,
    onFinish: () => {
      saving.value = false
    }
  })
}
</script>