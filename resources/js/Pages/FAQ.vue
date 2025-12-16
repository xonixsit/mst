<template>
  <div class="bg-white">
    <PublicNavbar />
    <div class="h-16"></div>

    <!-- Hero -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-blue-50 to-white">
      <div class="max-w-4xl mx-auto text-center space-y-4">
        <h1 class="text-5xl lg:text-6xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 bg-clip-text text-transparent">
          Frequently Asked Questions
        </h1>
        <p class="text-xl text-gray-600">Find answers to common questions about MySuperTax</p>
      </div>
    </section>

    <!-- Role Selector -->
    <section class="py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-200">
      <div class="max-w-4xl mx-auto">
        <p class="text-center text-gray-700 font-semibold mb-6">Select your role:</p>
        <div class="flex flex-wrap justify-center gap-4">
          <button
            v-for="role in roles"
            :key="role.id"
            @click="selectedRole = role.id"
            :class="[
              'px-6 py-3 rounded-lg font-medium transition-all',
              selectedRole === role.id
                ? 'bg-blue-600 text-white shadow-lg'
                : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-400'
            ]"
          >
            {{ role.label }}
          </button>
        </div>
      </div>
    </section>

    <!-- Content -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="max-w-4xl mx-auto">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search FAQs..."
          class="w-full px-6 py-3 border border-gray-300 rounded-lg mb-12 focus:ring-2 focus:ring-blue-500"
        />

        <div v-if="filteredFaqs.length === 0" class="text-center py-12 text-gray-600">
          No FAQs found
        </div>
        <div v-for="(faq, idx) in filteredFaqs" :key="idx" class="mb-4 border border-gray-200 rounded-lg overflow-hidden">
          <button
            @click="toggleFaq(idx)"
            class="w-full px-6 py-4 text-left bg-gray-50 hover:bg-gray-100 flex items-center justify-between"
          >
            <span class="font-semibold text-gray-900">{{ faq.question }}</span>
            <svg
              :class="['w-5 h-5 text-gray-600 transition-transform', expanded[idx] ? 'rotate-180' : '']"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
          </button>

          <div v-if="expanded[idx]" class="px-6 py-4 bg-white border-t border-gray-200 space-y-3">
            <p v-for="(para, pIdx) in faq.answer" :key="pIdx" class="text-gray-700 leading-relaxed">
              {{ para }}
            </p>
            <div v-if="faq.myth" class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
              <p class="text-sm font-semibold text-red-800 mb-2">❌ Common Myth:</p>
              <p class="text-sm text-red-700">{{ faq.myth }}</p>
            </div>
            <div v-if="faq.tip" class="mt-4 p-4 bg-green-50 border-l-4 border-green-500 rounded">
              <p class="text-sm font-semibold text-green-800 mb-2">💡 Pro Tip:</p>
              <p class="text-sm text-green-700">{{ faq.tip }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-blue-600 to-indigo-600">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Still have questions?</h2>
        <p class="text-blue-100 mb-8">Our tax professionals are ready to help.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Link href="/contact" class="px-8 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100">
            Contact Us
          </Link>
          <Link href="/client/login" class="px-8 py-3 border-2 border-white text-white rounded-lg font-semibold hover:bg-white/10">
            Sign In
          </Link>
        </div>
      </div>
    </section>

    <PublicFooter />
  </div>
  <ConsentBanner />
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import PublicNavbar from '@/Components/PublicNavbar.vue'
import PublicFooter from '@/Components/PublicFooter.vue'
import ConsentBanner from '@/Components/ConsentBanner.vue'

const selectedRole = ref('all')
const searchQuery = ref('')
const expanded = ref({})

const roles = [
  { id: 'all', label: 'All Users' },
  { id: 'visitor', label: 'Prospective Clients' },
  { id: 'client', label: 'Registered Users' },
  { id: 'professional', label: 'Tax Professionals' },
]

const allFaqs = {
  all: [
    { question: 'What is MySuperTax?', answer: ['MySuperTax is a comprehensive tax preparation and consulting platform for U.S. citizens and residents.', 'We provide professional tax services through a secure digital platform that connects clients with experienced tax professionals.'], tip: 'Explore our Services page to learn more.' },
    { question: 'Is MySuperTax secure?', answer: ['Yes, we comply with IRS Publication 4557 and use AES-256 encryption.', 'All data is encrypted both in transit and at rest.'], myth: 'Online tax services are not secure. Actually, we use enterprise-grade security.' },
    { question: 'How much does it cost?', answer: ['Pricing depends on your tax situation complexity.', 'Contact us for a personalized quote.'] },
  ],
  visitor: [
    { question: 'How do I get started?', answer: ['1. Register for an account', '2. Complete your profile', '3. Upload tax documents', '4. Get assigned a tax professional', '5. Review and approve your return'], tip: 'Have your documents ready before registering.' },
    { question: 'What documents do I need?', answer: ['W-2 forms from all employers', '1099 forms (interest, dividends, etc.)', 'Business records if self-employed', 'Mortgage interest statements', 'Charitable donation receipts'], myth: 'You need every receipt ever. Actually, we only need documentation for claimed deductions.' },
    { question: 'Is there a free consultation?', answer: ['Yes, we offer a free initial consultation.', 'Contact us to schedule yours today.'] },
    { question: 'What tax returns can you prepare?', answer: ['Individual income tax returns (Form 1040)', 'Self-employment returns (Schedule C)', 'Business entity returns', 'Amended returns (Form 1040-X)', 'State and local tax returns'] },
  ],
  client: [
    { question: 'How do I update my information?', answer: ['Log in to your account', 'Click on "My Profile"', 'Update your details', 'Click "Save Changes"'] },
    { question: 'How do I communicate with my tax professional?', answer: ['Use secure messaging within the platform', 'Email notifications for updates', 'Schedule video consultations', 'Phone calls if preferred'], tip: 'Use messaging for quick questions - typically answered within 24 hours.' },
    { question: 'How do I upload documents?', answer: ['Log in and go to "My Documents"', 'Click "Upload Document"', 'Select document type and file', 'Add notes if needed', 'Click "Upload"'], tip: 'Upload documents as you receive them throughout the year.' },
    { question: 'What file formats are accepted?', answer: ['PDF (recommended)', 'JPG/JPEG', 'PNG', 'DOC/DOCX', 'XLS/XLSX', 'Maximum file size: 50MB'] },
    { question: 'When will my return be filed?', answer: ['After you provide all documents', 'After your professional completes it', 'After you review and approve', 'After you authorize e-filing', 'Typically 5-10 business days after approval'], myth: 'E-filing is less official than paper. Actually, e-filing is the IRS-preferred method.' },
    { question: 'How do I pay my invoice?', answer: ['Log in and go to "My Invoices"', 'Click on the invoice', 'Click "Pay Now"', 'Select payment method', 'Complete payment'] },
    { question: 'What payment methods do you accept?', answer: ['Credit cards (Visa, Mastercard, Amex, Discover)', 'Debit cards', 'Bank transfers (ACH)', 'PayPal'] },
  ],
  professional: [
    { question: 'What tools are available?', answer: ['Client information management system', 'Secure document storage', 'Tax return preparation software', 'Client communication portal', 'Audit trail and compliance tracking', 'Reporting and analytics dashboard', 'Bulk operations for multiple clients'] },
    { question: 'How do I manage multiple clients?', answer: ['Dashboard showing all client status', 'Bulk operations for common tasks', 'Automated reminders for missing documents', 'Client segmentation and filtering', 'Task assignment and tracking'], tip: 'Use bulk operations to send reminders to multiple clients at once.' },
    { question: 'How does MySuperTax ensure compliance?', answer: ['Complies with IRS Publication 4557', 'Implements GLBA requirements', 'Follows CCPA regulations', 'Maintains comprehensive audit trails', 'Provides compliance reporting'] },
    { question: 'What security measures are in place?', answer: ['AES-256 encryption for data at rest', 'TLS/SSL encryption for data in transit', 'Multi-factor authentication', 'Role-based access controls', 'Regular security audits', 'Comprehensive audit logging'] },
    { question: 'Does MySuperTax provide training?', answer: ['Yes, comprehensive training available', 'Platform orientation and tutorials', 'Tax law updates and guidance', 'Best practices for client management', 'Monthly webinars on tax changes'] },
  ],
}

const filteredFaqs = computed(() => {
  const role = selectedRole.value
  let faqs = []

  if (role === 'all') {
    // Combine FAQs from all roles
    faqs = [
      ...(allFaqs['all'] || []),
      ...(allFaqs['visitor'] || []),
      ...(allFaqs['client'] || []),
      ...(allFaqs['professional'] || []),
    ]
  } else {
    faqs = allFaqs[role] || []
  }

  if (!searchQuery.value) {
    return faqs
  }

  const query = searchQuery.value.toLowerCase()
  return faqs.filter(faq =>
    faq.question.toLowerCase().includes(query) ||
    faq.answer.some(p => p.toLowerCase().includes(query))
  )
})

const toggleFaq = (idx) => {
  expanded.value[idx] = !expanded.value[idx]
}
</script>
