<template>
  <div v-if="show" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-start justify-center p-4 pt-8 overflow-y-auto">
    <div class="relative w-full max-w-4xl bg-white shadow-2xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all flex flex-col mb-8">
      <!-- Enhanced Header -->
      <div class="bg-gradient-to-r from-slate-50 via-indigo-50 to-purple-50 px-6 py-5 border-b border-gray-200 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-16 bg-gradient-to-bl from-indigo-100/40 to-transparent rounded-bl-full"></div>
        <div class="relative flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 via-indigo-900 to-purple-900 bg-clip-text text-transparent">
                {{ isEditing ? 'Edit Project' : 'Add New Project' }}
              </h3>
              <p class="text-sm text-gray-600 font-medium">{{ isEditing ? 'Update project details and information' : 'Create a new project for your client' }}</p>
            </div>
          </div>
          <button
            @click="closeModal"
            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Enhanced Form -->
      <div class="p-8 max-h-[70vh] overflow-y-auto">
        <form @submit.prevent="handleSubmit" class="space-y-8">
              
          <!-- Basic Information Section -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-blue-900">Basic Information</h4>
            </div>
            
            <div class="space-y-6">
              <!-- Project Name and Type -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <label for="project_name" class="block text-sm font-semibold text-blue-700">
                    Project Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    id="project_name"
                    v-model="formData.project_name"
                    type="text"
                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-900 placeholder-gray-500"
                    placeholder="Enter project name"
                    required
                  />
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>

                <div class="space-y-2">
                  <label for="project_type" class="block text-sm font-semibold text-blue-700">
                    Project Type <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="project_type"
                    v-model="formData.project_type"
                    class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white text-gray-900"
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
              <div class="space-y-2">
                <label for="description" class="block text-sm font-semibold text-blue-700">
                  Description
                </label>
                <textarea
                  id="description"
                  v-model="formData.project_description"
                  rows="3"
                  class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-900 placeholder-gray-500 resize-none"
                  placeholder="Describe the project details and scope..."
                ></textarea>
                <p v-if="errors.project_description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
              </div>
            </div>
          </div>

          <!-- Project Settings Section -->
          <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-green-900">Project Settings</h4>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="space-y-2">
                <label for="taxYear" class="block text-sm font-semibold text-green-700">
                  Tax Year
                </label>
                <select
                  id="taxYear"
                  v-model="formData.taxYear"
                  class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white text-gray-900"
                >
                  <option value="2025">2025</option>
                  <option v-for="year in availableTaxYears" :key="year" :value="year">
                    {{ year }}
                  </option>
                </select>
              </div>

              <div class="space-y-2">
                <label for="status" class="block text-sm font-semibold text-green-700">
                  Status <span class="text-red-500">*</span>
                </label>
                <select
                  id="status"
                  v-model="formData.status"
                  class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white text-gray-900"
                  required
                >
                  <option value="pending">Pending</option>
                  <option value="in_progress">In Progress</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
                <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</p>
              </div>

              <div class="space-y-2">
                <label for="priority" class="block text-sm font-semibold text-green-700">
                  Priority
                </label>
                <select
                  id="priority"
                  v-model="formData.priority"
                  class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white text-gray-900"
                >
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
              </div>
            </div>
          </div> 

          <!-- Timeline Section -->
          <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-6 border border-purple-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-purple-900">Timeline</h4>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="space-y-2">
                <label for="startDate" class="block text-sm font-semibold text-purple-700">
                  Start Date
                </label>
                <input
                  id="startDate"
                  v-model="formData.startDate"
                  type="date"
                  class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900"
                  placeholder="mm/dd/yyyy"
                />
              </div>

              <div class="space-y-2">
                <label for="dueDate" class="block text-sm font-semibold text-purple-700">
                  Due Date
                </label>
                <input
                  id="dueDate"
                  v-model="formData.dueDate"
                  type="date"
                  class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900"
                  placeholder="mm/dd/yyyy"
                />
              </div>

              <div class="space-y-2">
                <label for="completionDate" class="block text-sm font-semibold text-purple-700">
                  Completion Date
                </label>
                <input
                  id="completionDate"
                  v-model="formData.completionDate"
                  type="date"
                  class="w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 text-gray-900"
                  placeholder="mm/dd/yyyy"
                />
              </div>
            </div>
          </div>

          <!-- Hours Tracking Section -->
          <div class="bg-gradient-to-br from-orange-50 to-amber-100 rounded-xl p-6 border border-orange-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-orange-900">Hours Tracking</h4>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label for="estimatedHours" class="block text-sm font-semibold text-orange-700">
                  Estimated Hours
                </label>
                <input
                  id="estimatedHours"
                  v-model="formData.estimatedHours"
                  type="number"
                  step="0.5"
                  min="0"
                  class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 text-gray-900"
                  placeholder="0.0"
                />
              </div>

              <div class="space-y-2">
                <label for="actualHours" class="block text-sm font-semibold text-orange-700">
                  Actual Hours
                </label>
                <input
                  id="actualHours"
                  v-model="formData.actualHours"
                  type="number"
                  step="0.5"
                  min="0"
                  class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 text-gray-900"
                  placeholder="0.0"
                />
              </div>
            </div>
          </div>

          <!-- Notes Section -->
          <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200">
            <div class="flex items-center mb-6">
              <div class="w-10 h-10 bg-gray-500 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </div>
              <h4 class="text-lg font-bold text-gray-900">Additional Notes</h4>
            </div>
            
            <div class="space-y-2">
              <label for="notes" class="block text-sm font-semibold text-gray-700">
                Notes
              </label>
              <textarea
                id="notes"
                v-model="formData.notes"
                rows="3"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200 text-gray-900 placeholder-gray-500 resize-none"
                placeholder="Additional project notes..."
              ></textarea>
            </div>
          </div>
        </form>
      </div>

      <!-- Enhanced Actions -->
      <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 bg-white px-8 pb-8">
        <button
          type="button"
          @click="closeModal"
          class="bg-gradient-to-r from-gray-500 to-slate-600 hover:from-gray-600 hover:to-slate-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
          Cancel
        </button>
        <button
          type="button"
          @click="handleSubmit"
          class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          {{ isEditing ? 'Update' : 'Add' }} Project
        </button>
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