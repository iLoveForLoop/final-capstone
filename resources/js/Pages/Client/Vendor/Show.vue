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
    CalendarCheck,
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
    Wrench,
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
                            <span v-if="vendor.verified"
                                class="flex px-4 py-2 bg-white/10 text-white text-sm rounded-full">
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
                                                <div v-for="(image, index) in vendor.gallery" :key="index"
                                                    @click="openGallery(index)"
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
                                                <div class="flex flex-wrap gap-3">
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
                                                <video :src="vendor.videoUrl" controls
                                                    poster="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800&h=400&fit=crop"
                                                    class="w-full rounded-2xl shadow-lg">
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
                                                            <span
                                                                class="text-2xl sm:text-3xl font-bold text-primary">₱{{
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

                                    <!-- Enhanced Calendar -->
                                    <!-- <Card>
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
                                    </Card> -->

                                    <!-- Enhanced Contact Card -->
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
                                    <CardHeader class="px-6 py-5 border-b border-gray-100">
                                        <div class="flex items-start space-x-4">
                                            <!-- Avatar -->
                                            <Avatar class="w-12 h-12 flex-shrink-0 ring-2 ring-gray-100">
                                                <AvatarImage :src="review.avatar" :alt="review.name" />
                                                <AvatarFallback
                                                    class="bg-indigo-100 text-indigo-700 font-medium text-sm">
                                                    {{ review.name.charAt(0) }}
                                                </AvatarFallback>
                                            </Avatar>

                                            <!-- Review Header Info -->
                                            <div class="flex-1 min-w-0">
                                                <CardTitle class="text-base font-semibold text-gray-900 truncate">
                                                    {{ review.name }}
                                                </CardTitle>

                                                <div class="flex items-center justify-between mt-2">
                                                    <!-- Rating Stars -->
                                                    <div class="flex items-center space-x-1">
                                                        <Star v-for="n in 5" :key="n" :size="16" :class="[
                                                            'transition-colors duration-150',
                                                            n <= review.rating
                                                                ? 'text-amber-400 fill-amber-400'
                                                                : 'text-gray-300 fill-gray-300'
                                                        ]" />
                                                        <span class="ml-2 text-sm font-medium text-gray-700">
                                                            {{ review.rating }}.0
                                                        </span>
                                                    </div>

                                                    <!-- Date -->
                                                    <span class="text-sm text-gray-500 whitespace-nowrap">
                                                        {{ formatDate(review.date) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </CardHeader>
                                    <CardContent>
                                        <p class="text-gray-700 leading-relaxed text-base md:text-lg mt-3">{{
                                            review.comment
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
                    <!-- <div
                        class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/70 text-white px-6 py-2 rounded-full text-center">
                        {{ vendor.gallery[galleryIndex]?.caption }}
                    </div> -->
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
