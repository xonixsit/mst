<template>
  <div class="bg-white">
    <!-- Navigation -->
    <PublicNavbar />

    <!-- Spacer -->
    <div class="h-16"></div>

    <!-- Hero Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-blue-50 to-white">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <!-- Left Content -->
          <div class="space-y-6">
            <div class="inline-block">
              <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold">Our Services</span>
            </div>
            <h1 class="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent leading-tight">
              Comprehensive Tax Solutions
            </h1>
            <p class="text-lg text-gray-600 leading-relaxed">
              From individual tax returns to complex business structures, we provide expert guidance tailored to your unique needs.
            </p>
            <!-- IRS Badge -->
            <div class="flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg w-fit animate-glow-pulse">
              <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.218c0 1.264-.534 2.472-1.469 3.304a3.065 3.065 0 01-2.433 1.068c-.566 0-1.108-.116-1.605-.341a4.5 4.5 0 00-3.422 0 3.066 3.066 0 01-2.433-1.068C2.884 15.545 2.35 14.337 2.35 13.073V6.517a3.066 3.066 0 012.917-3.062zM9 12a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
              </svg>
              <span class="text-sm font-semibold text-blue-700">Authorized IRS E-File Provider</span>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
              <button class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl flex items-center space-x-2 group">
                <span>Get Started</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
              </button>
              <!-- Contact Number -->
              <a href="tel:+13153072751" class="inline-flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 text-blue-700 hover:bg-blue-100 rounded-lg font-semibold transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773c.058.3.102.605.102.924 0 1.748.585 3.364 1.56 4.657l1.548.773a1 1 0 01.54 1.06l-.74 4.435a1 1 0 01-.986.836H3a1 1 0 01-1-1V3z"></path>
                </svg>
                <span>+1 315-307-2751</span>
              </a>
            </div>
          </div>

          <!-- Right Image -->
          <div class="relative h-96 flex items-center justify-center">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100/40 to-indigo-100/40 rounded-3xl backdrop-blur-xl border border-white/60 shadow-2xl"></div>
            <div class="relative w-full h-full flex items-center justify-center p-4">
              <img 
                src="/images/services.png"
                alt="Tax Services Illustration"
                class="w-full h-full object-contain drop-shadow-2xl"
              />
              <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Grid -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-gray-50 to-white">
      <div class="max-w-7xl mx-auto">
        <!-- Section Header -->
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Expert Services</h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto">Comprehensive tax solutions designed to maximize your returns and minimize your burden</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Service 1: Individual Tax Returns -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-blue-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Individual Tax Returns</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">Expert preparation with maximum deductions and strategic planning for optimal results</p>
              <div class="flex items-center text-blue-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Service 2: Federal and State Tax Filing -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-green-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Federal & State Filing</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">Comprehensive multi-state filing with compliance across all jurisdictions</p>
              <div class="flex items-center text-green-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Service 3: Corporate Filing -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-purple-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Corporate Filing</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">Strategic planning for C-Corps & S-Corps with tax optimization</p>
              <div class="flex items-center text-purple-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Service 4: ITIN Assistance -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-orange-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M10 6H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-5m-4 0V5a2 2 0 10-4 0v5m0 0H5"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">ITIN Assistance</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">Specialized services for non-residents and foreign nationals</p>
              <div class="flex items-center text-orange-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Service 5: LLC Incorporation -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-red-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">LLC Incorporation</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">Formation guidance and tax election strategies for new entities</p>
              <div class="flex items-center text-red-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Service 6: Amendment Filing -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-indigo-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Amendment Filing</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">Professional corrections and amendments for prior year returns</p>
              <div class="flex items-center text-indigo-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Service 7: IRS Tax Representation -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-cyan-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-cyan-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">IRS Representation</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">Expert support for audits, appeals and tax dispute resolution</p>
              <div class="flex items-center text-cyan-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Service 8: FATCA -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-teal-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-teal-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">FATCA Compliance</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">Foreign account compliance and international reporting requirements</p>
              <div class="flex items-center text-teal-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Service 9: FBAR -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-pink-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-pink-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">FBAR Filing</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">Form 114 foreign bank account reporting and compliance</p>
              <div class="flex items-center text-pink-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Service 10: Payroll Services -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-lime-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-lime-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-lime-500 to-lime-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Payroll Services</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">Processing, withholding calculations and quarterly filing</p>
              <div class="flex items-center text-lime-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Service 11: Business Tax Planning -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-amber-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Business Tax Planning</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">Strategic planning and optimization for all business types</p>
              <div class="flex items-center text-amber-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Service 12: Retirement Planning -->
          <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-rose-400 hover:shadow-2xl transition-all duration-300 cursor-pointer overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 rounded-bl-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 group-hover:shadow-xl transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-gray-900 mb-3">Retirement Planning</h3>
              <p class="text-gray-600 text-sm leading-relaxed mb-4">401(k), IRA and retirement account optimization strategies</p>
              <div class="flex items-center text-rose-600 font-semibold text-sm group-hover:translate-x-1 transition-transform">
                Learn more <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-blue-600 to-indigo-600">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl font-bold text-white mb-4">Ready to Get Started?</h2>
        <p class="text-lg text-blue-100 mb-8">Let our experts help you optimize your tax strategy today.</p>
        <button class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-semibold transition-all shadow-lg">
          Schedule Consultation
        </button>
      </div>
    </section>

    <!-- Footer -->
    <PublicFooter />
  </div>
</template>

<script setup>
import PublicNavbar from '@/Components/PublicNavbar.vue'
import PublicFooter from '@/Components/PublicFooter.vue'
</script>
