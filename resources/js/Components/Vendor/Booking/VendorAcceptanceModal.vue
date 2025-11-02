<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
    X, CheckCircle, MessageCircle, Phone, Mail, Calendar,
    MapPin, User, Clock, AlertCircle, Sparkles, Download
} from 'lucide-vue-next'
import { chatClient } from '@/utils/chatClient';
import html2canvas from 'html2canvas'
import FullBookingDetails from './FullBookingDetails.vue';

const isOpen = ref(false)
const booking = ref(null)
const showConfetti = ref(false)
const modalContent = ref(null)

const bookingDetailsModal = defineModel('bookingDetailsModal')

const open = async (bookingData = {}) => {
    console.log('Inside: ', bookingData)
    booking.value = bookingData
    isOpen.value = true
    showConfetti.value = true


    setTimeout(() => {
        showConfetti.value = false
    }, 3000)


    setTimeout(() => {
        autoSaveImage()
    }, 1000)
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



// Generate printable content for image export
const generatePrintableContent = () => {
    return `
        <div class="printable-content" style="width: 794px; min-height: 1123px; background: white; padding: 40px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 12px; line-height: 1.4; color: #374151;">
            <div style="max-width: 714px; margin: 0 auto;">
                <!-- Header -->
                <div style="background: linear-gradient(to right, #10b981, #059669); padding: 32px; border-radius: 12px 12px 0 0; margin-bottom: 32px; text-align: center; color: white;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; margin-bottom: 16px;">
                        <div style="width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                    </div>
                    <h1 style="font-size: 28px; font-weight: 700; margin: 0 0 8px 0;">Booking Confirmed</h1>
                    <p style="font-size: 16px; margin: 0; opacity: 0.9;">Booking has been successfully accepted</p>
                </div>

                <!-- Important Notice -->
                <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div>
                            <h3 style="font-size: 14px; font-weight: 600; color: #92400e; margin: 0 0 4px 0;">Important Next Step</h3>
                            <p style="font-size: 13px; color: #92400e; margin: 0; line-height: 1.4;">
                                Please contact the client as soon as possible to confirm details and discuss their requirements.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                    <!-- Left Column -->
                    <div>
                        <!-- Client Information -->
                        <div style="margin-bottom: 32px;">
                            <div style="display: flex; align-items: center; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">
                                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Client Information</h2>
                            </div>
                            <div style="background: #f9fafb; border-radius: 8px; padding: 16px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                    <div>
                                        <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px;">Client Name</p>
                                        <p style="font-size: 16px; color: #111827; margin: 0; font-weight: 600;">${booking.value?.user?.client?.full_name || booking.value?.client || 'N/A'}</p>
                                    </div>
                                    <div style="text-align: right;">
                                        <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px;">Booking ID</p>
                                        <p style="font-size: 16px; color: #111827; margin: 0; font-weight: 600;">#${booking.value?.id || 'N/A'}</p>
                                    </div>
                                </div>
                                <div style="border-top: 1px solid #e5e7eb; padding-top: 12px;">
                                    <div style="margin-bottom: 8px;">
                                        <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px;">Contact Number</p>
                                        <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${booking.value?.user?.client?.contact_number || 'N/A'}</p>
                                    </div>
                                    <div>
                                        <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px;">Email Address</p>
                                        <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${booking.value?.user?.email || booking.value?.client_email || 'N/A'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div>
                            <div style="display: flex; align-items: center; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">
                                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Event Details</h2>
                            </div>
                            <div style="background: #f9fafb; border-radius: 8px; padding: 16px;">
                                <div style="margin-bottom: 12px;">
                                    <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px;">Event Date</p>
                                    <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${formatDate(booking.value?.event?.event_date || booking.value?.event_date)}</p>
                                </div>
                                <div style="margin-bottom: 12px;">
                                    <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px;">Event Time</p>
                                    <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${booking.value?.time || formatTime(booking.value?.event?.event_time)}</p>
                                </div>
                                <div>
                                    <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px;">Event Venue</p>
                                    <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${booking.value?.event?.location || booking.value?.event_location || 'N/A'}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <!-- Service Information -->
                        <div style="margin-bottom: 32px;">
                            <div style="display: flex; align-items: center; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">
                                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Service Information</h2>
                            </div>
                            <div style="background: #e0e7ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 20px; text-align: center;">
                                <p style="font-size: 18px; font-weight: 700; color: #3730a3; margin: 0 0 4px 0;">${booking.value?.service?.name || 'N/A'}</p>
                                <p style="font-size: 14px; color: #4f46e5; margin: 0; font-weight: 500;">${booking.value?.category?.name || 'N/A'}</p>
                            </div>
                        </div>

                        <!-- Confirmation Details -->
                        <div>
                            <div style="display: flex; align-items: center; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">
                                <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Confirmation Details</h2>
                            </div>
                            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px;">
                                <div style="text-align: center; margin-bottom: 16px;">
                                    <p style="font-size: 14px; color: #065f46; margin: 0 0 8px 0; font-weight: 600;">Confirmed On</p>
                                    <p style="font-size: 16px; color: #065f46; margin: 0; font-weight: 700;">${new Date().toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })}</p>
                                </div>
                                <div style="border-top: 1px solid #bbf7d0; padding-top: 16px;">
                                    <p style="font-size: 12px; color: #065f46; margin: 0; text-align: center; line-height: 1.4;">
                                        This booking has been officially confirmed and accepted by the vendor.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div style="margin-top: 48px; padding-top: 24px; border-top: 1px solid #e5e7eb; text-align: center;">
                    <p style="font-size: 11px; color: #9ca3af; margin: 0;">This is a system-generated booking confirmation document</p>
                </div>
            </div>
        </div>
    `
}

// Auto-save image when modal opens
const autoSaveImage = async () => {
    try {
        const printContent = generatePrintableContent()

        // Create a temporary div for image capture
        const printDiv = document.createElement('div')
        printDiv.innerHTML = printContent
        document.body.appendChild(printDiv)

        // Wait for fonts and styles to load
        await new Promise(resolve => setTimeout(resolve, 500))

        const canvas = await html2canvas(printDiv.firstElementChild, {
            backgroundColor: '#ffffff',
            scale: 2,
            useCORS: true,
            logging: false,
            width: 794,
            height: printDiv.firstElementChild.scrollHeight,
            windowWidth: 794,
            windowHeight: printDiv.firstElementChild.scrollHeight
        })

        document.body.removeChild(printDiv)

        const link = document.createElement('a')
        link.download = `booking-confirmation-${booking.value?.id || 'details'}-${new Date().getTime()}.png`
        link.href = canvas.toDataURL('image/png')
        link.click()

        console.log('Booking confirmation image auto-saved successfully')

    } catch (error) {
        console.error('Error auto-saving image:', error)
        // Don't show alert for auto-save to avoid interrupting user experience
    }
}

// Manual save image function
const saveImage = async () => {
    try {
        const saveButton = document.querySelector('[data-save-image]')
        const originalText = saveButton.innerHTML
        saveButton.innerHTML = 'Saving...'
        saveButton.disabled = true

        await autoSaveImage()

        saveButton.innerHTML = originalText
        saveButton.disabled = false

    } catch (error) {
        console.error('Error saving image:', error)
        alert('Failed to save image. Please try again.')

        const saveButton = document.querySelector('[data-save-image]')
        if (saveButton) {
            saveButton.innerHTML = 'Save Confirmation'
            saveButton.disabled = false
        }
    }
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

const fullBookingDetails = ref(null)



</script>

<template>
    <FullBookingDetails ref="fullBookingDetails" />
    <Transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 z-40 overflow-y-auto bg-gray-900/70 backdrop-blur-sm"
            aria-labelledby="modal-title" role="dialog" aria-modal="true" @click="close">

            <!-- Confetti Effect -->
            <div v-if="showConfetti" class="confetti-container fixed inset-0 pointer-events-none z-40">
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
                    <div ref="modalContent"
                        class="relative bg-white rounded-xl shadow-xs border border-gray-200 w-full max-w-4xl mx-auto max-h-[90vh] overflow-hidden flex flex-col">

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

                        <!-- Content Area -->
                        <div class="flex-1 overflow-y-auto">
                            <div class="p-6">
                                <!-- Important Notice -->
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                                    <div class="flex items-start space-x-3">
                                        <AlertCircle :size="20" class="text-amber-600 flex-shrink-0 mt-0.5" />
                                        <div>
                                            <h3 class="font-semibold text-amber-800 mb-1">Important Next Step</h3>
                                            <p class="text-amber-700 text-sm">
                                                Please contact the client as soon as possible to confirm details and
                                                discuss
                                                their requirements.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                                                        <p class="font-medium text-gray-900">#{{ booking?.id || 'N/A' }}
                                                        </p>
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
                                                        <span class="text-sm text-gray-700 truncate">{{
                                                            booking?.user?.email
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
                                                <p class="font-medium text-indigo-900">{{ booking?.service?.name ||
                                                    'N/A' }}
                                                </p>
                                                <p class="text-sm text-indigo-700">{{ booking?.category?.name || 'N/A'
                                                    }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex flex-col space-y-3">
                                            <button @click="chatClient(booking.user.id)"
                                                class="flex items-center justify-center space-x-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] shadow-md hover:shadow-lg">
                                                <MessageCircle :size="20" />
                                                <span>Chat Client Now</span>
                                            </button>

                                            <button @click="fullBookingDetails.open(booking)"
                                                class="flex items-center justify-center space-x-2 bg-white hover:bg-gray-50 text-gray-700 font-medium py-3 px-4 rounded-xl border border-gray-300 transition-all duration-200 hover:shadow-md">
                                                <span>View Full Booking Details</span>
                                            </button>

                                            <button @click="saveImage" data-save-image
                                                class="flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 hover:shadow-md">
                                                <Download :size="20" />
                                                <span>Save Confirmation</span>
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

/* Scrollbar styling */
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
