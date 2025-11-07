<template>
  <AuthLayout 
    title="Reset your password" 
    subtitle="Enter your new password below"
  >
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Hidden token field -->
      <input type="hidden" name="token" :value="token">

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
        @focus="form.clearErrors('email')"
      />

      <!-- Password Field -->
      <FormInput
        id="password"
        v-model="form.password"
        name="password"
        type="password"
        label="New password"
        placeholder="Enter your new password"
        autocomplete="new-password"
        required
        :error="form.errors.password"
        :icon="LockClosedIcon"
        help="Password must be at least 8 characters long"
        @focus="form.clearErrors('password')"
      />

      <!-- Confirm Password Field -->
      <FormInput
        id="password_confirmation"
        v-model="form.password_confirmation"
        name="password_confirmation"
        type="password"
        label="Confirm new password"
        placeholder="Confirm your new password"
        autocomplete="new-password"
        required
        :error="form.errors.password_confirmation"
        :icon="LockClosedIcon"
        @focus="form.clearErrors('password_confirmation')"
      />

      <!-- Submit Button -->
      <FormButton
        type="submit"
        variant="primary"
        size="lg"
        :loading="form.processing"
        loading-text="Resetting password..."
        full-width
        :icon="form.processing ? null : KeyIcon"
      >
        Reset password
      </FormButton>
    </form>

    <!-- Footer Links -->
    <template #footer>
      <div class="text-center">
        <p class="text-sm text-gray-600">
          Remember your password?
          <a 
            href="/login" 
            class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200"
          >
            Sign in
          </a>
        </p>
      </div>
    </template>
  </AuthLayout>
</template>

<script setup>
import { EnvelopeIcon, LockClosedIcon, KeyIcon } from '@heroicons/vue/24/outline'
import AuthLayout from '../../Layouts/AuthLayout.vue'
import FormInput from '../../Components/Form/FormInput.vue'
import FormButton from '../../Components/Form/FormButton.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  token: {
    type: String,
    required: true
  },
  email: {
    type: String,
    default: ''
  }
})

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: ''
})

const handleSubmit = () => {
  form.post('/reset-password', {
    onError: () => {
      // Clear passwords on error for security
      form.password = ''
      form.password_confirmation = ''
    }
  })
}
</script>