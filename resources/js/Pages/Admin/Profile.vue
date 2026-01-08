<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-indigo-50 to-purple-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-indigo-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-purple-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2 pr-2 pl-2">
          <div class="flex items-center space-x-4">
            <div class="w-14 h-14 bg-gradient-to-br from-amber-500 via-amber-600 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-amber-100">
            <UserCircleIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-indigo-900 to-purple-900 bg-clip-text text-transparent">
                Profile Settings
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Manage your account information and preferences</p>
              
              <!-- User Info -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-indigo-700">{{ page.props.auth.user?.name }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-purple-700">{{ (page.props.auth.user?.role || '').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-4 space-y-8">
        <!-- Enhanced Profile Information -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center mr-3">
                <UserIcon class="w-4 h-4 text-white" />
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900">Profile Information</h2>
                <p class="text-sm text-gray-600">Update your account's profile information and email address</p>
              </div>
            </div>
          </div>
          <form @submit.prevent="updateProfile" class="p-8">
            <div class="space-y-8">
              <!-- Personal Information -->
              <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200">
                <div class="flex items-center mb-6">
                  <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <UserIcon class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-blue-900">Personal Information</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label for="name" class="block text-sm font-semibold text-blue-700">Full Name</label>
                    <input
                      id="name"
                      v-model="profileForm.name"
                      type="text"
                      class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                      required
                    />
                  </div>
                  <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-blue-700">Email Address</label>
                    <input
                      id="email"
                      v-model="profileForm.email"
                      type="email"
                      class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                      required
                    />
                  </div>
                </div>
              </div>

              <!-- Contact Information -->
              <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 border border-green-200">
                <div class="flex items-center mb-6">
                  <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                    <PhoneIcon class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-green-900">Contact Information</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label for="phone" class="block text-sm font-semibold text-green-700">Phone Number</label>
                    <input
                      id="phone"
                      v-model="profileForm.phone"
                      type="tel"
                      class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                      placeholder="(555) 123-4567"
                    />
                  </div>
                  <div class="space-y-2">
                    <label for="license_number" class="block text-sm font-semibold text-green-700">License Number</label>
                    <input
                      id="license_number"
                      v-model="profileForm.license_number"
                      type="text"
                      class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                      placeholder="Professional license number"
                    />
                  </div>
                </div>
              </div>

              <!-- Address Information -->
              <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-6 border border-purple-200">
                <div class="flex items-center mb-6">
                  <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                    <MapPinIcon class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-purple-900">Address Information</h4>
                </div>
                
                <div class="space-y-6">
                  <div class="space-y-2">
                    <label for="address" class="block text-sm font-semibold text-purple-700">Street Address</label>
                    <input
                      id="address"
                      v-model="profileForm.address"
                      type="text"
                      class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                      placeholder="Street address"
                    />
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                      <label for="city" class="block text-sm font-semibold text-purple-700">City</label>
                      <input
                        id="city"
                        v-model="profileForm.city"
                        type="text"
                        class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                        placeholder="City"
                      />
                    </div>
                    <div class="space-y-2">
                      <label for="state" class="block text-sm font-semibold text-purple-700">State</label>
                      <input
                        id="state"
                        v-model="profileForm.state"
                        type="text"
                        class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                        placeholder="State"
                      />
                    </div>
                    <div class="space-y-2">
                      <label for="zip_code" class="block text-sm font-semibold text-purple-700">ZIP Code</label>
                      <input
                        id="zip_code"
                        v-model="profileForm.zip_code"
                        type="text"
                        class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                        placeholder="ZIP Code"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Professional Information -->
              <div class="bg-gradient-to-br from-orange-50 to-amber-100 rounded-xl p-6 border border-orange-200">
                <div class="flex items-center mb-6">
                  <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                    <BriefcaseIcon class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-orange-900">Professional Information</h4>
                </div>
                
                <div class="space-y-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-orange-700">Specializations</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                      <label v-for="specialization in availableSpecializations" :key="specialization" class="flex items-center space-x-2 bg-white p-3 rounded-lg border border-orange-200 hover:border-orange-300 transition-colors cursor-pointer">
                        <input
                          v-model="profileForm.specializations"
                          :value="specialization"
                          type="checkbox"
                          class="rounded border-orange-300 text-orange-600 focus:ring-orange-500"
                        />
                        <span class="text-sm text-orange-800">{{ specialization }}</span>
                      </label>
                    </div>
                  </div>

                  <div class="space-y-2">
                    <label for="bio" class="block text-sm font-semibold text-orange-700">Professional Bio</label>
                    <textarea
                      id="bio"
                      v-model="profileForm.bio"
                      rows="4"
                      class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200"
                      placeholder="Brief professional biography and experience"
                    ></textarea>
                  </div>
                </div>
              </div>

              <!-- System Information -->
              <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200">
                <div class="flex items-center mb-6">
                  <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center mr-3">
                    <LockClosedIcon class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-gray-900">System Information</h4>
                </div>
                
                <div class="space-y-2">
                  <label for="role" class="block text-sm font-semibold text-gray-700">Role</label>
                  <div class="relative">
                    <input
                      id="role"
                      :value="(page.props.auth.user?.role || '').replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"
                      type="text"
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-600 cursor-not-allowed"
                      disabled
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                      <LockClosedIcon class="w-4 h-4 text-gray-400" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
              <div class="flex justify-end pt-6 border-t border-gray-200">
                <button
                  type="submit"
                  class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center"
                  :disabled="profileForm.processing"
                >
                  <CheckIcon v-if="!profileForm.processing" class="w-5 h-5 mr-2" />
                  <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ profileForm.processing ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Change Password -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden max-w-4xl mx-auto"></div>
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                <LockClosedIcon class="w-4 h-4 text-white" />
              </div>
              <div>
                <h2 class="text-lg font-bold text-gray-900">Change Password</h2>
                <p class="text-sm text-gray-600">Ensure your account is using a long, random password to stay secure</p>
              </div>
            </div>
          </div>
          
          <form @submit.prevent="updatePassword" class="p-8">
            <div class="bg-gradient-to-br from-red-50 to-rose-100 rounded-xl p-6 border border-red-200">
              <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                  <LockClosedIcon class="w-5 h-5 text-white" />
                </div>
                <h4 class="text-lg font-bold text-red-900">Security Settings</h4>
              </div>
              
              <div class="space-y-6">
                <div class="space-y-2">
                  <label for="current_password" class="block text-sm font-semibold text-red-700">Current Password</label>
                  <input
                    id="current_password"
                    v-model="passwordForm.current_password"
                    type="password"
                    class="w-full px-4 py-3 border-2 border-red-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200"
                    required
                  />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-red-700">New Password</label>
                    <input
                      id="password"
                      v-model="passwordForm.password"
                      type="password"
                      class="w-full px-4 py-3 border-2 border-red-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200"
                      required
                    />
                  </div>
                  <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-semibold text-red-700">Confirm Password</label>
                    <input
                      id="password_confirmation"
                      v-model="passwordForm.password_confirmation"
                      type="password"
                      class="w-full px-4 py-3 border-2 border-red-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200"
                      required
                    />
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200 mt-8">
              <button
                type="submit"
                class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center"
                :disabled="passwordForm.processing"
              >
                <LockClosedIcon v-if="!passwordForm.processing" class="w-5 h-5 mr-2" />
                <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ passwordForm.processing ? 'Updating Password...' : 'Update Password' }}
              </button>
            </div>
          </form>
        </div>

      <!-- Two-Factor Authentication -->
      <TwoFactorSettings :user="page.props.auth.user" />

      <!-- Account Statistics -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-900">Account Statistics</h2>
        </div>
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="text-2xl font-bold text-indigo-600">{{ stats.totalClients }}</div>
              <div class="text-sm text-gray-600">Total Clients</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ stats.activeClients }}</div>
              <div class="text-sm text-gray-600">Active Clients</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">{{ stats.completedReturns }}</div>
              <div class="text-sm text-gray-600">Completed Returns</div>
            </div>
          </div>
        </div>
      </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import TwoFactorSettings from '@/Components/TwoFactorSettings.vue'
import { 
  UserCircleIcon, 
  UserIcon, 
  PhoneIcon, 
  MapPinIcon, 
  BriefcaseIcon, 
  LockClosedIcon, 
  CheckIcon 
} from '@heroicons/vue/24/outline'


const page = usePage()

// Available specializations
const availableSpecializations = [
  'Individual Tax Returns',
  'Business Tax Returns',
  'Corporate Tax',
  'Estate Planning',
  'Tax Planning',
  'Payroll Services',
  'Bookkeeping',
  'Financial Planning',
  'Audit Representation',
  'IRS Problem Resolution',
  'QuickBooks Setup',
  'Sales Tax'
]

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      totalClients: 0,
      activeClients: 0,
      completedReturns: 0
    })
  }
})

const profileForm = useForm({
  name: page.props.auth.user?.name || '',
  email: page.props.auth.user?.email || '',
  phone: page.props.auth.user?.phone || '',
  address: page.props.auth.user?.address || '',
  city: page.props.auth.user?.city || '',
  state: page.props.auth.user?.state || '',
  zip_code: page.props.auth.user?.zip_code || '',
  license_number: page.props.auth.user?.license_number || '',
  specializations: Array.isArray(page.props.auth.user?.specializations) 
    ? page.props.auth.user.specializations 
    : (page.props.auth.user?.specializations ? JSON.parse(page.props.auth.user.specializations) : []),
  bio: page.props.auth.user?.bio || ''
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const updateProfile = () => {
  profileForm.patch('/admin/profile', {
    preserveScroll: true,
    onSuccess: () => {
      // Profile updated successfully
    }
  })
}

const updatePassword = () => {
  passwordForm.put('/admin/password', {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    }
  })
}


</script>