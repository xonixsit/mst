<template>
  <AppLayout title="Tax Professional Details">
    <template #header>
      <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-emerald-50 to-green-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-emerald-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-green-100/30 to-transparent rounded-tr-full"></div>
        
        <!-- Content -->
        <div class="relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2 pr-2 pl-2">
          <div class="flex items-center space-x-4">
            <!-- Document Management Icon -->
            <div class="w-14 h-14 bg-gradient-to-br from-emarald-500 via-green-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
             <UserIcon class="w-8 h-8 text-white" />
            </div>
            
            <!-- Title Section -->
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-emerald-900 to-green-900 bg-clip-text text-transparent">
                {{ taxProfessional.first_name }} {{ taxProfessional.last_name }}
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Tax Professional Profile</p>
              
              <!-- Status Indicators -->
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div :class="taxProfessional.deleted_at ? 'bg-red-400' : 'bg-green-400'" class="w-2 h-2 rounded-full"></div>
                  <span :class="taxProfessional.deleted_at ? 'text-red-700' : 'text-green-700'" class="text-xs font-semibold">
                    {{ taxProfessional.deleted_at ? 'Inactive' : 'Active' }}
                  </span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-blue-700">{{ stats.total_clients }} Clients</span>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex items-center space-x-3">
            <Link
              :href="`/admin/tax-professionals/${taxProfessional.id}/edit`"
              class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <PencilIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">Edit Profile</span>
            </Link>
            <Link
              href="/admin/tax-professionals"
              class="bg-gradient-to-r from-slate-500 to-gray-600 hover:from-slate-600 hover:to-gray-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <ArrowLeftIcon class="w-5 h-5 mr-2" />
              <span class="font-semibold">Back to List</span>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-4">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Total Clients -->
          <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-blue-100 to-blue-200 rounded-xl shadow-lg border border-blue-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-blue-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Total Clients</p>
                  <p class="text-3xl font-bold text-blue-900 mt-2">{{ stats.total_clients }}</p>
                </div>
                <div class="p-4 bg-blue-500 rounded-xl shadow-lg group-hover:bg-blue-600 transition-colors duration-300">
                  <UsersIcon class="w-8 h-8 text-white" />
                </div>
              </div>
            </div>
          </div>

          <!-- Active Clients -->
          <div class="relative overflow-hidden bg-gradient-to-br from-green-50 via-green-100 to-green-200 rounded-xl shadow-lg border border-green-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-green-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-green-700 uppercase tracking-wide">Active Clients</p>
                  <p class="text-3xl font-bold text-green-900 mt-2">{{ stats.active_clients }}</p>
                </div>
                <div class="p-4 bg-green-500 rounded-xl shadow-lg group-hover:bg-green-600 transition-colors duration-300">
                  <CheckCircleIcon class="w-8 h-8 text-white" />
                </div>
              </div>
            </div>
          </div>

          <!-- Total Invoices -->
          <div class="relative overflow-hidden bg-gradient-to-br from-purple-50 via-purple-100 to-purple-200 rounded-xl shadow-lg border border-purple-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-purple-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-purple-700 uppercase tracking-wide">Total Invoices</p>
                  <p class="text-3xl font-bold text-purple-900 mt-2">{{ stats.total_invoices }}</p>
                </div>
                <div class="p-4 bg-purple-500 rounded-xl shadow-lg group-hover:bg-purple-600 transition-colors duration-300">
                  <DocumentTextIcon class="w-8 h-8 text-white" />
                </div>
              </div>
            </div>
          </div>

          <!-- Total Revenue -->
          <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 via-emerald-100 to-emerald-200 rounded-xl shadow-lg border border-emerald-200/50 hover:shadow-xl transition-all duration-300 group">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-emerald-300/30 to-transparent rounded-bl-full"></div>
            <div class="relative p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-semibold text-emerald-700 uppercase tracking-wide">Total Revenue</p>
                  <p class="text-3xl font-bold text-emerald-900 mt-2">${{ formatCurrency(stats.total_revenue) }}</p>
                </div>
                <div class="p-4 bg-emerald-500 rounded-xl shadow-lg group-hover:bg-emerald-600 transition-colors duration-300">
                  <CurrencyDollarIcon class="w-8 h-8 text-white" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Professional Information -->
          <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-xl font-bold text-gray-900">Professional Information</h3>
                  <p class="text-sm text-gray-600 mt-1">Complete professional profile details</p>
                </div>
                <div class="flex items-center space-x-2">
                  <UserIcon class="w-5 h-5 text-gray-400" />
                </div>
              </div>
            </div>

            <div class="p-6 space-y-6">
              <!-- Personal Details -->
              <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200">
                <div class="flex items-center mb-4">
                  <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <UserIcon class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-blue-900">Personal Details</h4>
                </div>
                <dl class="space-y-3">
                  <div class="flex items-center justify-between py-2 border-b border-blue-200/50">
                    <dt class="text-sm font-semibold text-blue-700">Full Name:</dt>
                    <dd class="text-sm font-bold text-blue-900">
                      {{ taxProfessional.first_name }} {{ taxProfessional.middle_name }} {{ taxProfessional.last_name }}
                    </dd>
                  </div>
                  <div class="flex items-center justify-between py-2 border-b border-blue-200/50">
                    <dt class="text-sm font-semibold text-blue-700">Email:</dt>
                    <dd class="text-sm font-medium text-blue-900">{{ taxProfessional.email }}</dd>
                  </div>
                  <div v-if="taxProfessional.phone" class="flex items-center justify-between py-2 border-b border-blue-200/50">
                    <dt class="text-sm font-semibold text-blue-700">Phone:</dt>
                    <dd class="text-sm font-medium text-blue-900">{{ taxProfessional.phone }}</dd>
                  </div>
                  <div class="flex items-center justify-between py-2">
                    <dt class="text-sm font-semibold text-blue-700">Status:</dt>
                    <dd>
                      <span :class="taxProfessional.deleted_at ? 'bg-red-100 text-red-800 border-red-200' : 'bg-green-100 text-green-800 border-green-200'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-sm">
                        <div :class="taxProfessional.deleted_at ? 'bg-red-500' : 'bg-green-500'" class="w-1.5 h-1.5 rounded-full mr-2"></div>
                        {{ taxProfessional.deleted_at ? 'Inactive' : 'Active' }}
                      </span>
                    </dd>
                  </div>
                </dl>
              </div>

              <!-- Address Information -->
              <div v-if="taxProfessional.address || taxProfessional.city || taxProfessional.state" class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-6 border border-purple-200">
                <div class="flex items-center mb-4">
                  <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                    <MapPinIcon class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-purple-900">Address</h4>
                </div>
                <div class="text-sm text-purple-800 leading-relaxed">
                  <div v-if="taxProfessional.address">{{ taxProfessional.address }}</div>
                  <div v-if="taxProfessional.city || taxProfessional.state || taxProfessional.zip_code">
                    {{ taxProfessional.city }}{{ taxProfessional.city && (taxProfessional.state || taxProfessional.zip_code) ? ', ' : '' }}{{ taxProfessional.state }} {{ taxProfessional.zip_code }}
                  </div>
                </div>
              </div>

              <!-- Professional Details -->
              <div class="bg-gradient-to-br from-amber-50 to-yellow-100 rounded-xl p-6 border border-amber-200">
                <div class="flex items-center mb-4">
                  <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center mr-3">
                    <AcademicCapIcon class="w-5 h-5 text-white" />
                  </div>
                  <h4 class="text-lg font-bold text-amber-900">Professional Details</h4>
                </div>
                <dl class="space-y-3">
                  <div v-if="taxProfessional.license_number" class="flex items-center justify-between py-2 border-b border-amber-200/50">
                    <dt class="text-sm font-semibold text-amber-700">License Number:</dt>
                    <dd class="text-sm font-bold text-amber-900">{{ taxProfessional.license_number }}</dd>
                  </div>
                  <div class="flex items-center justify-between py-2">
                    <dt class="text-sm font-semibold text-amber-700">Member Since:</dt>
                    <dd class="text-sm font-medium text-amber-900">{{ formatDate(taxProfessional.created_at) }}</dd>
                  </div>
                </dl>
              </div>
            </div>
          </div>

          <!-- Specializations & Bio -->
          <div class="space-y-8">
            <!-- Specializations -->
            <div v-if="specializations.length > 0" class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-xl font-bold text-gray-900">Specializations</h3>
                    <p class="text-sm text-gray-600 mt-1">Areas of expertise</p>
                  </div>
                  <div class="flex items-center space-x-2">
                    <AcademicCapIcon class="w-5 h-5 text-gray-400" />
                  </div>
                </div>
              </div>

              <div class="p-6">
                <div class="grid grid-cols-1 gap-3">
                  <div v-for="specialization in specializations" :key="specialization" class="bg-gradient-to-r from-emerald-50 to-green-100 border border-emerald-200 rounded-lg p-4 flex items-center">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center mr-3">
                      <CheckIcon class="w-4 h-4 text-white" />
                    </div>
                    <span class="text-sm font-semibold text-emerald-900">{{ specialization }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bio -->
            <div v-if="taxProfessional.bio" class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-xl font-bold text-gray-900">Professional Bio</h3>
                    <p class="text-sm text-gray-600 mt-1">About this professional</p>
                  </div>
                  <div class="flex items-center space-x-2">
                    <DocumentTextIcon class="w-5 h-5 text-gray-400" />
                  </div>
                </div>
              </div>

              <div class="p-6">
                <div class="bg-gradient-to-br from-gray-50 to-slate-100 rounded-xl p-6 border border-gray-200">
                  <p class="text-sm text-gray-700 leading-relaxed">{{ taxProfessional.bio }}</p>
                </div>
              </div>
            </div>

            <!-- Recent Clients -->
            <div v-if="taxProfessional.clients && taxProfessional.clients.length > 0" class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="text-xl font-bold text-gray-900">Recent Clients</h3>
                    <p class="text-sm text-gray-600 mt-1">Latest client assignments</p>
                  </div>
                  <div class="flex items-center space-x-2">
                    <UsersIcon class="w-5 h-5 text-gray-400" />
                  </div>
                </div>
              </div>

              <div class="p-6">
                <div class="space-y-3">
                  <div v-for="client in taxProfessional.clients.slice(0, 5)" :key="client.id" class="bg-gradient-to-r from-blue-50 to-indigo-100 border border-blue-200 rounded-lg p-4 flex items-center justify-between">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                        <UserIcon class="w-5 h-5 text-white" />
                      </div>
                      <div>
                        <div class="text-sm font-semibold text-blue-900">
                          {{ client.user?.first_name }} {{ client.user?.last_name }}
                        </div>
                        <div class="text-xs text-blue-600">{{ client.user?.email }}</div>
                      </div>
                    </div>
                    <Link
                      :href="`/admin/clients/${client.id}`"
                      class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-semibold transition-colors duration-200"
                    >
                      View
                    </Link>
                  </div>
                </div>
                <div v-if="taxProfessional.clients.length > 5" class="mt-4 text-center">
                  <Link
                    :href="`/admin/clients?user_id=${taxProfessional.id}`"
                    class="text-blue-600 hover:text-blue-700 text-sm font-semibold"
                  >
                    View All {{ taxProfessional.clients.length }} Clients →
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { 
  UserIcon,
  PencilIcon,
  ArrowLeftIcon,
  UsersIcon,
  CheckCircleIcon,
  DocumentTextIcon,
  CurrencyDollarIcon,
  MapPinIcon,
  AcademicCapIcon,
  CheckIcon,
  PhoneIcon
} from '@heroicons/vue/24/outline'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  taxProfessional: Object,
  stats: Object,
})

const specializations = computed(() => {
  try {
    return JSON.parse(props.taxProfessional.specializations || '[]')
  } catch {
    return []
  }
})

const formatCurrency = (amount) => {
  return parseFloat(amount || 0).toFixed(2)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>