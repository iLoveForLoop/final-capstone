<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    service: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close']);

// Modal visibility state
const isOpen = ref(false);
const isClosing = ref(false);

// Computed property to determine service type
const serviceType = computed(() => {
    if (props.service.catering_service) return 'catering';
    if (props.service.photography_service) return 'photography';
    // Add more service types here as needed
    return 'generic';
});

// Format price helper
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

// Get total dish count for catering
const getTotalDishCount = (dishes) => {
    if (!dishes || typeof dishes !== 'object') return 0;
    return Object.values(dishes).reduce((total, categoryDishes) => total + (categoryDishes?.length || 0), 0);
};

// Get service-specific data
const serviceData = computed(() => {
    switch (serviceType.value) {
        case 'catering':
            return props.service.catering_service;
        case 'photography':
            return props.service.photography_service;
        default:
            return {};
    }
});

// Get service type display name
const serviceTypeDisplay = computed(() => {
    switch (serviceType.value) {
        case 'catering':
            return 'Catering Service';
        case 'photography':
            return 'Photography Service';
        default:
            return 'Service';
    }
});

// Modal control methods
const openModal = () => {
    isOpen.value = true;
    isClosing.value = false;
};

const closeModal = () => {
    isClosing.value = true;
    setTimeout(() => {
        isOpen.value = false;
        isClosing.value = false;
        emit('close');
    }, 300);
};

const toggleModal = () => {
    if (isOpen.value) {
        closeModal();
    } else {
        openModal();
    }
};

defineExpose({
    openModal,
    closeModal,
    toggleModal
});
</script>

<template>
    <Transition name="modal-fade">
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black transition-opacity duration-300"
                :class="isClosing ? 'opacity-0' : 'opacity-50'" @click="closeModal"></div>

            <!-- Modal -->
            <Transition name="modal-scale">
                <div v-if="isOpen && !isClosing"
                    class="relative bg-white rounded-xl w-full max-w-3xl max-h-[90vh] flex flex-col shadow-xl overflow-hidden">
                    <!-- Modal Header -->
                    <div
                        class="px-4 md:px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white sticky top-0 z-10">
                        <div class="min-w-0">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 truncate">{{ service.name }}</h3>
                            <p class="text-xs sm:text-sm text-gray-600 mt-1 truncate">
                                {{ service.category?.name || serviceTypeDisplay }}
                            </p>
                        </div>
                        <button @click="closeModal"
                            class="text-gray-400 hover:text-gray-600 p-1 transition-colors duration-200 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="overflow-y-auto flex-1 px-4 md:px-6 py-4 md:py-6 space-y-6">
                        <!-- Service Image -->
                        <Transition name="fade">
                            <div v-if="service.image_url"
                                class="w-full h-40 sm:h-48 md:h-56 bg-gray-100 rounded-lg overflow-hidden">
                                <img :src="service.image_url" :alt="service.name"
                                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                            </div>
                        </Transition>

                        <!-- Price and Service-Specific Tags -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <div
                                    class="flex items-center bg-[#E6F4F6] text-[#239BA7] px-3 sm:px-4 py-2 rounded-lg font-semibold text-base sm:text-lg border border-[#239BA7]/30">
                                    {{ formatPrice(service.price) }}
                                    <div class="flex" v-if="service.category?.name === 'Catering'">
                                        <span
                                            v-if="service?.catering_service?.price !== service?.catering_service?.package_price"
                                            class="text-xs font-normal text-[#239BA7]/80 ml-1">/person</span>
                                        <span v-else class="text-xs font-normal text-[#239BA7]/80 ml-1">package</span>
                                    </div>
                                </div>

                                <!-- Catering-specific tags -->
                                <span v-if="serviceType === 'catering' && serviceData.buffet_type"
                                    class="inline-flex items-center text-xs sm:text-sm font-medium text-[#239BA7] bg-[#E6F4F6] px-2 sm:px-3 py-1 rounded-lg border border-[#239BA7]/30">
                                    {{ serviceData.buffet_type }}
                                </span>

                                <!-- Photography-specific tags -->
                                <span v-if="serviceType === 'photography' && serviceData.coverage_type"
                                    class="inline-flex items-center text-xs sm:text-sm font-medium text-[#239BA7] bg-[#E6F4F6] px-2 sm:px-3 py-1 rounded-lg border border-[#239BA7]/30">
                                    {{ serviceData.coverage_type }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center px-2 sm:px-3 py-1 rounded-lg text-xs sm:text-sm font-medium"
                                    :class="service.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    {{ service.is_available ? 'Available' : 'Unavailable' }}
                                </span>

                                <!-- Catering customizable tag -->
                                <span v-if="serviceType === 'catering' && serviceData.is_customizable"
                                    class="inline-flex items-center text-xs sm:text-sm px-2 sm:px-3 py-1 bg-[#F5FBFB] text-[#239BA7] rounded-lg font-medium border border-[#239BA7]/30">
                                    Customizable
                                </span>

                                <!-- Photography studio tag -->
                                <span v-if="serviceType === 'photography' && serviceData.studio_shoot_available"
                                    class="inline-flex items-center text-xs sm:text-sm px-2 sm:px-3 py-1 bg-[#F5FBFB] text-[#239BA7] rounded-lg font-medium border border-[#239BA7]/30">
                                    🏢 Studio Available
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <Transition name="fade">
                            <div v-if="service.description">
                                <h4 class="text-sm sm:text-base font-medium text-gray-900 mb-2">Description</h4>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ service.description }}</p>
                            </div>
                        </Transition>

                        <!-- Vendor Information -->
                        <Transition name="slide-up">
                            <div v-if="service.vendor" class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm sm:text-base font-medium text-gray-900 mb-3">Vendor Information</h4>
                                <div class="space-y-2">
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-0">
                                        <span class="text-xs sm:text-sm text-gray-600">Business Name</span>
                                        <span class="text-sm sm:text-base font-medium text-gray-900">{{
                                            service.vendor.business_name || service.vendor.name }}</span>
                                    </div>
                                    <div v-if="service.vendor.email"
                                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-0">
                                        <span class="text-xs sm:text-sm text-gray-600">Contact Email</span>
                                        <span class="text-xs sm:text-sm font-medium text-gray-900 break-all">{{
                                            service.vendor.email }}</span>
                                    </div>
                                    <div v-if="service.vendor.phone"
                                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-0">
                                        <span class="text-xs sm:text-sm text-gray-600">Phone</span>
                                        <span class="text-xs sm:text-sm font-medium text-gray-900">{{
                                            service.vendor.phone }}</span>
                                    </div>
                                </div>
                            </div>
                        </Transition>

                        <!-- CATERING SERVICE DETAILS -->
                        <TransitionGroup name="staggered-fade" tag="div" v-if="serviceType === 'catering'"
                            class="space-y-4">
                            <div key="catering-header">
                                <h4 class="text-sm sm:text-base font-medium text-gray-900">Catering Service Details</h4>
                            </div>

                            <!-- Basic Information -->
                            <div key="catering-basic" class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                <!-- Capacity -->
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <div class="flex items-center mb-2">
                                        <div
                                            class="w-5 h-5 sm:w-6 sm:h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-[#239BA7]" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <h5 class="text-xs sm:text-sm font-medium text-gray-900">Capacity</h5>
                                    </div>
                                    <p class="text-base sm:text-lg font-semibold text-gray-900">
                                        {{ serviceData.min_pax }}-{{ serviceData.max_pax }}
                                        <span class="text-xs sm:text-sm font-normal text-gray-500">pax</span>
                                    </p>
                                </div>

                                <!-- Delivery Fee -->
                                <div v-if="serviceData.delivery_fee" key="delivery-fee"
                                    class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <div class="flex items-center mb-2">
                                        <div
                                            class="w-5 h-5 sm:w-6 sm:h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-[#239BA7]" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path
                                                    d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                            </svg>
                                        </div>
                                        <h5 class="text-xs sm:text-sm font-medium text-gray-900">Delivery Fee</h5>
                                    </div>
                                    <p class="text-base sm:text-lg font-semibold text-gray-900">
                                        {{ formatPrice(serviceData.delivery_fee) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Menu -->
                            <div v-if="serviceData.dishes && Object.keys(serviceData.dishes).length"
                                key="catering-menu">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="text-xs sm:text-sm font-medium text-gray-900">Menu</h5>
                                    <span
                                        class="text-xs bg-[#E6F4F6] text-[#239BA7] px-2 py-1 rounded-full font-medium">
                                        {{ getTotalDishCount(serviceData.dishes) }} dishes total
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    <div v-for="(dishes, category) in serviceData.dishes" :key="category"
                                        class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <h6 class="text-xs sm:text-sm font-medium text-gray-900">{{ category }}</h6>
                                            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">
                                                {{ dishes?.length || 0 }} items
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <div v-for="dish in dishes" :key="dish"
                                                class="text-xs sm:text-sm text-gray-700 bg-white px-3 py-2 rounded border">
                                                {{ dish }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Service Areas -->
                            <div v-if="serviceData.service_area?.length" key="service-areas">
                                <h5 class="text-xs sm:text-sm font-medium text-gray-900 mb-3">Service Areas</h5>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="area in serviceData.service_area" :key="area"
                                        class="inline-flex items-center text-xs sm:text-sm px-2 sm:px-3 py-1 bg-[#E6F4F6] text-[#239BA7] rounded-lg border border-[#239BA7]/30">
                                        {{ area }}
                                    </span>
                                </div>
                            </div>

                            <!-- Specifications -->
                            <div v-if="serviceData.specifications?.length" key="catering-specs">
                                <h5 class="text-xs sm:text-sm font-medium text-gray-900 mb-3">What's Included</h5>
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <ul class="space-y-2">
                                        <li v-for="(spec, index) in serviceData.specifications" :key="index"
                                            class="flex items-start text-xs sm:text-sm text-gray-700">
                                            <span
                                                class="inline-block w-1.5 h-1.5 sm:w-2 sm:h-2 bg-[#239BA7] rounded-full mt-1.5 sm:mt-2 mr-2 sm:mr-3 flex-shrink-0"></span>
                                            <span>{{ spec }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </TransitionGroup>

                        <!-- PHOTOGRAPHY SERVICE DETAILS -->
                        <TransitionGroup name="staggered-fade" tag="div" v-if="serviceType === 'photography'"
                            class="space-y-4">
                            <div key="photography-header">
                                <h4 class="text-sm sm:text-base font-medium text-gray-900">Photography Service Details
                                </h4>
                            </div>

                            <!-- Basic Information Grid -->
                            <div key="photography-basic" class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                <!-- Duration -->
                                <div v-if="serviceData.duration" class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <div class="flex items-center mb-2">
                                        <div
                                            class="w-5 h-5 sm:w-6 sm:h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-[#239BA7]" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h5 class="text-xs sm:text-sm font-medium text-gray-900">Duration</h5>
                                    </div>
                                    <p class="text-base sm:text-lg font-semibold text-gray-900">{{ serviceData.duration
                                        }}</p>
                                </div>

                                <!-- Number of Photographers -->
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <div class="flex items-center mb-2">
                                        <div
                                            class="w-5 h-5 sm:w-6 sm:h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-[#239BA7]" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <h5 class="text-xs sm:text-sm font-medium text-gray-900">Photographers</h5>
                                    </div>
                                    <p class="text-base sm:text-lg font-semibold text-gray-900">
                                        {{ serviceData.number_of_photographers || 1 }}
                                        <span class="text-xs sm:text-sm font-normal text-gray-500">
                                            photographer{{ (serviceData.number_of_photographers || 1) > 1 ? 's' : '' }}
                                        </span>
                                    </p>
                                </div>

                                <!-- Hours of Coverage -->
                                <div v-if="serviceData.hours_of_coverage" class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <div class="flex items-center mb-2">
                                        <div
                                            class="w-5 h-5 sm:w-6 sm:h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-[#239BA7]" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </div>
                                        <h5 class="text-xs sm:text-sm font-medium text-gray-900">Coverage Hours</h5>
                                    </div>
                                    <p class="text-base sm:text-lg font-semibold text-gray-900">
                                        {{ serviceData.hours_of_coverage }}
                                        <span class="text-xs sm:text-sm font-normal text-gray-500">hours</span>
                                    </p>
                                </div>

                                <!-- Delivery Time -->
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <div class="flex items-center mb-2">
                                        <div
                                            class="w-5 h-5 sm:w-6 sm:h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-[#239BA7]" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <h5 class="text-xs sm:text-sm font-medium text-gray-900">Delivery Time</h5>
                                    </div>
                                    <p class="text-base sm:text-lg font-semibold text-gray-900">
                                        {{ serviceData.delivery_time_days || 30 }}
                                        <span class="text-xs sm:text-sm font-normal text-gray-500">days</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Equipment -->
                            <div v-if="serviceData.equipment?.length" key="photography-equipment">
                                <h5 class="text-xs sm:text-sm font-medium text-gray-900 mb-3">Equipment Included</h5>
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                        <span v-for="equipment in serviceData.equipment" :key="equipment"
                                            class="inline-flex items-center text-xs sm:text-sm px-2 sm:px-3 py-1 bg-white text-gray-700 rounded border">
                                            {{ equipment }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Deliverables -->
                            <div v-if="serviceData.deliverables?.length" key="photography-deliverables">
                                <h5 class="text-xs sm:text-sm font-medium text-gray-900 mb-3">Deliverables</h5>
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <ul class="space-y-2">
                                        <li v-for="deliverable in serviceData.deliverables" :key="deliverable"
                                            class="flex items-start text-xs sm:text-sm text-gray-700">
                                            <span
                                                class="inline-block w-1.5 h-1.5 sm:w-2 sm:h-2 bg-[#239BA7] rounded-full mt-1.5 sm:mt-2 mr-2 sm:mr-3 flex-shrink-0"></span>
                                            <span>{{ deliverable }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Specifications -->
                            <div v-if="serviceData.specifications?.length" key="photography-specs">
                                <h5 class="text-xs sm:text-sm font-medium text-gray-900 mb-3">Features & Specifications
                                </h5>
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <ul class="space-y-2">
                                        <li v-for="(spec, index) in serviceData.specifications" :key="index"
                                            class="flex items-start text-xs sm:text-sm text-gray-700">
                                            <span
                                                class="inline-block w-1.5 h-1.5 sm:w-2 sm:h-2 bg-[#239BA7] rounded-full mt-1.5 sm:mt-2 mr-2 sm:mr-3 flex-shrink-0"></span>
                                            <span>{{ spec }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </TransitionGroup>

                        <!-- GENERIC SERVICE DETAILS -->
                        <Transition name="fade" v-if="serviceType === 'generic'">
                            <div class="space-y-4">
                                <h4 class="text-sm sm:text-base font-medium text-gray-900">Service Details</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600">Additional service details will be displayed here
                                        based on the service type.</p>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-4 md:px-6 py-4 border-t border-gray-200 flex justify-end bg-white sticky bottom-0">
                        <button @click="closeModal"
                            class="px-4 sm:px-6 py-2 bg-[#239BA7] text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-[#1D8E99] transition-colors duration-200">
                            Close
                        </button>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
/* Modal transitions */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-scale-enter-active {
    transition: all 0.3s ease-out;
}

.modal-scale-leave-active {
    transition: all 0.3s ease-in;
}

.modal-scale-enter-from,
.modal-scale-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Slide up transition */
.slide-up-enter-active {
    transition: all 0.3s ease-out;
}

.slide-up-leave-active {
    transition: all 0.2s ease-in;
}

.slide-up-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

.slide-up-leave-to {
    opacity: 0;
}

/* Staggered fade for transition groups */
.staggered-fade-move,
.staggered-fade-enter-active,
.staggered-fade-leave-active {
    transition: all 0.3s ease;
}

.staggered-fade-enter-from,
.staggered-fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

.staggered-fade-leave-active {
    position: absolute;
}

/* Responsive text sizes */
@media (max-width: 640px) {
    .text-responsive {
        font-size: 0.875rem;
        /* 14px */
    }

    .text-responsive-lg {
        font-size: 1rem;
        /* 16px */
    }
}

/* Smooth scrolling */
.overflow-y-auto {
    scroll-behavior: smooth;
}

/* Hover effects */
button {
    transition: all 0.2s ease;
}

img {
    transition: transform 0.3s ease;
}
</style>
