<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue'
import { ref, computed, watch, onMounted, onBeforeMount, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import { ChevronDown, Search, Filter, Calendar, Eye, Check, X, CircleCheck, Download, AlertTriangle, Info, Clock, User, MapPin, CreditCard } from 'lucide-vue-next'
import axios from 'axios'
import BookingDetailsModal from '@/Components/BookingDetailsModal.vue'
import { useNotificationStore } from '@/store/notification'
import AcceptBookingModal from '@/Components/Vendor/Booking/AcceptBookingModal.vue'
import DeclineBookingModal from '@/Components/Vendor/Booking/DeclineBookingModal.vue'
import CompleteBookingModal from '@/Components/Vendor/Booking/CompleteBookingModal.vue'
import Statcard from '@/Components/Vendor/Booking/Statcard.vue'
import Filters from '@/Components/Vendor/Booking/Filters.vue'
import BookingsTable from '@/Components/Vendor/Booking/BookingsTable.vue'
import { push } from 'notivue'
import CancelBookingModal from '@/Components/Vendor/Booking/CancelBookingModal.vue'
import VendorAcceptanceModal from '@/Components/Vendor/Booking/VendorAcceptanceModal.vue'

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

// Modal states
const showAcceptModal = ref(false)
const showDeclineModal = ref(false)
const showCompleteModal = ref(false)
const showCancelModal = ref(false)
const selectedBooking = ref(null)
const declineReason = ref('')
const cancelReason = ref('')
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

// Modal functions
const openAcceptModal = (booking) => {
    selectedBooking.value = booking
    showAcceptModal.value = true
}

const openDeclineModal = (booking) => {
    selectedBooking.value = booking
    declineReason.value = ''
    showDeclineModal.value = true
}

const openCompleteModal = (booking) => {
    selectedBooking.value = booking
    showCompleteModal.value = true

}

const openCancelModal = (booking) => {
    selectedBooking.value = booking
    showCancelModal.value = true
}

const closeModals = () => {
    showAcceptModal.value = false
    showCancelModal.value = false
    showDeclineModal.value = false
    showCompleteModal.value = false
    selectedBooking.value = null
    declineReason.value = ''
}

// Action functions with loading states
const acceptBooking = () => {
    if (!selectedBooking.value || loadingActions.value[selectedBooking.value.raw_id]) return

    loadingActions.value[selectedBooking.value.raw_id] = 'accepting'

    router.patch(route('vendor.bookings.accept', selectedBooking.value.raw_id), {}, {
        onFinish: () => {
            delete loadingActions.value[selectedBooking.value.raw_id]
            closeModals()
        },
        onError: () => {
            delete loadingActions.value[selectedBooking.value.raw_id]
        }
    })
}

const declineBooking = () => {
    if (!selectedBooking.value || loadingActions.value[selectedBooking.value.raw_id]) return

    loadingActions.value[selectedBooking.value.raw_id] = 'declining'

    router.patch(route('vendor.bookings.decline', selectedBooking.value.raw_id), {
        reason: declineReason.value || 'No reason provided',
    }, {
        onFinish: () => {
            delete loadingActions.value[selectedBooking.value.raw_id]
            closeModals()
        },
        onError: () => {
            delete loadingActions.value[selectedBooking.value.raw_id]
        }
    })
}

const cancelBooking = () => {
    if (!selectedBooking.value || loadingActions.value[selectedBooking.value.raw_id]) return

    loadingActions.value[selectedBooking.value.raw_id] = 'cancelling'

    router.patch(route('vendor.bookings.cancel', selectedBooking.value.raw_id), {
        reason: cancelReason.value || 'No reason provided',
    }, {
        onFinish: () => {
            delete loadingActions.value[selectedBooking.value.raw_id]
            closeModals()
        },
        onError: () => {
            delete loadingActions.value[selectedBooking.value.raw_id]
        }
    })
}

const completeBooking = () => {
    if (!selectedBooking.value || loadingActions.value[selectedBooking.value.raw_id]) return




    loadingActions.value[selectedBooking.value.raw_id] = 'completing'

    router.patch(route('vendor.bookings.complete', selectedBooking.value.raw_id), {}, {
        onFinish: () => {
            delete loadingActions.value[selectedBooking.value.raw_id]
            closeModals()
            push.success('Booking Completed Succesesfully')
        },
        onError: () => {
            delete loadingActions.value[selectedBooking.value.raw_id]
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

const notificationStore = useNotificationStore()

onMounted(async () => {
    try {
        await axios.post('/vendor/bookings/notifications/mark-read')
        notificationStore.notifications.forEach(n => {
            if (n.type === 'booking_received') {
                notificationStore.markAsRead(n.id)
            }
        })
    } catch (error) {
        console.error('Failed to mark booking notifications as read:', error)
    }
})

const bookingDetailsModal = ref(null)

</script>

<template>
    <VendorLayout>
        <VendorAcceptanceModal ref="bookingDetailsModal" @contact-client="handleContactClient"
            @view-booking-details="handleViewBookingDetails" />


        <!-- <BookingDetailsModal ref="bookingDetailsModal" @accept-booking="openAcceptModal"
            @decline-booking="openDeclineModal" @complete-booking="openCompleteModal"
            @cancel-booking="openCancelModal" /> -->

        <!-- Accept Booking Modal -->
        <AcceptBookingModal :showAcceptModal="showAcceptModal" :selectedBooking="selectedBooking" :isLoading="isLoading"
            @close-modals="closeModals" @accept-booking="acceptBooking" :formatDate="formatDate" />

        <!-- Decline Booking Modal -->
        <DeclineBookingModal :showDeclineModal="showDeclineModal" :selectedBooking="selectedBooking"
            :isLoading="isLoading" @close-modals="closeModals" @decline-booking="declineBooking"
            :formatDate="formatDate" v-model:declineReason="declineReason" />

        <!-- Complete Booking Modal -->
        <CompleteBookingModal :showCompleteModal=showCompleteModal :selectedBooking="selectedBooking"
            :isLoading="isLoading" @close-modals="closeModals" :formatDate="formatDate"
            @complete-booking="completeBooking" />

        <!-- Cancel Booking Modal -->
        <CancelBookingModal :showCancelModal="showCancelModal" :selectedBooking="selectedBooking" :isLoading="isLoading"
            @close-modals="closeModals" @cancel-booking="cancelBooking" :formatDate="formatDate"
            v-model:cancelReason="cancelReason" />

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
                <!-- <Statcard :stats="stats" /> -->


                <!-- Filters -->
                <Filters :statusOptions="statusOptions" :dateRangeOptions="dateRangeOptions" :sortOptions="sortOptions"
                    v-model:searchQuery="searchQuery" v-model:selectedStatus="selectedStatus"
                    v-model:selectedDateRange="selectedDateRange" v-model:selectedSort="selectedSort" />


                <!-- Bookings Table -->
                <BookingsTable :bookings="bookings" :isLoading="isLoading"
                    v-model:bookingDetailsModal="bookingDetailsModal" @open-accept-modal="openAcceptModal"
                    @open-decline-modal="openDeclineModal" @open-complete-modal="openCompleteModal"
                    @open-cancel-modal="openCancelModal" />

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
