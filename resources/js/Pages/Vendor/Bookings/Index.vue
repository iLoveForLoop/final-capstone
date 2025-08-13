<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue'
import { ref, computed, watch, onMounted, onBeforeMount, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import { ChevronDown, Search, Filter, Calendar, Eye, Check, X, CircleCheck, Download } from 'lucide-vue-next'
import axios from 'axios'

const props = defineProps({
    bookings: {
        type: Object,
        required: true
    },
    stats: {
        type: Object,
        default: () => ({})
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

const statusOptions = [
    { value: 'all', label: 'All Statuses' },
    { value: 'pending', label: 'Pending' },
    { value: 'confirmed', label: 'Confirmed' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' }
]

const dateRangeOptions = [
    { value: 'all', label: 'All Dates' },
    { value: 'today', label: 'Today' },
    { value: 'week', label: 'This Week' },
    { value: 'month', label: 'This Month' },
    { value: 'upcoming', label: 'Upcoming' },
    { value: 'past', label: 'Past Bookings' }
]

const sortOptions = [
    { value: 'date_asc', label: 'Date (Oldest First)' },
    { value: 'date_desc', label: 'Date (Newest First)' },
    { value: 'price_asc', label: 'Price (Low to High)' },
    { value: 'price_desc', label: 'Price (High to Low)' }
]

// Filter controls - initialize from backend
const searchQuery = ref(props.filters.search || '')
const selectedStatus = ref(props.filters.status || 'all')
const selectedDateRange = ref(props.filters.date_range || 'all')
const selectedSort = ref(props.filters.sort || 'date_desc')

// Loading states
const loadingActions = ref({})

// Watch for filter changes and update URL
watch([searchQuery, selectedStatus, selectedDateRange, selectedSort], () => {
    updateFilters()
}, { deep: true })

// Debounced search
let searchTimeout = null
watch(searchQuery, () => {
    if (searchTimeout) clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        updateFilters()
    }, 500)
})

const updateFilters = () => {
    router.get(route('vendor.bookings.index'), {
        search: searchQuery.value,
        status: selectedStatus.value,
        date_range: selectedDateRange.value,
        sort: selectedSort.value
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

// Action functions with loading states
const acceptBooking = (bookingId) => {
    if (loadingActions.value[bookingId]) return
    loadingActions.value[bookingId] = 'accepting'

    router.patch(route('vendor.bookings.accept', bookingId), {}, {
        onFinish: () => {
            delete loadingActions.value[bookingId]
        },
        onError: () => {
            delete loadingActions.value[bookingId]
        }
    })
}

const declineBooking = (bookingId, reason = null) => {
    if (loadingActions.value[bookingId]) return

    const confirmed = confirm('Are you sure you want to cancel this booking?')
    if (!confirmed) return

    loadingActions.value[bookingId] = 'declining'

    router.patch(route('vendor.bookings.decline', bookingId), {
        reason: reason
    }, {
        onFinish: () => {
            delete loadingActions.value[bookingId]
        },
        onError: () => {
            delete loadingActions.value[bookingId]
        }
    })
}

const completeBooking = (bookingId) => {
    if (loadingActions.value[bookingId]) return

    const confirmed = confirm('Mark this booking as completed?')
    if (!confirmed) return

    loadingActions.value[bookingId] = 'completing'

    router.patch(route('vendor.bookings.complete', bookingId), {}, {
        onFinish: () => {
            delete loadingActions.value[bookingId]
        },
        onError: () => {
            delete loadingActions.value[bookingId]
        }
    })
}

// View booking details
const viewBookingDetails = (booking) => {
    router.get(route('vendor.bookings.show', booking.raw_id))
}

// Export bookings
const exportBookings = () => {
    window.open(route('vendor.bookings.export'), '_blank')
}

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

// Check if action is loading
const isLoading = (bookingId, action) => {
    return loadingActions.value[bookingId] === action
}

onBeforeUnmount(async () => {
    try {
        console.log('test')
        await axios.post(route('vendor.notifications.readAll'))
    } catch (error) {
        console.log(error.message)
    }

})
</script>

<template>
    <VendorLayout>
        <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Booking Management</h1>
                            <p class="mt-1 text-sm text-gray-500">Manage and track all your bookings</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="exportBookings"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <Download class="h-4 w-4 mr-2" />
                                Export
                            </button>
                            <span class="text-sm text-gray-500">
                                {{ bookings.total }} {{ bookings.total === 1 ? 'booking' : 'bookings' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <!-- <div v-if="stats" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm border p-4">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.total || 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border p-4">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Pending</p>
                                <p class="text-2xl font-semibold text-yellow-600">{{ stats.pending || 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border p-4">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">Confirmed</p>
                                <p class="text-2xl font-semibold text-green-600">{{ stats.confirmed || 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border p-4">
                        <div class="flex items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-500">This Month</p>
                                <p class="text-2xl font-semibold text-blue-600">{{ stats.this_month || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Filters -->
                <div class="mb-6 bg-white rounded-xl shadow-xs border border-gray-200 p-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Search class="h-5 w-5 text-gray-400" />
                            </div>
                            <input v-model="searchQuery" type="text" placeholder="Search bookings..."
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="sr-only">Status</label>
                            <div class="relative">
                                <select v-model="selectedStatus"
                                    class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg">
                                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <ChevronDown class="h-4 w-4 text-gray-400" />
                                </div>
                            </div>
                        </div>

                        <!-- Date Range Filter -->
                        <div>
                            <label class="sr-only">Date Range</label>
                            <div class="relative">
                                <select v-model="selectedDateRange"
                                    class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg">
                                    <option v-for="option in dateRangeOptions" :key="option.value"
                                        :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <Calendar class="h-4 w-4 text-gray-400" />
                                </div>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label class="sr-only">Sort By</label>
                            <div class="relative">
                                <select v-model="selectedSort"
                                    class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg">
                                    <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <Filter class="h-4 w-4 text-gray-400" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
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
                                        <div class="text-sm text-gray-500">{{ booking.service }}</div>
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
                                            <button @click="viewBookingDetails(booking)"
                                                class="text-indigo-600 hover:text-indigo-900 p-1 rounded-md hover:bg-indigo-50"
                                                title="View Details">
                                                <Eye class="h-5 w-5" />
                                            </button>

                                            <!-- Accept Button -->
                                            <button v-if="booking.status === 'pending'"
                                                @click="acceptBooking(booking.raw_id)"
                                                :disabled="isLoading(booking.raw_id, 'accepting')"
                                                class="text-green-600 hover:text-green-900 p-1 rounded-md hover:bg-green-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                                title="Accept">
                                                <div v-if="isLoading(booking.raw_id, 'accepting')"
                                                    class="animate-spin h-5 w-5 border-2 border-green-600 border-t-transparent rounded-full">
                                                </div>
                                                <Check v-else class="h-5 w-5" />
                                            </button>

                                            <!-- Cancel Button -->
                                            <button v-if="booking.status === 'pending'"
                                                @click="declineBooking(booking.raw_id)"
                                                :disabled="isLoading(booking.raw_id, 'declining')"
                                                class="text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                                title="Cancel">
                                                <div v-if="isLoading(booking.raw_id, 'declining')"
                                                    class="animate-spin h-5 w-5 border-2 border-red-600 border-t-transparent rounded-full">
                                                </div>
                                                <X v-else class="h-5 w-5" />
                                            </button>

                                            <!-- Complete Button -->
                                            <button v-if="booking.status === 'confirmed'"
                                                @click="completeBooking(booking.raw_id)"
                                                :disabled="isLoading(booking.raw_id, 'completing')"
                                                class="text-blue-600 hover:text-blue-900 p-1 rounded-md hover:bg-blue-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                                title="Mark as Completed">
                                                <div v-if="isLoading(booking.raw_id, 'completing')"
                                                    class="animate-spin h-5 w-5 border-2 border-blue-600 border-t-transparent rounded-full">
                                                </div>
                                                <CircleCheck v-else class="h-5 w-5" />
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
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                        aria-label="Pagination">
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
            </div>
        </div>
    </VendorLayout>
</template>

<style scoped>
/* Custom scrollbar for table */
::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Smooth transitions */
.shadow-xs {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* Hover effects */
tr {
    transition: background-color 0.2s ease;
}

button {
    transition: all 0.2s ease;
}
</style>
