<template>
  <AuthLayout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 flex flex-col justify-center py-6 sm:px-6 lg:px-4 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-bl from-blue-100/40 to-transparent rounded-bl-full"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-indigo-100/30 to-transparent rounded-tr-full"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-r from-emerald-100/20 to-blue-100/20 rounded-full blur-3xl"></div>

    <!-- Header -->
    <div class="relative sm:mx-auto sm:w-full sm:max-w-md mb-8">
      <div class="flex justify-center mb-6">
        <div class="flex items-center space-x-4">
          <!-- Enhanced Logo -->
          <div class="w-14 h-14 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700 rounded-2xl flex items-center justify-center shadow-xl ring-4 ring-blue-100">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent">MySuperTax</h1>
            <p class="text-sm text-gray-600 font-medium">Professional Tax Services</p>
          </div>
        </div>
      </div>
      
      <div class="text-center">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent">
          Welcome Back
        </h2>
        <p class="mt-3 text-gray-600 font-medium">Sign in to your account to continue</p>
        
        <!-- Status Indicators -->
        <div class="flex items-center justify-center space-x-4 mt-4">
          <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
            <span class="text-xs font-semibold text-emerald-700">Secure Login</span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
            <span class="text-xs font-semibold text-blue-700">Quick Access</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="relative sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white/95 backdrop-blur-sm py-10 px-8 shadow-2xl rounded-2xl border border-gray-200/50 ring-1 ring-gray-900/5">
        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl shadow-sm">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <p class="text-sm font-semibold text-emerald-800">{{ $page.props.flash.success }}</p>
          </div>
        </div>

        <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl shadow-sm">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center mr-3">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <p class="text-sm font-semibold text-red-800">{{ $page.props.flash.error }}</p>
          </div>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Enhanced Email Field -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">
              Email Address <span class="text-red-500 ml-1">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <div class="w-5 h-5 bg-emerald-100 rounded-lg flex items-center justify-center">
                  <EnvelopeIcon class="w-3 h-3 text-emerald-600" />
                </div>
              </div>
              <input
                id="email"
                v-model="form.email"
                name="email"
                type="email"
                placeholder="Enter your email address"
                autocomplete="email"
                required
                :class="[
                  'block w-full pl-12 pr-4 py-3 border rounded-xl shadow-sm transition-all duration-300',
                  'placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 sm:text-sm',
                  form.errors.email 
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 bg-red-50' 
                    : 'border-gray-300 text-gray-900 focus:ring-emerald-500 focus:border-emerald-500 hover:border-gray-400 bg-white'
                ]"
                @focus="form.clearErrors('email')"
              />
            </div>
            <p v-if="form.errors.email" class="text-sm text-red-600 flex items-center font-medium">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ form.errors.email }}
            </p>
          </div>

          <!-- Enhanced Password Field -->
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">
              Password <span class="text-red-500 ml-1">*</span>
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <div class="w-5 h-5 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <LockClosedIcon class="w-3 h-3 text-indigo-600" />
                </div>
              </div>
              <input
                id="password"
                v-model="form.password"
                name="password"
                type="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
                :class="[
                  'block w-full pl-12 pr-4 py-3 border rounded-xl shadow-sm transition-all duration-300',
                  'placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 sm:text-sm',
                  form.errors.password 
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 bg-red-50' 
                    : 'border-gray-300 text-gray-900 focus:ring-indigo-500 focus:border-indigo-500 hover:border-gray-400 bg-white'
                ]"
                @focus="form.clearErrors('password')"
              />
            </div>
            <p v-if="form.errors.password" class="text-sm text-red-600 flex items-center font-medium">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ form.errors.password }}
            </p>
          </div>

          <!-- Enhanced Remember Me & Forgot Password -->
          <div class="bg-gradient-to-r from-gray-50 to-slate-50 p-4 rounded-xl border border-gray-200">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <input
                  id="remember"
                  v-model="form.remember"
                  name="remember"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors duration-200"
                />
                <label for="remember" class="ml-3 text-sm font-medium text-gray-700">
                  Remember me for 30 days
                </label>
              </div>

              <div class="text-sm">
                <a 
                  href="/client/forgot-password" 
                  class="font-semibold text-blue-600 hover:text-blue-500 transition-colors duration-200 underline"
                >
                  Forgot password?
                </a>
              </div>
            </div>
          </div>

          <!-- Enhanced Submit Button -->
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:from-blue-700 hover:via-blue-800 hover:to-indigo-800 text-white py-4 px-6 rounded-xl font-semibold text-base transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center ring-2 ring-blue-100"
          >
            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            {{ form.processing ? 'Signing In...' : 'Sign In' }}
          </button>

          <!-- Quick Access Options -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-200">
            <div class="text-center">
              <p class="text-sm font-semibold text-blue-900 mb-2">Quick Access</p>
              <div class="flex items-center justify-center space-x-4">
                <div class="flex items-center space-x-1">
                  <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                  <span class="text-xs text-blue-700 font-medium">Admin Portal</span>
                </div>
                <div class="flex items-center space-x-1">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                  <span class="text-xs text-emerald-700 font-medium">Client Portal</span>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Enhanced Footer Links -->
      <div class="mt-8 text-center">
        <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 shadow-lg border border-gray-200/50">
          <p class="text-sm text-gray-600 font-medium">
            Don't have an account?
            <a 
              :href="registerLink" 
              class="font-semibold text-blue-600 hover:text-blue-500 transition-colors duration-200 underline ml-1"
            >
              Create account here
            </a>
          </p>
        </div>
      </div>
    </div>

    <!-- Enhanced Footer -->
    <footer class="relative mt-12 text-center">
      <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 mx-auto max-w-md shadow-lg border border-gray-200/50">
        <p class="text-sm text-gray-500 font-medium">
          &copy; {{ new Date().getFullYear() }} MySuperTax. Professional Tax Consulting Services.
        </p>
        <div class="flex items-center justify-center space-x-4 mt-2">
          <div class="flex items-center space-x-1">
            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
            <span class="text-xs text-emerald-700 font-semibold">Secure Platform</span>
          </div>
          <div class="flex items-center space-x-1">
            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
            <span class="text-xs text-blue-700 font-semibold">Professional Service</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
  </AuthLayout>
</template>

<script setup>
import { EnvelopeIcon, LockClosedIcon } from '@heroicons/vue/24/outline'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import AuthLayout from '../../Layouts/AuthLayout.vue'
import FormInput from '../../Components/Form/FormInput.vue'
import FormButton from '../../Components/Form/FormButton.vue'

// Get props from the controller
const props = defineProps({
  loginType: {
    type: String,
    default: 'admin'
  }
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
  login_type: props.loginType
})

// Determine the correct registration link based on current URL
const registerLink = computed(() => {
  const currentPath = window.location.pathname
  return currentPath.includes('/client/') ? '/client/register' : '/admin/register'
})

const handleSubmit = () => {
  const loginUrl = props.loginType === 'client' ? '/client/login' : '/admin/login'
  
  form.post(loginUrl, {
    onError: () => {
      form.password = ''
    }
  })
}
</script>