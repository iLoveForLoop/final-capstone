<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <!-- Enhanced Header Section -->
        <div class="relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-purple-700 to-blue-800"></div>
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>

            <!-- Header Content -->
            <div class="relative max-w-7xl mx-auto px-6 py-8">
                <!-- Back Button -->
                <button @click="goBack"
                    class="mb-8 group flex items-center gap-3 text-white hover:text-indigo-200 transition-all duration-300">
                    <div
                        class="w-10 h-10 rounded-full bg-white bg-opacity-20 backdrop-blur-sm flex items-center justify-center group-hover:bg-opacity-30 transition-all duration-300">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    <span class="font-medium">Back to Search</span>
                </button>

                <!-- Vendor Info Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center text-white">
                    <!-- Avatar Section -->
                    <div class="lg:col-span-3 flex justify-center lg:justify-start">
                        <div class="relative">
                            <div
                                class="w-40 h-40 rounded-2xl overflow-hidden border-4 border-white border-opacity-30 shadow-2xl">
                                <img :src="vendor.avatar" :alt="vendor.name" class="w-full h-full object-cover" />
                            </div>
                            <!-- Verified Badge -->
                            <div v-if="vendor.verified"
                                class="absolute -bottom-3 -right-3 bg-green-500 text-white rounded-full w-12 h-12 flex items-center justify-center border-4 border-white shadow-lg">
                                <i class="fas fa-check text-lg"></i>
                            </div>
                            <!-- Status Indicator -->
                            <div class="absolute -top-2 -left-2 px-3 py-1 rounded-full text-xs font-semibold"
                                :class="vendor.available ? 'bg-green-500' : 'bg-red-500'">
                                {{ vendor.available ? 'Available' : 'Busy' }}
                            </div>
                        </div>
                    </div>

                    <!-- Vendor Details -->
                    <div class="lg:col-span-6 text-center lg:text-left space-y-4">
                        <div>
                            <h1 class="text-4xl lg:text-5xl font-bold mb-3 gradient-text-white">{{ vendor.name }}</h1>
                            <p class="text-xl text-indigo-100 font-medium">{{ vendor.category }}</p>
                        </div>

                        <!-- Rating & Stats -->
                        <div
                            class="flex flex-col sm:flex-row gap-6 items-center lg:items-start justify-center lg:justify-start">
                            <div class="flex items-center gap-3">
                                <div class="flex gap-1">
                                    <i v-for="n in 5" :key="n"
                                        :class="['fas fa-star text-yellow-400 text-lg', n <= vendor.rating ? 'opacity-100' : 'opacity-30']"></i>
                                </div>
                                <span class="font-semibold text-lg">{{ vendor.rating }}</span>
                                <span class="text-indigo-200">({{ vendor.reviewCount }} reviews)</span>
                            </div>
                            <div class="flex items-center gap-2 text-indigo-200">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ vendor.location }}</span>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                            <div class="stat-badge">
                                <i class="fas fa-clock"></i>
                                <span>{{ vendor.responseTime }} response</span>
                            </div>
                            <div class="stat-badge">
                                <i class="fas fa-calendar-check"></i>
                                <span>{{ vendor.completedEvents }}+ Events</span>
                            </div>
                            <div class="stat-badge">
                                <i class="fas fa-shield-alt"></i>
                                <span>Verified Vendor</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="lg:col-span-3 flex flex-col gap-4">
                        <button @click="openBookingModal" class="btn-primary w-full py-4 text-lg font-semibold">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Book Now
                        </button>
                        <div class="flex gap-3">
                            <button @click="sendMessage" class="btn-secondary flex-1 py-3">
                                <i class="fas fa-comments mr-2"></i>
                                Message
                            </button>
                            <button @click="toggleFavorite" class="btn-icon w-12 h-12"
                                :class="isFavorite ? 'text-red-400' : 'text-white'">
                                <i class="fas fa-heart text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Navigation Tabs -->
        <div class="max-w-7xl mx-auto px-6 -mt-6">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="flex">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
                        'flex-1 px-6 py-5 font-semibold transition-all duration-300 flex items-center justify-center gap-3 relative',
                        activeTab === tab.id
                            ? 'text-indigo-600 bg-gradient-to-r from-indigo-50 to-purple-50'
                            : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50'
                    ]">
                        <i :class="tab.icon" class="text-lg"></i>
                        <span>{{ tab.name }}</span>
                        <div v-if="activeTab === tab.id"
                            class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-600 to-purple-600">
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Enhanced Tab Content -->
        <div class="max-w-7xl mx-auto px-6 pb-12">
            <div class="bg-white rounded-b-2xl shadow-xl min-h-96 mt-1">

                <!-- Overview Tab -->
                <div v-if="activeTab === 'overview'" class="p-8">
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                        <!-- Main Content -->
                        <div class="xl:col-span-2 space-y-8">
                            <!-- Photo Gallery -->
                            <section>
                                <div class="flex items-center justify-between mb-6">
                                    <h2 class="text-3xl font-bold text-gray-900">Portfolio Gallery</h2>
                                    <span class="text-sm text-gray-500">{{ vendor.gallery.length }} photos</span>
                                </div>
                                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div v-for="(image, index) in vendor.gallery" :key="index"
                                        @click="openGallery(index)"
                                        class="gallery-item group cursor-pointer relative aspect-video rounded-xl overflow-hidden">
                                        <img :src="image.url" :alt="image.caption"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" />
                                        <div class="gallery-overlay">
                                            <i
                                                class="fas fa-expand-arrows-alt text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- About Section -->
                            <section class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-8">
                                <h2 class="text-3xl font-bold text-gray-900 mb-6">About {{ vendor.name }}</h2>
                                <p class="text-gray-700 leading-relaxed text-lg mb-8">{{ vendor.description }}</p>

                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-4">Specialties & Services</h3>
                                    <div class="flex flex-wrap gap-3">
                                        <span v-for="specialty in vendor.specialties" :key="specialty"
                                            class="specialty-tag">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            {{ specialty }}
                                        </span>
                                    </div>
                                </div>
                            </section>

                            <!-- Video Section -->
                            <section v-if="vendor.videoUrl">
                                <h2 class="text-3xl font-bold text-gray-900 mb-6">Showcase Video</h2>
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
                            <div class="sidebar-card">
                                <h3 class="text-2xl font-bold text-gray-900 mb-6">Service Packages</h3>
                                <div class="space-y-4">
                                    <div v-for="p in vendor.packages" :key="p.id" class="package-card group">
                                        <div class="flex justify-between items-start mb-4">
                                            <h4
                                                class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                                {{ p.name }}
                                            </h4>
                                            <div class="text-right">
                                                <span class="text-3xl font-bold text-indigo-600">₱{{
                                                    p.price.toLocaleString() }}</span>
                                                <p class="text-sm text-gray-500">starting price</p>
                                            </div>
                                        </div>
                                        <ul class="space-y-3">
                                            <li v-for="feature in p.features" :key="feature"
                                                class="flex items-center gap-3 text-gray-700">
                                                <i class="fas fa-check text-green-500 text-sm flex-shrink-0"></i>
                                                <span>{{ feature }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Calendar -->
                            <div class="sidebar-card">
                                <h3 class="text-2xl font-bold text-gray-900 mb-6">Availability Calendar</h3>
                                <div class="calendar-container">
                                    <!-- Calendar Header -->
                                    <div class="calendar-header">
                                        <button @click="prevMonth" class="calendar-nav-btn">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <span class="font-bold text-lg text-white">{{ currentMonthYear }}</span>
                                        <button @click="nextMonth" class="calendar-nav-btn">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>

                                    <!-- Calendar Grid -->
                                    <div class="calendar-body">
                                        <div class="calendar-weekdays">
                                            <div v-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="day"
                                                class="calendar-weekday">{{ day }}</div>
                                        </div>
                                        <div class="calendar-dates">
                                            <div v-for="date in calendarDates" :key="date.date"
                                                :class="getDateClasses(date)" class="calendar-date">
                                                {{ date.day }}
                                            </div>
                                        </div>
                                        <!-- Calendar Legend -->
                                        <div class="calendar-legend">
                                            <div class="legend-item">
                                                <div class="w-3 h-3 bg-green-200 rounded-full"></div>
                                                <span>Available</span>
                                            </div>
                                            <div class="legend-item">
                                                <div class="w-3 h-3 bg-red-200 rounded-full"></div>
                                                <span>Booked</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Contact Card -->
                            <div class="sidebar-card">
                                <h3 class="text-2xl font-bold text-gray-900 mb-6">Contact Information</h3>
                                <div class="space-y-4">
                                    <a :href="`tel:${vendor.phone}`" class="contact-item group">
                                        <div
                                            class="contact-icon bg-green-100 text-green-600 group-hover:bg-green-600 group-hover:text-white">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Phone</p>
                                            <p class="text-gray-600">{{ vendor.phone }}</p>
                                        </div>
                                    </a>
                                    <a :href="`mailto:${vendor.email}`" class="contact-item group">
                                        <div
                                            class="contact-icon bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Email</p>
                                            <p class="text-gray-600">{{ vendor.email }}</p>
                                        </div>
                                    </a>
                                    <a :href="vendor.website" target="_blank" class="contact-item group">
                                        <div
                                            class="contact-icon bg-purple-100 text-purple-600 group-hover:bg-purple-600 group-hover:text-white">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Website</p>
                                            <p class="text-gray-600">Visit our site</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Reviews Tab -->
                <div v-if="activeTab === 'reviews'" class="p-8">
                    <!-- Reviews Summary -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                        <div class="review-summary-card">
                            <div class="text-6xl font-bold text-indigo-600 mb-3">{{ vendor.rating }}</div>
                            <div class="flex justify-center gap-1 mb-3">
                                <i v-for="n in 5" :key="n"
                                    :class="['fas fa-star text-yellow-400 text-xl', n <= vendor.rating ? 'opacity-100' : 'opacity-30']"></i>
                            </div>
                            <p class="text-gray-600 font-medium">{{ vendor.reviewCount }} total reviews</p>
                        </div>

                        <div class="lg:col-span-2 space-y-4">
                            <div v-for="(count, rating) in vendor.ratingBreakdown" :key="rating" class="rating-bar">
                                <span class="rating-label">{{ rating }} star{{ rating > 1 ? 's' : '' }}</span>
                                <div class="rating-progress">
                                    <div class="rating-fill"
                                        :style="{ width: (count / vendor.reviewCount) * 100 + '%' }"></div>
                                </div>
                                <span class="rating-count">{{ count }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Individual Reviews -->
                    <div class="space-y-6">
                        <div v-for="review in vendor.reviews" :key="review.id" class="review-card">
                            <div class="review-header">
                                <img :src="review.avatar" :alt="review.name" class="review-avatar" />
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 text-lg">{{ review.name }}</h4>
                                    <div class="flex items-center gap-4 mt-1">
                                        <div class="flex gap-1">
                                            <i v-for="n in 5" :key="n"
                                                :class="['fas fa-star text-yellow-400', n <= review.rating ? 'opacity-100' : 'opacity-30']"></i>
                                        </div>
                                        <span class="text-gray-500 text-sm">{{ formatDate(review.date) }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-700 leading-relaxed text-lg mt-4">{{ review.comment }}</p>
                            <div v-if="review.photos" class="review-photos">
                                <img v-for="photo in review.photos" :key="photo" :src="photo" alt="Review photo"
                                    class="review-photo" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Location Tab -->
                <div v-if="activeTab === 'location'" class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Map Placeholder -->
                        <div class="map-container">
                            <div class="map-placeholder">
                                <i class="fas fa-map-marked-alt text-8xl text-indigo-300 mb-6"></i>
                                <h3 class="text-2xl font-bold text-gray-800 mb-3">Interactive Map</h3>
                                <p class="text-gray-600 text-lg">{{ vendor.address }}</p>
                                <button class="mt-6 btn-primary">
                                    <i class="fas fa-directions mr-2"></i>
                                    Get Directions
                                </button>
                            </div>
                        </div>

                        <!-- Location Details -->
                        <div class="space-y-8">
                            <div class="location-card">
                                <h3 class="text-2xl font-bold text-gray-900 mb-6">Service Information</h3>
                                <div class="space-y-6">
                                    <div class="location-detail">
                                        <i class="fas fa-map-marker-alt text-indigo-600"></i>
                                        <div>
                                            <span class="detail-label">Primary Location</span>
                                            <span class="detail-value">{{ vendor.location }}</span>
                                        </div>
                                    </div>
                                    <div class="location-detail">
                                        <i class="fas fa-compass text-indigo-600"></i>
                                        <div>
                                            <span class="detail-label">Service Radius</span>
                                            <span class="detail-value">{{ vendor.serviceRadius }} km coverage</span>
                                        </div>
                                    </div>
                                    <div class="location-detail">
                                        <i class="fas fa-home text-indigo-600"></i>
                                        <div>
                                            <span class="detail-label">Full Address</span>
                                            <span class="detail-value">{{ vendor.address }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="location-card">
                                <h3 class="text-xl font-bold text-gray-900 mb-4">Service Features</h3>
                                <div class="feature-list">
                                    <div class="feature-item">
                                        <i class="fas fa-truck text-green-600"></i>
                                        <span>Own transportation available</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-tools text-green-600"></i>
                                        <span>Setup and breakdown included</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-clock text-blue-600"></i>
                                        <span>Flexible scheduling options</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-info-circle text-orange-600"></i>
                                        <span>Delivery fee may apply for distant locations</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Booking Modal -->
        <div v-if="showBookingModal" class="modal-overlay" @click="closeBookingModal">
            <div class="booking-modal" @click.stop>
                <div class="modal-header">
                    <h3 class="modal-title">Book {{ vendor.name }}</h3>
                    <button @click="closeBookingModal" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form @submit.prevent="submitBooking" class="modal-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Event Date</label>
                            <input v-model="bookingForm.date" type="date" required class="form-input" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Event Time</label>
                            <input v-model="bookingForm.time" type="time" required class="form-input" />
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Duration (hours)</label>
                            <input v-model="bookingForm.duration" type="number" min="1" required class="form-input" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Select Package</label>
                            <select v-model="bookingForm.packageId" required class="form-input">
                                <option value="">Choose a package</option>
                                <option v-for="p in vendor.packages" :key="p.id" :value="p.id">
                                    {{ p.name }} - ₱{{ p.price.toLocaleString() }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact Name</label>
                        <input v-model="bookingForm.contactName" type="text" required class="form-input"
                            placeholder="Your full name" />
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Contact Phone</label>
                            <input v-model="bookingForm.contactPhone" type="tel" required class="form-input"
                                placeholder="+63 XXX XXX XXXX" />
                        </div>
                        <div class="form-group">
                            <label class="form-label">Contact Email</label>
                            <input v-model="bookingForm.contactEmail" type="email" required class="form-input"
                                placeholder="your@email.com" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Event Location</label>
                        <input v-model="bookingForm.location" type="text" required class="form-input"
                            placeholder="Enter event venue address" />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Additional Notes</label>
                        <textarea v-model="bookingForm.notes" class="form-textarea"
                            placeholder="Special requests, requirements, or additional details..."></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" @click="closeBookingModal" class="btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Booking Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Gallery Modal -->
        <div v-if="showGalleryModal" class="modal-overlay" @click="closeGallery">
            <div class="gallery-modal" @click.stop>
                <button @click="closeGallery" class="gallery-close">
                    <i class="fas fa-times"></i>
                </button>
                <img :src="vendor.gallery[galleryIndex]?.url" :alt="vendor.gallery[galleryIndex]?.caption"
                    class="gallery-image" />
                <div class="gallery-nav">
                    <button @click="prevImage" class="gallery-nav-btn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button @click="nextImage" class="gallery-nav-btn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="gallery-caption">
                    {{ vendor.gallery[galleryIndex]?.caption }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const activeTab = ref('overview')
const showBookingModal = ref(false)
const showGalleryModal = ref(false)
const galleryIndex = ref(0)
const isFavorite = ref(false)
const currentMonth = ref(new Date().getMonth())
const currentYear = ref(new Date().getFullYear())

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

const tabs = [
    { id: 'overview', name: 'Overview', icon: 'fas fa-home' },
    { id: 'reviews', name: 'Reviews', icon: 'fas fa-star' },
    { id: 'location', name: 'Location', icon: 'fas fa-map-marker-alt' }
]

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
    return [
        'calendar-date',
        {
            'available': date.available && date.inCurrentMonth,
            'booked': date.booked && date.inCurrentMonth,
            'today': date.isToday,
            'other-month': !date.inCurrentMonth
        }
    ]
}
</script>

<style scoped>
/* Global Styles */
.gradient-text-white {
    background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-badge {
    @apply flex items-center gap-2 bg-white bg-opacity-20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-medium border border-white border-opacity-30;
}

.btn-primary {
    @apply bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-center;
}

.btn-secondary {
    @apply bg-white bg-opacity-20 backdrop-blur-sm text-white px-6 py-3 rounded-xl font-semibold hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center border border-white border-opacity-30;
}

.btn-icon {
    @apply bg-white bg-opacity-20 backdrop-blur-sm rounded-xl hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center border border-white border-opacity-30;
}

/* Gallery Styles */
.gallery-item {
    @apply relative overflow-hidden rounded-xl shadow-lg transition-all duration-300;
}

.gallery-overlay {
    @apply absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center;
}

/* Specialty Tags */
.specialty-tag {
    @apply bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-medium flex items-center shadow-md hover:shadow-lg transition-shadow duration-200;
}

/* Sidebar Styles */
.sidebar-card {
    @apply bg-gradient-to-br from-gray-50 to-white p-6 rounded-2xl shadow-lg border border-gray-100;
}

.package-card {
    @apply bg-white p-6 rounded-xl border-2 border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all duration-300 cursor-pointer;
}

/* Calendar Styles */
.calendar-container {
    @apply bg-white rounded-xl overflow-hidden shadow-md;
}

.calendar-header {
    @apply bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-4 flex justify-between items-center;
}

.calendar-nav-btn {
    @apply hover:bg-white hover:bg-opacity-20 p-2 rounded-full transition-colors duration-200;
}

.calendar-body {
    @apply p-4;
}

.calendar-weekdays {
    @apply grid grid-cols-7 gap-1 mb-3;
}

.calendar-weekday {
    @apply text-center text-xs font-semibold text-gray-500 py-2;
}

.calendar-dates {
    @apply grid grid-cols-7 gap-1;
}

.calendar-date {
    @apply text-center py-3 text-sm rounded-lg cursor-pointer transition-all duration-200 font-medium;
}

.calendar-date.available {
    @apply bg-green-100 text-green-800 hover:bg-green-200;
}

.calendar-date.booked {
    @apply bg-red-100 text-red-800 cursor-not-allowed;
}

.calendar-date.today {
    @apply ring-2 ring-indigo-500;
}

.calendar-date.other-month {
    @apply text-gray-300;
}

.calendar-legend {
    @apply flex justify-center gap-6 mt-6 text-xs;
}

.legend-item {
    @apply flex items-center gap-2;
}

/* Contact Styles */
.contact-item {
    @apply flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-all duration-200 cursor-pointer;
}

.contact-icon {
    @apply w-12 h-12 rounded-full flex items-center justify-center transition-all duration-200;
}

/* Reviews Styles */
.review-summary-card {
    @apply bg-gradient-to-br from-indigo-50 to-purple-50 p-8 rounded-2xl text-center border border-indigo-100;
}

.rating-bar {
    @apply flex items-center gap-4;
}

.rating-label {
    @apply text-sm text-gray-600 w-20 flex-shrink-0;
}

.rating-progress {
    @apply flex-1 bg-gray-200 rounded-full h-3 overflow-hidden;
}

.rating-fill {
    @apply bg-gradient-to-r from-indigo-500 to-purple-600 h-full rounded-full transition-all duration-500;
}

.rating-count {
    @apply text-sm text-gray-600 w-8 flex-shrink-0;
}

.review-card {
    @apply bg-gradient-to-br from-gray-50 to-white p-6 rounded-2xl shadow-sm border border-gray-100;
}

.review-header {
    @apply flex items-start gap-4;
}

.review-avatar {
    @apply w-16 h-16 rounded-full object-cover ring-4 ring-indigo-100;
}

.review-photos {
    @apply flex gap-3 mt-4;
}

.review-photo {
    @apply w-24 h-24 rounded-lg object-cover cursor-pointer hover:scale-105 transition-transform duration-200;
}

/* Location Styles */
.map-container {
    @apply relative;
}

.map-placeholder {
    @apply bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-8 flex flex-col items-center justify-center text-center min-h-96 border-2 border-dashed border-indigo-200;
}

.location-card {
    @apply bg-gradient-to-br from-gray-50 to-white p-6 rounded-2xl shadow-lg border border-gray-100;
}

.location-detail {
    @apply flex items-center gap-4;
}

.detail-label {
    @apply font-semibold text-gray-700 block;
}

.detail-value {
    @apply text-gray-600 block;
}

.feature-list {
    @apply space-y-4;
}

.feature-item {
    @apply flex items-center gap-3 text-gray-700;
}

/* Modal Styles */
.modal-overlay {
    @apply fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center p-4 z-50;
}

.booking-modal {
    @apply bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto;
}

.modal-header {
    @apply flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50;
}

.modal-title {
    @apply text-2xl font-bold text-gray-900;
}

.modal-close {
    @apply text-gray-500 hover:text-gray-700 text-2xl w-10 h-10 rounded-full hover:bg-gray-200 transition-colors duration-200;
}

.modal-body {
    @apply p-6 space-y-6;
}

.modal-footer {
    @apply flex gap-4 pt-6;
}

.form-grid {
    @apply grid grid-cols-1 md:grid-cols-2 gap-6;
}

.form-group {
    @apply space-y-2;
}

.form-label {
    @apply block text-sm font-semibold text-gray-700;
}

.form-input {
    @apply w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors duration-200;
}

.form-textarea {
    @apply w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent h-32 resize-none transition-colors duration-200;
}

/* Gallery Modal */
.gallery-modal {
    @apply relative max-w-6xl w-full h-full flex items-center justify-center;
}

.gallery-close {
    @apply absolute top-4 right-4 z-10 text-white text-2xl w-12 h-12 rounded-full bg-black bg-opacity-50 hover:bg-opacity-70 transition-colors duration-200;
}

.gallery-image {
    @apply max-w-full max-h-full object-contain rounded-lg;
}

.gallery-nav {
    @apply absolute inset-y-0 left-0 right-0 flex items-center justify-between px-4;
}

.gallery-nav-btn {
    @apply text-white text-3xl w-16 h-16 rounded-full bg-black bg-opacity-50 hover:bg-opacity-70 transition-colors duration-200;
}

.gallery-caption {
    @apply absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-70 text-white px-6 py-3 rounded-full text-center;
}

/* Video Container */
.video-container {
    @apply relative rounded-2xl overflow-hidden shadow-lg;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-grid {
        @apply grid-cols-1;
    }

    .calendar-date {
        @apply py-2 text-xs;
    }

    .review-avatar {
        @apply w-12 h-12;
    }

    .gallery-nav-btn {
        @apply w-12 h-12 text-xl;
    }
}
</style>
