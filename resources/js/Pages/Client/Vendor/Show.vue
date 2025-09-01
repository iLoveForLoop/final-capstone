<template>
    <div class="min-h-screen bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500">
        <!-- Header Section -->
        <div class="bg-white bg-opacity-10 backdrop-blur-md border-b border-white border-opacity-20">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <button @click="goBack"
                    class="mb-6 bg-white bg-opacity-20 backdrop-blur-md text-white px-6 py-3 rounded-full hover:bg-opacity-30 transition-all duration-300 flex items-center gap-2 hover:-translate-x-1">
                    <i class="fas fa-arrow-left"></i>
                    Back to Search
                </button>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center text-white">
                    <!-- Vendor Avatar -->
                    <div class="relative w-32 h-32 mx-auto lg:mx-0">
                        <img :src="vendor.avatar" :alt="vendor.name"
                            class="w-full h-full rounded-full border-4 border-white border-opacity-30 object-cover" />
                        <div v-if="vendor.verified"
                            class="absolute -bottom-2 -right-2 bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center border-3 border-white">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                    </div>

                    <!-- Vendor Information -->
                    <div class="lg:col-span-1 text-center lg:text-left">
                        <h1 class="text-4xl font-bold mb-2">{{ vendor.name }}</h1>
                        <p class="text-xl opacity-90 mb-4">{{ vendor.category }}</p>

                        <div class="flex flex-col sm:flex-row gap-6 mb-4 justify-center lg:justify-start">
                            <div class="flex items-center gap-3">
                                <div class="flex gap-1">
                                    <i v-for="n in 5" :key="n"
                                        :class="['fas fa-star text-yellow-400', n <= vendor.rating ? 'opacity-100' : 'opacity-30']"></i>
                                </div>
                                <span class="font-medium">{{ vendor.rating }} ({{ vendor.reviewCount }} reviews)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ vendor.location }}</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 justify-center lg:justify-start">
                            <div
                                class="flex items-center gap-2 bg-white bg-opacity-10 backdrop-blur-md px-4 py-2 rounded-full">
                                <i class="fas fa-clock"></i>
                                <span class="text-sm">{{ vendor.responseTime }}</span>
                            </div>
                            <div
                                class="flex items-center gap-2 bg-white bg-opacity-10 backdrop-blur-md px-4 py-2 rounded-full">
                                <i class="fas fa-calendar-check"></i>
                                <span class="text-sm">{{ vendor.completedEvents }}+ Events</span>
                            </div>
                            <div
                                class="flex items-center gap-2 bg-white bg-opacity-10 backdrop-blur-md px-4 py-2 rounded-full">
                                <i :class="['fas fa-circle', vendor.available ? 'text-green-400' : 'text-red-400']"></i>
                                <span class="text-sm">{{ vendor.available ? 'Available' : 'Busy' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-end">
                        <button @click="openBookingModal"
                            class="bg-gradient-to-r from-red-500 to-red-600 text-white px-8 py-4 rounded-full font-semibold hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-calendar-plus"></i>
                            Book Now
                        </button>
                        <button @click="sendMessage"
                            class="bg-white bg-opacity-20 backdrop-blur-md text-white px-8 py-4 rounded-full font-semibold hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fas fa-comments"></i>
                            Message
                        </button>
                        <button @click="toggleFavorite"
                            class="bg-white bg-opacity-20 backdrop-blur-md text-white w-14 h-14 rounded-full hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                            <i :class="['fas fa-heart text-xl', isFavorite ? 'text-red-400' : 'text-white']"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6">
            <!-- Navigation Tabs -->
            <div class="flex bg-white rounded-t-2xl mt-8 shadow-xl overflow-hidden">
                <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id" :class="[
                    'flex-1 px-8 py-6 font-semibold transition-all duration-300 flex items-center justify-center gap-3',
                    activeTab === tab.id
                        ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white'
                        : 'text-gray-600 hover:bg-gray-50'
                ]">
                    <i :class="tab.icon"></i>
                    {{ tab.name }}
                </button>
            </div>

            <!-- Tab Content -->
            <div class="bg-white rounded-b-2xl shadow-xl min-h-96">
                <!-- Overview Tab -->
                <div v-if="activeTab === 'overview'" class="p-10">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="lg:col-span-2 space-y-10">
                            <!-- Photo Gallery -->
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-6">Portfolio Gallery</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <div v-for="(image, index) in vendor.gallery" :key="index"
                                        @click="openGallery(index)"
                                        class="relative aspect-video rounded-xl overflow-hidden cursor-pointer group hover:scale-105 transition-transform duration-300">
                                        <img :src="image.url" :alt="image.caption" class="w-full h-full object-cover" />
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-300 flex items-center justify-center">
                                            <i
                                                class="fas fa-expand-arrows-alt text-white text-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- About Section -->
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-6">About {{ vendor.name }}</h3>
                                <p class="text-gray-600 leading-relaxed mb-6">{{ vendor.description }}</p>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Specialties</h4>
                                    <div class="flex flex-wrap gap-3">
                                        <span v-for="specialty in vendor.specialties" :key="specialty"
                                            class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-4 py-2 rounded-full text-sm font-medium">
                                            {{ specialty }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Video Section -->
                            <div v-if="vendor.videoUrl">
                                <h3 class="text-2xl font-bold text-gray-800 mb-6">Showcase Video</h3>
                                <div class="rounded-2xl overflow-hidden shadow-lg">
                                    <video :src="vendor.videoUrl" controls poster="/api/placeholder/600/300"
                                        class="w-full h-80 object-cover">
                                    </video>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="space-y-8">
                            <!-- Pricing Packages -->
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-8 rounded-2xl shadow-lg">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Service Packages</h3>
                                <div class="space-y-6">
                                    <div v-for="package in vendor.packages" :key="package.id"
                                        class="bg-white p-6 rounded-xl border-2 border-transparent hover:border-indigo-500 hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                                        <div class="flex justify-between items-center mb-4">
                                            <h4 class="text-lg font-semibold text-gray-800">{{ package.name }}</h4>
                                            <span class="text-2xl font-bold text-red-500">₱{{
                                                package.price.toLocaleString() }}</span>
                                        </div>
                                        <ul class="space-y-2">
                                            <li v-for="feature in package.features" :key="feature"
                                                class="flex items-center gap-3 text-gray-600">
                                                <i class="fas fa-check text-green-500 text-sm"></i>
                                                {{ feature }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Availability Calendar -->
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-8 rounded-2xl shadow-lg">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Availability</h3>
                                <div class="bg-white rounded-xl overflow-hidden shadow-md">
                                    <div
                                        class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white p-4 flex justify-between items-center">
                                        <button @click="prevMonth"
                                            class="hover:bg-white hover:bg-opacity-20 p-2 rounded transition-colors duration-200">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <span class="font-semibold">{{ currentMonthYear }}</span>
                                        <button @click="nextMonth"
                                            class="hover:bg-white hover:bg-opacity-20 p-2 rounded transition-colors duration-200">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="p-4">
                                        <div class="grid grid-cols-7 gap-1 mb-2">
                                            <div v-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="day"
                                                class="text-center text-xs font-semibold text-gray-500 py-2">
                                                {{ day }}
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-7 gap-1">
                                            <div v-for="date in calendarDates" :key="date.date" :class="[
                                                'text-center py-2 text-sm rounded cursor-pointer transition-colors duration-200',
                                                date.available ? 'bg-green-100 text-green-800 hover:bg-green-200' : '',
                                                date.booked ? 'bg-red-100 text-red-800' : '',
                                                date.isToday ? 'ring-2 ring-indigo-500' : '',
                                                !date.inCurrentMonth ? 'text-gray-300' : ''
                                            ]">
                                                {{ date.day }}
                                            </div>
                                        </div>
                                        <div class="flex justify-center gap-6 mt-4 text-xs">
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 bg-green-200 rounded"></div>
                                                <span class="text-gray-600">Available</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 bg-red-200 rounded"></div>
                                                <span class="text-gray-600">Booked</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-8 rounded-2xl shadow-lg">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Contact Information</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-4 text-gray-600">
                                        <i class="fas fa-phone text-indigo-500 w-5"></i>
                                        <span>{{ vendor.phone }}</span>
                                    </div>
                                    <div class="flex items-center gap-4 text-gray-600">
                                        <i class="fas fa-envelope text-indigo-500 w-5"></i>
                                        <span>{{ vendor.email }}</span>
                                    </div>
                                    <div class="flex items-center gap-4 text-gray-600">
                                        <i class="fas fa-globe text-indigo-500 w-5"></i>
                                        <a :href="vendor.website" target="_blank"
                                            class="text-indigo-600 hover:underline">{{ vendor.website }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div v-if="activeTab === 'reviews'" class="p-10">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mb-10">
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-8 rounded-2xl text-center">
                            <div class="text-5xl font-bold text-indigo-600 mb-2">{{ vendor.rating }}</div>
                            <div class="flex justify-center gap-1 mb-2">
                                <i v-for="n in 5" :key="n"
                                    :class="['fas fa-star text-yellow-400', n <= vendor.rating ? 'opacity-100' : 'opacity-30']"></i>
                            </div>
                            <div class="text-gray-600">{{ vendor.reviewCount }} reviews</div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="space-y-3">
                                <div v-for="(count, rating) in vendor.ratingBreakdown" :key="rating"
                                    class="flex items-center gap-4">
                                    <span class="text-sm text-gray-600 w-16">{{ rating }} star{{ rating > 1 ? 's' : ''
                                        }}</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full transition-all duration-500"
                                            :style="{ width: (count / vendor.reviewCount) * 100 + '%' }"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 w-8">{{ count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div v-for="review in vendor.reviews" :key="review.id" class="bg-gray-50 p-6 rounded-2xl">
                            <div class="flex items-start gap-4 mb-4">
                                <img :src="review.avatar" :alt="review.name"
                                    class="w-12 h-12 rounded-full object-cover" />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800">{{ review.name }}</h4>
                                    <div class="flex items-center gap-4 mt-1">
                                        <div class="flex gap-1">
                                            <i v-for="n in 5" :key="n"
                                                :class="['fas fa-star text-yellow-400 text-sm', n <= review.rating ? 'opacity-100' : 'opacity-30']"></i>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ formatDate(review.date) }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-700 leading-relaxed mb-4">{{ review.comment }}</p>
                            <div v-if="review.photos" class="flex gap-3">
                                <img v-for="photo in review.photos" :key="photo" :src="photo" alt="Review photo"
                                    class="w-20 h-20 rounded-lg object-cover" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Tab -->
                <div v-if="activeTab === 'location'" class="p-10">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        <div
                            class="bg-gray-100 rounded-2xl p-8 flex flex-col items-center justify-center text-center min-h-80">
                            <i class="fas fa-map-marked-alt text-6xl text-indigo-500 mb-4"></i>
                            <p class="text-xl font-semibold text-gray-800 mb-2">Interactive Map</p>
                            <p class="text-gray-600">{{ vendor.address }}</p>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-6">Service Area</h3>
                                <div class="space-y-4">
                                    <div>
                                        <span class="font-semibold text-gray-700">Primary Location:</span>
                                        <span class="ml-2 text-gray-600">{{ vendor.location }}</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-700">Service Radius:</span>
                                        <span class="ml-2 text-gray-600">{{ vendor.serviceRadius }} km</span>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-700">Address:</span>
                                        <span class="ml-2 text-gray-600">{{ vendor.address }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-lg font-semibold text-gray-800 mb-4">Transportation & Setup</h4>
                                <ul class="space-y-3">
                                    <li class="flex items-center gap-3 text-gray-600">
                                        <i class="fas fa-check text-green-500"></i>
                                        Own transportation available
                                    </li>
                                    <li class="flex items-center gap-3 text-gray-600">
                                        <i class="fas fa-check text-green-500"></i>
                                        Setup and breakdown included
                                    </li>
                                    <li class="flex items-center gap-3 text-gray-600">
                                        <i class="fas fa-info-circle text-blue-500"></i>
                                        Delivery fee may apply for distant locations
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Modal -->
        <div v-if="showBookingModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click="closeBookingModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-96 overflow-y-auto" @click.stop>
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-800">Book {{ vendor.name }}</h3>
                    <button @click="closeBookingModal" class="text-gray-500 hover:text-gray-700 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submitBooking" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Event Date</label>
                            <input v-model="bookingForm.date" type="date" required
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Event Time</label>
                            <input v-model="bookingForm.time" type="time" required
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Duration (hours)</label>
                            <input v-model="bookingForm.duration" type="number" min="1" required
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Select Package</label>
                            <select v-model="bookingForm.packageId" required
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <option value="">Choose a package</option>
                                <option v-for="package in vendor.packages" :key="package.id" :value="package.id">
                                    {{ package.name }} - ₱{{ package.price.toLocaleString() }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Event Location</label>
                            <input v-model="bookingForm.location" type="text" placeholder="Enter event venue" required
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Additional Notes</label>
                            <textarea v-model="bookingForm.notes" placeholder="Special requests or requirements"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent h-24 resize-none"></textarea>
                        </div>
                        <div class="flex gap-4 pt-4">
                            <button type="button" @click="closeBookingModal"
                                class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-all duration-300">
                                Send Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'VendorProfile',
    data() {
        return {
            activeTab: 'overview',
            showBookingModal: false,
            isFavorite: false,
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),

            tabs: [
                { id: 'overview', name: 'Overview', icon: 'fas fa-home' },
                { id: 'reviews', name: 'Reviews', icon: 'fas fa-star' },
                { id: 'location', name: 'Location', icon: 'fas fa-map-marker-alt' }
            ],

            bookingForm: {
                date: '',
                time: '',
                duration: 4,
                packageId: '',
                location: '',
                notes: ''
            },

            vendor: {
                id: 1,
                name: "Stellar Sounds",
                category: "Sound System Provider",
                rating: 4.8,
                reviewCount: 127,
                location: "Quezon City, Metro Manila",
                responseTime: "Responds within 2 hours",
                completedEvents: 250,
                available: true,
                verified: true,
                avatar: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=120&h=120&fit=crop&crop=face",
                phone: "+63 912 345 6789",
                email: "info@stellarsounds.ph",
                website: "www.stellarsounds.ph",
                address: "123 Music Street, Quezon City, Metro Manila 1100",
                serviceRadius: 25,

                description: "Stellar Sounds is Metro Manila's premier audio equipment provider, specializing in high-quality sound systems for weddings, corporate events, and private celebrations. With over 8 years of experience, we pride ourselves on delivering crystal-clear audio experiences that make your events unforgettable.",

                specialties: ["Wedding Ceremonies", "Corporate Events", "Live Music", "DJ Setup", "Wireless Microphones"],

                videoUrl: "https://sample-videos.com/zip/10/mp4/SampleVideo_1280x720_1mb.mp4",

                gallery: [
                    { url: "https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=400&h=300&fit=crop", caption: "Wedding Setup" },
                    { url: "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=400&h=300&fit=crop", caption: "Corporate Event" },
                    { url: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400&h=300&fit=crop", caption: "DJ Equipment" },
                    { url: "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=400&h=300&fit=crop", caption: "Outdoor Setup" },
                    { url: "https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?w=400&h=300&fit=crop", caption: "Sound Mixing" },
                    { url: "https://images.unsplash.com/photo-1485846234645-a62644f84728?w=400&h=300&fit=crop", caption: "Microphone Systems" }
                ],

                packages: [
                    {
                        id: 1,
                        name: "Basic Package",
                        price: 8000,
                        features: ["2 Speakers", "1 Wireless Mic", "Basic Mixing", "4-hour service"]
                    },
                    {
                        id: 2,
                        name: "Premium Package",
                        price: 15000,
                        features: ["4 Speakers", "2 Wireless Mics", "DJ Mixer", "Lighting Effects", "8-hour service"]
                    },
                    {
                        id: 3,
                        name: "Deluxe Package",
                        price: 25000,
                        features: ["6 Speakers", "4 Wireless Mics", "Professional DJ Setup", "LED Lighting", "Fog Machine", "Full-day service"]
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
                        comment: "Absolutely amazing service! The sound quality was perfect for our wedding. Professional setup and the team was very accommodating.",
                        avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=50&h=50&fit=crop&crop=face",
                        photos: ["https://images.unsplash.com/photo-1519741497674-611481863552?w=150&h=150&fit=crop", "https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=150&h=150&fit=crop"]
                    },
                    {
                        id: 2,
                        name: "John Cruz",
                        rating: 5,
                        date: "2024-01-10",
                        comment: "Used them for our company anniversary. Great equipment, on-time setup, and excellent customer service. Highly recommended!",
                        avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face"
                    },
                    {
                        id: 3,
                        name: "Anna Reyes",
                        rating: 4,
                        date: "2024-01-05",
                        comment: "Good service overall. Sound quality was great, though setup took a bit longer than expected.",
                        avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=50&h=50&fit=crop&crop=face"
                    }
                ]
            }
        }
    },

    computed: {
        currentMonthYear() {
            const months = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            return `${months[this.currentMonth]} ${this.currentYear}`;
        },

        calendarDates() {
            const dates = [];
            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(date.getDate() + i);

                const today = new Date();
                const isToday = date.toDateString() === today.toDateString();
                const inCurrentMonth = date.getMonth() === this.currentMonth;

                dates.push({
                    date: date.toISOString().split('T')[0],
                    day: date.getDate(),
                    available: inCurrentMonth && Math.random() > 0.3,
                    booked: inCurrentMonth && Math.random() > 0.8,
                    isToday: isToday,
                    inCurrentMonth: inCurrentMonth
                });
            }

            return dates;
        }
    },

    methods: {
        goBack() {
            // Navigate back to vendor search
            console.log('Navigating back to search');
        },

        toggleFavorite() {
            this.isFavorite = !this.isFavorite;
            // Add to favorites logic
        },

        openBookingModal() {
            this.showBookingModal = true;
        },

        closeBookingModal() {
            this.showBookingModal = false;
            this.bookingForm = {
                date: '',
                time: '',
                duration: 4,
                packageId: '',
                location: '',
                notes: ''
            };
        },

        submitBooking() {
            // Handle booking submission
            console.log('Booking submitted:', this.bookingForm);
            alert('Booking request sent! The vendor will respond within 2 hours.');
            this.closeBookingModal();
        },

        sendMessage() {
            // Open messaging interface
            alert('Message feature would open here');
        },

        openGallery(index) {
            // Open gallery lightbox
            console.log('Opening gallery at index:', index);
        },

        prevMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
        },

        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    }
}
</script>

<style>
/* Font Awesome Icons */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

/* Additional custom styles if needed */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
