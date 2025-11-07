<template>
  <div class="admin-client-edit">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Client</h1>
        <p class="text-gray-600 mt-1">Update client information</p>
      </div>
      <div class="flex space-x-3">
        <button
          @click="$inertia.visit(route('admin.clients.show', client.id))"
          class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
          </svg>
          View Client
        </button>
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
    </div>

    <div class="bg-white rounded-lg shadow">
      <div class="p-6">
        <ClientInformationManager
          :client="client"
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
  name: 'AdminClientEdit',
  components: {
    ClientInformationManager
  },
  props: {
    client: Object
  },
  setup(props) {
    const handleSave = (clientData) => {
      router.put(route('admin.clients.update', props.client.id), clientData, {
        onSuccess: () => {
          // Success message will be handled by the controller redirect
        },
        onError: (errors) => {
          console.error('Failed to update client:', errors)
        }
      })
    }

    const handleCancel = () => {
      router.visit(route('admin.clients.show', props.client.id))
    }

    return {
      handleSave,
      handleCancel
    }
  }
}
</script>

<style scoped>
.admin-client-edit {
  @apply p-6 bg-gray-50 min-h-screen;
}
</style>