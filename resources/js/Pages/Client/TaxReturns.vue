<template>
  <AppLayout title="Tax Returns">
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-4">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 sm:p-8">
            <div class="mb-6">
              <h2 class="text-2xl font-bold text-gray-900">Tax Returns</h2>
              <p class="mt-1 text-sm text-gray-600">View and manage your tax returns</p>
            </div>

            <!-- Placeholder content -->
            <div class="text-center py-6">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No tax returns available</h3>
              <p class="mt-1 text-sm text-gray-500">
                Your tax returns will appear here once they are processed by your tax professional.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
</script>
