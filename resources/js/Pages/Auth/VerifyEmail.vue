<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Verify Your Email Address
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Before continuing, please check your email for a verification link.
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <div v-if="status === 'verification-link-sent'" class="mb-4 font-medium text-sm text-green-600">
                    A new verification link has been sent to your email address.
                </div>

                <p class="text-sm text-gray-600 mb-4">
                    If you didn't receive the email, we will gladly send you another.
                </p>

                <form @submit.prevent="resendVerification">
                    <div class="flex items-center justify-between">
                        <button
                            type="submit"
                            :disabled="processing"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        >
                            <span v-if="processing">Sending...</span>
                            <span v-else>Resend Verification Email</span>
                        </button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <Link
                        href="/admin/logout"
                        method="post"
                        as="button"
                        class="text-sm text-gray-600 hover:text-gray-900 underline"
                    >
                        Log Out
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link, router } from '@inertiajs/vue3'

defineProps({
    status: String,
})

const processing = ref(false)

const resendVerification = () => {
    processing.value = true
    
    useForm({}).post('/admin/email/verification-notification', {
        onFinish: () => {
            processing.value = false
        }
    })
}


</script>