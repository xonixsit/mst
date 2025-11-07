<template>
  <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <TrashIcon class="h-6 w-6 text-red-600" />
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Permanently Delete Archived Data
              </h3>
              <div class="mt-4">
                <form @submit.prevent="handleSubmit" class="space-y-4">
                  <div>
                    <label for="archive_days" class="block text-sm font-medium text-gray-700">
                      Archive Retention Period (Days)
                    </label>
                    <input
                      id="archive_days"
                      v-model="form.archive_days"
                      type="number"
                      min="365"
                      max="3650"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    />
                    <p class="mt-1 text-sm text-gray-500">
                      Data archived for longer than this period will be permanently deleted (minimum 365 days)
                    </p>
                    <div v-if="form.errors.archive_days" class="mt-1 text-sm text-red-600">
                      {{ form.errors.archive_days }}
                    </div>
                  </div>

                  <div>
                    <label class="flex items-center">
                      <input
                        v-model="form.confirm_deletion"
                        type="checkbox"
                        class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50"
                      />
                      <span class="ml-2 text-sm text-gray-700">
                        I understand that this action will permanently delete data and cannot be undone
                      </span>
                    </label>
                    <div v-if="form.errors.confirm_deletion" class="mt-1 text-sm text-red-600">
                      {{ form.errors.confirm_deletion }}
                    </div>
                  </div>

                  <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                      <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
                      <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                          Danger Zone - Permanent Deletion
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                          <ul class="list-disc list-inside space-y-1">
                            <li>This will permanently delete archived clients and ALL related data</li>
                            <li>Deleted data includes documents, invoices, messages, and audit logs</li>
                            <li>Physical files will be removed from storage</li>
                            <li>This action is irreversible and cannot be undone</li>
                            <li>Ensure you have proper backups before proceeding</li>
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
            :disabled="form.processing || !form.confirm_deletion"
            type="button"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
          >
            <span v-if="form.processing" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Deleting...
            </span>
            <span v-else>Permanently Delete</span>
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
  TrashIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const emit = defineEmits(['close', 'delete'])

const form = useForm({
  archive_days: 2555, // Default to 7 years
  confirm_deletion: false,
})

const handleSubmit = () => {
  if (form.archive_days >= 365 && form.archive_days <= 3650 && form.confirm_deletion) {
    emit('delete', form.archive_days)
  }
}
</script>