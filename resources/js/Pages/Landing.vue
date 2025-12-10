<template>
  <div class="bg-white">
    <!-- Navigation Header -->
    <nav class="fixed top-0 w-full bg-white/95 backdrop-blur-md border-b border-gray-200 z-50 shadow-sm h-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex justify-between items-center h-full">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 rounded-xl flex items-center justify-center shadow-lg ring-2 ring-white/20 flex-shrink-0">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <div>
              <span class="text-xl font-bold text-gray-900">MySuperTax</span>
              <p class="text-xs text-gray-600 font-medium">Professional Services</p>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <Link href="/client/login" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
              Client Login
            </Link>
          </div>
        </div>
      </div>
    </nav>
    <!-- Spacer for fixed nav -->
    <div class="h-16"></div>
    <!-- Hero Section -->
    <section class="pb-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-blue-50 to-white">
      <div class="max-w-7xl mx-auto">
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
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
              <Link href="/client/login" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 group">
                <span>Start Free Trial</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
              </Link>
              <button class="border-2 border-gray-300 text-gray-700 hover:border-gray-400 hover:bg-gray-50 px-8 py-4 rounded-xl font-semibold transition-all duration-300">
                Schedule Demo
              </button>
            </div>

            <!-- Trust Indicators -->
            <div class="grid grid-cols-3 gap-6 pt-8 border-t border-gray-200">
              <div>
                <div class="text-3xl font-bold text-gray-900">10K+</div>
                <div class="text-sm text-gray-600 mt-1">Active Users</div>
              </div>
              <div>
                <div class="text-3xl font-bold text-gray-900">99.9%</div>
                <div class="text-sm text-gray-600 mt-1">Uptime SLA</div>
              </div>
              <div>
                <div class="text-3xl font-bold text-gray-900">24/7</div>
                <div class="text-sm text-gray-600 mt-1">Support</div>
              </div>
            </div>
          </div>

          <!-- Right Visual - Dashboard Preview -->
          <div class="relative">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
              <!-- Dashboard Header -->
              <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-white/20 rounded-lg"></div>
                  <div class="h-2 bg-white/20 rounded w-24"></div>
                </div>
                <div class="flex space-x-2">
                  <div class="w-3 h-3 bg-white/20 rounded-full"></div>
                  <div class="w-3 h-3 bg-white/20 rounded-full"></div>
                </div>
              </div>
              <!-- Dashboard Content -->
              <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                  <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="h-3 bg-blue-200 rounded w-1/2 mb-3"></div>
                    <div class="h-6 bg-blue-300 rounded w-2/3"></div>
                  </div>
                  <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <div class="h-3 bg-green-200 rounded w-1/2 mb-3"></div>
                    <div class="h-6 bg-green-300 rounded w-2/3"></div>
                  </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                  <div class="h-2 bg-gray-200 rounded w-full"></div>
                  <div class="h-2 bg-gray-200 rounded w-5/6"></div>
                  <div class="h-2 bg-gray-200 rounded w-4/6"></div>
                </div>
              </div>
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
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
      <div class="max-w-4xl mx-auto">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-12 text-center my-16">
          <h2 class="text-4xl font-bold text-white mb-4">Ready to Get Started?</h2>
          <p class="text-lg text-purple-100 mb-8 max-w-2xl mx-auto">Join thousands of tax professionals streamlining their practice with MySuperTax</p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/client/login" class="bg-white text-purple-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-semibold transition-all duration-300 shadow-lg">
              Start Free Trial
            </Link>
            <button class="border-2 border-white text-white hover:bg-white/10 px-8 py-4 rounded-xl font-semibold transition-all duration-300">
              Schedule Demo
            </button>
          </div>
        </div>
        </div>
    </section>
    <div class="h-16"></div>

    <!-- Contact Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
      <div class="max-w-4xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent mb-4">Get in Touch</h2>
          <p class="text-xl text-gray-600">Have questions? We're here to help.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
          <!-- Email -->
          <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Email</h3>
            <p class="text-gray-600 mb-4">support@mysupertax.com</p>
            <a href="mailto:support@mysupertax.com" class="text-blue-600 hover:text-blue-700 font-semibold">Send Email</a>
          </div>

          <!-- Phone -->
          <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Phone</h3>
            <p class="text-gray-600 mb-4">+1 (555) 123-4567</p>
            <a href="tel:+15551234567" class="text-green-600 hover:text-green-700 font-semibold">Call Now</a>
          </div>

          <!-- Address -->
          <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 text-center">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Address</h3>
            <p class="text-gray-600 mb-4">123 Tax Street, Suite 100<br>New York, NY 10001</p>
            <a href="#" class="text-purple-600 hover:text-purple-700 font-semibold">View Map</a>
          </div>
        </div>

        <!-- Business Hours -->
        <div class="bg-white rounded-xl p-8 shadow-lg">
          <h3 class="text-xl font-bold text-gray-900 mb-6 text-center">Business Hours</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
              <p class="text-gray-600 mb-2"><span class="font-semibold text-gray-900">Monday - Friday:</span> 9:00 AM - 6:00 PM EST</p>
              <p class="text-gray-600"><span class="font-semibold text-gray-900">Saturday:</span> 10:00 AM - 4:00 PM EST</p>
            </div>
            <div>
              <p class="text-gray-600 mb-2"><span class="font-semibold text-gray-900">Sunday:</span> Closed</p>
              <p class="text-gray-600"><span class="font-semibold text-gray-900">Holidays:</span> Closed</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        <div class="border-t border-gray-700 pt-8 flex flex-col sm:flex-row justify-between items-center">
          <p class="text-gray-400 text-sm">&copy; 2025 MySuperTax. All rights reserved.</p>
          <div class="flex space-x-6 mt-4 sm:mt-0">
            <a href="/legal/terms" class="text-gray-400 hover:text-white transition-colors text-sm">Terms</a>
            <a href="/legal/privacy" class="text-gray-400 hover:text-white transition-colors text-sm">Privacy</a>
            <a href="/legal/disclaimer" class="text-gray-400 hover:text-white transition-colors text-sm">Disclaimer</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
