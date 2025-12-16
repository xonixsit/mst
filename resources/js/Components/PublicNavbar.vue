<template>
  <nav class="fixed top-0 w-full z-50 h-16">
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
          <Link href="/faq" class="px-4 py-2 text-blue-100 hover:text-white font-medium transition-all duration-300 relative group">
            FAQ
            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-400 to-indigo-400 group-hover:w-full transition-all duration-300"></span>
          </Link>
        </div>

        <!-- CTA Buttons -->
        <div class="flex items-center space-x-3">
          <Link href="/client/register" class="hidden sm:inline-flex border-2 border-blue-400 text-blue-100 hover:text-white hover:border-white px-6 py-2 rounded-lg font-semibold transition-all duration-300">
            Register
          </Link>
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
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
