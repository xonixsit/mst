import { router } from '@inertiajs/vue3'

// Force all non-GET requests to use POST with method spoofing for IIS compatibility
const originalVisit = router.visit

router.visit = function(url, options = {}) {
    if (options.method && ['put', 'patch', 'delete'].includes(options.method.toLowerCase())) {
        options.data = options.data || {}
        options.data._method = options.method.toUpperCase()
        options.method = 'post'
    }
    return originalVisit.call(this, url, options)
}

export default router