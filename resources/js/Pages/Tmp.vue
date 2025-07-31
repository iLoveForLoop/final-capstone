<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const page = usePage()

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    services: {
        type: Object,
    },
});

console.log('page: ', usePage())

</script>

<template>

    <Head title="Eventory - Streamlined Event Vendor Management" />

    <div class="min-h-screen bg-gradient-to-br from-purple-50 to-blue-50">
        <!-- Sticky Navigation -->
        <nav class="sticky top-0 z-50 bg-white shadow-sm">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <Link href="/" class="text-2xl font-bold text-purple-800">Eventory</Link>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <!-- Common Links -->
                        <Link href="/" class="text-gray-700 hover:text-purple-600 transition"
                            :class="{ 'font-semibold text-purple-600': page.value?.url === '/' }">Home</Link>
                        <Link href="/services" class="text-gray-700 hover:text-purple-600 transition"
                            :class="{ 'font-semibold text-purple-600': page.value?.url.startsWith('/services') }">Browse
                        Services</Link>

                        <!-- Conditional Links -->
                        <template v-if="page.value?.props.auth.user">
                            <Link href="/bookings" class="text-gray-700 hover:text-purple-600 transition"
                                :class="{ 'font-semibold text-purple-600': page.value?.url.startsWith('/bookings') }">My
                            Bookings</Link>
                            <Link href="/favorites" class="text-gray-700 hover:text-purple-600 transition"
                                :class="{ 'font-semibold text-purple-600': page.value?.url.startsWith('/favorites') }">
                            Favorites</Link>
                        </template>

                        <Link href="/categories" class="text-gray-700 hover:text-purple-600 transition"
                            :class="{ 'font-semibold text-purple-600': page.value?.url.startsWith('/categories') }">
                        Categories
                        </Link>
                        <Link href="#how-it-works" class="text-gray-700 hover:text-purple-600 transition">How It Works
                        </Link>

                        <!-- Search Bar -->
                        <div class="relative ml-4">
                            <input type="text" placeholder="Search services..."
                                class="pl-4 pr-10 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent w-48">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-gray-400 absolute right-3 top-2.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <!-- Auth Links -->
                        <template v-if="canLogin">
                            <template v-if="page.value?.props.auth.user">
                                <!-- Profile Dropdown -->
                                <div class="relative ml-4">
                                    <button class="flex items-center space-x-2 focus:outline-none">
                                        <div
                                            class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-semibold">
                                            {{ page.value?.props.auth.user.name.charAt(0) }}
                                        </div>
                                    </button>
                                    <div
                                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                                        <Link href="/profile" class="block px-4 py-2 text-gray-700 hover:bg-purple-50">
                                        My Profile</Link>
                                        <Link href="/logout" method="post" as="button"
                                            class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-purple-50">
                                        Logout</Link>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <Link :href="route('login')" class="text-gray-700 hover:text-purple-600 transition">
                                Login</Link>
                                <Link v-if="canRegister" :href="route('register')"
                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                Register</Link>
                            </template>
                        </template>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button class="text-gray-700 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

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
                            class="w-full md:w-auto px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                            Search
                        </button>
                    </div>
                </div>

                <div class="mt-8">
                    <Link v-if="canRegister" :href="route('register')"
                        class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                    Start Planning Now
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Featured Services Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <div class="flex justify-between items-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900">Featured Services</h2>
                    <Link href="/services" class="text-purple-600 hover:text-purple-800 font-medium">
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
                                    class="text-purple-600 hover:text-purple-800 font-medium">
                                View
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section id="how-it-works" class="py-16 bg-gray-50">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center text-gray-900 mb-16">How Eventory Works</h2>
                <div class="grid md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-purple-600 font-bold text-xl">1</span>
                        </div>
                        <h3 class="font-semibold mb-2">Search Vendors</h3>
                        <p class="text-gray-600 text-sm">
                            Find vendors by service type, location, and availability
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-purple-600 font-bold text-xl">2</span>
                        </div>
                        <h3 class="font-semibold mb-2">Compare Options</h3>
                        <p class="text-gray-600 text-sm">
                            View profiles, portfolios, ratings, and pricing
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-purple-600 font-bold text-xl">3</span>
                        </div>
                        <h3 class="font-semibold mb-2">Book Services</h3>
                        <p class="text-gray-600 text-sm">
                            Reserve vendors and get instant confirmation
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-purple-600 font-bold text-xl">4</span>
                        </div>
                        <h3 class="font-semibold mb-2">Enjoy Your Event</h3>
                        <p class="text-gray-600 text-sm">
                            Leave reviews to help other planners
                        </p>
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
                    <h3 class="text-xl font-semibold mb-3 text-purple-700">Catering Services</h3>
                    <p class="text-gray-600">
                        Find the perfect food and beverage providers for your event
                    </p>
                    </Link>
                    <Link href="/categories/photography"
                        class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition hover:border-purple-300">
                    <h3 class="text-xl font-semibold mb-3 text-purple-700">Photography</h3>
                    <p class="text-gray-600">
                        Professional photographers and photo booth services
                    </p>
                    </Link>
                    <Link href="/categories/sound-systems"
                        class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition hover:border-purple-300">
                    <h3 class="text-xl font-semibold mb-3 text-purple-700">Sound Systems</h3>
                    <p class="text-gray-600">
                        Audio equipment and technical support for events
                    </p>
                    </Link>
                    <Link href="/categories/entertainers"
                        class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition hover:border-purple-300">
                    <h3 class="text-xl font-semibold mb-3 text-purple-700">Entertainers</h3>
                    <p class="text-gray-600">
                        Bands, DJs, performers, and other entertainment
                    </p>
                    </Link>
                    <Link href="/categories/makeup"
                        class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition hover:border-purple-300">
                    <h3 class="text-xl font-semibold mb-3 text-purple-700">Makeup Artists</h3>
                    <p class="text-gray-600">
                        Professional beauty services for special occasions
                    </p>
                    </Link>
                    <Link href="/categories/attire"
                        class="p-6 border border-gray-200 rounded-lg hover:shadow-md transition hover:border-purple-300">
                    <h3 class="text-xl font-semibold mb-3 text-purple-700">Attire Rentals</h3>
                    <p class="text-gray-600">
                        Formal wear and costume rentals for your event
                    </p>
                    </Link>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 bg-purple-700 text-white">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold mb-6">Ready to Plan Your Event?</h2>
                <p class="text-xl mb-8 max-w-2xl mx-auto">
                    Join thousands of event planners who found their perfect vendors through Eventory
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <Link v-if="canRegister" :href="route('register')"
                        class="px-8 py-3 rounded-lg bg-white text-purple-700 hover:bg-gray-100 transition font-semibold text-lg">
                    Start Planning Now
                    </Link>
                    <Link href="#how-it-works"
                        class="px-8 py-3 rounded-lg border-2 border-white text-white hover:bg-purple-600 transition font-semibold text-lg">
                    How It Works
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
