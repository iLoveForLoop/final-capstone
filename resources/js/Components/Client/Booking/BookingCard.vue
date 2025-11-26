<script setup>
import { ref } from 'vue';
import LeaveReviewModal from '../LeaveReviewModal.vue';
import ViewReviewModal from '@/Components/ViewReviewModal.vue';
import { Link, router } from '@inertiajs/vue3';
import CancelBooking from './CancelBooking.vue';
import VendorContactInformation from './VendorContactInformation.vue';

import emitter from '@/utils/eventBus';
import { MapPin, Calendar, Star, MessageCircle, XCircle, Eye, Target } from 'lucide-vue-next';
import ClientBookingDetailsModal from './ClientBookingDetailsModal.vue';

const props = defineProps({
    booking: {
        type: Object
    }
})

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getStatusColor = (status) => {
    const colors = {
        confirmed: 'bg-green-100 text-green-800 border-green-200',
        pending: 'bg-amber-100 text-amber-800 border-amber-200',
        completed: 'bg-blue-100 text-blue-800 border-blue-200',
        cancelled: 'bg-red-100 text-red-800 border-red-200'
    };
    return colors[status] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const isReviewModalOpen = ref(false)
const viewingReview = ref(false)
const showVendorModal = ref(false);
const showCancellationModal = ref(false)
const showBookingDetailsModal = ref(false) // Add this ref
const isLoading = ref(false)

// Create a template ref for the modal
const bookingDetailsModal = ref(null)

const serviceData = ref({
    id: props.booking.service.id,
    name: props.booking.service.name,
    provider: props.booking.vendor.business_name,
    date: props.booking.formatted_date,
    bookingId: props.booking.id,
    vendorId: props.booking.vendor.id
})

const handleConfirmCancellation = (bookingData) => {
    isLoading.value = true
    router.patch(route('client.booking.cancel', props.booking.id), {
        reason: bookingData.cancellationReason,
        comment: bookingData.additionalComments
    }, {
        onFinish: () => {
            showCancellationModal.value = false
            isLoading.value = false
        }
    })
}

const messageVendor = (data) => {
    showVendorModal.value = false
    emitter.emit('chat-vendor', props.booking.vendor.user_id)
}

const bookingData = ref({
    id: props.booking.f_id,
    serviceName: props.booking.service.name,
    serviceProvider: props.booking.vendor.full_name,
    date: props.booking.formatted_date,
    startTime: props.booking.time,
    location: props.booking.event_location
})

const contactData = ref({
    id: props.booking.f_id,
    name: props.booking.vendor.full_name,
    email: props.booking.vendor.user.email,
    phone: props.booking.vendor.contact_number,
    address: props.booking.vendor.location,
    avatar: props.booking.vendor_avatar,
    isVerified: false
})

console.log('Booking: ', props.booking);


// Function to open booking details modal
const openBookingDetails = () => {
    if (bookingDetailsModal.value) {
        bookingDetailsModal.value.open(props.booking)
    }
}
</script>

<template>
    <!-- Add the Booking Details Modal -->
    <ClientBookingDetailsModal ref="bookingDetailsModal" />

    <VendorContactInformation :vendor="contactData" :is-open="showVendorModal" :is-loading="isLoading"
        @message="messageVendor" @close="showVendorModal = false" />
    <CancelBooking :booking="bookingData" :isOpen="showCancellationModal" :isLoading="isLoading"
        @close="showCancellationModal = false" @confirm="handleConfirmCancellation" />
    <LeaveReviewModal :isOpen="isReviewModalOpen" @close="isReviewModalOpen = false" :serviceData="serviceData" />
    <ViewReviewModal :isOpen="viewingReview" @close="viewingReview = false" :review="booking.review" />

    <!-- Make the entire card clickable -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-200 cursor-pointer"
        @click="openBookingDetails">
        <!-- Header Section -->
        <div class="p-4 sm:p-6 border-b border-gray-100">
            <div class="flex items-start justify-between gap-4">
                <!-- Left Content -->
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <!-- Service Image -->
                    <img :src="booking.service_image" :alt="booking.title"
                        class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg object-cover border border-gray-200 flex-shrink-0">

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <!-- Badges -->
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="text-xs font-mono text-gray-500 bg-gray-50 px-2 py-1 rounded">
                                {{ booking.f_id }}
                            </span>
                            <span
                                :class="['px-2.5 py-1 text-xs font-medium rounded-full border', getStatusColor(booking.status)]">
                                {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
                            </span>
                            <span
                                class="bg-blue-50 text-blue-700 text-xs px-2.5 py-1 rounded-full border border-blue-200">
                                {{ booking.category.name }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-1">
                            {{ booking.service.name }}
                        </h3>

                        <!-- Description -->
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                            {{ booking.description }}
                        </p>

                        <!-- Vendor Info -->
                        <div class="flex items-center gap-3 text-sm">
                            <Link :href="route('client.vendor.show', booking.vendor.id)"
                                class="font-medium text-blue-600 hover:text-blue-700 transition-colors truncate"
                                @click.stop>
                            {{ booking.vendor.business_name }}
                            </Link>
                            <div class="flex items-center gap-1 text-gray-500">
                                <Star class="w-4 h-4 text-yellow-400 fill-current" />
                                <span v-if="booking.vendor_rating">{{ booking.vendor_rating }}/5</span>
                                <span v-else class="text-xs">No ratings</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Price & Quick Action -->
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <div class="text-right">
                        <div class="text-xl font-bold text-gray-900">{{ formatPrice(booking.raw_amount) }}</div>
                        <div class="text-xs text-gray-500 mt-1">Estimated Total Amount</div>
                    </div>
                    <button @click.stop="showVendorModal = true"
                        class="hidden sm:flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors">
                        <MessageCircle class="w-4 h-4" />
                        <span>Contact</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Details Section -->
        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Event Name -->
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white rounded-lg border border-gray-200 flex-shrink-0">
                        <Target class="w-4 h-4 text-blue-600" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Event Name</p>
                        <p class="text-sm font-medium text-gray-900 truncate">{{ booking.event_name }}</p>
                    </div>
                </div>

                <!-- Date & Time -->
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white rounded-lg border border-gray-200 flex-shrink-0">
                        <Calendar class="w-4 h-4 text-blue-600" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Event Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ formatDate(booking.event_date) }}</p>
                        <p class="text-xs text-gray-600">{{ booking.eventTime }}</p>
                    </div>
                </div>

                <!-- Location -->
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white rounded-lg border border-gray-200 flex-shrink-0">
                        <MapPin class="w-4 h-4 text-blue-600" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Location</p>
                        <p class="text-sm font-medium text-gray-900 truncate">{{ booking.event_location }}</p>
                    </div>
                </div>

                <!-- Notes (if available) -->
                <div class="flex items-center gap-3" v-if="booking.notes">
                    <div class="p-2 bg-white rounded-lg border border-gray-200 flex-shrink-0">
                        <MessageCircle class="w-4 h-4 text-blue-600" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Notes</p>
                        <p class="text-sm text-gray-700 truncate">{{ booking.notes }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Section -->
        <div class="p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <!-- Mobile Contact Button -->
                <button @click.stop="showVendorModal = true"
                    class="sm:hidden flex items-center justify-center gap-2 w-full py-2.5 text-blue-600 hover:text-blue-700 font-medium border border-blue-200 rounded-lg hover:bg-blue-50 transition-colors">
                    <MessageCircle class="w-4 h-4" />
                    <span>Contact Provider</span>
                </button>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 w-full sm:w-auto justify-center sm:justify-end">
                    <!-- Cancel Booking -->
                    <button @click.stop="showCancellationModal = true" v-if="booking.status === 'pending'"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors font-medium flex-1 sm:flex-none justify-center">
                        <XCircle class="w-4 h-4" />
                        <span class="hidden sm:inline">Cancel Booking</span>
                        <span class="sm:hidden">Cancel</span>
                    </button>

                    <!-- Leave Review -->
                    <button @click.stop="isReviewModalOpen = true"
                        v-if="booking.status === 'completed' && booking.can_review"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex-1 sm:flex-none justify-center">
                        <Star class="w-4 h-4" />
                        <span class="hidden sm:inline">Leave Review</span>
                        <span class="sm:hidden">Review</span>
                    </button>

                    <!-- View Review -->
                    <button @click.stop="viewingReview = true" v-if="!booking.can_review && booking.review"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium flex-1 sm:flex-none justify-center">
                        <Eye class="w-4 h-4" />
                        <span class="hidden sm:inline">View Review</span>
                        <span class="sm:hidden">View Review</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
