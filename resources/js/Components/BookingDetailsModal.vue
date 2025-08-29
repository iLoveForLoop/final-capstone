<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
    X, FileText, User, Calendar, Utensils,
    CheckCircle, MessageCircle, Package, CreditCard,
    MapPin, Mail, Phone, Clock, Users, ChefHat
} from 'lucide-vue-next'

const isOpen = ref(false)
const booking = ref(null)

const open = (bookingData = {}) => {
    booking.value = bookingData
    isOpen.value = true
}

const close = () => {
    isOpen.value = false
}

defineExpose({
    open,
    close
})

const getStatusBadgeClass = (status) => {
    if (!status) return 'bg-gray-100 text-gray-800 border-gray-200'

    const classes = {
        pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        confirmed: 'bg-green-100 text-green-800 border-green-200',
        completed: 'bg-blue-100 text-blue-800 border-blue-200',
        cancelled: 'bg-red-100 text-red-800 border-red-200'
    }
    return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200'
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    try {
        return new Date(date).toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })
    } catch (e) {
        return 'Invalid Date'
    }
}

const formatTime = (time) => {
    if (!time) return 'N/A'
    try {
        return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        })
    } catch (e) {
        return 'Invalid Time'
    }
}

const formatCurrency = (amount) => {
    if (!amount && amount !== 0) return '₱0'
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0
    }).format(amount)
}

const isCateringService = () => {
    return booking.value?.category?.name === 'Catering'
}

const getCateringDishes = () => {
    if (!booking.value?.selected_catering_dishes) return []

    let dishes = booking.value.selected_catering_dishes
    if (typeof dishes === 'string') {
        try {
            dishes = JSON.parse(dishes)
        } catch (e) {
            console.error('Error parsing catering dishes:', e)
            return []
        }
    }

    if (typeof dishes === 'object' && !Array.isArray(dishes)) {
        const result = []
        Object.entries(dishes).forEach(([category, dishArray]) => {
            if (Array.isArray(dishArray)) {
                dishArray.forEach(dishName => {
                    result.push({
                        category,
                        name: dishName,
                        price: 0
                    })
                })
            } else {
                result.push({
                    category,
                    name: dishArray,
                    price: 0
                })
            }
        })
        return result
    }

    if (Array.isArray(dishes)) {
        return dishes
    }

    return []
}

const getCateringDishesByCategory = () => {
    if (!booking.value?.selected_catering_dishes) return {}

    let dishes = booking.value.selected_catering_dishes
    if (typeof dishes === 'string') {
        try {
            dishes = JSON.parse(dishes)
        } catch (e) {
            console.error('Error parsing catering dishes:', e)
            return {}
        }
    }

    if (typeof dishes === 'object' && !Array.isArray(dishes)) {
        const result = {}
        Object.entries(dishes).forEach(([category, dishData]) => {
            result[category] = Array.isArray(dishData) ? dishData : [dishData]
        })
        return result
    }

    return {}
}

const calculateTotalDishPrice = () => {
    const dishes = getCateringDishes()
    return dishes.reduce((total, dish) => {
        return total + (parseFloat(dish.price) || 0)
    }, 0)
}

const calculateTotalPrice = () => {
    const basePrice = parseFloat(booking.value?.service?.price) || 0
    const dishTotal = calculateTotalDishPrice()
    const pax = booking.value?.pax || 1
    return (basePrice * pax) + dishTotal
}

// Handle escape key to close modal
const handleEscape = (event) => {
    if (event.key === 'Escape' && isOpen.value) {
        close()
    }
}

// Add event listener for escape key
onMounted(() => {
    window.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleEscape)
})
</script>

<template>
    <Transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/70 backdrop-blur-sm"
            aria-labelledby="modal-title" role="dialog" aria-modal="true" @click="close">

            <div class="flex items-center justify-center min-h-screen p-4" @click.stop>
                <Transition name="scale">
                    <div
                        class="relative bg-white rounded-xl shadow-xs border border-gray-200 w-full max-w-4xl mx-auto max-h-[90vh] overflow-hidden flex flex-col">

                        <!-- Header -->
                        <div class="bg-white px-6 py-4 border-b border-gray-200 flex-shrink-0">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-indigo-50 rounded-lg">
                                        <FileText :size="20" class="text-indigo-600" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                            Booking Details
                                        </h3>
                                        <p class="text-sm text-gray-500">Booking ID: #{{ booking?.id || 'N/A' }}</p>
                                    </div>
                                </div>
                                <button @click="close"
                                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    <X :size="20" />
                                </button>
                            </div>

                            <!-- Status Badge -->
                            <div class="mt-4">
                                <span :class="getStatusBadgeClass(booking?.status)"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border capitalize">
                                    {{ booking?.status || 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="flex-1 overflow-y-auto">
                            <div class="p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                                    <!-- Left Column - Client & Event Info -->
                                    <div class="space-y-6">
                                        <!-- Client Information Card -->
                                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                                            <div class="flex items-center space-x-3 mb-4">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                                        <User :size="24" class="text-indigo-600" />
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="text-lg font-semibold text-gray-900">Client Information
                                                    </h4>
                                                    <p class="text-sm text-gray-500">Customer details</p>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 gap-3">
                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <User :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate">{{
                                                            booking?.user?.client?.full_name || booking?.client || 'N/A'
                                                        }}</p>
                                                        <p class="text-xs text-gray-500">Full Name</p>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <Phone :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-900">{{
                                                            booking?.user?.client?.contact_number || 'N/A' }}</p>
                                                        <p class="text-xs text-gray-500">Contact Number</p>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <Mail :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate">{{
                                                            booking?.user?.email || booking?.client_email || 'N/A' }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">Email Address</p>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <MapPin :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-900">{{
                                                            booking?.user?.client?.location || 'N/A' }}</p>
                                                        <p class="text-xs text-gray-500">Location</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Event Information Card -->
                                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                                            <div class="flex items-center space-x-3 mb-4">
                                                <Calendar :size="20" class="text-green-600" />
                                                <div>
                                                    <h4 class="text-lg font-semibold text-gray-900">Event Details</h4>
                                                    <p class="text-sm text-gray-500">Booking schedule & location</p>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 gap-3">
                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <Calendar :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{
                                                            formatDate(booking?.event?.event_date ||
                                                                booking?.event_date) }}</p>
                                                        <p class="text-xs text-gray-500">Event Date</p>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <Clock :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ booking?.time ||
                                                            formatTime(booking?.event?.event_time) }}</p>
                                                        <p class="text-xs text-gray-500">Event Time</p>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <MapPin :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-900">{{
                                                            booking?.event?.location || booking?.event_location || 'N/A'
                                                        }}</p>
                                                        <p class="text-xs text-gray-500">Event Location</p>
                                                    </div>
                                                </div>

                                                <div v-if="booking?.event?.name || booking?.event_name"
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <FileText :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-900">{{
                                                            booking?.event?.name || booking?.event_name }}</p>
                                                        <p class="text-xs text-gray-500">Event Name</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div v-if="booking?.event?.description || booking?.notes"
                                                class="mt-4 p-3 bg-white rounded-lg border border-gray-200">
                                                <p class="text-sm text-gray-900 leading-relaxed">{{
                                                    booking?.event?.description || booking?.notes }}</p>
                                                <p class="text-xs text-gray-500 mt-1">Additional Notes</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column - Booking & Service Info -->
                                    <div class="space-y-6">
                                        <!-- Service Information Card -->
                                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                                            <div class="flex items-center space-x-3 mb-4">
                                                <Package :size="20" class="text-purple-600" />
                                                <div>
                                                    <h4 class="text-lg font-semibold text-gray-900">Service Information
                                                    </h4>
                                                    <p class="text-sm text-gray-500">Booking details & pricing</p>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 gap-3">
                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <Package :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-900">{{
                                                            booking?.service?.name || 'N/A' }}</p>
                                                        <p class="text-xs text-gray-500">Service</p>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <FileText :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-medium text-gray-900">{{
                                                            booking?.category?.name || 'N/A' }}</p>
                                                        <p class="text-xs text-gray-500">Category</p>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <Calendar :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{
                                                            formatDate(booking?.date || booking?.booking_date) }}</p>
                                                        <p class="text-xs text-gray-500">Booking Date</p>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                                    <Clock :size="16" class="text-gray-400 flex-shrink-0" />
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{
                                                            formatDate(booking?.created_at) }}</p>
                                                        <p class="text-xs text-gray-500">Created At</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Catering Specific Information -->
                                        <template v-if="isCateringService()">
                                            <!-- Guest Count -->
                                            <div v-if="booking?.pax"
                                                class="bg-blue-50 rounded-lg p-5 border border-blue-200">
                                                <div class="flex items-center space-x-3 mb-3">
                                                    <Users :size="20" class="text-blue-600" />
                                                    <div>
                                                        <h4 class="text-lg font-semibold text-blue-900">Guest Count</h4>
                                                        <p class="text-sm text-blue-600">Number of attendees</p>
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <span class="text-3xl font-bold text-blue-900">{{ booking.pax
                                                    }}</span>
                                                    <p class="text-sm text-blue-700 mt-1">people</p>
                                                </div>
                                            </div>

                                            <!-- Catering Dishes -->
                                            <div v-if="getCateringDishes().length > 0"
                                                class="bg-orange-50 rounded-lg p-5 border border-orange-200">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center space-x-3">
                                                        <Utensils :size="20" class="text-orange-600" />
                                                        <div>
                                                            <h4 class="text-lg font-semibold text-orange-900">Selected
                                                                Dishes</h4>
                                                            <p class="text-sm text-orange-600">{{
                                                                getCateringDishes().length }} dishes</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="space-y-3">
                                                    <div v-for="(dishes, category) in getCateringDishesByCategory()"
                                                        :key="category"
                                                        class="bg-white rounded-lg p-3 border border-orange-100">
                                                        <h5 class="text-sm font-medium text-orange-800 mb-2">{{ category
                                                        }}</h5>
                                                        <div class="flex flex-wrap gap-2">
                                                            <span v-for="(dishName, index) in dishes" :key="index"
                                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                                                {{ dishName }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pricing Summary -->
                                            <div class="bg-green-50 rounded-lg p-5 border border-green-200">
                                                <div class="flex items-center space-x-3 mb-4">
                                                    <CreditCard :size="20" class="text-green-600" />
                                                    <div>
                                                        <h4 class="text-lg font-semibold text-green-900">Pricing Summary
                                                        </h4>
                                                        <p class="text-sm text-green-600">Breakdown of charges</p>
                                                    </div>
                                                </div>

                                                <div class="space-y-2">
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-sm text-gray-700">Service Price</span>
                                                        <span class="text-sm font-medium text-gray-900">
                                                            {{ formatCurrency(booking?.raw_amount ||
                                                                booking?.service?.price) }}
                                                            <span v-if="booking?.is_per_pax"> × {{ booking?.pax || 1
                                                            }}</span>
                                                        </span>
                                                    </div>

                                                    <div v-if="calculateTotalDishPrice() > 0"
                                                        class="flex justify-between items-center">
                                                        <span class="text-sm text-gray-700">Additional Dishes</span>
                                                        <span class="text-sm font-medium text-gray-900">
                                                            {{ formatCurrency(calculateTotalDishPrice()) }}
                                                        </span>
                                                    </div>

                                                    <div
                                                        class="flex justify-between items-center pt-3 border-t border-green-200">
                                                        <span class="text-base font-semibold text-green-900">Total
                                                            Amount</span>
                                                        <span class="text-lg font-bold text-green-900">
                                                            {{ formatCurrency(calculateTotalPrice()) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Non-Catering Price Display -->
                                        <div v-else class="bg-green-50 rounded-lg p-5 border border-green-200">
                                            <div class="flex items-center space-x-3 mb-4">
                                                <CreditCard :size="20" class="text-green-600" />
                                                <div>
                                                    <h4 class="text-lg font-semibold text-green-900">Service Price</h4>
                                                    <p class="text-sm text-green-600">Total booking amount</p>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <span class="text-2xl font-bold text-green-900">
                                                    {{ booking?.price || formatCurrency(booking?.raw_amount ||
                                                        booking?.service?.price) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer with Actions -->
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex-shrink-0">
                            <div class="flex justify-end space-x-3">
                                <button @click="close"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">
                                    Close
                                </button>
                                <button v-if="booking?.status === 'pending'"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm">
                                    Accept Booking
                                </button>
                                <button v-if="booking?.status === 'confirmed'"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                                    Mark as Complete
                                </button>
                                <button
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm flex items-center space-x-2">
                                    <MessageCircle :size="16" />
                                    <span>Contact Client</span>
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
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.scale-enter-active,
.scale-leave-active {
    transition: all 0.2s ease;
}

.scale-enter-from,
.scale-leave-to {
    opacity: 0;
    transform: scale(0.95);
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
</style>
