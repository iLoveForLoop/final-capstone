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

const calendarEvents = ref([
    {
        title: 'Available',
        start: new Date(),
        end: new Date(new Date().setDate(new Date().getDate() + 5)),
        color: '#10b981',
        display: 'background',
    },
    {
        title: 'Booked - Wedding Event',
        start: new Date(new Date().setDate(new Date().getDate() + 6)),
        end: new Date(new Date().setDate(new Date().getDate() + 6)),
        color: '#ef4444',
    },
    {
        title: 'Available',
        start: new Date(new Date().setDate(new Date().getDate() + 7)),
        end: new Date(new Date().setDate(new Date().getDate() + 10)),
        color: '#10b981',
        display: 'background',
    },
    {
        title: 'Booked - Corporate Event',
        start: new Date(new Date().setDate(new Date().getDate() + 12)),
        end: new Date(new Date().setDate(new Date().getDate() + 12)),
        color: '#ef4444',
    },
])

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
    eventColor: '#3b82f6',
    height: 'auto',
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
                </CardHeader>
                <CardContent>
                    <div class="calendar-container">
                        <FullCalendar :options="calendarOptions" class="vendor-calendar" />
                        <div class="calendar-legend mt-4">
                            <div class="legend-item">
                                <div class="w-3 h-3 bg-green-200 rounded-full"></div>
                                <span class="text-sm">Available</span>
                            </div>
                            <div class="legend-item">
                                <div class="w-3 h-3 bg-red-200 rounded-full"></div>
                                <span class="text-sm">Booked</span>
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

/* Calendar */
.calendar-container {
    @apply bg-white rounded-xl overflow-hidden shadow-md p-2;
}

.vendor-calendar {
    @apply w-full;
}

:deep(.fc) {
    @apply text-sm;
}

:deep(.fc-toolbar-title) {
    @apply text-base font-semibold;
}

:deep(.fc-day) {
    @apply cursor-pointer;
}

:deep(.fc-day-other) {
    @apply bg-gray-50;
}

:deep(.fc-daygrid-day-number) {
    @apply p-2 text-sm;
}

:deep(.fc-event) {
    @apply cursor-pointer border-none text-xs p-1;
}

.calendar-legend {
    @apply flex justify-center gap-4 md:gap-6 text-xs;
}

.legend-item {
    @apply flex items-center gap-2;
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

/* Responsive */
@media (max-width: 640px) {
    :deep(.fc-header-toolbar) {
        @apply flex-col gap-2;
    }

    :deep(.fc-toolbar-chunk) {
        @apply mb-2;
    }
}
</style>
