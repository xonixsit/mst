<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8">
          <div class="text-center">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
              </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Enable Two-Factor Authentication</h1>
            <p class="text-blue-100 mt-2">Secure your account with email verification</p>
          </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-8">
          <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Email-Based Authentication</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
              When enabled, you'll receive a 6-digit verification code via email each time you log in. 
              This adds an extra layer of security to protect your account.
            </p>
          </div>

          <!-- Enable Button -->
          <form @submit.prevent="enableTwoFactor" class="space-y-6">
            <!-- Error Message -->
            <div v-if="form.errors.general" class="rounded-lg bg-red-50 border border-red-200 p-3">
              <p class="text-sm text-red-800 font-medium">{{ form.errors.general }}</p>
            </div>

            <!-- Success Message -->
            <div v-if="successMessage" class="rounded-lg bg-green-50 border border-green-200 p-3">
              <p class="text-sm text-green-800 font-medium">{{ successMessage }}</p>
            </div>

            <!-- Enable Button -->
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-semibold py-3 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl disabled:cursor-not-allowed"
            >
              {{ form.processing ? 'Enabling...' : 'Enable 2FA' }}
            </button>
          </form>

          <!-- Back Link -->
          <div class="mt-6 text-center">
            <a href="/admin/profile" class="text-sm text-gray-600 hover:text-gray-800 font-medium">
              ← Back to Profile
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  secret: String,
  qrCode: String,
})

const successMessage = ref('')

const form = useForm({})

const enableTwoFactor = () => {
  form.post('/auth/2fa/confirm', {
    onSuccess: () => {
      successMessage.value = '2FA has been enabled successfully! You will now receive email codes when logging in.'
      setTimeout(() => {
        window.location.href = '/admin/profile'
      }, 2000)
    },
    onError: (errors) => {
      form.errors.general = 'Failed to enable 2FA. Please try again.'
    }
  })
}
</script>
