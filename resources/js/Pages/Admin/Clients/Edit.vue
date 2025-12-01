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
            <!-- Edit Client Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-blue-600 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
              <PencilSquareIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-emerald-900 bg-clip-text text-transparent">
                Edit Client
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Update {{ client.user?.first_name }} {{ client.user?.last_name }}'s information</p>
              
              <!-- Progress Indicator -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-blue-700">{{ Math.round(overallProgress) }}% Complete</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-emerald-700">{{ activeSection.charAt(0).toUpperCase() + activeSection.slice(1) }} Section</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex items-center space-x-3">
            <button
              @click="router.visit(route('admin.clients.show', client.id))"
              class="bg-gradient-to-r from-gray-500 to-slate-600 hover:from-gray-600 hover:to-slate-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <EyeIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">View Client</span>
            </button>
            <button
              @click="router.visit(route('admin.clients.index'))"
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
                <p class="text-sm text-gray-600 mt-1">Update client profile sections as needed</p>
              </div>
              <div class="flex items-center space-x-3">
                <div class="bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                  <span class="text-sm font-semibold text-blue-600">{{ Math.round(overallProgress) }}% Complete</span>
                </div>
                <div v-if="autoSaving" class="flex items-center space-x-2 bg-yellow-50 px-3 py-2 rounded-lg border border-yellow-200">
                  <div class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-medium text-yellow-700">Auto-saving...</span>
                </div>
                <div v-else-if="lastSaved" class="flex items-center space-x-2 bg-green-50 px-3 py-2 rounded-lg border border-green-200">
                  <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                  <span class="text-xs font-medium text-green-700">Saved {{ formatTimeAgo(lastSaved) }}</span>
                </div>
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
                :data="client"
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
                  <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <ChartBarIcon class="w-4 h-4 text-white" />
                  </div>
                  <h3 class="text-lg font-bold text-gray-900">Edit Progress</h3>
                </div>
              </div>
              
              <div class="p-6">
                <!-- Overall Progress Bar -->
                <div class="mb-6">
                  <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-semibold text-gray-700">Overall Completion</span>
                    <span class="text-sm font-bold text-blue-600">{{ Math.round(overallProgress) }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner">
                    <div 
                      class="bg-gradient-to-r from-blue-500 to-emerald-500 h-3 rounded-full transition-all duration-500 shadow-sm"
                      :style="{ width: `${overallProgress}%` }"
                    ></div>
                  </div>
                  <p class="text-xs text-gray-500 mt-2">Update sections as needed</p>
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
                    @click="handleSave"
                    :disabled="form.processing"
                    class="w-full bg-gradient-to-r from-blue-600 to-emerald-600 hover:from-blue-700 hover:to-emerald-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center"
                  >
                    <PencilSquareIcon v-if="!form.processing" class="w-5 h-5 mr-2" />
                    <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ form.processing ? 'Updating Client...' : 'Update Client' }}
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
                    <h4 class="text-sm font-bold text-blue-900">Edit Notes</h4>
                  </div>
                  <p class="text-xs text-blue-700 mb-3 leading-relaxed">
                    Update client information sections as needed. Changes are saved when you click Update Client.
                  </p>
                  
                  <div class="space-y-2">
                    <button class="block text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline transition-all duration-200">
                      📋 Edit Guidelines
                    </button>
                    <button class="block text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline transition-all duration-200">
                      🔒 Data Security Policy
                    </button>
                  </div>
                </div>
            
            <!-- Completion Status -->
            <div class="mt-4 p-4 bg-green-50 rounded-lg" v-if="overallProgress >= 80">
              <div class="flex items-center">
                <CheckCircleIcon class="w-5 h-5 text-green-500 mr-2" />
                <span class="text-sm font-medium text-green-800">Ready to update!</span>
              </div>
              <p class="text-xs text-green-700 mt-1">
                Client information is comprehensive and ready for update.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onUnmounted } from 'vue'
import axios from 'axios'
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
  CheckCircleIcon,
  PencilSquareIcon,
  ArrowLeftIcon,
  ChartBarIcon,
  XMarkIcon,
  InformationCircleIcon,
  EyeIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  client: Object,
  visaStatusOptions: {
    type: Array,
    default: () => []
  }
})

// Reactive state
const activeSection = ref('personal')

// Form setup with existing client data
const form = useForm({
  personal: {
    firstName: props.client.user?.first_name || '',
    middleName: props.client.user?.middle_name || '',
    lastName: props.client.user?.last_name || '',
    email: props.client.user?.email || '',
    phone: props.client.phone || '',
    ssn: props.client.ssn || '',
    dateOfBirth: props.client.date_of_birth || '',
    maritalStatus: props.client.marital_status || 'single',
    occupation: props.client.occupation || '',
    insuranceCovered: props.client.insurance_covered || false,
    streetNo: props.client.street_no || '',
    apartmentNo: props.client.apartment_no || '',
    zipCode: props.client.zip_code || '',
    city: props.client.city || '',
    state: props.client.state || '',
    country: props.client.country || 'United States',
    mobileNumber: props.client.mobile_number || '',
    workNumber: props.client.work_number || '',
    visaStatus: props.client.visa_status || '',
    date_of_entry_us: props.client.date_of_entry_us || ''
  },
  spouse: {
    firstName: props.client.spouse?.first_name || '',
    middleName: props.client.spouse?.middle_name || '',
    lastName: props.client.spouse?.last_name || '',
    email: props.client.spouse?.email || '',
    phone: props.client.spouse?.phone || '',
    ssn: props.client.spouse?.ssn || '',
    dateOfBirth: props.client.spouse?.date_of_birth || '',
    occupation: props.client.spouse?.occupation || ''
  },
  employee: [props.client.employees?.[0] || {}],
  projects: props.client.projects || [],
  assets: props.client.assets || [],
  expenses: props.client.expenses || []
})

// Section configuration
const sections = computed(() => [
  {
    id: 'personal',
    name: 'Personal Details',
    icon: UserIcon,
    description: 'Basic client information and contact details'
  },
  {
    id: 'spouse',
    name: 'Spouse Details',
    icon: HeartIcon,
    description: 'Spouse information for married clients'
  },
  {
    id: 'employee',
    name: 'Employment',
    icon: BriefcaseIcon,
    description: 'Employment and salary information'
  },
  {
    id: 'projects',
    name: 'Projects',
    icon: FolderIcon,
    description: 'Tax-related projects and business activities'
  },
  {
    id: 'assets',
    name: 'Assets',
    icon: CurrencyDollarIcon,
    description: 'Client assets and investments'
  },
  {
    id: 'expenses',
    name: 'Expenses',
    icon: ReceiptPercentIcon,
    description: 'Deductible expenses and tax-related costs'
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
      return null
    case 'assets':
      return null
    case 'expenses':
      return null
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
    personal: 'bg-blue-500',
    spouse: 'bg-rose-500',
    employee: 'bg-indigo-500',
    projects: 'bg-purple-500',
    assets: 'bg-emerald-500',
    expenses: 'bg-orange-500'
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

// Methods
const handleSectionUpdate = () => {
  // Handle section updates
}

const handleEmployeeUpdate = (employeeData) => {
  if (!Array.isArray(form.employee)) {
    form.employee = []
  }
  if (form.employee.length === 0) {
    form.employee.push({})
  }
  
  form.employee[0] = { ...employeeData }
}

const handleSave = () => {
  const backendData = {
    personal: toSnakeCase(form.personal),
    spouse: toSnakeCase(form.spouse),
    employee: Array.isArray(form.employee) ? form.employee.map(emp => {
      const processedEmployee = { ...emp }
      
      // Remove auto-managed timestamp fields
      delete processedEmployee.created_at
      delete processedEmployee.updated_at
      
      return processedEmployee
    }) : [],
    projects: Array.isArray(form.projects) ? form.projects.map(project => {
      const processedProject = { ...project }
      
      // Remove fields that don't exist in database or are auto-managed
      delete processedProject.taxYear
      delete processedProject.priority
      delete processedProject.created_at
      delete processedProject.updated_at
      
      // Format dates
      if (processedProject.start_date) {
        processedProject.start_date = formatDateForBackend(processedProject.start_date)
      }
      if (processedProject.due_date) {
        processedProject.due_date = formatDateForBackend(processedProject.due_date)
      }
      if (processedProject.completion_date) {
        processedProject.completion_date = formatDateForBackend(processedProject.completion_date)
      }
      return processedProject
    }) : [],
    assets: Array.isArray(form.assets) ? form.assets.map(asset => {
      const processedAsset = { ...asset }
      
      // Remove auto-managed timestamp fields
      delete processedAsset.created_at
      delete processedAsset.updated_at
      
      if (processedAsset.date_of_purchase) {
        processedAsset.date_of_purchase = formatDateForBackend(processedAsset.date_of_purchase)
      }
      return processedAsset
    }) : [],
    expenses: Array.isArray(form.expenses) ? form.expenses.map(expense => {
      const processedExpense = { ...expense }
      
      // Remove auto-managed timestamp fields
      delete processedExpense.created_at
      delete processedExpense.updated_at
      
      // Format date fields
      if (processedExpense.expense_date) {
        processedExpense.expense_date = formatDateForBackend(processedExpense.expense_date)
      }
      
      return processedExpense
    }) : []
  }
  
  form.transform(() => backendData).put(route('admin.clients.update', props.client.id), {
    onSuccess: () => {
      router.visit(route('admin.clients.show', props.client.id))
    },
    onError: (errors) => {
      console.error('Failed to update client:', errors)
    }
  })
}

const handleCancel = () => {
  router.visit(route('admin.clients.show', props.client.id))
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

const formatTimeAgo = (date) => {
  const now = new Date()
  const diff = Math.floor((now - date) / 1000)
  
  if (diff < 60) return 'just now'
  if (diff < 3600) return `${Math.floor(diff / 60)}m ago`
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`
  return `${Math.floor(diff / 86400)}d ago`
}

const formatDateForBackend = (date) => {
  if (!date) return null
  
  try {
    let dateObj
    
    if (typeof date === 'string') {
      if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
        return date
      }
      dateObj = new Date(date)
    } else if (date instanceof Date) {
      dateObj = date
    } else {
      return null
    }
    
    if (isNaN(dateObj.getTime())) {
      console.warn('Invalid date provided:', date)
      return null
    }
    
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
  if (form.personal?.maritalStatus !== 'married') return 100
  
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

const getTabIconBgClasses = (sectionId) => {
  const isActive = activeSection.value === sectionId
  const bgMap = {
    personal: isActive ? 'bg-blue-500' : 'bg-blue-100',
    spouse: isActive ? 'bg-rose-500' : 'bg-rose-100',
    employee: isActive ? 'bg-indigo-500' : 'bg-indigo-100',
    projects: isActive ? 'bg-purple-500' : 'bg-purple-100',
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
    spouse: isActive ? 'bg-rose-500' : 'bg-rose-100',
    employee: isActive ? 'bg-indigo-500' : 'bg-indigo-100',
    projects: isActive ? 'bg-purple-500' : 'bg-purple-100',
    assets: isActive ? 'bg-emerald-500' : 'bg-emerald-100',
    expenses: isActive ? 'bg-orange-500' : 'bg-orange-100'
  }
  return bgMap[sectionId] || (isActive ? 'bg-gray-500' : 'bg-gray-100')
}

// Auto-save functionality
const autoSaveTimer = ref(null)
const lastSaved = ref(null)
const autoSaving = ref(false)

const autoSave = async () => {
  if (autoSaving.value || form.processing) return
  
  try {
    autoSaving.value = true
    
    const backendData = {
      personal: form.personal,
      spouse: form.spouse,
      employee: Array.isArray(form.employee) ? form.employee.map(emp => {
        const processedEmployee = { ...emp }
        
        // Remove auto-managed timestamp fields
        delete processedEmployee.created_at
        delete processedEmployee.updated_at
        
        return processedEmployee
      }) : [],
      projects: Array.isArray(form.projects) ? form.projects.map(project => {
        const processedProject = { ...project }
        
        // Remove fields that don't exist in database or are auto-managed
        delete processedProject.taxYear
        delete processedProject.priority
        delete processedProject.created_at
        delete processedProject.updated_at
        
        // Format dates
        if (processedProject.start_date) {
          processedProject.start_date = formatDateForBackend(processedProject.start_date)
        }
        if (processedProject.due_date) {
          processedProject.due_date = formatDateForBackend(processedProject.due_date)
        }
        if (processedProject.completion_date) {
          processedProject.completion_date = formatDateForBackend(processedProject.completion_date)
        }
        return processedProject
      }) : [],
      assets: Array.isArray(form.assets) ? form.assets.map(asset => {
        const processedAsset = { ...asset }
        
        // Remove auto-managed timestamp fields
        delete processedAsset.created_at
        delete processedAsset.updated_at
        
        if (processedAsset.date_of_purchase) {
          processedAsset.date_of_purchase = formatDateForBackend(processedAsset.date_of_purchase)
        }
        return processedAsset
      }) : [],
      expenses: Array.isArray(form.expenses) ? form.expenses.map(expense => {
        const processedExpense = { ...expense }
        
        // Remove auto-managed timestamp fields
        delete processedExpense.created_at
        delete processedExpense.updated_at
        
        // Format date fields
        if (processedExpense.expense_date) {
          processedExpense.expense_date = formatDateForBackend(processedExpense.expense_date)
        }
        
        return processedExpense
      }) : []
    }
    
    await axios.put(route('admin.clients.update', props.client.id), backendData)
    lastSaved.value = new Date()
  } catch (error) {
    console.error('Auto-save failed:', error)
  } finally {
    autoSaving.value = false
  }
}

const scheduleAutoSave = () => {
  // Temporarily disabled auto-save due to authentication issues
  return
  
  if (autoSaveTimer.value) {
    clearTimeout(autoSaveTimer.value)
  }
  
  autoSaveTimer.value = setTimeout(() => {
    autoSave()
  }, 3000) // Auto-save after 3 seconds of inactivity
}

// Watch form changes to trigger completion recalculation and auto-save
watch(() => form.data(), () => {
  // This will trigger reactivity for completion calculations
  scheduleAutoSave()
}, { deep: true })

// Cleanup timer on unmount
onUnmounted(() => {
  if (autoSaveTimer.value) {
    clearTimeout(autoSaveTimer.value)
  }
})
</script>

