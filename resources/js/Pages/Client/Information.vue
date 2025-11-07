<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">My Information</h1>
          <p class="mt-1 text-sm text-gray-600">Manage your comprehensive tax information and personal details</p>
        </div>
        <div class="flex items-center space-x-3">

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
    </template>

    <div class="max-w-7xl mx-auto">
      <!-- Navigation Tabs -->
      <div class="bg-white border-b border-gray-200 mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
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
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3">
          <div class="bg-white shadow rounded-lg">
            <div class="p-6">
              <!-- Personal Details Section -->
              <PersonalDetailsSection
                v-if="activeSection === 'personal'"
                v-model="form.personal"
                :errors="form.errors"
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
              <AssetsManagementSection
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
            <h3 class="text-lg font-medium text-gray-900 mb-4">Progress Summary</h3>
            
            <!-- Overall Progress Bar -->
            <div class="mb-6">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Overall Completion</span>
                <span class="text-sm text-gray-500">{{ Math.round(overallProgress) }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                  :style="{ width: `${overallProgress}%` }"
                ></div>
              </div>
            </div>
            
            <!-- Section List -->
            <div class="space-y-3 mb-6">
              <div 
                v-for="section in sections"
                :key="section.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors"
                @click="activeSection = section.id"
              >
                <div class="flex items-center space-x-3">
                  <component :is="section.icon" class="w-4 h-4 text-gray-500" />
                  <span class="text-sm font-medium text-gray-900">{{ section.name }}</span>
                </div>
                <div class="flex items-center space-x-2">
                  <!-- Progress indicator -->
                  <div class="w-8 h-2 bg-gray-200 rounded-full">
                    <div 
                      class="h-2 rounded-full transition-all duration-300"
                      :class="getSectionProgressColor(section.id)"
                      :style="{ width: `${getSectionProgress(section.id)}%` }"
                    ></div>
                  </div>
                  <!-- Completion status -->
                  <div 
                    v-if="getSectionProgress(section.id) === 100"
                    class="w-2 h-2 bg-green-500 rounded-full"
                  ></div>
                  <div 
                    v-else-if="getSectionProgress(section.id) > 0"
                    class="w-2 h-2 bg-yellow-500 rounded-full"
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
                @click="saveInformation"
                :disabled="form.processing"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ form.processing ? 'Saving...' : 'Save Information' }}
              </button>
              
              <button
                @click="exportData"
                class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700"
              >
                Export My Data
              </button>
              
              <button
                @click="showReviewModal = true"
                :disabled="overallProgress < 50"
                :class="[
                  'w-full py-2 px-4 rounded-md transition-colors',
                  overallProgress >= 50 
                    ? 'bg-green-600 text-white hover:bg-green-700' 
                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                ]"
              >
                Request Review
              </button>
              <p v-if="overallProgress < 50" class="text-xs text-gray-500 mt-1 text-center">
                Complete at least 50% to request review
              </p>
            </div>

            <!-- Help Section -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
              <h4 class="text-sm font-medium text-blue-900 mb-2">Need Help?</h4>
              <p class="text-xs text-blue-700 mb-3">
                Complete all sections to ensure accurate tax preparation. Your tax professional will review your information.
              </p>
              
              <!-- Section-specific help -->
              <div v-if="getSectionHelp(activeSection)" class="mb-3 p-2 bg-blue-100 rounded text-xs text-blue-800">
                {{ getSectionHelp(activeSection) }}
              </div>
              
              <div class="space-y-2">
                <button 
                  @click="showHelpModal = true"
                  class="block text-xs text-blue-600 hover:text-blue-800 underline"
                >
                  View Section Guide
                </button>
                <button class="block text-xs text-blue-600 hover:text-blue-800 underline">
                  Contact Support
                </button>
              </div>
            </div>
            
            <!-- Completion Status -->
            <div class="mt-4 p-4 bg-green-50 rounded-lg" v-if="overallProgress === 100">
              <div class="flex items-center">
                <CheckCircleIcon class="w-5 h-5 text-green-500 mr-2" />
                <span class="text-sm font-medium text-green-800">All sections completed!</span>
              </div>
              <p class="text-xs text-green-700 mt-1">
                Your information is ready for review by your tax professional.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Help Modal -->
    <div v-if="showHelpModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div 
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          @click="showHelpModal = false"
        ></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                <QuestionMarkCircleIcon class="h-6 w-6 text-blue-600" />
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  {{ sections.find(s => s.id === activeSection)?.name }} Guide
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500 mb-4">
                    {{ getSectionHelp(activeSection) }}
                  </p>
                  
                  <!-- Section-specific detailed help -->
                  <div v-if="activeSection === 'personal'" class="text-sm text-gray-700 space-y-2">
                    <p><strong>Required Fields:</strong></p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                      <li>First Name, Last Name - Your legal name as it appears on tax documents</li>
                      <li>Email & Phone - Primary contact information</li>
                      <li>SSN - Required for tax filing (format: 123-45-6789)</li>
                      <li>Date of Birth - Used for tax credits and verification</li>
                      <li>Address - Your primary residence address</li>
                    </ul>
                  </div>
                  
                  <div v-else-if="activeSection === 'spouse'" class="text-sm text-gray-700 space-y-2">
                    <p><strong>When to Complete:</strong></p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                      <li>Only required if marital status is "Married"</li>
                      <li>Spouse SSN is optional but recommended for joint filing</li>
                      <li>All information should match spouse's tax documents</li>
                    </ul>
                  </div>
                  
                  <div v-else-if="activeSection === 'employee'" class="text-sm text-gray-700 space-y-2">
                    <p><strong>Employment Information:</strong></p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                      <li>Enter information for your primary employer</li>
                      <li>Salary information helps with tax planning</li>
                      <li>Benefits information affects tax deductions</li>
                    </ul>
                  </div>
                  
                  <div v-else class="text-sm text-gray-700">
                    <p>Complete this section with accurate information. All fields are optional unless marked as required.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="showHelpModal = false"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Got it
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Review Request Modal -->
    <div v-if="showReviewModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div 
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          @click="showReviewModal = false"
        ></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="submitReviewRequest">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                  <CheckCircleIcon class="h-6 w-6 text-green-600" />
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                  <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Request Professional Review
                  </h3>
                  <div class="mt-4 space-y-4">
                    <!-- Section Selection -->
                    <div>
                      <label class="text-sm font-medium text-gray-700 mb-2 block">
                        Which sections would you like reviewed? (Optional - leave blank for all sections)
                      </label>
                      <div class="space-y-2">
                        <label v-for="section in sections" :key="section.id" class="flex items-center">
                          <input
                            v-model="reviewForm.sections"
                            :value="section.id"
                            type="checkbox"
                            class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                          />
                          <span class="ml-2 text-sm text-gray-700">{{ section.name }}</span>
                          <span v-if="getSectionProgress(section.id) === 100" class="ml-2 text-xs text-green-600">(Complete)</span>
                          <span v-else-if="getSectionProgress(section.id) > 0" class="ml-2 text-xs text-yellow-600">(In Progress)</span>
                        </label>
                      </div>
                    </div>

                    <!-- Priority Selection -->
                    <div>
                      <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                        Priority Level
                      </label>
                      <select
                        id="priority"
                        v-model="reviewForm.priority"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                      >
                        <option value="low">Low - Review when convenient</option>
                        <option value="normal">Normal - Standard review</option>
                        <option value="high">High - Urgent review needed</option>
                      </select>
                    </div>

                    <!-- Message -->
                    <div>
                      <label for="reviewMessage" class="block text-sm font-medium text-gray-700 mb-1">
                        Additional Message (Optional)
                      </label>
                      <textarea
                        id="reviewMessage"
                        v-model="reviewForm.message"
                        rows="3"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                        placeholder="Any specific questions or areas you'd like your tax professional to focus on..."
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="reviewForm.processing"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                {{ reviewForm.processing ? 'Sending...' : 'Send Review Request' }}
              </button>
              <button
                @click="showReviewModal = false"
                type="button"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PersonalDetailsSection from '@/Components/PersonalDetailsSection.vue'
import SpouseDetailsSection from '@/Components/SpouseDetailsSection.vue'
import EmployeeInformationSection from '@/Components/EmployeeInformationSection.vue'
import ProjectDetailsSection from '@/Components/ProjectDetailsSection.vue'
import AssetsManagementSection from '@/Components/AssetsManagementSection.vue'
import ExpensesManagementSection from '@/Components/ExpensesManagementSection.vue'

// Icons
import { 
  UserIcon, 
  HeartIcon, 
  BriefcaseIcon, 
  FolderIcon, 
  CurrencyDollarIcon, 
  ReceiptPercentIcon,
  CheckCircleIcon,
  QuestionMarkCircleIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  clientData: {
    type: Object,
    default: () => ({})
  }
})

// Reactive state
const activeSection = ref('personal')
const autoSaveStatus = ref('idle')
const autoSaveTimer = ref(null)
const showHelpModal = ref(false)
const showReviewModal = ref(false)
const reviewForm = useForm({
  sections: [],
  message: '',
  priority: 'normal'
})

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
    dateOfEntryUS: ''
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

// Computed properties

const autoSaveStatusText = computed(() => {
  switch (autoSaveStatus.value) {
    case 'saving': return 'Saving...'
    case 'saved': return 'All changes saved'
    case 'error': return 'Save failed'
    default: return 'Auto-save enabled'
  }
})

// Progress tracking computed properties
const overallProgress = computed(() => {
  const sectionProgresses = sections.value.map(section => getSectionProgress(section.id))
  const totalProgress = sectionProgresses.reduce((sum, progress) => sum + progress, 0)
  return totalProgress / sections.value.length
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
      return calculateProjectsProgress()
    case 'assets':
      return calculateAssetsProgress()
    case 'expenses':
      return calculateExpensesProgress()
    default:
      return 0
  }
}

const getSectionProgressColor = (sectionId) => {
  const progress = getSectionProgress(sectionId)
  if (progress === 100) return 'bg-green-500'
  if (progress > 0) return 'bg-yellow-500'
  return 'bg-gray-300'
}

const getSectionHelp = (sectionId) => {
  const helpTexts = {
    personal: 'Fill in your basic personal information, contact details, and address. All required fields must be completed.',
    spouse: 'Add spouse information if you are married. This is required for joint tax filing.',
    employee: 'Enter your employment details including employer name, position, and salary information.',
    projects: 'Add any tax-related projects or business activities you are involved in.',
    assets: 'List your assets including purchase dates and business usage percentages.',
    expenses: 'Track your deductible expenses by category for tax preparation.'
  }
  return helpTexts[sectionId] || ''
}





// Methods
const handleSectionUpdate = () => {
  scheduleAutoSave()
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
  scheduleAutoSave()
}

const scheduleAutoSave = () => {
  if (autoSaveTimer.value) {
    clearTimeout(autoSaveTimer.value)
  }
  
  autoSaveTimer.value = setTimeout(() => {
    autoSave()
  }, 2000)
}

const autoSave = async () => {
  try {
    autoSaveStatus.value = 'saving'
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    
    if (!csrfToken) {
      console.error('CSRF token not found')
      autoSaveStatus.value = 'error'
      return
    }
    
    const response = await fetch('/client/information/auto-save', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({
        personal: toSnakeCase(form.personal),
        spouse: toSnakeCase(form.spouse),
        employee: Array.isArray(form.employee) ? form.employee.map(emp => toSnakeCase(emp)) : [],
        projects: form.projects,
        assets: form.assets,
        expenses: form.expenses
      })
    })
    
    const result = await response.json()
    
    if (response.ok && result.success) {
      autoSaveStatus.value = 'saved'
      setTimeout(() => {
        if (autoSaveStatus.value === 'saved') {
          autoSaveStatus.value = 'idle'
        }
      }, 3000)
    } else {
      autoSaveStatus.value = 'error'
      console.error('Auto-save failed:', result.error || 'Unknown error')
    }
    
  } catch (error) {
    autoSaveStatus.value = 'error'
    console.error('Auto-save failed:', error)
  }
}

const saveInformation = () => {
  // Convert form data to snake_case for backend
  const backendData = {
    personal: toSnakeCase(form.personal),
    spouse: toSnakeCase(form.spouse),
    employee: Array.isArray(form.employee) ? form.employee.map(emp => toSnakeCase(emp)) : [],
    projects: form.projects,
    assets: form.assets,
    expenses: form.expenses
  }
  
  console.log('Saving client information:', backendData)
  console.log('Personal data being sent:', backendData.personal)
  
  form.transform(() => backendData).post('/client/information', {
    onSuccess: () => {
      // Success handled by flash message
    }
  })
}

const exportData = () => {
  window.open('/client/information/export', '_blank')
}

const submitReviewRequest = () => {
  reviewForm.post('/client/information/request-review', {
    onSuccess: () => {
      showReviewModal.value = false
      reviewForm.reset()
      // Success handled by flash message
    },
    onError: (errors) => {
      console.error('Review request failed:', errors)
    }
  })
}

// Utility functions to convert between camelCase and snake_case
const toCamelCase = (obj) => {
  const result = {}
  for (const [key, value] of Object.entries(obj)) {
    const camelKey = key.replace(/_([a-z])/g, (match, letter) => letter.toUpperCase())
    result[camelKey] = value
  }
  return result
}

const toSnakeCase = (obj) => {
  const result = {}
  for (const [key, value] of Object.entries(obj)) {
    const snakeKey = key.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`)
    result[snakeKey] = value
  }
  return result
}

const loadInitialData = () => {
  if (props.clientData) {
    // Load personal details and convert from snake_case to camelCase
    if (props.clientData.personal) {
      Object.assign(form.personal, toCamelCase(props.clientData.personal))
    }
    
    // Load spouse details and convert from snake_case to camelCase
    if (props.clientData.spouse) {
      Object.assign(form.spouse, toCamelCase(props.clientData.spouse))
    }
    
    // Load other sections
    if (props.clientData.employee && Array.isArray(props.clientData.employee)) {
      // Convert employee data from snake_case to camelCase
      form.employee = props.clientData.employee.map(emp => toCamelCase(emp))
    } else if (props.clientData.employee) {
      // Handle single employee object
      form.employee = [toCamelCase(props.clientData.employee)]
    }
    
    if (props.clientData.projects) {
      form.projects = props.clientData.projects
    }
    
    if (props.clientData.assets) {
      form.assets = props.clientData.assets
    }
    
    if (props.clientData.expenses) {
      form.expenses = props.clientData.expenses
    }
  }
}

// Lifecycle
onMounted(() => {
  loadInitialData()
})

// Watch for changes
watch(() => props.clientData, loadInitialData, { deep: true })

// Progress calculation methods
const calculatePersonalProgress = () => {
  const requiredFields = ['firstName', 'lastName', 'email', 'phone', 'ssn', 'dateOfBirth', 'maritalStatus', 'streetNo', 'zipCode', 'city', 'state']
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

const calculateProjectsProgress = () => {
  // Projects are optional, so if none exist, consider it complete
  if (!form.projects || form.projects.length === 0) return 100
  
  // If projects exist, check if they have basic information
  const validProjects = form.projects.filter(project => 
    project.name && project.name.trim().length > 0
  )
  return form.projects.length > 0 ? (validProjects.length / form.projects.length) * 100 : 100
}

const calculateAssetsProgress = () => {
  // Assets are optional, so if none exist, consider it complete
  if (!form.assets || form.assets.length === 0) return 100
  
  // If assets exist, check if they have required information
  const validAssets = form.assets.filter(asset => 
    asset.assetName && asset.dateOfPurchase && asset.costOfAcquisition
  )
  return form.assets.length > 0 ? (validAssets.length / form.assets.length) * 100 : 100
}

const calculateExpensesProgress = () => {
  // Expenses are optional, so if none exist, consider it complete
  if (!form.expenses || form.expenses.length === 0) return 100
  
  // If expenses exist, check if they have required information
  const validExpenses = form.expenses.filter(expense => 
    expense.particulars && expense.amount && expense.taxPayer
  )
  return form.expenses.length > 0 ? (validExpenses.length / form.expenses.length) * 100 : 100
}

// Watch form changes to trigger completion recalculation
watch(() => form.data(), () => {
  // This will trigger reactivity for completion calculations
}, { deep: true })
</script>