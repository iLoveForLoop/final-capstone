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
    <div class="min-h-screen bg-gray-50 py-4 sm:py-8 px-4 sm:px-6">
        <div class="max-w-2xl mx-auto">
            <!-- Progress Indicator -->
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs sm:text-sm font-medium text-gray-600 uppercase tracking-wide">
                        Step {{ currentStep + 1 }} of {{ props.selectedCategories.length }}
                    </span>
                    <span class="text-xs sm:text-sm text-gray-500">
                        {{ Math.round(((currentStep + 1) / props.selectedCategories.length) * 100) }}%
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-700 ease-out"
                        :style="{ width: `${((currentStep + 1) / props.selectedCategories.length) * 100}%` }"></div>
                </div>
            </div>

            <!-- Header -->
            <div class="text-center mb-6 sm:mb-8">
                <h1 v-if="currentCategory" class="text-xl sm:text-2xl lg:text-3xl font-semibold text-gray-900 mb-2">
                    {{ currentCategory.name }}
                </h1>
                <p class="text-sm sm:text-base text-gray-600">Select your preferred service</p>
            </div>

            <!-- Search Bar -->
            <div class="mb-6 sm:mb-8">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                        class="block w-full pl-10 pr-4 py-3 text-sm sm:text-base border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        placeholder="Search services...">
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex flex-col items-center justify-center py-12 sm:py-16">
                <div class="w-8 h-8 border-2 border-gray-300 border-t-blue-600 rounded-full animate-spin mb-4"></div>
                <p class="text-sm sm:text-base text-gray-600">Loading services...</p>
            </div>

            <!-- Error State -->
            <div v-else-if="errorMessage" class="mb-6 sm:mb-8">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 sm:p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm sm:text-base font-medium text-red-900 mb-1">Error loading services</h3>
                            <p class="text-xs sm:text-sm text-red-700 mb-3">{{ errorMessage }}</p>
                            <button @click="fetchServices"
                                class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-800 text-xs sm:text-sm font-medium rounded-md transition-colors">
                                Try Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services List -->
            <div v-else-if="services.length" class="space-y-3 sm:space-y-4 mb-8 sm:mb-12">
                <div v-for="service in services" :key="service.id"
                    class="bg-white border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-sm transition-all duration-200 cursor-pointer"
                    :class="{
                        'border-blue-500 bg-blue-50/30 shadow-sm': selectedServices.some(s =>
                            s.id === service.id && s.category_id === currentCategory.id)
                    }" @click="selectService(service.id)">

                    <label class="flex p-4 sm:p-5 cursor-pointer">
                        <!-- Radio Button -->
                        <div class="flex-shrink-0 mt-1">
                            <input type="radio" :name="'category_' + currentCategory.id"
                                :checked="selectedServices.some(s => s.id === service.id && s.category_id === currentCategory.id)"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 focus:ring-2" />
                        </div>

                        <!-- Content -->
                        <div class="ml-4 flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-start gap-4">


                                <!-- Service Details -->
                                <div class="flex-1 order-1 sm:order-2">



                                    <!-- Vendor -->
                                    <div class="flex items-center mb-3">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        <span class="text-xs sm:text-sm text-gray-600">{{ service.vendor?.name ||
                                            'Unknown vendor' }}</span>
                                    </div>

                                    <!-- Specialized Details -->
                                    <div v-if="service.catering_service || service.photography_service"
                                        class="border-t border-gray-100 pt-3 mt-3">

                                        <div v-if="service.catering_service" class="mb-3">

                                            <CateringSelectionCard :service="service" />
                                        </div>

                                        <div v-if="service.photography_service">

                                            <PhotographySelectionCard :service="service" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12 sm:py-16">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No services available</h3>
                <p class="text-sm sm:text-base text-gray-600 max-w-md mx-auto">We couldn't find any services for this
                    category at the moment.</p>
            </div>

            <!-- Navigation -->
            <div
                class="sticky bottom-0 bg-white border-t border-gray-200 p-4 -mx-4 sm:static sm:bg-transparent sm:border-0 sm:p-0 sm:mx-0">
                <button @click="nextStep" v-if="currentStep !== props.selectedCategories.length - 1"
                    :disabled="!selectedServices.some(s => s.category.id === currentCategory.id)"
                    class="w-full bg-blue-600 text-white text-sm sm:text-base font-semibold py-3 sm:py-4 px-6 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                    Continue
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
