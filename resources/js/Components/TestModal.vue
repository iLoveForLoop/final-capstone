<script setup>
import { ref, reactive } from 'vue'

// Modal state
const isOpen = ref(false)
const isLoading = ref(false)

// Mock booking data
const bookingData = reactive({
    id: 'BK-2025-8901',
    status: 'pending',
    service: {
        name: 'Premium Wedding Photography Package',
        duration: '8 hours',
        price: 2500,
        currency: 'USD'
    },
    customer: {
        name: 'Sarah Johnson',
        email: 'sarah.johnson@email.com',
        phone: '+1 (555) 123-4567',
        avatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=150&h=150&fit=crop&crop=face',
        joinDate: '2023-05-15',
        totalBookings: 12,
        rating: 4.8
    },
    event: {
        title: 'Sarah & Michael Wedding',
        type: 'Wedding',
        date: '2025-09-15',
        time: '14:00',
        endTime: '22:00',
        location: {
            venue: 'Grand Ballroom Hotel',
            address: '123 Elegant Street, Downtown, NY 10001',
            coordinates: '40.7128, -74.0060'
        },
        guests: 150,
        notes: 'Outdoor ceremony followed by indoor reception. Would like some candid shots during the cocktail hour and formal family photos. Special request for drone shots if weather permits.'
    },
    booking: {
        requestedDate: '2025-08-20',
        totalAmount: 2500,
        deposit: 750,
        balance: 1750,
        specialRequests: [
            'Engagement photos session (1 hour before ceremony)',
            'Digital gallery delivery within 2 weeks',
            'USB drive with high-resolution images'
        ]
    }
})

// Modal methods
const open = () => {
    isOpen.value = true
}

const close = () => {
    isOpen.value = false
}

const handleAccept = async () => {
    isLoading.value = true
    try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1500))
        console.log('Booking accepted:', bookingData.id)
        close()
    } catch (error) {
        console.error('Error accepting booking:', error)
    } finally {
        isLoading.value = false
    }
}

const handleDecline = async () => {
    isLoading.value = true
    try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        console.log('Booking declined:', bookingData.id)
        close()
    } catch (error) {
        console.error('Error declining booking:', error)
    } finally {
        isLoading.value = false
    }
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: bookingData.service.currency
    }).format(amount)
}

// Expose methods
defineExpose({
    open,
    close
})
</script>

<template>
    <!-- Modal Overlay -->
    <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="isOpen" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-5"
            @click="close">
            <!-- Modal Container -->
            <Transition enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 scale-95 translate-y-4" enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 scale-100 translate-y-0"
                leave-to-class="opacity-0 scale-95 translate-y-4">
                <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl flex flex-col"
                    @click.stop>
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center p-6 lg:p-8 border-b border-gray-200 bg-gray-50">
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">Booking Request Review</h2>
                            <div class="text-sm text-gray-600 font-medium">ID: {{ bookingData.id }}</div>
                        </div>
                        <button
                            class="p-2 rounded-lg hover:bg-gray-200 text-gray-500 hover:text-gray-700 transition-colors duration-200"
                            @click="close" :disabled="isLoading">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="flex-1 overflow-y-auto">
                        <!-- Customer Information -->
                        <section class="p-6 lg:p-8 border-b border-gray-100">
                            <h3 class="text-xl font-semibold text-gray-900 mb-5">Customer Information</h3>
                            <div class="flex flex-col sm:flex-row gap-4 items-start">
                                <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0">
                                    <img :src="bookingData.customer.avatar" :alt="bookingData.customer.name"
                                        class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xl font-semibold text-gray-900 mb-2">{{ bookingData.customer.name }}
                                    </h4>
                                    <div class="flex flex-wrap gap-4 mb-3">
                                        <span class="flex items-center gap-1 text-sm text-gray-600 font-medium">
                                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                                            </svg>
                                            {{ bookingData.customer.rating }}
                                        </span>
                                        <span class="text-sm text-gray-600 font-medium">{{
                                            bookingData.customer.totalBookings }} bookings</span>
                                        <span class="text-sm text-gray-600 font-medium">Member since {{ new
                                            Date(bookingData.customer.joinDate).getFullYear() }}</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 text-sm text-gray-700">
                                            <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none">
                                                <path
                                                    d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z"
                                                    stroke="currentColor" stroke-width="2" />
                                                <polyline points="22,6 12,13 2,6" stroke="currentColor"
                                                    stroke-width="2" />
                                            </svg>
                                            {{ bookingData.customer.email }}
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-gray-700">
                                            <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none">
                                                <path
                                                    d="M22 16.92V19.92C22 20.52 21.52 21 20.92 21H1.08C0.48 21 0 20.52 0 19.92V16.92C0 13.4 2.9 10.5 6.42 10.5H17.58C21.1 10.5 24 13.4 24 16.92Z"
                                                    stroke="currentColor" stroke-width="2" />
                                            </svg>
                                            {{ bookingData.customer.phone }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Event Details -->
                        <section class="p-6 lg:p-8 border-b border-gray-100">
                            <h3 class="text-xl font-semibold text-gray-900 mb-5">Event Details</h3>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5">
                                    <h4 class="text-xl font-semibold text-gray-900">{{ bookingData.event.title }}</h4>
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-lg uppercase tracking-wide mt-2 sm:mt-0">
                                        {{ bookingData.event.type }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Date
                                            & Time</label>
                                        <div class="text-sm text-gray-900 font-medium">
                                            {{ formatDate(bookingData.event.date) }}
                                            <br>
                                            <span class="text-gray-600 text-xs">{{ bookingData.event.time }} - {{
                                                bookingData.event.endTime }}</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Venue</label>
                                        <div class="text-sm text-gray-900 font-medium">
                                            <strong>{{ bookingData.event.location.venue }}</strong>
                                            <br>
                                            <span class="text-gray-600 text-xs">{{ bookingData.event.location.address
                                                }}</span>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Expected
                                            Guests</label>
                                        <div class="text-sm text-gray-900 font-medium">{{ bookingData.event.guests }}
                                            people</div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Service
                                            Duration</label>
                                        <div class="text-sm text-gray-900 font-medium">{{ bookingData.service.duration
                                            }}</div>
                                    </div>
                                </div>

                                <div v-if="bookingData.event.notes">
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Special
                                        Notes</label>
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ bookingData.event.notes }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- Service & Pricing -->
                        <section class="p-6 lg:p-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-5">Service & Pricing</h3>
                            <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5">
                                    <h4 class="text-lg font-semibold text-gray-900">{{ bookingData.service.name }}</h4>
                                    <div class="text-2xl font-bold text-green-600 mt-2 sm:mt-0">{{
                                        formatCurrency(bookingData.service.price) }}</div>
                                </div>

                                <div class="space-y-3 mb-5">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-700">Service Total</span>
                                        <span class="font-medium text-gray-900">{{
                                            formatCurrency(bookingData.booking.totalAmount) }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center text-sm bg-yellow-50 border border-yellow-200 rounded-lg p-3 font-semibold">
                                        <span class="text-yellow-800">Deposit Required</span>
                                        <span class="text-yellow-800">{{ formatCurrency(bookingData.booking.deposit)
                                            }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm font-semibold">
                                        <span class="text-gray-900">Balance Due</span>
                                        <span class="text-gray-900">{{ formatCurrency(bookingData.booking.balance)
                                            }}</span>
                                    </div>
                                </div>

                                <div v-if="bookingData.booking.specialRequests.length">
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Special
                                        Requests</label>
                                    <ul class="space-y-1">
                                        <li v-for="request in bookingData.booking.specialRequests" :key="request"
                                            class="text-sm text-gray-700 flex items-start gap-2">
                                            <span
                                                class="w-1.5 h-1.5 bg-green-500 rounded-full mt-2 flex-shrink-0"></span>
                                            {{ request }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-center p-6 lg:p-8 border-t border-gray-200 bg-gray-50 gap-4">
                        <div class="text-sm text-gray-600">
                            Request received: {{ formatDate(bookingData.booking.requestedDate) }}
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <button
                                class="px-6 py-3 border border-gray-300 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
                                @click="handleDecline" :disabled="isLoading">
                                <span v-if="isLoading">Processing...</span>
                                <span v-else>Decline</span>
                            </button>
                            <button
                                class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                @click="handleAccept" :disabled="isLoading">
                                <svg v-if="isLoading" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                        class="opacity-25" />
                                    <path fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        class="opacity-75" />
                                </svg>
                                <span v-if="isLoading">Processing...</span>
                                <span v-else>Accept Booking</span>
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
/* Custom styles for animations and any additional styling */
</style>
