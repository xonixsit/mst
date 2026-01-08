<template>
  <AuthLayout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
      <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
          <!-- Header -->
          <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/20 mx-auto mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
            </div>
            <h1 class="text-2xl font-bold text-white text-center">Verify Your Email</h1>
            <p class="text-blue-100 text-center mt-2">Enter the code sent to your email</p>
          </div>

          <!-- Content -->
          <div class="px-6 py-8">
            <!-- Success Message -->
            <div v-if="$page.props.flash?.success" class="rounded-lg bg-green-50 border border-green-200 p-4 mb-6">
              <p class="text-sm text-green-800 font-medium">{{ $page.props.flash.success }}</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- Error Message -->
              <div v-if="form.errors.code" class="rounded-lg bg-red-50 border border-red-200 p-4">
                <p class="text-sm text-red-800 font-medium">{{ form.errors.code }}</p>
              </div>

              <!-- Code Input -->
              <div>
                <label for="code" class="block text-sm font-semibold text-gray-900 mb-2">
                  Verification Code
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
                <p class="text-xs text-gray-600 mt-2">Check your email for the 6-digit code</p>
              </div>

              <!-- Submit Button -->
              <button
                type="submit"
                :disabled="form.processing || !form.code"
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-semibold py-3 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl disabled:cursor-not-allowed"
              >
                {{ form.processing ? 'Verifying...' : 'Verify' }}
              </button>
            </form>

            <!-- Resend Code -->
            <div class="mt-6 text-center">
              <p class="text-sm text-gray-600 mb-3">Didn't receive the code?</p>
              <button
                @click="resendCode"
                :disabled="resending"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium disabled:text-gray-400"
              >
                {{ resending ? 'Sending...' : 'Resend code' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-gray-600 mt-6">
          Having trouble? <a href="/admin/login" class="text-blue-600 hover:text-blue-800 font-medium">Start over</a>
        </p>
      </div>
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'

const resending = ref(false)

const form = useForm({
  code: '',
})

const submit = () => {
  form.post(route('auth.2fa.verify.code'), {
    onFinish: () => {
      form.code = ''
    },
  })
}

const resendCode = async () => {
  resending.value = true
  try {
    await axios.post(route('auth.2fa.resend'))
  } catch (error) {
    console.error('Error resending code:', error)
  } finally {
    resending.value = false
  }
}
</script>
