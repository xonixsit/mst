<template>
  <AuthLayout 
    title="Welcome back" 
    subtitle="Sign in to your account to continue"
  >
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Email Field -->
      <FormInput
        id="email"
        v-model="form.email"
        name="email"
        type="email"
        label="Email address"
        placeholder="Enter your email"
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
        label="Password"
        placeholder="Enter your password"
        autocomplete="current-password"
        required
        :error="form.errors.password"
        :icon="LockClosedIcon"
        @focus="form.clearErrors('password')"
      />

      <!-- Remember Me & Forgot Password -->
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <input
            id="remember"
            v-model="form.remember"
            name="remember"
            type="checkbox"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
          />
          <label for="remember" class="ml-2 block text-sm text-gray-900">
            Remember me
          </label>
        </div>

        <div class="text-sm">
          <a 
            href="/forgot-password" 
            class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200"
          >
            Forgot your password?
          </a>
        </div>
      </div>

      <!-- Submit Button -->
      <FormButton
        type="submit"
        variant="primary"
        size="lg"
        :loading="form.processing"
        loading-text="Signing in..."
        full-width
        :icon="form.processing ? null : LockClosedIcon"
      >
        Sign in
      </FormButton>
    </form>

    <!-- Footer Links -->
    <template #footer>
      <div class="text-center">
        <p class="text-sm text-gray-600">
          Don't have an account?
          <a 
            :href="registerLink" 
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
import { EnvelopeIcon, LockClosedIcon } from '@heroicons/vue/24/outline'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import AuthLayout from '../../Layouts/AuthLayout.vue'
import FormInput from '../../Components/Form/FormInput.vue'
import FormButton from '../../Components/Form/FormButton.vue'

const form = useForm({
  email: '',
  password: '',
  remember: false
})

// Determine the correct registration link based on current URL
const registerLink = computed(() => {
  const currentPath = window.location.pathname
  return currentPath.includes('/client/') ? '/client/register' : '/admin/register'
})

const handleSubmit = () => {
  const loginUrl = window.location.pathname.includes('/admin/') ? '/admin/login' : 
                   window.location.pathname.includes('/client/') ? '/client/login' : '/login'
  
  form.post(loginUrl, {
    onSuccess: () => {
      const dashboardUrl = window.location.pathname.includes('/admin/') ? '/admin/dashboard' : '/client/dashboard'
      window.location.href = dashboardUrl
    },
    onError: () => {
      form.password = ''
    }
  })
}
</script>