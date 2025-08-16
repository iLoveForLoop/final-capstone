<script setup>
import { router } from '@inertiajs/vue3';

defineProps({
    service: {
        type: Object
    }
})

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

// Helper function to get total dish count
const getTotalDishCount = (dishes) => {
    if (!dishes || typeof dishes !== 'object') return 0;
    return Object.values(dishes).reduce((total, categoryDishes) => total + (categoryDishes?.length || 0), 0);
};
</script>

<template>
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-6 shadow-sm hover:shadow-md transition-shadow">
        <!-- Header with Price and Buffet Type -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg font-semibold text-lg border border-blue-100">
                    {{ formatPrice(service.catering_service.price) }}<span
                        class="text-sm font-normal text-blue-600">/person</span>
                </div>

                <span v-if="service.catering_service.buffet_type"
                    class="inline-flex items-center text-sm font-medium text-purple-700 bg-purple-50 px-3 py-1.5 rounded-lg border border-purple-100">
                    {{ service.catering_service.buffet_type }}
                </span>
            </div>

            <div v-if="service.catering_service.is_customizable" class="flex-shrink-0">
                <span
                    class="inline-flex items-center text-sm px-3 py-1.5 bg-orange-50 text-orange-700 rounded-lg font-medium border border-orange-100">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    Customizable
                </span>
            </div>
        </div>

        <!-- Service Title and Description -->
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ service.name }}</h3>
            <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">{{ service.description }}</p>
        </div>

        <!-- Key Information Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Capacity -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                <div class="flex items-center mb-1">
                    <div class="w-7 h-7 bg-blue-50 rounded-lg flex items-center justify-center mr-2">
                        <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium uppercase tracking-wider">Capacity</div>
                        <div class="text-lg font-bold text-gray-900">
                            {{ service.catering_service.min_pax }}-{{ service.catering_service.max_pax }}
                            <span class="text-sm font-normal text-gray-500">pax</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Fee -->
            <div v-if="service.catering_service.delivery_fee" class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                <div class="flex items-center mb-1">
                    <div class="w-7 h-7 bg-green-50 rounded-lg flex items-center justify-center mr-2">
                        <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 font-medium uppercase tracking-wider">Delivery Fee</div>
                        <div class="text-lg font-bold text-gray-900">
                            {{ formatPrice(service.catering_service.delivery_fee) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Categories -->
        <div v-if="service.catering_service.dishes && Object.keys(service.catering_service.dishes).length"
            class="space-y-3">
            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Menu Includes
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full ml-2">
                    {{ getTotalDishCount(service.catering_service.dishes) }} dishes
                </span>
            </h4>

            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div v-for="(dishes, category) in service.catering_service.dishes" :key="category"
                        class="flex items-center justify-between py-1 px-2">
                        <span class="text-sm font-medium text-gray-700">{{ category }}</span>
                        <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">
                            {{ dishes?.length || 0 }} items
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Areas -->
        <div v-if="service.catering_service.service_area && service.catering_service.service_area.length"
            class="space-y-3">
            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Service Areas
            </h4>
            <div class="flex flex-wrap gap-2">
                <span v-for="area in service.catering_service.service_area" :key="area"
                    class="inline-flex items-center text-xs px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md border border-blue-100">
                    {{ area }}
                </span>
            </div>
        </div>

        <!-- Specifications -->
        <div v-if="service.catering_service.specifications?.length" class="space-y-3">
            <h4 class="text-sm font-semibold text-gray-700 flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                What's Included
            </h4>
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                <ul class="space-y-2">
                    <li v-for="(spec, index) in service.catering_service.specifications" :key="index"
                        class="flex items-start text-sm text-gray-700">
                        <span class="inline-block w-1.5 h-1.5 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                        <span>{{ spec }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth transitions for interactive elements */
div {
    transition: all 0.15s ease-in-out;
}
</style>
