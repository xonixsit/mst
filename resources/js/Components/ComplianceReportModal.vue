<template>
  <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="w-full">
              <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Generate Compliance Report
                </h3>
                <button
                  @click="$emit('close')"
                  class="text-gray-400 hover:text-gray-600"
                >
                  <XMarkIcon class="h-6 w-6" />
                </button>
              </div>

              <form @submit.prevent="generateReport" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input
                      id="start_date"
                      v-model="form.start_date"
                      type="date"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <div v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">
                      {{ form.errors.start_date }}
                    </div>
                  </div>

                  <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input
                      id="end_date"
                      v-model="form.end_date"
                      type="date"
                      required
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <div v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">
                      {{ form.errors.end_date }}
                    </div>
                  </div>
                </div>

                <div>
                  <label for="model_type" class="block text-sm font-medium text-gray-700">Model Type (Optional)</label>
                  <select
                    id="model_type"
                    v-model="form.model_type"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  >
                    <option value="">All Models</option>
                    <option value="App\Models\Client">Clients</option>
                    <option value="App\Models\Invoice">Invoices</option>
                    <option value="App\Models\Document">Documents</option>
                    <option value="App\Models\Message">Messages</option>
                  </select>
                  <div v-if="form.errors.model_type" class="mt-1 text-sm text-red-600">
                    {{ form.errors.model_type }}
                  </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                  <div class="flex">
                    <InformationCircleIcon class="h-5 w-5 text-blue-400" />
                    <div class="ml-3">
                      <h3 class="text-sm font-medium text-blue-800">
                        Compliance Report Information
                      </h3>
                      <div class="mt-2 text-sm text-blue-700">
                        <p>This report will include:</p>
                        <ul class="list-disc list-inside mt-1 space-y-1">
                          <li>Total audit events in the specified date range</li>
                          <li>Breakdown of events by type (created, updated, deleted, etc.)</li>
                          <li>User activity summary</li>
                          <li>Model activity breakdown</li>
                          <li>Daily activity trends</li>
                          <li>Compliance metrics and statistics</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                  <button
                    type="button"
                    @click="$emit('close')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                  >
                    <span v-if="form.processing" class="flex items-center">
                      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Generating...
                    </span>
                    <span v-else>Generate Report</span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import {
  XMarkIcon,
  InformationCircleIcon,
} from '@heroicons/vue/24/outline'

defineEmits(['close'])

const form = useForm({
  start_date: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // 30 days ago
  end_date: new Date().toISOString().split('T')[0], // Today
  model_type: '',
})

const generateReport = () => {
  form.get(route('admin.audit.compliance-report'), {
    onSuccess: () => {
      // The report page will open, so we can close this modal
    },
  })
}
</script>