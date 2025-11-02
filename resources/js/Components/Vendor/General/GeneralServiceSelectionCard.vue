<script setup>

import { ref } from 'vue';
import GeneralServiceViewModal from './GeneralServiceViewModal.vue';

const props = defineProps({
    service: {
        type: Object,
        required: true
    },
    isSelected: {
        type: Boolean,
        default: false
    },
    isDateAvailable: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['select', 'view'])

const serviceViewModal = ref(null)

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const showPriceRange = (service) => {
    if (service.max_price) {
        return `${formatPrice(service.price)} - ${formatPrice(service.max_price)}`;
    }
    return formatPrice(service.price);
};

const handleSelect = () => {
    emit('select', props.service.id)
}

const handleView = () => {
    serviceViewModal.value?.openModal()
}

const handleServiceViewClose = () => {
    console.log('Service view closed')
}
</script>

<template>
    <GeneralServiceViewModal ref="serviceViewModal" :service="service" @close="handleServiceViewClose" />

    <div
        class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex flex-col md:flex-row">
            <!-- Service Image -->
            <div class="md:w-2/5 relative">
                <slot name="favorite"></slot>
                <img :src="service.image_url || service.media?.[0]?.original_url || '/images/service-placeholder.jpg'"
                    :alt="service.name" class="w-full h-40 sm:h-48 md:h-full object-cover">
            </div>

            <!-- Content Container -->
            <div class="md:w-3/5 p-3 sm:p-4 flex flex-col">
                <!-- Category and Date Added - Keep side by side on mobile -->
                <!-- Category and Date Added - Keep side by side on mobile -->
                <div class="flex items-center justify-between mb-3">
                    <span v-if="service.category_name" class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                        {{ service.category_name }}
                    </span>

                    <div class="flex items-center text-xs sm:text-sm text-gray-500">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="truncate">{{ service.dateAdded || 'Recently added' }}</span>
                    </div>
                </div>

                <!-- Price and Rating - Keep side by side on mobile -->
                <div class="flex items-center justify-between mb-3">
                    <div class="text-base sm:text-lg font-bold text-green-600">
                        {{ showPriceRange(service) }}
                        <span v-if="service.photography_service?.coverage_type"
                            class="text-xs sm:text-sm font-normal text-gray-600 ml-1 sm:ml-2">
                            • {{ service.photography_service.coverage_type }}
                        </span>
                    </div>

                    <!-- Rating -->
                    <div class="flex items-center text-xs sm:text-sm text-gray-500">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1 text-yellow-400 flex-shrink-0" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        {{ service.avg_rating ? service.avg_rating.toFixed(1) + ' (' + (service.reviews_count || 0) +
                            ')' : 'No ratings yet' }}
                    </div>
                </div>

                <!-- Service Title and Description -->
                <div class="mb-3 flex-grow">
                    <h3 class="font-semibold text-gray-900 text-sm sm:text-base mb-2 line-clamp-1 leading-tight">
                        {{ service.name }}
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-600 line-clamp-2 leading-relaxed">
                        {{ service.description || 'No description provided' }}
                    </p>
                </div>

                <!-- Vendor Information -->
                <div v-if="service.vendor" class="text-xs sm:text-sm text-gray-600 mb-3 sm:mb-4">
                    <a :href="route('client.vendor.show', service.vendor.id)"
                        class="font-medium cursor-pointer text-blue-500 hover:text-blue-600 transition-colors break-words">
                        {{ service.vendor.business_name }}
                    </a>
                    <span class="mx-1">•</span>
                    <span class="text-gray-500">{{ service.vendor.location }}</span>
                </div>

                <!-- Action Buttons - Keep side by side on mobile -->
                <div v-if="isDateAvailable" class="flex space-x-2 sm:space-x-3 pt-3 border-t border-gray-100">
                    <button @click="handleView"
                        class="flex-1 border border-gray-300 text-gray-700 py-2.5 sm:py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm hover:bg-gray-50 transition-colors font-medium min-h-[44px]">
                        View Details
                    </button>

                    <button @click="handleSelect" :class="{
                        'bg-blue-600 text-white hover:bg-blue-700': !isSelected,
                        'bg-green-600 text-white hover:bg-green-700': isSelected
                    }"
                        class="flex-1 py-2.5 sm:py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm transition-colors flex items-center justify-center font-medium min-h-[44px]">
                        <span v-if="isSelected" class="flex items-center">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Selected
                        </span>
                        <span v-else>Select</span>
                    </button>
                </div>

                <!-- Date Not Available Message -->
                <div v-else class="pt-3 border-t border-gray-100">
                    <p class="text-xs sm:text-sm text-red-500 text-center px-2">
                        Sorry, the date you selected is not available
                    </p>
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

/* Ensure proper touch targets for mobile */
button {
    min-height: 44px;
}

@media (max-width: 640px) {
    button {
        min-height: 42px;
    }
}
</style>
