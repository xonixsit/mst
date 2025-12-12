<template>
  <div class="bg-white">
    <!-- Navigation -->
    <PublicNavbar />

    <!-- Spacer -->
    <div class="h-16"></div>

    <!-- Hero Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <!-- Left Content -->
          <div class="space-y-6 text-white">
            <div class="inline-block">
              <span class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-full text-sm font-semibold backdrop-blur-sm">Contact Us</span>
            </div>
            <h1 class="text-5xl lg:text-6xl font-bold leading-tight">
              Let's Connect & Grow Together
            </h1>
            <p class="text-xl text-blue-100">
              Have questions about our tax services? Our expert team is ready to help you navigate your tax planning needs with personalized solutions.
            </p>
            <div class="space-y-4 pt-4">
              <!-- Call Now CTA -->
              <a href="tel:+13153072751" class="flex items-center gap-3 bg-white text-blue-600 px-6 py-4 rounded-xl font-semibold hover:bg-blue-50 transition-all shadow-lg group">
                <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773c.058.3.102.605.102.924 0 1.748.585 3.364 1.56 4.657l1.548.773a1 1 0 01.54 1.06l-.74 4.435a1 1 0 01-.986.836H3a1 1 0 01-1-1V3z"></path>
                </svg>
                <div class="text-left">
                  <div class="text-sm text-blue-500">Call Now!</div>
                  <div class="text-lg font-bold">+1 315-307-2751</div>
                </div>
              </a>
              <!-- Quick Response -->
              <p class="text-blue-100 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                Typically respond within 2 hours
              </p>
            </div>
          </div>
          <!-- Right Image -->
          <div class="hidden lg:flex justify-center">
            <div class="relative">
              <div class="absolute inset-0 bg-white opacity-10 rounded-3xl blur-2xl"></div>
              <svg class="w-full h-auto text-white opacity-80" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                <circle cx="200" cy="200" r="150" fill="none" stroke="currentColor" stroke-width="2" opacity="0.3"/>
                <circle cx="200" cy="200" r="100" fill="none" stroke="currentColor" stroke-width="2" opacity="0.5"/>
                <path d="M200 100 L250 150 L250 250 L150 250 L150 150 Z" fill="currentColor" opacity="0.2"/>
                <circle cx="200" cy="180" r="20" fill="currentColor" opacity="0.6"/>
                <path d="M180 220 Q200 240 220 220" stroke="currentColor" stroke-width="3" fill="none" opacity="0.6"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Methods -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Multiple Ways to Reach Us</h2>
          <p class="text-xl text-gray-600">Choose the contact method that works best for you</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
          <!-- Phone -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 border border-blue-100 hover:shadow-xl transition-all text-center group">
            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
              <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773c.058.3.102.605.102.924 0 1.748.585 3.364 1.56 4.657l1.548.773a1 1 0 01.54 1.06l-.74 4.435a1 1 0 01-.986.836H3a1 1 0 01-1-1V3z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Phone Support</h3>
            <p class="text-gray-600 mb-4">Speak with our experts directly</p>
            <a href="tel:+13153072751" class="inline-block text-blue-600 font-bold text-lg hover:text-blue-700 transition-colors">+1 315-307-2751</a>
            <p class="text-sm text-gray-500 mt-3">Mon-Fri: 9AM-6PM EST</p>
          </div>

          <!-- Email -->
          <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 border border-green-100 hover:shadow-xl transition-all text-center group">
            <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
              <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Email Support</h3>
            <p class="text-gray-600 mb-4">Send us your detailed inquiry</p>
            <a href="mailto:support@mysupertax.com" class="inline-block text-green-600 font-bold hover:text-green-700 transition-colors break-all">support@mysupertax.com</a>
            <p class="text-sm text-gray-500 mt-3">Response within 24 hours</p>
          </div>

          <!-- Live Chat -->
          <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 border border-purple-100 hover:shadow-xl transition-all text-center group">
            <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
              <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"></path>
                <path d="M6 11a1 1 0 11-2 0 1 1 0 012 0zM10 11a1 1 0 11-2 0 1 1 0 012 0zM14 11a1 1 0 11-2 0 1 1 0 012 0z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Live Chat</h3>
            <p class="text-gray-600 mb-4">Instant answers to your questions</p>
            <button class="inline-block text-purple-600 font-bold hover:text-purple-700 transition-colors">Start Chat Now</button>
            <p class="text-sm text-gray-500 mt-3">Available 9AM-6PM EST</p>
          </div>
        </div>

        <!-- Business Hours & Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Hours -->
          <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl p-8 border border-gray-200">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
              <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00-.293.707l-1.414 1.414a1 1 0 001.414 1.414L9 9.414V6z" clip-rule="evenodd"></path>
              </svg>
              Business Hours
            </h3>
            <div class="space-y-4">
              <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <p class="text-gray-600 font-semibold">Monday - Friday</p>
                <p class="text-gray-900 font-bold">9:00 AM - 6:00 PM EST</p>
              </div>
              <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <p class="text-gray-600 font-semibold">Saturday</p>
                <p class="text-gray-900 font-bold">10:00 AM - 4:00 PM EST</p>
              </div>
              <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <p class="text-gray-600 font-semibold">Sunday</p>
                <p class="text-gray-900 font-bold">Closed</p>
              </div>
              <div class="flex justify-between items-center">
                <p class="text-gray-600 font-semibold">Holidays</p>
                <p class="text-gray-900 font-bold">Closed</p>
              </div>
            </div>
          </div>

          <!-- Why Contact Us -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 border border-blue-200">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
              <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 7a1 1 0 000 2h6a1 1 0 000-2H8zm0 4a1 1 0 000 2h3a1 1 0 000-2H8z" clip-rule="evenodd"></path>
              </svg>
              Common Inquiries
            </h3>
            <ul class="space-y-3">
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700">Tax planning & strategy</span>
              </li>
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700">Service pricing & packages</span>
              </li>
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700">Account setup & onboarding</span>
              </li>
              <li class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-gray-700">Technical support</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-gray-50 to-white">
      <div class="max-w-3xl mx-auto">
        <div class="text-center mb-12">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Send us a Message</h2>
          <p class="text-lg text-gray-600">Fill out the form below and our team will get back to you within 24 hours with personalized solutions.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
          <form @submit.prevent="submitContactForm" class="space-y-6">
            <!-- Name -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
              <input
                v-model="contactForm.name"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                placeholder="John Doe"
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
              <input
                v-model="contactForm.email"
                type="email"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                placeholder="john@example.com"
              />
            </div>

            <!-- Phone -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
              <input
                v-model="contactForm.phone"
                type="tel"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                placeholder="+1 (555) 123-4567"
              />
            </div>

            <!-- Subject -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Subject *</label>
              <select
                v-model="contactForm.subject"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              >
                <option value="">Select a subject...</option>
                <option value="tax-planning">Tax Planning & Strategy</option>
                <option value="pricing">Pricing & Packages</option>
                <option value="onboarding">Account Setup</option>
                <option value="support">Technical Support</option>
                <option value="other">Other Inquiry</option>
              </select>
            </div>

            <!-- Message -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Message *</label>
              <textarea
                v-model="contactForm.message"
                required
                rows="6"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                placeholder="Tell us more about your inquiry and how we can help..."
              ></textarea>
            </div>

            <!-- Success Message -->
            <div v-if="contactSuccess" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Thank you! We'll be in touch soon.
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="isSubmittingContact"
              class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-4 rounded-lg font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl"
            >
              {{ isSubmittingContact ? 'Sending...' : 'Send Message' }}
            </button>

            <!-- Privacy Note -->
            <p class="text-xs text-gray-500 text-center">
              We respect your privacy. Your information will only be used to respond to your inquiry.
            </p>
          </form>
        </div>
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

const contactForm = ref({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
})

const isSubmittingContact = ref(false)
const contactSuccess = ref(false)

const submitContactForm = async () => {
  isSubmittingContact.value = true
  try {
    const response = await fetch('/contact', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify(contactForm.value)
    })

    if (response.ok) {
      contactSuccess.value = true
      contactForm.value = { name: '', email: '', phone: '', subject: '', message: '' }
      setTimeout(() => {
        contactSuccess.value = false
      }, 3000)
    } else {
      console.error('Form submission failed')
    }
  } catch (error) {
    console.error('Error submitting form:', error)
  } finally {
    isSubmittingContact.value = false
  }
}
</script>
