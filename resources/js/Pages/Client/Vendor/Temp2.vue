<script setup>
import { ref, computed, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import {
    ArrowLeft,
    Star,
    MapPin,
    Clock,
    Calendar,
    Shield,
    Heart,
    MessageCircle,
    CalendarPlus,
    CheckCircle,
    X,
    ChevronLeft,
    ChevronRight,
    Phone,
    Mail,
    Globe,
    Check,
    Truck,
    // Tool,
    Info,
    Expand,
    Camera,
    Video,
    Navigation,
    User,
    Home,
    Award
} from 'lucide-vue-next'

// Import shadcn-vue components
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/Components/ui/dialog'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar'
import { Progress } from '@/Components/ui/progress'

const activeTab = ref('overview')
const showBookingModal = ref(false)
const showGalleryModal = ref(false)
const galleryIndex = ref(0)
const isFavorite = ref(false)
const calendarApi = ref(null)

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

const vendor = {
    id: 1,
    name: "Stellar Sounds",
    category: "Sound System Provider",
    rating: 4.8,
    reviewCount: 127,
    location: "Quezon City, Metro Manila",
    responseTime: "2 hours",
    completedEvents: 250,
    available: true,
    verified: true,
    avatar: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=200&h=200&fit=crop&crop=face",
    phone: "+63 912 345 6789",
    email: "info@stellarsounds.ph",
    website: "https://www.stellarsounds.ph",
    address: "123 Music Street, Quezon City, Metro Manila 1100",
    serviceRadius: 25,

    description: "Stellar Sounds is Metro Manila's premier audio equipment provider, specializing in high-quality sound systems for weddings, corporate events, and private celebrations. With over 8 years of experience, we pride ourselves on delivering crystal-clear audio experiences that make your events unforgettable. Our team of professional technicians ensures seamless setup and operation throughout your event.",

    specialties: ["Wedding Ceremonies", "Corporate Events", "Live Music", "DJ Setup", "Wireless Microphones", "Outdoor Events"],

    videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4",

    gallery: [
        { url: "https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=600&h=400&fit=crop", caption: "Wedding Ceremony Setup" },
        { url: "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=600&h=400&fit=crop", caption: "Corporate Event Audio" },
        { url: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=600&h=400&fit=crop", caption: "Professional DJ Equipment" },
        { url: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=600&h=400&fit=crop", caption: "Outdoor Event Setup" },
        { url: "https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?w=600&h=400&fit=crop", caption: "Sound Mixing Console" },
        { url: "https://images.unsplash.com/photo-1485846234645-a62644f84728?w=600&h=400&fit=crop", caption: "Wireless Microphone Systems" },
        { url: "https://images.unsplash.com/photo-1485846234645-a62644f84728?w=600&h=400&fit=crop", caption: "Wireless Microphone Systems" },
        { url: "https://images.unsplash.com/photo-1485846234645-a62644f84728?w=600&h=400&fit=crop", caption: "Wireless Microphone Systems" }
    ],

    packages: [
        {
            id: 1,
            name: "Basic Package",
            price: 8000,
            features: ["2 Main Speakers", "1 Wireless Microphone", "Basic Audio Mixing", "4-hour Service", "Setup & Breakdown"]
        },
        {
            id: 2,
            name: "Premium Package",
            price: 15000,
            features: ["4 Main Speakers", "2 Wireless Microphones", "Professional DJ Mixer", "LED Lighting Effects", "8-hour Service", "Technical Support"]
        },
        {
            id: 3,
            name: "Deluxe Package",
            price: 25000,
            features: ["6 Premium Speakers", "4 Wireless Microphones", "Complete DJ Setup", "Dynamic LED Lighting", "Fog Machine", "Full-day Service", "Dedicated Technician"]
        }
    ],

    ratingBreakdown: {
        5: 89,
        4: 23,
        3: 8,
        2: 4,
        1: 3
    },

    reviews: [
        {
            id: 1,
            name: "Maria Santos",
            rating: 5,
            date: "2024-01-15",
            comment: "Absolutely amazing service! The sound quality was perfect for our wedding. Professional setup and the team was very accommodating. They went above and beyond our expectations.",
            avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=60&h=60&fit=crop&crop=face",
            photos: ["https://images.unsplash.com/photo-1519741497674-611481863552?w=200&h=150&fit=crop", "https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=200&h=150&fit=crop"]
        },
        {
            id: 2,
            name: "John Cruz",
            rating: 5,
            date: "2024-01-10",
            comment: "Used them for our company anniversary. Great equipment, on-time setup, and excellent customer service. The audio quality was crystal clear throughout the entire event. Highly recommended!",
            avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=60&h=60&fit=crop&crop=face"
        },
        {
            id: 3,
            name: "Anna Reyes",
            rating: 4,
            date: "2024-01-05",
            comment: "Good service overall. Sound quality was great, though setup took a bit longer than expected. The team was professional and handled our requests well.",
            avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=60&h=60&fit=crop&crop=face"
        },
        {
            id: 4,
            name: "Roberto Dela Cruz",
            rating: 5,
            date: "2023-12-28",
            comment: "Stellar Sounds made our New Year's corporate party unforgettable! The DJ setup was professional and the lighting effects added the perfect atmosphere. Worth every peso!",
            avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&h=60&fit=crop&crop=face"
        }
    ]
}

// Calendar events
const calendarEvents = ref([
    {
        title: 'Available',
        start: new Date(),
        end: new Date(new Date().setDate(new Date().getDate() + 5)),
        color: '#10b981',
        display: 'background'
    },
    {
        title: 'Booked - Wedding Event',
        start: new Date(new Date().setDate(new Date().getDate() + 6)),
        end: new Date(new Date().setDate(new Date().getDate() + 6)),
        color: '#ef4444'
    },
    {
        title: 'Available',
        start: new Date(new Date().setDate(new Date().getDate() + 7)),
        end: new Date(new Date().setDate(new Date().getDate() + 10)),
        color: '#10b981',
        display: 'background'
    },
    {
        title: 'Booked - Corporate Event',
        start: new Date(new Date().setDate(new Date().getDate() + 12)),
        end: new Date(new Date().setDate(new Date().getDate() + 12)),
        color: '#ef4444'
    }
])

// Methods
const goBack = () => {
    console.log('Navigating back to search')
}

const toggleFavorite = () => {
    isFavorite.value = !isFavorite.value
}

const openBookingModal = () => {
    showBookingModal.value = true
}

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

const openGallery = (index) => {
    galleryIndex.value = index
    showGalleryModal.value = true
}

const closeGallery = () => {
    showGalleryModal.value = false
}

const prevImage = () => {
    if (galleryIndex.value > 0) {
        galleryIndex.value--
    } else {
        galleryIndex.value = vendor.gallery.length - 1
    }
}

const nextImage = () => {
    if (galleryIndex.value < vendor.gallery.length - 1) {
        galleryIndex.value++
    } else {
        galleryIndex.value = 0
    }
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const handleDateClick = (arg) => {
    if (arg.event) {
        // Date is booked, don't allow selection
        return
    }

    // Set the selected date in the booking form
    bookingForm.value.date = arg.dateStr
    openBookingModal()
}

const calendarOptions = {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    events: calendarEvents.value,
    dateClick: handleDateClick,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,dayGridWeek'
    },
    eventDisplay: 'block',
    eventColor: '#3b82f6',
    height: 'auto'
}

onMounted(() => {
    // Initialize calendar API if needed
})
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <!-- Enhanced Header Section -->
        <div class="relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-700 to-blue-800"></div>
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>

            <!-- Header Content -->
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-6 md:py-8">
                <!-- Back Button -->
                <Button @click="goBack" variant="ghost"
                    class="mb-6 md:mb-8 group text-white hover:text-indigo-200 hover:bg-white/10">
                    <ArrowLeft :size="18" class="mr-2" />
                    <span class="hidden sm:block">Back to Search</span>
                </Button>

                <!-- Vendor Info Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 items-center text-white">
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
                    <div class="lg:col-span-6 text-center lg:text-left space-y-4">
                        <div>
                            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-3 text-white">{{ vendor.name }}
                            </h1>
                            <p class="text-lg md:text-xl text-indigo-100 font-medium">{{ vendor.category }}</p>
                        </div>

                        <!-- Rating & Stats -->
                        <div
                            class="flex flex-col sm:flex-row gap-4 md:gap-6 items-center lg:items-start justify-center lg:justify-start">
                            <div class="flex items-center gap-3">
                                <div class="flex gap-1">
                                    <Star v-for="n in 5" :key="n" fill="currentColor" :size="18"
                                        :class="['text-yellow-400', n <= vendor.rating ? 'opacity-100' : 'opacity-30']" />
                                </div>
                                <span class="font-semibold text-lg">{{ vendor.rating }}</span>
                                <span class="text-indigo-200 text-sm md:text-base">({{ vendor.reviewCount }}
                                    reviews)</span>
                            </div>
                            <div class="flex items-center gap-2 text-indigo-200">
                                <MapPin :size="16" />
                                <span class="text-sm md:text-base">{{ vendor.location }}</span>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                            <Badge variant="secondary" class="bg-white/20 backdrop-blur-sm border-white/30 text-white">
                                <Clock :size="14" class="mr-1" />
                                <span class="text-sm">{{ vendor.responseTime }} response</span>
                            </Badge>
                            <Badge variant="secondary" class="bg-white/20 backdrop-blur-sm border-white/30 text-white">
                                <Calendar :size="14" class="mr-1" />
                                <span class="text-sm">{{ vendor.completedEvents }}+ Events</span>
                            </Badge>
                            <Badge variant="secondary" class="bg-white/20 backdrop-blur-sm border-white/30 text-white">
                                <Shield :size="14" class="mr-1" />
                                <span class="text-sm">Verified Vendor</span>
                            </Badge>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="lg:col-span-3 flex flex-col gap-4">
                        <Button @click="openBookingModal"
                            class="w-full py-3 md:py-4 text-base md:text-lg font-semibold">
                            <CalendarPlus :size="20" class="mr-2" />
                            Book Now
                        </Button>
                        <div class="flex gap-3">
                            <Button @click="sendMessage" variant="outline"
                                class="flex-1 py-3 bg-white/20 backdrop-blur-sm border-white/30 text-white hover:bg-white/30">
                                <MessageCircle :size="18" class="mr-2" />
                                <span class="hidden sm:inline">Message</span>
                            </Button>
                            <Button @click="toggleFavorite" variant="outline" size="icon"
                                class="w-12 h-12 bg-white/20 backdrop-blur-sm border-white/30 text-white hover:bg-white/30"
                                :class="isFavorite ? 'text-red-400' : 'text-white'">
                                <Heart :size="20" :fill="isFavorite ? 'currentColor' : 'none'" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Navigation Tabs -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 -mt-4 md:-mt-6">
            <Card class="rounded-2xl shadow-xl">
                <Tabs v-model="activeTab" class="w-full">
                    <TabsList class="w-full grid grid-cols-3 h-auto p-0 bg-transparent">
                        <TabsTrigger value="overview"
                            class="flex-1 px-4 md:px-6 py-4 md:py-5 font-semibold rounded-none data-[state=active]:bg-gradient-to-r data-[state=active]:from-indigo-50 data-[state=active]:to-purple-50 data-[state=active]:text-indigo-600 data-[state=active]:shadow-none">
                            <MapPin :size="18" class="mr-2" />
                            <span>Overview</span>
                        </TabsTrigger>
                        <TabsTrigger value="reviews"
                            class="flex-1 px-4 md:px-6 py-4 md:py-5 font-semibold rounded-none data-[state=active]:bg-gradient-to-r data-[state=active]:from-indigo-50 data-[state=active]:to-purple-50 data-[state=active]:text-indigo-600 data-[state=active]:shadow-none">
                            <Star :size="18" class="mr-2" />
                            <span>Reviews</span>
                        </TabsTrigger>
                        <TabsTrigger value="location"
                            class="flex-1 px-4 md:px-6 py-4 md:py-5 font-semibold rounded-none data-[state=active]:bg-gradient-to-r data-[state=active]:from-indigo-50 data-[state=active]:to-purple-50 data-[state=active]:text-indigo-600 data-[state=active]:shadow-none">
                            <MapPin :size="18" class="mr-2" />
                            <span>Location</span>
                        </TabsTrigger>
                    </TabsList>

                    <!-- Tab Content -->
                    <div class="p-4 md:p-6 lg:p-8">
                        <TabsContent value="overview">
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 md:gap-10">
                                <!-- Main Content -->
                                <div class="xl:col-span-2 space-y-8 md:space-y-10">
                                    <!-- Enhanced Photo Gallery -->
                                    <Card class="overflow-hidden shadow-lg border-0 bg-white">
                                        <CardHeader
                                            class="bg-gradient-to-r from-slate-50 to-gray-50 border-b border-gray-100 px-8 py-6">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <CardTitle class="text-2xl font-bold text-gray-900">Portfolio
                                                            Gallery</CardTitle>
                                                        <p class="text-gray-500 mt-1">Professional showcase of our work
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="bg-white px-4 py-2 rounded-full border shadow-sm">
                                                    <span class="text-sm font-semibold text-gray-700">{{
                                                        vendor.gallery.length }} photos</span>
                                                </div>
                                            </div>
                                        </CardHeader>
                                        <CardContent class="p-8">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                                <div v-for="(image, index) in vendor.gallery" :key="index"
                                                    @click="openGallery(index)"
                                                    class="gallery-item group cursor-pointer relative">
                                                    <!-- Main Image Container -->
                                                    <div
                                                        class="aspect-[4/3] rounded-2xl overflow-hidden bg-gray-100 shadow-md hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1">
                                                        <img :src="image.url" :alt="image.caption"
                                                            class="w-full h-full object-cover transition-all duration-700 group-hover:scale-110" />

                                                        <!-- Professional Overlay -->
                                                        <div
                                                            class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300">
                                                            <!-- Expand Icon -->
                                                            <div class="absolute top-4 right-4">
                                                                <div
                                                                    class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center transform scale-0 group-hover:scale-100 transition-transform duration-300 delay-100">
                                                                    <Expand :size="18" class="text-gray-800" />
                                                                </div>
                                                            </div>

                                                            <!-- Caption -->
                                                            <div
                                                                class="absolute bottom-0 left-0 right-0 p-4 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-150">
                                                                <p
                                                                    class="text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-200">
                                                                    {{ image.caption || `Photo ${index + 1}` }}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <!-- Professional Border Effect -->
                                                        <div
                                                            class="absolute inset-0 rounded-2xl ring-1 ring-black/5 group-hover:ring-2 group-hover:ring-indigo-500/20 transition-all duration-300">
                                                        </div>
                                                    </div>

                                                    <!-- Professional Image Number Badge -->
                                                    <div
                                                        class="absolute -top-3 -left-3 w-8 h-8 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-lg transform scale-0 group-hover:scale-100 transition-transform duration-300">
                                                        {{ index + 1 }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- View All Button -->
                                            <div class="mt-8 text-center">
                                                <button
                                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-blue-600 text-white rounded-xl hover:from-indigo-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-semibold">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View Full Gallery
                                                </button>
                                            </div>
                                        </CardContent>
                                    </Card>

                                    <!-- Enhanced About Section -->
                                    <Card class="overflow-hidden shadow-lg border-0">
                                        <div class="bg-gradient-to-r from-indigo-50 via-blue-50 to-purple-50 relative">
                                            <!-- Decorative Background -->
                                            <div class="absolute inset-0 opacity-10">
                                                <div
                                                    class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-indigo-400 to-transparent rounded-full">
                                                </div>
                                                <div
                                                    class="absolute bottom-0 left-0 w-40 h-40 bg-gradient-to-tr from-blue-400 to-transparent rounded-full">
                                                </div>
                                            </div>

                                            <CardHeader class="relative px-8 py-8">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <CardTitle class="text-2xl md:text-3xl font-bold text-gray-900">
                                                            About {{ vendor.name }}</CardTitle>
                                                        <p class="text-gray-600 mt-1">Professional services & expertise
                                                        </p>
                                                    </div>
                                                </div>
                                            </CardHeader>
                                        </div>

                                        <CardContent class="px-8 py-8 space-y-8 bg-white">
                                            <div class="prose max-w-none">
                                                <p
                                                    class="text-gray-700 leading-relaxed text-lg font-light tracking-wide">
                                                    {{ vendor.description }}</p>
                                            </div>

                                            <div class="border-t border-gray-100 pt-8">
                                                <div class="flex items-center gap-3 mb-6">
                                                    <div
                                                        class="w-8 h-8 bg-gradient-to-r from-green-400 to-emerald-500 rounded-lg flex items-center justify-center">
                                                        <CheckCircle :size="18" class="text-white" />
                                                    </div>
                                                    <h3 class="text-xl md:text-2xl font-bold text-gray-900">Specialties
                                                        & Services</h3>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div v-for="specialty in vendor.specialties" :key="specialty"
                                                        class="group flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100 hover:from-indigo-50 hover:to-blue-50 transition-all duration-300 border border-gray-200 hover:border-indigo-200">
                                                        <div
                                                            class="w-6 h-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                            <CheckCircle :size="14" class="text-white" />
                                                        </div>
                                                        <span
                                                            class="font-medium text-gray-800 group-hover:text-indigo-700 transition-colors">{{
                                                                specialty }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>

                                    <!-- Enhanced Video Section -->
                                    <Card v-if="vendor.videoUrl" class="overflow-hidden shadow-lg border-0">
                                        <CardHeader
                                            class="bg-gradient-to-r from-slate-50 to-gray-50 border-b border-gray-100 px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-600 rounded-xl flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <CardTitle class="text-2xl font-bold text-gray-900">Showcase Video
                                                    </CardTitle>
                                                    <p class="text-gray-500 mt-1">See our work in action</p>
                                                </div>
                                            </div>
                                        </CardHeader>
                                        <CardContent class="p-8">
                                            <div class="video-container relative rounded-2xl overflow-hidden shadow-xl">
                                                <video :src="vendor.videoUrl" controls
                                                    poster="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800&h=400&fit=crop"
                                                    class="w-full rounded-2xl">
                                                </video>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </div>

                                <!-- Enhanced Sidebar -->
                                <div class="space-y-8">
                                    <!-- Enhanced Pricing Packages -->
                                    <Card class="overflow-hidden shadow-lg border-0">
                                        <CardHeader
                                            class="bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-100 px-6 py-6">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                    </svg>
                                                </div>
                                                <CardTitle class="text-xl font-bold text-gray-900">Service Packages
                                                </CardTitle>
                                            </div>
                                        </CardHeader>
                                        <CardContent class="p-6 space-y-4 bg-white">
                                            <Card v-for="p in vendor.packages" :key="p.id"
                                                class="package-card group cursor-pointer border-2 border-gray-100 hover:border-indigo-300 transition-all duration-300 hover:shadow-lg bg-gradient-to-br from-white to-gray-50">
                                                <CardHeader
                                                    class="flex flex-row items-start justify-between space-y-0 pb-4 px-6 pt-6">
                                                    <div class="flex-1">
                                                        <CardTitle
                                                            class="group-hover:text-indigo-600 transition-colors text-lg font-bold">
                                                            {{ p.name }}
                                                        </CardTitle>
                                                    </div>
                                                    <div class="text-right">
                                                        <div
                                                            class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-3 py-1 rounded-full text-xs font-semibold mb-1">
                                                            POPULAR
                                                        </div>
                                                        <span
                                                            class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">₱{{
                                                                p.price.toLocaleString() }}</span>
                                                        <p class="text-sm text-gray-500 font-medium">starting price</p>
                                                    </div>
                                                </CardHeader>
                                                <CardContent class="px-6 pb-6">
                                                    <ul class="space-y-3">
                                                        <li v-for="feature in p.features" :key="feature"
                                                            class="flex items-center gap-3 text-gray-700 text-sm md:text-base">
                                                            <div
                                                                class="w-5 h-5 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                                                                <Check :size="12" class="text-white" />
                                                            </div>
                                                            <span class="leading-relaxed">{{ feature }}</span>
                                                        </li>
                                                    </ul>
                                                </CardContent>
                                            </Card>
                                        </CardContent>
                                    </Card>

                                    <!-- Enhanced Calendar -->
                                    <Card class="overflow-hidden shadow-lg border-0">
                                        <CardHeader
                                            class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100 px-6 py-6">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <CardTitle class="text-xl font-bold text-gray-900">Availability
                                                    </CardTitle>
                                                    <p class="text-gray-500 text-sm">Check our schedule</p>
                                                </div>
                                            </div>
                                        </CardHeader>
                                        <CardContent class="p-6 bg-white">
                                            <div class="calendar-container">
                                                <FullCalendar :options="calendarOptions" class="vendor-calendar" />
                                                <div class="calendar-legend mt-6 p-4 bg-gray-50 rounded-xl">
                                                    <h4 class="font-semibold text-gray-900 mb-3 text-sm">Legend</h4>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div class="legend-item flex items-center gap-2">
                                                            <div
                                                                class="w-4 h-4 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full">
                                                            </div>
                                                            <span
                                                                class="text-sm font-medium text-gray-700">Available</span>
                                                        </div>
                                                        <div class="legend-item flex items-center gap-2">
                                                            <div
                                                                class="w-4 h-4 bg-gradient-to-r from-red-400 to-pink-500 rounded-full">
                                                            </div>
                                                            <span
                                                                class="text-sm font-medium text-gray-700">Booked</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>

                                    <!-- Enhanced Contact Card -->
                                    <Card class="overflow-hidden shadow-lg border-0">
                                        <CardHeader
                                            class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-100 px-6 py-6">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                </div>
                                                <CardTitle class="text-xl font-bold text-gray-900">Get in Touch
                                                </CardTitle>
                                            </div>
                                        </CardHeader>
                                        <CardContent class="p-6 space-y-4 bg-white">
                                            <div
                                                class="contact-item group flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-gray-50 to-white hover:from-green-50 hover:to-emerald-50 transition-all duration-300 border border-gray-100 hover:border-green-200 cursor-pointer">
                                                <div
                                                    class="contact-icon w-12 h-12 bg-green-100 text-green-600 group-hover:bg-green-500 group-hover:text-white rounded-xl flex items-center justify-center transition-all duration-300">
                                                    <Phone :size="20" />
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-900 text-sm md:text-base">Phone
                                                    </p>
                                                    <p class="text-gray-600 text-sm md:text-base font-mono">{{
                                                        vendor.phone }}</p>
                                                </div>
                                            </div>

                                            <div
                                                class="contact-item group flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-gray-50 to-white hover:from-blue-50 hover:to-cyan-50 transition-all duration-300 border border-gray-100 hover:border-blue-200 cursor-pointer">
                                                <div
                                                    class="contact-icon w-12 h-12 bg-blue-100 text-blue-600 group-hover:bg-blue-500 group-hover:text-white rounded-xl flex items-center justify-center transition-all duration-300">
                                                    <Mail :size="20" />
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-900 text-sm md:text-base">Email
                                                    </p>
                                                    <p class="text-gray-600 text-sm md:text-base break-all">{{
                                                        vendor.email }}</p>
                                                </div>
                                            </div>

                                            <div
                                                class="contact-item group flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-gray-50 to-white hover:from-purple-50 hover:to-indigo-50 transition-all duration-300 border border-gray-100 hover:border-purple-200 cursor-pointer">
                                                <div
                                                    class="contact-icon w-12 h-12 bg-purple-100 text-purple-600 group-hover:bg-purple-500 group-hover:text-white rounded-xl flex items-center justify-center transition-all duration-300">
                                                    <Globe :size="20" />
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-900 text-sm md:text-base">Website
                                                    </p>
                                                    <p class="text-gray-600 text-sm md:text-base">Visit our professional
                                                        site</p>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </div>
                            </div>
                        </TabsContent>

                        <TabsContent value="reviews">
                            <!-- Reviews Summary -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 mb-8 md:mb-12">
                                <Card class="review-summary-card border-indigo-100">
                                    <CardContent class="pt-6 text-center">
                                        <div class="text-5xl md:text-6xl font-bold text-indigo-600 mb-3">{{
                                            vendor.rating }}</div>
                                        <div class="flex justify-center gap-1 mb-3">
                                            <Star v-for="n in 5" :key="n" fill="currentColor" :size="20"
                                                :class="['text-yellow-400', n <= vendor.rating ? 'opacity-100' : 'opacity-30']" />
                                        </div>
                                        <p class="text-gray-600 font-medium text-sm md:text-base">{{ vendor.reviewCount
                                        }} total reviews</p>
                                    </CardContent>
                                </Card>

                                <div class="lg:col-span-2 space-y-4">
                                    <div v-for="(count, rating) in vendor.ratingBreakdown" :key="rating"
                                        class="rating-bar">
                                        <span class="rating-label text-sm md:text-base">{{ rating }} star{{ rating > 1 ?
                                            's' : '' }}</span>
                                        <Progress :model-value="(count / vendor.reviewCount) * 100"
                                            class="rating-progress h-2 md:h-3" />
                                        <span class="rating-count text-sm md:text-base">{{ count }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Individual Reviews -->
                            <div class="space-y-6">
                                <Card v-for="review in vendor.reviews" :key="review.id" class="review-card">
                                    <CardHeader class="flex flex-row items-start space-y-0 pb-4">
                                        <Avatar class="w-12 h-12 md:w-16 md:h-16 ring-2 md:ring-4 ring-indigo-100 mr-4">
                                            <AvatarImage :src="review.avatar" :alt="review.name" />
                                            <AvatarFallback>{{ review.name.charAt(0) }}</AvatarFallback>
                                        </Avatar>
                                        <div class="flex-1">
                                            <CardTitle class="text-lg">{{ review.name }}</CardTitle>
                                            <div class="flex items-center gap-4 mt-1 flex-wrap">
                                                <div class="flex gap-1">
                                                    <Star v-for="n in 5" :key="n" fill="currentColor" :size="16"
                                                        :class="['text-yellow-400', n <= review.rating ? 'opacity-100' : 'opacity-30']" />
                                                </div>
                                                <span class="text-gray-500 text-sm">{{ formatDate(review.date) }}</span>
                                            </div>
                                        </div>
                                    </CardHeader>
                                    <CardContent>
                                        <p class="text-gray-700 leading-relaxed text-base md:text-lg">{{ review.comment
                                        }}</p>
                                        <div v-if="review.photos" class="review-photos mt-4">
                                            <img v-for="photo in review.photos" :key="photo" :src="photo"
                                                alt="Review photo" class="review-photo" />
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </TabsContent>

                        <TabsContent value="location">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
                                <!-- Map Placeholder -->
                                <Card class="map-container">
                                    <CardContent class="pt-6">
                                        <div class="map-placeholder">
                                            <MapPin :size="64" class="text-indigo-300 mb-4 md:mb-6" />
                                            <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-3">Interactive Map
                                            </h3>
                                            <p class="text-gray-600 text-base md:text-lg">{{ vendor.address }}</p>
                                            <Button class="mt-4 md:mt-6">
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
                                                    <Tool :size="18" class="text-green-600 flex-shrink-0" />
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
                        </TabsContent>
                    </div>
                </Tabs>
            </Card>
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

        <!-- Gallery Modal -->
        <Dialog v-model:open="showGalleryModal">
            <DialogContent class="max-w-6xl p-0 border-0 bg-black">
                <div class="relative">
                    <!-- Simple Close Button -->
                    <Button @click="closeGallery" variant="ghost" size="icon"
                        class="absolute top-4 right-4 z-10 text-white/80 hover:text-white hover:bg-white/10 transition-colors">
                        <X :size="20" />
                    </Button>

                    <!-- Main Image -->
                    <img :src="vendor.gallery[galleryIndex]?.url" :alt="vendor.gallery[galleryIndex]?.caption"
                        class="w-full max-h-[85vh] object-contain" />

                    <!-- Simple Navigation -->
                    <Button v-if="vendor.gallery.length > 1" @click="prevImage" variant="ghost" size="icon"
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white hover:bg-white/10 transition-colors">
                        <ChevronLeft :size="24" />
                    </Button>

                    <Button v-if="vendor.gallery.length > 1" @click="nextImage" variant="ghost" size="icon"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white/80 hover:text-white hover:bg-white/10 transition-colors">
                        <ChevronRight :size="24" />
                    </Button>

                    <!-- Simple Caption & Counter -->
                    <div v-if="vendor.gallery[galleryIndex]?.caption || vendor.gallery.length > 1"
                        class="absolute bottom-4 left-4 right-4 text-center">
                        <div class="inline-block bg-black/60 text-white px-4 py-2 rounded text-sm backdrop-blur-sm">
                            <p v-if="vendor.gallery[galleryIndex]?.caption" class="mb-1">
                                {{ vendor.gallery[galleryIndex].caption }}
                            </p>
                            <p v-if="vendor.gallery.length > 1" class="text-white/70 text-xs">
                                {{ galleryIndex + 1 }} / {{ vendor.gallery.length }}
                            </p>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style scoped>
/* Global Styles */
.gallery-item {
    @apply relative overflow-hidden rounded-xl shadow-lg transition-all duration-300;
}

.gallery-overlay {
    @apply absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center;
}

/* Calendar Styles */
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

/* Contact Styles */
.contact-item {
    @apply flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-all duration-200 cursor-pointer;
}

.contact-icon {
    @apply w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200;
}

/* Reviews Styles */
.review-summary-card {
    @apply bg-gradient-to-br from-indigo-50 to-purple-50;
}

.rating-bar {
    @apply flex items-center gap-3 md:gap-4;
}

.rating-label {
    @apply text-xs md:text-sm text-gray-600 w-16 md:w-20 flex-shrink-0;
}

.rating-count {
    @apply text-xs md:text-sm text-gray-600 w-6 md:w-8 flex-shrink-0;
}

.review-photos {
    @apply flex gap-2 md:gap-3 mt-3 md:mt-4;
}

.review-photo {
    @apply w-20 h-20 md:w-24 md:h-24 rounded-lg object-cover cursor-pointer hover:scale-105 transition-transform duration-200;
}

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

/* Video Container */
.video-container {
    @apply relative rounded-2xl overflow-hidden shadow-lg;
}

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
