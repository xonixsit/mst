<template>
  <div class="client-information-manager">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">Client Information Manager</h1>
          <p class="text-sm text-gray-600 mt-1">Manage comprehensive client information and data</p>
        </div>
        <div class="flex items-center space-x-3">
          <!-- Progress Indicator -->
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Progress:</span>
            <div class="w-32 bg-gray-200 rounded-full h-2">
              <div 
                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: `${completionPercentage}%` }"
              ></div>
            </div>
            <span class="text-sm font-medium text-gray-900">{{ completionPercentage }}%</span>
          </div>
          <!-- Auto-save Status -->
          <div class="flex items-center space-x-2">
            <div 
              :class="[
                'w-2 h-2 rounded-full',
                autoSaveStatus === 'saving' ? 'bg-yellow-500' : 
                autoSaveStatus === 'saved' ? 'bg-green-500' : 'bg-gray-300'
              ]"
            ></div>
            <span class="text-xs text-gray-500">
              {{ autoSaveStatusText }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white border-b border-gray-200">
      <nav class="flex space-x-8 px-6" aria-label="Tabs">
        <button
          v-for="section in sections"
          :key="section.id"
          @click="activeSection = section.id"
          :class="[
            'py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap',
            activeSection === section.id
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          ]"
        >
          <div class="flex items-center space-x-2">
            <component :is="section.icon" class="w-4 h-4" />
            <span>{{ section.name }}</span>
            <div 
              v-if="section.completed"
              class="w-2 h-2 bg-green-500 rounded-full"
            ></div>
          </div>
        </button>
      </nav>
    </div>

    <!-- Content Area -->
    <div class="flex-1 overflow-hidden">
      <div class="h-full flex">
        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
          <div class="p-6">
            <!-- Personal Details Section -->
            <PersonalDetailsSection
              v-if="activeSection === 'personal'"
              v-model="formData.personal"
              :errors="errors.personal"
              @update="handleSectionUpdate('personal', $event)"
              @validate="handleSectionValidation('personal', $event)"
            />

            <!-- Spouse Details Section -->
            <SpouseDetailsSection
              v-if="activeSection === 'spouse'"
              v-model="formData.spouse"
              :errors="errors.spouse"
              :show-spouse="formData.personal?.maritalStatus === 'married'"
              @update="handleSectionUpdate('spouse', $event)"
              @validate="handleSectionValidation('spouse', $event)"
            />

            <!-- Employee Information Section -->
            <EmployeeInformationSection
              v-if="activeSection === 'employee'"
              v-model="formData.employee"
              :errors="errors.employee"
              @update="handleSectionUpdate('employee', $event)"
              @validate="handleSectionValidation('employee', $event)"
            />

            <!-- Project Details Section -->
            <ProjectDetailsSection
              v-if="activeSection === 'projects'"
              v-model="formData.projects"
              :errors="errors.projects"
              @update="handleSectionUpdate('projects', $event)"
              @validate="handleSectionValidation('projects', $event)"
            />

            <!-- Assets Management Section -->
            <AssetsManagementSection
              v-if="activeSection === 'assets'"
              v-model="formData.assets"
              :errors="errors.assets"
              @update="handleSectionUpdate('assets', $event)"
              @validate="handleSectionValidation('assets', $event)"
            />

            <!-- Expenses Management Section -->
            <ExpensesManagementSection
              v-if="activeSection === 'expenses'"
              v-model="formData.expenses"
              :errors="errors.expenses"
              @update="handleSectionUpdate('expenses', $event)"
              @validate="handleSectionValidation('expenses', $event)"
            />
          </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="w-80 bg-gray-50 border-l border-gray-200 p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Summary</h3>
          
          <!-- Section Completion Status -->
          <div class="space-y-3">
            <div 
              v-for="section in sections"
              :key="section.id"
              class="flex items-center justify-between p-3 bg-white rounded-lg border"
            >
              <div class="flex items-center space-x-3">
                <component :is="section.icon" class="w-4 h-4 text-gray-500" />
                <span class="text-sm font-medium text-gray-900">{{ section.name }}</span>
              </div>
              <div 
                :class="[
                  'w-3 h-3 rounded-full',
                  section.completed ? 'bg-green-500' : 'bg-gray-300'
                ]"
              ></div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="mt-6 space-y-3">
            <button
              @click="saveData"
              :disabled="isSaving"
              class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ isSaving ? 'Saving...' : (props.client ? 'Update Client' : 'Create Client') }}
            </button>
            
            <button
              v-if="props.isAdmin"
              @click="cancelEdit"
              class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700"
            >
              Cancel
            </button>
            
            <button
              v-if="props.client && !props.isAdmin"
              @click="exportData"
              class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700"
            >
              Export Data
            </button>
            
            <button
              v-if="isAdminContext && props.client"
              @click="markComplete"
              class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700"
            >
              Mark as Complete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted, onUnmounted, defineAsyncComponent, shallowRef } from 'vue'
import { router } from '@inertiajs/vue3'
import cacheService from '../Services/CacheService.js'
import { debounce, createAutoSave, measurePerformance, createComponentTracker } from '../Utils/PerformanceUtils.js'

// Lazy load components for better performance
const PersonalDetailsSection = defineAsyncComponent(() => import('./PersonalDetailsSection.vue'))
const SpouseDetailsSection = defineAsyncComponent(() => import('./SpouseDetailsSection.vue'))
const EmployeeInformationSection = defineAsyncComponent(() => import('./EmployeeInformationSection.vue'))
const ProjectDetailsSection = defineAsyncComponent(() => import('./ProjectDetailsSection.vue'))
const AssetsManagementSection = defineAsyncComponent(() => import('./AssetsManagementSection.vue'))
const ExpensesManagementSection = defineAsyncComponent(() => import('./ExpensesManagementSection.vue'))

// Icons (using heroicons or similar)
import { 
  UserIcon, 
  HeartIcon, 
  BriefcaseIcon, 
  FolderIcon, 
  CurrencyDollarIcon, 
  ReceiptPercentIcon 
} from '@heroicons/vue/24/outline'

// Performance tracking
const componentTracker = createComponentTracker('ClientInformationManager')

// Props
const props = defineProps({
  client: {
    type: Object,
    default: null
  },
  clientId: {
    type: [Number, String],
    default: null
  },
  initialData: {
    type: Object,
    default: () => ({})
  },
  context: {
    type: String,
    default: 'admin', // 'admin' or 'client'
    validator: (value) => ['admin', 'client'].includes(value)
  },
  isAdmin: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['update', 'save', 'complete', 'cancel'])

// Reactive state
const activeSection = ref('personal')
const autoSaveStatus = ref('idle') // 'idle', 'saving', 'saved', 'error'
const isSaving = ref(false)
const autoSaveTimer = ref(null)

// Form data structure
const formData = reactive({
  personal: {
    firstName: '',
    middleName: '',
    lastName: '',
    email: '',
    phone: '',
    ssn: '',
    dateOfBirth: '',
    maritalStatus: 'single',
    occupation: '',
    insuranceCovered: false,
    streetNo: '',
    apartmentNo: '',
    zipCode: '',
    city: '',
    state: '',
    country: 'United States',
    mobileNumber: '',
    workNumber: '',
    visaStatus: '',
    dateOfEntryUS: ''
  },
  spouse: {
    firstName: '',
    lastName: '',
    ssn: '',
    dateOfBirth: '',
    occupation: ''
  },
  employee: {
    employerName: '',
    position: '',
    startDate: '',
    salary: '',
    benefits: ''
  },
  projects: [],
  assets: [],
  expenses: []
})

// Validation errors
const errors = reactive({
  personal: {},
  spouse: {},
  employee: {},
  projects: {},
  assets: {},
  expenses: {}
})

// Section configuration
const sections = computed(() => [
  {
    id: 'personal',
    name: 'Personal Details',
    icon: UserIcon,
    completed: isPersonalSectionComplete.value
  },
  {
    id: 'spouse',
    name: 'Spouse Details',
    icon: HeartIcon,
    completed: isSpouseSectionComplete.value
  },
  {
    id: 'employee',
    name: 'Employee Information',
    icon: BriefcaseIcon,
    completed: isEmployeeSectionComplete.value
  },
  {
    id: 'projects',
    name: 'Project Details',
    icon: FolderIcon,
    completed: isProjectsSectionComplete.value
  },
  {
    id: 'assets',
    name: 'Assets',
    icon: CurrencyDollarIcon,
    completed: isAssetsSectionComplete.value
  },
  {
    id: 'expenses',
    name: 'Expenses',
    icon: ReceiptPercentIcon,
    completed: isExpensesSectionComplete.value
  }
])

// Computed properties
const isAdminContext = computed(() => props.context === 'admin')

const completionPercentage = computed(() => {
  const completedSections = sections.value.filter(section => section.completed).length
  return Math.round((completedSections / sections.value.length) * 100)
})

const autoSaveStatusText = computed(() => {
  switch (autoSaveStatus.value) {
    case 'saving': return 'Saving...'
    case 'saved': return 'All changes saved'
    case 'error': return 'Save failed'
    default: return 'Auto-save enabled'
  }
})

// Section completion checks
const isPersonalSectionComplete = computed(() => {
  const personal = formData.personal
  return !!(personal.firstName && personal.lastName && personal.email && 
           personal.phone && personal.ssn && personal.dateOfBirth)
})

const isSpouseSectionComplete = computed(() => {
  if (formData.personal.maritalStatus !== 'married') return true
  const spouse = formData.spouse
  return !!(spouse.firstName && spouse.lastName)
})

const isEmployeeSectionComplete = computed(() => {
  const employee = formData.employee
  return !!(employee.employerName && employee.position)
})

const isProjectsSectionComplete = computed(() => {
  return formData.projects.length > 0
})

const isAssetsSectionComplete = computed(() => {
  return formData.assets.length > 0
})

const isExpensesSectionComplete = computed(() => {
  return formData.expenses.length > 0
})

// Methods
const handleSectionUpdate = measurePerformance('sectionUpdate', (sectionName, data) => {
  // Use shallow merge for better performance
  Object.assign(formData[sectionName], data)
  emit('update', { section: sectionName, data: formData[sectionName] })
  
  // Cache form data to prevent loss
  cacheService.cacheFormData(`client_${props.client?.id || 'new'}_${sectionName}`, formData[sectionName])
  
  // Trigger auto-save
  scheduleAutoSave()
})

const handleSectionValidation = (sectionName, validationErrors) => {
  errors[sectionName] = validationErrors
}

// Create optimized auto-save with debouncing
const autoSaveHandler = createAutoSave(
  `client_${props.client?.id || 'new'}`,
  async () => {
    if (props.readonly || props.isAdmin) return
    
    const clientId = props.client?.id || props.clientId
    await router.post(`/clients/${clientId}/information`, {
      data: formData,
      auto_save: true
    }, {
      preserveState: true,
      preserveScroll: true,
      only: ['errors']
    })
  },
  2000
)

const scheduleAutoSave = debounce(() => {
  autoSaveStatus.value = 'saving'
  autoSaveHandler.markDirty()
  
  // Update status after save
  setTimeout(() => {
    autoSaveStatus.value = 'saved'
    setTimeout(() => {
      if (autoSaveStatus.value === 'saved') {
        autoSaveStatus.value = 'idle'
      }
    }, 3000)
  }, 500)
}, 1000)

const autoSave = async () => {
  if (props.readonly || props.isAdmin) return // Disable auto-save in admin context
  
  try {
    autoSaveStatus.value = 'saving'
    
    const clientId = props.client?.id || props.clientId
    const response = await router.post(`/clients/${clientId}/information`, {
      data: formData,
      auto_save: true
    }, {
      preserveState: true,
      preserveScroll: true,
      only: ['errors']
    })
    
    autoSaveStatus.value = 'saved'
    
    // Reset status after 3 seconds
    setTimeout(() => {
      if (autoSaveStatus.value === 'saved') {
        autoSaveStatus.value = 'idle'
      }
    }, 3000)
    
  } catch (error) {
    autoSaveStatus.value = 'error'
    console.error('Auto-save failed:', error)
  }
}

const saveData = async () => {
  if (isSaving.value) return
  
  try {
    isSaving.value = true
    
    // Emit the save event with form data for parent component to handle
    emit('save', formData)
    
  } catch (error) {
    console.error('Save failed:', error)
  } finally {
    isSaving.value = false
  }
}

const cancelEdit = () => {
  emit('cancel')
}

const exportData = () => {
  const clientId = props.client?.id || props.clientId
  window.open(`/clients/${clientId}/export`, '_blank')
}

const markComplete = async () => {
  try {
    const clientId = props.client?.id || props.clientId
    await router.post(`/clients/${clientId}/complete`)
    emit('complete', clientId)
  } catch (error) {
    console.error('Mark complete failed:', error)
  }
}

const loadInitialData = measurePerformance('loadInitialData', () => {
  const clientId = props.client?.id || 'new'
  
  // Try to load from cache first
  const cachedData = cacheService.getCachedFormData(`client_${clientId}`)
  if (cachedData && !props.client) {
    Object.assign(formData, cachedData)
    return
  }
  
  // Load from client prop if available
  if (props.client) {
    // Use more efficient object assignment
    const clientData = props.client
    
    // Personal details
    Object.assign(formData.personal, {
      firstName: clientData.first_name || '',
      middleName: clientData.middle_name || '',
      lastName: clientData.last_name || '',
      email: clientData.email || '',
      phone: clientData.phone || '',
      mobileNumber: clientData.mobile_number || '',
      workNumber: clientData.work_number || '',
      ssn: clientData.ssn || '',
      dateOfBirth: clientData.date_of_birth || '',
      maritalStatus: clientData.marital_status || 'single',
      occupation: clientData.occupation || '',
      insuranceCovered: clientData.insurance_covered || false,
      streetNo: clientData.street_no || '',
      apartmentNo: clientData.apartment_no || '',
      city: clientData.city || '',
      state: clientData.state || '',
      zipCode: clientData.zip_code || '',
      country: clientData.country || 'United States',
      visaStatus: clientData.visa_status || '',
      dateOfEntryUS: clientData.date_of_entry_us || ''
    })

    // Spouse details
    if (clientData.spouse) {
      Object.assign(formData.spouse, {
        firstName: clientData.spouse.first_name || '',
        lastName: clientData.spouse.last_name || '',
        ssn: clientData.spouse.ssn || '',
        dateOfBirth: clientData.spouse.date_of_birth || '',
        occupation: clientData.spouse.occupation || ''
      })
    }

    // Use direct assignment for arrays (more efficient)
    if (clientData.employees?.length) {
      formData.employee = clientData.employees[0] || {}
    }
    
    if (clientData.projects?.length) {
      formData.projects = clientData.projects
    }
    
    if (clientData.assets?.length) {
      formData.assets = clientData.assets
    }
    
    if (clientData.expenses?.length) {
      formData.expenses = clientData.expenses
    }
  }
  
  // Load from initialData prop if available
  if (props.initialData) {
    Object.keys(formData).forEach(key => {
      if (props.initialData[key]) {
        Object.assign(formData[key], props.initialData[key])
      }
    })
  }
  
  // Cache the loaded data
  cacheService.cacheFormData(`client_${clientId}`, formData)
})

// Lifecycle hooks
onMounted(() => {
  componentTracker.trackRender()
  loadInitialData()
  
  // Preload next likely components
  if (activeSection.value === 'personal') {
    // Preload spouse section if married
    if (formData.personal.maritalStatus === 'married') {
      import('./SpouseDetailsSection.vue')
    }
  }
})

onUnmounted(() => {
  if (autoSaveTimer.value) {
    clearTimeout(autoSaveTimer.value)
  }
  
  // Save form data to cache before unmounting
  const clientId = props.client?.id || 'new'
  cacheService.cacheFormData(`client_${clientId}`, formData)
  
  // Log performance stats
  if (import.meta.env.DEV) {
    console.log('ClientInformationManager performance:', componentTracker.getStats())
  }
})

// Watch for prop changes with shallow comparison for better performance
watch(() => props.initialData, loadInitialData, { deep: false })

// Watch active section changes to preload next components
watch(activeSection, (newSection) => {
  componentTracker.trackRender()
  
  // Preload likely next sections
  const sectionOrder = ['personal', 'spouse', 'employee', 'projects', 'assets', 'expenses']
  const currentIndex = sectionOrder.indexOf(newSection)
  const nextSection = sectionOrder[currentIndex + 1]
  
  if (nextSection) {
    // Preload next section component
    switch (nextSection) {
      case 'spouse':
        import('./SpouseDetailsSection.vue')
        break
      case 'employee':
        import('./EmployeeInformationSection.vue')
        break
      case 'projects':
        import('./ProjectDetailsSection.vue')
        break
      case 'assets':
        import('./AssetsManagementSection.vue')
        break
      case 'expenses':
        import('./ExpensesManagementSection.vue')
        break
    }
  }
})
</script>

<style scoped>
.client-information-manager {
  @apply h-full flex flex-col bg-gray-50;
}
</style>