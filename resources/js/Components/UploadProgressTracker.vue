<template>
  <div v-if="uploads.length > 0" class="fixed bottom-6 right-6 w-96 max-h-96 bg-white shadow-2xl rounded-2xl border border-gray-200 overflow-hidden z-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex items-center justify-between">
      <div class="flex items-center space-x-3">
        <CloudArrowUpIcon class="w-5 h-5 text-white" />
        <h3 class="text-white font-bold">Upload Progress</h3>
      </div>
      <button
        @click="minimized = !minimized"
        class="text-white hover:bg-white/20 p-1 rounded-lg transition-colors"
      >
        <ChevronDownIcon v-if="!minimized" class="w-5 h-5" />
        <ChevronUpIcon v-else class="w-5 h-5" />
      </button>
    </div>

    <!-- Content -->
    <div v-if="!minimized" class="overflow-y-auto max-h-80 divide-y divide-gray-100">
      <div v-for="upload in uploads" :key="upload.fileId" class="p-4 hover:bg-gray-50 transition-colors">
        <!-- File name and status -->
        <div class="flex items-start justify-between mb-2">
          <div class="flex-1">
            <p class="text-sm font-semibold text-gray-900 truncate">{{ upload.fileName }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ formatFileSize(upload.fileSize) }}</p>
          </div>
          <div class="flex items-center space-x-2">
            <span :class="getStatusColor(upload.status)" class="text-xs font-bold px-2 py-1 rounded-full">
              {{ formatStatus(upload.status) }}
            </span>
            <button
              v-if="upload.status === 'paused'"
              @click="resumeUpload(upload)"
              class="text-blue-600 hover:text-blue-700 p-1"
              title="Resume upload"
            >
              <PlayIcon class="w-4 h-4" />
            </button>
            <button
              v-if="upload.status !== 'completed'"
              @click="cancelUpload(upload)"
              class="text-red-600 hover:text-red-700 p-1"
              title="Cancel upload"
            >
              <XMarkIcon class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Progress bar with percentage overlay -->
        <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden relative">
          <div
            :style="{ width: upload.progress + '%' }"
            :class="getProgressColor(upload.status)"
            class="h-full transition-all duration-300 flex items-center justify-center"
          >
            <span class="text-xs font-bold text-white drop-shadow-md">
              {{ upload.progress.toFixed(1) }}%
            </span>
          </div>
          <!-- Fallback text if progress is too small -->
          <div v-if="upload.progress < 15" class="absolute inset-0 flex items-center justify-center">
            <span class="text-xs font-bold text-gray-700">{{ upload.progress.toFixed(1) }}%</span>
          </div>
        </div>

        <!-- Progress details -->
        <div class="flex items-center justify-between mt-2">
          <span class="text-xs text-gray-600 font-medium">
            {{ upload.uploadedChunks }}/{{ upload.totalChunks }} chunks
          </span>
          <span class="text-xs text-gray-600 font-medium">
            {{ formatFileSize(upload.fileSize * (upload.progress / 100)) }} / {{ formatFileSize(upload.fileSize) }}
          </span>
        </div>

        <!-- Speed and ETA -->
        <div v-if="upload.status === 'uploading'" class="flex items-center justify-between mt-2 text-xs text-gray-500">
          <span>Speed: {{ formatSpeed(upload.speed) }}</span>
          <span>ETA: {{ formatETA(upload.eta) }}</span>
        </div>
      </div>
    </div>

    <!-- Summary when minimized -->
    <div v-if="minimized" class="px-6 py-3 bg-gray-50 text-sm text-gray-600">
      {{ uploads.length }} upload{{ uploads.length !== 1 ? 's' : '' }} in progress
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
  CloudArrowUpIcon,
  ChevronDownIcon,
  ChevronUpIcon,
  PlayIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  uploads: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['resume', 'cancel'])

const minimized = ref(false)
let speedInterval = null

onMounted(() => {
  // Update speed calculations every second
  speedInterval = setInterval(() => {
    props.uploads.forEach(upload => {
      if (upload.status === 'uploading' && upload.lastProgress !== undefined) {
        const timeDiff = (Date.now() - upload.lastTime) / 1000
        const progressDiff = upload.progress - upload.lastProgress
        upload.speed = (progressDiff / timeDiff) * upload.fileSize / 1024 / 1024 // MB/s
        
        if (upload.speed > 0) {
          const remainingProgress = 100 - upload.progress
          upload.eta = (remainingProgress / 100) * upload.fileSize / (upload.speed * 1024 * 1024)
        }
        
        upload.lastProgress = upload.progress
        upload.lastTime = Date.now()
      }
    })
  }, 1000)
})

onUnmounted(() => {
  if (speedInterval) clearInterval(speedInterval)
})

const formatFileSize = (bytes) => {
  if (!bytes) return 'Unknown'
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

const formatSpeed = (mbps) => {
  if (!mbps) return '0 MB/s'
  return mbps.toFixed(2) + ' MB/s'
}

const formatETA = (seconds) => {
  if (!seconds || seconds === Infinity) return '--'
  if (seconds < 60) return Math.round(seconds) + 's'
  if (seconds < 3600) return Math.round(seconds / 60) + 'm'
  return Math.round(seconds / 3600) + 'h'
}

const formatStatus = (status) => {
  const statusMap = {
    'initializing': 'Initializing',
    'uploading': 'Uploading',
    'paused': 'Paused',
    'resuming': 'Resuming',
    'finalizing': 'Finalizing',
    'completed': 'Completed'
  }
  return statusMap[status] || status
}

const getStatusColor = (status) => {
  const colors = {
    'initializing': 'bg-blue-100 text-blue-800',
    'uploading': 'bg-purple-100 text-purple-800',
    'paused': 'bg-amber-100 text-amber-800',
    'resuming': 'bg-blue-100 text-blue-800',
    'finalizing': 'bg-indigo-100 text-indigo-800',
    'completed': 'bg-green-100 text-green-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getProgressColor = (status) => {
  const colors = {
    'initializing': 'bg-blue-500',
    'uploading': 'bg-gradient-to-r from-purple-500 to-indigo-500',
    'paused': 'bg-amber-500',
    'resuming': 'bg-blue-500',
    'finalizing': 'bg-indigo-500',
    'completed': 'bg-green-500'
  }
  return colors[status] || 'bg-gray-500'
}

const resumeUpload = (upload) => {
  emit('resume', upload)
}

const cancelUpload = (upload) => {
  emit('cancel', upload)
}
</script>
