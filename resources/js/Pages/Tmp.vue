<script setup>
import { ref } from 'vue'
import {
    X, FileText, User, Calendar, Clock, MapPin, Mail, Phone, Utensils,
    CheckCircle, MessageCircle, Users, Package, Truck, ChefHat, Sparkles
} from 'lucide-vue-next'

const isOpen = ref(false)
const booking = ref(null)
const isVisible = ref(false)

const open = (bookingData = {}) => {
    booking.value = bookingData
    isOpen.value = true
    setTimeout(() => {
        isVisible.value = true
    }, 10)
}

const close = () => {
    isVisible.value = false
    setTimeout(() => {
        isOpen.value = false
    }, 300)
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

const formatCurrency = (amount) => {
    if (!amount) return '₱0'
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0
    }).format(amount)
}

const getCateringService = () => {
    return booking.value?.service?.catering_service || booking.value?.catering_service
}
</script>

<template>
    <Transition name="modal">
        <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <!-- Background overlay -->
            <Transition name="fade">
                <div v-if="isVisible" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" aria-hidden="true"
                    @click="close">
                </div>
            </Transition>

            <!-- Modal container -->
            <div class="flex items-center justify-center min-h-screen p-4">
                <!-- Modal panel -->
                <Transition name="scale">
                    <div v-if="isVisible"
                        class="relative bg-white rounded-xl shadow-2xl transform transition-all w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden flex flex-col">

                        <!-- Gradient header -->
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-4 sm:p-6 text-white">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                        <FileText :size="20" class="text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl sm:text-2xl font-bold" id="modal-title">
                                            Booking Details
                                        </h3>
                                        <p class="text-sm opacity-90 mt-1">ID: #{{ booking?.id }}</p>
                                    </div>
                                </div>
                                <button @click="close"
                                    class="p-2 hover:bg-white/20 rounded-lg transition-all duration-200">
                                    <X :size="20" class="text-white" />
                                </button>
                            </div>

                            <!-- Status and Service Type -->
                            <div class="flex flex-wrap items-center gap-3">
                                <span :class="getStatusBadgeClass(booking?.status)"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border backdrop-blur-sm">
                                    {{ booking?.status?.charAt(0).toUpperCase() + booking?.status?.slice(1) }}
                                </span>

                                <span v-if="getCateringService()"
                                    class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium border border-orange-200">
                                    <ChefHat :size="14" class="mr-1" />
                                    Catering Service
                                </span>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                            <div class="space-y-4">

                                <!-- Service & Pricing -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <!-- Service Info -->
                                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <Package :size="18" class="text-blue-600" />
                                            <span class="font-semibold text-blue-900">Service Details</span>
                                        </div>
                                        <p class="text-lg font-bold text-gray-900 mb-2">{{ booking?.service?.name ||
                                            'N/A' }}</p>

                                        <div class="space-y-2">
                                            <div v-if="booking?.pax"
                                                class="flex items-center space-x-2 text-sm text-gray-700">
                                                <Users :size="14" class="text-gray-500" />
                                                <span><strong>{{ booking.pax }}</strong> guests</span>
                                            </div>

                                            <div v-if="getCateringService()?.buffet_type"
                                                class="flex items-center space-x-2 text-sm text-gray-700">
                                                <Sparkles :size="14" class="text-gray-500" />
                                                <span class="capitalize">{{ getCateringService().buffet_type }}
                                                    Buffet</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pricing -->
                                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <Package :size="18" class="text-green-600" />
                                            <span class="font-semibold text-green-900">Pricing</span>
                                        </div>

                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-700">Service Price</span>
                                                <span class="text-lg font-bold text-gray-900">
                                                    {{ formatCurrency(getCateringService()?.price ||
                                                        booking?.service?.price) }}
                                                    <span
                                                        v-if="getCateringService()?.price && !getCateringService()?.package_price"
                                                        class="text-sm font-normal text-gray-600">/person</span>
                                                </span>
                                            </div>

                                            <div v-if="getCateringService()?.delivery_fee"
                                                class="flex justify-between items-center">
                                                <span class="text-sm text-gray-700">Delivery Fee</span>
                                                <span class="text-sm font-semibold text-gray-900">
                                                    +{{ formatCurrency(getCateringService().delivery_fee) }}
                                                </span>
                                            </div>

                                            <div v-if="getCateringService()?.package_price"
                                                class="flex justify-between items-center pt-2 border-t border-green-200">
                                                <span class="text-sm font-semibold text-green-900">Package Total</span>
                                                <span class="text-lg font-bold text-green-900">
                                                    {{ formatCurrency(getCateringService().package_price) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Event & Timing -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <!-- Event Details -->
                                    <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <Calendar :size="18" class="text-purple-600" />
                                            <span class="font-semibold text-purple-900">Event Details</span>
                                        </div>

                                        <div class="space-y-3">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ booking?.event?.name ||
                                                    'N/A' }}</p>
                                                <p class="text-xs text-gray-600">{{ booking?.event?.location || 'N/A' }}
                                                </p>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <p class="text-xs text-gray-500">Date</p>
                                                    <p class="text-sm font-medium text-gray-900">{{
                                                        formatDate(booking?.event?.event_date) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Time</p>
                                                    <p class="text-sm font-medium text-gray-900">{{
                                                        formatTime(booking?.event?.event_time) }}</p>
                                                </div>
                                            </div>

                                            <div v-if="booking?.event?.description" class="bg-white rounded-lg p-3">
                                                <p class="text-xs text-gray-500 mb-1">Description</p>
                                                <p class="text-sm text-gray-700">{{ booking.event.description }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Catering Specifics -->
                                    <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <ChefHat :size="18" class="text-orange-600" />
                                            <span class="font-semibold text-orange-900">Catering Details</span>
                                        </div>

                                        <div class="space-y-3">
                                            <div v-if="getCateringService()?.min_pax && getCateringService()?.max_pax"
                                                class="flex items-center space-x-2 text-sm text-gray-700">
                                                <Users :size="14" class="text-gray-500" />
                                                <span>Capacity: <strong>{{ getCateringService().min_pax }}-{{
                                                    getCateringService().max_pax }}</strong> pax</span>
                                            </div>

                                            <div v-if="getCateringService()?.lead_time_days"
                                                class="flex items-center space-x-2 text-sm text-gray-700">
                                                <Clock :size="14" class="text-gray-500" />
                                                <span>Lead time: <strong>{{ getCateringService().lead_time_days
                                                }}</strong> days</span>
                                            </div>

                                            <div v-if="getCateringService()?.is_customizable"
                                                class="flex items-center space-x-2 text-sm text-emerald-700">
                                                <Sparkles :size="14" />
                                                <span>Customizable menu available</span>
                                            </div>

                                            <div v-if="getCateringService()?.service_area?.length" class="text-sm">
                                                <p class="text-xs text-gray-500 mb-1">Service Areas</p>
                                                <div class="flex flex-wrap gap-1">
                                                    <span
                                                        v-for="(area, index) in getCateringService().service_area.slice(0, 3)"
                                                        :key="index"
                                                        class="px-2 py-1 bg-white text-xs text-gray-700 rounded-full border">
                                                        {{ area }}
                                                    </span>
                                                    <span v-if="getCateringService().service_area.length > 3"
                                                        class="px-2 py-1 bg-white text-xs text-gray-500 rounded-full border">
                                                        +{{ getCateringService().service_area.length - 3 }} more
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Client & Contact -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <!-- Client Information -->
                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <User :size="18" class="text-gray-600" />
                                            <span class="font-semibold text-gray-900">Client Information</span>
                                        </div>

                                        <div class="space-y-3">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{
                                                    booking?.user?.client?.full_name || 'N/A' }}</p>
                                            </div>

                                            <div class="space-y-2">
                                                <div class="flex items-center space-x-2 text-sm text-gray-700">
                                                    <Phone :size="14" class="text-gray-500" />
                                                    <span>{{ booking?.user?.client?.contact_number || 'N/A' }}</span>
                                                </div>

                                                <div class="flex items-center space-x-2 text-sm text-gray-700">
                                                    <Mail :size="14" class="text-gray-500" />
                                                    <span>{{ booking?.user?.email || 'N/A' }}</span>
                                                </div>

                                                <div class="flex items-center space-x-2 text-sm text-gray-700">
                                                    <MapPin :size="14" class="text-gray-500" />
                                                    <span>{{ booking?.user?.client?.location || 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Catering Dishes -->
                                    <div v-if="booking?.catering_dishes && booking.catering_dishes.length > 0"
                                        class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <Utensils :size="18" class="text-amber-600" />
                                            <span class="font-semibold text-amber-900">Selected Dishes ({{
                                                booking.catering_dishes.length }})</span>
                                        </div>

                                        <div class="space-y-2 max-h-40 overflow-y-auto">
                                            <div v-for="(dish, index) in booking.catering_dishes" :key="index"
                                                class="bg-white rounded-lg p-3 border border-amber-200">
                                                <p class="text-sm font-medium text-gray-900 mb-1">{{ dish.name || dish
                                                }}</p>
                                                <p v-if="dish.description" class="text-xs text-gray-600 mb-1">{{
                                                    dish.description }}</p>
                                                <p v-if="dish.price" class="text-xs font-medium text-amber-600">
                                                    {{ formatCurrency(dish.price) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                                    <h4 class="font-semibold text-blue-900 mb-3">Quick Actions</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        <button v-if="booking?.status === 'pending'"
                                            class="flex items-center justify-center space-x-2 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                            <CheckCircle :size="16" />
                                            <span>Confirm Booking</span>
                                        </button>

                                        <button v-if="booking?.status === 'confirmed'"
                                            class="flex items-center justify-center space-x-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                            <CheckCircle :size="16" />
                                            <span>Mark Complete</span>
                                        </button>

                                        <button
                                            class="flex items-center justify-center space-x-2 px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium">
                                            <MessageCircle :size="16" />
                                            <span>Contact Client</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-white border-t border-gray-200 p-4">
                            <div class="flex justify-end">
                                <button @click="close"
                                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm">
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
    transform: scale(0.95) translateY(-10px);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 4px;
}

::-webkit-scrollbar-track {
    background: #f8fafc;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}
</style>
