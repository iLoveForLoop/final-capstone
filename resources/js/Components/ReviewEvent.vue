<script setup>
import { computed } from 'vue'

const props = defineProps({
    selectedServices: {
        type: Array,
        default: () => []
    },
})

const eventForm = defineModel('eventForm')

// Calculate total price
const totalPrice = computed(() => {
    return props.selectedServices.reduce((sum, service) => {
        return sum + parseFloat(service.price || 0)
    }, 0) || 0
})

// Format currency
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
}


// Format date
const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}
</script>

<template>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6">
        <!-- Header -->
        <div class="text-center pb-6 border-b border-gray-100">
            <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800 mb-2">Review Your Event</h2>
            <p class="text-gray-500 text-sm sm:text-base">Please verify all details before proceeding</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Event Details Card -->
                <div class="bg-white rounded-lg border border-gray-100 p-5 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-medium text-gray-800 mb-4">Event Details</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Event Name</p>
                            <p class="text-gray-800 font-medium">{{ eventForm.name || 'Untitled Event' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Location</p>
                            <p class="text-gray-800 font-medium">{{ eventForm.location || 'Location TBD' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Date</p>
                            <p class="text-gray-800 font-medium">{{ formatDate(eventForm.event_date) }}</p>
                        </div>

                        <div v-if="eventForm.event_time">
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Time</p>
                            <p class="text-gray-800 font-medium">{{ eventForm.event_time }}</p>
                        </div>
                    </div>

                    <div v-if="eventForm.description" class="mt-5 pt-5 border-t border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Description</p>
                        <p class="text-gray-700">{{ eventForm.description }}</p>
                    </div>
                </div>

                <!-- Selected Services Card -->
                <div class="bg-white rounded-lg border border-gray-100 p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg sm:text-xl font-medium text-gray-800">Selected Services</h3>
                        <span class="bg-gray-100 text-gray-800 px-2.5 py-1 rounded-full text-xs font-medium">
                            {{ selectedServices?.length || 0 }} selected
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div v-for="service in selectedServices" :key="service.id"
                            class="border border-gray-100 rounded-lg p-4 hover:border-gray-200 transition-colors">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <!-- Service Image -->
                                <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gray-100">
                                    <img v-if="service.image_url" :src="service.image_url" :alt="service.name"
                                        class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Service Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                        <div class="min-w-0">
                                            <h4 class="text-base font-medium text-gray-800">{{ service.name }}</h4>
                                            <p class="text-gray-500 text-sm mt-1 line-clamp-2">{{ service.description }}
                                            </p>
                                        </div>
                                        <div class="sm:text-right">
                                            <p class="text-gray-800 font-medium">{{ formatCurrency(service.price) }}</p>
                                        </div>
                                    </div>

                                    <!-- Vendor Info -->
                                    <div class="flex items-center mt-3 text-xs text-gray-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" />
                                        </svg>
                                        {{ service.vendor?.name || 'Unknown vendor' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!selectedServices?.length" class="text-center py-8">
                        <div class="w-14 h-14 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m5.25 0h7.5m-7.5 0v7.5" />
                            </svg>
                        </div>
                        <h4 class="text-base font-medium text-gray-700 mb-1">No services selected</h4>
                        <p class="text-gray-500 text-sm">Add services to continue</p>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="bg-white rounded-lg border border-gray-100 p-5 sm:p-6">
                    <label class="block text-base font-medium text-gray-800 mb-3">Additional Notes</label>
                    <textarea v-model="eventForm.final_notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-gray-300 focus:ring-1 focus:ring-gray-200 transition-colors placeholder-gray-400"
                        placeholder="Any special requests or instructions..."></textarea>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border border-gray-100 p-5 sm:p-6 sticky top-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-5">Summary</h3>

                    <div class="space-y-3 mb-5">
                        <div v-for="service in selectedServices" :key="service.id" class="flex justify-between text-sm">
                            <p class="text-gray-700 truncate pr-2">{{ service.name }}</p>
                            <p class="text-gray-800 font-medium whitespace-nowrap">{{ formatCurrency(service.price) }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-gray-600 text-sm">Subtotal</p>
                            <p class="text-gray-800 font-medium">{{ formatCurrency(totalPrice) }}</p>
                        </div>
                        <div class="flex justify-between items-center text-base mt-4 pt-4 border-t border-gray-100">
                            <p class="font-medium text-gray-800">Total</p>
                            <p class="font-semibold text-gray-900">{{ formatCurrency(totalPrice) }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <p class="text-gray-500">Event Date</p>
                                <p class="text-gray-700">{{ eventForm.event_date || 'Not set' }}</p>
                            </div>
                            <div class="flex justify-between">
                                <p class="text-gray-500">Location</p>
                                <p class="text-gray-700 text-right">{{ eventForm.location || 'Not set' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
