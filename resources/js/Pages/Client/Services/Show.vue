<script setup>
import BookingModal from '@/Components/Client/BookingModal.vue'
import ClientNavbar from '@/Components/ClientNavbar.vue'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

// Reactive data

const props = defineProps({
    service: {
        type: Object
    }
})

console.log(props.service)

const currentImageIndex = ref(0)
const selectedDate = ref('')
const selectedTime = ref('')
const guestCount = ref(50)
const activeTab = ref('overview')
const bookingModal = ref(null)


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
    if (currentImageIndex.value < props.service.images.length - 1) {
        currentImageIndex.value++
    }
}

const handleBooking = () => {
    // console.log(selectedDate.value, selectedTime.value)
    bookingModal.value.openModal(selectedDate.value, selectedTime.value)
}

const requestQuote = () => {
    alert('Quote request submitted! We will contact you within 24 hours.')
    // Handle quote request logic here
}
</script>

<template>
    <BookingModal ref="bookingModal" :service="service" :time="selectedTime" :date="selectedDate" />
    <div class="min-h-screen bg-gray-50">
        <!-- <ClientNavbar /> -->
        <!-- Header Navigation -->
        <div class="bg-[#bad6d8] shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 py-3">
                <Link href="/client/services"
                    class="flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Services
                </Link>
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
                                <div class="flex justify-start items-center gap-3 mb-3">
                                    <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                        {{ service.category_name }}
                                    </span>
                                    <div v-if="service.category_name === 'Catering' && service.catering_service?.is_customizable"
                                        class="">
                                        <div
                                            class="inline-flex items-center bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 text-sm px-3 py-1 rounded-full">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a1 1 0 01-1-1V9a1 1 0 011-1h1a2 2 0 100-4H4a1 1 0 01-1-1V4a1 1 0 011-1h3a1 1 0 001-1z" />
                                            </svg>
                                            Customizable Menu Available
                                        </div>
                                    </div>
                                </div>

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
                                    <li v-for="feature in service.specifications" :key="feature"
                                        class="flex items-start">
                                        <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ feature }}</span>
                                    </li>
                                </ul>

                                <h4 v-if="service.category_name === 'Catering'" class="text-lg font-semibold mb-3">
                                    Menu:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div v-for="(dishes, category) in service.menuCategories" :key="category"
                                        class="bg-gray-50 rounded-lg p-4">
                                        <h5 class="font-semibold text-gray-900 mb-2">{{ category }}</h5>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <li v-for="item in dishes" :key="item">• {{ item }}</li>
                                        </ul>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div v-show="activeTab === 'vendor'">
                            <div class="flex items-start space-x-6">
                                <img :src="service.vendor.avatar" :alt="service.vendor.name"
                                    class="w-20 h-20 rounded-full object-cover">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold mb-2">{{ service.vendor.business_name }}</h3>
                                    <p class="text-gray-600 mb-4">{{ service.vendor.description }}</p>

                                    <div class="grid grid-cols-2 gap-4 mb-6">
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
                                <p class="text-gray-600">{{ service.servingSize }} (min. {{ service.minimumGuests }}
                                    guests)</p>
                            </div>

                            <!-- Quick Booking Form -->
                            <form @submit.prevent="handleBooking" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Date</label>
                                    <input v-model="selectedDate" type="date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Time</label>
                                    <select v-model="selectedTime"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                        <option value="">Select time</option>
                                        <option value="11:00">11:00 AM - Brunch</option>
                                        <option value="12:00">12:00 PM - Lunch</option>
                                        <option value="18:00">6:00 PM - Dinner</option>
                                        <option value="19:00">7:00 PM - Evening</option>
                                    </select>
                                </div>

                                <div v-if="service.category_name === 'Catering'">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Number of Guests</label>
                                    <input v-model="guestCount" type="number" :min="service.minimumGuests"
                                        placeholder="Enter guest count"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                </div>

                                <div v-if="guestCount >= service.minimumGuests" class="bg-blue-50 p-3 rounded-lg">
                                    <div class="text-sm text-blue-700">
                                        <span class="font-medium">Estimated Total: {{ formatPrice(service.price *
                                            guestCount) }}</span>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                    Book Now
                                </button>

                                <button type="button" @click="requestQuote"
                                    class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                    Chat Vendor
                                </button>
                            </form>
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
                                    <span class="text-sm">{{ service.vendor.contact_number }}</span>
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
                                    <span class="font-medium text-green-600">Within 4 hours</span>
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
