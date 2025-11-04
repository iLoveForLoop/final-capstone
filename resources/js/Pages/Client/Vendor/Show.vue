<script setup>
import { ref, onMounted } from 'vue'
import { Star, MapPin, Home, Box, Scale } from 'lucide-vue-next'

import { Card } from '@/Components/ui/card'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs'

import Header from '@/Components/Client/Vendor/Header.vue'
import OverviewTab from '@/Components/Client/Vendor/OverviewTab.vue'
import ReviewsTab from '@/Components/Client/Vendor/ReviewsTab.vue'
import LocationTab from '@/Components/Client/Vendor/LocationTab.vue'
import ServicesTab from '@/Components/Client/Vendor/ServicesTab.vue'
import ClientNavbar from '@/Components/ClientNavbar.vue'
import LegalsTab from '@/Components/Client/Vendor/LegalsTab.vue'


const props = defineProps({
    vendor: {
        type: {
            Object
        }
    }
})

const activeTab = ref('overview')




onMounted(() => {

})
</script>

<template>
    <ClientNavbar />
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <!-- Enhanced Header Section -->
        <Header :vendor="vendor" />

        <!-- Enhanced Navigation Tabs -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 ">
            <Card class="rounded-2xl shadow-xl">
                <Tabs v-model="activeTab" class="w-full">
                    <TabsList class="w-full flex h-auto p-0 bg-transparent border-b border-gray-100">
                        <TabsTrigger value="overview" class="flex-1 px-4 md:px-5 py-3 md:py-4 font-medium rounded-none transition-all duration-200
               data-[state=active]:text-indigo-600 data-[state=active]:border-b-2 data-[state=active]:border-indigo-600
               data-[state=active]:bg-transparent hover:text-indigo-500 text-gray-500 hover:bg-gray-50">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <Home :size="16" class="transition-colors" />
                                <span class="text-xs md:text-sm">Overview</span>
                            </div>
                        </TabsTrigger>
                        <TabsTrigger value="services" class="flex-1 px-4 md:px-5 py-3 md:py-4 font-medium rounded-none transition-all duration-200
               data-[state=active]:text-indigo-600 data-[state=active]:border-b-2 data-[state=active]:border-indigo-600
               data-[state=active]:bg-transparent hover:text-indigo-500 text-gray-500 hover:bg-gray-50">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <Box :size="16" class="transition-colors" />
                                <span class="text-xs md:text-sm">Services</span>
                            </div>
                        </TabsTrigger>
                        <TabsTrigger value="reviews" class="flex-1 px-4 md:px-5 py-3 md:py-4 font-medium rounded-none transition-all duration-200
               data-[state=active]:text-indigo-600 data-[state=active]:border-b-2 data-[state=active]:border-indigo-600
               data-[state=active]:bg-transparent hover:text-indigo-500 text-gray-500 hover:bg-gray-50">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <Star :size="16" class="transition-colors" />
                                <span class="text-xs md:text-sm">Reviews</span>
                            </div>
                        </TabsTrigger>
                        <TabsTrigger value="legal" class="flex-1 px-4 md:px-5 py-3 md:py-4 font-medium rounded-none transition-all duration-200
               data-[state=active]:text-indigo-600 data-[state=active]:border-b-2 data-[state=active]:border-indigo-600
               data-[state=active]:bg-transparent hover:text-indigo-500 text-gray-500 hover:bg-gray-50">
                            <div class="flex flex-col items-center justify-center gap-1">

                                <Scale :size="16" class="transition-colors" />
                                <span class="text-xs md:text-sm">Legal</span>
                            </div>
                        </TabsTrigger>
                        <TabsTrigger value="location" class="flex-1 px-4 md:px-5 py-3 md:py-4 font-medium rounded-none transition-all duration-200
               data-[state=active]:text-indigo-600 data-[state=active]:border-b-2 data-[state=active]:border-indigo-600
               data-[state=active]:bg-transparent hover:text-indigo-500 text-gray-500 hover:bg-gray-50">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <MapPin :size="16" class="transition-colors" />
                                <span class="text-xs md:text-sm">Location</span>
                            </div>
                        </TabsTrigger>

                    </TabsList>

                    <!-- Tab Content -->
                    <div class="p-4 md:p-6 lg:p-8">
                        <TabsContent value="overview">
                            <OverviewTab :vendor="vendor" />
                        </TabsContent>

                        <TabsContent value="services">
                            <ServicesTab :vendorId="vendor.id" />
                        </TabsContent>

                        <TabsContent value="reviews">
                            <ReviewsTab :vendor="vendor" />
                        </TabsContent>

                        <TabsContent value="legal">
                            <LegalsTab />
                        </TabsContent>

                        <TabsContent value="location">
                            <LocationTab :vendor="vendor" />
                        </TabsContent>


                    </div>
                </Tabs>
            </Card>
        </div>
    </div>
</template>

<style scoped>
/* Responsive Design */
@media (max-width: 640px) {
    :deep(.fc-header-toolbar) {
        @apply flex-col gap-2;
    }

    :deep(.fc-toolbar-chunk) {
        @apply mb-2;
    }
}
</style>
