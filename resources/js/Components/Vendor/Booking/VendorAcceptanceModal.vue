<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
    X, CheckCircle, MessageCircle, Phone, Mail, Calendar,
    MapPin, User, Clock, AlertCircle, Sparkles
} from 'lucide-vue-next'

import { chatClient } from '@/utils/chatClient';

const isOpen = ref(false)
const booking = ref(null)
const showConfetti = ref(false)

const open = (bookingData = {}) => {
    console.log('Inside: ', bookingData)
    booking.value = bookingData
    isOpen.value = true
    showConfetti.value = true
    // Auto-hide confetti after animation
    setTimeout(() => {
        showConfetti.value = false
    }, 3000)
}

const close = () => {
    isOpen.value = false
}

defineExpose({
    open,
    close
})

const emit = defineEmits(['contact-client', 'view-booking-details'])

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

const contactClient = () => {
    emit('contact-client', booking.value)
}

const viewBookingDetails = () => {
    emit('view-booking-details', booking.value)
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
                        class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-4xl mx-auto overflow-hidden">

                        <!-- Header with Success Animation -->
                        <div
                            class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-8 text-center relative overflow-hidden">
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
                                        <CheckCircle :size="32" class="text-green-600" />
                                    </div>
                                </div>

                                <h2 class="text-2xl font-bold text-white mb-2">Booking Accepted!</h2>
                                <p class="text-green-100">You've successfully accepted this booking</p>
                            </div>
                        </div>

                        <!-- Main Content - Now in 2 columns -->
                        <div class="p-6">
                            <!-- Important Notice -->
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                                <div class="flex items-start space-x-3">
                                    <AlertCircle :size="20" class="text-amber-600 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <h3 class="font-semibold text-amber-800 mb-1">Important Next Step</h3>
                                        <p class="text-amber-700 text-sm">
                                            Please contact the client as soon as possible to confirm details and discuss
                                            their requirements.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <!-- Client Information -->
                                    <div class="mb-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <User :size="20" class="text-indigo-600 mr-2" />
                                            Client to Contact
                                        </h3>

                                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="font-medium text-gray-900">{{
                                                        booking?.user?.client?.full_name ||
                                                        booking?.client || 'N/A' }}</p>
                                                    <p class="text-sm text-gray-500">Customer Name</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-medium text-gray-900">#{{ booking?.id || 'N/A' }}</p>
                                                    <p class="text-sm text-gray-500">Booking ID</p>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 gap-2 pt-2 border-t border-gray-200">
                                                <div class="flex items-center space-x-2">
                                                    <Phone :size="16" class="text-gray-400" />
                                                    <span class="text-sm text-gray-700">{{
                                                        booking?.user?.client?.contact_number
                                                        || 'N/A' }}</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <Mail :size="16" class="text-gray-400" />
                                                    <span class="text-sm text-gray-700 truncate">{{ booking?.user?.email
                                                        ||
                                                        booking?.client_email || 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Event Details -->
                                    <div class="mb-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <Calendar :size="20" class="text-indigo-600 mr-2" />
                                            Event Details
                                        </h3>

                                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                            <div class="flex items-center space-x-2">
                                                <Calendar :size="16" class="text-gray-400" />
                                                <span class="text-sm text-gray-700">{{
                                                    formatDate(booking?.event?.event_date ||
                                                        booking?.event_date) }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <Clock :size="16" class="text-gray-400" />
                                                <span class="text-sm text-gray-700">{{ booking?.time ||
                                                    formatTime(booking?.event?.event_time) }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <MapPin :size="16" class="text-gray-400" />
                                                <span class="text-sm text-gray-700">{{ booking?.event?.location ||
                                                    booking?.event_location || 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-6">
                                    <!-- Service Information -->
                                    <div class="mb-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Service Booked</h3>
                                        <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
                                            <p class="font-medium text-indigo-900">{{ booking?.service?.name || 'N/A' }}
                                            </p>
                                            <p class="text-sm text-indigo-700">{{ booking?.category?.name || 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex flex-col space-y-3">
                                        {{ console.log('id', booking.user.id) }}
                                        <button @click="chatClient(booking.user.id)"
                                            class="flex items-center justify-center space-x-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-md hover:shadow-lg">
                                            <MessageCircle :size="20" />
                                            <span>Chat Client Now</span>
                                        </button>

                                        <button @click="viewBookingDetails"
                                            class="flex items-center justify-center space-x-2 bg-white hover:bg-gray-50 text-gray-700 font-medium py-3 px-4 rounded-xl border border-gray-300 transition-all duration-200 hover:shadow-md">
                                            <span>View Full Booking Details</span>
                                        </button>

                                        <button @click="close"
                                            class="text-gray-500 hover:text-gray-700 font-medium py-2 transition-colors duration-200">
                                            Close
                                        </button>
                                    </div>
                                </div>
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
