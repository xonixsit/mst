<template>
  <div class="bg-white">
    <!-- Lead Form Modal -->
    <LeadFormModal :isOpen="showLeadModal" @close="showLeadModal = false" />

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
              <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold">About MySuperTax</span>
            </div>
            <h1 class="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent">
              Trusted Tax Experts
            </h1>
            <p class="text-xl text-gray-600">
              We are a group of professionals from reputable consulting companies with collective industry experience in taxation, finance, bookkeeping, and business consulting.
            </p>
            <p class="text-lg text-gray-600">
              With thorough understanding of tax laws, our professional team helps minimize your tax liabilities and retain more of your hard-earned money.
            </p>
            <!-- IRS Badge -->
            <div class="flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg w-fit animate-glow-pulse">
              <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.218c0 1.264-.534 2.472-1.469 3.304a3.065 3.065 0 01-2.433 1.068c-.566 0-1.108-.116-1.605-.341a4.5 4.5 0 00-3.422 0 3.066 3.066 0 01-2.433-1.068C2.884 15.545 2.35 14.337 2.35 13.073V6.517a3.066 3.066 0 012.917-3.062zM9 12a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
              </svg>
              <span class="text-sm font-semibold text-blue-700">Authorized IRS E-File Provider</span>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 pt-4">
              <button @click="showLeadModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition-all shadow-lg">
                Get Started
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
          <div class="flex justify-center">
            <img src="/images/aboutus.png" alt="MySuperTax Team" class="w-full h-auto rounded-2xl shadow-2xl object-cover" />
          </div>
        </div>
      </div>
    </section>

    <!-- About Us Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <div class="space-y-6">
            <h2 class="text-4xl font-bold text-gray-900">Who We Are</h2>
            <p class="text-lg text-gray-600">
              We are a group of professionals from many reputable consulting companies with a collective industry experience of many years in taxation, finance, bookkeeping, and business consulting.
            </p>
            <p class="text-lg text-gray-600">
              With thorough understanding of tax laws, our professional and expert team helps minimize your tax liabilities and retain more of your hard-earned money. We work closely with you to manage every aspect of your tax planning needs and make sure you feel relaxed while doing business with us.
            </p>
            <p class="text-lg text-gray-600">
              Our firm hires experienced Paid Preparers, Enrolled Agents, and CPAs from the industry. To the extent requested by our clients, we are happy to be involved as knowledgeable advisers to achieve effective business decision-making and help clients reach their goals.
            </p>
          </div>
          <div class="flex justify-center">
            <img src="/images/whoarewe.png" alt="Who We Are" class="w-full h-auto rounded-2xl shadow-2xl object-cover" />
          </div>
        </div>
      </div>
    </section>

    <!-- Our Approach Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Approach</h2>
          <p class="text-xl text-gray-600">Client satisfaction is our number one priority</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 border border-blue-100">
            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Personalized Attention</h3>
            <p class="text-gray-600">We take time to learn your goals and objectives, providing personal and professional attention to every client relationship.</p>
          </div>
          <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 border border-green-100">
            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Partnership Mindset</h3>
            <p class="text-gray-600">We view every relationship as a partnership. Your success is our success, and we're committed to helping you achieve your financial goals.</p>
          </div>
          <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 border border-purple-100">
            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Expert Guidance</h3>
            <p class="text-gray-600">By combining our expertise and the energy of our staff, we deliver knowledgeable advice for effective business decision-making.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Values Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Core Values</h2>
          <p class="text-xl text-gray-600">Principles that guide everything we do</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Integrity</h3>
            <p class="text-gray-600">We maintain the highest ethical standards in all our dealings.</p>
          </div>
          <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Excellence</h3>
            <p class="text-gray-600">We strive for excellence in every service we provide.</p>
          </div>
          <div class="text-center">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Innovation</h3>
            <p class="text-gray-600">We continuously innovate to serve you better.</p>
          </div>
          <div class="text-center">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Partnership</h3>
            <p class="text-gray-600">We work as partners to achieve your goals.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <!-- <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Team</h2>
          <p class="text-xl text-gray-600">Experienced professionals dedicated to your success</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all">
            <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-600"></div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900">Sarah Johnson</h3>
              <p class="text-blue-600 font-semibold mb-2">Founder & CEO</p>
              <p class="text-gray-600 text-sm">CPA with 20+ years of tax expertise and business leadership.</p>
            </div>
          </div>
          <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all">
            <div class="h-48 bg-gradient-to-br from-green-400 to-blue-600"></div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900">Michael Chen</h3>
              <p class="text-green-600 font-semibold mb-2">Chief Technology Officer</p>
              <p class="text-gray-600 text-sm">Tech innovator building secure, scalable tax solutions.</p>
            </div>
          </div>
          <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all">
            <div class="h-48 bg-gradient-to-br from-purple-400 to-pink-600"></div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900">Emily Rodriguez</h3>
              <p class="text-purple-600 font-semibold mb-2">Head of Client Services</p>
              <p class="text-gray-600 text-sm">Dedicated to delivering exceptional client experiences.</p>
            </div>
          </div>
        </div>
      </div>
    </section> -->

    <!-- Testimonials Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <div class="inline-block mb-4">
            <span class="bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-semibold">Client Success Stories</span>
          </div>
          <h2 class="text-4xl font-bold text-gray-900 mb-4">What Our Clients Say</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">Join thousands of satisfied clients who have transformed their tax planning with MySuperTax</p>
        </div>

        <!-- Testimonials Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
          <!-- Testimonial 1 -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 border border-blue-100 shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center mb-4">
              <div class="flex gap-1">
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
              </div>
            </div>
            <p class="text-gray-700 mb-6 leading-relaxed">"MySuperTax transformed how we manage our tax planning. Their expertise and personalized approach saved us thousands in taxes while giving us complete peace of mind."</p>
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">JD</div>
              <div>
                <p class="font-bold text-gray-900">James Davidson</p>
                <p class="text-sm text-gray-600">Business Owner, Tech Startup</p>
              </div>
            </div>
          </div>

          <!-- Testimonial 2 -->
          <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 border border-green-100 shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center mb-4">
              <div class="flex gap-1">
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
              </div>
            </div>
            <p class="text-gray-700 mb-6 leading-relaxed">"The team at MySuperTax is incredibly knowledgeable and responsive. They've been instrumental in helping us navigate complex tax situations as our business has grown."</p>
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold">SM</div>
              <div>
                <p class="font-bold text-gray-900">Sarah Mitchell</p>
                <p class="text-sm text-gray-600">Consultant, Finance Professional</p>
              </div>
            </div>
          </div>

          <!-- Testimonial 3 -->
          <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 border border-purple-100 shadow-lg hover:shadow-xl transition-all">
            <div class="flex items-center mb-4">
              <div class="flex gap-1">
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
              </div>
            </div>
            <p class="text-gray-700 mb-6 leading-relaxed">"As a self-employed consultant, I was overwhelmed with tax planning. MySuperTax made everything simple and helped me keep more of what I earn. Highly recommended!"</p>
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full flex items-center justify-center text-white font-bold">RC</div>
              <div>
                <p class="font-bold text-gray-900">Robert Chen</p>
                <p class="text-sm text-gray-600">Self-Employed Consultant</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Trust Metrics -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-12 text-white">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <div>
              <p class="text-4xl font-bold mb-2">5000+</p>
              <p class="text-blue-100">Satisfied Clients</p>
            </div>
            <div>
              <p class="text-4xl font-bold mb-2">$50M+</p>
              <p class="text-blue-100">Tax Savings Delivered</p>
            </div>
            <div>
              <p class="text-4xl font-bold mb-2">98%</p>
              <p class="text-blue-100">Client Satisfaction Rate</p>
            </div>
            <div>
              <p class="text-4xl font-bold mb-2">15+</p>
              <p class="text-blue-100">Years of Excellence</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Trust Badges Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-white border-t border-gray-200">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- IRS E-File Badge -->
          <div class="flex flex-col items-center text-center">
            <div class="w-24 h-24 mb-4 flex items-center justify-center">
              <svg viewBox="0 0 100 100" class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <!-- Badge Circle -->
                <circle cx="50" cy="50" r="48" fill="none" stroke="#2563eb" stroke-width="2"/>
                <circle cx="50" cy="50" r="45" fill="#eff6ff"/>
                <!-- IRS Shield -->
                <path d="M50 15 L65 25 L65 40 Q65 55 50 65 Q35 55 35 40 L35 25 Z" fill="#2563eb"/>
                <!-- Checkmark -->
                <path d="M45 50 L48 55 L58 42" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Badge Star -->
                <circle cx="70" cy="25" r="6" fill="#fbbf24"/>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Authorized IRS E-File Provider</h3>
            <p class="text-sm text-gray-600">Certified and approved by the IRS for secure electronic filing</p>
          </div>

          <!-- Quality Assurance Badge -->
          <div class="flex flex-col items-center text-center">
            <div class="w-24 h-24 mb-4 flex items-center justify-center">
              <svg viewBox="0 0 100 100" class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <!-- Badge Circle -->
                <circle cx="50" cy="50" r="48" fill="none" stroke="#059669" stroke-width="2"/>
                <circle cx="50" cy="50" r="45" fill="#ecfdf5"/>
                <!-- Quality Seal -->
                <path d="M50 20 L58 35 L75 35 L62 45 L68 60 L50 50 L32 60 L38 45 L25 35 L42 35 Z" fill="#059669"/>
                <!-- Inner Circle -->
                <circle cx="50" cy="50" r="12" fill="white"/>
                <text x="50" y="55" font-size="16" font-weight="bold" fill="#059669" text-anchor="middle">QA</text>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Quality Assurance</h3>
            <p class="text-sm text-gray-600">Rigorous testing and verification for accuracy and compliance</p>
          </div>

          <!-- Security Badge -->
          <div class="flex flex-col items-center text-center">
            <div class="w-24 h-24 mb-4 flex items-center justify-center">
              <svg viewBox="0 0 100 100" class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <!-- Badge Circle -->
                <circle cx="50" cy="50" r="48" fill="none" stroke="#dc2626" stroke-width="2"/>
                <circle cx="50" cy="50" r="45" fill="#fef2f2"/>
                <!-- Lock Shield -->
                <path d="M50 20 L70 30 L70 45 Q70 65 50 75 Q30 65 30 45 L30 30 Z" fill="#dc2626"/>
                <!-- Lock -->
                <rect x="42" y="45" width="16" height="18" rx="2" fill="white"/>
                <path d="M45 45 Q45 40 50 40 Q55 40 55 45" stroke="white" stroke-width="2" fill="none"/>
                <!-- Keyhole -->
                <circle cx="50" cy="54" r="2" fill="#dc2626"/>
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">100% Secure Transaction</h3>
            <p class="text-sm text-gray-600">Bank-level encryption protecting your sensitive data</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-blue-600 to-indigo-600">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl font-bold text-white mb-4">Join Thousands of Satisfied Clients</h2>
        <p class="text-lg text-blue-100 mb-8">Experience the MySuperTax difference today.</p>
        <button @click="showLeadModal = true" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-semibold transition-all shadow-lg">
          Get Started Now
        </button>
      </div>
    </section>

    <!-- Footer -->
    <PublicFooter />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import PublicNavbar from '@/Components/PublicNavbar.vue'
import PublicFooter from '@/Components/PublicFooter.vue'
import LeadFormModal from '@/Components/LeadFormModal.vue'

const showLeadModal = ref(false)
</script>
