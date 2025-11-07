<script setup>
import { ref } from 'vue';
// import BookingModal from './BookingModal.vue';
import { Link, router, usePage } from '@inertiajs/vue3'
// import LoginModal from '../LoginModal.vue';
import { isPricePackage } from '@/utils/packageIdentifier';
import BookingModal from './BookingModal.vue';
import LoginModal from '../LoginModal.vue';

const props = defineProps({
    service: {
        type: Object
    }
})

const page = usePage()

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};

const bookingModal = ref(null);
const loginModal = ref(null)

const handleViewDetails = () => {
    router.get(route('client.service.show', props.service.id))
}

const tryToBook = () => {
    if (!page.props.auth.user) {
        console.log('Log in First');
        loginModal.value.show()
        return
    }
    bookingModal.value.openModal()
}

// Function to get service-specific information (same as parent component)
const getServiceInfo = (service) => {
    if (service.catering_service) {
        return {
            type: 'catering',
            icon: '🍽️',
            details: [
                `${service.catering_service.min_pax}-${service.catering_service.max_pax} pax`,
                `${service.catering_service.lead_time_days} days lead time`,
                service.catering_service.is_customizable ? 'Customizable' : null
            ].filter(Boolean),
            price: service.catering_service.price || service.price,
            hasDelivery: !!service.catering_service.delivery_fee,
            deliveryFee: service.catering_service.delivery_fee,
            menuCount: service.catering_service.dishes ?
                Object.values(service.catering_service.dishes).reduce((total, dishes) => total + (dishes?.length || 0), 0) : 0
        };
    }

    if (service.photography_service) {
        return {
            type: 'photography',
            icon: '📸',
            details: [
                `${service.photography_service.duration}h session`,
                service.photography_service.style,
                service.photography_service.equipment ? 'Pro Equipment' : null
            ].filter(Boolean),
            price: service.photography_service.price || service.price,
            hasDelivery: false,
            deliveryFee: null,
            menuCount: 0
        };
    }

    // Default service
    return {
        type: 'general',
        icon: '⭐',
        details: [service.category?.name || 'Professional Service'],
        price: service.price,
        hasDelivery: false,
        deliveryFee: null,
        menuCount: 0
    };
};
</script>

<template>
    <BookingModal ref="bookingModal" :service="service" :pax="service.minimumGuests" />
    <LoginModal ref="loginModal" />

    <div class="flex">
        <!-- Service Image -->
        <div class="relative w-48 h-32 flex-shrink-0">
            <img v-if="service.image_url" :src="service.image_url" :alt="service.name"
                class="w-full h-full object-cover">
            <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center">
                <div class="text-2xl text-gray-400">{{ getServiceInfo(service).icon }}</div>
            </div>
            <slot name="favorite"></slot>
        </div>

        <!-- Service Details -->
        <div class="flex-1 p-4">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <!-- Service Type and Availability -->
                    <div class="flex items-center mb-2">
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2 capitalize">
                            {{ getServiceInfo(service).type }}
                        </span>
                        <span v-if="service.is_available" class="bg-green-500 text-white px-2 py-1 text-xs rounded">
                            Available
                        </span>
                        <span v-else class="bg-red-500 text-white px-2 py-1 text-xs rounded">
                            Unavailable
                        </span>
                    </div>

                    <!-- Service Name and Category -->
                    <h3 class="font-semibold text-gray-900 mb-1">{{ service.name }}</h3>
                    <div class="text-sm text-gray-600 mb-2">{{ service.category?.name || 'Service' }}</div>

                    <!-- Service Description -->
                    <p class="text-sm text-gray-600 mb-2 line-clamp-1">{{ service.description }}</p>

                    <!-- Service-specific details -->
                    <div class="flex flex-wrap gap-1 mb-2">
                        <span v-for="detail in getServiceInfo(service).details.slice(0, 3)" :key="detail"
                            class="text-xs px-2 py-1 bg-gray-50 text-gray-700 rounded border border-gray-200">
                            {{ detail }}
                        </span>
                    </div>

                    <!-- Menu count for catering -->
                    <div v-if="getServiceInfo(service).type === 'catering' && getServiceInfo(service).menuCount > 0"
                        class="text-xs text-gray-600 mb-2">
                        {{ getServiceInfo(service).menuCount }} dishes available
                    </div>

                    <!-- Rating and Reviews -->
                    <div class="flex items-center text-sm text-gray-500">
                        <div v-if="service.average_rating" class="flex items-center mr-4">
                            <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            {{ Number(service.average_rating).toFixed(1) }}
                            <span v-if="service.reviews_count" class="ml-1">({{ service.reviews_count }})</span>
                        </div>
                        <span v-else>No reviews yet</span>
                    </div>

                    <!-- Vendor Information -->
                    <div class="text-sm text-gray-600 mt-2">
                        <Link :href="route('client.vendor.show', service.vendor.id)"
                            class="font-medium cursor-pointer text-blue-500">
                        {{ service.vendor.business_name }}
                        </Link> • {{ service.vendor.location }}
                    </div>
                </div>

                <!-- Price and Action Buttons -->
                <div class="flex flex-col items-end ml-4">
                    <div class="text-lg font-bold text-green-600 mb-2">
                        {{ formatPrice(getServiceInfo(service).price) }}
                        <span v-if="getServiceInfo(service).type === 'catering'"
                            class="text-sm text-gray-500">/pax</span>
                    </div>
                    <div v-if="getServiceInfo(service).hasDelivery" class="text-xs text-gray-500 mb-2">
                        + {{ formatPrice(getServiceInfo(service).deliveryFee) }} delivery
                    </div>
                    <div class="flex space-x-2">
                        <button @click="tryToBook"
                            class="bg-blue-600 text-white py-1 px-3 rounded text-sm hover:bg-blue-700 transition-colors">
                            Book Now
                        </button>
                        <button @click="handleViewDetails"
                            class="border border-gray-300 text-gray-700 py-1 px-3 rounded text-sm hover:bg-gray-50 transition-colors">
                            Details
                        </button>
                    </div>
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
</style>
