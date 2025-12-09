<template>
  <AppLayout title="Compliance Report">
    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-4">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Header -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <div>
                <h2 class="text-2xl font-bold text-gray-900">Compliance Report</h2>
                <p class="mt-1 text-sm text-gray-600">
                  Audit trail compliance report for {{ formatDate(statistics.date_range.start) }} to {{ formatDate(statistics.date_range.end) }}
                </p>
              </div>
              <div class="flex space-x-3">
                <button
                  @click="printReport"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <PrinterIcon class="w-4 h-4 mr-2" />
                  Print Report
                </button>
                <Link
                  :href="route('admin.audit.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <ArrowLeftIcon class="w-4 h-4 mr-2" />
                  Back to Audit Logs
                </Link>
              </div>
            </div>
          </div>

          <!-- Report Content -->
          <div class="p-6 space-y-8">
            <!-- Summary Statistics -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Summary Statistics</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <DocumentTextIcon class="h-8 w-8 text-blue-600" />
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-medium text-blue-600">Total Events</p>
                      <p class="text-2xl font-bold text-blue-900">{{ statistics.total_events.toLocaleString() }}</p>
                    </div>
                  </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <CalendarDaysIcon class="h-8 w-8 text-green-600" />
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-medium text-green-600">Date Range</p>
                      <p class="text-sm font-bold text-green-900">
                        {{ getDaysDifference() }} days
                      </p>
                    </div>
                  </div>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <ChartBarIcon class="h-8 w-8 text-purple-600" />
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-medium text-purple-600">Avg Events/Day</p>
                      <p class="text-2xl font-bold text-purple-900">
                        {{ getAverageEventsPerDay() }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Event Breakdown -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Event Type Breakdown</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div
                    v-for="event in statistics.event_breakdown"
                    :key="event.event"
                    class="bg-white rounded-lg p-4 border border-gray-200"
                  >
                    <div class="flex justify-between items-center">
                      <div>
                        <p class="text-sm font-medium text-gray-900">{{ formatEvent(event.event) }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ event.count.toLocaleString() }}</p>
                      </div>
                      <div class="text-right">
                        <p class="text-sm text-gray-500">
                          {{ getPercentage(event.count, statistics.total_events) }}%
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- User Activity -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Top User Activity</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Events
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Percentage
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="user in statistics.user_activity.slice(0, 10)" :key="user.user">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                          {{ user.user }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {{ user.count.toLocaleString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          {{ getPercentage(user.count, statistics.total_events) }}%
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Model Activity -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Model Activity Breakdown</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div
                    v-for="model in statistics.model_activity"
                    :key="model.model"
                    class="bg-white rounded-lg p-4 border border-gray-200"
                  >
                    <div class="flex justify-between items-center">
                      <div>
                        <p class="text-sm font-medium text-gray-900">{{ model.model }}</p>
                        <p class="text-xl font-bold text-gray-900">{{ model.count.toLocaleString() }}</p>
                      </div>
                      <div class="text-right">
                        <p class="text-sm text-gray-500">
                          {{ getPercentage(model.count, statistics.total_events) }}%
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Daily Activity Chart -->
            <div v-if="statistics.daily_activity.length > 0">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Daily Activity Trend</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Events
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Activity Level
                        </th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr v-for="day in statistics.daily_activity" :key="day.date">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                          {{ formatDate(day.date) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {{ day.count.toLocaleString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                          <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                              <div
                                class="bg-blue-600 h-2 rounded-full"
                                :style="{ width: getActivityBarWidth(day.count) + '%' }"
                              ></div>
                            </div>
                            <span class="text-sm text-gray-500">
                              {{ getActivityLevel(day.count) }}
                            </span>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- Compliance Notes -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
              <div class="flex">
                <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400" />
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-yellow-800">
                    Compliance Information
                  </h3>
                  <div class="mt-2 text-sm text-yellow-700">
                    <p>This report demonstrates compliance with audit trail requirements:</p>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                      <li>All user actions are logged with timestamps and user identification</li>
                      <li>Field-level changes are tracked for data integrity</li>
                      <li>System maintains complete audit trail for {{ getDaysDifference() }} days</li>
                      <li>IP addresses and user agents are recorded for security purposes</li>
                      <li>Bulk operations are properly logged and tracked</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Report Footer -->
          <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="text-center text-sm text-gray-500">
              <p>Report generated on {{ new Date().toLocaleString() }}</p>
              <p class="mt-1">MySuperTax Platform - Audit Compliance Report</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
  ArrowLeftIcon,
  PrinterIcon,
  DocumentTextIcon,
  CalendarDaysIcon,
  ChartBarIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  filters: Object,
  statistics: Object,
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const formatEvent = (event) => {
  return event.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

const getPercentage = (value, total) => {
  if (total === 0) return 0
  return Math.round((value / total) * 100)
}

const getDaysDifference = () => {
  const start = new Date(props.statistics.date_range.start)
  const end = new Date(props.statistics.date_range.end)
  const diffTime = Math.abs(end - start)
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
}

const getAverageEventsPerDay = () => {
  const days = getDaysDifference()
  if (days === 0) return 0
  return Math.round(props.statistics.total_events / days)
}

const getMaxDailyEvents = () => {
  if (!props.statistics.daily_activity.length) return 1
  return Math.max(...props.statistics.daily_activity.map(day => day.count))
}

const getActivityBarWidth = (count) => {
  const max = getMaxDailyEvents()
  return max > 0 ? (count / max) * 100 : 0
}

const getActivityLevel = (count) => {
  const avg = getAverageEventsPerDay()
  if (count > avg * 1.5) return 'High'
  if (count > avg * 0.5) return 'Normal'
  return 'Low'
}

const printReport = () => {
  window.print()
}
</script>

<style>
@media print {
  .no-print {
    display: none !important;
  }
}
</style>