<template>
  <AuthLayout 
    title="Create your account" 
    subtitle="Join our professional tax consulting platform"
  >
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Name Field -->
      <FormInput
        id="name"
        v-model="form.name"
        name="name"
        type="text"
        label="Full name"
        placeholder="Enter your full name"
        autocomplete="name"
        required
        :error="form.errors.name"
        :icon="UserIcon"
        @focus="form.clearErrors('name')"
      />

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
        help="We'll use this email for account notifications and password recovery"
        @focus="form.clearErrors('email')"
      />

      <!-- Role Selection -->
      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">
          Account Type <span class="text-red-500 ml-1">*</span>
        </label>
        <select
          id="role"
          v-model="form.role"
          name="role"
          required
          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          :class="{ 'border-red-300': form.errors.role }"
        >
          <option value="">Select account type</option>
          <option value="client">Client - I need tax services</option>
          <option value="tax_professional">Tax Professional - I provide tax services</option>
        </select>
        <p v-if="form.errors.role" class="text-sm text-red-600 flex items-center">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          {{ form.errors.role }}
        </p>
        <p v-else class="text-sm text-gray-500">
          Choose the type of account that best describes your role
        </p>
      </div>

      <!-- Password Field -->
      <FormInput
        id="password"
        v-model="form.password"
        name="password"
        type="password"
        label="Password"
        placeholder="Create a strong password"
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
        label="Confirm password"
        placeholder="Confirm your password"
        autocomplete="new-password"
        required
        :error="form.errors.password_confirmation"
        :icon="LockClosedIcon"
        @focus="form.clearErrors('password_confirmation')"
      />

      <!-- Terms and Privacy -->
      <div class="flex items-start">
        <div class="flex items-center h-5">
          <input
            id="terms"
            v-model="form.terms"
            name="terms"
            type="checkbox"
            required
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
          />
        </div>
        <div class="ml-3 text-sm">
          <label for="terms" class="text-gray-700">
            I agree to the 
            <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a>
            and 
            <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
          </label>
        </div>
      </div>

      <!-- Submit Button -->
      <FormButton
        type="submit"
        variant="primary"
        size="lg"
        :loading="form.processing"
        loading-text="Creating account..."
        full-width
        :icon="form.processing ? null : UserPlusIcon"
      >
        Create account
      </FormButton>
    </form>

    <!-- Footer Links -->
    <template #footer>
      <div class="text-center">
        <p class="text-sm text-gray-600">
          Already have an account?
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
import { UserIcon, EnvelopeIcon, LockClosedIcon, UserPlusIcon } from '@heroicons/vue/24/outline'
import AuthLayout from '../../Layouts/AuthLayout.vue'
import FormInput from '../../Components/Form/FormInput.vue'
import FormButton from '../../Components/Form/FormButton.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  role: '',
  password: '',
  password_confirmation: '',
  terms: false
})

const handleSubmit = () => {
  // Determine the correct registration endpoint based on role
  const endpoint = form.role === 'client' ? '/client/register' : '/admin/register'
  
  form.post(endpoint, {
    onError: () => {
      // Clear passwords on error for security
      form.password = ''
      form.password_confirmation = ''
    }
  })
}
</script>