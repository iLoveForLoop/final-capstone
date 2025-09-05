<script setup>
import { ref } from 'vue'
// Import shadcn-vue components
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/Components/ui/dialog'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Textarea } from '@/Components/ui/textarea'

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
    Mail,
} from 'lucide-vue-next'

const bookingForm = ref({
    date: '',
    time: '',
    duration: 4,
    packageId: '',
    location: '',
    notes: '',
    contactName: '',
    contactPhone: '',
    contactEmail: ''
})


const showBookingModal = ref(false)
const isFavorite = ref(false)

const closeBookingModal = () => {
    showBookingModal.value = false
    bookingForm.value = {
        date: '',
        time: '',
        duration: 4,
        packageId: '',
        location: '',
        notes: '',
        contactName: '',
        contactPhone: '',
        contactEmail: ''
    }
}


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
                        <p class="text-white/80 text-xl mt-2">
                            {{ vendor.categories.join(", ") }}
                        </p>
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
    <!-- Enhanced Booking Modal -->
    <Dialog v-model:open="showBookingModal">
        <DialogContent class="sm:max-w-[625px]">
            <DialogHeader>
                <DialogTitle>Book {{ vendor.name }}</DialogTitle>
                <DialogDescription>
                    Fill out the form below to send a booking request to the vendor.
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitBooking" class="grid gap-4 py-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="date">Event Date</Label>
                        <Input id="date" v-model="bookingForm.date" type="date" required />
                    </div>
                    <div class="grid gap-2">
                        <Label for="time">Event Time</Label>
                        <Input id="time" v-model="bookingForm.time" type="time" required />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="duration">Duration (hours)</Label>
                        <Input id="duration" v-model="bookingForm.duration" type="number" min="1" required />
                    </div>
                    <div class="grid gap-2">
                        <Label for="package">Select Package</Label>
                        <Select v-model="bookingForm.packageId" required>
                            <SelectTrigger>
                                <SelectValue placeholder="Choose a package" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="p in vendor.packages" :key="p.id" :value="p.id">
                                    {{ p.name }} - ₱{{ p.price.toLocaleString() }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="name">Contact Name</Label>
                    <Input id="name" v-model="bookingForm.contactName" type="text" required
                        placeholder="Your full name" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="phone">Contact Phone</Label>
                        <Input id="phone" v-model="bookingForm.contactPhone" type="tel" required
                            placeholder="+63 XXX XXX XXXX" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="email">Contact Email</Label>
                        <Input id="email" v-model="bookingForm.contactEmail" type="email" required
                            placeholder="your@email.com" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="location">Event Location</Label>
                    <Input id="location" v-model="bookingForm.location" type="text" required
                        placeholder="Enter event venue address" />
                </div>

                <div class="grid gap-2">
                    <Label for="notes">Additional Notes</Label>
                    <Textarea id="notes" v-model="bookingForm.notes"
                        placeholder="Special requests, requirements, or additional details..." />
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <Button type="button" variant="outline" @click="closeBookingModal">Cancel</Button>
                    <Button type="submit">
                        <Mail :size="18" class="mr-2" />
                        Send Booking Request
                    </Button>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>

<style></style>
