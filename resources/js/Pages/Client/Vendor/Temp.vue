<script setup>
import { ref, computed } from 'vue'
import {
    ArrowLeft,
    Heart,
    MessageCircle,
    Phone,
    Mail,
    Globe,
    MapPin,
    Clock,
    Calendar,
    Shield,
    Star,
    CheckCircle,
    ChevronLeft,
    ChevronRight,
    Expand,
    CalendarCheck,
    Compass,
    Home,
    Truck,
    Wrench,
    Info,
    Send
} from 'lucide-vue-next'

import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs'
import { Progress } from '@/Components/ui/progress'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/Components/ui/dialog'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'

const activeTab = ref('overview')
const showBookingModal = ref(false)
const showGalleryModal = ref(false)
const galleryIndex = ref(0)
const isFavorite = ref(false)
const currentMonth = ref(new Date().getMonth())
const currentYear = ref(new Date().getFullYear())

console.log('Active Tab: ', activeTab.value)

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

const currentMonthYear = computed(() => {
    const months = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December']
    return `${months[currentMonth.value]} ${currentYear.value}`
})

const calendarDates = computed(() => {
    const dates = []
    const firstDay = new Date(currentYear.value, currentMonth.value, 1)
    const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0)
    const startDate = new Date(firstDay)
    startDate.setDate(startDate.getDate() - firstDay.getDay())

    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate)
        date.setDate(date.getDate() + i)

        const today = new Date()
        const isToday = date.toDateString() === today.toDateString()
        const inCurrentMonth = date.getMonth() === currentMonth.value

        dates.push({
            date: date.toISOString().split('T')[0],
            day: date.getDate(),
            available: inCurrentMonth && Math.random() > 0.3,
            booked: inCurrentMonth && Math.random() > 0.8,
            isToday: isToday,
            inCurrentMonth: inCurrentMonth
        })
    }

    return dates
})

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

const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11
        currentYear.value--
    } else {
        currentMonth.value--
    }
}

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0
        currentYear.value++
    } else {
        currentMonth.value++
    }
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const getDateClasses = (date) => {
    const classes = []
    if (date.available && date.inCurrentMonth) {
        classes.push('bg-green-100 text-green-800 hover:bg-green-200')
    }
    if (date.booked && date.inCurrentMonth) {
        classes.push('bg-red-100 text-red-800 cursor-not-allowed')
    }
    if (date.isToday) {
        classes.push('ring-2 ring-primary')
    }
    if (!date.inCurrentMonth) {
        classes.push('text-muted-foreground opacity-50')
    }
    return classes.join(' ')
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <!-- Enhanced Header Section -->
        <div class="relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-700 to-blue-800"></div>
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>

            <!-- Header Content -->
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-8">
                <!-- Back Button -->
                <Button @click="goBack" variant="ghost"
                    class="mb-8 group text-white hover:text-indigo-200 hover:bg-white/10">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back to Search
                </Button>

                <!-- Vendor Info Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center text-white">
                    <!-- Avatar Section -->
                    <div class="lg:col-span-3 flex justify-center lg:justify-start">
                        <div class="relative">
                            <Avatar
                                class="w-32 h-32 sm:w-40 sm:h-40 border-4 border-white border-opacity-30 shadow-2xl">
                                <AvatarImage :src="vendor.avatar" :alt="vendor.name" />
                                <AvatarFallback>{{ vendor.name.slice(0, 2) }}</AvatarFallback>
                            </Avatar>
                            <!-- Verified Badge -->
                            <Badge v-if="vendor.verified"
                                class="absolute -bottom-3 -right-3 bg-green-500 text-white rounded-full w-12 h-12 flex items-center justify-center border-4 border-white shadow-lg">
                                <Shield class="h-5 w-5" />
                            </Badge>
                            <!-- Status Indicator -->
                            <Badge class="absolute -top-2 -left-2 text-xs font-semibold"
                                :variant="vendor.available ? 'default' : 'destructive'">
                                {{ vendor.available ? 'Available' : 'Busy' }}
                            </Badge>
                        </div>
                    </div>

                    <!-- Vendor Details -->
                    <div class="lg:col-span-6 text-center lg:text-left space-y-4">
                        <div>
                            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-3">{{ vendor.name }}</h1>
                            <p class="text-lg sm:text-xl text-indigo-100 font-medium">{{ vendor.category }}</p>
                        </div>

                        <!-- Rating & Stats -->
                        <div
                            class="flex flex-col sm:flex-row gap-4 sm:gap-6 items-center lg:items-start justify-center lg:justify-start">
                            <div class="flex items-center gap-3">
                                <div class="flex gap-1">
                                    <Star v-for="n in 5" :key="n"
                                        :class="['h-5 w-5 text-yellow-400', n <= vendor.rating ? 'fill-current' : 'opacity-30']" />
                                </div>
                                <span class="font-semibold text-lg">{{ vendor.rating }}</span>
                                <span class="text-indigo-200">({{ vendor.reviewCount }} reviews)</span>
                            </div>
                            <div class="flex items-center gap-2 text-indigo-200">
                                <MapPin class="h-4 w-4" />
                                <span class="text-sm sm:text-base">{{ vendor.location }}</span>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                            <Badge variant="secondary"
                                class="bg-white bg-opacity-20 text-white border-white border-opacity-30">
                                <Clock class="h-4 w-4 mr-2" />
                                {{ vendor.responseTime }} response
                            </Badge>
                            <Badge variant="secondary"
                                class="bg-white bg-opacity-20 text-white border-white border-opacity-30">
                                <CalendarCheck class="h-4 w-4 mr-2" />
                                {{ vendor.completedEvents }}+ Events
                            </Badge>
                            <Badge variant="secondary"
                                class="bg-white bg-opacity-20 text-white border-white border-opacity-30">
                                <Shield class="h-4 w-4 mr-2" />
                                Verified Vendor
                            </Badge>
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
                            <Button @click="sendMessage" variant="outline"
                                class="flex-1 border-white text-white hover:bg-white hover:text-slate-900">
                                <MessageCircle class="h-4 w-4 mr-2" />
                                Message
                            </Button>
                            <Button @click="toggleFavorite" variant="outline" size="icon"
                                class="border-white hover:bg-white hover:text-slate-900"
                                :class="isFavorite ? 'text-red-400' : 'text-white'">
                                <Heart :class="['h-5 w-5', isFavorite ? 'fill-current' : '']" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Navigation Tabs -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 -mt-6">
            <Card class="overflow-hidden">
                <Tabs v-model="activeTab">
                    <TabsList class="grid w-full grid-cols-3">
                        <TabsTrigger value="overview" class="flex items-center gap-2">
                            <Home class="h-4 w-4" />
                            <span class="hidden sm:inline">Overview</span>
                        </TabsTrigger>
                        <TabsTrigger value="reviews" class="flex items-center gap-2">
                            <Star class="h-4 w-4" />
                            <span class="hidden sm:inline">Reviews</span>
                        </TabsTrigger>
                        <TabsTrigger value="location" class="flex items-center gap-2">
                            <MapPin class="h-4 w-4" />
                            <span class="hidden sm:inline">Location</span>
                        </TabsTrigger>
                    </TabsList>

                    <!-- Overview Tab -->
                    <TabsContent value="overview" class="p-4 sm:p-8 mt-0">
                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                            <!-- Main Content -->
                            <div class="xl:col-span-2 space-y-8">
                                <!-- Photo Gallery -->
                                <section>
                                    <div class="flex items-center justify-between mb-6">
                                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Portfolio Gallery</h2>
                                        <span class="text-sm text-gray-500">{{ vendor.gallery.length }} photos</span>
                                    </div>
                                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div v-for="(image, index) in vendor.gallery" :key="index"
                                            @click="openGallery(index)"
                                            class="group cursor-pointer relative aspect-video rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300">
                                            <img :src="image.url" :alt="image.caption"
                                                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" />
                                            <div
                                                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center">
                                                <Expand
                                                    class="text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 h-8 w-8" />
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <!-- About Section -->
                                <Card class="bg-gradient-to-br from-gray-50 to-gray-100">
                                    <CardHeader>
                                        <CardTitle class="text-2xl sm:text-3xl font-bold text-gray-900">About {{
                                            vendor.name }}</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <p class="text-gray-700 leading-relaxed text-base sm:text-lg mb-8">{{
                                            vendor.description }}</p>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-4">Specialties & Services</h3>
                                            <div class="flex flex-wrap gap-3">
                                                <Badge v-for="specialty in vendor.specialties" :key="specialty">
                                                    <CheckCircle class="h-4 w-4 mr-2" />
                                                    {{ specialty }}
                                                </Badge>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>

                                <!-- Video Section -->
                                <section v-if="vendor.videoUrl">
                                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Showcase Video</h2>
                                    <div class="video-container">
                                        <video :src="vendor.videoUrl" controls
                                            poster="https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800&h=400&fit=crop"
                                            class="w-full rounded-2xl shadow-lg">
                                        </video>
                                    </div>
                                </section>
                            </div>

                            <!-- Enhanced Sidebar -->
                            <div class="space-y-6">
                                <!-- Pricing Packages -->
                                <Card>
                                    <CardHeader>
                                        <CardTitle class="text-xl sm:text-2xl font-bold text-gray-900">Service Packages
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

                                <!-- Enhanced Calendar -->
                                <Card>
                                    <CardHeader>
                                        <CardTitle class="text-xl sm:text-2xl font-bold text-gray-900">Availability
                                            Calendar</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div class="space-y-4">
                                            <!-- Calendar Header -->
                                            <div class="flex justify-between items-center">
                                                <Button @click="prevMonth" variant="outline" size="icon">
                                                    <ChevronLeft class="h-4 w-4" />
                                                </Button>
                                                <span class="font-bold text-base sm:text-lg">{{ currentMonthYear
                                                    }}</span>
                                                <Button @click="nextMonth" variant="outline" size="icon">
                                                    <ChevronRight class="h-4 w-4" />
                                                </Button>
                                            </div>

                                            <!-- Calendar Grid -->
                                            <div class="space-y-2">
                                                <div class="grid grid-cols-7 gap-1">
                                                    <div v-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="day"
                                                        class="text-center text-xs font-semibold text-muted-foreground py-2">
                                                        {{ day }}
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-7 gap-1">
                                                    <Button v-for="date in calendarDates" :key="date.date"
                                                        variant="ghost" size="sm" :class="getDateClasses(date)"
                                                        class="h-8 w-8 p-0 font-normal">
                                                        {{ date.day }}
                                                    </Button>
                                                </div>
                                                <!-- Calendar Legend -->
                                                <div class="flex justify-center gap-4 sm:gap-6 mt-6 text-xs">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-3 h-3 bg-green-200 rounded-full"></div>
                                                        <span>Available</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-3 h-3 bg-red-200 rounded-full"></div>
                                                        <span>Booked</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>

                                <!-- Enhanced Contact Card -->
                                <Card>
                                    <CardHeader>
                                        <CardTitle class="text-xl sm:text-2xl font-bold text-gray-900">Contact
                                            Information</CardTitle>
                                    </CardHeader>
                                    <CardContent class="space-y-4">
                                        <a :href="`tel:${vendor.phone}`"
                                            class="flex items-center gap-4 p-3 rounded-xl hover:bg-accent transition-all duration-200 cursor-pointer group">
                                            <div
                                                class="w-12 h-12 rounded-full bg-green-100 text-green-600 group-hover:bg-green-600 group-hover:text-white flex items-center justify-center transition-all duration-200">
                                                <Phone class="h-5 w-5" />
                                            </div>
                                            <div>
                                                <p class="font-semibold">Phone</p>
                                                <p class="text-sm sm:text-base text-muted-foreground">{{ vendor.phone }}
                                                </p>
                                            </div>
                                        </a>
                                        <a :href="`mailto:${vendor.email}`"
                                            class="flex items-center gap-4 p-3 rounded-xl hover:bg-accent transition-all duration-200 cursor-pointer group">
                                            <div
                                                class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition-all duration-200">
                                                <Mail class="h-5 w-5" />
                                            </div>
                                            <div>
                                                <p class="font-semibold">Email</p>
                                                <p class="text-sm sm:text-base text-muted-foreground">{{ vendor.email }}
                                                </p>
                                            </div>
                                        </a>
                                        <a :href="vendor.website" target="_blank"
                                            class="flex items-center gap-4 p-3 rounded-xl hover:bg-accent transition-all duration-200 cursor-pointer group">
                                            <div
                                                class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 group-hover:bg-purple-600 group-hover:text-white flex items-center justify-center transition-all duration-200">
                                                <Globe class="h-5 w-5" />
                                            </div>
                                            <div>
                                                <p class="font-semibold">Website</p>
                                                <p class="text-sm sm:text-base text-muted-foreground">Visit our site</p>
                                            </div>
                                        </a>
                                    </CardContent>
                                </Card>
                            </div>
                        </div>
                    </TabsContent>

                    <!-- Enhanced Reviews Tab -->
                    <TabsContent value="reviews" class="p-4 sm:p-8 mt-0">
                        <!-- Reviews Summary -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                            <Card class="text-center">
                                <CardContent class="p-8">
                                    <div class="text-5xl sm:text-6xl font-bold text-primary mb-3">{{ vendor.rating }}
                                    </div>
                                    <div class="flex justify-center gap-1 mb-3">
                                        <Star v-for="n in 5" :key="n"
                                            :class="['h-5 w-5 sm:h-6 sm:w-6 text-yellow-400', n <= vendor.rating ? 'fill-current' : 'opacity-30']" />
                                    </div>
                                    <p class="text-muted-foreground font-medium">{{ vendor.reviewCount }} total reviews
                                    </p>
                                </CardContent>
                            </Card>

                            <div class="lg:col-span-2 space-y-4">
                                <div v-for="(count, rating) in vendor.ratingBreakdown" :key="rating"
                                    class="flex items-center gap-4">
                                    <span class="text-sm text-muted-foreground w-16 sm:w-20 flex-shrink-0">{{ rating }}
                                        star{{ rating > 1 ? 's' : '' }}</span>
                                    <Progress :value="(count / vendor.reviewCount) * 100" class="flex-1" />
                                    <span class="text-sm text-muted-foreground w-8 flex-shrink-0">{{ count }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Individual Reviews -->
                        <div class="space-y-6">
                            <Card v-for="review in vendor.reviews" :key="review.id">
                                <CardContent class="p-6">
                                    <div class="flex items-start gap-4 mb-4">
                                        <Avatar class="w-12 h-12 sm:w-16 sm:h-16">
                                            <AvatarImage :src="review.avatar" :alt="review.name" />
                                            <AvatarFallback>{{ review.name.slice(0, 2) }}</AvatarFallback>
                                        </Avatar>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-base sm:text-lg">{{ review.name }}</h4>
                                            <div class="flex items-center gap-4 mt-1">
                                                <div class="flex gap-1">
                                                    <Star v-for="n in 5" :key="n"
                                                        :class="['h-4 w-4 text-yellow-400', n <= review.rating ? 'fill-current' : 'opacity-30']" />
                                                </div>
                                                <span class="text-muted-foreground text-sm">{{ formatDate(review.date)
                                                    }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-sm sm:text-base leading-relaxed mb-4">{{ review.comment }}</p>
                                    <div v-if="review.photos" class="flex gap-3 overflow-x-auto">
                                        <img v-for="photo in review.photos" :key="photo" :src="photo" alt="Review photo"
                                            class="w-20 h-20 sm:w-24 sm:h-24 rounded-lg object-cover cursor-pointer hover:scale-105 transition-transform duration-200 flex-shrink-0" />
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </TabsContent>

                    <!-- Enhanced Location Tab -->
                    <TabsContent value="location" class="p-4 sm:p-8 mt-0">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Map Placeholder -->
                            <Card class="border-2 border-dashed">
                                <CardContent class="p-8 flex flex-col items-center justify-center text-center min-h-96">
                                    <MapPin class="h-16 w-16 sm:h-20 sm:w-20 text-muted-foreground mb-6" />
                                    <CardTitle class="text-xl sm:text-2xl mb-3">Interactive Map</CardTitle>
                                    <p class="text-muted-foreground text-base sm:text-lg mb-6">{{ vendor.address }}</p>
                                    <Button>
                                        <MapPin class="h-4 w-4 mr-2" />
                                        Get Directions
                                    </Button>
                                </CardContent>
                            </Card>

                            <!-- Location Details -->
                            <div class="space-y-8">
                                <Card>
                                    <CardHeader>
                                        <CardTitle class="text-xl sm:text-2xl">Service Information</CardTitle>
                                    </CardHeader>
                                    <CardContent class="space-y-6">
                                        <div class="flex items-center gap-4">
                                            <MapPin class="h-5 w-5 text-muted-foreground" />
                                            <div>
                                                <span class="font-semibold block text-sm sm:text-base">Primary
                                                    Location</span>
                                                <span class="text-muted-foreground block text-sm sm:text-base">{{
                                                    vendor.location }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <Compass class="h-5 w-5 text-muted-foreground" />
                                            <div>
                                                <span class="font-semibold block text-sm sm:text-base">Service
                                                    Radius</span>
                                                <span class="text-muted-foreground block text-sm sm:text-base">{{
                                                    vendor.serviceRadius }} km coverage</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <Home class="h-5 w-5 text-muted-foreground" />
                                            <div>
                                                <span class="font-semibold block text-sm sm:text-base">Full
                                                    Address</span>
                                                <span class="text-muted-foreground block text-sm sm:text-base">{{
                                                    vendor.address }}</span>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>

                                <Card>
                                    <CardHeader>
                                        <CardTitle class="text-lg sm:text-xl">Service Features</CardTitle>
                                    </CardHeader>
                                    <CardContent class="space-y-4">
                                        <div class="flex items-center gap-3 text-sm sm:text-base">
                                            <Truck class="h-5 w-5 text-green-600" />
                                            <span>Own transportation available</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm sm:text-base">
                                            <Wrench class="h-5 w-5 text-green-600" />
                                            <span>Setup and breakdown included</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm sm:text-base">
                                            <Clock class="h-5 w-5 text-blue-600" />
                                            <span>Flexible scheduling options</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm sm:text-base">
                                            <Info class="h-5 w-5 text-orange-600" />
                                            <span>Delivery fee may apply for distant locations</span>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </div>
                    </TabsContent>
                </Tabs>
            </Card>
        </div>

        <!-- Enhanced Booking Modal -->
        <Dialog v-model:open="showBookingModal">
            <DialogContent class="sm:max-w-2xl max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle class="text-xl sm:text-2xl">Book {{ vendor.name }}</DialogTitle>
                </DialogHeader>

                <form @submit.prevent="submitBooking" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="date">Event Date</Label>
                            <Input id="date" v-model="bookingForm.date" type="date" required />
                        </div>
                        <div class="space-y-2">
                            <Label for="time">Event Time</Label>
                            <Input id="time" v-model="bookingForm.time" type="time" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="duration">Duration (hours)</Label>
                            <Input id="duration" v-model="bookingForm.duration" type="number" min="1" required />
                        </div>
                        <div class="space-y-2">
                            <Label for="package">Select Package</Label>
                            <Select v-model="bookingForm.packageId" required>
                                <SelectTrigger>
                                    <SelectValue placeholder="Choose a package" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="p in vendor.packages" :key="p.id" :value="p.id.toString()">
                                        {{ p.name }} - ₱{{ p.price.toLocaleString() }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="contactName">Contact Name</Label>
                        <Input id="contactName" v-model="bookingForm.contactName" type="text" required
                            placeholder="Your full name" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="contactPhone">Contact Phone</Label>
                            <Input id="contactPhone" v-model="bookingForm.contactPhone" type="tel" required
                                placeholder="+63 XXX XXX XXXX" />
                        </div>
                        <div class="space-y-2">
                            <Label for="contactEmail">Contact Email</Label>
                            <Input id="contactEmail" v-model="bookingForm.contactEmail" type="email" required
                                placeholder="your@email.com" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="location">Event Location</Label>
                        <Input id="location" v-model="bookingForm.location" type="text" required
                            placeholder="Enter event venue address" />
                    </div>

                    <div class="space-y-2">
                        <Label for="notes">Additional Notes</Label>
                        <Textarea id="notes" v-model="bookingForm.notes" rows="4"
                            placeholder="Special requests, requirements, or additional details..." />
                    </div>

                    <DialogFooter>
                        <Button type="button" @click="closeBookingModal" variant="outline">
                            Cancel
                        </Button>
                        <Button type="submit">
                            <Send class="h-4 w-4 mr-2" />
                            Send Booking Request
                        </Button>
                    </DialogFooter>
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
                    <div
                        class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/70 text-white px-6 py-2 rounded-full text-center">
                        {{ vendor.gallery[galleryIndex]?.caption }}
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style scoped>
/* Custom scrollbar for mobile */
::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}

::-webkit-scrollbar-track {
    background: hsl(var(--muted));
}

::-webkit-scrollbar-thumb {
    background: hsl(var(--muted-foreground));
    border-radius: 2px;
}

::-webkit-scrollbar-thumb:hover {
    background: hsl(var(--muted-foreground) / 0.8);
}

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}

/* Video container responsive */
.video-container video {
    aspect-ratio: 16 / 9;
}

/* Gallery image hover effects */
.group:hover .group-hover\\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\\:opacity-100 {
    opacity: 1;
}

.group:hover .group-hover\\:bg-opacity-60 {
    background-opacity: 0.6;
}

/* Mobile optimizations */
@media (max-width: 640px) {
    .grid-cols-2 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
}
</style>
