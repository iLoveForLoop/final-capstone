<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import LoginModal from '@/Components/LoginModal.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import MyDropdown from '@/Components/MyDropdown.vue';



const page = usePage()

const loginModal = ref(null)
const isDropdownShowing = ref(false)
const isMobileMenuOpen = ref(false)

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



</script>

<template>

    <Head title="Welcome Client" />

    <div class="min-h-screen bg-gradient-to-br from-purple-50 to-blue-50">
        <LoginModal ref="loginModal" />


        <!-- Sticky Navigation -->
        <nav class="sticky top-0 z-30 bg-[#bad6d8] backdrop-blur-md border-b border-slate-300/20">
            <div class="max-w-7xl mx-auto px-6 py-4 relative">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div class="flex">
                        <Link href="/" class="flex items-center space-x-2 group">
                        <span
                            class="text-2xl font-bold text-slate-800 group-hover:text-purple-700 transition-colors duration-300 ">
                            Eventory
                        </span>
                        </Link>
                    </div>

                    <!-- Right Side Actions -->
                    <div class="hidden lg:flex items-center space-x-3">
                        <template v-if="page.props.auth.user">
                            <!-- Use MyDropdown Component -->
                            <MyDropdown />
                        </template>
                        <template v-else>
                            <!-- Login Button -->
                            <Link href="/login"
                                class="px-4 py-2 text-slate-700 hover:text-slate-900 transition-all duration-300 font-medium">
                            Log In
                            </Link>
                            <!-- Get Started Button -->
                            <Link href="/register"
                                class="px-6 py-2 bg-gradient-to-r from-[#239BA7] to-[#1b848e] text-white rounded-full hover:from-[#2aa8b4] hover:to-[#239BA7] transition-all duration-300 font-semibold shadow-lg hover:shadow-purple-500/25">
                            Get Started
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="lg:hidden">
                        <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                            class="p-2 text-slate-700 hover:text-slate-900 hover:bg-white/20 rounded-lg transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- ✅ Center Navigation (Always centered) -->
                <div
                    class="hidden lg:flex items-center space-x-1 absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2">
                    <Link href="/"
                        class="px-4 py-2 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                        :class="{ 'text-slate-900 bg-white/20': page.url === '/client' }">
                    Home
                    </Link>

                    <Link href="/services"
                        class="px-4 py-2 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                        :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/services') }">
                    Services
                    </Link>

                    <template v-if="page.props.auth.user">
                        <Link href="/bookings"
                            class="px-4 py-2 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                            :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/bookings') }">
                        Bookings
                        </Link>
                        <Link href="/favorites"
                            class="px-4 py-2 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                            :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/favorites') }">
                        Favorites
                        </Link>
                    </template>

                    <Link href="/categories"
                        class="px-4 py-2 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                        :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/categories') }">
                    Categories
                    </Link>
                </div>

                <!-- Mobile Menu -->
                <div v-if="isMobileMenuOpen" class="lg:hidden mt-4 pb-4 border-t border-slate-300/20 pt-4">
                    <div class="flex flex-col space-y-2">
                        <Link href="/" @click="isMobileMenuOpen = false"
                            class="px-4 py-3 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                            :class="{ 'text-slate-900 bg-white/20': page.url === '/client' }">
                        Home
                        </Link>

                        <Link href="/services" @click="isMobileMenuOpen = false"
                            class="px-4 py-3 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                            :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/services') }">
                        Services
                        </Link>

                        <template v-if="page.props.auth.user">
                            <Link href="/bookings" @click="isMobileMenuOpen = false"
                                class="px-4 py-3 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                                :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/bookings') }">
                            Bookings
                            </Link>
                            <Link href="/favorites" @click="isMobileMenuOpen = false"
                                class="px-4 py-3 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                                :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/favorites') }">
                            Favorites
                            </Link>
                        </template>

                        <Link href="/categories" @click="isMobileMenuOpen = false"
                            class="px-4 py-3 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                            :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/categories') }">
                        Categories
                        </Link>

                        <!-- Mobile Auth Section -->
                        <div class="pt-4 border-t border-slate-300/20 mt-4">
                            <template v-if="page.props.auth.user">
                                <div
                                    class="flex items-center space-x-3 px-4 py-3 text-slate-700 mb-2 bg-white/10 rounded-lg border border-slate-300/20">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-600 flex items-center justify-center text-white font-medium text-sm">
                                        {{page.props.auth.user.name.split(' ').map(word =>
                                            word.charAt(0)).join('').substring(0, 2).toUpperCase()}}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ page.props.auth.user.name }}</p>
                                        <p class="text-xs text-slate-600">{{ page.props.auth.user.email }}</p>
                                    </div>
                                </div>

                                <Link :href="route('profile.edit')" @click="isMobileMenuOpen = false"
                                    class="flex items-center px-4 py-3 text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium rounded-lg">
                                <svg class="w-4 h-4 mr-3 text-slate-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profile
                                </Link>

                                <Link href="/settings" @click="isMobileMenuOpen = false"
                                    class="flex items-center px-4 py-3 text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium rounded-lg"
                                    v-if="route().has && route().has('settings')">
                                <svg class="w-4 h-4 mr-3 text-slate-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Settings
                                </Link>

                                <hr class="border-slate-300/20 my-2">

                                <Link :href="route('logout')" method="post" as="button"
                                    @click="isMobileMenuOpen = false"
                                    class="flex items-center w-full px-4 py-3 text-slate-700 hover:text-red-600 hover:bg-red-100/30 transition-all duration-300 font-medium rounded-lg">
                                <svg class="w-4 h-4 mr-3 text-slate-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Sign out
                                </Link>
                            </template>
                            <template v-else>
                                <Link href="/login" @click="isMobileMenuOpen = false"
                                    class="block px-4 py-3 text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium rounded-lg">
                                Log In
                                </Link>
                                <Link href="/register" @click="isMobileMenuOpen = false"
                                    class="block mx-4 mt-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-full hover:from-purple-500 hover:to-purple-600 transition-all duration-300 font-semibold text-center shadow-lg">
                                Get Started
                                </Link>
                            </template>
                        </div>
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
                            class="w-full md:w-auto px-6 py-3 bg-[#239BA7] text-white rounded-lg hover:bg-purple-700 transition font-medium">
                            Search
                        </button>


                    </div>
                </div>

                <div class="mt-8">
                    <button @click="loginModal.show()"
                        class="inline-flex items-center px-6 py-3 bg-[#239BA7] text-white rounded-lg hover:bg-purple-700 transition font-medium">
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
                    <Link href="/services" class="text-[#239BA7] hover:text-[#239BA7] font-medium">
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
                                    class="text-[#239BA7] hover:text-[#239BA7] font-medium">
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
                            <span class="text-[#239BA7] font-bold text-xl">1</span>
                        </div>
                        <h3 class="font-semibold mb-2">Search Vendors</h3>
                        <p class="text-gray-600 text-sm">
                            Find vendors by service type, location, and availability
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-[#239BA7] font-bold text-xl">2</span>
                        </div>
                        <h3 class="font-semibold mb-2">Compare Options</h3>
                        <p class="text-gray-600 text-sm">
                            View profiles, portfolios, ratings, and pricing
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-[#239BA7] font-bold text-xl">3</span>
                        </div>
                        <h3 class="font-semibold mb-2">Book Services</h3>
                        <p class="text-gray-600 text-sm">
                            Reserve vendors and get instant confirmation
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-[#239BA7] font-bold text-xl">4</span>
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
                        class="px-8 py-3 rounded-lg border-2 border-white text-white hover:bg-[#239BA7] transition font-semibold text-lg">
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
