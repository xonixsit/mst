<template>
  <AppLayout>
    <template #header>
      <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-blue-50 to-cyan-50"></div>
        <div class="absolute top-0 right-0 w-64 h-32 bg-gradient-to-bl from-blue-100/40 to-transparent rounded-bl-full"></div>
        <div class="absolute bottom-0 left-0 w-48 h-24 bg-gradient-to-tr from-cyan-100/30 to-transparent rounded-tr-full"></div>
        
        <div class="px-10 relative flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0 py-2">
          <div class="flex items-center space-x-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-blue-600 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-blue-100">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </div>
            
            <div>
              <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-cyan-900 bg-clip-text text-transparent">
                Our Services
              </h1>
              <p class="mt-2 text-sm text-gray-600 font-medium">Professional tax services tailored to your needs</p>
              
              <div class="flex items-center space-x-4 mt-3">
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                  <span class="text-xs font-semibold text-blue-700">Professional Services</span>
                </div>
                <div class="flex items-center space-x-2">
                  <div class="w-2 h-2 bg-cyan-400 rounded-full"></div>
                  <span class="text-xs font-semibold text-cyan-700">Expert Support</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <Link
              href="/client/messages"
              class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
              <span class="font-semibold">Contact Us</span>
            </Link>
            
            <Link
              href="/client/dashboard"
              class="bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white px-6 py-3 rounded-xl flex items-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
            >
              <svg class="w-5 h-5 mr-2 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
              </svg>
              <span class="font-semibold">Back to Dashboard</span>
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="space-y-8">
      <!-- Services Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Individual Tax Returns -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all duration-200 hover:shadow-2xl hover:scale-105">
          <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Individual Tax Returns</h3>
            <p class="text-blue-100">Complete tax preparation for individuals and families</p>
          </div>
          <div class="p-6">
            <ul class="space-y-3 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Federal and state tax returns
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Deduction optimization
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Electronic filing
              </li>
            </ul>
          </div>
        </div>

        <!-- Business Tax Services -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all duration-200 hover:shadow-2xl hover:scale-105">
          <div class="bg-gradient-to-r from-emerald-500 to-green-600 p-6">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Business Tax Services</h3>
            <p class="text-emerald-100">Comprehensive business tax solutions</p>
          </div>
          <div class="p-6">
            <ul class="space-y-3 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Corporate tax returns
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Quarterly filings
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Business consulting
              </li>
            </ul>
          </div>
        </div>

        <!-- Tax Planning -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all duration-200 hover:shadow-2xl hover:scale-105">
          <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-6">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Tax Planning</h3>
            <p class="text-purple-100">Strategic tax planning and consultation</p>
          </div>
          <div class="p-6">
            <ul class="space-y-3 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Year-round planning
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Tax strategy optimization
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Investment guidance
              </li>
            </ul>
          </div>
        </div>

        <!-- Bookkeeping -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all duration-200 hover:shadow-2xl hover:scale-105">
          <div class="bg-gradient-to-r from-orange-500 to-red-600 p-6">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Bookkeeping</h3>
            <p class="text-orange-100">Professional bookkeeping and accounting</p>
          </div>
          <div class="p-6">
            <ul class="space-y-3 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Monthly reconciliation
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Financial statements
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Payroll processing
              </li>
            </ul>
          </div>
        </div>

        <!-- IRS Representation -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all duration-200 hover:shadow-2xl hover:scale-105">
          <div class="bg-gradient-to-r from-red-500 to-rose-600 p-6">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">IRS Representation</h3>
            <p class="text-red-100">Expert representation for tax issues</p>
          </div>
          <div class="p-6">
            <ul class="space-y-3 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Audit representation
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Tax resolution
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Payment plans
              </li>
            </ul>
          </div>
        </div>

        <!-- Consultation -->
        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden transform transition-all duration-200 hover:shadow-2xl hover:scale-105">
          <div class="bg-gradient-to-r from-teal-500 to-cyan-600 p-6">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Tax Consultation</h3>
            <p class="text-teal-100">Expert advice and guidance</p>
          </div>
          <div class="p-6">
            <ul class="space-y-3 text-sm text-gray-600">
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                One-on-one consultation
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Tax law updates
              </li>
              <li class="flex items-center">
                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Custom solutions
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Contact Section -->
      <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-8 text-white">
        <div class="text-center">
          <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
          <p class="text-xl text-blue-100 mb-8">Contact us today to discuss your tax needs and how we can help.</p>
          <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
            <Link
              href="/client/messages"
              class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
            >
              Send Message
            </Link>
            <Link
              href="/client/information"
              class="bg-blue-500 text-white px-8 py-4 rounded-xl font-bold hover:bg-blue-400 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
            >
              Update Information
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
</script>