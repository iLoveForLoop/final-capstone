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
</script>

<template>

    <Head title="Welcome Client" />

    <div class="min-h-screen bg-gradient-to-br from-purple-50 to-blue-50">
        <QuickBookingStepperModal ref="eventModal" :categories="categories" />
        <!-- Sticky Navigation -->
        <ClientNavbar />

        <!-- Hero Section with Search -->
        <section class="container mx-auto px-6 py-12 md:py-20">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    Find Perfect Vendors for Your Perfect Event
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    Book trusted caterers, photographers, and more with our streamlined platform
                </p>

                <!-- Search Bar -->
                <div class="bg-white p-2 rounded-lg shadow-md max-w-2xl mx-auto">
                    <div class="flex flex-col md:flex-row gap-2">
                        <div class="flex-1">
                            <input type="text" placeholder="What service are you looking for?"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div class="w-full md:w-auto">
                            <select
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option>All Categories</option>
                                <option>Catering</option>
                                <option>Photography</option>
                                <option>Sound Systems</option>
                                <option>Entertainers</option>
                            </select>
                        </div>
                        <button
                            class="w-full md:w-auto px-6 py-3 bg-[#239BA7] text-white rounded-lg hover:bg-[#239BA7] transition font-medium">
                            Search
                        </button>
                    </div>
                </div>

                <div class="mt-8">
                    <button @click="eventModal.open()"
                        class="inline-flex items-center px-6 py-3 bg-[#239BA7] text-white rounded-lg hover:bg-[#239BA7] transition font-medium">
                        Start Planning Now
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Featured Services Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <div class="flex justify-between items-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900">Featured Services</h2>
                    <Link href="/services" class="text-[#239BA7] hover:text-purple-800 font-medium">
                    View all services →
                    </Link>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Service Cards -->
                    <div v-for="service in services.data" :key="service.id"
                        class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition">
                        <div class="h-48 bg-purple-100 flex items-center justify-center">
                            <img :src="service.image_url" :alt="service.name" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">{{ service.name }}</h3>
                            <p class="text-gray-600 mb-4">{{ service.description }}</p>
                            <div class="flex justify-between items-center">
                                <span class="font-bold">{{ service.price }}</span>
                                <Link :href="`/services/${service.id}`"
                                    class="text-[#239BA7] hover:text-purple-800 font-medium">
                                View
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Vendor Categories -->
        <section class="py-16">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center text-gray-900 mb-16">Vendor Categories</h2>
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
                    <Link href="/categories/catering"
                        class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition hover:border-purple-300">
                    <h3 class="text-xl font-semibold mb-3 text-[#239BA7]">Catering Services</h3>
                    <p class="text-gray-600">
                        Find the perfect food and beverage providers for your event
                    </p>
                    </Link>
                    <Link href="/categories/photography"
                        class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition hover:border-purple-300">
                    <h3 class="text-xl font-semibold mb-3 text-[#239BA7]">Photography</h3>
                    <p class="text-gray-600">
                        Professional photographers and photo booth services
                    </p>
                    </Link>
                    <Link href="/categories/sound-systems"
                        class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition hover:border-purple-300">
                    <h3 class="text-xl font-semibold mb-3 text-[#239BA7]">Sound Systems</h3>
                    <p class="text-gray-600">
                        Audio equipment and technical support for events
                    </p>
                    </Link>
                    <Link href="/categories/entertainers"
                        class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition hover:border-purple-300">
                    <h3 class="text-xl font-semibold mb-3 text-[#239BA7]">Entertainers</h3>
                    <p class="text-gray-600">
                        Bands, DJs, performers, and other entertainment
                    </p>
                    </Link>
                    <Link href="/categories/makeup"
                        class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition hover:border-purple-300">
                    <h3 class="text-xl font-semibold mb-3 text-[#239BA7]">Makeup Artists</h3>
                    <p class="text-gray-600">
                        Professional beauty services for special occasions
                    </p>
                    </Link>
                    <Link href="/categories/attire"
                        class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition hover:border-purple-300">
                    <h3 class="text-xl font-semibold mb-3 text-[#239BA7]">Attire Rentals</h3>
                    <p class="text-gray-600">
                        Formal wear and costume rentals for your event
                    </p>
                    </Link>
                </div>
            </div>
        </section>



        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between">
                    <div class="mb-8 md:mb-0">
                        <div class="text-2xl font-bold mb-4">Eventory</div>
                        <p class="text-gray-400 max-w-md">
                            The centralized platform for event planners and local vendors to connect, collaborate, and
                            create
                            memorable experiences.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                        <div>
                            <h3 class="font-semibold text-lg mb-4">Product</h3>
                            <ul class="space-y-2">
                                <li>
                                    <Link href="/features" class="text-gray-400 hover:text-white transition">Features
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/pricing" class="text-gray-400 hover:text-white transition">Pricing
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/services" class="text-gray-400 hover:text-white transition">Vendors
                                    </Link>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-4">Company</h3>
                            <ul class="space-y-2">
                                <li>
                                    <Link href="/about" class="text-gray-400 hover:text-white transition">About</Link>
                                </li>
                                <li>
                                    <Link href="/contact" class="text-gray-400 hover:text-white transition">Contact
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/careers" class="text-gray-400 hover:text-white transition">Careers
                                    </Link>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-4">Legal</h3>
                            <ul class="space-y-2">
                                <li>
                                    <Link href="/privacy" class="text-gray-400 hover:text-white transition">Privacy
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/terms" class="text-gray-400 hover:text-white transition">Terms</Link>
                                </li>
                                <li>
                                    <Link href="/cookie-policy" class="text-gray-400 hover:text-white transition">Cookie
                                    Policy
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                    &copy; 2023 Eventory. All rights reserved. Capstone Project.
                </div>
            </div>
        </footer>
    </div>
</template>
