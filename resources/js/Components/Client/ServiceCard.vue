<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    services: {
        type: Object
    },
    viewMode: {
        type: String,
        default: 'grid'
    }
})

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};

// Function to get service-specific information
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

// Function to render star rating
const renderStars = (rating) => {
    const stars = [];
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;

    for (let i = 0; i < fullStars; i++) {
        stars.push('★');
    }

    if (hasHalfStar) {
        stars.push('☆');
    }

    while (stars.length < 5) {
        stars.push('☆');
    }

    return stars;
};
</script>

<template>
    <!-- Grid View -->
    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="service in services.data" :key="service.id"
            class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-sm transition-shadow">
            <!-- Service Image -->
            <div class="relative h-48 bg-gray-100">
                <img v-if="service.image_url" :src="service.image_url" :alt="service.name"
                    class="w-full h-full object-cover">
                <div v-else class="flex items-center justify-center h-full text-gray-400">
                    <div class="text-4xl">{{ getServiceInfo(service).icon }}</div>
                </div>

                <!-- Service Type Badge -->
                <div class="absolute top-3 left-3">
                    <span
                        class="px-2 py-1 bg-white/90 backdrop-blur-sm text-gray-700 text-xs rounded-full font-medium capitalize">
                        {{ getServiceInfo(service).type }}
                    </span>
                </div>

                <!-- Availability Badge -->
                <div class="absolute top-3 right-3">
                    <span :class="[
                        'px-2 py-1 text-xs rounded-full font-medium',
                        service.is_available
                            ? 'bg-green-100 text-green-800'
                            : 'bg-red-100 text-red-800'
                    ]">
                        {{ service.is_available ? 'Available' : 'Booked' }}
                    </span>
                </div>
            </div>

            <!-- Service Content -->
            <div class="p-6">
                <!-- Title and Rating -->
                <div class="mb-3">
                    <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-1">{{ service.name }}</h3>

                    <!-- Rating -->
                    <div v-if="service.average_rating" class="flex items-center gap-2 mb-2">
                        <div class="flex text-yellow-400 text-sm">
                            <span v-for="(star, index) in renderStars(service.average_rating)" :key="index"
                                :class="star === '★' ? 'text-yellow-400' : 'text-gray-300'">
                                {{ star }}
                            </span>
                        </div>
                        <span class="text-sm text-gray-600">
                            {{ Number(service.average_rating).toFixed(1) }}
                            <span v-if="service.reviews_count">({{ service.reviews_count }})</span>
                        </span>
                    </div>
                    <div v-else class="text-sm text-gray-500 mb-2">No reviews yet</div>
                </div>

                <!-- Service Category -->
                <div class="text-sm text-gray-600 mb-2">
                    {{ service.category?.name || 'Service' }}
                </div>

                <!-- Service-specific details -->
                <div class="mb-3">
                    <div class="flex flex-wrap gap-2 mb-2">
                        <span v-for="detail in getServiceInfo(service).details.slice(0, 2)" :key="detail"
                            class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded border border-blue-200">
                            {{ detail }}
                        </span>
                    </div>

                    <!-- Menu count for catering -->
                    <div v-if="getServiceInfo(service).type === 'catering' && getServiceInfo(service).menuCount > 0"
                        class="text-xs text-gray-600">
                        {{ getServiceInfo(service).menuCount }} dishes available
                    </div>
                </div>

                <!-- Description -->
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                    {{ service.description }}
                </p>

                <!-- Price and Action -->
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">
                            {{ formatPrice(getServiceInfo(service).price) }}
                            <span v-if="getServiceInfo(service).type === 'catering'"
                                class="text-sm text-gray-500">/pax</span>
                        </div>
                        <div v-if="getServiceInfo(service).hasDelivery" class="text-xs text-gray-500">
                            + {{ formatPrice(getServiceInfo(service).deliveryFee) }} delivery
                        </div>
                    </div>
                    <Link :href="`/client/services/${service.id}`"
                        class="bg-blue-600 text-white px-4 py-2 text-sm rounded hover:bg-blue-700 transition-colors">
                    View Details
                    </Link>
                </div>
            </div>
        </div>
    </div>

    <!-- List View -->
    <div v-else class="space-y-4">
        <div v-for="service in services.data" :key="service.id" class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-start space-x-4">
                <!-- Service Image -->
                <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded overflow-hidden">
                    <img v-if="service.image_url" :src="service.image_url" :alt="service.name"
                        class="w-full h-full object-cover">
                    <div v-else class="flex items-center justify-center h-full text-gray-400">
                        <div class="text-2xl">{{ getServiceInfo(service).icon }}</div>
                    </div>
                </div>

                <!-- Service Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0 pr-4">
                            <!-- Title and Category -->
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ service.name }}</h3>
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full capitalize">
                                    {{ getServiceInfo(service).type }}
                                </span>
                            </div>

                            <div class="text-sm text-gray-600 mb-2">{{ service.category?.name || 'Service' }}</div>

                            <!-- Rating -->
                            <div v-if="service.average_rating" class="flex items-center gap-2 mb-2">
                                <div class="flex text-yellow-400 text-sm">
                                    <span v-for="(star, index) in renderStars(service.average_rating)" :key="index"
                                        :class="star === '★' ? 'text-yellow-400' : 'text-gray-300'">
                                        {{ star }}
                                    </span>
                                </div>
                                <span class="text-sm text-gray-600">
                                    {{ Number(service.average_rating).toFixed(1) }}
                                    <span v-if="service.reviews_count">({{ service.reviews_count }})</span>
                                </span>
                            </div>
                            <div v-else class="text-sm text-gray-500 mb-2">No reviews yet</div>

                            <!-- Service-specific details -->
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span v-for="detail in getServiceInfo(service).details.slice(0, 3)" :key="detail"
                                    class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded border border-blue-200">
                                    {{ detail }}
                                </span>
                            </div>

                            <!-- Menu count for catering -->
                            <div v-if="getServiceInfo(service).type === 'catering' && getServiceInfo(service).menuCount > 0"
                                class="text-xs text-gray-600 mb-2">
                                {{ getServiceInfo(service).menuCount }} dishes available
                            </div>

                            <!-- Description -->
                            <p class="text-gray-600 text-sm line-clamp-1">{{ service.description }}</p>
                        </div>

                        <!-- Right side: Price, Availability, Action -->
                        <div class="flex items-center space-x-4">
                            <!-- Availability -->
                            <span :class="[
                                'px-2 py-1 text-xs rounded-full whitespace-nowrap',
                                service.is_available
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800'
                            ]">
                                {{ service.is_available ? 'Available' : 'Booked' }}
                            </span>

                            <!-- Price -->
                            <div class="text-right">
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ formatPrice(getServiceInfo(service).price) }}
                                    <span v-if="getServiceInfo(service).type === 'catering'"
                                        class="text-sm text-gray-500">/pax</span>
                                </div>
                                <div v-if="getServiceInfo(service).hasDelivery" class="text-xs text-gray-500">
                                    + {{ formatPrice(getServiceInfo(service).deliveryFee) }} delivery
                                </div>
                            </div>

                            <!-- Action Button -->
                            <Link :href="`/client/services/${service.id}`"
                                class="bg-blue-600 text-white px-4 py-2 text-sm rounded hover:bg-blue-700 transition-colors whitespace-nowrap">
                            View Details
                            </Link>
                        </div>
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
