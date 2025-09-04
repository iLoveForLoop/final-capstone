<script setup>
import { ref } from 'vue'

defineProps({
    vendor: {
        type: Object,
    },
})

import {
    ArrowLeft,
    Star,
    MapPin,
    Clock,
    Calendar,
    CalendarCheck,
    Shield,
    Heart,
    MessageCircle,
    Check,
} from 'lucide-vue-next'

const showBookingModal = ref(false)
const isFavorite = ref(false)

const submitBooking = () => {
    console.log('Booking submitted:', bookingForm.value)
    alert('Booking request sent! The vendor will respond within 2 hours.')
    closeBookingModal()
}

const sendMessage = () => {
    alert('Message feature would open here')
}

const goBack = () => {
    console.log('Navigating back to search')
}

const toggleFavorite = () => {
    isFavorite.value = !isFavorite.value
}

const openBookingModal = () => {
    showBookingModal.value = true
}

// Import shadcn-vue components
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar'
</script>


<template>
    <div class="relative overflow-hidden">
        <!-- Simple Background -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 to-slate-800"></div>

        <!-- Header Content -->
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-12">
            <!-- Back Button -->
            <Button @click="goBack" class="mb-8 text-white bg-transparent">
                <ArrowLeft class="mr-2 h-4 w-4" />
                Back
            </Button>

            <!-- Vendor Info Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center text-white">
                <!-- Avatar Section -->
                <div class="lg:col-span-3 flex justify-center lg:justify-start">
                    <div class="relative">
                        <Avatar class="w-32 h-32 md:w-40 md:h-40 border-4 border-white/30 shadow-2xl">
                            <AvatarImage :src="vendor.avatar" :alt="vendor.name" />
                            <AvatarFallback>SS</AvatarFallback>
                        </Avatar>
                        <!-- Verified Badge -->
                        <Badge v-if="vendor.verified"
                            class="absolute -bottom-3 -right-3 bg-green-500 text-white rounded-full w-10 h-10 md:w-12 md:h-12 flex items-center justify-center border-4 border-white shadow-lg p-0">
                            <Check :size="16" />
                        </Badge>
                        <!-- Status Indicator -->
                        <Badge class="absolute -top-2 -left-2"
                            :class="vendor.available ? 'bg-green-500' : 'bg-red-500'">
                            {{ vendor.available ? 'Available' : 'Busy' }}
                        </Badge>
                    </div>
                </div>

                <!-- Vendor Details -->
                <div class="lg:col-span-6 text-center lg:text-left space-y-6">
                    <div>
                        <h1 class="text-4xl font-bold">{{ vendor.name }}</h1>
                        <p class="text-white/80 text-xl mt-2">{{ vendor.category }}</p>
                    </div>

                    <!-- Rating & Location -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 items-center lg:items-start justify-center lg:justify-start">
                        <div class="flex items-center gap-3">
                            <div class="flex gap-1">
                                <Star v-for="n in 5" :key="n"
                                    :class="['h-5 w-5 text-yellow-400', n <= vendor.rating ? 'fill-current' : 'opacity-30']" />
                            </div>
                            <span class="font-medium text-lg">{{ vendor.rating }}</span>
                            <span class="text-white/60">({{ vendor.reviewCount }} reviews)</span>
                        </div>
                        <div class="flex items-center gap-2 text-white/80">
                            <MapPin class="h-4 w-4" />
                            <span>{{ vendor.location }}</span>
                        </div>
                    </div>

                    <!-- Simple Stats -->
                    <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                        <span class="flex px-4 py-2 bg-white/10 text-white text-sm rounded-full">
                            <Clock class="h-4 w-4 mr-2" />
                            {{ vendor.responseTime }} response
                        </span>
                        <span class="flex px-4 py-2 bg-white/10 text-white text-sm rounded-full">
                            <CalendarCheck class="h-4 w-4 mr-2" />
                            {{ vendor.completedEvents }}+ Events
                        </span>
                        <span v-if="vendor.verified" class="flex px-4 py-2 bg-white/10 text-white text-sm rounded-full">
                            <Shield class="h-4 w-4 mr-2" />
                            Verified Vendor
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="lg:col-span-3 flex flex-col gap-4">
                    <Button @click="openBookingModal" size="lg"
                        class="w-full bg-white text-slate-900 hover:bg-slate-100 font-semibold">
                        <Calendar class="h-5 w-5 mr-2" />
                        Book Now
                    </Button>
                    <div class="flex gap-3">
                        <Button @click="sendMessage" variant="ghost"
                            class="bg-white flex-1 border-white text-slate-900 hover:bg-white/90 hover:text-slate-900">
                            <MessageCircle class="h-4 w-4 mr-2" />
                            Message
                        </Button>
                        <Button @click="toggleFavorite" variant="ghost" size="icon"
                            class="bg-white border-white hover:bg-white/90 hover:text-slate-900"
                            :class="isFavorite ? 'text-red-400' : 'text-slate-900'">
                            <Heart :class="['h-5 w-5', isFavorite ? 'fill-current' : '']" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style></style>
