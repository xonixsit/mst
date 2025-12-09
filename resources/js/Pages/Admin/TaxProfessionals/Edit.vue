<template>
  <AppLayout title="Edit Tax Professional">
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-emerald-50 to-green-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-emerald-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-green-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2 pr-2 pl-2">
          <div class="flex items-center space-x-4">
            <!-- Tax Professional Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 via-emerald-600 to-green-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-emerald-100">
              <PencilIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-emerald-900 to-green-900 bg-clip-text text-transparent">
                Edit Tax Professional
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Update {{ taxProfessional.first_name }} {{ taxProfessional.last_name }}'s information</p>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex items-center space-x-3">
            <Link
              :href="`/admin/tax-professionals/${taxProfessional.id}`"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <EyeIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">View Profile</span>
            </Link>
            <Link
              href="/admin/tax-professionals"
              class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <ArrowLeftIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">Back to List</span>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-4">
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-8 py-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-gray-900">Professional Information</h3>
                <p class="text-sm text-gray-600 mt-1">Update the tax professional's details</p>
              </div>
              <div class="flex items-center space-x-2">
                <PencilIcon class="w-5 h-5 text-gray-400" />
              </div>
            </div>
          </div>

          <form @submit.prevent="submit" class="p-8">
            <div class="space-y-8">
              <!-- Personal Information -->
              <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200">
                <div class="flex items-center mb-6">
                  <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <UserIcon class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-blue-900">Personal Information</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-blue-700">First Name *</label>
                    <input
                      v-model="form.first_name"
                      type="text"
                      required
                      class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                      :class="{ 'border-red-300': form.errors.first_name }"
                    />
                    <div v-if="form.errors.first_name" class="text-red-600 text-sm">{{ form.errors.first_name }}</div>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-blue-700">Middle Name</label>
                    <input
                      v-model="form.middle_name"
                      type="text"
                      class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    />
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-blue-700">Last Name *</label>
                    <input
                      v-model="form.last_name"
                      type="text"
                      required
                      class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                      :class="{ 'border-red-300': form.errors.last_name }"
                    />
                    <div v-if="form.errors.last_name" class="text-red-600 text-sm">{{ form.errors.last_name }}</div>
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
                    <label class="block text-sm font-semibold text-green-700">Email Address *</label>
                    <input
                      v-model="form.email"
                      type="email"
                      required
                      class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                      :class="{ 'border-red-300': form.errors.email }"
                    />
                    <div v-if="form.errors.email" class="text-red-600 text-sm">{{ form.errors.email }}</div>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-green-700">Phone Number</label>
                    <input
                      v-model="form.phone"
                      type="tel"
                      class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
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
                    <label class="block text-sm font-semibold text-purple-700">Street Address</label>
                    <input
                      v-model="form.address"
                      type="text"
                      class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                    />
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-purple-700">City</label>
                      <input
                        v-model="form.city"
                        type="text"
                        class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                      />
                    </div>

                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-purple-700">State</label>
                      <input
                        v-model="form.state"
                        type="text"
                        class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                      />
                    </div>

                    <div class="space-y-2">
                      <label class="block text-sm font-semibold text-purple-700">ZIP Code</label>
                      <input
                        v-model="form.zip_code"
                        type="text"
                        class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Professional Information -->
              <div class="bg-gradient-to-br from-amber-50 to-yellow-100 rounded-xl p-6 border border-amber-200">
                <div class="flex items-center mb-6">
                  <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center mr-3">
                    <AcademicCapIcon class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-amber-900">Professional Information</h4>
                </div>
                
                <div class="space-y-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-amber-700">License Number</label>
                    <input
                      v-model="form.license_number"
                      type="text"
                      class="w-full px-4 py-3 border-2 border-amber-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200"
                    />
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-amber-700">Specializations</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                      <label v-for="specialization in availableSpecializations" :key="specialization" class="flex items-center space-x-2 bg-white p-3 rounded-lg border border-amber-200 hover:border-amber-300 transition-colors cursor-pointer">
                        <input
                          v-model="form.specializations"
                          :value="specialization"
                          type="checkbox"
                          class="rounded border-amber-300 text-amber-600 focus:ring-amber-500"
                        />
                        <span class="text-sm font-medium text-amber-800">{{ specialization }}</span>
                      </label>
                    </div>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-amber-700">Bio</label>
                    <textarea
                      v-model="form.bio"
                      rows="4"
                      class="w-full px-4 py-3 border-2 border-amber-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 resize-none"
                      placeholder="Brief professional biography..."
                    ></textarea>
                  </div>
                </div>
              </div>

              <!-- Password Update -->
              <div class="bg-gradient-to-br from-red-50 to-rose-100 rounded-xl p-6 border border-red-200">
                <div class="flex items-center mb-6">
                  <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                    <LockClosedIcon class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-red-900">Update Password</h4>
                  <span class="ml-3 text-sm text-red-600">(Leave blank to keep current password)</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-red-700">New Password</label>
                    <input
                      v-model="form.password"
                      type="password"
                      class="w-full px-4 py-3 border-2 border-red-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200"
                      :class="{ 'border-red-300': form.errors.password }"
                    />
                    <div v-if="form.errors.password" class="text-red-600 text-sm">{{ form.errors.password }}</div>
                  </div>

                  <div class="space-y-2">
                    <label class="block text-sm font-semibold text-red-700">Confirm New Password</label>
                    <input
                      v-model="form.password_confirmation"
                      type="password"
                      class="w-full px-4 py-3 border-2 border-red-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-4 pt-8 border-t border-gray-200 mt-8">
              <Link
                href="/admin/tax-professionals"
                class="bg-gradient-to-r from-gray-500 to-slate-600 hover:from-gray-600 hover:to-slate-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center"
              >
                <XMarkIcon class="w-4 h-4 mr-2" />
                Cancel
              </Link>
              <button
                type="submit"
                :disabled="form.processing"

                class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center"
              >
                <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <CheckIcon v-else class="w-4 h-4 mr-2" />
                <span v-if="form.processing">Updating Professional...</span>
                <span v-else>Update Professional</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm, router } from '@inertiajs/vue3'
import { watch, nextTick } from 'vue'
import { 
  PencilIcon,
  ArrowLeftIcon,
  EyeIcon,
  UserIcon,
  PhoneIcon,
  MapPinIcon,
  AcademicCapIcon,
  LockClosedIcon,
  XMarkIcon,
  CheckIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  taxProfessional: Object,
})

// Debug: Log the received data
console.log('Tax Professional Props:', props.taxProfessional)

const availableSpecializations = [
  'Individual Tax Returns',
  'Business Tax Returns',
  'Estate Planning',
  'Tax Planning',
  'Audit Representation',
  'Bookkeeping',
  'Payroll Services',
  'Financial Planning'
]

const form = useForm({
  first_name: '',
  middle_name: '',
  last_name: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  zip_code: '',
  license_number: '',
  specializations: [],
  bio: '',
  password: '',
  password_confirmation: '',
})



// Populate form data when component mounts or props change
watch(() => props.taxProfessional, (newVal) => {
  if (newVal && typeof newVal === 'object') {
    form.first_name = newVal.first_name || ''
    form.middle_name = newVal.middle_name || ''
    form.last_name = newVal.last_name || ''
    form.email = newVal.email || ''
    form.phone = newVal.phone || ''
    form.address = newVal.address || ''
    form.city = newVal.city || ''
    form.state = newVal.state || ''
    form.zip_code = newVal.zip_code || ''
    form.license_number = newVal.license_number || ''
    form.bio = newVal.bio || ''
    
    // Handle specializations - could be array or JSON string
    if (Array.isArray(newVal.specializations)) {
      form.specializations = newVal.specializations
    } else if (typeof newVal.specializations === 'string') {
      try {
        form.specializations = JSON.parse(newVal.specializations || '[]')
      } catch (e) {
        form.specializations = []
      }
    } else {
      form.specializations = []
    }
  }
}, { immediate: true, deep: true })

const submit = () => {
  form.put(`/admin/tax-professionals/${props.taxProfessional.id}`)
}
</script>