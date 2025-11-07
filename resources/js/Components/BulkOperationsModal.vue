<template>
  <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Bulk Operations</h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="mb-4">
        <p class="text-sm text-gray-600">
          {{ selectedCount }} client{{ selectedCount !== 1 ? 's' : '' }} selected
        </p>
      </div>

      <div class="space-y-4">
        <!-- Operation Selection -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Select Operation</label>
          <select
            v-model="selectedOperation"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            @change="resetOperationData"
          >
            <option value="">Choose an operation...</option>
            <option value="activate">Activate Clients</option>
            <option value="deactivate">Deactivate Clients</option>
            <option value="archive">Archive Clients</option>
            <option value="assign_user">Assign to User</option>
            <option value="send_email">Send Email</option>
            <option value="request_documents">Request Documents</option>
            <option value="export_selected">Export Selected</option>
            <option value="delete">Delete Clients</option>
          </select>
        </div>

        <!-- User Assignment -->
        <div v-if="selectedOperation === 'assign_user'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Assign to User</label>
          <select
            v-model="operationData.user_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Select a user...</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }} ({{ user.email }})
            </option>
          </select>
        </div>

        <!-- Email Composition -->
        <div v-if="selectedOperation === 'send_email'" class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Subject</label>
            <input
              v-model="operationData.subject"
              type="text"
              placeholder="Enter email subject..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Message</label>
            <textarea
              v-model="operationData.message"
              rows="4"
              placeholder="Enter email message..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Template</label>
            <select
              v-model="selectedTemplate"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="applyTemplate"
            >
              <option value="">Choose a template...</option>
              <option value="document_request">Document Request</option>
              <option value="appointment_reminder">Appointment Reminder</option>
              <option value="status_update">Status Update</option>
              <option value="welcome">Welcome Message</option>
            </select>
          </div>
        </div>

        <!-- Document Request -->
        <div v-if="selectedOperation === 'request_documents'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Document Types</label>
          <div class="space-y-2">
            <label v-for="docType in documentTypes" :key="docType.value" class="flex items-center">
              <input
                type="checkbox"
                :value="docType.value"
                v-model="operationData.document_types"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              />
              <span class="ml-2 text-sm text-gray-700">{{ docType.label }}</span>
            </label>
          </div>
        </div>

        <!-- Export Options -->
        <div v-if="selectedOperation === 'export_selected'">
          <label class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
          <select
            v-model="operationData.format"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="excel">Excel (.xlsx)</option>
            <option value="csv">CSV (.csv)</option>
            <option value="pdf">PDF (.pdf)</option>
          </select>
        </div>

        <!-- Confirmation for Delete -->
        <div v-if="selectedOperation === 'delete'" class="bg-red-50 border border-red-200 rounded-md p-3">
          <div class="flex">
            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Warning</h3>
              <p class="text-sm text-red-700 mt-1">
                This action will permanently delete the selected clients and cannot be undone.
              </p>
            </div>
          </div>
          <div class="mt-3">
            <label class="flex items-center">
              <input
                type="checkbox"
                v-model="operationData.confirm_delete"
                class="rounded border-gray-300 text-red-600 focus:ring-red-500"
              />
              <span class="ml-2 text-sm text-red-700">I understand this action cannot be undone</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-3 mt-6">
        <button
          @click="$emit('close')"
          class="px-4 py-2 text-gray-600 hover:text-gray-800"
        >
          Cancel
        </button>
        <button
          @click="executeOperation"
          :disabled="!canExecute || isExecuting"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ isExecuting ? 'Processing...' : 'Execute Operation' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch } from 'vue'

export default {
  name: 'BulkOperationsModal',
  props: {
    show: Boolean,
    selectedClients: Array,
    users: {
      type: Array,
      default: () => []
    }
  },
  emits: ['close', 'execute'],
  setup(props, { emit }) {
    const selectedOperation = ref('')
    const selectedTemplate = ref('')
    const isExecuting = ref(false)
    
    const operationData = ref({
      user_id: null,
      subject: '',
      message: '',
      document_types: [],
      format: 'excel',
      confirm_delete: false
    })

    const documentTypes = [
      { value: 'w2', label: 'W-2 Forms' },
      { value: '1099', label: '1099 Forms' },
      { value: 'receipts', label: 'Receipts' },
      { value: 'bank_statements', label: 'Bank Statements' },
      { value: 'tax_returns', label: 'Previous Tax Returns' },
      { value: 'id_documents', label: 'ID Documents' }
    ]

    const emailTemplates = {
      document_request: {
        subject: 'Document Request - Tax Preparation',
        message: 'Dear [Client Name],\n\nWe need the following documents to proceed with your tax preparation:\n\n[Document List]\n\nPlease upload these documents at your earliest convenience.\n\nBest regards,\n[Your Name]'
      },
      appointment_reminder: {
        subject: 'Appointment Reminder',
        message: 'Dear [Client Name],\n\nThis is a reminder about your upcoming appointment on [Date] at [Time].\n\nPlease let us know if you need to reschedule.\n\nBest regards,\n[Your Name]'
      },
      status_update: {
        subject: 'Status Update - Your Tax Return',
        message: 'Dear [Client Name],\n\nWe wanted to update you on the status of your tax return preparation.\n\n[Status Details]\n\nIf you have any questions, please don\'t hesitate to contact us.\n\nBest regards,\n[Your Name]'
      },
      welcome: {
        subject: 'Welcome to Our Tax Services',
        message: 'Dear [Client Name],\n\nWelcome to our tax consulting services! We\'re excited to work with you.\n\nTo get started, please log into your client portal and complete your information.\n\nBest regards,\n[Your Name]'
      }
    }

    const selectedCount = computed(() => props.selectedClients.length)

    const canExecute = computed(() => {
      if (!selectedOperation.value) return false
      
      switch (selectedOperation.value) {
        case 'assign_user':
          return operationData.value.user_id
        case 'send_email':
          return operationData.value.subject && operationData.value.message
        case 'request_documents':
          return operationData.value.document_types.length > 0
        case 'delete':
          return operationData.value.confirm_delete
        default:
          return true
      }
    })

    const resetOperationData = () => {
      operationData.value = {
        user_id: null,
        subject: '',
        message: '',
        document_types: [],
        format: 'excel',
        confirm_delete: false
      }
      selectedTemplate.value = ''
    }

    const applyTemplate = () => {
      if (selectedTemplate.value && emailTemplates[selectedTemplate.value]) {
        const template = emailTemplates[selectedTemplate.value]
        operationData.value.subject = template.subject
        operationData.value.message = template.message
      }
    }

    const executeOperation = async () => {
      if (!canExecute.value) return

      isExecuting.value = true
      
      try {
        emit('execute', {
          operation: selectedOperation.value,
          client_ids: props.selectedClients,
          data: operationData.value
        })
      } finally {
        isExecuting.value = false
      }
    }

    // Reset form when modal is closed
    watch(() => props.show, (newValue) => {
      if (!newValue) {
        selectedOperation.value = ''
        resetOperationData()
      }
    })

    return {
      selectedOperation,
      selectedTemplate,
      operationData,
      documentTypes,
      selectedCount,
      canExecute,
      isExecuting,
      resetOperationData,
      applyTemplate,
      executeOperation
    }
  }
}
</script>