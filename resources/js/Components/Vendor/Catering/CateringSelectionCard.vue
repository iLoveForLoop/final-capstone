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


</script>

<template>
    <div
        class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-50 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
        <!-- Service Image with Enhanced Overlay -->
        <div class="relative h-48 sm:h-56 md:h-64 w-full overflow-hidden">
            <img :src="service.image_url" :alt="service.name"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

            <!-- Enhanced Gradient Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-60 group-hover:opacity-40 transition-opacity duration-300">
            </div>

            <!-- Top Info Bar with Glass Effect -->
            <div
                class="absolute top-3 sm:top-4 left-3 sm:left-4 right-3 sm:right-4 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 z-10">
                <!-- Category Badge with Glass Effect -->
                <span
                    class="text-xs sm:text-sm font-semibold px-2 sm:px-3 py-1 sm:py-2 bg-white/95 backdrop-blur-sm text-gray-800 rounded-lg sm:rounded-xl shadow-lg border border-white/20 self-start">
                    {{ service.category.name }}
                </span>

                <!-- Availability Badge with Glow Effect -->
                <span :class="{
                    'bg-emerald-500 text-white shadow-emerald-500/30': service.is_available,
                    'bg-gray-500 text-white shadow-gray-500/30': !service.is_available
                }"
                    class="text-xs sm:text-sm font-medium px-2 sm:px-4 py-1 sm:py-2 rounded-lg sm:rounded-xl shadow-lg backdrop-blur-sm self-start">
                    {{ service.is_available ? 'Available' : 'Unavailable' }}
                </span>
            </div>

            <!-- Price Badge - Bottom Right -->
            <div class="absolute bottom-3 sm:bottom-4 right-3 sm:right-4 z-10">
                <div
                    class="bg-white/95 backdrop-blur-sm rounded-lg sm:rounded-xl px-3 sm:px-4 py-1.5 sm:py-2 shadow-lg border border-white/20">
                    <span class="text-sm sm:text-lg font-bold text-gray-900">{{
                        formatPrice(service.catering_service.price) }}</span>
                    <span class="text-xs sm:text-sm text-gray-600">/person</span>
                </div>
            </div>
        </div>

        <!-- Enhanced Service Details -->
        <div class="p-4 sm:p-6">
            <!-- Title Section -->
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-3 sm:mb-4 space-y-2 sm:space-y-0">
                <div class="flex-1 sm:mr-4">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 line-clamp-2 leading-tight">{{
                        service.name }}</h3>
                    <span v-if="service.catering_service.buffet_type"
                        class="inline-flex items-center text-xs sm:text-sm font-medium text-indigo-700 bg-indigo-50 px-2 sm:px-3 py-1 rounded-lg border border-indigo-100">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
                        </svg>
                        {{ service.catering_service.buffet_type }}
                    </span>
                </div>
            </div>

            <!-- Description -->
            <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-5 line-clamp-2 leading-relaxed">{{
                service.description }}</p>

            <!-- Enhanced Key Features Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-5">
                <!-- Capacity -->
                <div
                    class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Capacity</p>
                        <p class="text-sm font-semibold text-gray-900">{{ service.catering_service.min_pax }}-{{
                            service.catering_service.max_pax }} pax</p>
                    </div>
                </div>

                <!-- Lead Time -->
                <div
                    class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition-colors">
                    <div class="flex-shrink-0 w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="h-4 w-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Lead Time</p>
                        <p class="text-sm font-semibold text-gray-900">{{ service.catering_service.lead_time_days }}
                            days</p>
                    </div>
                </div>

                <!-- Delivery Fee -->
                <div v-if="service.catering_service.delivery_fee"
                    class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-gray-100 transition-colors sm:col-span-2">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Delivery Fee</p>
                        <p class="text-sm font-semibold text-gray-900">{{
                            formatPrice(service.catering_service.delivery_fee) }}</p>
                    </div>
                </div>
            </div>

            <!-- Service Areas with Enhanced Design -->
            <div v-if="service.catering_service.service_area && service.catering_service.service_area.length"
                class="mb-4 sm:mb-5">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
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
                        class="inline-flex items-center text-xs sm:text-sm px-2 sm:px-3 py-1 bg-blue-50 text-blue-700 rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors">
                        {{ area }}
                    </span>
                </div>
            </div>

            <!-- Menu Preview with Enhanced Layout -->
            <div v-if="service.catering_service.dishes && service.catering_service.dishes.length" class="mb-4 sm:mb-5">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Menu Highlights
                </h4>
                <div class="flex flex-wrap gap-2">
                    <span v-for="(dish, index) in service.catering_service.dishes.slice(0, 3)" :key="index"
                        class="inline-flex items-center text-xs sm:text-sm px-2 sm:px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-100 hover:bg-emerald-100 transition-colors">
                        {{ dish }}
                    </span>
                    <span v-if="service.catering_service.dishes.length > 3"
                        class="inline-flex items-center text-xs sm:text-sm px-2 sm:px-3 py-1 bg-gray-100 text-gray-600 rounded-lg border border-gray-200 hover:bg-gray-200 transition-colors cursor-pointer">
                        +{{ service.catering_service.dishes.length - 3 }} more
                    </span>
                </div>
            </div>

            <!-- Special Features Row -->
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-gray-100 space-y-3 sm:space-y-0">
                <!-- Customizable Badge -->
                <div v-if="service.catering_service.is_customizable" class="flex-shrink-0">
                    <span
                        class="inline-flex items-center text-xs sm:text-sm px-2 sm:px-3 py-1.5 sm:py-2 bg-gradient-to-r from-purple-50 to-pink-50 text-purple-700 rounded-lg border border-purple-100 font-medium">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Customizable Menu
                    </span>
                </div>

                <!-- Action Button -->
                <button
                    class="inline-flex items-center justify-center w-full sm:w-auto px-4 sm:px-6 py-2.5 sm:py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 hover:shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="hidden sm:inline">View Details</span>
                    <span class="sm:hidden">View</span>
                </button>
            </div>
        </div>
    </div>
</template>
