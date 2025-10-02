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
    },
    eventDate: {
        type: String,
        default: null
    }
})

// Single array model for all selected services
const selectedServices = defineModel('selectedServices')
const selectedDishes = defineModel('selectedDishes')

const currentStep = ref(0)
const services = ref([])
const loading = ref(false)
const errorMessage = ref(null)
const searchQuery = ref('')

const currentCategory = computed(() => {
    return props.selectedCategories[currentStep.value] || null
})

// Filtered services based on search query
const filteredServices = computed(() => {
    if (!searchQuery.value.trim()) {
        return services.value
    }

    const query = searchQuery.value.toLowerCase().trim()

    return services.value.filter(service => {
        // Search by service name
        const serviceName = service.name?.toLowerCase() || ''

        // Search by business name (vendor.business_name)
        const businessName = service.vendor?.business_name?.toLowerCase() || ''

        // Search by description
        const description = service.description?.toLowerCase() || ''

        // Search by price (convert to string for partial matching)
        const price = service.price?.toString() || ''

        // Search by vendor name (fallback)
        const vendorName = service.vendor?.name?.toLowerCase() || ''

        // Search by category name
        const categoryName = service.category?.name?.toLowerCase() || ''

        return serviceName.includes(query) ||
            businessName.includes(query) ||
            description.includes(query) ||
            price.includes(query) ||
            vendorName.includes(query) ||
            categoryName.includes(query)
    })
})

const fetchServices = async () => {
    if (!currentCategory.value?.id) return

    loading.value = true
    errorMessage.value = null

    try {
        const response = await axios.get(`/api/services/${currentCategory.value.id}`, {
            params: { event_date: props.eventDate }
        })
        services.value = response.data.success ? response.data.data : []
        errorMessage.value = response.data.success ? null : (response.data.message || 'Failed to load services')
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Network error occurred'
        services.value = []
    } finally {
        loading.value = false
    }
}

// Clear search when changing categories
watch(currentCategory, () => {
    searchQuery.value = ''
    fetchServices()
})

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

// Clear search function
const clearSearch = () => {
    searchQuery.value = ''
}

// Initial fetch
fetchServices()

const nextStep = () => {
    if (currentStep.value < props.selectedCategories.length - 1) {
        currentStep.value++
    } else {
        console.log('Final selection:', selectedServices.value)
    }
}

//Check if service available
const isServiceAvailableOnDate = (service) => {
    return service.is_available_on_date ?? service.is_available
}
</script>

<template>
    <div class="h-full bg-white flex flex-col">
        <!-- Header with Progress -->
        <div class="sticky top-0 bg-white border-b border-gray-200 z-30 px-3 py-3 sm:px-6 sm:py-4">
            <!-- Progress Bar -->
            <div class="mb-3 sm:mb-4">
                <div class="flex items-center justify-between mb-1.5 sm:mb-2">
                    <span class="text-xs font-medium text-[#239BA7] uppercase tracking-wider">
                        Step {{ currentStep + 1 }} of {{ props.selectedCategories.length }}
                    </span>
                    <span class="text-xs sm:text-sm font-bold text-[#239BA7]">
                        {{ Math.round(((currentStep + 1) / props.selectedCategories.length) * 100) }}%
                    </span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-gradient-to-r from-[#239BA7] to-[#1AC8D9] h-1.5 rounded-full transition-all duration-500 ease-out"
                        :style="{ width: `${((currentStep + 1) / props.selectedCategories.length) * 100}%` }">
                    </div>
                </div>
            </div>

            <!-- Category Title and Continue Button -->
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <h1 v-if="currentCategory" class="text-lg sm:text-xl font-bold text-gray-900 truncate">
                        {{ currentCategory.name }}
                    </h1>
                    <p class="text-gray-600 text-xs sm:text-sm mt-1">Choose your preferred service</p>
                </div>

                <!-- Desktop Continue Button -->
                <button @click="nextStep" v-if="currentStep !== props.selectedCategories.length - 1"
                    :disabled="!selectedServices.some(s => s.category?.id === currentCategory.id || s.category_id === currentCategory.id)"
                    class="hidden sm:flex items-center bg-[#239BA7] text-white text-sm font-medium py-2.5 px-4 sm:px-5 rounded-lg hover:bg-[#1D8E99] focus:outline-none focus:ring-2 focus:ring-[#239BA7] focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm flex-shrink-0 ml-4">
                    Continue
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="px-3 py-3 sm:px-6 sm:py-4 border-b border-gray-100 bg-gray-50">
            <div class="relative max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" v-model="searchQuery"
                    class="block w-full pl-10 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#239BA7] focus:border-[#239BA7] transition-all bg-white"
                    placeholder="Search services...">

                <!-- Clear search button -->
                <button v-if="searchQuery" @click="clearSearch"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="h-4 w-4 text-gray-400 hover:text-gray-600 cursor-pointer" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Search Results Info -->
            <div v-if="searchQuery && !loading" class="text-left mt-2">
                <p class="text-xs text-gray-600">
                    {{ filteredServices.length }} result{{ filteredServices.length !== 1 ? 's' : '' }}
                    for "<span class="font-medium">{{ searchQuery }}</span>"
                </p>
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto px-3 py-3 sm:px-6 sm:py-4">
            <!-- Loading State -->
            <div v-if="loading" class="flex flex-col items-center justify-center py-8 sm:py-12">
                <div class="w-8 h-8 border-2 border-gray-300 border-t-[#239BA7] rounded-full animate-spin mb-3"></div>
                <p class="text-gray-600 text-sm font-medium">Loading services...</p>
            </div>

            <!-- Error State -->
            <div v-else-if="errorMessage" class="mb-4 sm:mb-6">
                <div class="bg-red-50 border border-red-100 rounded-lg p-3 sm:p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5 text-red-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium text-red-900 mb-1">Error loading services</h3>
                            <p class="text-xs text-red-700 mb-2 sm:mb-3">{{ errorMessage }}</p>
                            <button @click="fetchServices"
                                class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-800 text-xs font-medium rounded-lg transition-colors">
                                Try Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services List -->
            <div v-else-if="filteredServices.length" class="space-y-3 sm:space-y-4">
                <div v-for="service in filteredServices" :key="service.id"
                    class="bg-white rounded-lg border transition-all duration-200 overflow-hidden hover:shadow-sm"
                    :class="{
                        'border-[#239BA7] border-2 ring-1 ring-[#239BA7] ring-opacity-20': isServiceSelected(service),
                        'border-gray-200 hover:border-gray-300': !isServiceSelected(service)
                    }">

                    <!-- Selected Header Bar -->
                    <div v-if="isServiceSelected(service)"
                        class="bg-[#239BA7] text-white px-3 sm:px-4 py-2 text-xs font-medium">
                        <div class="flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Selected Service
                        </div>
                    </div>

                    <div class="p-3 sm:p-4">
                        <!-- Specialized Service Cards -->
                        <div v-if="service.catering_service || service.photography_service">
                            <div v-if="service.catering_service">
                                <CateringSelectionCard :service="service" :isSelected="isServiceSelected(service)"
                                    @select="selectService" v-model:selectedDishes="selectedDishes"
                                    :isDateAvailable="isServiceAvailableOnDate(service)" />
                            </div>
                            <div v-if="service.photography_service">
                                <PhotographySelectionCard :service="service" :isSelected="isServiceSelected(service)"
                                    @select="selectService" :isDateAvailable="isServiceAvailableOnDate(service)" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Search Results State -->
            <div v-else-if="searchQuery && !loading" class="text-center py-8 sm:py-12">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-2 sm:mb-3 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-1">No results found</h3>
                <p class="text-gray-600 text-xs mb-3 sm:mb-4">
                    No services match "<span class="font-medium">{{ searchQuery }}</span>"
                </p>
                <button @click="clearSearch"
                    class="inline-flex items-center px-3 py-1.5 bg-[#239BA7] hover:bg-[#1D8E99] text-white text-xs font-medium rounded-lg transition-colors">
                    Clear Search
                </button>
            </div>

            <!-- Empty State -->
            <div v-else-if="!loading && !searchQuery" class="text-center py-8 sm:py-12">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-2 sm:mb-3 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                    </svg>
                </div>
                <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-1">No services available</h3>
                <p class="text-gray-600 text-xs">We couldn't find any services for this category.</p>
            </div>
        </div>

        <!-- Mobile Continue Button -->
        <div v-if="currentStep !== props.selectedCategories.length - 1"
            class="sticky bottom-0 bg-white border-t border-gray-200 p-3 sm:p-4 sm:hidden">
            <button @click="nextStep"
                :disabled="!selectedServices.some(s => s.category?.id === currentCategory.id || s.category_id === currentCategory.id)"
                class="w-full bg-[#239BA7] text-white text-sm font-medium py-3 px-6 rounded-lg hover:bg-[#1D8E99] focus:outline-none focus:ring-2 focus:ring-[#239BA7] focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center justify-center min-h-[48px]">
                Continue
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
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

/* Smooth scrolling for modal content */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f7fafc;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #a0aec0;
}

/* Better touch targets for mobile */
@media (max-width: 640px) {
    button {
        min-height: 44px;
    }

    .min-h-\[48px\] {
        min-height: 48px;
    }
}
</style>
