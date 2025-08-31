<script setup>
import ClientNavbar from '@/Components/ClientNavbar.vue'
import { ref } from 'vue'


// Reactive data
const currentImageIndex = ref(0)
const selectedDate = ref('')
const selectedTime = ref('')
const activeTab = ref('overview')

// Mock service data
const service = ref({
    id: 1,
    name: "Premium Wedding Photography Package",
    description: "Capture your special day with our professional wedding photography service. We specialize in candid moments, artistic compositions, and timeless memories that you'll treasure forever. Our team of experienced photographers will work closely with you to understand your vision and deliver stunning photographs that tell the story of your love.",
    price: 75000,
    category: "Photography",
    rating: 4.8,
    reviewCount: 127,
    duration: "8-10 hours",
    teamSize: "2-3 photographers",
    advanceBooking: "2 weeks minimum",
    paymentTerms: "50% deposit, 50% on completion",
    images: [
        "https://images.unsplash.com/photo-1519741497674-611481863552?w=800&h=600&fit=crop",
        "https://images.unsplash.com/photo-1606800052052-a08af7148866?w=800&h=600&fit=crop",
        "https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=800&h=600&fit=crop",
        "https://images.unsplash.com/photo-1537633552985-df8429e8048b?w=800&h=600&fit=crop",
        "https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=800&h=600&fit=crop"
    ],
    features: [
        "Professional photography equipment and lighting",
        "Pre-wedding consultation and planning session",
        "Full day coverage (8-10 hours)",
        "High-resolution edited photos (500+ images)",
        "Online gallery for easy sharing with family and friends",
        "Printed photo album (premium quality)",
        "Raw files available upon request",
        "Backup photographer included",
        "Professional photo editing and retouching",
        "Same-day preview photos (20-30 images)"
    ],
    vendor: {
        name: "Maria Santos",
        businessName: "Capture Moments Photography",
        bio: "Professional wedding photographer with over 8 years of experience. Specialized in candid and artistic wedding photography. Based in Manila with coverage throughout the Philippines.",
        location: "Manila, Philippines",
        experience: 8,
        completedServices: 250,
        phone: "+63 917 123 4567",
        email: "hello@capturemoments.ph",
        website: "www.capturemoments.ph",
        avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b407?w=150&h=150&fit=crop&crop=face"
    },
    reviews: [
        {
            id: 1,
            name: "Sarah Johnson",
            rating: 5,
            date: "2 weeks ago",
            comment: "Maria and her team were absolutely incredible! They captured every special moment of our wedding day beautifully. The photos turned out even better than we imagined. Highly recommended!",
            avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=50&h=50&fit=crop&crop=face"
        },
        {
            id: 2,
            name: "Michael Chen",
            rating: 5,
            date: "1 month ago",
            comment: "Professional, creative, and so easy to work with. Maria made us feel comfortable throughout the entire process. The final photos exceeded our expectations!",
            avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face"
        },
        {
            id: 3,
            name: "Jennifer Lopez",
            rating: 4,
            date: "2 months ago",
            comment: "Great service and beautiful photos. Maria was very professional and captured all the important moments. The online gallery made it easy to share with our family.",
            avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=50&h=50&fit=crop&crop=face"
        }
    ]
})

// Navigation tabs
const tabs = ref([
    { id: 'overview', name: 'Overview' },
    { id: 'vendor', name: 'About Vendor' },
    { id: 'reviews', name: 'Reviews' }
])

// Methods
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price)
}

const previousImage = () => {
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--
    }
}

const nextImage = () => {
    if (currentImageIndex.value < service.value.images.length - 1) {
        currentImageIndex.value++
    }
}

const handleBooking = () => {
    if (selectedDate.value && selectedTime.value) {
        alert(`Booking request submitted for ${selectedDate.value} at ${selectedTime.value}`)
        // Handle booking logic here
    }
}

const requestQuote = () => {
    alert('Quote request submitted! We will contact you within 24 hours.')
    // Handle quote request logic here
}
</script>

<template>

    <div class="min-h-screen bg-gray-50">
        <ClientNavbar />
        <!-- Header Navigation -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 py-3">
                <button class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Services
                </button>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Image Gallery -->
                    <div class="relative mb-8">
                        <div class="relative h-96 bg-gray-200 rounded-xl overflow-hidden">
                            <img :src="service.images[currentImageIndex]" :alt="service.name"
                                class="w-full h-full object-cover">
                            <!-- Image Navigation -->
                            <div class="absolute inset-0 flex items-center justify-between p-4">
                                <button @click="previousImage"
                                    class="bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-colors"
                                    :disabled="currentImageIndex === 0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button @click="nextImage"
                                    class="bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-colors"
                                    :disabled="currentImageIndex === service.images.length - 1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                            <!-- Image Counter -->
                            <div
                                class="absolute bottom-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
                                {{ currentImageIndex + 1 }} / {{ service.images.length }}
                            </div>
                        </div>
                        <!-- Thumbnail Gallery -->
                        <div class="flex space-x-2 mt-4 overflow-x-auto">
                            <button v-for="(image, index) in service.images" :key="index"
                                @click="currentImageIndex = index"
                                class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all"
                                :class="currentImageIndex === index ? 'border-blue-600' : 'border-gray-200 hover:border-gray-300'">
                                <img :src="image" :alt="`${service.name} ${index + 1}`"
                                    class="w-full h-full object-cover">
                            </button>
                        </div>
                    </div>

                    <!-- Service Info -->
                    <div class="bg-white rounded-xl shadow-sm p-8">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <span
                                    class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full mb-3">
                                    {{ service.category }}
                                </span>
                                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ service.name }}</h1>
                                <div class="flex items-center space-x-4 text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-yellow-400 mr-1" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="font-medium">{{ service.rating }}</span>
                                        <span class="text-gray-500 ml-1">({{ service.reviewCount }} reviews)</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ service.vendor.location }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button
                                    class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                                <button
                                    class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Tabs Navigation -->
                        <div class="border-b border-gray-200 mb-6">
                            <nav class="flex space-x-8">
                                <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                                    class="py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                                    :class="activeTab === tab.id
                                        ? 'border-blue-600 text-blue-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                                    {{ tab.name }}
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div v-show="activeTab === 'overview'">
                            <div class="prose max-w-none">
                                <h3 class="text-xl font-semibold mb-4">About This Service</h3>
                                <p class="text-gray-700 leading-relaxed mb-6">{{ service.description }}</p>

                                <h4 class="text-lg font-semibold mb-3">What's Included:</h4>
                                <ul class="space-y-2 mb-6">
                                    <li v-for="feature in service.features" :key="feature" class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ feature }}</span>
                                    </li>
                                </ul>

                                <h4 class="text-lg font-semibold mb-3">Service Details:</h4>
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Duration: {{ service.duration }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                                        </svg>
                                        <span>Team Size: {{ service.teamSize }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Advance Booking: {{ service.advanceBooking }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Payment: {{ service.paymentTerms }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-show="activeTab === 'vendor'">
                            <div class="flex items-start space-x-6">
                                <img :src="service.vendor.avatar" :alt="service.vendor.name"
                                    class="w-20 h-20 rounded-full object-cover">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold mb-2">{{ service.vendor.businessName }}</h3>
                                    <p class="text-gray-600 mb-4">{{ service.vendor.bio }}</p>

                                    <div class="grid grid-cols-2 gap-4 mb-6">
                                        <div>
                                            <span class="text-sm text-gray-500">Years of Experience</span>
                                            <p class="font-semibold">{{ service.vendor.experience }} years</p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500">Services Completed</span>
                                            <p class="font-semibold">{{ service.vendor.completedServices }}+</p>
                                        </div>
                                    </div>

                                    <div class="flex space-x-4">
                                        <button
                                            class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </svg>
                                            Contact Vendor
                                        </button>
                                        <button
                                            class="flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                            </svg>
                                            Call Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-show="activeTab === 'reviews'">
                            <div class="space-y-6">
                                <div v-for="review in service.reviews" :key="review.id"
                                    class="border-b border-gray-200 pb-6">
                                    <div class="flex items-start space-x-4">
                                        <img :src="review.avatar" :alt="review.name"
                                            class="w-12 h-12 rounded-full object-cover">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-semibold">{{ review.name }}</h4>
                                                <span class="text-sm text-gray-500">{{ review.date }}</span>
                                            </div>
                                            <div class="flex items-center mb-2">
                                                <div class="flex">
                                                    <svg v-for="n in 5" :key="n" class="w-4 h-4"
                                                        :class="n <= review.rating ? 'text-yellow-400' : 'text-gray-300'"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </div>
                                                <span class="ml-2 text-sm text-gray-600">{{ review.rating }}/5</span>
                                            </div>
                                            <p class="text-gray-700">{{ review.comment }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8">
                        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                            <div class="text-center mb-6">
                                <div class="text-3xl font-bold text-green-600 mb-2">{{ formatPrice(service.price) }}
                                </div>
                                <p class="text-gray-600">Starting price</p>
                            </div>

                            <!-- Quick Booking Form -->
                            <form @submit.prevent="handleBooking" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Date</label>
                                    <input v-model="selectedDate" type="date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Time</label>
                                    <select v-model="selectedTime"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                        <option value="">Select time</option>
                                        <option value="08:00">8:00 AM</option>
                                        <option value="10:00">10:00 AM</option>
                                        <option value="12:00">12:00 PM</option>
                                        <option value="14:00">2:00 PM</option>
                                        <option value="16:00">4:00 PM</option>
                                        <option value="18:00">6:00 PM</option>
                                    </select>
                                </div>

                                <button type="submit"
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                    Book Now
                                </button>

                                <button type="button" @click="requestQuote"
                                    class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                    Request Custom Quote
                                </button>
                            </form>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex items-center justify-center space-x-4 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Free cancellation
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Instant confirmation
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vendor Contact Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="font-semibold mb-4">Contact Vendor</h3>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                    <span class="text-sm">{{ service.vendor.phone }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                    <span class="text-sm">{{ service.vendor.email }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm">{{ service.vendor.website }}</span>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Response time:</span>
                                    <span class="font-medium text-green-600">Within 2 hours</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>



<style scoped>
.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.prose {
    max-width: none;
}

.prose h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.prose h4 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.prose p {
    margin-bottom: 1rem;
}

.prose ul {
    margin-bottom: 1.5rem;
}

.prose ul li {
    margin-bottom: 0.5rem;
}

/* Custom scrollbar for thumbnail gallery */
.overflow-x-auto::-webkit-scrollbar {
    height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
