<script setup>
import { ref, watch } from 'vue'

const isOpen = ref(false)
const booking = ref(null)
const isVisible = ref(false)

const open = (bookingData = {}) => {
    booking.value = bookingData
    isOpen.value = true
    // Small delay to trigger animation after modal is rendered
    setTimeout(() => {
        isVisible.value = true
    }, 10)
}

const close = () => {
    isVisible.value = false
    setTimeout(() => {
        isOpen.value = false
    }, 300) // Match this with CSS transition duration
}

defineExpose({
    open,
    close
})

const getStatusBadgeClass = (status) => {
    const classes = {
        pending: 'bg-amber-100 text-amber-800 border-amber-200',
        confirmed: 'bg-blue-100 text-blue-800 border-blue-200',
        cancelled: 'bg-red-100 text-red-800 border-red-200',
        completed: 'bg-emerald-100 text-emerald-800 border-emerald-200'
    }
    return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200'
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const formatTime = (time) => {
    if (!time) return 'N/A'
    return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    })
}

const formatDateTime = (datetime) => {
    if (!datetime) return 'N/A'
    return new Date(datetime).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    })
}
</script>

<template>
    <Transition name="modal">
        <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <!-- Background overlay with fade animation -->
            <Transition name="fade">
                <div v-if="isVisible" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" aria-hidden="true"
                    @click="close">
                </div>
            </Transition>

            <!-- Modal container -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <!-- Modal panel with scale animation -->
                <Transition name="scale">
                    <div v-if="isVisible"
                        class="relative bg-white rounded-2xl shadow-2xl transform transition-all w-full max-w-6xl mx-4 max-h-[90vh] overflow-hidden">

                        <!-- Gradient header -->
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-white">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold" id="modal-title">
                                        Booking Details
                                    </h3>
                                </div>
                                <button @click="close"
                                    class="p-2 hover:bg-white/20 rounded-lg transition-all duration-200 transform hover:scale-110">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Status Badge -->
                            <div class="flex items-center space-x-4">
                                <span :class="getStatusBadgeClass(booking?.status)"
                                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border-2 backdrop-blur-sm">
                                    {{ booking?.status?.charAt(0).toUpperCase() + booking?.status?.slice(1) }}
                                </span>
                                <span class="text-sm opacity-90">Booking ID: #{{ booking?.id }}</span>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                            <!-- Grid Layout -->
                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <!-- Client Information Card -->
                                    <div
                                        class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-6 shadow-sm border border-gray-100">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <div
                                                class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            Client Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Full
                                                    Name</label>
                                                <p class="text-gray-900 font-medium text-sm">{{
                                                    booking?.user?.client?.full_name || 'N/A' }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Contact
                                                    Number</label>
                                                <p class="text-gray-900 text-sm">{{
                                                    booking?.user?.client?.contact_number || 'N/A' }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</label>
                                                <p class="text-gray-900 text-sm">{{ booking?.user?.email || 'N/A' }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</label>
                                                <p class="text-gray-900 text-sm">{{ booking?.user?.client?.location ||
                                                    'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Event Details Card -->
                                    <div
                                        class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-6 shadow-sm border border-gray-100">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <div
                                                class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            Event Details
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Event
                                                    Name</label>
                                                <p class="text-gray-900 font-medium text-sm">{{ booking?.event?.name ||
                                                    'N/A' }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Event
                                                    Date</label>
                                                <p class="text-gray-900 text-sm">{{
                                                    formatDate(booking?.event?.event_date) }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Event
                                                    Time</label>
                                                <p class="text-gray-900 text-sm">{{
                                                    formatTime(booking?.event?.event_time) }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</label>
                                                <p class="text-gray-900 text-sm">{{ booking?.event?.location || 'N/A' }}
                                                </p>
                                            </div>
                                            <div v-if="booking?.event?.description" class="md:col-span-2 space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Description</label>
                                                <p class="text-gray-900 text-sm leading-relaxed">{{
                                                    booking?.event?.description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-6">
                                    <!-- Booking Information Card -->
                                    <div
                                        class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-6 shadow-sm border border-gray-100">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <div
                                                class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            Booking Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Service</label>
                                                <p class="text-gray-900 font-medium text-sm">{{ booking?.service?.name
                                                    || 'N/A' }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Booking
                                                    Date</label>
                                                <p class="text-gray-900 text-sm">{{ formatDate(booking?.booking_date) }}
                                                </p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Created
                                                    At</label>
                                                <p class="text-gray-900 text-sm">{{ formatDateTime(booking?.created_at)
                                                    }}</p>
                                            </div>
                                            <div v-if="booking?.pax" class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Number
                                                    of Guests</label>
                                                <p class="text-gray-900 font-medium text-sm">{{ booking.pax }} people
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Catering Dishes Card -->
                                    <div v-if="booking?.catering_dishes && booking.catering_dishes.length > 0"
                                        class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-6 shadow-sm border border-gray-100">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <div
                                                class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            Selected Dishes
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div v-for="(dish, index) in booking.catering_dishes" :key="index"
                                                class="bg-white rounded-lg p-3 shadow-xs border border-gray-100 hover:shadow-md transition-shadow">
                                                <p class="text-gray-900 font-medium text-sm mb-1">{{ dish.name || dish
                                                    }}</p>
                                                <p v-if="dish.description" class="text-gray-600 text-xs">{{
                                                    dish.description }}</p>
                                                <p v-if="dish.price" class="text-orange-600 text-sm font-medium mt-1">
                                                    ₱{{ dish.price }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Actions Card -->
                                    <div
                                        class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 shadow-sm border border-blue-100">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                                        <div class="flex flex-wrap gap-3">
                                            <button v-if="booking?.status === 'pending'"
                                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all duration-200 transform hover:scale-105 text-sm font-medium shadow-sm">
                                                Confirm Booking
                                            </button>
                                            <button v-if="booking?.status === 'confirmed'"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-105 text-sm font-medium shadow-sm">
                                                Mark Complete
                                            </button>
                                            <button
                                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 transform hover:scale-105 text-sm font-medium shadow-sm">
                                                Contact Client
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                            <div class="flex justify-end">
                                <button @click="close"
                                    class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 transform hover:scale-105 font-medium shadow-sm">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Fade animation for backdrop */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Scale animation for modal */
.scale-enter-active,
.scale-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.scale-enter-from,
.scale-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
}

/* Modal container transition */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

/* Custom scrollbar for modal content */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions for all interactive elements */
* {
    transition-property: color, background-color, border-color, transform, box-shadow;
    transition-duration: 0.2s;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
