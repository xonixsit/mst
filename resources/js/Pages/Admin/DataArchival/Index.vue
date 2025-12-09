<template>
  <AppLayout title="Data Archival & Retention">
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-4">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <div>
                <h2 class="text-2xl font-bold text-gray-900">Data Archival & Retention Management</h2>
                <p class="mt-1 text-sm text-gray-600">
                  Manage client data lifecycle, archival policies, and compliance requirements
                </p>
              </div>
              <div class="flex space-x-3">
                <Link
                  :href="route('admin.data-archival.archived-clients')"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <ArchiveBoxIcon class="w-4 h-4 mr-2" />
                  View Archived Clients
                </Link>
                <Link
                  :href="route('admin.data-archival.retention-policy')"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <CogIcon class="w-4 h-4 mr-2" />
                  Retention Policy
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <UsersIcon class="h-8 w-8 text-green-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Active Clients</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.active_clients.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <UserMinusIcon class="h-8 w-8 text-yellow-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Inactive Clients</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.inactive_clients.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <ArchiveBoxIcon class="h-8 w-8 text-blue-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Archived Clients</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.archived_clients.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <DocumentIcon class="h-8 w-8 text-purple-600" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Archived Documents</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.archived_documents.toLocaleString() }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recommendations -->
        <div v-if="recommendations.length > 0" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Retention Policy Recommendations</h3>
            <div class="space-y-4">
              <div
                v-for="recommendation in recommendations"
                :key="recommendation.action"
                :class="[
                  'border rounded-lg p-4',
                  recommendation.priority === 'high' ? 'border-red-200 bg-red-50' :
                  recommendation.priority === 'medium' ? 'border-yellow-200 bg-yellow-50' :
                  'border-blue-200 bg-blue-50'
                ]"
              >
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <ExclamationTriangleIcon
                      v-if="recommendation.priority === 'high'"
                      class="h-5 w-5 text-red-400"
                    />
                    <InformationCircleIcon
                      v-else-if="recommendation.priority === 'medium'"
                      class="h-5 w-5 text-yellow-400"
                    />
                    <CheckCircleIcon
                      v-else
                      class="h-5 w-5 text-blue-400"
                    />
                  </div>
                  <div class="ml-3 flex-1">
                    <p :class="[
                      'text-sm font-medium',
                      recommendation.priority === 'high' ? 'text-red-800' :
                      recommendation.priority === 'medium' ? 'text-yellow-800' :
                      'text-blue-800'
                    ]">
                      {{ recommendation.message }}
                    </p>
                    <div class="mt-2">
                      <button
                        v-if="recommendation.action === 'archive_inactive_clients'"
                        @click="showArchiveModal = true"
                        :class="[
                          'text-sm font-medium underline',
                          recommendation.priority === 'high' ? 'text-red-600 hover:text-red-500' :
                          recommendation.priority === 'medium' ? 'text-yellow-600 hover:text-yellow-500' :
                          'text-blue-600 hover:text-blue-500'
                        ]"
                      >
                        Archive Inactive Clients
                      </button>
                      <button
                        v-else-if="recommendation.action === 'delete_old_archived_data'"
                        @click="showDeleteModal = true"
                        :class="[
                          'text-sm font-medium underline',
                          recommendation.priority === 'high' ? 'text-red-600 hover:text-red-500' :
                          recommendation.priority === 'medium' ? 'text-yellow-600 hover:text-yellow-500' :
                          'text-blue-600 hover:text-blue-500'
                        ]"
                      >
                        Review Deletion Policy
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Archive Inactive Clients -->
              <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                  <ArchiveBoxArrowDownIcon class="h-6 w-6 text-blue-600 mr-2" />
                  <h4 class="text-md font-medium text-gray-900">Archive Inactive Clients</h4>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                  Archive clients who have been inactive for a specified period to optimize system performance.
                </p>
                <button
                  @click="showArchiveModal = true"
                  class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  Archive Inactive Clients
                </button>
              </div>

              <!-- Delete Old Archived Data -->
              <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                  <TrashIcon class="h-6 w-6 text-red-600 mr-2" />
                  <h4 class="text-md font-medium text-gray-900">Delete Old Archived Data</h4>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                  Permanently delete archived data that has exceeded the retention period.
                </p>
                <button
                  @click="showDeleteModal = true"
                  class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  Delete Old Data
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Archive Modal -->
    <ArchiveModal
      v-if="showArchiveModal"
      @close="showArchiveModal = false"
      @archive="handleArchive"
    />

    <!-- Delete Modal -->
    <DeleteModal
      v-if="showDeleteModal"
      @close="showDeleteModal = false"
      @delete="handleDelete"
    />
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ArchiveModal from '@/Components/ArchiveModal.vue'
import DeleteModal from '@/Components/DeleteModal.vue'
import {
  ArchiveBoxIcon,
  ArchiveBoxArrowDownIcon,
  CogIcon,
  UsersIcon,
  UserMinusIcon,
  DocumentIcon,
  TrashIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  stats: Object,
  recommendations: Array,
})

const showArchiveModal = ref(false)
const showDeleteModal = ref(false)

const handleArchive = (inactiveDays) => {
  router.post(route('admin.data-archival.archive-inactive-clients'), {
    inactive_days: inactiveDays
  }, {
    onSuccess: () => {
      showArchiveModal.value = false
    }
  })
}

const handleDelete = (archiveDays) => {
  router.post(route('admin.data-archival.permanently-delete-archived-data'), {
    archive_days: archiveDays,
    confirm_deletion: true
  }, {
    onSuccess: () => {
      showDeleteModal.value = false
    }
  })
}
</script>