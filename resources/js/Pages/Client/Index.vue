<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

import QuickBookingStepperModal from '@/Components/QuickBookingStepperModal.vue';
import ClientNavbar from '@/Components/ClientNavbar.vue'

const eventModal = ref(null)

defineProps({
    services: {
        type: Object,
    },
    categories: {
        type: Array
    }
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};
</script>

<template>

    <Head title="Home" />

    <div class="min-h-screen bg-gray-50">
        <QuickBookingStepperModal ref="eventModal" :categories="categories" />

        <!-- Navigation -->
        <ClientNavbar />

        <!-- Hero Section -->
        <section class="bg-white border-b border-gray-200">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center max-w-3xl mx-auto">
                    <h1 class="text-4xl font-semibold text-gray-900 mb-4">
                        Find the right vendors for your event
                    </h1>
                    <p class="text-lg text-gray-600 mb-12">
                        Connect with verified professionals and manage your bookings in one place
                    </p>

                    <!-- Search Bar -->
                    <div class="bg-white border border-gray-300 rounded-lg p-4 max-w-2xl mx-auto mb-8">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-1">
                                <input type="text" placeholder="Search vendors or services..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="w-full sm:w-48">
                                <select
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option>All Categories</option>
                                    <option>Catering</option>
                                    <option>Photography</option>
                                    <option>Sound Systems</option>
                                    <option>Entertainment</option>
                                    <option>Makeup Artists</option>
                                    <option>Attire Rentals</option>
                                </select>
                            </div>
                            <button
                                class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                                Search
                            </button>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <button @click="eventModal.open()"
                        class="inline-flex items-center px-6 py-3 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition-colors font-medium">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Start Planning
                    </button>
                </div>
            </div>
        </section>

        <!-- Quick Stats -->
        <section class="py-12 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-semibold text-gray-900 mb-1">3</div>
                        <div class="text-sm text-gray-600">Active Bookings</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-semibold text-gray-900 mb-1">12</div>
                        <div class="text-sm text-gray-600">Completed Events</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-semibold text-gray-900 mb-1">8</div>
                        <div class="text-sm text-gray-600">Saved Vendors</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-semibold text-gray-900 mb-1">15%</div>
                        <div class="text-sm text-gray-600">Budget Saved</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Services -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">Featured Services</h2>
                        <p class="text-gray-600 mt-1">Top-rated vendors in your area</p>
                    </div>
                    <Link href="/services" class="text-blue-600 hover:text-blue-700 font-medium">
                    View All →
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="service in services.data" :key="service.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="relative">
                            <img :src="service.image_url" :alt="service.title" class="w-full h-48 object-cover">
                            <div class="absolute top-3 right-3 flex space-x-2">
                                <!-- <span v-if="service.availability === 'available'"
                        class="bg-green-500 text-white px-2 py-1 text-xs rounded">
                        Available
                    </span> -->
                                <!-- <span v-else class="bg-red-500 text-white px-2 py-1 text-xs rounded">
                        Unavailable
                    </span> -->
                                <!-- <button
                                    class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button> -->
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                    {{ service.category_name }}
                                </span>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ service.dateAdded }}
                                </div>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-1">{{ service.title }}</h3>
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ service.description }}</p>
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-lg font-bold text-green-600">{{ formatPrice(service.price) }}</div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    {{ service.vendor.rating ? service.vendor.rating : 'No ratings yet' }}
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 mb-3">
                                <span class="font-medium">{{ service.vendor.business_name }}</span> • {{
                                    service.vendor.location }}
                            </div>
                            <div class="flex space-x-2">
                                <button
                                    class="flex-1 bg-blue-600 text-white py-2 px-4 rounded text-sm hover:bg-blue-700 transition-colors">
                                    Book Now
                                </button>
                                <button
                                    class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded text-sm hover:bg-gray-50 transition-colors">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="py-16 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">Browse Categories</h2>
                    <p class="text-gray-600">Find vendors by service type</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link href="/categories/catering"
                        class="group p-6 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H19M7 13v4a2 2 0 002 2h4a2 2 0 002-2v-4m-6 0a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Catering Services</h3>
                    <p class="text-gray-600 text-sm">Professional food and beverage services</p>
                    </Link>

                    <Link href="/categories/photography"
                        class="group p-6 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Photography</h3>
                    <p class="text-gray-600 text-sm">Professional event photographers</p>
                    </Link>

                    <Link href="/categories/sound-systems"
                        class="group p-6 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M12 8l-4 4H5a1 1 0 00-1 1v2a1 1 0 001 1h3l4 4V8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Sound Systems</h3>
                    <p class="text-gray-600 text-sm">Audio equipment and technical support</p>
                    </Link>

                    <Link href="/categories/entertainers"
                        class="group p-6 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Entertainment</h3>
                    <p class="text-gray-600 text-sm">Bands, DJs, and performers</p>
                    </Link>

                    <Link href="/categories/makeup"
                        class="group p-6 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Makeup Artists</h3>
                    <p class="text-gray-600 text-sm">Professional beauty services</p>
                    </Link>

                    <Link href="/categories/attire"
                        class="group p-6 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors">
                    <div class="w-12 h-12 bg-blue-50 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Attire Rentals</h3>
                    <p class="text-gray-600 text-sm">Formal wear and costume rentals</p>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8">
                    <div>
                        <div class="text-xl font-semibold mb-4">Eventory</div>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Connecting event planners with trusted local vendors.
                        </p>
                    </div>

                    <div>
                        <h3 class="font-medium mb-4">Platform</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <Link href="/dashboard" class="text-gray-400 hover:text-white">Dashboard</Link>
                            </li>
                            <li>
                                <Link href="/bookings" class="text-gray-400 hover:text-white">My Bookings</Link>
                            </li>
                            <li>
                                <Link href="/services" class="text-gray-400 hover:text-white">Browse Services</Link>
                            </li>
                            <li>
                                <Link href="/vendors" class="text-gray-400 hover:text-white">Find Vendors</Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-medium mb-4">Categories</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <Link href="/categories/catering" class="text-gray-400 hover:text-white">Catering</Link>
                            </li>
                            <li>
                                <Link href="/categories/photography" class="text-gray-400 hover:text-white">Photography
                                </Link>
                            </li>
                            <li>
                                <Link href="/categories/sound-systems" class="text-gray-400 hover:text-white">Audio &
                                Sound</Link>
                            </li>
                            <li>
                                <Link href="/categories/entertainers" class="text-gray-400 hover:text-white">
                                Entertainment</Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-medium mb-4">Support</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <Link href="/help" class="text-gray-400 hover:text-white">Help Center</Link>
                            </li>
                            <li>
                                <Link href="/contact" class="text-gray-400 hover:text-white">Contact</Link>
                            </li>
                            <li>
                                <Link href="/about" class="text-gray-400 hover:text-white">About</Link>
                            </li>
                            <li>
                                <Link href="/privacy" class="text-gray-400 hover:text-white">Privacy</Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-8 pt-8">
                    <p class="text-gray-400 text-sm text-center">
                        © 2024 Eventory. All rights reserved. | Capstone Project
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
