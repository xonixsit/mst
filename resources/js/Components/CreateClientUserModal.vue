<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="close"
      ></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
              <UserPlusIcon class="h-6 w-6 text-blue-600" />
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Create Client User Account
              </h3>
              <div class="mt-4 space-y-4">
                <!-- First Name -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    First Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.first_name"
                    type="text"
                    placeholder="Enter first name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <p v-if="errors.first_name" class="text-red-600 text-xs mt-1">{{ errors.first_name }}</p>
                </div>

                <!-- Middle Name -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Middle Name
                  </label>
                  <input
                    v-model="form.middle_name"
                    type="text"
                    placeholder="Enter middle name (optional)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>

                <!-- Last Name -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Last Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.last_name"
                    type="text"
                    placeholder="Enter last name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <p v-if="errors.last_name" class="text-red-600 text-xs mt-1">{{ errors.last_name }}</p>
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.email"
                    type="email"
                    placeholder="Enter email address"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <p v-if="errors.email" class="text-red-600 text-xs mt-1">{{ errors.email }}</p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                  <div class="flex items-start">
                    <InformationCircleIcon class="h-5 w-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" />
                    <div class="text-sm text-blue-800">
                      <p class="font-semibold">Account Creation Details:</p>
                      <ul class="list-disc list-inside mt-2 space-y-1 text-xs">
                        <li>A secure password will be auto-generated</li>
                        <li>Login credentials will be sent to the email address</li>
                        <li>Client can change password on first login</li>
                        <li>Account will be active immediately</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="submit"
            :disabled="isSubmitting"
            type="button"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isSubmitting ? 'Creating...' : 'Create Account & Send Credentials' }}
          </button>
          <button
            @click="close"
            type="button"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { UserPlusIcon, InformationCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  isOpen: Boolean
})

const emit = defineEmits(['close', 'submit'])

const isSubmitting = ref(false)
const form = reactive({
  first_name: '',
  middle_name: '',
  last_name: '',
  email: ''
})

const errors = reactive({
  first_name: '',
  last_name: '',
  email: ''
})

const close = () => {
  resetForm()
  emit('close')
}

const resetForm = () => {
  form.first_name = ''
  form.middle_name = ''
  form.last_name = ''
  form.email = ''
  errors.first_name = ''
  errors.last_name = ''
  errors.email = ''
}

const submit = async () => {
  // Clear previous errors
  errors.first_name = ''
  errors.last_name = ''
  errors.email = ''

  // Validate
  if (!form.first_name.trim()) {
    errors.first_name = 'First name is required'
  }
  if (!form.last_name.trim()) {
    errors.last_name = 'Last name is required'
  }
  if (!form.email.trim()) {
    errors.email = 'Email is required'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Please enter a valid email address'
  }

  if (errors.first_name || errors.last_name || errors.email) {
    return
  }

  isSubmitting.value = true
  try {
    emit('submit', { ...form })
    resetForm()
  } finally {
    isSubmitting.value = false
  }
}
</script>
