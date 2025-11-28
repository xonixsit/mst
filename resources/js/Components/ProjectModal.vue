<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="w-full">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                {{ isEditing ? 'Edit Project' : 'Add New Project' }}
              </h3>
              
              <form @submit.prevent="handleSubmit" class="space-y-4">
                <!-- Project Name and Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                      Project Name <span class="text-red-500">*</span>
                    </label>
                    <input
                      id="project_name"
                      v-model="formData.project_name"
                      type="text"
                      :class="inputClasses('project_name')"
                      placeholder="Enter project name"
                      required
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                  </div>

                  <div>
                    <label for="project_type" class="block text-sm font-medium text-gray-700 mb-1">
                      Project Type <span class="text-red-500">*</span>
                    </label>
                    <select
                      id="project_type"
                      v-model="formData.project_type"
                      :class="inputClasses('project_type')"
                      required
                    >
                      <option value="">Select project type</option>
                      <option value="tax_preparation">Tax Preparation</option>
                      <option value="tax_planning">Tax Planning</option>
                      <option value="audit_support">Audit Support</option>
                      <option value="consultation">Consultation</option>
                      <option value="bookkeeping">Bookkeeping</option>
                      <option value="payroll">Payroll Services</option>
                      <option value="business_formation">Business Formation</option>
                      <option value="other">Other</option>
                    </select>
                    <p v-if="errors.type" class="mt-1 text-sm text-red-600">{{ errors.type }}</p>
                  </div>
                </div>

                <!-- Description -->
                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                  </label>
                  <textarea
                    id="description"
                    v-model="formData.project_description"
                    rows="3"
                    :class="inputClasses('project_description')"
                    placeholder="Describe the project details and scope..."
                  ></textarea>
                  <p v-if="errors.project_description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                </div>

                <!-- Tax Year and Status -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label for="taxYear" class="block text-sm font-medium text-gray-700 mb-1">
                      Tax Year
                    </label>
                    <select
                      id="taxYear"
                      v-model="formData.taxYear"
                      :class="inputClasses('taxYear')"
                    >
                      <option value="">Select tax year</option>
                      <option v-for="year in availableTaxYears" :key="year" :value="year">
                        {{ year }}
                      </option>
                    </select>
                  </div>

                  <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                      Status <span class="text-red-500">*</span>
                    </label>
                    <select
                      id="status"
                      v-model="formData.status"
                      :class="inputClasses('status')"
                      required
                    >
                      <option value="">Select status</option>
                      <option value="pending">Pending</option>
                      <option value="in_progress">In Progress</option>
                      <option value="completed">Completed</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                    <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
                  </div>

                  <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                      Priority
                    </label>
                    <select
                      id="priority"
                      v-model="formData.priority"
                      :class="inputClasses('priority')"
                    >
                      <option value="">Select priority</option>
                      <option value="low">Low</option>
                      <option value="medium">Medium</option>
                      <option value="high">High</option>
                      <option value="urgent">Urgent</option>
                    </select>
                  </div>
                </div> 

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">
                      Start Date
                    </label>
                    <input
                      id="startDate"
                      v-model="formData.startDate"
                      type="date"
                      :class="inputClasses('startDate')"
                    />
                  </div>

                  <div>
                    <label for="dueDate" class="block text-sm font-medium text-gray-700 mb-1">
                      Due Date
                    </label>
                    <input
                      id="dueDate"
                      v-model="formData.dueDate"
                      type="date"
                      :class="inputClasses('dueDate')"
                    />
                  </div>

                  <div>
                    <label for="completionDate" class="block text-sm font-medium text-gray-700 mb-1">
                      Completion Date
                    </label>
                    <input
                      id="completionDate"
                      v-model="formData.completionDate"
                      type="date"
                      :class="inputClasses('completionDate')"
                    />
                  </div>
                </div>

                <!-- Hours -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="estimatedHours" class="block text-sm font-medium text-gray-700 mb-1">
                      Estimated Hours
                    </label>
                    <input
                      id="estimatedHours"
                      v-model="formData.estimatedHours"
                      type="number"
                      step="0.5"
                      min="0"
                      :class="inputClasses('estimatedHours')"
                      placeholder="0.0"
                    />
                  </div>

                  <div>
                    <label for="actualHours" class="block text-sm font-medium text-gray-700 mb-1">
                      Actual Hours
                    </label>
                    <input
                      id="actualHours"
                      v-model="formData.actualHours"
                      type="number"
                      step="0.5"
                      min="0"
                      :class="inputClasses('actualHours')"
                      placeholder="0.0"
                    />
                  </div>
                </div>

                <!-- Notes -->
                <div>
                  <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Notes
                  </label>
                  <textarea
                    id="notes"
                    v-model="formData.notes"
                    rows="3"
                    :class="inputClasses('notes')"
                    placeholder="Additional project notes..."
                  ></textarea>
                </div>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Modal footer -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            type="button"
            @click="handleSubmit"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            {{ isEditing ? 'Update' : 'Add' }} Project
          </button>
          <button
            type="button"
            @click="closeModal"
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
import { ref, reactive, computed, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  project: {
    type: Object,
    default: null
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close', 'save'])

const isEditing = computed(() => props.project && props.project.id)

const formData = reactive({
  id: null,
  project_name: '',
  project_description: '',
  project_type: '',
  taxYear: new Date().getFullYear(),
  status: 'pending',
  priority: 'medium',
  startDate: '',
  dueDate: '',
  completionDate: '',
  estimatedHours: '',
  actualHours: '',
  notes: ''
})

const availableTaxYears = computed(() => {
  const currentYear = new Date().getFullYear()
  const years = []
  for (let i = currentYear + 1; i >= currentYear - 5; i--) {
    years.push(i)
  }
  return years
})

const inputClasses = (fieldName) => {
  const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-gray-900 placeholder-gray-500'
  const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
  
  return props.errors[fieldName] ? `${baseClasses} ${errorClasses}` : baseClasses
}

const resetForm = () => {
  Object.assign(formData, {
    id: null,
    project_name: '',
    project_description: '',
    project_type: '',
    taxYear: new Date().getFullYear(),
    status: 'pending',
    priority: 'medium',
    startDate: '',
    dueDate: '',
    completionDate: '',
    estimatedHours: '',
    actualHours: '',
    notes: ''
  })
}

const loadProject = () => {
  if (props.project) {
    // Map project fields to form fields
    Object.assign(formData, {
      id: props.project.id,
      project_name: props.project.project_name || '',
      project_description: props.project.project_description || '',
      project_type: props.project.project_type || '',
      taxYear: props.project.taxYear || props.project.tax_year || new Date().getFullYear(),
      status: props.project.status || 'pending',
      priority: props.project.priority || 'medium',
      startDate: props.project.startDate || props.project.start_date || '',
      dueDate: props.project.dueDate || props.project.due_date || '',
      completionDate: props.project.completionDate || props.project.completion_date || '',
      estimatedHours: props.project.estimatedHours || props.project.estimated_hours || '',
      actualHours: props.project.actualHours || props.project.actual_hours || '',
      notes: props.project.notes || ''
    })
  } else {
    resetForm()
  }
}

const handleSubmit = () => {
  emit('save', { ...formData })
}

const closeModal = () => {
  emit('close')
}

watch(() => props.show, (newValue) => {
  if (newValue) {
    loadProject()
  }
})

watch(() => props.project, loadProject, { deep: true })
</script>