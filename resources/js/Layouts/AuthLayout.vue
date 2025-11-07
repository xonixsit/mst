<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center">
        <div class="flex items-center space-x-2">
          <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-900">MySuperTax</h1>
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        {{ title }}
      </h2>
      <p v-if="subtitle" class="mt-2 text-center text-sm text-gray-600">
        {{ subtitle }}
      </p>
    </div>

    <!-- Main Content -->
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow-xl rounded-lg sm:px-10 border border-gray-200">
        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
          <div class="flex">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="ml-3 text-sm text-green-700">{{ $page.props.flash.success }}</p>
          </div>
        </div>

        <div v-if="$page.props.flash?.error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
          <div class="flex">
            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="ml-3 text-sm text-red-700">{{ $page.props.flash.error }}</p>
          </div>
        </div>

        <!-- Form Content -->
        <slot />
      </div>

      <!-- Footer Links -->
      <div class="mt-6">
        <slot name="footer" />
      </div>
    </div>

    <!-- Footer -->
    <footer class="mt-8 text-center text-sm text-gray-500">
      <p>&copy; {{ new Date().getFullYear() }} MySuperTax. Professional tax consulting platform.</p>
    </footer>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: null
  }
})
</script>