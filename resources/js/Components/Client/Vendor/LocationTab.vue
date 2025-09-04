<script setup>
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Clock, Globe, Info, MapPin, Navigation, Truck, Wrench } from 'lucide-vue-next';


defineProps({
    vendor: {
        type: Object
    }
})
</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
        <!-- Map Placeholder -->
        <Card class="map-container">
            <CardContent class="pt-6">
                <div class="map-placeholder">
                    <MapPin :size="64" class="text-indigo-300 mb-4 md:mb-6" />
                    <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-3">Interactive Map
                    </h3>
                    <p class="text-gray-600 text-base md:text-lg">{{ vendor.address }}</p>
                    <Button class="mt-4 md:mt-6 bg-white">
                        <Navigation :size="18" class="mr-2" />
                        Get Directions
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Location Details -->
        <div class="space-y-6 md:space-y-8">
            <Card>
                <CardHeader>
                    <CardTitle>Service Information</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4 md:space-y-6">
                    <div class="location-detail">
                        <MapPin :size="20" class="text-indigo-600 flex-shrink-0" />
                        <div>
                            <span class="detail-label">Primary Location</span>
                            <span class="detail-value">{{ vendor.location }}</span>
                        </div>
                    </div>
                    <div class="location-detail">
                        <Globe :size="20" class="text-indigo-600 flex-shrink-0" />
                        <div>
                            <span class="detail-label">Service Radius</span>
                            <span class="detail-value">{{ vendor.serviceRadius }} km
                                coverage</span>
                        </div>
                    </div>
                    <div class="location-detail">
                        <MapPin :size="20" class="text-indigo-600 flex-shrink-0" />
                        <div>
                            <span class="detail-label">Full Address</span>
                            <span class="detail-value">{{ vendor.address }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Service Features</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="feature-list">
                        <div class="feature-item">
                            <Truck :size="18" class="text-green-600 flex-shrink-0" />
                            <span class="text-sm md:text-base">Own transportation
                                available</span>
                        </div>
                        <div class="feature-item">
                            <Wrench class="h-5 w-5 text-green-600" />
                            <span class="text-sm md:text-base">Setup and breakdown
                                included</span>
                        </div>
                        <div class="feature-item">
                            <Clock :size="18" class="text-blue-600 flex-shrink-0" />
                            <span class="text-sm md:text-base">Flexible scheduling
                                options</span>
                        </div>
                        <div class="feature-item">
                            <Info :size="18" class="text-orange-600 flex-shrink-0" />
                            <span class="text-sm md:text-base">Delivery fee may apply for
                                distant locations</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>



<style scoped>
/* Location Styles */
.map-placeholder {
    @apply bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 flex flex-col items-center justify-center text-center min-h-80 md:min-h-96 border-2 border-dashed border-indigo-200;
}

.location-detail {
    @apply flex items-center gap-3 md:gap-4;
}

.detail-label {
    @apply font-semibold text-gray-700 block text-sm md:text-base;
}

.detail-value {
    @apply text-gray-600 block text-sm md:text-base;
}

.feature-list {
    @apply space-y-3 md:space-y-4;
}

.feature-item {
    @apply flex items-center gap-3 text-gray-700 text-sm md:text-base;
}
</style>
