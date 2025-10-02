<script setup>
import { computed, ref } from 'vue'
import { Check, Calendar, MapPin, Clock, Briefcase, FileText, AlertCircle, Loader } from 'lucide-vue-next'

const props = defineProps({
    selectedServices: {
        type: Array,
        default: () => []
    },
})

const eventForm = defineModel('eventForm')
const agreedToTerms = ref(false)
const isSubmitting = ref(false)

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
        currency: 'PHP',
        minimumFractionDigits: 0
    }).format(amount);
}

// Format date
const formatDate = (dateString) => {
    if (!dateString) return 'Not set'
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

// Format time
const formatTime = (timeString) => {
    if (!timeString) return 'Not set'
    return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    })
}

// Handle form submission
const submitBooking = async () => {
    if (!agreedToTerms.value) {
        alert('Please agree to the terms and conditions to proceed.')
        return
    }
    isSubmitting.value = true
    try {
        // Handle booking submission logic here
        console.log('Submitting booking:', {
            eventForm: eventForm.value,
            selectedServices: props.selectedServices,
            totalPrice: totalPrice.value
        })


        await new Promise(resolve => setTimeout(resolve, 2000))

        // handling would go here
        alert('Booking request sent to vendors successfully!')
    } catch (error) {
        console.error('Booking submission failed:', error)
        alert('Failed to submit booking. Please try again.')
    } finally {
        isSubmitting.value = false
    }
}
</script>

<template>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
        <!-- Header -->
        <div class="text-center pb-6 border-b border-gray-200">
            <div class="w-12 h-12 mx-auto mb-4 bg-blue-50 rounded-full flex items-center justify-center">
                <Check class="h-6 w-6 text-blue-600" />
            </div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 mb-2">Review Booking</h1>
            <p class="text-gray-600 text-sm sm:text-base">Verify your event details before submitting to vendors</p>
        </div>

        <div class="mt-6 lg:mt-8">
            <!-- Event Details -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6 mb-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <Calendar class="h-5 w-5 text-gray-600" />
                    Event Details
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Event Name</label>
                        <p class="text-gray-900 font-medium text-sm sm:text-base">{{ eventForm.name || 'Untitled Event'
                            }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</label>
                        <div class="flex items-center gap-1 text-gray-900 font-medium text-sm sm:text-base">
                            <MapPin class="h-4 w-4 text-gray-500" />
                            {{ eventForm.location || 'Location TBD' }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Date</label>
                        <div class="flex items-center gap-1 text-gray-900 font-medium text-sm sm:text-base">
                            <Calendar class="h-4 w-4 text-gray-500" />
                            {{ formatDate(eventForm.event_date) }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Time</label>
                        <div class="flex items-center gap-1 text-gray-900 font-medium text-sm sm:text-base">
                            <Clock class="h-4 w-4 text-gray-500" />
                            {{ formatTime(eventForm.event_time) }}
                        </div>
                    </div>
                </div>

                <div v-if="eventForm.description" class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
                    <label
                        class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2 block">Description</label>
                    <p class="text-gray-700 text-sm sm:text-base leading-relaxed">{{ eventForm.description }}</p>
                </div>
            </div>

            <!-- Selected Services -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6 mb-6">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <Briefcase class="h-5 w-5 text-gray-600" />
                        Selected Services
                    </h2>
                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-medium">
                        {{ selectedServices?.length || 0 }} services
                    </span>
                </div>

                <div class="space-y-4">
                    <div v-for="service in selectedServices" :key="service.id"
                        class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors">
                        <div class="flex gap-4">
                            <!-- Service Image -->
                            <div
                                class="flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                                <img v-if="service.image_url" :src="service.image_url" :alt="service.name"
                                    class="w-full h-full object-cover">
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                    <Briefcase class="h-6 w-6 sm:h-8 sm:w-8" />
                                </div>
                            </div>

                            <!-- Service Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1">{{
                                            service.name }}</h3>
                                        <p class="text-gray-600 text-sm leading-relaxed line-clamp-2">{{
                                            service.description }}</p>

                                        <!-- Vendor Info -->
                                        <div class="flex items-center mt-2 text-xs sm:text-sm text-gray-500">
                                            <MapPin class="h-3 w-3 sm:h-4 sm:w-4 mr-1" />
                                            <span class="font-medium">{{ service.vendor?.business_name ||
                                                service.vendor?.name || 'Vendor' }}</span>
                                            <span class="mx-2">•</span>
                                            <span class="truncate">
                                                {{ service.vendor?.location || 'Location not specified' }}
                                            </span>

                                        </div>
                                    </div>
                                    <div class="sm:text-right">
                                        <p class="text-lg sm:text-xl font-semibold text-green-600">{{
                                            formatCurrency(service.price) }}</p>
                                        <p v-if="service.catering_service?.price !== service.catering_service?.package_price"
                                            class="text-xs text-gray-500 mt-1">per person</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!selectedServices?.length" class="text-center py-8">
                    <div class="w-12 h-12 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center">
                        <AlertCircle class="h-6 w-6 text-gray-400" />
                    </div>
                    <h3 class="text-base font-semibold text-gray-700 mb-1">No Services Selected</h3>
                    <p class="text-gray-500 text-sm">Add services to continue with your booking</p>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6 mb-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <FileText class="h-5 w-5 text-gray-600" />
                    Additional Information
                </h2>

                <div class="space-y-6">
                    <!-- Special Instructions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions</label>
                        <textarea v-model="eventForm.final_notes" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-gray-400 resize-none"
                            placeholder="Any special requests or instructions for vendors..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">This will be shared with all selected vendors</p>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" v-model="agreedToTerms" id="terms"
                                class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <div class="flex-1">
                                <label for="terms" class="block text-sm font-medium text-gray-700 mb-1">
                                    Accept Terms & Conditions
                                </label>
                                <p class="text-xs text-gray-600 leading-relaxed">
                                    I agree to the
                                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">booking terms</a>,
                                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">cancellation
                                        policy</a>, and
                                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">privacy
                                        policy</a>.
                                    I understand this request will be sent to vendors and a deposit may be required.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary & Submit -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4">Booking Summary</h2>

                <div class="space-y-4">
                    <!-- Services List -->
                    <div class="space-y-3">
                        <div v-for="service in selectedServices" :key="service.id"
                            class="flex justify-between items-start text-sm">
                            <div class="flex-1 min-w-0 pr-3">
                                <p class="text-gray-700 font-medium truncate">{{ service.name }}</p>
                                <p class="text-gray-500 text-xs mt-0.5">{{ service.vendor?.business_name }}</p>
                            </div>
                            <p class="text-gray-900 font-semibold whitespace-nowrap">{{ formatCurrency(service.price) }}
                            </p>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center text-base">
                            <span class="font-semibold text-gray-900">Total</span>
                            <span class="font-bold text-green-600">{{ formatCurrency(totalPrice) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-2">All prices in Philippine Peso (₱)</p>
                    </div>

                    <!-- Quick Event Info -->
                    <div class="bg-gray-50 rounded-lg p-3 mt-4">
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <p class="text-gray-500 font-medium">Date</p>
                                <p class="text-gray-900">{{ formatDate(eventForm.event_date) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 font-medium">Location</p>
                                <p class="text-gray-900 truncate">{{ eventForm.location || 'Not set' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button @click="submitBooking"
                            :disabled="!agreedToTerms || !selectedServices.length || isSubmitting"
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <Loader v-if="isSubmitting" class="h-4 w-4 animate-spin" />
                            <span class="text-sm sm:text-base">
                                {{ isSubmitting ? 'Sending to Vendors...' : 'Submit Booking Request' }}
                            </span>
                        </button>
                        <p class="text-xs text-gray-500 text-center mt-2">
                            Vendors will contact you within 24 hours
                        </p>
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

/* Improve mobile touch targets */
@media (max-width: 640px) {
    button {
        min-height: 48px;
    }

    input[type="checkbox"] {
        min-width: 16px;
        min-height: 16px;
    }
}
</style>
