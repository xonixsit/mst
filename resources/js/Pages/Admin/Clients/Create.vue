<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Create New Client</h1>
          <p class="mt-1 text-sm text-gray-600">Add comprehensive client information to the system</p>
        </div>
        <div class="flex items-center space-x-3">
          <button
            @click="router.visit(route('admin.clients.index'))"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Clients
          </button>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto">
      <!-- Navigation Tabs -->
      <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 mb-6 p-2 rounded-t-lg">
        <nav class="flex space-x-2" aria-label="Tabs">
          <button
            v-for="section in sections"
            :key="section.id"
            @click="activeSection = section.id"
            :class="[
              'py-3 px-4 font-medium text-sm whitespace-nowrap transition-all duration-200 rounded-lg flex-1 min-w-0',
              getSectionTabClasses(section.id)
            ]"
          >
            <div class="flex items-center justify-center space-x-2">
              <component :is="section.icon" :class="getTabIconClasses(section.id)" class="w-4 h-4" />
              <span class="font-medium">{{ section.name }}</span>
              <div 
                v-if="getSectionProgress(section.id) === 100"
                class="w-2 h-2 bg-green-400 rounded-full animate-pulse"
              ></div>
              <div 
                v-else-if="getSectionProgress(section.id) === null"
                :class="`w-2 h-2 rounded-full ${getTabStatusDotClasses(section.id)}`"
              ></div>
            </div>
          </button>
        </nav>
      </div>

      <!-- Content Area -->
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3">
          <div class="shadow rounded-lg overflow-hidden" :class="getSectionBackgroundClasses(activeSection)">
            <div class="p-6">
              <!-- Personal Details Section -->
              <PersonalDetailsSection
                v-if="activeSection === 'personal'"
                v-model="form.personal"
                :errors="form.errors"
                :visa-status-options="visaStatusOptions"
                @update="handleSectionUpdate"
              />

              <!-- Spouse Details Section -->
              <SpouseDetailsSection
                v-if="activeSection === 'spouse'"
                v-model="form.spouse"
                :errors="form.errors"
                :show-spouse="form.personal?.maritalStatus === 'married'"
                @update="handleSectionUpdate"
              />

              <!-- Employee Information Section -->
              <EmployeeInformationSection
                v-if="activeSection === 'employee'"
                v-model="form.employee[0]"
                :errors="form.errors"
                @update="handleEmployeeUpdate"
              />

              <!-- Project Details Section -->
              <ProjectDetailsSection
                v-if="activeSection === 'projects'"
                v-model="form.projects"
                :errors="form.errors"
                @update="handleSectionUpdate"
              />

              <!-- Assets Management Section -->
              <AssetDetailsSection
                v-if="activeSection === 'assets'"
                v-model="form.assets"
                :errors="form.errors"
                @update="handleSectionUpdate"
              />

              <!-- Expenses Management Section -->
              <ExpensesManagementSection
                v-if="activeSection === 'expenses'"
                v-model="form.expenses"
                :errors="form.errors"
                @update="handleSectionUpdate"
              />
            </div>
          </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white shadow rounded-lg p-6 sticky top-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Creation Progress</h3>
            
            <!-- Overall Progress Bar -->
            <div class="mb-6">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Overall Completion</span>
                <span class="text-sm text-gray-500">{{ Math.round(overallProgress) }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="bg-gradient-to-r from-blue-500 to-emerald-500 h-2 rounded-full transition-all duration-300"
                  :style="{ width: `${overallProgress}%` }"
                ></div>
              </div>
            </div>
            
            <!-- Section List -->
            <div class="space-y-3 mb-6">
              <div 
                v-for="section in sections"
                :key="section.id"
                :class="[
                  'flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-200',
                  getSidebarSectionClasses(section.id)
                ]"
                @click="activeSection = section.id"
              >
                <div class="flex items-center space-x-3">
                  <component :is="section.icon" :class="getSectionIconClasses(section.id)" class="w-4 h-4" />
                  <span class="text-sm font-medium" :class="getSectionTextClasses(section.id)">{{ section.name }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <!-- Progress indicator -->
                  <div class="w-8 h-2 bg-gray-200 rounded-full">
                    <div 
                      class="h-2 rounded-full transition-all duration-300"
                      :class="getSectionProgressColor(section.id)"
                      :style="{ width: getSectionProgress(section.id) !== null ? `${getSectionProgress(section.id)}%` : '100%' }"
                    ></div>
                  </div>
                  <!-- Completion status -->
                  <div 
                    v-if="getSectionProgress(section.id) === null"
                    :class="`w-2 h-2 rounded-full ${getSectionThemeColor(section.id)}`"
                  ></div>
                  <div 
                    v-else-if="getSectionProgress(section.id) === 100"
                    class="w-2 h-2 bg-green-500 rounded-full"
                  ></div>
                  <div 
                    v-else-if="getSectionProgress(section.id) > 0"
                    class="w-2 h-2 bg-amber-500 rounded-full"
                  ></div>
                  <div 
                    v-else
                    class="w-2 h-2 bg-gray-300 rounded-full"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
              <button
                @click="saveClient"
                :disabled="form.processing"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ form.processing ? 'Creating Client...' : 'Create Client' }}
              </button>
              
              <button
                @click="handleCancel"
                class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700"
              >
                Cancel
              </button>
            </div>

            <!-- Admin Help Section -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
              <h4 class="text-sm font-medium text-blue-900 mb-2">Admin Notes</h4>
              <p class="text-xs text-blue-700 mb-3">
                Fill out client information sections as needed. Not all sections are required for initial client creation.
              </p>
              
              <!-- Section-specific help -->
              <div v-if="getSectionHelp(activeSection)" class="mb-3 p-2 bg-blue-100 rounded text-xs text-blue-800">
                {{ getSectionHelp(activeSection) }}
              </div>
              
              <div class="space-y-2">
                <button class="block text-xs text-blue-600 hover:text-blue-800 underline">
                  Admin Guidelines
                </button>
                <button class="block text-xs text-blue-600 hover:text-blue-800 underline">
                  Client Data Policy
                </button>
              </div>
            </div>
            
            <!-- Completion Status -->
            <div class="mt-4 p-4 bg-green-50 rounded-lg" v-if="overallProgress >= 80">
              <div class="flex items-center">
                <CheckCircleIcon class="w-5 h-5 text-green-500 mr-2" />
                <span class="text-sm font-medium text-green-800">Ready to create!</span>
              </div>
              <p class="text-xs text-green-700 mt-1">
                Client information is comprehensive and ready for creation.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PersonalDetailsSection from '@/Components/PersonalDetailsSection.vue'
import SpouseDetailsSection from '@/Components/SpouseDetailsSection.vue'
import EmployeeInformationSection from '@/Components/EmployeeInformationSection.vue'
import ProjectDetailsSection from '@/Components/ProjectDetailsSection.vue'
import AssetDetailsSection from '@/Components/AssetDetailsSection.vue'
import ExpensesManagementSection from '@/Components/ExpensesManagementSection.vue'

// Icons
import { 
  UserIcon, 
  HeartIcon, 
  BriefcaseIcon, 
  FolderIcon, 
  CurrencyDollarIcon, 
  ReceiptPercentIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  visaStatusOptions: {
    type: Array,
    default: () => []
  }
})

// Reactive state
const activeSection = ref('personal')

// Form setup
const form = useForm({
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
    date_of_entry_us: ''
  },
  spouse: {
    firstName: '',
    middleName: '',
    lastName: '',
    email: '',
    phone: '',
    ssn: '',
    dateOfBirth: '',
    occupation: ''
  },
  employee: [{}], // Initialize with one empty employee object
  projects: [],
  assets: [],
  expenses: []
})

// Section configuration
const sections = computed(() => [
  {
    id: 'personal',
    name: 'Personal Details',
    icon: UserIcon
  },
  {
    id: 'spouse',
    name: 'Spouse Details',
    icon: HeartIcon
  },
  {
    id: 'employee',
    name: 'Employment',
    icon: BriefcaseIcon
  },
  {
    id: 'projects',
    name: 'Projects',
    icon: FolderIcon
  },
  {
    id: 'assets',
    name: 'Assets',
    icon: CurrencyDollarIcon
  },
  {
    id: 'expenses',
    name: 'Expenses',
    icon: ReceiptPercentIcon
  }
])

// Progress tracking computed properties
const overallProgress = computed(() => {
  const sectionProgresses = sections.value.map(section => getSectionProgress(section.id)).filter(progress => progress !== null)
  const totalProgress = sectionProgresses.reduce((sum, progress) => sum + progress, 0)
  return sectionProgresses.length > 0 ? totalProgress / sectionProgresses.length : 0
})

const getSectionProgress = (sectionId) => {
  switch (sectionId) {
    case 'personal':
      return calculatePersonalProgress()
    case 'spouse':
      return calculateSpouseProgress()
    case 'employee':
      return calculateEmployeeProgress()
    case 'projects':
      return null // No progress calculation for projects
    case 'assets':
      return null // No progress calculation for assets
    case 'expenses':
      return null // No progress calculation for expenses
    default:
      return 0
  }
}

const getSectionProgressColor = (sectionId) => {
  const progress = getSectionProgress(sectionId)
  if (progress === null) return getSectionThemeColor(sectionId)
  if (progress === 100) return 'bg-green-500'
  if (progress > 0) return 'bg-amber-500'
  return 'bg-gray-300'
}

// Color psychology for My Super Tax
const getSectionThemeColor = (sectionId) => {
  const colors = {
    personal: 'bg-blue-500',      // Trust, reliability, professionalism
    spouse: 'bg-rose-500',        // Love, relationships, warmth
    employee: 'bg-indigo-500',    // Corporate, professional, stability
    projects: 'bg-purple-500',    // Creativity, planning, innovation
    assets: 'bg-emerald-500',     // Money, growth, prosperity
    expenses: 'bg-orange-500'     // Energy, attention, caution
  }
  return colors[sectionId] || 'bg-gray-500'
}

const getSectionTabClasses = (sectionId) => {
  const isActive = activeSection.value === sectionId
  
  const colorMap = {
    personal: {
      active: 'bg-blue-500 text-white shadow-lg transform scale-105 border-2 border-blue-600',
      inactive: 'bg-blue-100 text-blue-700 hover:bg-blue-200 hover:shadow-md hover:transform hover:scale-102 border border-blue-200'
    },
    spouse: {
      active: 'bg-rose-500 text-white shadow-lg transform scale-105 border-2 border-rose-600',
      inactive: 'bg-rose-100 text-rose-700 hover:bg-rose-200 hover:shadow-md hover:transform hover:scale-102 border border-rose-200'
    },
    employee: {
      active: 'bg-indigo-500 text-white shadow-lg transform scale-105 border-2 border-indigo-600',
      inactive: 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200 hover:shadow-md hover:transform hover:scale-102 border border-indigo-200'
    },
    projects: {
      active: 'bg-purple-500 text-white shadow-lg transform scale-105 border-2 border-purple-600',
      inactive: 'bg-purple-100 text-purple-700 hover:bg-purple-200 hover:shadow-md hover:transform hover:scale-102 border border-purple-200'
    },
    assets: {
      active: 'bg-emerald-500 text-white shadow-lg transform scale-105 border-2 border-emerald-600',
      inactive: 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 hover:shadow-md hover:transform hover:scale-102 border border-emerald-200'
    },
    expenses: {
      active: 'bg-orange-500 text-white shadow-lg transform scale-105 border-2 border-orange-600',
      inactive: 'bg-orange-100 text-orange-700 hover:bg-orange-200 hover:shadow-md hover:transform hover:scale-102 border border-orange-200'
    }
  }
  
  return isActive ? colorMap[sectionId]?.active : colorMap[sectionId]?.inactive
}

const getSectionBackgroundClasses = (sectionId) => {
  const backgroundMap = {
    personal: 'bg-gradient-to-br from-blue-50 to-white',
    spouse: 'bg-gradient-to-br from-rose-50 to-white',
    employee: 'bg-gradient-to-br from-indigo-50 to-white',
    projects: 'bg-gradient-to-br from-purple-50 to-white',
    assets: 'bg-gradient-to-br from-emerald-50 to-white',
    expenses: 'bg-gradient-to-br from-orange-50 to-white'
  }
  
  return backgroundMap[sectionId] || 'bg-white'
}

const getSidebarSectionClasses = (sectionId) => {
  const isActive = activeSection.value === sectionId
  
  const colorMap = {
    personal: isActive ? 'bg-blue-100 border border-blue-200' : 'bg-gray-50 hover:bg-blue-50',
    spouse: isActive ? 'bg-rose-100 border border-rose-200' : 'bg-gray-50 hover:bg-rose-50',
    employee: isActive ? 'bg-indigo-100 border border-indigo-200' : 'bg-gray-50 hover:bg-indigo-50',
    projects: isActive ? 'bg-purple-100 border border-purple-200' : 'bg-gray-50 hover:bg-purple-50',
    assets: isActive ? 'bg-emerald-100 border border-emerald-200' : 'bg-gray-50 hover:bg-emerald-50',
    expenses: isActive ? 'bg-orange-100 border border-orange-200' : 'bg-gray-50 hover:bg-orange-50'
  }
  
  return colorMap[sectionId] || 'bg-gray-50 hover:bg-gray-100'
}

const getSectionIconClasses = (sectionId) => {
  const isActive = activeSection.value === sectionId
  
  const colorMap = {
    personal: isActive ? 'text-blue-600' : 'text-gray-500',
    spouse: isActive ? 'text-rose-600' : 'text-gray-500',
    employee: isActive ? 'text-indigo-600' : 'text-gray-500',
    projects: isActive ? 'text-purple-600' : 'text-gray-500',
    assets: isActive ? 'text-emerald-600' : 'text-gray-500',
    expenses: isActive ? 'text-orange-600' : 'text-gray-500'
  }
  
  return colorMap[sectionId] || 'text-gray-500'
}

const getSectionTextClasses = (sectionId) => {
  const isActive = activeSection.value === sectionId
  
  const colorMap = {
    personal: isActive ? 'text-blue-900' : 'text-gray-900',
    spouse: isActive ? 'text-rose-900' : 'text-gray-900',
    employee: isActive ? 'text-indigo-900' : 'text-gray-900',
    projects: isActive ? 'text-purple-900' : 'text-gray-900',
    assets: isActive ? 'text-emerald-900' : 'text-gray-900',
    expenses: isActive ? 'text-orange-900' : 'text-gray-900'
  }
  
  return colorMap[sectionId] || 'text-gray-900'
}

const getTabIconClasses = (sectionId) => {
  const isActive = activeSection.value === sectionId
  
  if (isActive) {
    return 'text-white'
  }
  
  const colorMap = {
    personal: 'text-blue-600',
    spouse: 'text-rose-600',
    employee: 'text-indigo-600',
    projects: 'text-purple-600',
    assets: 'text-emerald-600',
    expenses: 'text-orange-600'
  }
  
  return colorMap[sectionId] || 'text-gray-600'
}

const getTabStatusDotClasses = (sectionId) => {
  const isActive = activeSection.value === sectionId
  
  if (isActive) {
    return 'bg-white bg-opacity-80'
  }
  
  const colorMap = {
    personal: 'bg-blue-400',
    spouse: 'bg-rose-400',
    employee: 'bg-indigo-400',
    projects: 'bg-purple-400',
    assets: 'bg-emerald-400',
    expenses: 'bg-orange-400'
  }
  
  return colorMap[sectionId] || 'bg-gray-400'
}

const getSectionHelp = (sectionId) => {
  const helpTexts = {
    personal: 'Enter basic client information including name, contact details, and address. Required for client creation.',
    spouse: 'Add spouse information if client is married. Required for joint tax filing.',
    employee: 'Enter employment details including employer name, position, and salary information.',
    projects: 'Add any tax-related projects or business activities the client is involved in.',
    assets: 'List client assets including purchase dates and business usage percentages.',
    expenses: 'Track client deductible expenses by category for tax preparation.'
  }
  return helpTexts[sectionId] || ''
}

// Methods
const handleSectionUpdate = () => {
  // Handle section updates
}

const handleEmployeeUpdate = (employeeData) => {
  // Ensure employee array exists and has at least one item
  if (!Array.isArray(form.employee)) {
    form.employee = []
  }
  if (form.employee.length === 0) {
    form.employee.push({})
  }
  
  // Update the first employee record
  form.employee[0] = { ...employeeData }
}

const saveClient = () => {
  // Convert form data to snake_case for backend
  const backendData = {
    personal: toSnakeCase(form.personal),
    spouse: toSnakeCase(form.spouse),
    employee: Array.isArray(form.employee) ? form.employee : [],
    projects: form.projects,
    assets: Array.isArray(form.assets) ? form.assets.map(asset => {
      const processedAsset = { ...asset }
      // Ensure date is properly formatted
      if (processedAsset.date_of_purchase) {
        processedAsset.date_of_purchase = formatDateForBackend(processedAsset.date_of_purchase)
      }
      return processedAsset
    }) : [],
    expenses: form.expenses
  }
  
  console.log('Creating client with data:', backendData)
  
  form.transform(() => backendData).post(route('admin.clients.store'), {
    onSuccess: () => {
      // Success handled by controller redirect
    },
    onError: (errors) => {
      console.error('Failed to create client:', errors)
    }
  })
}

const handleCancel = () => {
  router.visit(route('admin.clients.index'))
}

// Utility functions
const toSnakeCase = (obj) => {
  const result = {}
  for (const [key, value] of Object.entries(obj)) {
    const snakeKey = key.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`)
    result[snakeKey] = value
  }
  return result
}

const formatDateForBackend = (date) => {
  if (!date) return null
  
  try {
    let dateObj
    
    // Handle different date formats
    if (typeof date === 'string') {
      // If it's already in YYYY-MM-DD format, return as is
      if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
        return date
      }
      dateObj = new Date(date)
    } else if (date instanceof Date) {
      dateObj = date
    } else {
      return null
    }
    
    // Check if the date is valid
    if (isNaN(dateObj.getTime())) {
      console.warn('Invalid date provided:', date)
      return null
    }
    
    // Return in YYYY-MM-DD format for backend validation
    const year = dateObj.getFullYear()
    const month = String(dateObj.getMonth() + 1).padStart(2, '0')
    const day = String(dateObj.getDate()).padStart(2, '0')
    
    return `${year}-${month}-${day}`
  } catch (error) {
    console.error('Error formatting date for backend:', error, date)
    return null
  }
}

// Progress calculation methods
const calculatePersonalProgress = () => {
  const requiredFields = ['firstName', 'lastName', 'email', 'phone']
  const filledFields = requiredFields.filter(field => {
    const value = form.personal[field]
    return value && value.toString().trim().length > 0
  })
  return (filledFields.length / requiredFields.length) * 100
}

const calculateSpouseProgress = () => {
  if (form.personal?.maritalStatus !== 'married') return 100 // Not applicable
  
  const requiredFields = ['firstName', 'lastName']
  const filledFields = requiredFields.filter(field => {
    const value = form.spouse[field]
    return value && value.toString().trim().length > 0
  })
  return (filledFields.length / requiredFields.length) * 100
}

const calculateEmployeeProgress = () => {
  if (!form.employee || !Array.isArray(form.employee) || form.employee.length === 0) return 0
  
  const employee = form.employee[0]
  const requiredFields = ['employerName', 'position']
  const filledFields = requiredFields.filter(field => {
    const value = employee[field]
    return value && value.toString().trim().length > 0
  })
  return (filledFields.length / requiredFields.length) * 100
}

// Watch form changes to trigger completion recalculation
watch(() => form.data(), () => {
  // This will trigger reactivity for completion calculations
}, { deep: true })
</script>