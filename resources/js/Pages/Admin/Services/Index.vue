<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-emerald-50 to-blue-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-emerald-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-blue-100/30 to-transparent rounded-tr-full"></div>
           <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2 pr-2 pl-2">
          <div class="flex items-center space-x-4">
            <!-- Document Management Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
            
              <CogIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-emerald-900 to-blue-900 bg-clip-text text-transparent">
                Tax Services
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Comprehensive tax solutions for individuals and businesses</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-emerald-700">All Services Available</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-blue-700">24/7 Support</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Contact Section -->
          <div class="flex flex-col items-end space-y-2">
            <div class="text-right">
              <p class="text-sm font-semibold text-gray-600">Contact Us Now</p>
              <p class="text-xs text-gray-500 mt-1">If Need Help</p>
            </div>
            <a href="tel:315-307-2751" class="text-2xl font-bold text-emerald-600 hover:text-emerald-700 transition-colors duration-200">
              315-307-2751
            </a>
          </div>
        </div>
      </div>
    </template>

    <div class="space-y-8">
      <!-- Professional Services Hero Section -->
      <div class="bg-gradient-to-br from-teal-50 via-emerald-50 to-green-50 rounded-2xl shadow-xl border border-teal-100 overflow-hidden mb-8">
        <div class="relative p-8 lg:p-12">
          <!-- Background Pattern -->
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-teal-200/20 to-transparent rounded-bl-full"></div>
          <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-emerald-200/20 to-transparent rounded-tr-full"></div>
          
          <!-- Content -->
          <div class="relative text-center">
            <!-- Icon -->
            <div class="w-20 h-20 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
              <DocumentTextIcon class="w-10 h-10 text-white" />
            </div>
            
            <!-- Title -->
            <h2 class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-teal-800 via-emerald-800 to-green-800 bg-clip-text text-transparent mb-4">
              Professional Tax Services
            </h2>
            
            <!-- Description -->
            <p class="text-lg text-teal-700 max-w-4xl mx-auto leading-relaxed mb-8">
              Offering comprehensive services for salaried employees, self-employed professionals, consultants working on client projects, Student visas (F1 OPT/CPT), business owners and High Net Worth Individuals (HNI)
            </p>
            
            <!-- Services Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12">
              <!-- Left Column - Core Services -->
              <div class="bg-white/60 backdrop-blur-sm rounded-xl p-6 border border-teal-200/50 shadow-lg">
                <h3 class="text-xl font-bold text-teal-800 mb-6 flex items-center">
                  <CheckIcon class="w-6 h-6 text-teal-600 mr-3" />
                  Core Tax Services
                </h3>
                <div class="space-y-4">
                  <div class="flex items-start space-x-3">
                    <CheckIcon class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" />
                    <span class="text-teal-700 font-medium">Faster Refunds with direct deposit processing</span>
                  </div>
                  <div class="flex items-start space-x-3">
                    <CheckIcon class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" />
                    <span class="text-teal-700 font-medium">Greater Accuracy through expert review</span>
                  </div>
                  <div class="flex items-start space-x-3">
                    <CheckIcon class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" />
                    <span class="text-teal-700 font-medium">Secure Confidential Submission with encryption</span>
                  </div>
                  <div class="flex items-start space-x-3">
                    <CheckIcon class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" />
                    <span class="text-teal-700 font-medium">Quick Confirmation within 24 hours</span>
                  </div>
                  <div class="flex items-start space-x-3">
                    <CheckIcon class="w-5 h-5 text-teal-600 mt-0.5 flex-shrink-0" />
                    <span class="text-teal-700 font-medium">Hassle-free filing with full support</span>
                  </div>
                </div>
              </div>
              
              <!-- Right Column - Additional Benefits -->
              <div class="bg-white/60 backdrop-blur-sm rounded-xl p-6 border border-emerald-200/50 shadow-lg">
                <h3 class="text-xl font-bold text-emerald-800 mb-6 flex items-center">
                  <CheckIcon class="w-6 h-6 text-emerald-600 mr-3" />
                  Additional Benefits
                </h3>
                <div class="space-y-4">
                  <div class="flex items-start space-x-3">
                    <CheckIcon class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                    <span class="text-emerald-700 font-medium">Remote Desktop Filing from anywhere in the world</span>
                  </div>
                  <div class="flex items-start space-x-3">
                    <CheckIcon class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                    <span class="text-emerald-700 font-medium">Dedicated Tax Team with multinational experience</span>
                  </div>
                  <div class="flex items-start space-x-3">
                    <CheckIcon class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                    <span class="text-emerald-700 font-medium">E-filing facility for all US State Tax Returns</span>
                  </div>
                  <div class="flex items-start space-x-3">
                    <CheckIcon class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                    <span class="text-emerald-700 font-medium">Fully secured website with HTTPS encryption</span>
                  </div>
                  <div class="flex items-start space-x-3">
                    <CheckIcon class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                    <span class="text-emerald-700 font-medium">Competitive fees - less than IRS-decided rates</span>
                  </div>
                  <div class="flex items-start space-x-3">
                    <CheckIcon class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" />
                    <span class="text-emerald-700 font-medium">Authorized e-filers with IRS certification</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Service Categories -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="category in serviceCategories"
          :key="category.id"
          class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 group cursor-pointer transform hover:scale-105"
          @click="navigateToService(category)"
        >
          <div class="p-6">
            <div class="flex items-center space-x-4 mb-6">
              <div :class="category.iconBg" class="w-14 h-14 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                <component :is="category.icon" class="w-7 h-7 text-white" />
              </div>
              <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 group-hover:text-gray-800">{{ category.name }}</h3>
                <p class="text-sm text-gray-600 group-hover:text-gray-700">{{ category.description }}</p>
              </div>
            </div>
            
            <div class="space-y-3 mb-6">
              <div
                v-for="item in category.items"
                :key="item"
                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200"
              >
                <div class="w-2 h-2 bg-emerald-400 rounded-full flex-shrink-0"></div>
                <span class="text-sm text-gray-700 font-medium">{{ item }}</span>
              </div>
            </div>

            <!-- Service Stats -->
            <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl p-4 border border-gray-100">
              <div class="flex items-center justify-between">
                <div class="text-center flex-1">
                  <div class="text-lg font-bold text-gray-900">{{ category.stats.clients }}+</div>
                  <div class="text-xs text-gray-600 font-medium">Clients Served</div>
                </div>
                <div class="w-px h-8 bg-gray-300"></div>
                <div class="text-center flex-1">
                  <div class="text-lg font-bold text-gray-900">{{ category.stats.satisfaction }}%</div>
                  <div class="text-xs text-gray-600 font-medium">Satisfaction</div>
                </div>
                <div class="w-px h-8 bg-gray-300"></div>
                <div class="text-center flex-1">
                  <div class="text-lg font-bold text-gray-900">{{ category.stats.experience }}+</div>
                  <div class="text-xs text-gray-600 font-medium">Years Exp.</div>
                </div>
              </div>
            </div>

            <!-- Hover indicator -->
            <div class="mt-4 text-center">
              <span class="text-xs text-gray-500 group-hover:text-gray-700 font-medium transition-colors duration-200">
                Click to explore {{ category.name.toLowerCase() }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Contact Section -->
      <div class="bg-gradient-to-r from-gray-900 via-emerald-900 to-blue-900 rounded-2xl shadow-2xl overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div class="p-8 lg:p-12">
            <div class="text-center lg:text-left">
              <h2 class="text-2xl lg:text-3xl font-bold text-white mb-4">Contact Us Now</h2>
              <h3 class="text-3xl lg:text-4xl font-bold text-white mb-6">If Need Help</h3>
              <div class="text-4xl lg:text-5xl font-bold text-white mb-6">315-307-2751</div>
              <p class="text-emerald-200 mb-8">Or go to contact form</p>
              <button class="bg-white text-gray-900 px-8 py-4 rounded-xl font-bold hover:bg-gray-100 transition-colors duration-200 shadow-lg">
                LET'S START NOW
              </button>
            </div>
          </div>
          <div class="relative">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/20 to-transparent"></div>
            <div class="h-full bg-gradient-to-br from-emerald-600/20 via-blue-600/20 to-indigo-700/20 flex items-center justify-center">
              <div class="text-center text-white p-8">
                <PhoneIcon class="w-14 h-14 mx-auto mb-4 opacity-50" />
                <p class="text-lg font-medium opacity-75">Ready to get started?</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import {
  CogIcon,
  DocumentTextIcon,
  CheckIcon,
  ChevronRightIcon,
  PhoneIcon,
  UserGroupIcon,
  BuildingOfficeIcon,
  AcademicCapIcon,
  CurrencyDollarIcon,
  ShieldCheckIcon,
  ClipboardDocumentListIcon,
  BanknotesIcon,
  CalculatorIcon,
  GlobeAltIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

// Services menu items
const services = ref([
  {
    id: 1,
    name: 'Federal and State Tax Filing',
    icon: DocumentTextIcon,
    link: '/admin/services/tax-filing'
  },
  {
    id: 2,
    name: 'Corporate Filing',
    icon: BuildingOfficeIcon,
    link: '/admin/services/corporate'
  },
  {
    id: 3,
    name: 'ITIN Assistance',
    icon: ShieldCheckIcon,
    link: '/admin/services/itin'
  },
  {
    id: 4,
    name: 'LLC Incorporation',
    icon: ClipboardDocumentListIcon,
    link: '/admin/services/llc'
  },
  {
    id: 5,
    name: 'Amendment Filing',
    icon: DocumentTextIcon,
    link: '/admin/services/amendment'
  },
  {
    id: 6,
    name: 'IRS Tax Representation',
    icon: UserGroupIcon,
    link: '/admin/services/representation'
  },
  {
    id: 7,
    name: 'FATCA',
    icon: GlobeAltIcon,
    link: '/admin/services/fatca'
  },
  {
    id: 8,
    name: 'FBAR',
    icon: BanknotesIcon,
    link: '/admin/services/fbar'
  },
  {
    id: 9,
    name: 'Payroll',
    icon: CalculatorIcon,
    link: '/admin/services/payroll'
  }
])

// Service categories
const serviceCategories = ref([
  {
    id: 1,
    name: 'Individual Tax Services',
    description: 'Personal tax filing and consultation',
    icon: UserGroupIcon,
    iconBg: 'bg-gradient-to-br from-blue-500 to-blue-600',
    buttonClass: 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white',
    items: [
      'Federal Tax Returns',
      'State Tax Returns',
      'Tax Planning',
      'Refund Optimization'
    ],
    stats: {
      clients: 250,
      satisfaction: 98,
      experience: 15
    }
  },
  {
    id: 2,
    name: 'Business Tax Services',
    description: 'Corporate and business tax solutions',
    icon: BuildingOfficeIcon,
    iconBg: 'bg-gradient-to-br from-emerald-500 to-emerald-600',
    buttonClass: 'bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white',
    items: [
      'Corporate Tax Returns',
      'LLC Tax Filing',
      'Partnership Returns',
      'Business Consultation'
    ],
    stats: {
      clients: 180,
      satisfaction: 96,
      experience: 12
    }
  },
  {
    id: 3,
    name: 'International Services',
    description: 'Global tax compliance and reporting',
    icon: GlobeAltIcon,
    iconBg: 'bg-gradient-to-br from-purple-500 to-purple-600',
    buttonClass: 'bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white',
    items: [
      'FATCA Reporting',
      'FBAR Filing',
      'Foreign Income',
      'Tax Treaties'
    ],
    stats: {
      clients: 120,
      satisfaction: 94,
      experience: 10
    }
  },
  {
    id: 4,
    name: 'Student Services',
    description: 'Specialized services for students',
    icon: AcademicCapIcon,
    iconBg: 'bg-gradient-to-br from-amber-500 to-amber-600',
    buttonClass: 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white',
    items: [
      'F1 Visa Tax Returns',
      'OPT/CPT Filing',
      'Student Tax Credits',
      'Education Credits'
    ],
    stats: {
      clients: 300,
      satisfaction: 97,
      experience: 8
    }
  },
  {
    id: 5,
    name: 'IRS Representation',
    description: 'Professional IRS assistance',
    icon: ShieldCheckIcon,
    iconBg: 'bg-gradient-to-br from-red-500 to-red-600',
    buttonClass: 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white',
    items: [
      'Audit Representation',
      'Tax Resolution',
      'Payment Plans',
      'Penalty Abatement'
    ],
    stats: {
      clients: 85,
      satisfaction: 95,
      experience: 20
    }
  },
  {
    id: 6,
    name: 'Payroll Services',
    description: 'Complete payroll management',
    icon: CalculatorIcon,
    iconBg: 'bg-gradient-to-br from-indigo-500 to-indigo-600',
    buttonClass: 'bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white',
    items: [
      'Payroll Processing',
      'Tax Withholding',
      'Quarterly Reports',
      'Year-end Forms'
    ],
    stats: {
      clients: 65,
      satisfaction: 99,
      experience: 7
    }
  }
])

// Methods
const navigateToService = (category) => {
  // Navigate to specific service page or show more details
  console.log('Navigating to:', category.name)
  // You can implement actual navigation here
}
</script>