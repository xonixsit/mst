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
          <button @click="addProject" :disabled="readonly"
            class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-4 py-2 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center">
            <PlusIcon class="w-4 h-4 mr-2" />
            Add Project
          </button>
        </div>
      </div>

      <!-- Projects List -->
      <div class="p-6">
        <!-- Empty State -->
        <div v-if="localData.length === 0" class="text-center py-6">
          <FolderIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">No projects</h3>
          <p class="mt-1 text-sm text-gray-500">Get started by adding your first tax project.</p>
          <div class="mt-6 flex justify-center">
            <button @click="addProject" :disabled="readonly"
              class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none inline-flex items-center">
              <PlusIcon class="w-4 h-4 mr-2" />
              Add Project
            </button>
          </div>
        </div>

        <!-- Projects Accordion -->
        <div v-else class="space-y-4">
          <div v-for="(project, index) in localData" :key="project.id || index"
            class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <!-- Project Header (Accordion Toggle) -->
            <div class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50"
              @click="toggleProject(index)">
              <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                  <div :class="getStatusBadgeClass(project?.status)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                    {{ formatStatus(project?.status) }}
                  </div>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-gray-900">
                    {{ project?.project_name || `Project ${index + 1}` }}
                  </h3>
                  <p class="text-sm text-gray-500">
                    {{ formatProjectType(project?.project_type) }}
                    <span v-if="project?.taxYear || project?.tax_year">• {{ project.taxYear || project.tax_year }}</span>
                    <span v-if="project?.dueDate || project?.due_date">• Due: {{ formatDate(project.dueDate || project.due_date) }}</span>
                  </p>
                </div>
              </div>

              <div class="flex items-center space-x-2">
                <button @click.stop="editProject(project)" :disabled="readonly"
                  class="text-blue-600 hover:text-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </button>
                <button @click.stop="confirmDeleteProject(index)" :disabled="readonly"
                  class="text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed">
                  <TrashIcon class="w-4 h-4" />
                </button>
                <svg
                  :class="['w-5 h-5 text-gray-400 transition-transform', expandedProjects[index] ? 'rotate-180' : '']"
                  fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>

            <!-- Project Details (Accordion Content) -->
            <div v-if="expandedProjects[index]" class="border-t border-gray-200 p-4 bg-gray-50">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                <!-- Priority -->
                <div>
                  <span class="font-medium text-gray-700">Priority:</span>
                  <span :class="getPriorityClass(project?.priority || 'medium')" class="ml-2 capitalize">{{ project?.priority || 'Medium' }}</span>
                </div>
                
                <!-- Project Type -->
                <div>
                  <span class="font-medium text-gray-700">Type:</span>
                  <span class="ml-2 text-gray-900 capitalize">{{ formatProjectType(project?.type || project?.project_type) || 'Not specified' }}</span>
                </div>
                
                <!-- Status -->
                <div>
                  <span class="font-medium text-gray-700">Status:</span>
                  <span class="ml-2">
                    <span :class="getStatusBadgeClass(project?.status)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize">
                      {{ formatStatus(project?.status) || 'Not specified' }}
                    </span>
                  </span>
                </div>
                
                <!-- Start Date -->
                <div>
                  <span class="font-medium text-gray-700">Start Date:</span>
                  <span class="ml-2 text-gray-900">{{ formatDate(project?.startDate || project?.start_date) || 'Not set' }}</span>
                </div>
                
                <!-- Due Date -->
                <div>
                  <span class="font-medium text-gray-700">Due Date:</span>
                  <span class="ml-2 text-gray-900">{{ formatDate(project?.dueDate || project?.due_date) || 'Not set' }}</span>
                </div>
                
                <!-- Completion Date -->
                <div>
                  <span class="font-medium text-gray-700">Completed:</span>
                  <span class="ml-2 text-gray-900">{{ formatDate(project?.completionDate || project?.completion_date) || 'Not completed' }}</span>
                </div>
                
                <!-- Estimated Hours -->
                <div>
                  <span class="font-medium text-gray-700">Est. Hours:</span>
                  <span class="ml-2 text-gray-900">{{ (project?.estimatedHours || project?.estimated_hours) ? `${project.estimatedHours || project.estimated_hours}h` : 'Not set' }}</span>
                </div>
                
                <!-- Actual Hours -->
                <div>
                  <span class="font-medium text-gray-700">Actual Hours:</span>
                  <span class="ml-2 text-gray-900">{{ (project?.actualHours || project?.actual_hours) ? `${project.actualHours || project.actual_hours}h` : 'Not tracked' }}</span>
                </div>
                
                <!-- Hours Variance -->
                <div v-if="(project?.estimatedHours || project?.estimated_hours) && (project?.actualHours || project?.actual_hours)">
                  <span class="font-medium text-gray-700">Variance:</span>
                  <span :class="getVarianceClass(project)" class="ml-2">
                    {{ getHoursVariance(project) }}
                  </span>
                </div>
                <div v-else>
                  <span class="font-medium text-gray-700">Variance:</span>
                  <span class="ml-2 text-gray-500">Not available</span>
                </div>
              </div>
              
              <!-- Description -->
              <div class="mt-4">
                <span class="font-medium text-gray-700">Description:</span>
                <p class="mt-1 text-gray-900">{{ project?.project_description || 'No description provided' }}</p>
              </div>
              
              <!-- Notes -->
              <div class="mt-4">
                <span class="font-medium text-gray-700">Notes:</span>
                <p class="mt-1 text-gray-900">{{ project?.notes || 'No notes added' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      </div>
    </div>

  <!-- Project Modal -->
  <ProjectModal :show="showProjectModal" :project="selectedProject" :errors="projectErrors" @close="closeProjectModal"
    @save="saveProject" />

  <!-- Delete Confirmation Modal -->
  <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeDeleteModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Delete Project
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Are you sure you want to delete this project? This action cannot be undone and will permanently remove the project from your records.
                </p>
                <div v-if="projectToDelete !== null && localData[projectToDelete]" class="mt-3 p-3 bg-gray-50 rounded-md">
                  <p class="text-sm font-medium text-gray-900">{{ localData[projectToDelete].project_name || `Project ${projectToDelete + 1}` }}</p>
                  <p class="text-sm text-gray-600">{{ formatProjectType(localData[projectToDelete].project_type) }}</p>
                  <p v-if="localData[projectToDelete].status" class="text-sm text-gray-600">Status: {{ formatStatus(localData[projectToDelete].status) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            type="button"
            @click="removeProject(projectToDelete)"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Delete Project
          </button>
          <button
            type="button"
            @click="closeDeleteModal"
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
  import { ref, reactive, computed, watch, onMounted } from 'vue'
  import { PlusIcon, TrashIcon, FolderIcon } from '@heroicons/vue/24/outline'
  import ProjectModal from './ProjectModal.vue'

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
  const expandedProjects = reactive({})

  // Modal state
  const showProjectModal = ref(false)
  const selectedProject = ref(null)
  const projectErrors = ref({})

  // Delete confirmation modal state
  const showDeleteModal = ref(false)
  const projectToDelete = ref(null)

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
    const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-gray-900 placeholder-gray-500 disabled:bg-gray-50 disabled:text-gray-500'
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
    selectedProject.value = null
    projectErrors.value = {}
    showProjectModal.value = true
  }

  const editProject = (project) => {
    if (props.readonly) return
    selectedProject.value = project
    projectErrors.value = {}
    showProjectModal.value = true
  }

  const closeProjectModal = () => {
    showProjectModal.value = false
    selectedProject.value = null
    projectErrors.value = {}
  }

  const saveProject = (projectData) => {
    // Validate project data
    const errors = validateProject(projectData)
    console.log(projectData)
    if (Object.keys(errors).length > 0) {
      projectErrors.value = errors
      return
    }

    if (projectData.id && localData.find(p => p.id === projectData.id)) {
      // Update existing project
      const index = localData.findIndex(p => p.id === projectData.id)
      if (index !== -1) {
        localData[index] = { ...projectData }
      }
    } else {
      // Add new project
      const newProject = {
        ...projectData,
        id: Date.now() // Temporary ID for frontend
      }
      localData.push(newProject)
    }

    emitUpdate()
    closeProjectModal()
  }

  const validateProject = (projectData) => {
    const errors = {}

    if (!projectData.project_name || projectData.project_name.trim().length === 0) {
      errors.project_name = 'Project name is required'
    }

    if (!projectData.project_type) {
      errors.project_type = 'Project type is required'
    }

    if (!projectData.status) {
      errors.status = 'Status is required'
    }

    return errors
  }

  const toggleProject = (index) => {
    expandedProjects[index] = !expandedProjects[index]
  }

  const confirmDeleteProject = (index) => {
    if (props.readonly) return
    projectToDelete.value = index
    showDeleteModal.value = true
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
    closeDeleteModal()
  }

  const closeDeleteModal = () => {
    showDeleteModal.value = false
    projectToDelete.value = null
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

    // Initialize expanded state for existing projects
    localData.forEach((_, index) => {
      if (expandedProjects[index] === undefined) {
        expandedProjects[index] = false
      }
    })
  }

  // Watch for external errors
  watch(() => props.errors, (newErrors) => {
    Object.keys(fieldErrors).forEach(key => delete fieldErrors[key])
    Object.assign(fieldErrors, newErrors)
  }, { deep: true })

  // Watch for prop changes
  watch(() => props.modelValue, initializeData, { deep: true })

  // Helper methods
  const formatStatus = (status) => {
    return status ? status.replace('_', ' ') : ''
  }

  const formatProjectType = (type) => {
    return type ? type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : ''
  }

  const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  }

  const getStatusBadgeClass = (status) => {
    const classes = {
      pending: 'bg-gray-100 text-gray-800',
      in_progress: 'bg-blue-100 text-blue-800',
      completed: 'bg-green-100 text-green-800',
      cancelled: 'bg-red-100 text-red-800'
    }
    return classes[status] || classes.pending
  }

  const getPriorityClass = (priority) => {
    const classes = {
      low: 'text-gray-600',
      medium: 'text-blue-600',
      high: 'text-orange-600',
      urgent: 'text-red-600'
    }
    return classes[priority] || classes.medium
  }

  const getHoursVariance = (project) => {
    const estimated = parseFloat(project?.estimatedHours || project?.estimated_hours || 0)
    const actual = parseFloat(project?.actualHours || project?.actual_hours || 0)
    const variance = actual - estimated
    return variance > 0 ? `+${variance}h` : `${variance}h`
  }

  const getVarianceClass = (project) => {
    const estimated = parseFloat(project?.estimatedHours || project?.estimated_hours)
    const actual = parseFloat(project?.actualHours || project?.actual_hours)
    const variance = actual - estimated
    return variance > 0 ? 'text-red-600' : variance < 0 ? 'text-green-600' : 'text-gray-600'
  }



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
  input:focus,
  select:focus,
  textarea:focus {
    ring-width: 2px;
    ring-color: rgb(59 130 246 / 0.5);
  }

  /* Disabled state styling */
  input:disabled,
  select:disabled,
  textarea:disabled {
    cursor: not-allowed;
  }

  /* Project card styling */
  .project-card {
    transition: all 0.2s ease-in-out;
  }

  .project-card:hover {
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  }
</style>