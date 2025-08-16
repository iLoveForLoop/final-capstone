<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import CateringSelectionCard from './Vendor/Catering/CateringSelectionCard.vue'
import PhotographySelectionCard from './Vendor/Photograpy/PhotographySelectionCard.vue'

const props = defineProps({
    selectedCategories: {
        type: Array,
        required: true,
        default: () => []
    }
})

// Single array model for all selected services
const selectedServices = defineModel('selectedServices')
const currentStep = ref(0)
const services = ref([])
const loading = ref(false)
const errorMessage = ref(null)

const currentCategory = computed(() => {
    return props.selectedCategories[currentStep.value] || null
})

const fetchServices = async () => {
    if (!currentCategory.value?.id) return

    loading.value = true
    errorMessage.value = null

    try {
        const response = await axios.get(`/api/services/${currentCategory.value.id}`)
        services.value = response.data.success ? response.data.data : []
        errorMessage.value = response.data.success ? null : (response.data.message || 'Failed to load services')
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Network error occurred'
        services.value = []
    } finally {
        loading.value = false
    }
}

// Update selected service for current category
const selectService = (serviceId) => {
    // Remove any existing selection for this category
    const index = selectedServices.value.findIndex(
        s => s.category_id === currentCategory.value.id || s.category?.id === currentCategory.value.id
    )
    if (index !== -1) {
        selectedServices.value.splice(index, 1)
    }

    // Add new selection if a service was selected (not cleared)
    if (serviceId) {
        const service = services.value.find(s => s.id === serviceId)
        if (service) {
            selectedServices.value.push({ ...service })
        }
    }
}

// Check if service is selected
const isServiceSelected = (service) => {
    return selectedServices.value.some(s =>
        s.id === service.id && (s.category_id === currentCategory.value.id || s.category?.id === currentCategory.value.id)
    )
}

// Initial fetch and watch
fetchServices()
watch(currentCategory, fetchServices)

const nextStep = () => {
    if (currentStep.value < props.selectedCategories.length - 1) {
        currentStep.value++
    } else {
        console.log('Final selection:', selectedServices.value)
    }
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50/30 py-4 sm:py-6 px-4 sm:px-6">
        <div class="max-w-4xl mx-auto">
            <!-- Progress Indicator -->
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm sm:text-base font-semibold text-gray-700 uppercase tracking-wide">
                        Step {{ currentStep + 1 }} of {{ props.selectedCategories.length }}
                    </span>
                    <span class="text-sm sm:text-base text-blue-600 font-bold">
                        {{ Math.round(((currentStep + 1) / props.selectedCategories.length) * 100) }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-700 ease-out shadow-sm"
                        :style="{ width: `${((currentStep + 1) / props.selectedCategories.length) * 100}%` }">
                    </div>
                </div>
            </div>

            <!-- Header -->
            <div class="text-center mb-6 sm:mb-8">
                <h1 v-if="currentCategory" class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">
                    {{ currentCategory.name }}
                </h1>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">Choose your preferred service from the
                    options below</p>
            </div>

            <!-- Search Bar -->
            <div class="mb-6 sm:mb-8">
                <div class="relative max-w-md mx-auto">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                        class="block w-full pl-12 pr-4 py-4 text-base border border-gray-300 rounded-2xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all shadow-sm bg-white"
                        placeholder="Search services...">
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex flex-col items-center justify-center py-16 sm:py-20">
                <div class="w-12 h-12 border-3 border-gray-300 border-t-blue-600 rounded-full animate-spin mb-6"></div>
                <p class="text-base sm:text-lg text-gray-600 font-medium">Loading services...</p>
            </div>

            <!-- Error State -->
            <div v-else-if="errorMessage" class="mb-8 sm:mb-10">
                <div class="bg-red-50 border border-red-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-base sm:text-lg font-semibold text-red-900 mb-2">Error loading services</h3>
                            <p class="text-sm sm:text-base text-red-700 mb-4">{{ errorMessage }}</p>
                            <button @click="fetchServices"
                                class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-800 text-sm sm:text-base font-medium rounded-xl transition-colors">
                                Try Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services List -->
            <div v-else-if="services.length" class="space-y-6 sm:space-y-8 mb-8 sm:mb-12">
                <div v-for="service in services" :key="service.id"
                    class="relative bg-white rounded-2xl shadow-lg border transition-all duration-300 overflow-hidden"
                    :class="{
                        'border-green-400 shadow-green-100 ring-2 ring-green-200': isServiceSelected(service),
                        'border-gray-200 hover:border-gray-300 hover:shadow-xl': !isServiceSelected(service)
                    }">

                    <!-- Selected Header Bar -->
                    <div v-if="isServiceSelected(service)"
                        class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 sm:px-6 py-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-semibold">Selected Service</span>
                        </div>
                    </div>

                    <!-- Selection Badge for Mobile -->
                    <div v-if="isServiceSelected(service)" class="absolute top-4 right-4 z-20 sm:hidden">
                        <div class="bg-green-500 text-white rounded-full p-2 shadow-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6">
                        <!-- Mobile-First Service Header -->
                        <div class="mb-6">
                            <!-- Service Image for Mobile -->
                            <div class="w-full h-48 sm:h-56 rounded-xl overflow-hidden mb-4 relative shadow-md">
                                <img :src="service.image_url" :alt="service.name" class="w-full h-full object-cover">

                                <!-- Mobile Overlay Info -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent">
                                </div>
                                <div class="absolute bottom-4 left-4 right-4">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span
                                            class="px-3 py-1 bg-white/90 backdrop-blur-sm text-gray-800 text-sm font-medium rounded-full">
                                            {{ service.category.name }}
                                        </span>
                                        <span :class="{
                                            'bg-green-500 text-white': service.is_available,
                                            'bg-gray-500 text-white': !service.is_available
                                        }" class="px-3 py-1 text-sm font-medium rounded-full backdrop-blur-sm">
                                            {{ service.is_available ? 'Available' : 'Unavailable' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Service Title and Info -->
                            <div class="space-y-3">
                                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight">{{ service.name }}
                                </h3>
                                <p class="text-gray-600 text-base leading-relaxed line-clamp-2">{{ service.description
                                    }}</p>

                                <!-- Vendor Info -->
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                    <span class="font-medium">{{ service.vendor?.name || 'Unknown vendor' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Specialized Service Details -->
                        <div v-if="service.catering_service || service.photography_service" class="mb-6">
                            <div v-if="service.catering_service" class="mb-6">
                                <CateringSelectionCard :service="service" :isSelected="isServiceSelected(service)" />
                            </div>
                            <div v-if="service.photography_service" class="mb-6">
                                <PhotographySelectionCard :service="service" :isSelected="isServiceSelected(service)" />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-100">
                            <button
                                class="flex-1 px-6 py-4 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors text-base">
                                View Details
                            </button>

                            <button @click="selectService(service.id)" :class="{
                                'bg-green-600 text-white hover:bg-green-700': isServiceSelected(service),
                                'bg-blue-600 text-white hover:bg-blue-700': !isServiceSelected(service)
                            }"
                                class="flex-1 px-6 py-4 font-semibold rounded-xl transition-colors flex items-center justify-center text-base">

                                <span v-if="isServiceSelected(service)" class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Selected
                                </span>
                                <span v-else>Select Service</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16 sm:py-20">
                <div class="w-20 h-20 mx-auto mb-6 bg-gray-100 rounded-2xl flex items-center justify-center">
                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-3">No services available</h3>
                <p class="text-base sm:text-lg text-gray-600 max-w-md mx-auto">We couldn't find any services for this
                    category at the moment.</p>
            </div>

            <!-- Navigation -->
            <div
                class="sticky bottom-0 bg-white/95 backdrop-blur-lg border-t border-gray-200 p-4 -mx-4 sm:static sm:bg-transparent sm:border-0 sm:p-0 sm:mx-0 sm:backdrop-blur-none shadow-lg sm:shadow-none">
                <button @click="nextStep" v-if="currentStep !== props.selectedCategories.length - 1"
                    :disabled="!selectedServices.some(s => s.category?.id === currentCategory.id || s.category_id === currentCategory.id)"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white text-base sm:text-lg font-bold py-4 px-8 rounded-2xl hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-xl flex items-center justify-center">
                    Continue
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
