import cacheService from '../Services/CacheService.js'

/**
 * Debounce function to limit function calls
 */
export function debounce(func, wait, immediate = false) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      timeout = null
      if (!immediate) func(...args)
    }
    const callNow = immediate && !timeout
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
    if (callNow) func(...args)
  }
}

/**
 * Throttle function to limit function calls
 */
export function throttle(func, limit) {
  let inThrottle
  return function(...args) {
    if (!inThrottle) {
      func.apply(this, args)
      inThrottle = true
      setTimeout(() => inThrottle = false, limit)
    }
  }
}

/**
 * Lazy load images with intersection observer
 */
export function lazyLoadImages(selector = 'img[data-src]') {
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target
          img.src = img.dataset.src
          img.classList.remove('lazy')
          imageObserver.unobserve(img)
        }
      })
    })

    document.querySelectorAll(selector).forEach(img => {
      imageObserver.observe(img)
    })
  }
}

/**
 * Preload critical resources
 */
export function preloadResources(resources) {
  resources.forEach(resource => {
    const link = document.createElement('link')
    link.rel = 'preload'
    link.href = resource.href
    link.as = resource.as || 'fetch'
    if (resource.crossorigin) link.crossOrigin = resource.crossorigin
    document.head.appendChild(link)
  })
}

/**
 * Measure and log performance metrics
 */
export function measurePerformance(name, fn) {
  return async (...args) => {
    const startTime = performance.now()
    performance.mark(`${name}-start`)
    
    try {
      const result = await fn(...args)
      const endTime = performance.now()
      const duration = endTime - startTime
      
      performance.mark(`${name}-end`)
      performance.measure(name, `${name}-start`, `${name}-end`)
      
      // Log slow operations
      if (duration > 100) {
        console.warn(`Slow operation detected: ${name} took ${duration.toFixed(2)}ms`)
      }
      
      return result
    } catch (error) {
      console.error(`Performance measurement failed for ${name}:`, error)
      throw error
    }
  }
}

/**
 * Optimize large lists with virtual scrolling helper
 */
export function createVirtualScrollConfig(itemHeight, containerHeight, buffer = 5) {
  return {
    itemHeight,
    containerHeight,
    buffer,
    getVisibleRange(scrollTop, totalItems) {
      const visibleCount = Math.ceil(containerHeight / itemHeight)
      const startIndex = Math.max(0, Math.floor(scrollTop / itemHeight) - buffer)
      const endIndex = Math.min(totalItems - 1, startIndex + visibleCount + buffer * 2)
      
      return { startIndex, endIndex, visibleCount }
    }
  }
}

/**
 * Batch DOM updates to prevent layout thrashing
 */
export function batchDOMUpdates(updates) {
  return new Promise(resolve => {
    requestAnimationFrame(() => {
      updates.forEach(update => update())
      resolve()
    })
  })
}

/**
 * Optimize form handling with auto-save
 */
export function createAutoSave(formId, saveFunction, delay = 2000) {
  let saveTimeout
  let isDirty = false
  
  const debouncedSave = debounce(async () => {
    if (isDirty) {
      try {
        await saveFunction()
        isDirty = false
        console.log(`Auto-saved form: ${formId}`)
      } catch (error) {
        console.error(`Auto-save failed for form ${formId}:`, error)
      }
    }
  }, delay)

  return {
    markDirty() {
      isDirty = true
      debouncedSave()
    },
    
    forceSave() {
      clearTimeout(saveTimeout)
      return saveFunction()
    },
    
    isDirty() {
      return isDirty
    }
  }
}

/**
 * Optimize API calls with request deduplication
 */
export function createRequestDeduplicator() {
  const pendingRequests = new Map()
  
  return async function deduplicatedRequest(key, requestFn) {
    if (pendingRequests.has(key)) {
      return pendingRequests.get(key)
    }
    
    const promise = requestFn()
    pendingRequests.set(key, promise)
    
    try {
      const result = await promise
      return result
    } finally {
      pendingRequests.delete(key)
    }
  }
}

/**
 * Memory usage monitoring
 */
export function monitorMemoryUsage() {
  if (performance.memory) {
    const memory = performance.memory
    const usage = {
      used: Math.round(memory.usedJSHeapSize / 1024 / 1024),
      total: Math.round(memory.totalJSHeapSize / 1024 / 1024),
      limit: Math.round(memory.jsHeapSizeLimit / 1024 / 1024)
    }
    
    // Warn if memory usage is high
    if (usage.used > usage.limit * 0.8) {
      console.warn('High memory usage detected:', usage)
    }
    
    return usage
  }
  
  return null
}

/**
 * Component performance tracker
 */
export function createComponentTracker(componentName) {
  const startTime = performance.now()
  let renderCount = 0
  
  return {
    trackRender() {
      renderCount++
      const currentTime = performance.now()
      const totalTime = currentTime - startTime
      
      if (renderCount > 10 && totalTime < 1000) {
        console.warn(`Component ${componentName} rendered ${renderCount} times in ${totalTime.toFixed(2)}ms`)
      }
    },
    
    getStats() {
      return {
        componentName,
        renderCount,
        totalTime: performance.now() - startTime
      }
    }
  }
}

/**
 * Optimize event listeners with passive listeners
 */
export function addOptimizedEventListener(element, event, handler, options = {}) {
  const optimizedOptions = {
    passive: true,
    ...options
  }
  
  // Use passive listeners for scroll and touch events
  if (['scroll', 'touchstart', 'touchmove', 'wheel'].includes(event)) {
    optimizedOptions.passive = true
  }
  
  element.addEventListener(event, handler, optimizedOptions)
  
  return () => element.removeEventListener(event, handler, optimizedOptions)
}

/**
 * Bundle size analyzer helper
 */
export function analyzeBundleSize() {
  if (import.meta.env.DEV) {
    const scripts = Array.from(document.querySelectorAll('script[src]'))
    const styles = Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
    
    console.group('Bundle Analysis')
    console.log('Scripts:', scripts.length)
    console.log('Stylesheets:', styles.length)
    
    // Estimate total size (rough calculation)
    let totalEstimatedSize = 0
    scripts.forEach(script => {
      // This is a rough estimate - in production you'd use actual file sizes
      totalEstimatedSize += script.src.length * 10 // Very rough estimate
    })
    
    console.log('Estimated bundle size:', `${(totalEstimatedSize / 1024).toFixed(2)}KB`)
    console.groupEnd()
  }
}

/**
 * Performance budget checker
 */
export function checkPerformanceBudget(budgets = {}) {
  const defaultBudgets = {
    loadTime: 3000, // 3 seconds
    memoryUsage: 50 * 1024 * 1024, // 50MB
    bundleSize: 500 * 1024, // 500KB
    ...budgets
  }
  
  const results = {
    passed: true,
    violations: []
  }
  
  // Check load time
  if (performance.timing) {
    const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart
    if (loadTime > defaultBudgets.loadTime) {
      results.passed = false
      results.violations.push({
        metric: 'loadTime',
        actual: loadTime,
        budget: defaultBudgets.loadTime
      })
    }
  }
  
  // Check memory usage
  if (performance.memory) {
    const memoryUsage = performance.memory.usedJSHeapSize
    if (memoryUsage > defaultBudgets.memoryUsage) {
      results.passed = false
      results.violations.push({
        metric: 'memoryUsage',
        actual: memoryUsage,
        budget: defaultBudgets.memoryUsage
      })
    }
  }
  
  return results
}

/**
 * Resource hints helper
 */
export function addResourceHints(hints) {
  hints.forEach(hint => {
    const link = document.createElement('link')
    link.rel = hint.rel // preload, prefetch, preconnect, dns-prefetch
    link.href = hint.href
    if (hint.as) link.as = hint.as
    if (hint.crossorigin) link.crossOrigin = hint.crossorigin
    document.head.appendChild(link)
  })
}

/**
 * Critical CSS inliner helper
 */
export function inlineCriticalCSS(css) {
  const style = document.createElement('style')
  style.textContent = css
  document.head.insertBefore(style, document.head.firstChild)
}

export default {
  debounce,
  throttle,
  lazyLoadImages,
  preloadResources,
  measurePerformance,
  createVirtualScrollConfig,
  batchDOMUpdates,
  createAutoSave,
  createRequestDeduplicator,
  monitorMemoryUsage,
  createComponentTracker,
  addOptimizedEventListener,
  analyzeBundleSize,
  checkPerformanceBudget,
  addResourceHints,
  inlineCriticalCSS
}