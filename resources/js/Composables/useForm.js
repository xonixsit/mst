import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export function useForm(initialData = {}) {
  const data = reactive({ ...initialData })
  const errors = ref({})
  const processing = ref(false)
  const recentlySuccessful = ref(false)
  
  const hasErrors = computed(() => Object.keys(errors.value).length > 0)
  
  const clearErrors = (field = null) => {
    if (field) {
      delete errors.value[field]
    } else {
      errors.value = {}
    }
  }
  
  const setError = (field, message) => {
    errors.value[field] = message
  }
  
  const reset = (...fields) => {
    if (fields.length === 0) {
      Object.keys(data).forEach(key => {
        data[key] = initialData[key] || ''
      })
    } else {
      fields.forEach(field => {
        data[field] = initialData[field] || ''
      })
    }
    clearErrors()
  }
  
  const submit = (method, url, options = {}) => {
    processing.value = true
    clearErrors()
    
    const submitOptions = {
      ...options,
      onSuccess: (response) => {
        recentlySuccessful.value = true
        setTimeout(() => {
          recentlySuccessful.value = false
        }, 2000)
        
        if (options.onSuccess) {
          options.onSuccess(response)
        }
      },
      onError: (responseErrors) => {
        errors.value = responseErrors
        
        if (options.onError) {
          options.onError(responseErrors)
        }
      },
      onFinish: () => {
        processing.value = false
        
        if (options.onFinish) {
          options.onFinish()
        }
      }
    }
    
    router[method.toLowerCase()](url, data, submitOptions)
  }
  
  const get = (url, options = {}) => submit('get', url, options)
  const post = (url, options = {}) => submit('post', url, options)
  const put = (url, options = {}) => submit('put', url, options)
  const patch = (url, options = {}) => submit('patch', url, options)
  const delete_ = (url, options = {}) => submit('delete', url, options)
  
  return {
    data,
    errors,
    processing,
    recentlySuccessful,
    hasErrors,
    clearErrors,
    setError,
    reset,
    get,
    post,
    put,
    patch,
    delete: delete_
  }
}