<template>
  <AuthLayout 
    title="Forgot your password?" 
    subtitle="No worries, we'll send you reset instructions"
  >
    <!-- Success Message -->
    <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
      <div class="flex">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="ml-3 text-sm text-green-700">{{ status }}</p>
      </div>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Email Field -->
      <FormInput
        id="email"
        v-model="form.email"
        name="email"
        type="email"
        label="Email address"
        placeholder="Enter your email address"
        autocomplete="email"
        required
        :error="form.errors.email"
        :icon="EnvelopeIcon"
        help="We'll send a password reset link to this email address"
        @focus="form.clearErrors('email')"
      />

      <!-- Submit Button -->
      <FormButton
        type="submit"
        variant="primary"
        size="lg"
        :loading="form.processing"
        loading-text="Sending reset link..."
        full-width
        :icon="form.processing ? null : PaperAirplaneIcon"
      >
        Send reset link
      </FormButton>
    </form>

    <!-- Footer Links -->
    <template #footer>
      <div class="text-center space-y-2">
        <p class="text-sm text-gray-600">
          Remember your password?
          <a 
            href="/login" 
            class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200"
          >
            Sign in
          </a>
        </p>
        <p class="text-sm text-gray-600">
          Don't have an account?
          <a 
            href="/register" 
            class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200"
          >
            Create account
          </a>
        </p>
      </div>
    </template>
  </AuthLayout>
</template>

<script setup>
import { EnvelopeIcon, PaperAirplaneIcon } from '@heroicons/vue/24/outline'
import AuthLayout from '../../Layouts/AuthLayout.vue'
import FormInput from '../../Components/Form/FormInput.vue'
import FormButton from '../../Components/Form/FormButton.vue'
import { useForm } from '@inertiajs/vue3'

defineProps({
  status: {
    type: String,
    default: null
  }
})

const form = useForm({
  email: ''
})

const handleSubmit = () => {
  form.post('/forgot-password')
}
</script>