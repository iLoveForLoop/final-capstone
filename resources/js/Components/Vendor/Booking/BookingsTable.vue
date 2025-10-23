<script setup>
import { router } from '@inertiajs/vue3';
import { Check, CircleCheck, Eye, X, Calendar } from 'lucide-vue-next';
defineProps({
    bookings: Object,
    isLoading: Function
})

const bookingDetailsModal = defineModel('bookingDetailsModal')

const emit = defineEmits(['open-accept-modal', 'open-decline-modal', 'open-complete-modal', 'open-cancel-modal'])



// Format date for display
const formatDate = (dateString) => {
    const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' }
    return new Date(dateString).toLocaleDateString('en-US', options)
}

// Get status color
const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        completed: 'bg-blue-100 text-blue-800',
        cancelled: 'bg-red-100 text-red-800'
    }
    return colors[status] || 'bg-gray-100 text-gray-800'
}


</script>

<template>
    <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Booking ID
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Client & Service
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date & Time
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Price
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="booking in bookings.data" :key="booking.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ booking.id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ booking.client }}</div>
                            <div class="text-sm text-gray-500">{{ booking.service.name }}</div>
                            <div class="text-xs text-gray-400 mt-1">{{ booking.event_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ formatDate(booking.date) }}</div>
                            <div class="text-sm text-gray-500">{{ booking.time }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ booking.price }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                :class="[getStatusColor(booking.status), 'px-2.5 py-0.5 rounded-full text-xs font-medium capitalize']">
                                {{ booking.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <!-- View Details Button -->
                                <!-- {{ console.log('Booking deatails', booking) }} -->
                                <button @click="bookingDetailsModal.open(booking)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 hover:border-indigo-300 transition-colors duration-150"
                                    title="View booking details">
                                    <Eye class="h-4 w-4" />
                                    <span>View</span>
                                </button>

                                <!-- Accept Button -->
                                <button v-if="booking.status === 'pending'" @click="emit('open-accept-modal', booking)"
                                    :disabled="isLoading(booking.raw_id, 'accepting')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-md hover:bg-green-100 hover:border-green-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
                                    title="Accept booking">
                                    <div v-if="isLoading(booking.raw_id, 'accepting')"
                                        class="animate-spin h-4 w-4 border-2 border-green-600 border-t-transparent rounded-full">
                                    </div>
                                    <Check v-else class="h-4 w-4" />
                                    <span>Accept</span>
                                </button>

                                <!-- Decline Button -->
                                <button v-if="booking.status === 'pending'" @click="emit('open-decline-modal', booking)"
                                    :disabled="isLoading(booking.raw_id, 'declining')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 hover:border-red-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
                                    title="Decline booking">
                                    <div v-if="isLoading(booking.raw_id, 'declining')"
                                        class="animate-spin h-4 w-4 border-2 border-red-600 border-t-transparent rounded-full">
                                    </div>
                                    <X v-else class="h-4 w-4" />
                                    <span>Decline</span>
                                </button>



                                <!-- Complete Button -->
                                <button v-if="booking.status === 'confirmed'"
                                    @click="emit('open-complete-modal', booking)"
                                    :disabled="isLoading(booking.raw_id, 'completing')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 hover:border-blue-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
                                    title="Mark booking as completed">
                                    <div v-if="isLoading(booking.raw_id, 'completing')"
                                        class="animate-spin h-4 w-4 border-2 border-blue-600 border-t-transparent rounded-full">
                                    </div>
                                    <CircleCheck v-else class="h-4 w-4" />
                                    <span>Complete</span>
                                </button>

                                <!-- Cancel Button -->
                                <button v-if="booking.status === 'confirmed'"
                                    @click="emit('open-cancel-modal', booking)"
                                    :disabled="isLoading(booking.raw_id, 'canceling')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 hover:border-red-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
                                    title="Decline booking">
                                    <div v-if="isLoading(booking.raw_id, 'canceling')"
                                        class="animate-spin h-4 w-4 border-2 border-red-600 border-t-transparent rounded-full">
                                    </div>
                                    <X v-else class="h-4 w-4" />
                                    <span>Cancel</span>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="bookings.data && bookings.data.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <Calendar class="h-12 w-12 mx-auto mb-4 text-gray-300" />
                                <h3 class="text-sm font-medium text-gray-900 mb-1">No bookings found</h3>
                                <p class="text-sm">No bookings match your current filters.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="bookings.links && bookings.links.length > 3"
            class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button v-if="bookings.prev_page_url" @click="router.get(bookings.prev_page_url)"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </button>
                    <button v-if="bookings.next_page_url" @click="router.get(bookings.next_page_url)"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ bookings.from || 0 }}</span>
                            to
                            <span class="font-medium">{{ bookings.to || 0 }}</span>
                            of
                            <span class="font-medium">{{ bookings.total || 0 }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <template v-for="(link, index) in bookings.links" :key="index">
                                <button v-if="link.url" @click="router.get(link.url)" :class="[
                                    'relative inline-flex items-center px-2 py-2 border text-sm font-medium',
                                    link.active
                                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                    index === 0 ? 'rounded-l-md' : '',
                                    index === bookings.links.length - 1 ? 'rounded-r-md' : ''
                                ]" v-html="link.label">
                                </button>
                                <span v-else :class="[
                                    'relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500',
                                    index === 0 ? 'rounded-l-md' : '',
                                    index === bookings.links.length - 1 ? 'rounded-r-md' : ''
                                ]" v-html="link.label">
                                </span>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style></style>
