<script setup>
import { ref } from 'vue'

import { Button } from '@/Components/ui/button'
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card'
import { Dialog, DialogContent } from '@/Components/ui/dialog'

import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'

import { Expand, CheckCircle, ChevronLeft, ChevronRight, Phone, Mail, Globe } from 'lucide-vue-next'

const props = defineProps({
    vendor: {
        type: Object,
    },
})

const showGalleryModal = ref(false)
const galleryIndex = ref(0)

const handleDateClick = (arg) => {
    if (arg.event) return
    bookingForm.value.date = arg.dateStr
    openBookingModal()
}

const calendarOptions = {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    events: props.vendor.calendarEvents,
    dateClick: handleDateClick,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek',
    },
    eventDisplay: 'block',
    eventColor: '#ef4444',
    height: 'auto',
    eventDidMount: (info) => {
        info.el.style.border = 'none'
        info.el.style.borderLeft = '3px solid #dc2626'
        info.el.style.fontWeight = '500'
        info.el.style.borderRadius = '4px'
    },
}

const openGallery = (index) => {
    galleryIndex.value = index
    showGalleryModal.value = true
}

const prevImage = () => {
    galleryIndex.value =
        galleryIndex.value > 0 ? galleryIndex.value - 1 : props.vendor.gallery.length - 1
}

const nextImage = () => {
    galleryIndex.value =
        galleryIndex.value < props.vendor.gallery.length - 1 ? galleryIndex.value + 1 : 0
}
</script>

<template>
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 md:gap-8">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6 md:space-y-8">
            <!-- Photo Gallery -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Portfolio Gallery</CardTitle>
                    <span class="text-sm text-gray-500">{{ vendor.gallery.length }}
                        photos</span>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                        <div v-for="(image, index) in vendor.gallery" :key="index" @click="openGallery(index)"
                            class="gallery-item group cursor-pointer relative aspect-video rounded-xl overflow-hidden">
                            <img :src="image.url" :alt="image.caption"
                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" />
                            <div class="gallery-overlay">
                                <Expand :size="24"
                                    class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- About Section -->
            <Card class="bg-gradient-to-br from-gray-50 to-gray-100 border-0">
                <CardHeader>
                    <CardTitle>About {{ vendor.name }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-6">
                    <p class="text-gray-700 leading-relaxed text-base md:text-lg">{{
                        vendor.description }}</p>
                    <div>
                        <h3 class="text-xl md:text-2xl font-semibold text-gray-900 mb-6">
                            Specialties & Services</h3>
                        <div class="flex flex-col flex-wrap gap-3">
                            <div v-for="specialty in vendor.specialties" :key="specialty"
                                class="flex items-center gap-2 px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-full transition-colors duration-200">
                                <div class="w-2 h-2 bg-indigo-600 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-700">{{ specialty
                                }}</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Video Section -->
            <Card v-if="vendor.videoUrl">
                <CardHeader>
                    <CardTitle>Showcase Video</CardTitle>
                </CardHeader>
                <CardContent>

                    <div class="video-container">
                        <video :src="vendor.videoUrl" controls class="w-full rounded-2xl shadow-lg">
                        </video>
                    </div>
                </CardContent>
            </Card>

            <!-- Enhanced Calendar -->
            <Card>
                <CardHeader>
                    <CardTitle>Availability Calendar</CardTitle>
                    <p class="text-sm text-gray-600 mt-1">Check available dates for booking</p>
                </CardHeader>
                <CardContent>
                    <div class="calendar-container">
                        <FullCalendar :options="calendarOptions" class="vendor-calendar" />
                        <div class="calendar-info mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <p class="text-sm text-blue-700 font-medium">Click on available dates to book</p>
                            </div>
                        </div>
                        <div class="calendar-legend mt-4 flex justify-center gap-6">
                            <div class="legend-item flex items-center gap-2">
                                <div class="w-3 h-3 bg-white rounded-sm border border-gray-300"></div>
                                <span class="text-sm text-gray-600">Available</span>
                            </div>
                            <div class="legend-item flex items-center gap-2">
                                <div class="w-3 h-3 bg-red-500 rounded-sm"></div>
                                <span class="text-sm text-gray-600">Booked</span>
                            </div>
                            <div class="legend-item flex items-center gap-2">
                                <div class="w-3 h-3 bg-blue-100 rounded-sm border-2 border-blue-500"></div>
                                <span class="text-sm text-gray-600">Today</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Enhanced Sidebar -->
        <div class="space-y-6">
            <!-- Pricing Packages -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-xl sm:text-2xl font-bold text-gray-900">Service
                        Packages
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <Card v-for="p in vendor.packages" :key="p.id"
                        class="border-2 hover:border-primary transition-colors cursor-pointer">
                        <CardHeader class="pb-4">
                            <div class="flex justify-between items-start">
                                <CardTitle class="text-lg sm:text-xl">{{ p.name }}</CardTitle>
                                <div class="text-right">
                                    <span class="text-2xl sm:text-3xl font-bold text-primary">₱{{
                                        p.price.toLocaleString() }}</span>
                                    <p class="text-xs sm:text-sm text-muted-foreground">starting
                                        price</p>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <ul class="space-y-2 sm:space-y-3">
                                <li v-for="feature in p.features" :key="feature"
                                    class="flex items-center gap-3 text-sm sm:text-base">
                                    <CheckCircle class="h-4 w-4 text-green-500 flex-shrink-0" />
                                    <span>{{ feature }}</span>
                                </li>
                            </ul>
                        </CardContent>
                    </Card>
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle>Contact Information</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="contact-item group">
                        <div
                            class="contact-icon bg-green-100 text-green-600 group-hover:bg-green-600 group-hover:text-white">
                            <Phone :size="18" />
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Phone
                            </p>
                            <p class="text-gray-600 text-sm md:text-base">{{ vendor.phone }}</p>
                        </div>
                    </div>
                    <div class="contact-item group">
                        <div
                            class="contact-icon bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white">
                            <Mail :size="18" />
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Email
                            </p>

                            <p class="text-gray-600 text-sm md:text-base">{{ vendor.email }}</p>
                        </div>
                    </div>
                    <div class="contact-item group">
                        <div
                            class="contact-icon bg-purple-100 text-purple-600 group-hover:bg-purple-600 group-hover:text-white">
                            <Globe :size="18" />
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm md:text-base">Website
                            </p>
                            <p class="text-gray-600 text-sm md:text-base">Visit our site</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>

    <!-- Gallery Modal -->
    <Dialog v-model:open="showGalleryModal">
        <DialogContent class="max-w-4xl p-0 bg-transparent border-0 shadow-none">
            <div class="relative">
                <img :src="vendor.gallery[galleryIndex]?.url" :alt="vendor.gallery[galleryIndex]?.caption"
                    class="w-full h-auto max-h-[80vh] object-contain rounded-lg" />
                <Button @click="prevImage" variant="outline" size="icon"
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/50 text-white border-white/20 hover:bg-black/70">
                    <ChevronLeft class="h-4 w-4" />
                </Button>
                <Button @click="nextImage" variant="outline" size="icon"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/50 text-white border-white/20 hover:bg-black/70">
                    <ChevronRight class="h-4 w-4" />
                </Button>
            </div>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
/* Gallery */
.gallery-item {
    @apply relative overflow-hidden rounded-xl shadow-lg transition-all duration-300;
}

.gallery-overlay {
    @apply absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center;
}

/* Calendar Container */
.calendar-container {
    @apply bg-white rounded-xl overflow-hidden;
}

.vendor-calendar {
    @apply w-full;
}

/* Contact */
.contact-item {
    @apply flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-all duration-200 cursor-pointer;
}

.contact-icon {
    @apply w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200;
}

/* Video */
.video-container {
    @apply relative rounded-2xl overflow-hidden shadow-lg;
}

/* Calendar Legend */
.calendar-legend {
    @apply flex justify-center gap-6 text-sm;
}

.legend-item {
    @apply flex items-center gap-2;
}

/* Responsive */
@media (max-width: 768px) {
    .calendar-legend {
        @apply flex-col gap-3 items-start;
    }
}
</style>

<style>
/* Enhanced Calendar Styles */
.vendor-calendar .fc {
    @apply text-sm font-sans;
}

.vendor-calendar .fc-toolbar {
    @apply p-4 pb-3 border-b border-gray-200 bg-white;
}

.vendor-calendar .fc-toolbar-title {
    @apply text-lg font-semibold text-gray-900;
}

.vendor-calendar .fc-button {
    @apply bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400 hover:text-gray-900 transition-all duration-200 font-medium py-2 px-3 text-sm rounded-lg;
}

.vendor-calendar .fc-button:focus {
    @apply outline-none ring-2 ring-blue-500 ring-offset-2;
}

.vendor-calendar .fc-button-active {
    @apply bg-blue-600 border-blue-600 text-white hover:bg-blue-700 hover:border-blue-700;
}

.vendor-calendar .fc-today-button {
    @apply bg-blue-600 border-blue-600 text-white hover:bg-blue-700 hover:border-blue-700;
}

.vendor-calendar .fc-prev-button,
.vendor-calendar .fc-next-button {
    @apply hover:bg-gray-100 hover:border-gray-400 transition-all duration-200;
}

.vendor-calendar .fc-prev-button:hover,
.vendor-calendar .fc-next-button:hover {
    @apply bg-gray-100 border-gray-400 transform scale-105;
}

.vendor-calendar .fc-scrollgrid {
    @apply border border-gray-200 rounded-lg overflow-hidden shadow-sm;
}

.vendor-calendar .fc-col-header {
    @apply bg-gradient-to-b from-gray-50 to-gray-100;
}

.vendor-calendar .fc-col-header-cell {
    @apply border-r border-b border-gray-200 py-3 last:border-r-0;
}

.vendor-calendar .fc-col-header-cell-cushion {
    @apply text-sm font-semibold text-gray-700 py-2 no-underline;
}

.vendor-calendar .fc-daygrid-day {
    @apply border-r border-b border-gray-200 bg-white transition-all duration-200 cursor-pointer;
}

.vendor-calendar .fc-daygrid-day:hover {
    @apply bg-blue-50 transform scale-[1.02] z-10 relative shadow-md;
}

.vendor-calendar .fc-daygrid-day:last-child {
    @apply border-r-0;
}

.vendor-calendar .fc-daygrid-day-number {
    @apply p-2 text-sm font-medium text-gray-900 m-1;
}

.vendor-calendar .fc-day-other {
    @apply bg-gray-50;
}

.vendor-calendar .fc-day-other .fc-daygrid-day-number {
    @apply text-gray-400;
}

.vendor-calendar .fc-day-today {
    @apply bg-blue-50 relative;
}

.vendor-calendar .fc-day-today::before {
    content: '';
    @apply absolute inset-1 border-2 border-blue-500 rounded-lg pointer-events-none;
}

.vendor-calendar .fc-day-today .fc-daygrid-day-number {
    @apply text-blue-700 font-semibold;
}

.vendor-calendar .fc-event {
    @apply cursor-default border-none text-xs font-medium px-2 py-1 m-1 bg-red-500 text-white rounded shadow-sm;
    border-left: 3px solid #dc2626 !important;
}

.vendor-calendar .fc-event:hover {
    @apply bg-red-600 transform translate-y-[-1px] shadow-md;
}

.vendor-calendar .fc-day-disabled {
    @apply bg-gray-100 cursor-not-allowed;
}

.vendor-calendar .fc-day-disabled .fc-daygrid-day-number {
    @apply text-gray-400;
}

.vendor-calendar .fc-day-disabled:hover {
    @apply bg-gray-100 transform-none shadow-none;
}

.vendor-calendar .fc-daygrid-more-link {
    @apply text-xs text-blue-600 hover:text-blue-800 font-medium;
}

.vendor-calendar .fc-popover {
    @apply shadow-lg border border-gray-200 rounded-lg;
}

.vendor-calendar .fc-popover-header {
    @apply bg-gray-50 border-b border-gray-200 py-2 px-3;
}

.vendor-calendar .fc-popover-title {
    @apply text-sm font-semibold text-gray-700;
}

.vendor-calendar .fc-more-popover {
    @apply z-20;
}

/* Smooth animations */
.vendor-calendar .fc-daygrid-day,
.vendor-calendar .fc-event,
.vendor-calendar .fc-button {
    transition: all 0.2s ease-in-out;
}

/* Focus states for accessibility */
.vendor-calendar .fc-button:focus,
.vendor-calendar .fc-daygrid-day:focus {
    @apply outline-none ring-2 ring-blue-500 ring-offset-2;
}

/* Responsive Calendar */
@media (max-width: 768px) {
    .vendor-calendar .fc-header-toolbar {
        @apply flex-col gap-3;
    }

    .vendor-calendar .fc-toolbar-chunk {
        @apply mb-2 w-full justify-center;
    }

    .vendor-calendar .fc-toolbar-title {
        @apply text-base text-center;
    }

    .vendor-calendar .fc-button {
        @apply text-xs px-2 py-1;
    }

    .vendor-calendar .fc-daygrid-day-number {
        @apply text-xs p-1 m-0.5;
    }

    .vendor-calendar .fc-event {
        @apply text-xs px-1 py-0.5 m-0.5;
    }
}

@media (max-width: 640px) {
    .vendor-calendar .fc-toolbar {
        @apply p-3;
    }

    .vendor-calendar .fc-col-header-cell-cushion {
        @apply text-xs py-1;
    }

    .vendor-calendar .fc-daygrid-day-number {
        @apply text-xs p-1;
    }
}
</style>
