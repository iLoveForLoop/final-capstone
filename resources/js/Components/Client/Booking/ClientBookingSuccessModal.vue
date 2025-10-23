<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import {
    X, CheckCircle, MessageCircle, Calendar, MapPin,
    Clock, Users, FileText, Sparkles, Mail, Phone, User
} from 'lucide-vue-next'

const isOpen = ref(false)
const bookingData = ref(null)

const open = (data = {}) => {
    document.body.classList.add("overflow-hidden");
    bookingData.value = data
    isOpen.value = true
    showConfetti.value = true

    // Auto-hide confetti after animation
    setTimeout(() => {
        showConfetti.value = false
    }, 3000)
}

const close = () => {
    document.body.classList.remove("overflow-hidden");   isOpen.value = false
}

defineExpose({
    open,
    close
})

const emit = defineEmits(['contact-vendors', 'view-bookings'])

const showConfetti = ref(false)

// Computed properties for vendor data
const vendors = computed(() => {
    return bookingData.value?.services?.map(service => service.vendor) || []
})

const uniqueVendors = computed(() => {
    const seen = new Set()
    return vendors.value.filter(vendor => {
        if (!vendor || seen.has(vendor.id)) return false
        seen.add(vendor.id)
        return true
    })
})

const servicesCount = computed(() => {
    return bookingData.value?.services?.length || 0
})

const totalPrice = computed(() => {
    if (!bookingData.value?.services) return 0
    return bookingData.value.services.reduce((sum, service) => {
        return sum + parseFloat(service.price || 0)
    }, 0)
})

// Format date
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

// Format time
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

// Format currency
const formatCurrency = (amount) => {
    if (!amount && amount !== 0) return '₱0'
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0
    }).format(amount)
}

const contactVendors = () => {
    emit('contact-vendors', bookingData.value)
}

const viewBookings = () => {
    emit('view-bookings', bookingData.value)
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

            <!-- Confetti Effect -->
            <div v-if="showConfetti" class="confetti-container fixed inset-0 pointer-events-none z-50">
                <div class="confetti" v-for="n in 50" :key="n" :style="{
                    left: `${Math.random() * 100}%`,
                    animationDelay: `${Math.random() * 3}s`,
                    animationDuration: `${1 + Math.random() * 2}s`
                }">
                    <Sparkles :size="16" class="text-yellow-400" />
                </div>
            </div>

            <div class="flex items-center justify-center min-h-screen p-4" @click.stop>
                <Transition name="bounce">
                    <div
                        class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-2xl mx-auto overflow-hidden">

                        <!-- Header with Success Animation -->
                        <div
                            class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-8 text-center relative overflow-hidden">
                            <!-- Animated Background Elements -->
                            <div class="absolute inset-0 opacity-10">
                                <div
                                    class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full -translate-x-1/2 -translate-y-1/2">
                                </div>
                                <div
                                    class="absolute bottom-0 right-0 w-40 h-40 bg-white rounded-full translate-x-1/2 translate-y-1/2">
                                </div>
                            </div>

                            <!-- Success Icon with Animation -->
                            <div class="relative z-10">
                                <div
                                    class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4">
                                    <div
                                        class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg animate-pulse-slow">
                                        <CheckCircle :size="32" class="text-blue-600" />
                                    </div>
                                </div>

                                <h2 class="text-2xl font-bold text-white mb-2">Booking Request Sent!</h2>
                                <p class="text-blue-100">Your booking request has been submitted successfully</p>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="p-6">
                            <!-- Important Notice -->
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                                <div class="flex items-start space-x-3">
                                    <MessageCircle :size="20" class="text-blue-600 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <h3 class="font-semibold text-blue-800 mb-1">What's Next?</h3>
                                        <p class="text-blue-700 text-sm">
                                            Vendors will review your request and contact you within 24 hours. You can
                                            also reach out to them directly if needed.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Summary -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Event Details -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                        <Calendar :size="18" class="text-gray-600 mr-2" />
                                        Event Details
                                    </h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center space-x-2">
                                            <Calendar :size="14" class="text-gray-400" />
                                            <span class="text-gray-700">{{ formatDate(bookingData?.event_date) }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <Clock :size="14" class="text-gray-400" />
                                            <span class="text-gray-700">{{ formatTime(bookingData?.event_time) }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <MapPin :size="14" class="text-gray-400" />
                                            <span class="text-gray-700">{{ bookingData?.location || 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Booking Summary -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                        <FileText :size="18" class="text-gray-600 mr-2" />
                                        Booking Summary
                                    </h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Services:</span>
                                            <span class="font-medium text-gray-900">{{ servicesCount }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Vendors:</span>
                                            <span class="font-medium text-gray-900">{{ uniqueVendors.length }}</span>
                                        </div>
                                        <div class="flex justify-between pt-2 border-t border-gray-200">
                                            <span class="text-gray-600">Total:</span>
                                            <span class="font-semibold text-green-600">{{ formatCurrency(totalPrice)
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Vendors List -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <Users :size="18" class="text-gray-600 mr-2" />
                                    Vendors Contacted
                                </h3>
                                <div class="space-y-3">
                                    <div v-for="vendor in uniqueVendors" :key="vendor.id"
                                        class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <User :size="16" class="text-indigo-600" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ vendor.business_name || vendor.full_name }}
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">
                                                {{ vendor.location }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <p class="text-xs text-gray-500">{{ vendor.contact_number }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Multiple Vendor Notice -->
                                <div v-if="uniqueVendors.length > 1"
                                    class="mt-3 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                    <p class="text-xs text-amber-700">
                                        Your request has been sent to {{ uniqueVendors.length }} vendors. Each vendor
                                        will respond separately about their services.
                                    </p>
                                </div>
                            </div>

                            <!-- Services Overview -->
                            <div v-if="servicesCount > 0" class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <FileText :size="18" class="text-gray-600 mr-2" />
                                    Services Booked
                                </h3>
                                <div class="space-y-2">
                                    <div v-for="service in bookingData.services" :key="service.id"
                                        class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">{{ service.name }}</p>
                                            <p class="text-xs text-gray-500">{{ service.category?.name }}</p>
                                        </div>
                                        <p class="text-sm font-semibold text-green-600 whitespace-nowrap ml-2">
                                            {{ formatCurrency(service.price) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col space-y-3">
                                <button @click="contactVendors"
                                    class="flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-md hover:shadow-lg">
                                    <MessageCircle :size="20" />
                                    <span>Contact Vendors</span>
                                </button>

                                <button @click="viewBookings"
                                    class="flex items-center justify-center space-x-2 bg-white hover:bg-gray-50 text-gray-700 font-medium py-3 px-4 rounded-xl border border-gray-300 transition-all duration-200 hover:shadow-md">
                                    <span>View My Bookings</span>
                                </button>

                                <button @click="close"
                                    class="text-gray-500 hover:text-gray-700 font-medium py-2 transition-colors duration-200">
                                    Close
                                </button>
                            </div>

                            <!-- Help Text -->
                            <div class="text-center mt-4 pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-500">
                                    Need help? Contact support at
                                    <a href="mailto:support@example.com"
                                        class="text-blue-600 hover:text-blue-800">support@example.com</a>
                                </p>
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
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.bounce-enter-active {
    animation: bounce-in 0.5s;
}

.bounce-leave-active {
    animation: bounce-in 0.5s reverse;
}

@keyframes bounce-in {
    0% {
        transform: scale(0.5);
        opacity: 0;
    }

    50% {
        transform: scale(1.05);
    }

    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.confetti-container {
    overflow: hidden;
}

.confetti {
    position: absolute;
    top: -20px;
    animation: fall linear forwards;
}

@keyframes fall {
    to {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0;
    }
}

.animate-pulse-slow {
    animation: pulse 2s infinite;
}

@keyframes pulse {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.05);
    }
}
</style>
