<template>
  <div class="bg-white">
    <!-- Navigation Header -->
    <PublicNavbar />
    <nav class="hidden">
      <!-- Gradient Background with Blur -->
      <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-blue-900 to-slate-900 backdrop-blur-xl"></div>
      
      <!-- Border Accent -->
      <div class="absolute bottom-0 w-full h-px bg-gradient-to-r from-transparent via-blue-400/50 to-transparent"></div>
      
      <!-- Content -->
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex justify-between items-center h-full">
          <!-- Logo Section -->
          <Link href="/" class="flex items-center space-x-3 group">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 via-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg ring-2 ring-blue-300/30 group-hover:ring-blue-300/60 transition-all duration-300 flex-shrink-0">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <div class="hidden sm:block">
              <span class="text-lg font-bold bg-gradient-to-r from-blue-200 to-indigo-200 bg-clip-text text-transparent">MySuperTax</span>
              <p class="text-xs text-blue-300/70 font-medium">Professional Services</p>
            </div>
          </Link>

          <!-- Navigation Menu -->
          <div class="hidden lg:flex items-center space-x-1">
            <Link href="/services" class="px-4 py-2 text-blue-100 hover:text-white font-medium transition-all duration-300 relative group">
              Services
              <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-400 to-indigo-400 group-hover:w-full transition-all duration-300"></span>
            </Link>
            <Link href="/about" class="px-4 py-2 text-blue-100 hover:text-white font-medium transition-all duration-300 relative group">
              About
              <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-400 to-indigo-400 group-hover:w-full transition-all duration-300"></span>
            </Link>
            <Link href="/contact" class="px-4 py-2 text-blue-100 hover:text-white font-medium transition-all duration-300 relative group">
              Contact
              <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-400 to-indigo-400 group-hover:w-full transition-all duration-300"></span>
            </Link>
          </div>

          <!-- CTA Button -->
          <div class="flex items-center space-x-3">
            <Link href="/client/login" class="hidden sm:inline-flex bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-6 py-2.5 rounded-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 transform">
              <span>Client Login</span>
              <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
              </svg>
            </Link>
          </div>
        </div>
      </div>
    </nav>
    <div class="h-16"></div>
    <!-- Spacer for fixed nav -->
    <!-- <div class="h-16"></div> -->
    <!-- Hero Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-blue-50 via-white to-white relative overflow-hidden">
      <!-- Animated Background Elements -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 right-10 w-72 h-72 bg-blue-200/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 left-10 w-96 h-96 bg-indigo-200/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
      </div>

      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <!-- Left Content -->
          <div class="space-y-8">
            <!-- Main Headline -->
            <div class="space-y-6">
              <div class="inline-block">
                <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold">✨ AUTHORIZED E-FILER ACROSS ALL STATES</span>
              </div>
              <h1 class="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent leading-tight">
                Professional Tax Services & Planning
              </h1>
              <p class="text-xl text-gray-600 leading-relaxed max-w-lg">
                Expert tax preparation, planning, and compliance services. We help maximize your benefits through strategic tax planning and proactive recommendations.
              </p>
            </div>

            <!-- CTA Buttons -->
            <!-- IRS Badge -->
            <div class="flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg w-fit animate-glow-pulse">
              <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
              </svg>
              <span class="text-sm font-semibold text-blue-700">Authorized IRS E-File Provider</span>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
              <button @click="showLeadModal = true" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 group">
                <span>Get Free Tax Consultation</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
              </button>
              <!-- Call Now CTA -->
              <a href="tel:+13153072751" class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-4 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl group animate-pulse">
                <div class="text-left">
                  <div class="text-xs opacity-90">Call Now!</div>
                  <div class="text-lg font-bold">+1 315-307-2751</div>
                </div>
                <svg class="w-6 h-6 group-hover:scale-110 transition-transform animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
              </a>
            </div>
          </div>

          <!-- Right Visual - Tax Illustration -->
          <div class="relative h-96 flex items-center justify-center">
            <!-- Glossy Background Card -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100/40 to-indigo-100/40 rounded-3xl backdrop-blur-xl border border-white/60 shadow-2xl"></div>
            
            <!-- Illustration Container -->
            <div class="relative w-full h-full flex items-center justify-center overflow-hidden">
              <img 
                src="/images/tax-illustration.png"
                alt="Tax Management Illustration"
                class="w-full h-full object-contain drop-shadow-2xl"
              />
              
              <!-- Shimmering Overlay -->
              <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-blue-600 to-indigo-600">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
          <!-- Stat 1 -->
          <div class="text-white">
            <div class="text-4xl md:text-5xl font-bold mb-2">
              <span ref="stat1">0</span>+
            </div>
            <div class="text-blue-100 font-medium">Satisfied Clients</div>
          </div>
          
          <!-- Stat 2 -->
          <div class="text-white">
            <div class="text-4xl md:text-5xl font-bold mb-2">
              $<span ref="stat2">0</span>M+
            </div>
            <div class="text-blue-100 font-medium">Tax Savings Delivered</div>
          </div>
          
          <!-- Stat 3 -->
          <div class="text-white">
            <div class="text-4xl md:text-5xl font-bold mb-2">
              <span ref="stat3">0</span>%
            </div>
            <div class="text-blue-100 font-medium">Client Satisfaction Rate</div>
          </div>
          
          <!-- Stat 4 -->
          <div class="text-white">
            <div class="text-4xl md:text-5xl font-bold mb-2">
              <span ref="stat4">0</span>+
            </div>
            <div class="text-blue-100 font-medium">Years of Excellence</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="px-4 sm:px-6 lg:px-8 bg-gray-50">
      <div class="max-w-7xl mx-auto mt-32">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Core Services</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">Comprehensive tax solutions designed to maximize your benefits and ensure compliance</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Feature 1 -->
          <div class="group bg-gradient-to-br from-blue-50 via-white to-blue-50 border border-blue-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-100/30 rounded-full blur-xl"></div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-blue-200/20 rounded-full blur-lg"></div>
            
            <div class="relative z-10">
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors shadow-sm">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">Individual Tax Returns</h3>
              <p class="text-gray-600 mb-4">Expert tax planning and preparation with maximum deductions and strategic planning for optimal results</p>
              <Link href="/services#individual-tax-returns" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold text-sm transition-colors">
                Learn More
                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </Link>
            </div>
          </div>

          <!-- Feature 2 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-green-200 transition-colors">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Corporate Filing</h3>
            <p class="text-gray-600 mb-4">3+ years experience in corporate tax filing with focus on compliance partnership and maximum tax benefits</p>
            <Link href="/services#corporate-filing" class="inline-flex items-center text-green-600 hover:text-green-800 font-semibold text-sm transition-colors">
              Learn More
              <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </Link>
          </div>

          <!-- Feature 3 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-purple-200 transition-colors">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-5m-4 0V5a2 2 0 10-4 0v5m0 0H5"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">ITIN Application</h3>
            <p class="text-gray-600 mb-4">Assistance for non-residents and individuals without SSN to obtain ITIN for federal tax reporting</p>
            <Link href="/services#itin-application" class="inline-flex items-center text-purple-600 hover:text-purple-800 font-semibold text-sm transition-colors">
              Learn More
              <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </Link>
          </div>

          <!-- Feature 4 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-indigo-200 transition-colors">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Payroll Services</h3>
            <p class="text-gray-600 mb-4">Electronic payroll processing, statutory compliance, and comprehensive reporting for management</p>
            <Link href="/services#payroll-services" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-semibold text-sm transition-colors">
              Learn More
              <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </Link>
          </div>

          <!-- Feature 5 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-red-200 transition-colors">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Amendment Filing</h3>
            <p class="text-gray-600 mb-4">Professional corrections using 1040X form for income adjustments, expense additions, and filing status changes</p>
            <Link href="/services#amendment-filing" class="inline-flex items-center text-red-600 hover:text-red-800 font-semibold text-sm transition-colors">
              Learn More
              <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </Link>
          </div>

          <!-- Feature 6 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-orange-200 transition-colors">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">FBAR Filing</h3>
            <p class="text-gray-600 mb-4">Foreign Bank Account Reporting for U.S. persons with foreign accounts exceeding $10,000</p>
            <Link href="/services#fbar-filing" class="inline-flex items-center text-orange-600 hover:text-orange-800 font-semibold text-sm transition-colors">
              Learn More
              <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </Link>
          </div>

          <!-- Feature 7 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-teal-200 transition-colors">
              <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Retirement Planning</h3>
            <p class="text-gray-600 mb-4">401(k) retirement savings plans with up to $18,000 pre-tax contributions and solo 401(k) options</p>
            <Link href="/services#retirement-planning" class="inline-flex items-center text-teal-600 hover:text-teal-800 font-semibold text-sm transition-colors">
              Learn More
              <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </Link>
          </div>

          <!-- Feature 8 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-cyan-200 transition-colors">
              <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Business Tax Planning</h3>
            <p class="text-gray-600 mb-4">Strategic entity structure optimization for sole proprietorship, LLC, S corp, and C corp taxation</p>
            <Link href="/services#business-tax-planning" class="inline-flex items-center text-cyan-600 hover:text-cyan-800 font-semibold text-sm transition-colors">
              Learn More
              <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </Link>
          </div>
        </div>
      </div>
    </section>
    <div class="h-16"></div>

    <!-- CTA Section -->
    <!-- Testimonials Section -->
    <TestimonialsSection />
    <div class="h-16"></div>

    <!-- Footer -->
    <PublicFooter />

    <!-- Lead Form Modal -->
    <LeadFormModal :isOpen="showLeadModal" @close="showLeadModal = false" />
  </div>
  
  <!-- Consent Banner -->
  <ConsentBanner />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import LeadFormModal from '@/Components/LeadFormModal.vue'
import PublicNavbar from '@/Components/PublicNavbar.vue'
import PublicFooter from '@/Components/PublicFooter.vue'
import TestimonialsSection from '@/Components/TestimonialsSection.vue'
import ConsentBanner from '@/Components/ConsentBanner.vue'

const showLeadModal = ref(false)

// Refs for statistics
const stat1 = ref(null)
const stat2 = ref(null)
const stat3 = ref(null)
const stat4 = ref(null)

// Animation function
const animateCounter = (element, target, duration = 2000, suffix = '') => {
  let start = 0
  const increment = target / (duration / 16) // 60fps
  
  const timer = setInterval(() => {
    start += increment
    if (start >= target) {
      element.textContent = target + suffix
      clearInterval(timer)
    } else {
      element.textContent = Math.floor(start) + suffix
    }
  }, 16)
}

// Intersection Observer for triggering animation when in view
const observeStats = () => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        // Animate each statistic
        animateCounter(stat1.value, 5000)
        animateCounter(stat2.value, 50)
        animateCounter(stat3.value, 98)
        animateCounter(stat4.value, 15)
        
        // Stop observing after animation starts
        observer.unobserve(entry.target)
      }
    })
  }, { threshold: 0.5 })
  
  // Observe the first stat element
  if (stat1.value) {
    observer.observe(stat1.value.parentElement.parentElement.parentElement)
  }
}

onMounted(() => {
  // Start observing when component is mounted
  setTimeout(observeStats, 100) // Small delay to ensure refs are available
})
</script>
