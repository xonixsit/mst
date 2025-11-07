class CacheService {
  constructor() {
    this.cache = new Map()
    this.expirationTimes = new Map()
    this.defaultTTL = 5 * 60 * 1000 // 5 minutes
    this.maxSize = 100 // Maximum number of cached items
    
    // Clean up expired items periodically
    setInterval(() => this.cleanup(), 60000) // Every minute
  }

  /**
   * Set a value in cache with optional TTL
   */
  set(key, value, ttl = this.defaultTTL) {
    // Remove oldest items if cache is full
    if (this.cache.size >= this.maxSize) {
      this.evictOldest()
    }

    this.cache.set(key, {
      value,
      timestamp: Date.now(),
      hits: 0
    })
    
    this.expirationTimes.set(key, Date.now() + ttl)
    
    // Track cache operations
    this.trackCacheOperation('set', key)
  }

  /**
   * Get a value from cache
   */
  get(key) {
    if (!this.cache.has(key)) {
      this.trackCacheOperation('miss', key)
      return null
    }

    // Check if expired
    if (this.isExpired(key)) {
      this.delete(key)
      this.trackCacheOperation('expired', key)
      return null
    }

    const item = this.cache.get(key)
    item.hits++
    item.lastAccessed = Date.now()
    
    this.trackCacheOperation('hit', key)
    return item.value
  }

  /**
   * Check if a key exists and is not expired
   */
  has(key) {
    return this.cache.has(key) && !this.isExpired(key)
  }

  /**
   * Delete a key from cache
   */
  delete(key) {
    this.cache.delete(key)
    this.expirationTimes.delete(key)
    this.trackCacheOperation('delete', key)
  }

  /**
   * Clear all cache
   */
  clear() {
    this.cache.clear()
    this.expirationTimes.clear()
    this.trackCacheOperation('clear')
  }

  /**
   * Get or set pattern - get from cache or execute function and cache result
   */
  async getOrSet(key, asyncFunction, ttl = this.defaultTTL) {
    const cached = this.get(key)
    if (cached !== null) {
      return cached
    }

    try {
      const result = await asyncFunction()
      this.set(key, result, ttl)
      return result
    } catch (error) {
      console.error(`Cache getOrSet failed for key ${key}:`, error)
      throw error
    }
  }

  /**
   * Memoize a function with caching
   */
  memoize(fn, keyGenerator = (...args) => JSON.stringify(args), ttl = this.defaultTTL) {
    return async (...args) => {
      const key = `memoized_${fn.name}_${keyGenerator(...args)}`
      return this.getOrSet(key, () => fn(...args), ttl)
    }
  }

  /**
   * Cache API responses
   */
  async cacheApiCall(url, options = {}, ttl = this.defaultTTL) {
    const cacheKey = `api_${url}_${JSON.stringify(options)}`
    
    return this.getOrSet(cacheKey, async () => {
      const response = await fetch(url, options)
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      return response.json()
    }, ttl)
  }

  /**
   * Cache form data to prevent loss
   */
  cacheFormData(formId, data) {
    const key = `form_${formId}`
    this.set(key, data, 30 * 60 * 1000) // 30 minutes for form data
  }

  /**
   * Get cached form data
   */
  getCachedFormData(formId) {
    return this.get(`form_${formId}`)
  }

  /**
   * Cache user preferences
   */
  cacheUserPreferences(userId, preferences) {
    const key = `user_prefs_${userId}`
    this.set(key, preferences, 24 * 60 * 60 * 1000) // 24 hours
  }

  /**
   * Get cached user preferences
   */
  getCachedUserPreferences(userId) {
    return this.get(`user_prefs_${userId}`)
  }

  /**
   * Check if key is expired
   */
  isExpired(key) {
    const expirationTime = this.expirationTimes.get(key)
    return expirationTime && Date.now() > expirationTime
  }

  /**
   * Clean up expired items
   */
  cleanup() {
    const now = Date.now()
    const expiredKeys = []

    for (const [key, expirationTime] of this.expirationTimes.entries()) {
      if (now > expirationTime) {
        expiredKeys.push(key)
      }
    }

    expiredKeys.forEach(key => this.delete(key))
    
    if (expiredKeys.length > 0) {
      console.log(`Cache cleanup: removed ${expiredKeys.length} expired items`)
    }
  }

  /**
   * Evict oldest items when cache is full
   */
  evictOldest() {
    let oldestKey = null
    let oldestTime = Date.now()

    for (const [key, item] of this.cache.entries()) {
      if (item.timestamp < oldestTime) {
        oldestTime = item.timestamp
        oldestKey = key
      }
    }

    if (oldestKey) {
      this.delete(oldestKey)
    }
  }

  /**
   * Get cache statistics
   */
  getStats() {
    const stats = {
      size: this.cache.size,
      maxSize: this.maxSize,
      hitRate: 0,
      totalHits: 0,
      totalMisses: 0
    }

    // Calculate hit rate from localStorage tracking
    const hits = parseInt(localStorage.getItem('cache_hits') || '0')
    const misses = parseInt(localStorage.getItem('cache_misses') || '0')
    const total = hits + misses

    if (total > 0) {
      stats.hitRate = (hits / total * 100).toFixed(2)
      stats.totalHits = hits
      stats.totalMisses = misses
    }

    return stats
  }

  /**
   * Track cache operations for analytics
   */
  trackCacheOperation(operation, key = null) {
    const timestamp = new Date().toISOString()
    
    switch (operation) {
      case 'hit':
        const hits = parseInt(localStorage.getItem('cache_hits') || '0')
        localStorage.setItem('cache_hits', (hits + 1).toString())
        break
      case 'miss':
        const misses = parseInt(localStorage.getItem('cache_misses') || '0')
        localStorage.setItem('cache_misses', (misses + 1).toString())
        break
    }

    // Log to console in development
    if (import.meta.env.DEV) {
      console.log(`Cache ${operation}:`, key || 'all', timestamp)
    }
  }

  /**
   * Preload data for better performance
   */
  async preloadData(preloadConfig) {
    const promises = preloadConfig.map(async ({ key, loader, ttl }) => {
      try {
        if (!this.has(key)) {
          const data = await loader()
          this.set(key, data, ttl)
        }
      } catch (error) {
        console.error(`Preload failed for ${key}:`, error)
      }
    })

    await Promise.allSettled(promises)
  }

  /**
   * Invalidate cache by pattern
   */
  invalidatePattern(pattern) {
    const regex = new RegExp(pattern)
    const keysToDelete = []

    for (const key of this.cache.keys()) {
      if (regex.test(key)) {
        keysToDelete.push(key)
      }
    }

    keysToDelete.forEach(key => this.delete(key))
    return keysToDelete.length
  }

  /**
   * Persist important cache data to localStorage
   */
  persistToStorage() {
    const importantData = {}
    
    for (const [key, item] of this.cache.entries()) {
      if (key.startsWith('user_prefs_') || key.startsWith('form_')) {
        importantData[key] = {
          value: item.value,
          expiration: this.expirationTimes.get(key)
        }
      }
    }

    localStorage.setItem('cache_persistent', JSON.stringify(importantData))
  }

  /**
   * Restore cache data from localStorage
   */
  restoreFromStorage() {
    try {
      const persistentData = localStorage.getItem('cache_persistent')
      if (persistentData) {
        const data = JSON.parse(persistentData)
        const now = Date.now()

        for (const [key, item] of Object.entries(data)) {
          if (item.expiration > now) {
            const ttl = item.expiration - now
            this.set(key, item.value, ttl)
          }
        }
      }
    } catch (error) {
      console.error('Failed to restore cache from storage:', error)
    }
  }
}

// Create singleton instance
const cacheService = new CacheService()

// Restore cache on initialization
cacheService.restoreFromStorage()

// Persist cache before page unload
window.addEventListener('beforeunload', () => {
  cacheService.persistToStorage()
})

export default cacheService