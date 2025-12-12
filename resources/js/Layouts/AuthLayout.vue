<template>
  <div class="bg-white min-h-screen flex flex-col">
    <!-- Navigation -->
    <PublicNavbar />

    <!-- Spacer -->
    <div class="h-16"></div>

    <!-- Main Content -->
    <main class="flex-grow">
      <slot />
    </main>

    <!-- Footer -->
    <PublicFooter />
  </div>
</template>

<script setup>
import PublicNavbar from '@/Components/PublicNavbar.vue'
import PublicFooter from '@/Components/PublicFooter.vue'
</script>
