<script setup>
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { MapPin, Navigation } from 'lucide-vue-next';
import VendorLocationMap from './VendorLocationMap.vue';

const props = defineProps({
    vendor: {
        type: Object
    }
})

const openDirections = () => {
    if (!props.vendor?.address) return;

    // Encode the address for Google Maps URL
    const encodedAddress = encodeURIComponent(props.vendor.address);

    // Open Google Maps with directions
    window.open(`https://www.google.com/maps/dir/?api=1&destination=${encodedAddress}`, '_blank');
}
</script>

<template>
    <Card class="border border-gray-200">
        <CardHeader class="pb-3">
            <CardTitle class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <MapPin :size="18" class="text-blue-600" />
                Location
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
            <VendorLocationMap :vendor="vendor" />

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-2">
                    <MapPin :size="16" class="text-gray-500" />
                    <span class="text-sm text-gray-700">{{ vendor.address }}</span>
                </div>

                <Button @click="openDirections" variant="default" size="sm"
                    class="bg-blue-600 hover:bg-blue-700 text-white">
                    <Navigation :size="14" class="mr-1" />
                    Get Directions
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
