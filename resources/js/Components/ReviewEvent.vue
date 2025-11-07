<script setup>
import { computed, ref } from 'vue'
import { Check, Calendar, MapPin, Clock, Briefcase, FileText, AlertCircle, Loader, Info } from 'lucide-vue-next'
import { isPricePackage } from '@/utils/packageIdentifier'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    selectedServices: {
        type: Array,
        default: () => []
    },
})

const eventForm = defineModel('eventForm')
const agreedToTerms = ref(false)
const isSubmitting = ref(false)
const emit = defineEmits(['submit-selection'])

// Memoize service prices to avoid recalculation
const servicePrices = computed(() => {
    return props.selectedServices.map(service => {
        const basePrice = parseFloat(service.price || 0)

        if (isPricePackage(service) && eventForm.value?.pax) {
            return {
                id: service.id,
                price: basePrice * parseInt(eventForm.value.pax),
                isPricePackage: true
            }
        }

        return {
            id: service.id,
            price: basePrice,
            isPricePackage: false
        }
    })
})

// Get memoized price for a service
const getServicePrice = (serviceId) => {
    const priceData = servicePrices.value.find(p => p.id === serviceId)
    return priceData?.price || 0
}

// Calculate total price from memoized prices
const totalPrice = computed(() => {
    return servicePrices.value.reduce((sum, priceData) => sum + priceData.price, 0)
})

// Format currency (consider memoizing if called frequently with same values)
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

// Memoize formatted date and time
const formattedEventDate = computed(() => formatDate(eventForm.value?.event_date))
const formattedEventTime = computed(() => formatTime(eventForm.value?.event_time))

const successModal = ref(null)

// Handle form submission
const submitBooking = async () => {
    if (!agreedToTerms.value) {
        alert('Please agree to the terms and conditions to proceed.')
        return
    }
    isSubmitting.value = true

    await new Promise(resolve => setTimeout(resolve, 1000))
    emit('submit-selection')

}
</script>

<template>
    <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-6 py-3 sm:py-4 lg:py-6">
        <!-- Header -->
        <div class="text-center pb-4 sm:pb-6 border-b border-gray-200">
            <div
                class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-3 sm:mb-4 bg-blue-50 rounded-full flex items-center justify-center">
                <Check class="h-5 w-5 sm:h-6 sm:w-6 text-blue-600" />
            </div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-semibold text-gray-900 mb-2">Review Booking</h1>
            <p class="text-gray-600 text-xs sm:text-sm lg:text-base">Verify your event details before submitting to
                vendors</p>
        </div>

        <div class="mt-4 sm:mt-6 lg:mt-8 space-y-4 sm:space-y-6">
            <!-- Event Details -->
            <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4 lg:p-6">
                <h2
                    class="text-base sm:text-lg lg:text-xl font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
                    <Calendar class="h-4 w-4 sm:h-5 sm:w-5 text-gray-600" />
                    Event Details
                </h2>

                <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 lg:gap-6">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Event Name</label>
                        <p class="text-gray-900 font-medium text-sm sm:text-base break-words">{{ eventForm.name ||
                            'Untitled Event' }}</p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</label>
                        <div
                            class="flex items-start sm:items-center gap-1 text-gray-900 font-medium text-sm sm:text-base">
                            <MapPin class="h-4 w-4 text-gray-500 flex-shrink-0 mt-0.5 sm:mt-0" />
                            <span class="break-words">{{ eventForm.location || 'Location TBD' }}</span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Date</label>
                        <div class="flex items-center gap-1 text-gray-900 font-medium text-sm sm:text-base">
                            <Calendar class="h-4 w-4 text-gray-500 flex-shrink-0" />
                            {{ formattedEventDate }}
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Time</label>
                        <div class="flex items-center gap-1 text-gray-900 font-medium text-sm sm:text-base">
                            <Clock class="h-4 w-4 text-gray-500 flex-shrink-0" />
                            {{ formattedEventTime }}
                        </div>
                    </div>

                    <!-- Pax Information -->
                    <div v-if="eventForm?.pax" class="space-y-1">
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wide">Number of
                            Guests</label>
                        <div class="flex items-center gap-1 text-gray-900 font-medium text-sm sm:text-base">
                            <Briefcase class="h-4 w-4 text-gray-500 flex-shrink-0" />
                            {{ eventForm.pax }} guests
                        </div>
                    </div>
                </div>

                <div v-if="eventForm.description"
                    class="mt-3 sm:mt-4 lg:mt-6 pt-3 sm:pt-4 lg:pt-6 border-t border-gray-200">
                    <label
                        class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2 block">Description</label>
                    <p class="text-gray-700 text-sm sm:text-base leading-relaxed break-words">{{ eventForm.description
                    }}</p>
                </div>
            </div>

            <!-- Selected Services -->
            <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4 lg:p-6">
                <div class="flex flex-col xs:flex-row xs:items-center xs:justify-between gap-2 mb-3 sm:mb-4 lg:mb-6">
                    <h2 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <Briefcase class="h-4 w-4 sm:h-5 sm:w-5 text-gray-600" />
                        Selected Services
                    </h2>
                    <span
                        class="bg-gray-100 text-gray-700 px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs font-medium self-start xs:self-auto">
                        {{ selectedServices?.length || 0 }} services
                    </span>
                </div>

                <div class="space-y-3 sm:space-y-4">
                    <!-- Use will-change for better scroll performance -->
                    <div v-for="service in selectedServices" :key="service.id"
                        class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:border-gray-300 transition-colors service-card">
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                            <!-- Service Image -->
                            <div
                                class="flex-shrink-0 w-full sm:w-16 sm:h-16 lg:w-20 lg:h-20 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 self-center sm:self-start">
                                <img v-if="service.image_url" :src="service.image_url" :alt="service.name"
                                    class="w-full h-full object-cover" loading="lazy">
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                    <Briefcase class="h-6 w-6 sm:h-8 sm:w-8" />
                                </div>
                            </div>

                            <!-- Service Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col gap-2">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <h3
                                                class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 mb-1 break-words">
                                                {{ service.name }}
                                            </h3>
                                            <p
                                                class="text-gray-600 text-xs sm:text-sm leading-relaxed line-clamp-2 break-words">
                                                {{ service.description }}
                                            </p>

                                            <!-- Price Calculation Info -->
                                            <div v-if="isPricePackage(service) && eventForm?.pax"
                                                class="mt-2 text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded inline-flex items-center">
                                                <span class="font-medium">
                                                    {{ formatCurrency(service.price) }} × {{ eventForm.pax }} guests
                                                </span>
                                            </div>

                                            <!-- Vendor Info -->
                                            <div
                                                class="flex items-center mt-2 text-xs sm:text-sm text-gray-500 flex-wrap gap-1">
                                                <MapPin class="h-3 w-3 sm:h-4 sm:w-4 flex-shrink-0" />
                                                <span class="font-medium truncate">{{ service.vendor?.business_name ||
                                                    service.vendor?.name || 'Vendor' }}</span>
                                                <span class="hidden sm:inline">•</span>
                                                <span class="truncate text-xs">
                                                    {{ service.vendor?.location || 'Location not specified' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="sm:text-right mt-2 sm:mt-0">
                                            <p
                                                class="text-base sm:text-lg lg:text-xl font-semibold text-green-600 whitespace-nowrap">
                                                {{ formatCurrency(getServicePrice(service.id)) }}
                                            </p>
                                            <p v-if="isPricePackage(service)"
                                                class="text-xs text-gray-500 mt-1 whitespace-nowrap">
                                                {{ eventForm?.pax ? `total for ${eventForm.pax} guests` : `per person`
                                                }}
                                            </p>
                                            <p v-else-if="service.catering_service?.price !== service.catering_service?.package_price"
                                                class="text-xs text-gray-500 mt-1 whitespace-nowrap">
                                                fixed price
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!selectedServices?.length" class="text-center py-6 sm:py-8">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-2 sm:mb-3 bg-gray-100 rounded-full flex items-center justify-center">
                        <AlertCircle class="h-5 w-5 sm:h-6 sm:w-6 text-gray-400" />
                    </div>
                    <h3 class="text-sm sm:text-base font-semibold text-gray-700 mb-1">No Services Selected</h3>
                    <p class="text-gray-500 text-xs sm:text-sm">Add services to continue with your booking</p>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4 lg:p-6">
                <!-- <h2
                    class="text-base sm:text-lg lg:text-xl font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
                    <FileText class="h-4 w-4 sm:h-5 sm:w-5 text-gray-600" />
                    Additional Information
                </h2> -->

                <div class="space-y-4 sm:space-y-6">
                    <!-- Special Instructions -->
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions</label>
                        <textarea v-model="eventForm.final_notes" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-gray-400 resize-none"
                            placeholder="Any special requests or instructions for vendors..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">This will be shared with all selected vendors</p>
                    </div> -->

                    <!-- Terms and Conditions -->
                    <div class=" border-gray-200 pt-4 sm:pt-6">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" v-model="agreedToTerms" id="terms"
                                class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <label for="terms" class="block text-sm font-medium text-gray-700 mb-1">
                                    Accept Terms & Conditions
                                </label>
                                <p class="text-xs text-gray-600 leading-relaxed break-words">
                                    I agree to the
                                    <a class="text-blue-600 hover:text-blue-800 font-medium" href="/termsandcondition"
                                        target="_blank">booking terms</a>,
                                    <a href="/termsandcondition" target="_blank"
                                        class="text-blue-600 hover:text-blue-800 font-medium">cancellation
                                        policy</a>, and
                                    <a href="/termsandcondition" target="_blank"
                                        class="text-blue-600 hover:text-blue-800 font-medium">privacy
                                        policy</a>.
                                    I understand this request will be sent to vendors and a deposit may be required.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary & Submit -->
            <div
                class="bg-white rounded-lg border border-gray-200 p-3 sm:p-4 lg:p-6 sticky bottom-0 bg-white shadow-lg sm:shadow-none sm:static">
                <h2 class="text-base sm:text-lg lg:text-xl font-semibold text-gray-900 mb-3 sm:mb-4">Booking Summary
                </h2>

                <div class="space-y-3 sm:space-y-4">
                    <!-- Services List -->
                    <div class="space-y-2 sm:space-y-3 max-h-40 sm:max-h-none overflow-y-auto">
                        <div v-for="service in selectedServices" :key="service.id"
                            class="flex justify-between items-start text-sm gap-2">
                            <div class="flex-1 min-w-0 pr-2">
                                <p class="text-gray-700 font-medium truncate text-xs sm:text-sm">{{ service.name }}</p>
                                <p class="text-gray-500 text-xs mt-0.5 truncate">{{ service.vendor?.business_name }}</p>
                                <p v-if="isPricePackage(service) && eventForm?.pax"
                                    class="text-xs text-blue-600 mt-0.5">
                                    {{ formatCurrency(service.price) }} × {{ eventForm.pax }} guests
                                </p>
                            </div>
                            <p class="text-gray-900 font-semibold whitespace-nowrap text-xs sm:text-sm">
                                {{ formatCurrency(getServicePrice(service.id)) }}
                            </p>
                        </div>
                    </div>

                    <!-- Total with Estimated Indicator -->
                    <div class="border-t border-gray-200 pt-3 sm:pt-4">
                        <div class="flex justify-between items-center text-sm sm:text-base">
                            <div class="flex items-center gap-1">
                                <span class="font-semibold text-gray-900">Estimated Total</span>
                                <div class="group relative tooltip-wrapper">
                                    <Info class="h-3 w-3 sm:h-4 sm:w-4 text-gray-400 cursor-help" />
                                    <div class="tooltip">
                                        Final price may vary based on vendor quotes
                                        <div class="tooltip-arrow"></div>
                                    </div>
                                </div>
                            </div>
                            <span class="font-bold text-green-600">{{ formatCurrency(totalPrice) }}</span>
                        </div>
                    </div>

                    <!-- Quick Event Info -->
                    <div class="bg-gray-50 rounded-lg p-2 sm:p-3 mt-3 sm:mt-4">
                        <div class="grid grid-cols-2 gap-2 sm:gap-4 text-xs">
                            <div>
                                <p class="text-gray-500 font-medium">Date</p>
                                <p class="text-gray-900 text-xs sm:text-sm">{{ formattedEventDate }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 font-medium">Location</p>
                                <p class="text-gray-900 text-xs sm:text-sm truncate">{{ eventForm.location || 'Not set'
                                }}</p>
                            </div>
                            <div v-if="eventForm?.pax">
                                <p class="text-gray-500 font-medium">Guests</p>
                                <p class="text-gray-900 text-xs sm:text-sm">{{ eventForm.pax }} people</p>
                            </div>
                            <div>
                                <p class="text-gray-500 font-medium">Services</p>
                                <p class="text-gray-900 text-xs sm:text-sm">{{ selectedServices?.length || 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 sm:mt-6">
                        <button @click="submitBooking"
                            :disabled="!agreedToTerms || !selectedServices.length || isSubmitting"
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-3 px-4 sm:px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-sm sm:text-base">
                            <Loader v-if="isSubmitting" class="h-4 w-4 animate-spin" />
                            <span>
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

/* Optimize scroll performance */
.service-card {
    will-change: auto;
    contain: layout style paint;
}

/* Optimize tooltip to prevent repaints */
.tooltip-wrapper {
    position: relative;
}

.tooltip {
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-bottom: 0.5rem;
    padding: 0.25rem 0.5rem;
    background-color: #1f2937;
    color: white;
    font-size: 0.75rem;
    border-radius: 0.25rem;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
    z-index: 10;
}

.tooltip-wrapper:hover .tooltip {
    opacity: 1;
}

.tooltip-arrow {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border-width: 4px;
    border-style: solid;
    border-color: #1f2937 transparent transparent transparent;
}

/* Custom breakpoint for extra small screens */
@media (min-width: 475px) {
    .xs\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .xs\:flex-row {
        flex-direction: row;
    }

    .xs\:items-center {
        align-items: center;
    }

    .xs\:justify-between {
        justify-content: space-between;
    }

    .xs\:self-auto {
        align-self: auto;
    }
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

/* Sticky footer for mobile */
@media (max-width: 768px) {
    .sticky {
        position: sticky;
        z-index: 10;
    }
}
</style>
