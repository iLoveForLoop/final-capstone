<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

defineProps({
    service: {
        type: Object
    }
})

const showDetails = ref(false);

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const getTotalDishCount = (dishes) => {
    if (!dishes || typeof dishes !== 'object') return 0;
    return Object.values(dishes).reduce((total, categoryDishes) => total + (categoryDishes?.length || 0), 0);
};

const toggleDetails = () => {
    showDetails.value = !showDetails.value;
};
</script>

<template>
    <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-5 hover:shadow-sm transition-shadow">
        <!-- Header with Price and Buffet Type -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div
                    class="bg-[#E6F4F6] text-[#239BA7] px-3 py-1.5 rounded-lg font-semibold text-base border border-[#239BA7]/30">
                    {{ formatPrice(service.catering_service.price) }}<span
                        v-if="service.catering_service.price !== service.catering_service.package_price"
                        class="text-xs font-normal text-[#239BA7]/80 ml-1">/person</span> <span v-else
                        class="text-xs font-normal text-[#239BA7]/80 ml-1">package</span>
                </div>

                <span v-if="service.catering_service.buffet_type"
                    class="inline-flex items-center text-xs font-medium text-[#239BA7] bg-[#E6F4F6] px-2.5 py-1 rounded-lg border border-[#239BA7]/30">
                    {{ service.catering_service.buffet_type }}
                </span>
            </div>

            <div v-if="service.catering_service.is_customizable" class="flex-shrink-0">
                <span
                    class="inline-flex items-center text-xs px-2.5 py-1 bg-[#F5FBFB] text-[#239BA7] rounded-lg font-medium border border-[#239BA7]/30">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    Customizable
                </span>
            </div>
        </div>

        <!-- Service Title and Description -->
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-1.5">{{ service.name }}</h3>
            <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">{{ service.description }}</p>
        </div>

        <!-- Key Information Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <!-- Capacity -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2">
                        <svg class="h-3.5 w-3.5 text-[#239BA7]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Capacity</div>
                        <div class="text-base font-semibold text-gray-900">
                            {{ service.catering_service.min_pax }}-{{ service.catering_service.max_pax }}
                            <span class="text-xs font-normal text-gray-500">pax</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Fee -->
            <div v-if="service.catering_service.delivery_fee" class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2">
                        <svg class="h-3.5 w-3.5 text-[#239BA7]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Delivery Fee</div>
                        <div class="text-base font-semibold text-gray-900">
                            {{ formatPrice(service.catering_service.delivery_fee) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toggle Button -->
        <button @click="toggleDetails"
            class="w-full flex items-center justify-between text-sm font-medium text-[#239BA7]">
            <span>{{ showDetails ? 'Hide details' : 'Show more details' }}</span>
            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showDetails }" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Collapsible Content -->
        <div v-show="showDetails" class="space-y-4 pt-2">
            <!-- Menu Categories -->
            <div v-if="service.catering_service.dishes && Object.keys(service.catering_service.dishes).length"
                class="space-y-2">
                <h4 class="text-xs font-semibold text-gray-700 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Menu Includes
                    <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded-full ml-1.5">
                        {{ getTotalDishCount(service.catering_service.dishes) }} dishes
                    </span>
                </h4>

                <div class="bg-gray-50 rounded-lg p-2 border border-gray-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                        <div v-for="(dishes, category) in service.catering_service.dishes" :key="category"
                            class="flex items-center justify-between py-1 px-2 hover:bg-gray-100 rounded">
                            <span class="text-xs font-medium text-gray-700">{{ category }}</span>
                            <span class="text-xs text-gray-500 bg-white px-1.5 py-0.5 rounded-full">
                                {{ dishes?.length || 0 }} items
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Areas -->
            <div v-if="service.catering_service.service_area && service.catering_service.service_area.length"
                class="space-y-2">
                <h4 class="text-xs font-semibold text-gray-700 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Service Areas
                </h4>
                <div class="flex flex-wrap gap-1.5">
                    <span v-for="area in service.catering_service.service_area" :key="area"
                        class="inline-flex items-center text-xs px-2 py-0.5 bg-[#E6F4F6] text-[#239BA7] rounded-md border border-[#239BA7]/30">
                        {{ area }}
                    </span>
                </div>
            </div>

            <!-- Specifications -->
            <div v-if="service.catering_service.specifications?.length" class="space-y-2">
                <h4 class="text-xs font-semibold text-gray-700 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    What's Included
                </h4>
                <div class="bg-gray-50 rounded-lg p-2 border border-gray-100">
                    <ul class="space-y-1.5">
                        <li v-for="(spec, index) in service.catering_service.specifications" :key="index"
                            class="flex items-start text-xs text-gray-700">
                            <span
                                class="inline-block w-1.5 h-1.5 bg-[#239BA7] rounded-full mt-1.5 mr-2 flex-shrink-0"></span>
                            <span>{{ spec }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <!-- <div class="flex gap-3 pt-4 border-t border-gray-100">
            <button
                class="flex-1 px-4 py-2.5 bg-gray-50 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors text-xs sm:text-sm">
                View Details
            </button>

            <button @click="$emit('selectService', service.id)"
                class="flex-1 px-4 py-2.5 bg-[#239BA7] text-white font-medium rounded-lg hover:bg-[#1D8E99] transition-colors text-xs sm:text-sm">
                Select Service
            </button>
        </div> -->
    </div>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.rotate-180 {
    transform: rotate(180deg);
}

/* Smooth transitions for interactive elements */
div {
    transition: all 0.15s ease-in-out;
}
</style>
