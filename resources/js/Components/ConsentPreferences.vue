<template>
  <div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Privacy & Consent Preferences</h2>

    <!-- Current Preferences -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Consent Preferences</h3>
      <div class="space-y-4">
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
          <div>
            <p class="font-medium text-gray-900">Essential Cookies</p>
            <p class="text-sm text-gray-600">Required for platform functionality and security</p>
          </div>
          <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Always On</span>
        </div>

        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
          <div>
            <p class="font-medium text-gray-900">Analytics</p>
            <p class="text-sm text-gray-600">Help us understand how you use our platform</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input 
              type="checkbox" 
              v-model="preferences.analytics"
              class="sr-only peer"
            />
            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
          </label>
        </div>

        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
          <div>
            <p class="font-medium text-gray-900">Marketing</p>
            <p class="text-sm text-gray-600">Personalized content and promotional offers</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input 
              type="checkbox" 
              v-model="preferences.marketing"
              class="sr-only peer"
            />
            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
          </label>
        </div>
      </div>
    </div>

    <!-- Consent History -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Consent History</h3>
      <div v-if="history.length > 0" class="space-y-2 max-h-64 overflow-y-auto">
        <div v-for="log in history" :key="log.id" class="p-3 bg-gray-50 rounded text-sm">
          <div class="flex justify-between items-start">
            <div>
              <p class="font-medium text-gray-900 capitalize">{{ log.action }}</p>
              <p class="text-gray-600 text-xs">{{ formatDate(log.created_at) }}</p>
              <p class="text-gray-500 text-xs">Source: {{ log.source }}</p>
            </div>
            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">{{ log.action }}</span>
          </div>
        </div>
      </div>
      <p v-else class="text-gray-600 text-sm">No consent history available</p>
    </div>

    <!-- Actions -->
    <div class="flex gap-4">
      <button
        @click="savePreferences"
        :disabled="saving"
        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg font-medium transition-colors"
      >
        {{ saving ? 'Saving...' : 'Save Preferences' }}
      </button>
      <button
        @click="withdrawAllConsents"
        class="px-6 py-2 border border-red-300 text-red-600 hover:bg-red-50 rounded-lg font-medium transition-colors"
      >
        Withdraw All Consents
      </button>
    </div>

    <!-- Success Message -->
    <div v-if="successMessage" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 text-sm">
      {{ successMessage }}
    </div>

    <!-- Error Message -->
    <div v-if="errorMessage" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
      {{ errorMessage }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const preferences = ref({
  analytics: false,
  marketing: false,
})
const history = ref([])
const saving = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

onMounted(() => {
  loadPreferences()
  loadHistory()
})

const loadPreferences = async () => {
  try {
    const response = await fetch('/api/consent')
    const data = await response.json()
    preferences.value = {
      analytics: data.consents.analytics || false,
      marketing: data.consents.marketing || false,
    }
  } catch (error) {
    console.error('Error loading preferences:', error)
    errorMessage.value = 'Failed to load preferences'
  }
}

const loadHistory = async () => {
  try {
    const response = await fetch('/api/consent/history')
    const data = await response.json()
    history.value = data.history
  } catch (error) {
    console.error('Error loading history:', error)
  }
}

const savePreferences = async () => {
  saving.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await fetch('/api/consent/update', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content,
      },
      body: JSON.stringify({
        consents: [
          { type: 'analytics', given: preferences.value.analytics },
          { type: 'marketing', given: preferences.value.marketing },
        ],
      }),
    })

    if (response.ok) {
      successMessage.value = 'Preferences saved successfully'
      loadHistory()
      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    } else {
      errorMessage.value = 'Failed to save preferences'
    }
  } catch (error) {
    console.error('Error saving preferences:', error)
    errorMessage.value = 'An error occurred while saving preferences'
  } finally {
    saving.value = false
  }
}

const withdrawAllConsents = async () => {
  if (!confirm('Are you sure you want to withdraw all consents? This action cannot be undone.')) {
    return
  }

  try {
    const response = await fetch('/api/consent/withdraw', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content,
      },
      body: JSON.stringify({
        consent_type: 'all',
      }),
    })

    if (response.ok) {
      preferences.value = {
        analytics: false,
        marketing: false,
      }
      successMessage.value = 'All consents have been withdrawn'
      loadHistory()
    }
  } catch (error) {
    console.error('Error withdrawing consents:', error)
    errorMessage.value = 'Failed to withdraw consents'
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString()
}
</script>
