<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import QuickBookingStepperModal from '@/Components/QuickBookingStepperModal.vue';
import ClientNavbar from '@/Components/ClientNavbar.vue'
import NewServiceCard from '@/Components/Client/NewServiceCard.vue';


const eventModal = ref(null)


defineProps({
    services: {
        type: Object,
    },
    categories: {
        type: Array
    }
});

const search = ref("")
const selectedCategory = ref("")

const handleSearch = () => {
    router.get(route("client.service.index", {
        categories: selectedCategory.value || null,
        search: search.value || null
    }))
}


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
                            <!-- Search Input -->
                            <div class="flex-1">
                                <input v-model="search" type="text" placeholder="Search vendors or services..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Category Select -->
                            <div class="w-full sm:w-48">
                                <select v-model="selectedCategory"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Categories</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Search Button -->
                            <button @click="handleSearch"
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
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
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
                        <NewServiceCard :service="service" />
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
