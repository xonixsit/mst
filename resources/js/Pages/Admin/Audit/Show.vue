<template>
  <AppLayout title="Audit Log Details">
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Header -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <div>
                <h2 class="text-2xl font-bold text-gray-900">Audit Log Details</h2>
                <p class="mt-1 text-sm text-gray-600">
                  Detailed information about this audit event
                </p>
              </div>
              <Link
                :href="route('admin.audit.index')"
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <ArrowLeftIcon class="w-4 h-4 mr-2" />
                Back to Audit Logs
              </Link>
            </div>
          </div>

          <!-- Audit Log Information -->
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Basic Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700">Event ID</label>
                  <p class="mt-1 text-sm text-gray-900">{{ auditLog.id }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Date & Time</label>
                  <p class="mt-1 text-sm text-gray-900">{{ formatDateTime(auditLog.created_at) }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Event Type</label>
                  <span :class="getEventBadgeClass(auditLog.event)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1">
                    {{ formatEvent(auditLog.event) }}
                  </span>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Model</label>
                  <p class="mt-1 text-sm text-gray-900">
                    {{ getModelName(auditLog.auditable_type) }} #{{ auditLog.auditable_id }}
                  </p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Description</label>
                  <p class="mt-1 text-sm text-gray-900">{{ auditLog.description }}</p>
                </div>
              </div>

              <!-- User Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">User Information</h3>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700">User</label>
                  <div class="mt-1 flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                      <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                        <UserIcon class="h-4 w-4 text-gray-600" />
                      </div>
                    </div>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-gray-900">
                        {{ auditLog.user ? auditLog.user.name : 'System' }}
                      </p>
                      <p v-if="auditLog.user" class="text-sm text-gray-500">
                        {{ auditLog.user.email }}
                      </p>
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">IP Address</label>
                  <p class="mt-1 text-sm text-gray-900">{{ auditLog.ip_address || 'N/A' }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">User Agent</label>
                  <p class="mt-1 text-sm text-gray-900 break-all">{{ auditLog.user_agent || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <!-- Changes Section -->
            <div v-if="auditLog.old_values || auditLog.new_values" class="mt-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Data Changes</h3>
              
              <div class="bg-gray-50 rounded-lg p-4">
                <div v-if="hasChanges" class="space-y-4">
                  <div v-for="(change, field) in changes" :key="field" class="border-b border-gray-200 pb-3 last:border-b-0">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">{{ formatFieldName(field) }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-xs font-medium text-red-700 mb-1">Old Value</label>
                        <div class="bg-red-50 border border-red-200 rounded p-2">
                          <code class="text-xs text-red-800">{{ formatValue(change.old) }}</code>
                        </div>
                      </div>
                      <div>
                        <label class="block text-xs font-medium text-green-700 mb-1">New Value</label>
                        <div class="bg-green-50 border border-green-200 rounded p-2">
                          <code class="text-xs text-green-800">{{ formatValue(change.new) }}</code>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div v-else-if="auditLog.event === 'created'" class="text-center py-4">
                  <p class="text-sm text-gray-600">New record created with the following data:</p>
                  <div class="mt-3 bg-green-50 border border-green-200 rounded p-3">
                    <pre class="text-xs text-green-800 whitespace-pre-wrap">{{ JSON.stringify(auditLog.new_values, null, 2) }}</pre>
                  </div>
                </div>
                
                <div v-else-if="auditLog.event === 'deleted'" class="text-center py-4">
                  <p class="text-sm text-gray-600">Record deleted with the following data:</p>
                  <div class="mt-3 bg-red-50 border border-red-200 rounded p-3">
                    <pre class="text-xs text-red-800 whitespace-pre-wrap">{{ JSON.stringify(auditLog.old_values, null, 2) }}</pre>
                  </div>
                </div>
                
                <div v-else class="text-center py-4">
                  <p class="text-sm text-gray-500">No data changes recorded for this event.</p>
                </div>
              </div>
            </div>

            <!-- Metadata Section -->
            <div v-if="auditLog.metadata" class="mt-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <pre class="text-xs text-gray-800 whitespace-pre-wrap">{{ JSON.stringify(auditLog.metadata, null, 2) }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
  ArrowLeftIcon,
  UserIcon,
} from '@heroicons/vue/24/outline'

// Use the global route function
const route = window.route

const props = defineProps({
  auditLog: Object,
})

const changes = computed(() => {
  if (!props.auditLog.old_values || !props.auditLog.new_values) {
    return {}
  }

  const changesList = {}
  
  Object.keys(props.auditLog.new_values).forEach(key => {
    const oldValue = props.auditLog.old_values[key]
    const newValue = props.auditLog.new_values[key]
    
    if (oldValue !== newValue) {
      changesList[key] = {
        old: oldValue,
        new: newValue,
      }
    }
  })
  
  return changesList
})

const hasChanges = computed(() => {
  return Object.keys(changes.value).length > 0
})

const formatDateTime = (dateTime) => {
  return new Date(dateTime).toLocaleString()
}

const formatEvent = (event) => {
  return event.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const getModelName = (modelType) => {
  return modelType.split('\\').pop()
}

const formatFieldName = (field) => {
  return field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const formatValue = (value) => {
  if (value === null) return 'null'
  if (value === undefined) return 'undefined'
  if (typeof value === 'object') return JSON.stringify(value, null, 2)
  return String(value)
}

const getEventBadgeClass = (event) => {
  const classes = {
    created: 'bg-green-100 text-green-800',
    updated: 'bg-blue-100 text-blue-800',
    deleted: 'bg-red-100 text-red-800',
    status_changed: 'bg-yellow-100 text-yellow-800',
    assigned: 'bg-purple-100 text-purple-800',
    bulk_email: 'bg-indigo-100 text-indigo-800',
    bulk_status_update: 'bg-orange-100 text-orange-800',
  }
  return classes[event] || 'bg-gray-100 text-gray-800'
}
</script>