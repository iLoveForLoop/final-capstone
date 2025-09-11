<script setup>
import { Card, CardContent } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';

import {
    Clock,
    Star,
    Calendar,
    Phone,
    Camera,
    Video,
    Users,
    Heart,
    Search,
    Package,
    Loader2,
    AlertCircle
} from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import NewServiceCard from '../NewServiceCard.vue';

const props = defineProps({
    vendorId: {
        type: [String, Number],
        required: true
    }
})

const services = ref([])
const isLoading = ref(true)
const error = ref(null)

const fetchServices = async () => {
    try {
        isLoading.value = true
        error.value = null

        const response = await axios.get(`/client/vendor/services/${props.vendorId}`)

        if (response.data.success) {
            services.value = response.data.data
        } else {
            throw new Error(response.data.message || 'Failed to fetch services')
        }
    } catch (err) {
        console.error('Error fetching services:', err)
        error.value = err.response?.data?.message || err.message || 'Failed to load services'
    } finally {
        isLoading.value = false
    }
}

const retryFetch = () => {
    fetchServices()
}

onMounted(() => {
    fetchServices()
})
</script>

<template>
    <div class="space-y-6">
        <!-- Loading State -->
        <div v-if="isLoading" class="space-y-6">
            <!-- Loading Header -->
            <div class="flex items-center justify-between">
                <div class="h-6 bg-gray-200 rounded w-32 animate-pulse"></div>
                <div class="h-5 bg-gray-200 rounded w-20 animate-pulse"></div>
            </div>

            <!-- Loading Cards Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <Card v-for="i in 6" :key="i" class="animate-pulse">
                    <CardContent class="p-6 space-y-4">
                        <!-- Service Image Placeholder -->
                        <div class="h-48 bg-gray-200 rounded-lg"></div>

                        <!-- Content Placeholder -->
                        <div class="space-y-3">
                            <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                        </div>

                        <!-- Price and Rating Placeholder -->
                        <div class="flex justify-between items-center">
                            <div class="h-4 bg-gray-200 rounded w-20"></div>
                            <div class="h-6 bg-gray-200 rounded w-16"></div>
                        </div>

                        <!-- Features Placeholder -->
                        <div class="space-y-2">
                            <div class="h-3 bg-gray-200 rounded w-full"></div>
                            <div class="h-3 bg-gray-200 rounded w-4/5"></div>
                            <div class="h-3 bg-gray-200 rounded w-3/5"></div>
                        </div>

                        <!-- Buttons Placeholder -->
                        <div class="flex gap-3 pt-4">
                            <div class="h-9 bg-gray-200 rounded flex-1"></div>
                            <div class="h-9 bg-gray-200 rounded flex-1"></div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="flex flex-col items-center justify-center py-16 px-4">
            <div class="text-center max-w-md">
                <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <AlertCircle :size="32" class="text-red-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Unable to Load Services</h3>
                <p class="text-gray-600 mb-6">{{ error }}</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <Button @click="retryFetch" class="bg-indigo-600 hover:bg-indigo-700">
                        <Loader2 :size="16" class="mr-2" :class="{ 'animate-spin': isLoading }" />
                        Try Again
                    </Button>
                    <Button variant="outline" @click="$router.go(-1)">
                        Go Back
                    </Button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="!services.length" class="flex flex-col items-center justify-center py-16 px-4">
            <div class="text-center max-w-md">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <Package :size="32" class="text-gray-400" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Services Available</h3>
                <p class="text-gray-600 mb-6">This vendor hasn't added any services yet. Check back later or contact
                    them directly.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <Button class="bg-indigo-600 hover:bg-indigo-700">
                        <Phone :size="16" class="mr-2" />
                        Contact Vendor
                    </Button>
                    <Button variant="outline">
                        <Search :size="16" class="mr-2" />
                        Browse Other Vendors
                    </Button>
                </div>
            </div>
        </div>

        <!-- Services Content -->
        <div v-else class="space-y-6">
            <!-- Services Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Available Services</h2>
                <Badge variant="secondary" class="bg-indigo-100 text-indigo-800">
                    {{ services.length }} {{ services.length === 1 ? 'service' : 'services' }}
                </Badge>
            </div>

            <!-- Services Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <Card v-for="service in services" :key="service.id"
                    class="hover:shadow-md transition-all duration-200 hover:border-indigo-200">
                    <NewServiceCard :service="service" />
                </Card>
            </div>

            <!-- Additional Actions -->
            <div class="mt-12 p-6 bg-gray-50 rounded-xl border border-gray-200">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Need Something Custom?</h3>
                    <p class="text-gray-600 mb-4">Don't see exactly what you're looking for? Contact us to discuss
                        custom packages and special requirements.</p>
                    <Button class="bg-indigo-600 hover:bg-indigo-700">
                        <Phone :size="16" class="mr-2" />
                        Request Custom Quote
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Enhanced card styling */
.card {
    @apply border-gray-200 transition-all duration-200;
}

.card:hover {
    @apply border-indigo-200 shadow-md;
}

/* Loading animations */
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Smooth transitions */
* {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Empty state styling */
.empty-state-icon {
    @apply w-16 h-16 mx-auto mb-4 p-4 bg-gray-100 rounded-full;
}

/* Error state styling */
.error-state-icon {
    @apply w-16 h-16 mx-auto mb-4 p-4 bg-red-100 rounded-full;
}
</style>
