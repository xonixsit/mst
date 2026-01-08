<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-2xl">
      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8">
          <h1 class="text-2xl font-bold text-white">Set Up Two-Factor Authentication</h1>
          <p class="text-blue-100 mt-2">Secure your account with an additional layer of protection</p>
        </div>

        <!-- Content -->
        <div class="px-6 py-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- QR Code Section -->
            <div class="flex flex-col items-center">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 1: Scan QR Code</h3>
              <div class="bg-gray-50 p-4 rounded-lg border-2 border-gray-200 mb-4">
                <img :src="qrCode" alt="QR Code" class="w-48 h-48" />
              </div>
              <p class="text-sm text-gray-600 text-center">
                Scan this QR code with your authenticator app (Google Authenticator, Authy, Microsoft Authenticator, etc.)
              </p>
            </div>

            <!-- Manual Entry Section -->
            <div class="flex flex-col">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 2: Manual Entry</h3>
              <p class="text-sm text-gray-600 mb-4">
                If you can't scan the QR code, enter this key manually:
              </p>
              <div class="bg-gray-50 p-4 rounded-lg border-2 border-gray-200 mb-4 font-mono text-center break-all">
                {{ secret }}
              </div>
              <button
                @click="copySecret"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium mb-6"
              >
                {{ copied ? '✓ Copied!' : 'Copy to clipboard' }}
              </button>

              <!-- Verification Code -->
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Step 3: Verify</h3>
              <form @submit.prevent="submit" class="space-y-4">
                <!-- Error Message -->
                <div v-if="form.errors.code" class="rounded-lg bg-red-50 border border-red-200 p-3">
                  <p class="text-sm text-red-800 font-medium">{{ form.errors.code }}</p>
                </div>

                <!-- Code Input -->
                <div>
                  <label for="code" class="block text-sm font-semibold text-gray-900 mb-2">
                    Enter the 6-digit code from your app:
                  </label>
                  <input
                    id="code"
                    v-model="form.code"
                    type="text"
                    inputmode="numeric"
                    placeholder="000000"
                    maxlength="6"
                    class="w-full px-4 py-3 text-center text-2xl tracking-widest border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    @input="form.code = form.code.replace(/[^0-9]/g, '')"
                  />
                </div>

                <!-- Submit Button -->
                <button
                  type="submit"
                  :disabled="form.processing || !form.code"
                  class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-semibold py-3 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl disabled:cursor-not-allowed"
                >
                  {{ form.processing ? 'Verifying...' : 'Verify & Enable 2FA' }}
                </button>
              </form>
            </div>
          </div>

          <!-- Important Notice -->
          <div class="mt-8 p-4 bg-amber-50 rounded-lg border border-amber-200">
            <p class="text-sm text-amber-900 font-medium mb-2">⚠️ Important:</p>
            <ul class="text-sm text-amber-800 space-y-1 list-disc list-inside">
              <li>Save your backup codes in a safe place</li>
              <li>You'll need them if you lose access to your authenticator app</li>
              <li>Never share your secret key or backup codes with anyone</li>
            </ul>
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

const copied = ref(false)

const form = useForm({
  secret: props.secret,
  code: '',
})

const copySecret = () => {
  navigator.clipboard.writeText(props.secret)
  copied.value = true
  setTimeout(() => {
    copied.value = false
  }, 2000)
}

const submit = () => {
  form.post(route('auth.2fa.confirm'), {
    onFinish: () => {
      form.code = ''
    },
  })
}
</script>
