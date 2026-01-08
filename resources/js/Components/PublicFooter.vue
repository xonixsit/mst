<template>
  <footer class="bg-gradient-to-r from-slate-900 via-blue-900 to-slate-900 border-t border-blue-800/50 py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Main Footer Content -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
        <!-- Brand Section -->
        <div class="space-y-6">
          <Link href="/" class="flex items-center space-x-3 group">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 via-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg ring-2 ring-blue-400/30 group-hover:ring-blue-400/60 transition-all duration-300 flex-shrink-0">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <div>
              <span class="text-lg font-bold bg-gradient-to-r from-blue-200 to-indigo-200 bg-clip-text text-transparent">MySuperTax</span>
              <p class="text-xs text-blue-300/70 font-medium">Professional Services</p>
            </div>
          </Link>
          <p class="text-gray-400 text-sm leading-relaxed">
            We are a group of professionals from many reputable consulting companies.
          </p>
          <!-- Social Links -->
          <div class="flex items-center space-x-4">
            <a href="#" class="w-10 h-10 bg-blue-900/50 hover:bg-blue-600 rounded-full flex items-center justify-center transition-colors">
              <svg class="w-5 h-5 text-blue-400 hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-blue-900/50 hover:bg-blue-600 rounded-full flex items-center justify-center transition-colors">
              <svg class="w-5 h-5 text-blue-400 hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-blue-900/50 hover:bg-blue-600 rounded-full flex items-center justify-center transition-colors">
              <svg class="w-5 h-5 text-blue-400 hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                <path d="M16.5 7.5c0 1.933-1.567 3.5-3.5 3.5S9.5 9.433 9.5 7.5 11.067 4 12.5 4s3 1.567 3 3.5M6 20c0-3.314 2.686-6 6-6s6 2.686 6 6" stroke="white" stroke-width="2" fill="none"/>
              </svg>
            </a>
            <a href="#" class="w-10 h-10 bg-blue-900/50 hover:bg-blue-600 rounded-full flex items-center justify-center transition-colors">
              <svg class="w-5 h-5 text-blue-400 hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="1"/>
                <circle cx="19" cy="12" r="1"/>
                <circle cx="5" cy="12" r="1"/>
              </svg>
            </a>
          </div>
        </div>

        <!-- Resources -->
        <div>
          <h3 class="text-white font-bold text-lg mb-6 flex items-center gap-2">
            <span class="text-blue-400">RESOURCE</span>
          </h3>
          <ul class="space-y-3">
            <li>
              <Link href="/" class="text-gray-400 hover:text-blue-400 transition-colors flex items-center gap-2">
                <span class="text-blue-400">–</span> Home
              </Link>
            </li>
            <li>
              <Link href="/about" class="text-gray-400 hover:text-blue-400 transition-colors flex items-center gap-2">
                <span class="text-blue-400">–</span> About Us
              </Link>
            </li>
            <li>
              <Link href="/client/login" class="text-gray-400 hover:text-blue-400 transition-colors flex items-center gap-2">
                <span class="text-blue-400">–</span> Make Payment
              </Link>
            </li>
            <li>
              <Link href="/services" class="text-gray-400 hover:text-blue-400 transition-colors flex items-center gap-2">
                <span class="text-blue-400">–</span> Services
              </Link>
            </li>
            <li>
              <a href="https://sa.www4.irs.gov/wmr/" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-blue-400 transition-colors flex items-center gap-2">
                <span class="text-blue-400">–</span> Check Refund
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
              </a>
            </li>
          </ul>
        </div>

        <!-- Useful Links -->
        <div>
          <h3 class="text-white font-bold text-lg mb-6 flex items-center gap-2">
            <span class="text-blue-400">USEFUL LINKS</span>
          </h3>
          <ul class="space-y-3">
            <li>
              <Link href="/client/login" class="text-gray-400 hover:text-blue-400 transition-colors flex items-center gap-2">
                <span class="text-blue-400">–</span> Login
              </Link>
            </li>
            <li>
              <Link href="/client/register" class="text-gray-400 hover:text-blue-400 transition-colors flex items-center gap-2">
                <span class="text-blue-400">–</span> Register
              </Link>
            </li>
            <li>
              <Link href="/contact" class="text-gray-400 hover:text-blue-400 transition-colors flex items-center gap-2">
                <span class="text-blue-400">–</span> Contact Us
              </Link>
            </li>
            <li>
              <a href="/faq" class="text-gray-400 hover:text-blue-400 transition-colors flex items-center gap-2">
                <span class="text-blue-400">–</span> FAQ
              </a>
            </li>
          </ul>
        </div>

        <!-- Contacts -->
        <div>
          <h3 class="text-white font-bold text-lg mb-6 flex items-center gap-2">
            <span class="text-blue-400">CONTACTS</span>
          </h3>
          <ul class="space-y-4">
            <li class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-400 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
              </svg>
              <a href="tel:+13153072751" class="text-gray-400 hover:text-blue-400 transition-colors">+1 315-307-2751</a>
            </li>
            <li class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-400 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
              </svg>
              <a href="mailto:support@mysupertax.com" class="text-gray-400 hover:text-blue-400 transition-colors break-all">support@mysupertax.com</a>
            </li>
            <li class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-400 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
              </svg>
              <span class="text-gray-400">Moline, IL-61265, USA</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Divider -->
      <div class="border-t border-blue-800/50 my-8"></div>

      <!-- Bottom Footer -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-gray-500 text-sm">
          Copyright © 2025. All rights reserved to <span class="text-blue-400 font-semibold">MySuperTax</span>
        </p>
        <div class="flex items-center gap-6 text-sm flex-wrap justify-center">
          <Link href="/legal/terms" class="text-gray-400 hover:text-blue-400 transition-colors">Terms & Condition</Link>
          <span class="text-gray-700">.</span>
          <Link href="/legal/privacy" class="text-gray-400 hover:text-blue-400 transition-colors">Privacy Policy</Link>
          <span class="text-gray-700">.</span>
          <Link href="/legal/disclaimer" class="text-gray-400 hover:text-blue-400 transition-colors">Disclaimer</Link>
        </div>
      </div>

      <!-- Scroll to Top Button -->
      <button @click="scrollToTop" class="fixed bottom-8 right-8 w-12 h-12 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-full flex items-center justify-center shadow-lg transition-all hover:scale-110">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
      </button>
    </div>
  </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>
