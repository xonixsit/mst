<template>
  <div class="space-y-2">
    <label class="block text-sm font-semibold text-gray-700">
      Security Verification <span class="text-red-500 ml-1">*</span>
    </label>
    
    <!-- Captcha Container -->
    <div class="bg-gradient-to-r from-gray-50 to-slate-50 p-4 rounded-xl border border-gray-200 shadow-sm">
      <!-- Captcha Question -->
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-800">Solve this math problem:</p>
            <p class="text-lg font-bold text-gray-900 font-mono">{{ captchaQuestion }}</p>
          </div>
        </div>
        
        <!-- Refresh Button -->
        <button
          type="button"
          @click="generateCaptcha"
          class="p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
          title="Generate new question"
        >
          <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
        </button>
      </div>
      
      <!-- Answer Input -->
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <div class="w-5 h-5 bg-orange-100 rounded-lg flex items-center justify-center">
            <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z"></path>
            </svg>
          </div>
        </div>
        <input
          :id="inputId"
          v-model="userAnswer"
          type="number"
          placeholder="Enter your answer"
          required
          :class="[
            'block w-full pl-12 pr-4 py-3 border rounded-xl shadow-sm transition-all duration-300',
            'placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 sm:text-sm',
            hasError 
              ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 bg-red-50' 
              : isCorrect
                ? 'border-green-300 text-green-900 focus:ring-green-500 focus:border-green-500 bg-green-50'
                : 'border-gray-300 text-gray-900 focus:ring-orange-500 focus:border-orange-500 hover:border-gray-400 bg-white'
          ]"
          @input="validateAnswer"
          @blur="validateAnswer"
        />
        
        <!-- Status Icon -->
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
          <div v-if="isCorrect" class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <div v-else-if="hasError" class="w-5 h-5 bg-red-500 rounded-full flex items-center justify-center">
            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Error Message -->
    <p v-if="hasError" class="text-sm text-red-600 flex items-center font-medium">
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      {{ error }}
    </p>
    
    <!-- Success Message -->
    <p v-else-if="isCorrect" class="text-sm text-green-600 flex items-center font-medium">
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      Correct! Security verification passed.
    </p>
    
    <!-- Help Text -->
    <p v-else class="text-xs text-gray-500 font-medium">
      Please solve the math problem above to verify you're human
    </p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: null
  },
  inputId: {
    type: String,
    default: 'captcha'
  }
})

const emit = defineEmits(['update:modelValue', 'verified'])

// Reactive state
const num1 = ref(0)
const num2 = ref(0)
const operator = ref('+')
const correctAnswer = ref(0)
const userAnswer = ref('')

// Computed properties
const captchaQuestion = computed(() => {
  return `${num1.value} ${operator.value} ${num2.value} = ?`
})

const hasError = computed(() => !!props.error)

const isCorrect = computed(() => {
  return userAnswer.value !== '' && parseInt(userAnswer.value) === correctAnswer.value
})

// Methods
const generateCaptcha = () => {
  // Generate random numbers between 1-20 for easier calculation
  num1.value = Math.floor(Math.random() * 20) + 1
  num2.value = Math.floor(Math.random() * 20) + 1
  
  // Randomly choose operator (mostly addition and subtraction for simplicity)
  const operators = ['+', '-', '+', '+'] // Weighted towards addition
  operator.value = operators[Math.floor(Math.random() * operators.length)]
  
  // Calculate correct answer
  switch (operator.value) {
    case '+':
      correctAnswer.value = num1.value + num2.value
      break
    case '-':
      // Ensure positive result
      if (num1.value < num2.value) {
        [num1.value, num2.value] = [num2.value, num1.value]
      }
      correctAnswer.value = num1.value - num2.value
      break
    case '*':
      // Use smaller numbers for multiplication
      num1.value = Math.floor(Math.random() * 10) + 1
      num2.value = Math.floor(Math.random() * 10) + 1
      correctAnswer.value = num1.value * num2.value
      break
  }
  
  // Reset user answer
  userAnswer.value = ''
}

const validateAnswer = () => {
  if (userAnswer.value === '') return
  
  const isValid = parseInt(userAnswer.value) === correctAnswer.value
  emit('update:modelValue', isValid)
  
  if (isValid) {
    emit('verified', true)
  }
}

// Watch for changes in user answer
watch(userAnswer, validateAnswer)

// Watch for model value changes from parent
watch(() => props.modelValue, (newValue) => {
  if (!newValue) {
    // Reset if parent sets to false
    userAnswer.value = ''
    generateCaptcha()
  }
})

// Initialize captcha on mount
onMounted(() => {
  generateCaptcha()
})
</script>