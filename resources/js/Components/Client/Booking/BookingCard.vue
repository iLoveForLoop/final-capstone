<script setup>
import { ref } from 'vue';
import LeaveReviewModal from '../LeaveReviewModal.vue';
import { Link } from '@inertiajs/vue3';

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
        month: 'long',
        day: 'numeric'
    });
};

const getStatusColor = (status) => {
    const colors = {
        confirmed: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-blue-100 text-blue-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const isReviewModalOpen = ref(false)

const serviceData = ref({
    id: props.booking.service.id,
    name: props.booking.service.name,
    provider: props.booking.vendor.business_name,
    date: props.booking.formatted_date,
    bookingId: props.booking.id,
    vendorId: props.booking.vendor.id

})



</script>

<template>
    <LeaveReviewModal :isOpen="isReviewModalOpen" @close="isReviewModalOpen = false" :serviceData="serviceData" />
    <div class="p-6">
        <!-- Booking Header -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-start space-x-4">
                <img :src="booking.service_image" :alt="booking.title" class="w-20 h-20 rounded-lg object-cover">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="text-sm font-mono text-gray-500">{{ booking.id }}</span>
                        <span :class="['px-2 py-1 text-xs rounded-full', getStatusColor(booking.status)]">
                            {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
                        </span>
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                            {{ booking.category.name }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ booking.event_name }}</h3>
                    <p class="text-sm text-gray-600 mb-2">{{ booking.description }}</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <Link :href="route('client.vendor.show', booking.vendor.id)" class="font-medium text-blue-500">
                        {{
                            booking.vendor.business_name }}</Link>
                        <span class="mx-2">•</span>
                        <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span v-if="booking.vendor_rating">Rating: {{ booking.vendor_rating }}/5</span>
                        <span v-else>No ratings yet</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-gray-900">{{ formatPrice(booking.raw_amount) }}
                </div>
                <!-- <div class="text-sm mt-1" :class="getPaymentStatusColor(booking.paymentStatus)">
                                    {{ booking.paymentStatus.charAt(0).toUpperCase() + booking.paymentStatus.slice(1) }}
                                </div> -->
            </div>
        </div>

        <!-- Booking Details -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 bg-gray-50 rounded-lg p-4">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">EVENT DATE</p>
                <p class="text-sm font-medium text-gray-900">{{ formatDate(booking.event_date) }}</p>
                <p class="text-xs text-gray-600">{{ booking.eventTime }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">LOCATION</p>
                <p class="text-sm font-medium text-gray-900">{{ booking.event_location }}</p>
            </div>
            <!-- <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">PAYMENT</p>
                                <p class="text-sm font-medium text-gray-900">Paid: {{ formatPrice(booking.amountPaid) }}
                                </p>
                                <p class="text-xs text-gray-600" v-if="booking.balanceAmount > 0">
                                    Balance: {{ formatPrice(booking.balanceAmount) }}
                                </p>
                            </div> -->
        </div>

        <!-- Notes -->
        <div v-if="booking.notes" class="mb-4">
            <p class="text-xs font-medium text-gray-500 mb-1">NOTES</p>
            <p class="text-sm text-gray-700 bg-gray-50 rounded p-3">{{ booking.notes }}</p>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="flex space-x-3">
                <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Contact Provider
                </button>
                <!-- <button @click="downloadInvoice(booking.id)" class="text-sm text-gray-600 hover:text-gray-700">
                                    Download Invoice
                                </button> -->
            </div>
            <div class="flex space-x-2">
                <!-- <button v-if="booking.status === 'confirmed' || booking.status === 'pending'"
                                    @click="rescheduleBooking(booking.id)"
                                    class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                                    Reschedule
                                </button> -->
                <button v-if="booking.status === 'confirmed' || booking.status === 'pending'"
                    class="px-4 py-2 text-sm border border-red-300 text-red-700 rounded hover:bg-red-50 transition-colors">
                    Cancel
                </button>
                <button @click="isReviewModalOpen = true" v-if="booking.status === 'completed'"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                    Leave Review
                </button>
            </div>
        </div>
    </div>
</template>


<style></style>
