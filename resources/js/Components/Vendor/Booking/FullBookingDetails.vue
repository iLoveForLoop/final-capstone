<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
    X, User, Calendar, Utensils,
    Users, CreditCard, Package
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

const formatDate = (date) => {
    if (!date) return 'N/A'
    try {
        return new Date(date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
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
        minimumFractionDigits: 2
    }).format(amount)
}

const getServicePriceRange = () => {
    const price = booking.value?.service?.price
    const maxPrice = booking.value?.service?.max_price

    if (!price) return 'N/A'

    if (maxPrice && maxPrice > price) {
        return `${formatCurrency(price)} - ${formatCurrency(maxPrice)}`
    }

    return formatCurrency(price)
}

const isCateringService = () => {
    return booking.value?.category?.name === 'Catering'
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

const calculateTotalPrice = () => {
    const basePrice = parseFloat(booking.value?.service?.price) || 0
    const pax = booking.value?.pax || 1
    return basePrice * pax
}

const handleEscape = (event) => {
    if (event.key === 'Escape' && isOpen.value) {
        close()
    }
}

onMounted(() => {
    window.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleEscape)
})
</script>

<template>
    <Transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50" aria-labelledby="modal-title"
            role="dialog" aria-modal="true" @click="close">

            <div class="flex items-center justify-center min-h-screen p-4" @click.stop>
                <Transition name="scale">
                    <div
                        class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl mx-auto max-h-[92vh] overflow-hidden flex flex-col">

                        <!-- Header -->
                        <div class="px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                        Booking Details
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-0.5">ID: #{{ booking?.id || 'N/A' }}</p>
                                </div>
                                <button @click="close"
                                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    <X :size="20" />
                                </button>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="flex-1 overflow-y-auto">
                            <div class="p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                                    <!-- Left Column -->
                                    <div class="space-y-6">
                                        <!-- Client Information -->
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                <User :size="16" class="mr-2 text-gray-600" />
                                                Client Information
                                            </h4>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="text-xs text-gray-500">Name</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.user?.client?.full_name || booking?.client || 'N/A'
                                                        }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Contact Number</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.user?.client?.contact_number || 'N/A' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Email</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.user?.email || booking?.client_email || 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Event Details -->
                                        <div class="pt-6 border-t border-gray-200">
                                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                <Calendar :size="16" class="mr-2 text-gray-600" />
                                                Event Details
                                            </h4>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="text-xs text-gray-500">Date</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ formatDate(booking?.event?.event_date || booking?.event_date)
                                                        }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Time</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.time || formatTime(booking?.event?.event_time) }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Venue</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.event?.location || booking?.event_location || 'N/A'
                                                        }}
                                                    </p>
                                                </div>
                                                <div v-if="booking?.event?.description || booking?.notes">
                                                    <label class="text-xs text-gray-500">Notes</label>
                                                    <p class="text-sm text-gray-700 mt-1 bg-gray-50 p-3 rounded">
                                                        {{ booking?.event?.description || booking?.notes }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="space-y-6">
                                        <!-- Service Information -->
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                <Package :size="16" class="mr-2 text-gray-600" />
                                                Service Information
                                            </h4>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="text-xs text-gray-500">Service</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.service?.name || 'N/A' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Category</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.category?.name || 'N/A' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Service Price</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ getServicePriceRange() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Catering Specific -->
                                        <template v-if="isCateringService()">
                                            <div v-if="booking?.pax" class="pt-6 border-t border-gray-200">
                                                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                    <Users :size="16" class="mr-2 text-gray-600" />
                                                    Guest Count
                                                </h4>
                                                <p class="text-2xl font-semibold text-gray-900">{{ booking.pax }} <span
                                                        class="text-sm font-normal text-gray-500">guests</span></p>
                                            </div>

                                            <div v-if="Object.keys(getCateringDishesByCategory()).length > 0"
                                                class="pt-6 border-t border-gray-200">
                                                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                    <Utensils :size="16" class="mr-2 text-gray-600" />
                                                    Menu Selection
                                                </h4>
                                                <div class="space-y-4">
                                                    <div v-for="(dishes, category) in getCateringDishesByCategory()"
                                                        :key="category">
                                                        <p class="text-xs font-medium text-gray-700 mb-2">{{ category }}
                                                        </p>
                                                        <div class="flex flex-wrap gap-2">
                                                            <span v-for="(dishName, index) in dishes" :key="index"
                                                                class="inline-block px-2.5 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                                                {{ dishName }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Payment Details -->
                                        <div class="pt-6 border-t border-gray-200">
                                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                <CreditCard :size="16" class="mr-2 text-gray-600" />
                                                Payment Details
                                            </h4>

                                            <template v-if="isCateringService() && booking?.pax">
                                                <div class="space-y-2">
                                                    <div class="flex justify-between text-sm">
                                                        <span class="text-gray-600">Price per person</span>
                                                        <span class="text-gray-900">{{
                                                            formatCurrency(booking?.service?.price) }}</span>
                                                    </div>
                                                    <div class="flex justify-between text-sm">
                                                        <span class="text-gray-600">Number of guests</span>
                                                        <span class="text-gray-900">× {{ booking?.pax }}</span>
                                                    </div>
                                                    <div
                                                        class="pt-2 mt-2 border-t border-gray-200 flex justify-between">
                                                        <span class="text-sm font-semibold text-gray-900">Total
                                                            Amount</span>
                                                        <span class="text-lg font-semibold text-gray-900">
                                                            {{ formatCurrency(calculateTotalPrice()) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <p class="text-lg font-semibold text-gray-900">
                                                    {{ formatCurrency(booking?.service?.price || booking?.raw_amount) }}
                                                </p>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <div class="flex justify-end">
                                <button @click="close"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
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
    transform: scale(0.98);
}

::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f9fafb;
}

::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>
