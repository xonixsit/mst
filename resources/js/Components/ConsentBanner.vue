<template>
  <div v-if="showBanner" class="fixed bottom-0 left-0 right-0 z-50 bg-gradient-to-r from-slate-900 via-blue-900 to-slate-900 border-t border-blue-800/50 shadow-2xl" style="display: block !important;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <!-- Content -->
        <div class="flex-1">
          <h3 class="text-white font-bold text-lg mb-2">Privacy & Cookie Consent</h3>
          <p class="text-gray-300 text-sm mb-4">
            We use cookies and tracking technologies to enhance your experience, analyze usage, and improve our services. 
            <Link href="/legal/privacy" class="text-blue-400 hover:text-blue-300 underline">Learn more about our privacy practices</Link>.
          </p>
          
          <!-- Consent Options -->
          <div class="space-y-2 mb-4">
            <label class="flex items-center gap-3 cursor-pointer">
              <input 
                type="checkbox" 
                v-model="consents.essential" 
                disabled
                class="w-4 h-4 rounded bg-blue-600 border-blue-500 cursor-not-allowed"
              />
              <span class="text-gray-300 text-sm">
                <span class="font-semibold">Essential Cookies</span> (Required for functionality)
              </span>
            </label>
            
            <label class="flex items-center gap-3 cursor-pointer">
              <input 
                type="checkbox" 
                v-model="consents.analytics"
                class="w-4 h-4 rounded bg-blue-600 border-blue-500 cursor-pointer"
              />
              <span class="text-gray-300 text-sm">
                <span class="font-semibold">Analytics</span> (Help us improve your experience)
              </span>
            </label>
            
            <label class="flex items-center gap-3 cursor-pointer">
              <input 
                type="checkbox" 
                v-model="consents.marketing"
                class="w-4 h-4 rounded bg-blue-600 border-blue-500 cursor-pointer"
              />
              <span class="text-gray-300 text-sm">
                <span class="font-semibold">Marketing</span> (Personalized content and offers)
              </span>
            </label>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
          <button
            @click="rejectAll"
            class="px-6 py-2 rounded-lg border border-gray-500 text-gray-300 hover:text-white hover:border-gray-300 transition-colors text-sm font-medium"
          >
            Reject All
          </button>
          <button
            @click="acceptSelected"
            class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors text-sm font-medium"
          >
            Accept Selected
          </button>
          <button
            @click="acceptAll"
            class="px-6 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white transition-colors text-sm font-medium"
          >
            Accept All
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'

const showBanner = ref(false)
const consents = ref({
  essential: true,
  analytics: false,
  marketing: false,
})

onMounted(() => {
  checkConsentStatus()
})

const checkConsentStatus = async () => {
  try {
    // Check localStorage first
    const dismissed = localStorage.getItem('consent_banner_dismissed')
    if (dismissed === 'true') {
      showBanner.value = false
      return
    }

    // Try to fetch from API if user is authenticated
    try {
      const response = await fetch('/api/consent')
      if (response.ok) {
        const data = await response.json()
        if (!data.has_consented) {
          showBanner.value = true
        } else {
          consents.value = {
            essential: true,
            analytics: data.consents.analytics || false,
            marketing: data.consents.marketing || false,
          }
          showBanner.value = false
        }
      } else {
        showBanner.value = true
      }
    } catch (error) {
      // If API fails, show banner for unauthenticated users
      showBanner.value = true
    }
  } catch (error) {
    console.error('Error checking consent status:', error)
    showBanner.value = true
  }
}

const submitConsent = async (consentData, action = 'accepted') => {
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
    
    const response = await fetch('/api/consent', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        ...(csrfToken && { 'X-CSRF-Token': csrfToken }),
      },
      body: JSON.stringify({
        consents: consentData,
        source: 'banner',
      }),
    })

    if (response.ok) {
      showBanner.value = false
      localStorage.setItem('consent_banner_dismissed', 'true')
      localStorage.setItem('consent_preferences', JSON.stringify(consentData))
    } else {
      console.error('Failed to submit consent:', response.statusText)
      // Still hide banner even if API fails
      showBanner.value = false
      localStorage.setItem('consent_banner_dismissed', 'true')
    }
  } catch (error) {
    console.error('Error submitting consent:', error)
    // Still hide banner even if request fails
    showBanner.value = false
    localStorage.setItem('consent_banner_dismissed', 'true')
  }
}

const acceptAll = () => {
  const consentData = [
    { type: 'terms', given: true },
    { type: 'privacy', given: true },
    { type: 'analytics', given: true },
    { type: 'marketing', given: true },
    { type: 'cookies', given: true },
  ]
  submitConsent(consentData, 'accepted')
}

const rejectAll = () => {
  const consentData = [
    { type: 'terms', given: true },
    { type: 'privacy', given: true },
    { type: 'analytics', given: false },
    { type: 'marketing', given: false },
    { type: 'cookies', given: false },
  ]
  submitConsent(consentData, 'accepted')
}

const acceptSelected = () => {
  const consentData = [
    { type: 'terms', given: true },
    { type: 'privacy', given: true },
    { type: 'analytics', given: consents.value.analytics },
    { type: 'marketing', given: consents.value.marketing },
    { type: 'cookies', given: consents.value.analytics || consents.value.marketing },
  ]
  submitConsent(consentData, 'accepted')
}
</script>
