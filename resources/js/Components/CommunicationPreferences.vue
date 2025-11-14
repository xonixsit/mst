<template>
  <div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-lg font-medium text-gray-900">Communication Preferences</h2>
      <p class="mt-1 text-sm text-gray-600">Manage how you receive notifications and communications</p>
    </div>
    <div class="px-6 py-4">
      <form @submit.prevent="updatePreferences" class="space-y-6">
        <!-- Notification Methods -->
        <div>
          <h3 class="text-sm font-medium text-gray-900 mb-3">Notification Methods</h3>
          <div class="space-y-3">
            <div class="flex items-center">
              <input
                id="email_notifications"
                v-model="form.email_notifications_enabled"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="email_notifications" class="ml-3 text-sm text-gray-700">
                Email notifications
              </label>
            </div>
            <div class="flex items-center">
              <input
                id="sms_notifications"
                v-model="form.sms_notifications_enabled"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="sms_notifications" class="ml-3 text-sm text-gray-700">
                SMS notifications
              </label>
            </div>
          </div>
        </div>

        <!-- Preferred Communication Method -->
        <div>
          <h3 class="text-sm font-medium text-gray-900 mb-3">Preferred Communication Method</h3>
          <select
            v-model="form.preferred_communication_method"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="email">Email</option>
            <option value="sms">SMS</option>
            <option value="phone">Phone</option>
          </select>
        </div>

        <!-- Notification Types -->
        <div>
          <h3 class="text-sm font-medium text-gray-900 mb-3">Notification Types</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <div>
                <label for="email_notifications_pref" class="text-sm text-gray-700">
                  Information updates
                </label>
                <p class="text-xs text-gray-500">Get notified when your information is updated</p>
              </div>
              <input
                id="email_notifications_pref"
                v-model="form.communication_preferences.email_notifications"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
            </div>
            <div class="flex items-center justify-between">
              <div>
                <label for="document_notifications" class="text-sm text-gray-700">
                  Document notifications
                </label>
                <p class="text-xs text-gray-500">Get notified about document status changes</p>
              </div>
              <input
                id="document_notifications"
                v-model="form.communication_preferences.document_notifications"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
            </div>
            <div class="flex items-center justify-between">
              <div>
                <label for="invoice_notifications" class="text-sm text-gray-700">
                  Invoice notifications
                </label>
                <p class="text-xs text-gray-500">Get notified about new invoices and payments</p>
              </div>
              <input
                id="invoice_notifications"
                v-model="form.communication_preferences.invoice_notifications"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
            </div>
            <div class="flex items-center justify-between">
              <div>
                <label for="reminder_notifications" class="text-sm text-gray-700">
                  Reminder notifications
                </label>
                <p class="text-xs text-gray-500">Get reminders for missing documents and deadlines</p>
              </div>
              <input
                id="reminder_notifications"
                v-model="form.communication_preferences.reminder_notifications"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
            </div>
          </div>
        </div>

        <!-- Notification Frequency -->
        <div>
          <h3 class="text-sm font-medium text-gray-900 mb-3">Notification Frequency</h3>
          <select
            v-model="form.communication_preferences.notification_frequency"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="immediate">Immediate</option>
            <option value="daily">Daily digest</option>
            <option value="weekly">Weekly summary</option>
          </select>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end pt-4 border-t border-gray-200">
          <button
            type="submit"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            :disabled="form.processing"
          >
            {{ form.processing ? 'Saving...' : 'Save Preferences' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  client: {
    type: Object,
    required: true
  }
})

const form = useForm({
  email_notifications_enabled: props.client.email_notifications_enabled ?? true,
  sms_notifications_enabled: props.client.sms_notifications_enabled ?? false,
  preferred_communication_method: props.client.preferred_communication_method ?? 'email',
  communication_preferences: {
    email_notifications: props.client.communication_preferences?.email_notifications ?? true,
    document_notifications: props.client.communication_preferences?.document_notifications ?? true,
    invoice_notifications: props.client.communication_preferences?.invoice_notifications ?? true,
    reminder_notifications: props.client.communication_preferences?.reminder_notifications ?? true,
    notification_frequency: props.client.communication_preferences?.notification_frequency ?? 'immediate'
  }
})

const updatePreferences = () => {
  form.patch(`/client/communication-preferences`, {
    preserveScroll: true,
    onSuccess: () => {
      // Show success message
    }
  })
}
</script>