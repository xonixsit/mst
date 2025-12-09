<template>
  <AppLayout title="Import Clients">
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-4">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <div class="flex items-center justify-between mb-8">
              <div>
                <h1 class="text-2xl font-medium text-gray-900">Import Clients from CSV</h1>
                <p class="mt-2 text-sm text-gray-600">
                  Upload a CSV file to import multiple clients at once. You can preview the data before importing.
                </p>
              </div>
              <div class="flex space-x-3">
                <button
                  @click="downloadTemplate"
                  class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  Download Template
                </button>
                <Link
                  :href="route('admin.clients.index')"
                  class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150"
                >
                  Back to Clients
                </Link>
              </div>
            </div>

            <!-- Import Form -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
              <form @submit.prevent="handleImport" class="space-y-6">
                <!-- File Upload -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    CSV File
                  </label>
                  <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                    <div class="space-y-1 text-center">
                      <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <div class="flex text-sm text-gray-600">
                        <label for="csv-file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                          <span>Upload a CSV file</span>
                          <input
                            id="csv-file"
                            ref="fileInput"
                            type="file"
                            accept=".csv,.txt"
                            @change="handleFileSelect"
                            class="sr-only"
                          />
                        </label>
                        <p class="pl-1">or drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">CSV files up to 10MB</p>
                    </div>
                  </div>
                  <div v-if="selectedFile" class="mt-3 p-3 bg-blue-50 rounded-md">
                    <div class="flex items-center">
                      <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                      </svg>
                      <span class="text-sm text-blue-700 font-medium">{{ selectedFile.name }}</span>
                      <span class="text-sm text-blue-600 ml-2">({{ formatFileSize(selectedFile.size) }})</span>
                      <button
                        type="button"
                        @click="clearFile"
                        class="ml-auto text-blue-400 hover:text-blue-600"
                      >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Import Options -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Import Options
                    </label>
                    <div class="space-y-3">
                      <label class="flex items-center">
                        <input
                          v-model="importOptions.skipInvalid"
                          type="checkbox"
                          class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        />
                        <span class="ml-2 text-sm text-gray-600">Skip invalid rows</span>
                      </label>
                      <label class="flex items-center">
                        <input
                          v-model="importOptions.updateExisting"
                          type="checkbox"
                          class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        />
                        <span class="ml-2 text-sm text-gray-600">Update existing clients</span>
                      </label>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Assign to User (Optional)
                    </label>
                    <select
                      v-model="importOptions.assignToUser"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                      <option value="">No assignment</option>
                      <option
                        v-for="user in availableUsers"
                        :key="user.id"
                        :value="user.id"
                      >
                        {{ user.name }} ({{ user.email }})
                      </option>
                    </select>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                  <button
                    type="button"
                    @click="previewImport"
                    :disabled="!selectedFile || processing"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150"
                  >
                    <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Preview Import
                  </button>
                  <button
                    type="submit"
                    :disabled="!selectedFile || processing"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                  >
                    <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Import Clients
                  </button>
                </div>
              </form>
            </div>

            <!-- Preview Results -->
            <div v-if="previewData" class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Import Preview</h3>
              
              <!-- Preview Statistics -->
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                  <div class="text-2xl font-bold text-blue-600">{{ previewData.total_rows }}</div>
                  <div class="text-sm text-blue-600">Total Rows</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                  <div class="text-2xl font-bold text-green-600">{{ previewData.valid_rows }}</div>
                  <div class="text-sm text-green-600">Valid Rows</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                  <div class="text-2xl font-bold text-red-600">{{ previewData.invalid_rows }}</div>
                  <div class="text-sm text-red-600">Invalid Rows</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                  <div class="text-2xl font-bold text-yellow-600">{{ previewData.errors?.length || 0 }}</div>
                  <div class="text-sm text-yellow-600">Total Errors</div>
                </div>
              </div>

              <!-- Preview Data Table -->
              <div v-if="previewData.preview_data?.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Row</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issues</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="row in previewData.preview_data" :key="row.row_number" :class="row.is_valid ? 'bg-white' : 'bg-red-50'">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ row.row_number }}</td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span v-if="row.is_valid" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          Valid
                        </span>
                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                          Invalid
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ `${row.data.first_name || ''} ${row.data.last_name || ''}`.trim() }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ row.data.email }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ row.data.phone }}</td>
                      <td class="px-6 py-4 text-sm text-red-600">
                        <div v-if="row.errors?.length" class="space-y-1">
                          <div v-for="error in row.errors.slice(0, 2)" :key="error" class="text-xs">{{ error }}</div>
                          <div v-if="row.errors.length > 2" class="text-xs text-gray-500">+{{ row.errors.length - 2 }} more</div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Errors Summary -->
              <div v-if="previewData.errors?.length" class="mt-6">
                <h4 class="text-md font-medium text-red-900 mb-3">Import Errors</h4>
                <div class="bg-red-50 border border-red-200 rounded-md p-4 max-h-40 overflow-y-auto">
                  <ul class="text-sm text-red-700 space-y-1">
                    <li v-for="error in previewData.errors.slice(0, 10)" :key="error">{{ error }}</li>
                    <li v-if="previewData.errors.length > 10" class="text-red-500 font-medium">
                      ... and {{ previewData.errors.length - 10 }} more errors
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Import Results -->
            <div v-if="importResult" class="bg-white border border-gray-200 rounded-lg p-6">
              <div class="flex items-center mb-4">
                <svg v-if="importResult.success" class="w-6 h-6 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <svg v-else class="w-6 h-6 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-lg font-medium" :class="importResult.success ? 'text-green-900' : 'text-red-900'">
                  Import {{ importResult.success ? 'Completed' : 'Failed' }}
                </h3>
              </div>

              <p class="text-gray-600 mb-4">{{ importResult.message }}</p>

              <div v-if="importResult.success" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-green-50 p-4 rounded-lg">
                  <div class="text-2xl font-bold text-green-600">{{ importResult.imported }}</div>
                  <div class="text-sm text-green-600">Imported</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                  <div class="text-2xl font-bold text-yellow-600">{{ importResult.skipped }}</div>
                  <div class="text-sm text-yellow-600">Skipped</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                  <div class="text-2xl font-bold text-red-600">{{ importResult.errors?.length || 0 }}</div>
                  <div class="text-sm text-red-600">Errors</div>
                </div>
              </div>

              <div v-if="importResult.errors?.length" class="mt-4">
                <h4 class="text-md font-medium text-red-900 mb-3">Import Errors</h4>
                <div class="bg-red-50 border border-red-200 rounded-md p-4 max-h-40 overflow-y-auto">
                  <ul class="text-sm text-red-700 space-y-1">
                    <li v-for="error in importResult.errors" :key="error">{{ error }}</li>
                  </ul>
                </div>
              </div>

              <div class="mt-6 flex justify-end">
                <Link
                  :href="route('admin.clients.index')"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                  View Clients
                </Link>
              </div>
            </div>

            <!-- Sample Headers Info -->
            <div class="bg-gray-50 rounded-lg p-6 mt-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">CSV Format Requirements</h3>
              <p class="text-sm text-gray-600 mb-4">
                Your CSV file should include the following columns. Required columns are marked with an asterisk (*).
              </p>
              <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 text-sm">
                <div v-for="header in sampleHeaders" :key="header" class="flex items-center">
                  <span class="font-mono bg-gray-200 px-2 py-1 rounded text-xs">{{ header }}</span>
                  <span v-if="['first_name', 'last_name', 'email'].includes(header)" class="text-red-500 ml-1">*</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  availableUsers: Array,
  sampleHeaders: Array
})

const selectedFile = ref(null)
const processing = ref(false)
const previewData = ref(null)
const importResult = ref(null)

const importOptions = reactive({
  skipInvalid: true,
  updateExisting: false,
  assignToUser: ''
})

const handleFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedFile.value = file
    previewData.value = null
    importResult.value = null
  }
}

const clearFile = () => {
  selectedFile.value = null
  previewData.value = null
  importResult.value = null
  if (document.getElementById('csv-file')) {
    document.getElementById('csv-file').value = ''
  }
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const downloadTemplate = () => {
  window.location.href = route('admin.clients.download-csv-template')
}

const previewImport = async () => {
  if (!selectedFile.value) return

  processing.value = true
  importResult.value = null

  const formData = new FormData()
  formData.append('csv_file', selectedFile.value)
  formData.append('preview', '1')
  formData.append('skip_invalid', importOptions.skipInvalid ? '1' : '0')
  formData.append('update_existing', importOptions.updateExisting ? '1' : '0')
  if (importOptions.assignToUser) {
    formData.append('assign_to_user', importOptions.assignToUser)
  }

  try {
    const response = await fetch(route('admin.clients.process-csv-import'), {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })

    const result = await response.json()
    previewData.value = result
  } catch (error) {
    console.error('Preview failed:', error)
    previewData.value = {
      success: false,
      message: 'Failed to preview import: ' + error.message
    }
  } finally {
    processing.value = false
  }
}

const handleImport = async () => {
  if (!selectedFile.value) return

  processing.value = true
  previewData.value = null

  const formData = new FormData()
  formData.append('csv_file', selectedFile.value)
  formData.append('skip_invalid', importOptions.skipInvalid ? '1' : '0')
  formData.append('update_existing', importOptions.updateExisting ? '1' : '0')
  if (importOptions.assignToUser) {
    formData.append('assign_to_user', importOptions.assignToUser)
  }

  try {
    const response = await fetch(route('admin.clients.process-csv-import'), {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })

    const result = await response.json()
    importResult.value = result
  } catch (error) {
    console.error('Import failed:', error)
    importResult.value = {
      success: false,
      message: 'Import failed: ' + error.message,
      imported: 0,
      skipped: 0,
      errors: [error.message]
    }
  } finally {
    processing.value = false
  }
}
</script>