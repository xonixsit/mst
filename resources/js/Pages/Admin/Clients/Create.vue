<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-emerald-50 to-blue-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-emerald-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-blue-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <!-- Create Client Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 via-emerald-600 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-emerald-100">
              <UserPlusIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-emerald-900 to-blue-900 bg-clip-text text-transparent">
                Create New Client
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Add comprehensive client information to the system</p>
              
              <!-- Progress Indicator -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-emerald-700">{{ Math.round(overallProgress) }}% Complete</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-blue-700">{{ activeSection.charAt(0).toUpperCase() + activeSection.slice(1) }} Section</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Button -->
          <div class="flex items-center">
            <button
              @click="handleCancel"
              class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <ArrowLeftIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">Back to Clients</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Enhanced Navigation Tabs -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden mb-8">
          <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-xl font-bold text-gray-900">Client Information Sections</h3>
                <p class="text-sm text-gray-600 mt-1">Complete all sections to create a comprehensive client profile</p>
              </div>
              <div class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                <span class="text-sm font-semibold text-emerald-600">{{ Math.round(overallProgress) }}% Complete</span>
              </div>
            </div>
          </div>
          
          <div class="p-6">
            <nav class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3" aria-label="Tabs">
              <button
                v-for="section in sections"
                :key="section.id"
                @click="activeSection = section.id"
                :class="[
                  'relative p-4 rounded-xl font-medium text-sm transition-all duration-300 border-2 group hover:shadow-md transform hover:scale-105',
                  getSectionTabClasses(section.id)
                ]"
              >
                <div class="flex flex-col items-center space-y-2">
                  <div :class="[
                    'w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300',
                    getTabIconBgClasses(section.id)
                  ]">
                    <component :is="section.icon" :class="getTabIconClasses(section.id)" class="w-5 h-5" />
                  </div>
                  <span class="font-semibold text-center">{{ section.name }}</span>
                  <div class="flex items-center space-x-1">
                    <div 
                      v-if="getSectionProgress(section.id) === 100"
                      class="w-2 h-2 bg-green-500 rounded-full animate-pulse"
                    ></div>
                    <div 
                      v-else
                      :class="`w-2 h-2 rounded-full ${getTabStatusDotClasses(section.id)}`"
                    ></div>
                    <span class="text-xs font-medium opacity-75">
                      {{ getSectionProgress(section.id) === 100 ? 'Complete' : 'Pending' }}
                    </span>
                  </div>
                </div>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
        <!-- Enhanced Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-3">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
                <div class="flex items-center">
                  <div :class="[
                    'w-8 h-8 rounded-lg flex items-center justify-center mr-3',
                    getActiveTabIconBgClasses()
                  ]">
                    <component :is="getActiveSection().icon" :class="getActiveTabIconClasses()" class="w-4 h-4" />
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ getActiveSection().name }}</h3>
                    <p class="text-sm text-gray-600">{{ getActiveSection().description }}</p>
                  </div>
                </div>
              </div>
              <div class="p-8">
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

          <!-- Enhanced Sidebar Summary -->
          <div class="lg:col-span-1">
            <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden sticky top-6">
              <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
                    <ChartBarIcon class="w-4 h-4 text-white" />
                  </div>
                  <h3 class="text-lg font-bold text-gray-900">Creation Progress</h3>
                </div>
              </div>
              
              <div class="p-6">
                <!-- Overall Progress Bar -->
                <div class="mb-6">
                  <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-semibold text-gray-700">Overall Completion</span>
                    <span class="text-sm font-bold text-emerald-600">{{ Math.round(overallProgress) }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner">
                    <div 
                      class="bg-gradient-to-r from-emerald-500 to-blue-500 h-3 rounded-full transition-all duration-500 shadow-sm"
                      :style="{ width: `${overallProgress}%` }"
                    ></div>
                  </div>
                  <p class="text-xs text-gray-500 mt-2">Complete all sections to create client</p>
                </div>
            
                <!-- Enhanced Section List -->
                <div class="space-y-3 mb-6">
                  <div 
                    v-for="section in sections"
                    :key="section.id"
                    :class="[
                      'flex items-center justify-between p-4 rounded-xl cursor-pointer transition-all duration-300 border-2 hover:shadow-md transform hover:scale-105',
                      getSidebarSectionClasses(section.id)
                    ]"
                    @click="activeSection = section.id"
                  >
                    <div class="flex items-center space-x-3">
                      <div :class="[
                        'w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-300',
                        getSectionIconBgClasses(section.id)
                      ]">
                        <component :is="section.icon" :class="getSectionIconClasses(section.id)" class="w-4 h-4" />
                      </div>
                      <div>
                        <span class="text-sm font-semibold block" :class="getSectionTextClasses(section.id)">{{ section.name }}</span>
                        <span class="text-xs opacity-75" :class="getSectionTextClasses(section.id)">
                          {{ getSectionProgress(section.id) === 100 ? 'Complete' : getSectionProgress(section.id) > 0 ? 'In Progress' : 'Not Started' }}
                        </span>
                      </div>
                    </div>
                    <div class="flex items-center space-x-2">
                      <!-- Progress indicator -->
                      <div class="w-12 h-2 bg-gray-200 rounded-full shadow-inner">
                        <div 
                          class="h-2 rounded-full transition-all duration-500"
                          :class="getSectionProgressColor(section.id)"
                          :style="{ width: getSectionProgress(section.id) !== null ? `${getSectionProgress(section.id)}%` : '0%' }"
                        ></div>
                      </div>
                      <!-- Completion status -->
                      <div 
                        v-if="getSectionProgress(section.id) === 100"
                        class="w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-sm"
                      ></div>
                      <div 
                        v-else-if="getSectionProgress(section.id) > 0"
                        class="w-3 h-3 bg-amber-500 rounded-full shadow-sm"
                      ></div>
                      <div 
                        v-else
                        class="w-3 h-3 bg-gray-300 rounded-full shadow-sm"
                      ></div>
                    </div>
                  </div>
            </div>

                <!-- Enhanced Action Buttons -->
                <div class="space-y-3">
                  <button
                    @click="saveClient"
                    :disabled="form.processing"
                    class="w-full bg-gradient-to-r from-emerald-600 to-blue-600 hover:from-emerald-700 hover:to-blue-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center"
                  >
                    <UserPlusIcon v-if="!form.processing" class="w-5 h-5 mr-2" />
                    <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ form.processing ? 'Creating Client...' : 'Create Client' }}
                  </button>
                  
                  <button
                    @click="handleCancel"
                    class="w-full bg-gradient-to-r from-gray-500 to-slate-600 hover:from-gray-600 hover:to-slate-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center"
                  >
                    <XMarkIcon class="w-5 h-5 mr-2" />
                    Cancel
                  </button>
                </div>

                <!-- Enhanced Admin Help Section -->
                <div class="mt-6 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-4 border border-blue-200">
                  <div class="flex items-center mb-3">
                    <div class="w-6 h-6 bg-blue-500 rounded-lg flex items-center justify-center mr-2">
                      <InformationCircleIcon class="w-3 h-3 text-white" />
                    </div>
                    <h4 class="text-sm font-bold text-blue-900">Admin Notes</h4>
                  </div>
                  <p class="text-xs text-blue-700 mb-3 leading-relaxed">
                    Fill out client information sections as needed. Not all sections are required for initial client creation.
                  </p>
                  
                  <!-- Section-specific help -->
                  <div v-if="getSectionHelp(activeSection)" class="mb-3 p-3 bg-blue-100 rounded-lg border border-blue-200">
                    <p class="text-xs text-blue-800 font-medium">{{ getSectionHelp(activeSection) }}</p>
                  </div>
                  
                  <div class="space-y-2">
                    <button class="block text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline transition-all duration-200">
                      📋 Admin Guidelines
                    </button>
                    <button class="block text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline transition-all duration-200">
                      🔒 Client Data Policy
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
import { route } from 'ziggy-js'
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
  CheckCircleIcon,
  UserPlusIcon,
  ArrowLeftIcon,
  ChartBarIcon,
  XMarkIcon,
  InformationCircleIcon
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

const getTabIconBgClasses = (sectionId) => {
  const isActive = activeSection.value === sectionId
  const bgMap = {
    personal: isActive ? 'bg-blue-500' : 'bg-blue-100',
    spouse: isActive ? 'bg-pink-500' : 'bg-pink-100',
    employee: isActive ? 'bg-purple-500' : 'bg-purple-100',
    projects: isActive ? 'bg-indigo-500' : 'bg-indigo-100',
    assets: isActive ? 'bg-emerald-500' : 'bg-emerald-100',
    expenses: isActive ? 'bg-orange-500' : 'bg-orange-100'
  }
  return bgMap[sectionId] || (isActive ? 'bg-gray-500' : 'bg-gray-100')
}

const getActiveSection = () => {
  return sections.value.find(section => section.id === activeSection.value) || sections.value[0]
}

const getActiveTabIconBgClasses = () => {
  return getTabIconBgClasses(activeSection.value)
}

const getActiveTabIconClasses = () => {
  return 'text-white'
}

const getSectionIconBgClasses = (sectionId) => {
  const isActive = activeSection.value === sectionId
  const bgMap = {
    personal: isActive ? 'bg-blue-500' : 'bg-blue-100',
    spouse: isActive ? 'bg-pink-500' : 'bg-pink-100',
    employee: isActive ? 'bg-purple-500' : 'bg-purple-100',
    projects: isActive ? 'bg-indigo-500' : 'bg-indigo-100',
    assets: isActive ? 'bg-emerald-500' : 'bg-emerald-100',
    expenses: isActive ? 'bg-orange-500' : 'bg-orange-100'
  }
  return bgMap[sectionId] || (isActive ? 'bg-gray-500' : 'bg-gray-100')
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
  router.visit('/admin/clients')
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