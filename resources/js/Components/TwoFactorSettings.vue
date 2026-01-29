<template>
  <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900">Two-Factor Authentication</h3>
            <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
          </div>
        </div>
        <div v-if="user.two_factor_enabled" class="flex items-center space-x-2 px-3 py-1.5 rounded-full bg-green-100 border border-green-200">
          <span class="w-2 h-2 bg-green-500 rounded-full"></span>
          <span class="text-xs font-semibold text-green-800">Enabled</span>
        </div>
        <div v-else class="flex items-center space-x-2 px-3 py-1.5 rounded-full bg-gray-100 border border-gray-200">
          <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
          <span class="text-xs font-semibold text-gray-800">Disabled</span>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="px-6 py-6">
      <div v-if="!user.two_factor_enabled" class="space-y-4">
        <p class="text-gray-600">
          Two-factor authentication adds an extra layer of security to your account. In addition to your password, you'll need to enter a code from your phone to sign in.
        </p>
        <button
          @click="enableTwoFactor"
          :disabled="enablingTwoFactor"
          class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 text-white rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl disabled:cursor-not-allowed"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          {{ enablingTwoFactor ? 'Enabling...' : 'Enable 2FA' }}
        </button>
      </div>

      <div v-else class="space-y-6">
        <!-- Status -->
        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
          <p class="text-sm text-green-900">
            <strong>✓ Two-factor authentication is enabled</strong>
          </p>
          <p class="text-sm text-green-800 mt-1">
            Your account is protected with an additional layer of security.
          </p>
        </div>

        <!-- Backup Codes -->
        <div class="border-t border-gray-200 pt-6">
          <h4 class="text-sm font-semibold text-gray-900 mb-3">Backup Codes</h4>
          <p class="text-sm text-gray-600 mb-4">
            Save these codes in a safe place. You can use them to access your account if you lose your authenticator device.
          </p>
          <button
            @click="showBackupCodes"
            :disabled="loadingBackupCodes"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors disabled:opacity-50"
          >
            {{ loadingBackupCodes ? 'Loading...' : 'View Backup Codes' }}
          </button>

          <!-- Backup Codes Display -->
          <div v-if="backupCodesVisible" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-2 gap-2 font-mono text-sm mb-4">
              <div v-for="code in backupCodes" :key="code" class="p-2 bg-white rounded border border-gray-200">
                {{ code }}
              </div>
            </div>
            <button
              @click="copyBackupCodes"
              class="text-sm text-blue-600 hover:text-blue-800 font-medium"
            >
              {{ copiedBackupCodes ? '✓ Copied!' : 'Copy all codes' }}
            </button>
          </div>
        </div>

        <!-- Regenerate Codes -->
        <div class="border-t border-gray-200 pt-6">
          <h4 class="text-sm font-semibold text-gray-900 mb-3">Regenerate Backup Codes</h4>
          <p class="text-sm text-gray-600 mb-4">
            Generate new backup codes. Your old codes will no longer work.
          </p>
          <button
            @click="regenerateBackupCodes"
            :disabled="regeneratingCodes"
            class="px-4 py-2 border border-amber-300 text-amber-700 rounded-lg font-semibold hover:bg-amber-50 transition-colors disabled:opacity-50"
          >
            {{ regeneratingCodes ? 'Regenerating...' : 'Regenerate Codes' }}
          </button>
        </div>

        <!-- Disable 2FA -->
        <div class="border-t border-gray-200 pt-6">
          <h4 class="text-sm font-semibold text-gray-900 mb-3">Disable 2FA</h4>
          <p class="text-sm text-gray-600 mb-4">
            Remove two-factor authentication from your account.
          </p>
          <button
            @click="showDisableConfirm = true"
            class="px-4 py-2 border border-red-300 text-red-700 rounded-lg font-semibold hover:bg-red-50 transition-colors"
          >
            Disable 2FA
          </button>

          <!-- Disable Confirmation -->
          <div v-if="showDisableConfirm" class="mt-4 p-4 bg-red-50 rounded-lg border border-red-200">
            <p class="text-sm text-red-900 font-medium mb-4">
              Are you sure? This will remove two-factor authentication from your account.
            </p>
            <form @submit.prevent="disableTwoFactor" class="space-y-4">
              <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                  Enter your password to confirm:
                </label>
                <input
                  v-model="disableForm.password"
                  type="password"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                />
                <div v-if="disableForm.errors.password" class="text-sm text-red-600 mt-1">
                  {{ disableForm.errors.password }}
                </div>
              </div>
              <div class="flex gap-2">
                <button
                  type="button"
                  @click="showDisableConfirm = false"
                  class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="disableForm.processing"
                  class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-300 text-white rounded-lg font-semibold transition-colors"
                >
                  {{ disableForm.processing ? 'Disabling...' : 'Disable 2FA' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  user: Object,
})

// Expose route function to template
// const route = window.route

const backupCodes = ref([])
const backupCodesVisible = ref(false)
const loadingBackupCodes = ref(false)
const copiedBackupCodes = ref(false)
const regeneratingCodes = ref(false)
const showDisableConfirm = ref(false)
const enablingTwoFactor = ref(false)

const disableForm = useForm({
  password: '',
})

const enableTwoFactor = async () => {
  enablingTwoFactor.value = true
  try {
    const response = await axios.post('/auth/2fa/confirm')
    // Update the user prop to reflect the change
    props.user.two_factor_enabled = true
    // Show success message or refresh page
    window.location.reload()
  } catch (error) {
    console.error('Error enabling 2FA:', error)
  } finally {
    enablingTwoFactor.value = false
  }
}

const showBackupCodes = async () => {
  loadingBackupCodes.value = true
  try {
    const response = await axios.get('/auth/2fa/backup-codes')
    backupCodes.value = response.data.codes
    backupCodesVisible.value = true
  } catch (error) {
    console.error('Error loading backup codes:', error)
  } finally {
    loadingBackupCodes.value = false
  }
}

const copyBackupCodes = () => {
  const text = backupCodes.value.join('\n')
  navigator.clipboard.writeText(text)
  copiedBackupCodes.value = true
  setTimeout(() => {
    copiedBackupCodes.value = false
  }, 2000)
}

const regenerateBackupCodes = async () => {
  if (!confirm('Are you sure? Your old backup codes will no longer work.')) {
    return
  }

  regeneratingCodes.value = true
  try {
    const response = await axios.post('/auth/2fa/backup-codes/regenerate', {
      password: prompt('Enter your password to confirm:'),
    })
    backupCodes.value = response.data.codes
    backupCodesVisible.value = true
  } catch (error) {
    console.error('Error regenerating backup codes:', error)
  } finally {
    regeneratingCodes.value = false
  }
}

const disableTwoFactor = () => {
  disableForm.post('/auth/2fa/disable', {
    onSuccess: () => {
      showDisableConfirm.value = false
      disableForm.reset()
    },
  })
}
</script>
