<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import VendorLayout from '@/Layouts/VendorLayout.vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import listPlugin from '@fullcalendar/list'

const props = defineProps({
    bookings: {
        type: Array,
        default: () => []
    },
    bookingStats: {
        type: Object,
        default: () => ({ total: 0, thisMonth: 0 })
    }
})

const calendarRef = ref(null)
const showModal = ref(false)
const selectedBookings = ref([])
const selectedDate = ref(null)
const currentView = ref('dayGridMonth')

// FullCalendar options
const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin, listPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,listWeek'
    },
    height: 'auto',
    events: [],
    eventDisplay: 'block',
    dayMaxEvents: 3,
    moreLinkClick: 'popover',
    eventClick: handleEventClick,
    dateClick: handleDateClick,
    eventDidMount: handleEventDidMount,
    dayCellDidMount: handleDayCellDidMount
})

// Convert bookings to FullCalendar events
const calendarEvents = computed(() => {
    return props.bookings.map(booking => ({
        id: booking.id,
        title: `${booking.service_name} - ${booking.client_name}`,
        date: booking.event_date,
        backgroundColor: getEventBackgroundColor(booking.status),
        borderColor: getEventBorderColor(booking.status),
        textColor: getEventTextColor(booking.status),
        extendedProps: {
            booking: booking,
            status: booking.status,
            clientName: booking.client_name,
            serviceName: booking.service_name
        }
    }))
})

function getEventBackgroundColor(status) {
    const colors = {
        'confirmed': '#10b981', // green-500
        'pending': '#f59e0b',   // yellow-500
        'completed': '#3b82f6', // blue-500
        'cancelled': '#ef4444'  // red-500
    }
    return colors[status] || '#6b7280' // gray-500
}

function getEventBorderColor(status) {
    const colors = {
        'confirmed': '#059669', // green-600
        'pending': '#d97706',   // yellow-600
        'completed': '#2563eb', // blue-600
        'cancelled': '#dc2626'  // red-600
    }
    return colors[status] || '#4b5563' // gray-600
}

function getEventTextColor(status) {
    return '#ffffff' // white text for all events
}

function getBookingStatusColor(status) {
    const colors = {
        'confirmed': 'bg-green-100 text-green-800',
        'pending': 'bg-yellow-100 text-yellow-800',
        'completed': 'bg-blue-100 text-blue-800',
        'cancelled': 'bg-red-100 text-red-800'
    }
    return colors[status] || 'bg-gray-100 text-gray-800'
}

function handleEventClick(info) {
    const booking = info.event.extendedProps.booking
    selectedBookings.value = [booking]
    selectedDate.value = info.event.start
    showModal.value = true
}

function handleDateClick(info) {
    const clickedDate = info.dateStr
    const dayBookings = props.bookings.filter(booking => booking.event_date === clickedDate)

    if (dayBookings.length > 0) {
        selectedBookings.value = dayBookings
        selectedDate.value = info.date
        showModal.value = true
    }
}

function handleEventDidMount(info) {
    // Add custom styling or tooltips if needed
    const booking = info.event.extendedProps.booking
    info.el.setAttribute('title', `${booking.service_name} - ${booking.client_name} (${booking.status})`)
}

function handleDayCellDidMount(info) {
    // Add custom day cell styling if needed
    const dateStr = info.date.toISOString().split('T')[0]
    const dayBookings = props.bookings.filter(booking => booking.event_date === dateStr)

    if (dayBookings.length > 0) {
        info.el.classList.add('has-bookings')
        info.el.style.cursor = 'pointer'
    }
}

function closeModal() {
    showModal.value = false
    selectedBookings.value = []
    selectedDate.value = null
}

function formatDate(date) {
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

function changeView(view) {
    currentView.value = view
    if (calendarRef.value) {
        calendarRef.value.getApi().changeView(view)
    }
}

function goToToday() {
    if (calendarRef.value) {
        calendarRef.value.getApi().today()
    }
}

function goToPrev() {
    if (calendarRef.value) {
        calendarRef.value.getApi().prev()
    }
}

function goToNext() {
    if (calendarRef.value) {
        calendarRef.value.getApi().next()
    }
}

onMounted(() => {
    nextTick(() => {
        if (calendarRef.value) {
            // Update events
            calendarOptions.value.events = calendarEvents.value

            // Render calendar
            const calendarApi = calendarRef.value.getApi()
            calendarApi.removeAllEvents()
            calendarApi.addEventSource(calendarEvents.value)
        }
    })
})

// Watch for prop changes and update events
import { watch } from 'vue'
watch(() => props.bookings, (newBookings) => {
    if (calendarRef.value) {
        const calendarApi = calendarRef.value.getApi()
        calendarApi.removeAllEvents()
        calendarApi.addEventSource(calendarEvents.value)
    }
}, { deep: true })
</script>

<template>
    <VendorLayout title="Calendar">
        <div class="min-h-screen bg-gray-50 py-4 md:py-8">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-6 md:mb-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Calendar</h1>
                            <p class="text-gray-500 text-sm mt-1">View your bookings and schedule overview</p>
                        </div>

                        <!-- Stats Cards -->
                        <!-- <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                            <div
                                class="bg-white px-3 py-2 md:px-4 md:py-2 rounded-lg shadow-sm border flex-1 sm:flex-initial">
                                <div class="text-center sm:text-left">
                                    <span class="text-lg md:text-xl font-semibold text-gray-900">{{ bookingStats.total
                                    }}</span>
                                    <p class="text-xs text-gray-500">Total Bookings</p>
                                </div>
                            </div>
                            <div
                                class="bg-white px-3 py-2 md:px-4 md:py-2 rounded-lg shadow-sm border flex-1 sm:flex-initial">
                                <div class="text-center sm:text-left">
                                    <span class="text-lg md:text-xl font-semibold text-gray-900">{{
                                        bookingStats.thisMonth }}</span>
                                    <p class="text-xs text-gray-500">This Month</p>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>

                <!-- Custom Toolbar -->
                <div class="bg-white rounded-xl shadow-sm border mb-4 md:mb-6">
                    <div class="px-3 md:px-6 py-3 md:py-4 border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <!-- View Toggle -->
                            <div class="flex items-center space-x-2">
                                <button @click="changeView('dayGridMonth')" :class="[
                                    'px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200',
                                    currentView === 'dayGridMonth'
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]">
                                    Month
                                </button>
                                <button @click="changeView('listWeek')" :class="[
                                    'px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200',
                                    currentView === 'listWeek'
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]">
                                    List
                                </button>
                            </div>

                            <!-- Navigation -->
                            <div class="flex items-center space-x-2">
                                <button @click="goToPrev"
                                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200"
                                    aria-label="Previous">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>

                                <button @click="goToToday"
                                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    Today
                                </button>

                                <button @click="goToNext"
                                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200"
                                    aria-label="Next">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="px-3 md:px-6 py-3 bg-gray-50 border-b border-gray-100">
                        <div class="flex flex-wrap items-center gap-3 md:gap-6 text-xs justify-center sm:justify-start">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded-sm"></div>
                                <span class="text-gray-600">Confirmed</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-yellow-500 rounded-sm"></div>
                                <span class="text-gray-600">Pending</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-blue-500 rounded-sm"></div>
                                <span class="text-gray-600">Completed</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-red-500 rounded-sm"></div>
                                <span class="text-gray-600">Cancelled</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FullCalendar Component -->
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="p-4 md:p-6">
                        <FullCalendar ref="calendarRef" :options="calendarOptions" class="fullcalendar-custom" />
                    </div>
                </div>

                <!-- Booking Details Modal -->
                <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal">
                        </div>

                        <div
                            class="inline-block align-bottom bg-white rounded-t-lg sm:rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full w-full">
                            <!-- Modal Header -->
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        {{ selectedDate ? formatDate(selectedDate) : '' }}
                                    </h3>
                                    <button @click="closeModal"
                                        class="rounded-md text-gray-400 hover:text-gray-600 focus:outline-none">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ selectedBookings.length }} booking{{ selectedBookings.length !== 1 ? 's' : '' }}
                                </p>
                            </div>

                            <!-- Modal Body -->
                            <div class="bg-white px-4 pb-4 sm:p-6 max-h-96 overflow-y-auto">
                                <div class="space-y-3" v-if="selectedBookings.length > 0">
                                    <div v-for="booking in selectedBookings" :key="booking.id"
                                        class="border rounded-lg p-3 sm:p-4 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-900 text-sm sm:text-base">{{
                                                    booking.service_name }}</h4>
                                                <p class="text-sm text-gray-600 mt-1">Client: {{ booking.client_name }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">{{ booking.event_date }}</p>
                                            </div>
                                            <div
                                                class="flex items-center justify-between sm:justify-end sm:flex-col sm:items-end gap-2">
                                                <span :class="[
                                                    'px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap',
                                                    getBookingStatusColor(booking.status)
                                                ]">
                                                    {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1)
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Empty State -->
                                <div v-else class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No bookings</h3>
                                    <p class="mt-1 text-sm text-gray-500">No bookings scheduled for this day.</p>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div
                                class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                <button type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
                                    @click="closeModal">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<style>
/* Custom FullCalendar Styles */
.fullcalendar-custom {
    font-family: inherit;
}

.fullcalendar-custom .fc-toolbar {
    display: none !important;
    /* Hide default toolbar since we have custom one */
}

.fullcalendar-custom .fc-daygrid-day.has-bookings {
    background-color: #f8fafc;
}

.fullcalendar-custom .fc-daygrid-day:hover {
    background-color: #f1f5f9;
}

.fullcalendar-custom .fc-event {
    border-radius: 6px;
    font-size: 12px;
    padding: 2px 6px;
    margin-bottom: 2px;
    cursor: pointer;
}

.fullcalendar-custom .fc-event:hover {
    opacity: 0.8;
}

.fullcalendar-custom .fc-day-today {
    background-color: #dbeafe !important;
}

.fullcalendar-custom .fc-col-header-cell {
    background-color: #f9fafb;
    font-weight: 600;
    color: #374151;
}

.fullcalendar-custom .fc-daygrid-day-number {
    color: #1f2937;
    font-weight: 500;
}

.fullcalendar-custom .fc-list-event:hover {
    background-color: #f3f4f6;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .fullcalendar-custom .fc-event {
        font-size: 10px;
        padding: 1px 4px;
    }

    .fullcalendar-custom .fc-col-header-cell {
        font-size: 12px;
    }
}
</style>
