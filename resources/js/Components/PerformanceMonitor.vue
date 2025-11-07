<template>
  <div v-if="showMonitor" class="fixed bottom-4 right-4 bg-gray-800 text-white p-3 rounded-lg shadow-lg text-xs z-50">
    <div class="flex items-center justify-between mb-2">
      <span class="font-semibold">Performance</span>
      <button @click="toggleMonitor" class="text-gray-400 hover:text-white">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    
    <div class="space-y-1">
      <div class="flex justify-between">
        <span>Load Time:</span>
        <span :class="getPerformanceClass(metrics.loadTime)">{{ metrics.loadTime }}ms</span>
      </div>
      
      <div class="flex justify-between">
        <span>Memory:</span>
        <span :class="getMemoryClass(metrics.memoryUsage)">{{ formatMemory(metrics.memoryUsage) }}</span>
      </div>
      
      <div class="flex justify-between">
        <span>Components:</span>
        <span>{{ metrics.componentCount }}</span>
      </div>
      
      <div class="flex justify-between">
        <span>Cache Hits:</span>
        <span class="text-green-400">{{ metrics.cacheHits }}</span>
      </div>
      
      <div class="flex justify-between">
        <span>API Calls:</span>
        <span>{{ metrics.apiCalls }}</span>
      </div>
      
      <div v-if="metrics.slowOperations.length > 0" class="mt-2 pt-2 border-t border-gray-600">
        <div class="text-yellow-400 mb-1">Slow Operations:</div>
        <div v-for="op in metrics.slowOperations" :key="op.name" class="text-xs">
          {{ op.name }}: {{ op.duration }}ms
        </div>
      </div>
    </div>
  </div>
  
  <!-- Toggle button when monitor is hidden -->
  <button 
    v-else
    @click="toggleMonitor"
    class="fixed bottom-4 right-4 bg-gray-800 text-white p-2 rounded-full shadow-lg z-50 hover:bg-gray-700"
    title="Show Performance Monitor"
  >
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
    </svg>
  </button>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'

const showMonitor = ref(false)
const updateInterval = ref(null)

const metrics = reactive({
  loadTime: 0,
  memoryUsage: 0,
  componentCount: 0,
  cacheHits: 0,
  apiCalls: 0,
  slowOperations: []
})

const toggleMonitor = () => {
  showMonitor.value = !showMonitor.value
  
  if (showMonitor.value) {
    startMonitoring()
  } else {
    stopMonitoring()
  }
}

const startMonitoring = () => {
  updateMetrics()
  updateInterval.value = setInterval(updateMetrics, 2000)
}

const stopMonitoring = () => {
  if (updateInterval.value) {
    clearInterval(updateInterval.value)
    updateInterval.value = null
  }
}

const updateMetrics = () => {
  // Performance timing
  if (performance.timing) {
    metrics.loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart
  }
  
  // Memory usage (if available)
  if (performance.memory) {
    metrics.memoryUsage = performance.memory.usedJSHeapSize
  }
  
  // Component count (approximate)
  metrics.componentCount = document.querySelectorAll('[data-v-]').length
  
  // Cache hits from localStorage
  metrics.cacheHits = parseInt(localStorage.getItem('cache_hits') || '0')
  
  // API calls from sessionStorage
  metrics.apiCalls = parseInt(sessionStorage.getItem('api_calls') || '0')
  
  // Check for slow operations
  checkSlowOperations()
}

const checkSlowOperations = () => {
  const entries = performance.getEntriesByType('measure')
  metrics.slowOperations = entries
    .filter(entry => entry.duration > 100)
    .map(entry => ({
      name: entry.name,
      duration: Math.round(entry.duration)
    }))
    .slice(-5) // Keep only last 5
}

const getPerformanceClass = (loadTime) => {
  if (loadTime < 1000) return 'text-green-400'
  if (loadTime < 3000) return 'text-yellow-400'
  return 'text-red-400'
}

const getMemoryClass = (memory) => {
  const mb = memory / (1024 * 1024)
  if (mb < 50) return 'text-green-400'
  if (mb < 100) return 'text-yellow-400'
  return 'text-red-400'
}

const formatMemory = (bytes) => {
  const mb = bytes / (1024 * 1024)
  return `${mb.toFixed(1)}MB`
}

// Performance measurement utilities
const measureOperation = (name, operation) => {
  performance.mark(`${name}-start`)
  const result = operation()
  performance.mark(`${name}-end`)
  performance.measure(name, `${name}-start`, `${name}-end`)
  return result
}

// Track API calls
const trackApiCall = () => {
  const current = parseInt(sessionStorage.getItem('api_calls') || '0')
  sessionStorage.setItem('api_calls', (current + 1).toString())
}

// Track cache hits
const trackCacheHit = () => {
  const current = parseInt(localStorage.getItem('cache_hits') || '0')
  localStorage.setItem('cache_hits', (current + 1).toString())
}

onMounted(() => {
  // Only show in development
  if (import.meta.env.DEV) {
    // Auto-show after 2 seconds in development
    setTimeout(() => {
      showMonitor.value = true
      startMonitoring()
    }, 2000)
  }
})

onUnmounted(() => {
  stopMonitoring()
})

// Expose utilities for use in other components
defineExpose({
  measureOperation,
  trackApiCall,
  trackCacheHit
})
</script>