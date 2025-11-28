<template>
  <div v-if="error" class="error-display">
    <!-- Critical Error Alert -->
    <div v-if="error.code && error.code.startsWith('ERR_')" 
         class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
        </div>
        <div class="ml-3 flex-1">
          <h3 class="text-sm font-medium text-red-800">
            {{ error.title || 'Error Occurred' }}
          </h3>
          <div class="mt-2 text-sm text-red-700">
            <p>{{ error.message }}</p>
            
            <!-- Validation Errors -->
            <div v-if="error.validation_errors" class="mt-3">
              <p class="font-medium">Please correct the following:</p>
              <ul class="mt-1 list-disc list-inside space-y-1">
                <li v-for="(messages, field) in error.validation_errors" :key="field">
                  <span class="font-medium">{{ formatFieldName(field) }}:</span>
                  {{ Array.isArray(messages) ? messages[0] : messages }}
                </li>
              </ul>
            </div>
            
            <!-- Action Suggestions -->
            <div v-if="error.suggestions && error.suggestions.length > 0" class="mt-3">
              <p class="font-medium">Suggested actions:</p>
              <ul class="mt-1 list-disc list-inside space-y-1">
                <li v-for="suggestion in error.suggestions" :key="suggestion">
                  {{ suggestion }}
                </li>
              </ul>
            </div>
          </div>
          
          <!-- Error Details (Collapsible) -->
          <div v-if="showDetails" class="mt-4">
            <button 
              @click="toggleDetails" 
              class="text-sm text-red-600 hover:text-red-500 font-medium flex items-center"
            >
              <span>{{ detailsExpanded ? 'Hide' : 'Show' }} Technical Details</span>
              <ChevronDownIcon 
                :class="['ml-1 h-4 w-4 transition-transform', detailsExpanded ? 'rotate-180' : '']" 
              />
            </button>
            
            <div v-if="detailsExpanded" class="mt-2 bg-red-100 rounded p-3 text-xs font-mono">
              <div class="space-y-2">
                <div v-if="error.error_id">
                  <span class="font-semibold">Error ID:</span> {{ error.error_id }}
                </div>
                <div v-if="error.code">
                  <span class="font-semibold">Error Code:</span> {{ error.code }}
                </div>
                <div v-if="error.timestamp">
                  <span class="font-semibold">Timestamp:</span> {{ formatTimestamp(error.timestamp) }}
                </div>
                <div v-if="error.details && error.details.debug_message">
                  <span class="font-semibold">Debug:</span> {{ error.details.debug_message }}
                </div>
              </div>
            </div>
          </div>
          
          <!-- Support Contact -->
          <div v-if="error.error_id" class="mt-4 p-3 bg-blue-50 rounded border border-blue-200">
            <p class="text-sm text-blue-800">
              <strong>Need help?</strong> Contact support and provide this error ID: 
              <code class="bg-blue-100 px-1 py-0.5 rounded text-xs font-mono">{{ error.error_id }}</code>
            </p>
            <div class="mt-2 flex space-x-2">
              <button 
                @click="copyErrorId" 
                class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700"
              >
                {{ copied ? 'Copied!' : 'Copy Error ID' }}
              </button>
              <button 
                @click="reportError" 
                class="text-xs bg-gray-600 text-white px-2 py-1 rounded hover:bg-gray-700"
              >
                Report Issue
              </button>
            </div>
          </div>
        </div>
        
        <!-- Close Button -->
        <div class="flex-shrink-0 ml-4">
          <button 
            @click="$emit('close')" 
            class="text-red-400 hover:text-red-600"
          >
            <XMarkIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>
    
    <!-- Simple Error Message (Fallback) -->
    <div v-else class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
      <div class="flex items-center">
        <ExclamationTriangleIcon class="h-5 w-5 text-red-400 mr-3" />
        <p class="text-sm text-red-700">{{ error.message || error }}</p>
        <button 
          @click="$emit('close')" 
          class="ml-auto text-red-400 hover:text-red-600"
        >
          <XMarkIcon class="h-5 w-5" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { 
  ExclamationTriangleIcon, 
  XMarkIcon, 
  ChevronDownIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
  error: {
    type: [Object, String],
    default: null
  },
  showDetails: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close', 'report'])

const detailsExpanded = ref(false)
const copied = ref(false)

const toggleDetails = () => {
  detailsExpanded.value = !detailsExpanded.value
}

const formatFieldName = (fieldName) => {
  return fieldName
    .replace(/_/g, ' ')
    .replace(/([A-Z])/g, ' $1')
    .replace(/^./, str => str.toUpperCase())
    .trim()
}

const formatTimestamp = (timestamp) => {
  try {
    return new Date(timestamp).toLocaleString()
  } catch {
    return timestamp
  }
}

const copyErrorId = async () => {
  if (props.error?.error_id) {
    try {
      await navigator.clipboard.writeText(props.error.error_id)
      copied.value = true
      setTimeout(() => {
        copied.value = false
      }, 2000)
    } catch (err) {
      console.error('Failed to copy error ID:', err)
    }
  }
}

const reportError = () => {
  const errorData = {
    error_id: props.error?.error_id,
    error_code: props.error?.code,
    timestamp: props.error?.timestamp,
    user_agent: navigator.userAgent,
    url: window.location.href
  }
  
  emit('report', errorData)
  
  // You could also open a support form or email client
  const subject = encodeURIComponent(`Error Report - ${props.error?.code || 'Unknown Error'}`)
  const body = encodeURIComponent(`Error ID: ${props.error?.error_id}\nError Code: ${props.error?.code}\nTimestamp: ${props.error?.timestamp}\nURL: ${window.location.href}\n\nPlease describe what you were doing when this error occurred:\n\n`)
  
  window.open(`mailto:support@mysupertax.com?subject=${subject}&body=${body}`)
}
</script>

<style scoped>
.error-display {
  @apply w-full;
}

code {
  @apply bg-gray-100 px-1 py-0.5 rounded text-xs font-mono;
}
</style>