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
const currentMonthYear = ref('')

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
    datesSet: handleDatesSet
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
        'confirmed': '#10b981',
        'pending': '#f59e0b',
        'completed': '#3b82f6',
        'cancelled': '#ef4444'
    }
    return colors[status] || '#6b7280'
}

function getEventBorderColor(status) {
    const colors = {
        'confirmed': '#059669',
        'pending': '#d97706',
        'completed': '#2563eb',
        'cancelled': '#dc2626'
    }
    return colors[status] || '#4b5563'
}

function getEventTextColor(status) {
    return '#ffffff'
}

function getBookingStatusColor(status) {
    const colors = {
        'confirmed': 'bg-green-50 text-green-700 border border-green-200',
        'pending': 'bg-amber-50 text-amber-700 border border-amber-200',
        'completed': 'bg-blue-50 text-blue-700 border border-blue-200',
        'cancelled': 'bg-red-50 text-red-700 border border-red-200'
    }
    return colors[status] || 'bg-gray-50 text-gray-700 border border-gray-200'
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
    const booking = info.event.extendedProps.booking
    info.el.setAttribute('title', `${booking.service_name} - ${booking.client_name} (${booking.status})`)

    // Clean event styling with subtle enhancements
    info.el.style.fontWeight = '500'
    info.el.style.border = 'none'
    info.el.style.borderLeft = `3px solid ${getEventBorderColor(booking.status)}`
}

function handleDatesSet(dateInfo) {
    const currentDate = dateInfo.view.currentStart
    currentMonthYear.value = currentDate.toLocaleDateString('en-US', {
        month: 'long',
        year: 'numeric'
    })
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

            // Set initial month/year
            const currentDate = calendarApi.getDate()
            currentMonthYear.value = currentDate.toLocaleDateString('en-US', {
                month: 'long',
                year: 'numeric'
            })
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
        <div class="min-h-screen bg-gray-50 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Booking Calendar</h1>
                            <p class="text-gray-600 mt-1">Manage your appointments and schedule</p>
                        </div>

                        <!-- Stats Cards -->
                        <div class="flex gap-4">
                            <div class="bg-white px-5 py-3 rounded-lg border border-gray-200 shadow-sm">
                                <div class="text-center">
                                    <span class="text-xl font-bold text-gray-900">{{ bookingStats.total }}</span>
                                    <p class="text-sm text-gray-600 mt-1">Total Bookings</p>
                                </div>
                            </div>
                            <div class="bg-white px-5 py-3 rounded-lg border border-gray-200 shadow-sm">
                                <div class="text-center">
                                    <span class="text-xl font-bold text-gray-900">{{ bookingStats.thisMonth }}</span>
                                    <p class="text-sm text-gray-600 mt-1">This Month</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Container -->
                <div class="space-y-4">
                    <!-- Control Panel -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                            <!-- Current Month & Navigation -->
                            <div class="flex items-center gap-4">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">{{ currentMonthYear }}</h2>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button @click="goToPrev"
                                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors border border-gray-300"
                                        aria-label="Previous month">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>

                                    <button @click="goToToday"
                                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                        Today
                                    </button>

                                    <button @click="goToNext"
                                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors border border-gray-300"
                                        aria-label="Next month">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- View Toggle & Legend -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <!-- View Toggle -->
                                <div class="flex items-center space-x-1 bg-gray-100 p-1 rounded-lg">
                                    <button @click="changeView('dayGridMonth')" :class="[
                                        'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                                        currentView === 'dayGridMonth'
                                            ? 'bg-white text-blue-600 shadow-sm'
                                            : 'text-gray-600 hover:text-gray-900'
                                    ]">
                                        Month
                                    </button>
                                    <button @click="changeView('listWeek')" :class="[
                                        'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                                        currentView === 'listWeek'
                                            ? 'bg-white text-blue-600 shadow-sm'
                                            : 'text-gray-600 hover:text-gray-900'
                                    ]">
                                        List
                                    </button>
                                </div>

                                <!-- Legend -->
                                <div class="flex items-center gap-3 text-sm">
                                    <div class="flex items-center space-x-1">
                                        <div class="w-3 h-3 bg-green-500 rounded-sm"></div>
                                        <span class="text-gray-700">Confirmed</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-sm"></div>
                                        <span class="text-gray-700">Pending</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="w-3 h-3 bg-blue-500 rounded-sm"></div>
                                        <span class="text-gray-700">Completed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="p-2">
                            <FullCalendar ref="calendarRef" :options="calendarOptions" class="fullcalendar-custom" />
                        </div>
                    </div>
                </div>

                <!-- Booking Details Modal -->
                <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal">
                        </div>

                        <div
                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full w-full">
                            <!-- Modal Header -->
                            <div class="bg-white px-6 pt-5 pb-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ selectedDate ? formatDate(selectedDate) : '' }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ selectedBookings.length }} booking{{ selectedBookings.length !== 1 ? 's'
                                            : '' }}
                                        </p>
                                    </div>
                                    <button @click="closeModal"
                                        class="rounded-md p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Modal Body -->
                            <div class="bg-white px-6 pb-4 sm:p-6 max-h-96 overflow-y-auto">
                                <div class="space-y-3" v-if="selectedBookings.length > 0">
                                    <div v-for="booking in selectedBookings" :key="booking.id"
                                        class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
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
                                                    'px-3 py-1 text-xs font-medium rounded-full',
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
                                    <h3 class="mt-4 text-sm font-medium text-gray-900">No bookings scheduled</h3>
                                    <p class="mt-2 text-sm text-gray-500">No appointments for this day.</p>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                                <button type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm"
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
/* Enhanced FullCalendar Styles - Clean but Professional */
.fullcalendar-custom {
    font-family: inherit;
    --fc-border-color: #e2e8f0;
    --fc-today-bg-color: #f0f9ff;
    --fc-neutral-bg-color: #f8fafc;
}

.fullcalendar-custom .fc-toolbar {
    display: none !important;
}

/* Calendar Grid Improvements */
.fullcalendar-custom .fc-scrollgrid {
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px;
    overflow: hidden;
}

.fullcalendar-custom .fc-scrollgrid thead {
    background: #f8fafc;
}

.fullcalendar-custom .fc-col-header-cell {
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    background: transparent !important;
    padding: 12px 8px;
}

.fullcalendar-custom .fc-col-header-cell:last-child {
    border-right: none;
}

.fullcalendar-custom .fc-col-header-cell-cushion {
    padding: 12px 8px;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.05em;
    font-weight: 600;
    color: #374151;
}

/* Day Cells - Clean hover effect */
.fullcalendar-custom .fc-daygrid-day {
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    background: white;
    transition: background-color 0.15s ease;
    position: relative;
}

.fullcalendar-custom .fc-daygrid-day:hover {
    background: #f8fafc !important;
}

.fullcalendar-custom .fc-daygrid-day:last-child {
    border-right: none;
}

.fullcalendar-custom .fc-daygrid-day.fc-day-other {
    background-color: #fafafa;
}

.fullcalendar-custom .fc-daygrid-day.fc-day-other .fc-daygrid-day-number {
    color: #9ca3af;
}

.fullcalendar-custom .fc-daygrid-day-number {
    color: #1f2937;
    font-weight: 500;
    padding: 8px;
    font-size: 14px;
    position: relative;
}

/* Today Highlight - Clean but clear */
.fullcalendar-custom .fc-day-today {
    background: #eff6ff !important;
    position: relative;
}

.fullcalendar-custom .fc-day-today::before {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    right: 2px;
    bottom: 2px;
    border: 2px solid #3b82f6;
    border-radius: 6px;
    pointer-events: none;
}

.fullcalendar-custom .fc-day-today .fc-daygrid-day-number {
    color: #1e40af;
    font-weight: 600;
}

/* Events Styling - Professional but clean */
.fullcalendar-custom .fc-event {
    border-radius: 4px;
    font-size: 11px;
    padding: 4px 6px;
    margin: 1px 2px;
    cursor: pointer;
    border: none;
    font-weight: 500;
    transition: opacity 0.15s ease;
    border-left: 3px solid transparent;
    line-height: 1.2;
}

.fullcalendar-custom .fc-event:hover {
    opacity: 0.9;
}

.fullcalendar-custom .fc-event-main {
    padding: 0;
}

/* More Events Popover */
.fullcalendar-custom .fc-more-popover {
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.fullcalendar-custom .fc-popover-header {
    background: #f9fafb;
    font-weight: 600;
    border-radius: 8px 8px 0 0;
    padding: 12px 16px;
    color: #374151;
}

/* List View */
.fullcalendar-custom .fc-list-event:hover {
    background-color: #f9fafb;
}

.fullcalendar-custom .fc-list-day-cushion {
    background: #f9fafb !important;
    font-weight: 600;
    color: #374151;
    padding: 12px 16px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .fullcalendar-custom .fc-event {
        font-size: 10px;
        padding: 3px 5px;
        border-radius: 3px;
    }

    .fullcalendar-custom .fc-col-header-cell-cushion {
        font-size: 11px;
        padding: 8px 4px;
    }

    .fullcalendar-custom .fc-daygrid-day-number {
        font-size: 12px;
        padding: 6px 4px;
    }
}
</style>
