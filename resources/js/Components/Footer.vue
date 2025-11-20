<template>
  <footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Company Info -->
        <div class="col-span-1 md:col-span-2">
          <div class="flex items-center mb-3">
            <h3 class="text-lg font-bold">{{ $page.props.app?.name || 'MySuperTax' }}</h3>
          </div>
          <p class="text-gray-300 mb-3 max-w-md text-sm">
            Professional tax consulting services providing comprehensive tax preparation, 
            planning, and advisory services for individuals and businesses.
          </p>
          <div class="text-xs text-gray-400">
            <p class="mb-1">📧 support@mysupertax.com</p>
            <p class="mb-1">📞 (555) 123-4567</p>
            <p>🏢 123 Tax Street, Finance City, FC 12345</p>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="text-sm font-semibold mb-3">Quick Links</h4>
          <ul class="space-y-1 text-xs">
            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Services</a></li>
            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">About Us</a></li>
            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Contact</a></li>
            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">FAQ</a></li>
            <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Support</a></li>
          </ul>
        </div>

        <!-- Legal Links -->
        <div>
          <h4 class="text-sm font-semibold mb-3">Legal</h4>
          <ul class="space-y-1 text-xs">
            <li><a href="/legal/terms" target="_blank" class="text-gray-300 hover:text-white transition-colors">Terms of Service</a></li>
            <li><a href="/legal/privacy" target="_blank" class="text-gray-300 hover:text-white transition-colors">Privacy Policy</a></li>
            <li><a href="/legal/disclaimer" target="_blank" class="text-gray-300 hover:text-white transition-colors">Disclaimer</a></li>
            <li><a href="#" @click.prevent="showTerms = true" class="text-gray-300 hover:text-white transition-colors cursor-pointer">Quick View Terms</a></li>
          </ul>
        </div>
      </div>

      <!-- Bottom Section -->
      <div class="mt-6 pt-6 border-t border-gray-700">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="text-xs text-gray-400 mb-3 md:mb-0">
            <p>&copy; {{ currentYear }} {{ $page.props.app?.name || 'MySuperTax' }}. All rights reserved.</p>
            <p class="mt-1">Licensed Tax Professional | EIN: 12-3456789</p>
          </div>
          <div class="flex space-x-4 text-xs text-gray-400">
            <span>🔒 Secure & Confidential</span>
            <span>📋 IRS Compliant</span>
            <span>✅ PTIN Certified</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Terms of Service Modal -->
    <div v-if="showTerms" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-4xl max-h-[80vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Terms of Service</h2>
            <button @click="showTerms = false" class="text-gray-500 hover:text-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="prose max-w-none text-gray-700">
            <p class="mb-4"><strong>Effective Date:</strong> January 1, 2025</p>
            
            <h3 class="text-lg font-semibold mb-2">1. Acceptance of Terms</h3>
            <p class="mb-4">By accessing and using MySuperTax services, you agree to be bound by these Terms of Service and all applicable laws and regulations.</p>
            
            <h3 class="text-lg font-semibold mb-2">2. Services Provided</h3>
            <p class="mb-4">MySuperTax provides professional tax preparation, planning, and advisory services. We are not responsible for the accuracy of information provided by clients.</p>
            
            <h3 class="text-lg font-semibold mb-2">3. Client Responsibilities</h3>
            <p class="mb-4">Clients must provide accurate, complete, and timely information. Clients are responsible for reviewing all tax documents before filing.</p>
            
            <h3 class="text-lg font-semibold mb-2">4. Confidentiality</h3>
            <p class="mb-4">We maintain strict confidentiality of all client information in accordance with professional standards and applicable laws.</p>
            
            <h3 class="text-lg font-semibold mb-2">5. Limitation of Liability</h3>
            <p class="mb-4">Our liability is limited to the fees paid for services. We are not liable for penalties, interest, or other consequences resulting from client-provided information.</p>
            
            <h3 class="text-lg font-semibold mb-2">6. Payment Terms</h3>
            <p class="mb-4">Payment is due upon completion of services unless other arrangements have been made. Late payments may incur additional fees.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div v-if="showPrivacy" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-4xl max-h-[80vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Privacy Policy</h2>
            <button @click="showPrivacy = false" class="text-gray-500 hover:text-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="prose max-w-none text-gray-700">
            <p class="mb-4"><strong>Effective Date:</strong> January 1, 2025</p>
            
            <h3 class="text-lg font-semibold mb-2">Information We Collect</h3>
            <p class="mb-4">We collect personal and financial information necessary to provide tax services, including but not limited to SSN, income information, and contact details.</p>
            
            <h3 class="text-lg font-semibold mb-2">How We Use Information</h3>
            <p class="mb-4">Information is used solely for providing tax services, communicating with clients, and meeting legal obligations.</p>
            
            <h3 class="text-lg font-semibold mb-2">Information Sharing</h3>
            <p class="mb-4">We do not sell, trade, or share personal information except as required by law or with explicit client consent.</p>
            
            <h3 class="text-lg font-semibold mb-2">Data Security</h3>
            <p class="mb-4">We implement industry-standard security measures to protect client information, including encryption and secure storage.</p>
            
            <h3 class="text-lg font-semibold mb-2">Data Retention</h3>
            <p class="mb-4">Client records are retained in accordance with IRS requirements and professional standards, typically for 7 years.</p>
            
            <h3 class="text-lg font-semibold mb-2">Your Rights</h3>
            <p class="mb-4">You have the right to access, correct, or request deletion of your personal information, subject to legal requirements.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Disclaimer Modal -->
    <div v-if="showDisclaimer" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-4xl max-h-[80vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Professional Disclaimer</h2>
            <button @click="showDisclaimer = false" class="text-gray-500 hover:text-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="prose max-w-none text-gray-700">
            <h3 class="text-lg font-semibold mb-2">Professional Services</h3>
            <p class="mb-4">MySuperTax provides professional tax preparation and advisory services. We are licensed tax professionals committed to accuracy and compliance.</p>
            
            <h3 class="text-lg font-semibold mb-2">No Guarantee of Results</h3>
            <p class="mb-4">While we strive for accuracy, tax laws are complex and subject to interpretation. We cannot guarantee specific outcomes or refund amounts.</p>
            
            <h3 class="text-lg font-semibold mb-2">Client Information Accuracy</h3>
            <p class="mb-4">The accuracy of tax returns depends on the completeness and accuracy of information provided by clients. Clients are responsible for reviewing all documents.</p>
            
            <h3 class="text-lg font-semibold mb-2">IRS Representation</h3>
            <p class="mb-4">We provide representation services for IRS matters within our scope of practice. Additional fees may apply for complex representation cases.</p>
            
            <h3 class="text-lg font-semibold mb-2">Professional Standards</h3>
            <p class="mb-4">Our services are provided in accordance with IRS Circular 230 and professional ethical standards. We maintain continuing education and professional certifications.</p>
            
            <h3 class="text-lg font-semibold mb-2">Limitation of Scope</h3>
            <p class="mb-4">Our services are limited to tax matters. We do not provide legal, investment, or other financial advice outside our area of expertise.</p>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, computed } from 'vue'

const showTerms = ref(false)
const showPrivacy = ref(false)
const showDisclaimer = ref(false)

const currentYear = computed(() => new Date().getFullYear())
</script>