<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import ClientNavbar from '@/Components/ClientNavbar.vue'

// Props from Laravel controller
const props = defineProps({
    bookings: Object,
    filters: Object
})

// Reactive data
const searchTerm = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || 'all')
const dateRangeFilter = ref(props.filters.date_range || 'all')
const sortBy = ref(props.filters.sort || 'date_desc')
const expandedBooking = ref(null)

// Methods
const getStatusColor = (status) => {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'confirmed': 'bg-blue-100 text-blue-800 border-blue-200',
        'completed': 'bg-green-100 text-green-800 border-green-200',
        'cancelled': 'bg-red-100 text-red-800 border-red-200'
    }
    return colors[status] || 'bg-gray-100 text-gray-800 border-gray-200'
}

const getStatusIcon = (status) => {
    switch (status) {
        case 'confirmed': return '✓'
        case 'completed': return '★'
        case 'pending': return '⏳'
        case 'cancelled': return '✗'
        default: return '•'
    }
}

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}

const applyFilters = () => {
    router.get(route('client.bookings.index'), {
        search: searchTerm.value,
        status: statusFilter.value,
        date_range: dateRangeFilter.value,
        sort: sortBy.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const resetFilters = () => {
    searchTerm.value = ''
    statusFilter.value = 'all'
    dateRangeFilter.value = 'all'
    sortBy.value = 'date_desc'
    applyFilters()
}

const toggleBookingDetails = (bookingId) => {
    expandedBooking.value = expandedBooking.value === bookingId ? null : bookingId
}

const cancelBooking = (bookingId) => {
    if (confirm('Are you sure you want to cancel this booking?')) {
        router.patch(route('bookings.cancel', bookingId), {}, {
            onSuccess: () => {
                // Handle success
            }
        })
    }
}

const leaveReview = (bookingId) => {
    router.visit(route('bookings.review', bookingId))
}

// Computed
const hasBookings = computed(() => props.bookings.data && props.bookings.data.length > 0)
</script>

<template>
    <div>
        <ClientNavbar />

        <div class="min-h-screen bg-gray-50">
            <!-- Header -->
            <div class="bg-white shadow-sm border-b">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="py-6">
                        <h1 class="text-3xl font-bold text-gray-900">My Bookings</h1>
                        <p class="mt-2 text-gray-600">Manage and track your event bookings</p>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Filters and Search -->
                <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <!-- Search -->
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" placeholder="Search bookings..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                v-model="searchTerm" @input="applyFilters" />
                        </div>

                        <!-- Status Filter -->
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z">
                                </path>
                            </svg>
                            <select
                                class="w-full pl-10 pr-8 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white"
                                v-model="statusFilter" @change="applyFilters">
                                <option value="all">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <!-- Date Range Filter -->
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <select
                                class="w-full pl-10 pr-8 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white"
                                v-model="dateRangeFilter" @change="applyFilters">
                                <option value="all">All Dates</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="upcoming">Upcoming</option>
                                <option value="past">Past</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="relative">
                            <select
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none bg-white"
                                v-model="sortBy" @change="applyFilters">
                                <option value="date_desc">Newest First</option>
                                <option value="date_asc">Oldest First</option>
                                <option value="price_desc">Highest Price</option>
                                <option value="price_asc">Lowest Price</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex justify-end">
                        <button @click="resetFilters"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition-colors">
                            Reset Filters
                        </button>
                    </div>
                </div>

                <!-- Bookings List -->
                <div v-if="hasBookings" class="space-y-6">
                    <div v-for="booking in bookings.data" :key="booking.raw_id"
                        class="bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Main Booking Card -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="text-2xl font-bold text-gray-900">#{{ booking.id }}</div>
                                    <div
                                        :class="`px-3 py-1 rounded-full text-sm font-medium border ${getStatusColor(booking.status)}`">
                                        <span class="mr-1">{{ getStatusIcon(booking.status) }}</span>
                                        {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-blue-600">{{ booking.price }}</div>
                                    <div class="text-sm text-gray-500">{{ booking.pax }} guests</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ booking.event_name }}</h3>
                                    <div class="space-y-2 text-gray-600">
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="text-sm">{{ booking.event_location }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <span class="text-sm">{{ formatDate(booking.event_date) }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-sm">{{ booking.time }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-3">
                                        <span
                                            class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                                            {{ booking.category }}
                                        </span>
                                    </div>
                                    <div class="space-y-2 text-gray-600">
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                            <span class="text-sm">{{ booking.client }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <span class="text-sm">{{ booking.contact }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-6 flex flex-wrap gap-3">
                                <button @click="toggleBookingDetails(booking.raw_id)"
                                    class="flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    View Details
                                    <svg v-if="expandedBooking === booking.raw_id" class="h-4 w-4 ml-2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7"></path>
                                    </svg>
                                    <svg v-else class="h-4 w-4 ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <button v-if="booking.status === 'completed'" @click="leaveReview(booking.raw_id)"
                                    class="flex items-center px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                        </path>
                                    </svg>
                                    Leave Review
                                </button>

                                <button v-if="booking.status === 'pending'" @click="cancelBooking(booking.raw_id)"
                                    class="flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors">
                                    Cancel Booking
                                </button>
                            </div>
                        </div>

                        <!-- Expanded Details -->
                        <div v-if="expandedBooking === booking.raw_id" class="border-t bg-gray-50 p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        Event Details
                                    </h4>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div><strong>Notes:</strong> {{ booking.notes }}</div>
                                        <div><strong>Guests:</strong> {{ booking.pax }} people</div>
                                        <!-- <div><strong>Pricing:</strong> {{ booking.is_per_pax ? 'Per person' : 'Package
                                            deal' }}</div> -->
                                        <div><strong>Booked on:</strong> {{ formatDate(booking.formatted_date) }}</div>
                                    </div>
                                </div>

                                <div v-if="booking.catering_dishes && booking.catering_dishes.length > 0">
                                    <h4 class="font-semibold text-gray-900 mb-3">Menu Items</h4>
                                    <div class="space-y-1">
                                        <div v-for="(dish, index) in booking.catering_dishes" :key="index"
                                            class="flex items-center text-sm text-gray-600">
                                            <div class="w-2 h-2 bg-blue-400 rounded-full mr-2 flex-shrink-0"></div>
                                            {{ dish }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-12">
                    <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No bookings found</h3>
                    <p class="text-gray-600">Try adjusting your search or filter criteria.</p>
                </div>

                <!-- Pagination -->

            </div>
        </div>
    </div>
</template>

<style scoped>
/* Additional custom styles if needed */
</style>
