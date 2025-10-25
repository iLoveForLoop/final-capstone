<script setup>
import { ref, onMounted } from 'vue'
import { Star, MapPin, Home, Box } from 'lucide-vue-next'

import { Card } from '@/Components/ui/card'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs'

import Header from '@/Components/Client/Vendor/Header.vue'
import OverviewTab from '@/Components/Client/Vendor/OverviewTab.vue'
import ReviewsTab from '@/Components/Client/Vendor/ReviewsTab.vue'
import LocationTab from '@/Components/Client/Vendor/LocationTab.vue'
import ServicesTab from '@/Components/Client/Vendor/ServicesTab.vue'
import ClientNavbar from '@/Components/ClientNavbar.vue'


const props = defineProps({
    vendor: {
        type: {
            Object
        }
    }
})

const activeTab = ref('overview')


// const vendor = {
//     id: 1,
//     name: "Stellar Sounds",
//     categories: ["Sound System Provider"],
//     rating: 4.8,
//     reviewCount: 127,
//     location: "Quezon City, Metro Manila",
//     responseTime: "2 hours",
//     completedEvents: 250,
//     available: true,
//     verified: true,
//     avatar: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=200&h=200&fit=crop&crop=face",
//     phone: "+63 912 345 6789",
//     email: "info@stellarsounds.ph",
//     website: "https://www.stellarsounds.ph",
//     address: "123 Music Street, Quezon City, Metro Manila 1100",
//     serviceRadius: 25,

//     description: "Stellar Sounds is Metro Manila's premier audio equipment provider, specializing in high-quality sound systems for weddings, corporate events, and private celebrations. With over 8 years of experience, we pride ourselves on delivering crystal-clear audio experiences that make your events unforgettable. Our team of professional technicians ensures seamless setup and operation throughout your event.",

//     specialties: ["Wedding Ceremonies", "Corporate Events", "Live Music", "DJ Setup", "Wireless Microphones", "Outdoor Events"],

//     videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4",

//     gallery: [
//         { url: "https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=600&h=400&fit=crop", caption: "Wedding Ceremony Setup" },
//         { url: "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600&h=400&fit=crop", caption: "Corporate Event Audio" },
//         { url: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=600&h=400&fit=crop", caption: "Professional DJ Equipment" },
//         { url: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=600&h=400&fit=crop", caption: "Outdoor Event Setup" },
//         { url: "https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?w=600&h=400&fit=crop", caption: "Sound Mixing Console" },
//         { url: "https://images.unsplash.com/photo-1485846234645-a62644f84728?w=600&h=400&fit=crop", caption: "Wireless Microphone Systems" }
//     ],

//     packages: [
//         {
//             id: 1,
//             name: "Basic Package",
//             price: 8000,
//             features: ["2 Main Speakers", "1 Wireless Microphone", "Basic Audio Mixing", "4-hour Service", "Setup & Breakdown"]
//         },
//         {
//             id: 2,
//             name: "Premium Package",
//             price: 15000,
//             features: ["4 Main Speakers", "2 Wireless Microphones", "Professional DJ Mixer", "LED Lighting Effects", "8-hour Service", "Technical Support"]
//         },
//         {
//             id: 3,
//             name: "Deluxe Package",
//             price: 25000,
//             features: ["6 Premium Speakers", "4 Wireless Microphones", "Complete DJ Setup", "Dynamic LED Lighting", "Fog Machine", "Full-day Service", "Dedicated Technician"]
//         }
//     ],

//     ratingBreakdown: {
//         5: 89,
//         4: 23,
//         3: 8,
//         2: 4,
//         1: 3
//     },

//     reviews: [
//         {
//             id: 1,
//             name: "Maria Santos",
//             rating: 5,
//             date: "2024-01-15",
//             comment: "Absolutely amazing service! The sound quality was perfect for our wedding. Professional setup and the team was very accommodating. They went above and beyond our expectations.",
//             avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=60&h=60&fit=crop&crop=face",
//             photos: ["https://images.unsplash.com/photo-1519741497674-611481863552?w=200&h=150&fit=crop", "https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=200&h=150&fit=crop"]
//         },
//         {
//             id: 2,
//             name: "John Cruz",
//             rating: 5,
//             date: "2024-01-10",
//             comment: "Used them for our company anniversary. Great equipment, on-time setup, and excellent customer service. The audio quality was crystal clear throughout the entire event. Highly recommended!",
//             avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=60&h=60&fit=crop&crop=face"
//         },
//         {
//             id: 3,
//             name: "Anna Reyes",
//             rating: 4,
//             date: "2024-01-05",
//             comment: "Good service overall. Sound quality was great, though setup took a bit longer than expected. The team was professional and handled our requests well.",
//             avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=60&h=60&fit=crop&crop=face"
//         },
//         {
//             id: 4,
//             name: "Roberto Dela Cruz",
//             rating: 5,
//             date: "2023-12-28",
//             comment: "Stellar Sounds made our New Year's corporate party unforgettable! The DJ setup was professional and the lighting effects added the perfect atmosphere. Worth every peso!",
//             avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face"
//         }
//     ]
// }

// Calendar events


// Methods



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

                        <TabsContent value="reviews">
                            <ReviewsTab :vendor="vendor" />
                        </TabsContent>

                        <TabsContent value="location">
                            <LocationTab :vendor="vendor" />
                        </TabsContent>
                        <TabsContent value="services">
                            <ServicesTab :vendorId="vendor.id" />
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
