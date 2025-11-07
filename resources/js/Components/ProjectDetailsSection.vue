<template>
  <div class="project-details-section">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <!-- Section Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">Project Details</h2>
            <p class="text-sm text-gray-600 mt-1">Tax-related projects and their current status</p>
          </div>
          <button
            @click="addProject"
            :disabled="readonly"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <PlusIcon class="w-4 h-4 mr-1" />
            Add Project
          </button>
        </div>
      </div>

      <!-- Projects List -->
      <div class="p-6">
        <!-- Empty State -->
        <div v-if="localData.length === 0" class="text-center py-12">
          <FolderIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No projects</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by adding your first tax project.</p>
          <div class="mt-6">
            <button
              @click="addProject"
              :disabled="readonly"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <PlusIcon class="w-4 h-4 mr-2" />
              Add Project
            </button>
          </div>
        </div>

        <!-- Projects Grid -->
        <div v-else class="space-y-6">
          <div
            v-for="(project, index) in localData"
            :key="project.id || index"
            class="bg-gray-50 rounded-lg border border-gray-200 p-6"
          >
            <!-- Project Header -->
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900">
                Project {{ index + 1 }}
              </h3>
              <button
                @click="removeProject(index)"
                :disabled="readonly"
                class="text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <TrashIcon class="w-5 h-5" />
              </button>
            </div>

            <!-- Project Form -->
            <form class="space-y-4">
              <!-- Project Name and Type -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label :for="`projectName-${index}`" class="block text-sm font-medium text-gray-700 mb-1">
                    Project Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    :id="`projectName-${index}`"
                    v-model="project.name"
                    type="text"
                    :class="inputClasses(`projects.${index}.name`)"
                    placeholder="Enter project name"
                    :disabled="readonly"
                    @blur="validateProjectField(index, 'name')"
                    @input="handleProjectInput(index, 'name', $event.target.value)"
                  />
                  <p v-if="getProjectError(index, 'name')" class="mt-1 text-sm text-red-600">
                    {{ getProjectError(index, 'name') }}
                  </p>
                </div>

                <div>
                  <label :for="`projectType-${index}`" class="block text-sm font-medium text-gray-700 mb-1">
                    Project Type <span class="text-red-500">*</span>
                  </label>
                  <select
                    :id="`projectType-${index}`"
                    v-model="project.type"
                    :class="inputClasses(`projects.${index}.type`)"
                    :disabled="readonly"
                    @change="handleProjectInput(index, 'type', $event.target.value)"
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
                  <p v-if="getProjectError(index, 'type')" class="mt-1 text-sm text-red-600">
                    {{ getProjectError(index, 'type') }}
                  </p>
                </div>
              </div>

              <!-- Tax Year and Status -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label :for="`taxYear-${index}`" class="block text-sm font-medium text-gray-700 mb-1">
                    Tax Year
                  </label>
                  <select
                    :id="`taxYear-${index}`"
                    v-model="project.taxYear"
                    :class="inputClasses(`projects.${index}.taxYear`)"
                    :disabled="readonly"
                    @change="handleProjectInput(index, 'taxYear', $event.target.value)"
                  >
                    <option value="">Select tax year</option>
                    <option v-for="year in availableTaxYears" :key="year" :value="year">
                      {{ year }}
                    </option>
                  </select>
                </div>

                <div>
                  <label :for="`status-${index}`" class="block text-sm font-medium text-gray-700 mb-1">
                    Status <span class="text-red-500">*</span>
                  </label>
                  <select
                    :id="`status-${index}`"
                    v-model="project.status"
                    :class="inputClasses(`projects.${index}.status`)"
                    :disabled="readonly"
                    @change="handleProjectInput(index, 'status', $event.target.value)"
                  >
                    <option value="">Select status</option>
                    <option value="not_started">Not Started</option>
                    <option value="in_progress">In Progress</option>
                    <option value="under_review">Under Review</option>
                    <option value="completed">Completed</option>
                    <option value="on_hold">On Hold</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                  <p v-if="getProjectError(index, 'status')" class="mt-1 text-sm text-red-600">
                    {{ getProjectError(index, 'status') }}
                  </p>
                </div>

                <div>
                  <label :for="`priority-${index}`" class="block text-sm font-medium text-gray-700 mb-1">
                    Priority
                  </label>
                  <select
                    :id="`priority-${index}`"
                    v-model="project.priority"
                    :class="inputClasses(`projects.${index}.priority`)"
                    :disabled="readonly"
                    @change="handleProjectInput(index, 'priority', $event.target.value)"
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
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label :for="`startDate-${index}`" class="block text-sm font-medium text-gray-700 mb-1">
                    Start Date
                  </label>
                  <input
                    :id="`startDate-${index}`"
                    v-model="project.startDate"
                    type="date"
                    :class="inputClasses(`projects.${index}.startDate`)"
                    :disabled="readonly"
                    @input="handleProjectInput(index, 'startDate', $event.target.value)"
                  />
                </div>

                <div>
                  <label :for="`dueDate-${index}`" class="block text-sm font-medium text-gray-700 mb-1">
                    Due Date
                  </label>
                  <input
                    :id="`dueDate-${index}`"
                    v-model="project.dueDate"
                    type="date"
                    :class="inputClasses(`projects.${index}.dueDate`)"
                    :disabled="readonly"
                    @input="handleProjectInput(index, 'dueDate', $event.target.value)"
                  />
                </div>
              </div>

              <!-- Description -->
              <div>
                <label :for="`description-${index}`" class="block text-sm font-medium text-gray-700 mb-1">
                  Description
                </label>
                <textarea
                  :id="`description-${index}`"
                  v-model="project.description"
                  rows="3"
                  :class="inputClasses(`projects.${index}.description`)"
                  placeholder="Describe the project scope and requirements..."
                  :disabled="readonly"
                  @input="handleProjectInput(index, 'description', $event.target.value)"
                ></textarea>
              </div>

              <!-- Status Badge -->
              <div class="flex items-center justify-between pt-2">
                <div class="flex items-center space-x-2">
                  <span class="text-sm text-gray-500">Status:</span>
                  <span 
                    :class="getStatusBadgeClasses(project.status)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  >
                    {{ getStatusLabel(project.status) }}
                  </span>
                </div>
                <div v-if="project.priority" class="flex items-center space-x-2">
                  <span class="text-sm text-gray-500">Priority:</span>
                  <span 
                    :class="getPriorityBadgeClasses(project.priority)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  >
                    {{ project.priority.charAt(0).toUpperCase() + project.priority.slice(1) }}
                  </span>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { PlusIcon, TrashIcon, FolderIcon } from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  readonly: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['update:modelValue', 'update', 'validate'])

// Local reactive data
const localData = reactive([])

// Field validation errors
const fieldErrors = reactive({})

// Available tax years (current year and previous 5 years)
const availableTaxYears = computed(() => {
  const currentYear = new Date().getFullYear()
  const years = []
  for (let i = 0; i <= 5; i++) {
    years.push(currentYear - i)
  }
  return years
})

// Computed properties
const inputClasses = (fieldPath) => {
  const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-50 disabled:text-gray-500'
  const errorClasses = 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500'
  
  return fieldErrors[fieldPath] ? `${baseClasses} ${errorClasses}` : baseClasses
}

// Helper methods
const getProjectError = (index, field) => {
  return fieldErrors[`projects.${index}.${field}`]
}

const getStatusLabel = (status) => {
  const labels = {
    'not_started': 'Not Started',
    'in_progress': 'In Progress',
    'under_review': 'Under Review',
    'completed': 'Completed',
    'on_hold': 'On Hold',
    'cancelled': 'Cancelled'
  }
  return labels[status] || status
}

const getStatusBadgeClasses = (status) => {
  const classes = {
    'not_started': 'bg-gray-100 text-gray-800',
    'in_progress': 'bg-blue-100 text-blue-800',
    'under_review': 'bg-yellow-100 text-yellow-800',
    'completed': 'bg-green-100 text-green-800',
    'on_hold': 'bg-orange-100 text-orange-800',
    'cancelled': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getPriorityBadgeClasses = (priority) => {
  const classes = {
    'low': 'bg-gray-100 text-gray-800',
    'medium': 'bg-blue-100 text-blue-800',
    'high': 'bg-orange-100 text-orange-800',
    'urgent': 'bg-red-100 text-red-800'
  }
  return classes[priority] || 'bg-gray-100 text-gray-800'
}

// Project management methods
const addProject = () => {
  if (props.readonly) return
  
  const newProject = {
    id: Date.now(), // Temporary ID
    name: '',
    type: '',
    taxYear: new Date().getFullYear(),
    status: 'not_started',
    priority: 'medium',
    startDate: '',
    dueDate: '',
    description: ''
  }
  
  localData.push(newProject)
  emitUpdate()
}

const removeProject = (index) => {
  if (props.readonly) return
  
  localData.splice(index, 1)
  
  // Clean up errors for removed project
  Object.keys(fieldErrors).forEach(key => {
    if (key.startsWith(`projects.${index}.`)) {
      delete fieldErrors[key]
    }
  })
  
  // Reindex errors for remaining projects
  const newErrors = {}
  Object.keys(fieldErrors).forEach(key => {
    const match = key.match(/^projects\.(\d+)\.(.+)$/)
    if (match) {
      const errorIndex = parseInt(match[1])
      const field = match[2]
      if (errorIndex > index) {
        newErrors[`projects.${errorIndex - 1}.${field}`] = fieldErrors[key]
      } else if (errorIndex < index) {
        newErrors[key] = fieldErrors[key]
      }
    } else {
      newErrors[key] = fieldErrors[key]
    }
  })
  
  Object.keys(fieldErrors).forEach(key => delete fieldErrors[key])
  Object.assign(fieldErrors, newErrors)
  
  emitUpdate()
}

// Validation methods
const validateProjectField = (index, fieldName) => {
  const project = localData[index]
  const value = project[fieldName]
  const fieldPath = `projects.${index}.${fieldName}`
  let error = null

  switch (fieldName) {
    case 'name':
      if (!value || value.trim().length === 0) {
        error = 'Project name is required'
      } else if (value.length > 100) {
        error = 'Project name must be less than 100 characters'
      }
      break

    case 'type':
      if (!value) {
        error = 'Project type is required'
      }
      break

    case 'status':
      if (!value) {
        error = 'Project status is required'
      }
      break
  }

  if (error) {
    fieldErrors[fieldPath] = error
  } else {
    delete fieldErrors[fieldPath]
  }

  // Emit validation results
  emit('validate', fieldErrors)
  
  return !error
}

// Input handlers
const handleProjectInput = (index, fieldName, value) => {
  localData[index][fieldName] = value
  
  // Clear field error when user starts typing
  const fieldPath = `projects.${index}.${fieldName}`
  if (fieldErrors[fieldPath]) {
    delete fieldErrors[fieldPath]
  }
  
  emitUpdate()
}

const emitUpdate = () => {
  const dataToEmit = localData.map(project => ({ ...project }))
  emit('update:modelValue', dataToEmit)
  emit('update', dataToEmit)
}

// Initialize local data from props
const initializeData = () => {
  // Clear existing data
  localData.splice(0, localData.length)
  
  if (props.modelValue && Array.isArray(props.modelValue)) {
    props.modelValue.forEach(project => {
      localData.push({ ...project })
    })
  }
}

// Watch for external errors
watch(() => props.errors, (newErrors) => {
  Object.keys(fieldErrors).forEach(key => delete fieldErrors[key])
  Object.assign(fieldErrors, newErrors)
}, { deep: true })

// Watch for prop changes
watch(() => props.modelValue, initializeData, { deep: true })

// Initialize on mount
onMounted(() => {
  initializeData()
})
</script>

<style scoped>
.project-details-section {
  @apply max-w-none;
}

/* Custom styles for form elements */
input:focus, select:focus, textarea:focus {
  @apply ring-2 ring-blue-500 ring-opacity-50;
}

/* Disabled state styling */
input:disabled, select:disabled, textarea:disabled {
  @apply cursor-not-allowed;
}

/* Project card styling */
.project-card {
  transition: all 0.2s ease-in-out;
}

.project-card:hover {
  @apply shadow-md;
}
</style>