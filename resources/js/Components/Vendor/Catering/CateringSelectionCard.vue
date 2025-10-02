<script setup>
import { computed, ref, watch } from 'vue';
import DishSelectionModal from './DishSelectionModal.vue';
import ServiceViewModal from '../../ServiceViewModal.vue';

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

const finalSelectedDishes = defineModel('selectedDishes')

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const handleSelect = () => {
    if (props.service.catering_service.is_customizable) {
        openDishModal();
    } else {
        emit('select', props.service.id)
    }
}

const handleView = () => {
    serviceViewModal.value?.openModal();
}

// Dish Selection Modal
const dishModal = ref(null);
const selectedDishes = ref({});

const openDishModal = () => {
    dishModal.value?.openModal();
};

const updateSelectedDishes = (newSelection) => {
    selectedDishes.value = newSelection || {};
    finalSelectedDishes.value = selectedDishes.value

    if (hasSelections()) {
        emit('select', props.service.id);
    }
};

const handleModalClose = () => {
    console.log('Modal was closed');
};

// Service View Modal
const serviceViewModal = ref(null);

const handleServiceViewClose = () => {
    console.log('Service view modal was closed');
};

// Helper function to get selected dish count
const getSelectedDishCount = () => {
    if (!selectedDishes.value || typeof selectedDishes.value !== 'object') return 0;
    return Object.values(selectedDishes.value).filter(dish => dish !== null && dish !== undefined).length;
};

// Helper function to check if service has selections
const hasSelections = () => {
    return getSelectedDishCount() > 0;
};

watch(() => props.isSelected, (newValue) => {
    console.log('isSelected: ', newValue)
    if (!newValue) {
        selectedDishes.value = {}
    }
})
</script>

<template>
    <!-- Modals -->
    <DishSelectionModal ref="dishModal" :service="service" v-model="selectedDishes"
        @update:modelValue="updateSelectedDishes" @close="handleModalClose" :isSelected="isSelected" />

    <ServiceViewModal ref="serviceViewModal" :service="service" @close="handleServiceViewClose" />

    <div
        class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex flex-col md:flex-row">
            <!-- Service Image -->
            <div class="md:w-2/5 relative">
                <slot name="favorite"></slot>
                <img :src="service.image_url || '/images/service-placeholder.jpg'" :alt="service.name"
                    class="w-full h-40 sm:h-48 md:h-full object-cover">
            </div>

            <!-- Content Container -->
            <div class="md:w-3/5 p-3 sm:p-4 flex flex-col">
                <!-- Category and Customizable Badge -->
                <div class="flex items-center justify-between mb-3">
                    <span v-if="service.category_name" class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                        {{ service.category_name }}
                    </span>

                    <div v-if="service.catering_service.is_customizable"
                        class="flex items-center text-xs sm:text-sm text-yellow-600">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Customizable
                    </div>
                </div>

                <!-- Price and Buffet Type -->
                <div class="flex items-center justify-between mb-3">
                    <div class="text-base sm:text-lg font-bold text-green-600">
                        {{ formatPrice(service.catering_service.price) }}
                        <span v-if="service.catering_service.price !== service.catering_service.package_price"
                            class="text-xs sm:text-sm font-normal text-gray-600 ml-1 sm:ml-1">
                            /person
                        </span>
                        <span v-else class="text-xs sm:text-sm font-normal text-gray-600 ml-1 sm:ml-1">
                            package
                        </span>
                    </div>

                    <!-- Rating -->
                    <div class="flex items-center text-xs sm:text-sm text-gray-500">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1 text-yellow-400 flex-shrink-0" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        {{ service.rating || 'No ratings yet' }}
                    </div>
                </div>

                <!-- Service Title and Description -->
                <div class="mb-3 flex-grow">
                    <h3 class="font-semibold text-gray-900 text-sm sm:text-base mb-2 line-clamp-1 leading-tight">
                        {{ service.name }}
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-600 line-clamp-2 leading-relaxed">
                        {{ service.description }}
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

                <!-- Action Buttons -->
                <div v-if="isDateAvailable" class="flex space-x-2 sm:space-x-3 pt-3 border-t border-gray-100">
                    <button @click="handleView"
                        class="flex-1 border border-gray-300 text-gray-700 py-2.5 sm:py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm hover:bg-gray-50 transition-colors font-medium min-h-[44px]">
                        View Details
                    </button>

                    <button @click="handleSelect" :class="{
                        'bg-blue-600 text-white hover:bg-blue-700': !hasSelections(),
                        'bg-green-600 text-white hover:bg-green-700': hasSelections() || isSelected
                    }"
                        class="flex-1 py-2.5 sm:py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm transition-colors flex items-center justify-center font-medium min-h-[44px]">
                        <span v-if="hasSelections() && isSelected" class="flex items-center">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Customize
                            <!-- Customize ({{ getSelectedDishCount() }}) -->
                        </span>
                        <span v-else-if="!hasSelections() && isSelected">Selected</span>
                        <span v-else>Select</span>
                    </button>
                </div>

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
    -webkit-line-camp: 1;
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
