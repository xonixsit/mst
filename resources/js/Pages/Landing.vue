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
                <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold">✨ Trusted by 10,000+ Tax Professionals</span>
              </div>
              <h1 class="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent leading-tight">
                Tax Management Simplified
              </h1>
              <p class="text-xl text-gray-600 leading-relaxed max-w-lg">
                Streamline your tax practice with secure document management, automated invoicing, and real-time client communication. All in one platform.
              </p>
            </div>

            <!-- CTA Buttons -->
            <!-- IRS Badge -->
            <div class="flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg w-fit animate-glow-pulse">
              <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.218c0 1.264-.534 2.472-1.469 3.304a3.065 3.065 0 01-2.433 1.068c-.566 0-1.108-.116-1.605-.341a4.5 4.5 0 00-3.422 0 3.066 3.066 0 01-2.433-1.068C2.884 15.545 2.35 14.337 2.35 13.073V6.517a3.066 3.066 0 012.917-3.062zM9 12a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
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
              <a href="tel:+13153072751" class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-4 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl group">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773c.058.3.102.605.102.924 0 1.748.585 3.364 1.56 4.657l1.548.773a1 1 0 01.54 1.06l-.74 4.435a1 1 0 01-.986.836H3a1 1 0 01-1-1V3z"></path>
                </svg>
                <div class="text-left">
                  <div class="text-xs opacity-90">Call Now!</div>
                  <div class="text-lg font-bold">+1 315-307-2751</div>
                </div>
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

    <!-- Features Section -->
    <section class="px-4 sm:px-6 lg:px-8 bg-gray-50">
      <div class="max-w-7xl mx-auto mt-32">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Everything You Need</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">Powerful features designed to streamline your tax practice and improve client satisfaction</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-200 transition-colors">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Secure Documents</h3>
            <p class="text-gray-600">Enterprise-grade encryption and compliance for all your tax documents</p>
          </div>

          <!-- Feature 2 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-green-200 transition-colors">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Invoice Management</h3>
            <p class="text-gray-600">Automated invoicing with calculations and email delivery</p>
          </div>

          <!-- Feature 3 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-purple-200 transition-colors">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Real-time Communication</h3>
            <p class="text-gray-600">Built-in messaging for seamless client and team collaboration</p>
          </div>

          <!-- Feature 4 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-indigo-200 transition-colors">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Analytics & Reports</h3>
            <p class="text-gray-600">Comprehensive dashboards for business insights and performance</p>
          </div>

          <!-- Feature 5 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-red-200 transition-colors">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Enterprise Security</h3>
            <p class="text-gray-600">Bank-level encryption and compliance certifications</p>
          </div>

          <!-- Feature 6 -->
          <div class="group bg-white border border-gray-200 rounded-xl p-8 hover:shadow-lg transition-all duration-300">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-orange-200 transition-colors">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">24/7 Support</h3>
            <p class="text-gray-600">Dedicated support team available round the clock</p>
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
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import LeadFormModal from '@/Components/LeadFormModal.vue'
import PublicNavbar from '@/Components/PublicNavbar.vue'
import PublicFooter from '@/Components/PublicFooter.vue'
import TestimonialsSection from '@/Components/TestimonialsSection.vue'

const showLeadModal = ref(false)
</script>
