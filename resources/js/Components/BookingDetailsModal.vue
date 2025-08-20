<script setup>
import { ref } from 'vue'
import {
    X, FileText, User, Calendar, Clock, MapPin, Mail, Phone, Utensils,
    CheckCircle, MessageCircle, Users, Package, ChefHat, Sparkles, CreditCard
} from 'lucide-vue-next'

const isOpen = ref(false)
const booking = ref(null)
const isVisible = ref(false)

const open = (bookingData = {}) => {
    booking.value = bookingData
    isOpen.value = true
    setTimeout(() => {
        isVisible.value = true
    }, 20)
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
    return booking.value?.service?.catering_service
}

const calculateTotalDishPrice = () => {
    if (!booking.value?.catering_dishes) return 0
    return booking.value.catering_dishes.reduce((total, dish) => {
        return total + (parseFloat(dish.price) || 0)
    }, 0)
}

const calculateTotalPrice = () => {
    const basePrice = parseFloat(booking.value?.service?.price) || 0
    const dishTotal = calculateTotalDishPrice()
    const pax = booking.value?.pax || 1
    return (basePrice * pax) + dishTotal
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
            <div class="flex items-center justify-center min-h-screen p-2 sm:p-4">
                <!-- Modal panel -->
                <Transition name="scale">
                    <div v-if="isVisible"
                        class="relative bg-white rounded-xl shadow-2xl transform transition-all w-full max-w-6xl mx-auto max-h-[90vh] overflow-hidden flex flex-col border border-gray-200">

                        <!-- Gradient Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-4 sm:p-6 text-white flex-shrink-0">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                        <FileText :size="20" class="text-white" />
                                    </div>
                                    <h3 class="text-xl sm:text-2xl font-bold" id="modal-title">
                                        Booking Details
                                    </h3>
                                </div>
                                <button @click="close"
                                    class="p-2 hover:bg-white/20 rounded-lg transition-all duration-200">
                                    <X :size="20" class="text-white" />
                                </button>
                            </div>

                            <!-- Status and Booking ID -->
                            <div class="flex flex-wrap items-center gap-3">
                                <span :class="getStatusBadgeClass(booking?.status)"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border backdrop-blur-sm">
                                    {{ booking?.status?.charAt(0).toUpperCase() + booking?.status?.slice(1) }}
                                </span>
                                <span class="text-sm opacity-90">Booking ID: #{{ booking?.id }}</span>
                                <span v-if="getCateringService()"
                                    class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium border border-orange-200">
                                    <ChefHat :size="14" class="mr-1" />
                                    Catering Service
                                </span>
                            </div>
                        </div>

                        <!-- Main Scrollable Content -->
                        <div class="p-4 sm:p-6 overflow-y-auto">
                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <!-- Client Information -->
                                    <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                                        <div class="flex items-center space-x-2 mb-4">
                                            <User :size="20" class="text-blue-600" />
                                            <h4 class="text-lg font-semibold text-gray-900">Client Information</h4>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Full
                                                    Name</label>
                                                <p class="text-gray-900 font-medium">{{ booking?.user?.client?.full_name
                                                    || 'N/A' }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Contact
                                                    Number</label>
                                                <p class="text-gray-900">{{ booking?.user?.client?.contact_number ||
                                                    'N/A' }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</label>
                                                <p class="text-gray-900">{{ booking?.user?.email || 'N/A' }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</label>
                                                <p class="text-gray-900">{{ booking?.user?.client?.location || 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Event Details -->
                                    <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                                        <div class="flex items-center space-x-2 mb-4">
                                            <Calendar :size="20" class="text-green-600" />
                                            <h4 class="text-lg font-semibold text-gray-900">Event Details</h4>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Event
                                                    Name</label>
                                                <p class="text-gray-900 font-medium">{{ booking?.event?.name || 'N/A' }}
                                                </p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Event
                                                    Date</label>
                                                <p class="text-gray-900">{{ formatDate(booking?.event?.event_date) }}
                                                </p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Event
                                                    Time</label>
                                                <p class="text-gray-900">{{ formatTime(booking?.event?.event_time) }}
                                                </p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</label>
                                                <p class="text-gray-900">{{ booking?.event?.location || 'N/A' }}</p>
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
                                    <!-- Booking & Catering Information -->
                                    <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                                        <div class="flex items-center space-x-2 mb-4">
                                            <Package :size="20" class="text-purple-600" />
                                            <h4 class="text-lg font-semibold text-gray-900">Booking Information</h4>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Service</label>
                                                <p class="text-gray-900 font-medium">{{ booking?.service?.name || 'N/A'
                                                    }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Booking
                                                    Date</label>
                                                <p class="text-gray-900">{{ formatDate(booking?.booking_date) }}</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label
                                                    class="text-xs font-medium text-gray-500 uppercase tracking-wide">Created
                                                    At</label>
                                                <p class="text-gray-900">{{ formatDate(booking?.created_at) }}</p>
                                            </div>

                                            <!-- Catering Specific Information -->
                                            <template v-if="getCateringService()">
                                                <div v-if="booking?.pax" class="space-y-1">
                                                    <label
                                                        class="text-xs font-medium text-gray-500 uppercase tracking-wide">Number
                                                        of Guests</label>
                                                    <p class="text-gray-900 font-medium">{{ booking.pax }} people</p>
                                                </div>
                                                <div v-if="getCateringService()?.buffet_type" class="space-y-1">
                                                    <label
                                                        class="text-xs font-medium text-gray-500 uppercase tracking-wide">Buffet
                                                        Type</label>
                                                    <p class="text-gray-900 capitalize">{{
                                                        getCateringService().buffet_type }}</p>
                                                </div>
                                                <div v-if="getCateringService()?.min_pax && getCateringService()?.max_pax"
                                                    class="space-y-1">
                                                    <label
                                                        class="text-xs font-medium text-gray-500 uppercase tracking-wide">Capacity
                                                        Range</label>
                                                    <p class="text-gray-900">{{ getCateringService().min_pax }}-{{
                                                        getCateringService().max_pax }} pax</p>
                                                </div>
                                                <div v-if="getCateringService()?.lead_time_days" class="space-y-1">
                                                    <label
                                                        class="text-xs font-medium text-gray-500 uppercase tracking-wide">Lead
                                                        Time</label>
                                                    <p class="text-gray-900">{{ getCateringService().lead_time_days }}
                                                        days</p>
                                                </div>
                                                <div v-if="getCateringService()?.is_customizable" class="space-y-1">
                                                    <label
                                                        class="text-xs font-medium text-gray-500 uppercase tracking-wide">Customization</label>
                                                    <p class="text-green-600 font-medium">Available</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Catering Dishes -->
                                    <div v-if="booking?.catering_dishes && booking.catering_dishes.length > 0"
                                        class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center space-x-2">
                                                <Utensils :size="20" class="text-orange-600" />
                                                <h4 class="text-lg font-semibold text-gray-900">Selected Dishes</h4>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ booking.catering_dishes.length }}
                                                items</span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div v-for="(dish, index) in booking.catering_dishes" :key="index"
                                                class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                                <p class="text-sm font-medium text-gray-900 mb-1">{{ dish.name || dish
                                                    }}</p>
                                                <p v-if="dish.description" class="text-xs text-gray-600 mb-1">{{
                                                    dish.description }}</p>
                                                <p v-if="dish.price" class="text-sm font-medium text-orange-600">
                                                    {{ formatCurrency(dish.price) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="booking.catering_dishes.length > 0"
                                            class="mt-4 pt-3 border-t border-gray-200">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">Dishes Subtotal</span>
                                                <span class="text-sm font-semibold text-gray-900">
                                                    {{ formatCurrency(calculateTotalDishPrice()) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pricing Summary -->
                                    <div v-if="getCateringService()"
                                        class="bg-blue-50 rounded-lg p-5 border border-blue-200">
                                        <div class="flex items-center space-x-2 mb-4">
                                            <CreditCard :size="20" class="text-blue-600" />
                                            <h4 class="text-lg font-semibold text-blue-900">Pricing Summary</h4>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-700">Service Price</span>
                                                <span class="text-sm font-medium text-gray-900">
                                                    {{ formatCurrency(booking?.service?.price) }} × {{ booking?.pax || 1
                                                    }}
                                                </span>
                                            </div>
                                            <div v-if="calculateTotalDishPrice() > 0"
                                                class="flex justify-between items-center">
                                                <span class="text-sm text-gray-700">Additional Dishes</span>
                                                <span class="text-sm font-medium text-gray-900">
                                                    {{ formatCurrency(calculateTotalDishPrice()) }}
                                                </span>
                                            </div>
                                            <div v-if="getCateringService()?.delivery_fee"
                                                class="flex justify-between items-center">
                                                <span class="text-sm text-gray-700">Delivery Fee</span>
                                                <span class="text-sm font-medium text-gray-900">
                                                    {{ formatCurrency(getCateringService().delivery_fee) }}
                                                </span>
                                            </div>
                                            <div
                                                class="flex justify-between items-center pt-3 border-t border-blue-200">
                                                <span class="text-base font-semibold text-blue-900">Estimated
                                                    Total</span>
                                                <span class="text-lg font-bold text-blue-900">
                                                    {{ formatCurrency(calculateTotalPrice()) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                            <button v-if="booking?.status === 'pending'"
                                                class="flex items-center justify-center space-x-2 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                                <CheckCircle :size="16" />
                                                <span>Confirm</span>
                                            </button>
                                            <button v-if="booking?.status === 'confirmed'"
                                                class="flex items-center justify-center space-x-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                                <CheckCircle :size="16" />
                                                <span>Complete</span>
                                            </button>
                                            <button
                                                class="flex items-center justify-center space-x-2 px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium">
                                                <MessageCircle :size="16" />
                                                <span>Contact</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 px-4 sm:px-6 py-4 border-t border-gray-200 flex-shrink-0">
                            <div class="flex justify-end">
                                <button @click="close"
                                    class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
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

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}
</style>
