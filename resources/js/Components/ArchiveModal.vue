<template>
  <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
              <ArchiveBoxIcon class="h-6 w-6 text-blue-600" />
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Archive Inactive Clients
              </h3>
              <div class="mt-4">
                <form @submit.prevent="handleSubmit" class="space-y-4">
                  <div>
                    <label for="inactive_days" class="block text-sm font-medium text-gray-700">
                      Inactive Period (Days)
                    </label>
                    <input
                      id="inactive_days"
                      v-model="form.inactive_days"
                      type="number"
                      min="30"
                      max="3650"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                    <p class="mt-1 text-sm text-gray-500">
                      Clients inactive for this many days will be archived (minimum 30 days)
                    </p>
                    <div v-if="form.errors.inactive_days" class="mt-1 text-sm text-red-600">
                      {{ form.errors.inactive_days }}
                    </div>
                  </div>

                  <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                      <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400" />
                      <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                          Important Information
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                          <ul class="list-disc list-inside space-y-1">
                            <li>Archived clients will be moved to archive storage</li>
                            <li>All related documents, invoices, and messages will be archived</li>
                            <li>Archived data can be restored if needed</li>
                            <li>This action cannot be undone automatically</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="handleSubmit"
            :disabled="form.processing"
            type="button"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
          >
            <span v-if="form.processing" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Archiving...
            </span>
            <span v-else>Archive Clients</span>
          </button>
          <button
            @click="$emit('close')"
            type="button"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import {
  ArchiveBoxIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const emit = defineEmits(['close', 'archive'])

const form = useForm({
  inactive_days: 365, // Default to 1 year
})

const handleSubmit = () => {
  if (form.inactive_days >= 30 && form.inactive_days <= 3650) {
    emit('archive', form.inactive_days)
  }
}
</script>