<template>
  <div class="admin-client-create">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Create New Client</h1>
        <p class="text-gray-600 mt-1">Add a new client to the system</p>
      </div>
      <button
        @click="$inertia.visit(route('admin.clients.index'))"
        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Clients
      </button>
    </div>

    <div class="bg-white rounded-lg shadow">
      <div class="p-6">
        <ClientInformationManager
          :client="null"
          :is-admin="true"
          @save="handleSave"
          @cancel="handleCancel"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3'
import ClientInformationManager from '../../../Components/ClientInformationManager.vue'

export default {
  name: 'AdminClientCreate',
  components: {
    ClientInformationManager
  },
  setup() {
    const handleSave = (clientData) => {
      router.post(route('admin.clients.store'), clientData, {
        onSuccess: () => {
          // Success message will be handled by the controller redirect
        },
        onError: (errors) => {
          console.error('Failed to create client:', errors)
        }
      })
    }

    const handleCancel = () => {
      router.visit(route('admin.clients.index'))
    }

    return {
      handleSave,
      handleCancel
    }
  }
}
</script>

<style scoped>
.admin-client-create {
  @apply p-6 bg-gray-50 min-h-screen;
}
</style>