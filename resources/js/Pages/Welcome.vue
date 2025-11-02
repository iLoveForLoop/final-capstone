    <template>

        <Head title="Home" />

        <ClientLayout>
            <div class="min-h-screen bg-gradient-to-br from-gray-300 via-white to-gray-500 relative overflow-hidden">
                <!-- Enhanced animated background with floating event images -->
                <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
                    <!-- Floating collage of event service pictures -->
                    <div class="absolute inset-0">
                        <div class="collage-grid">
                            <div class="collage-item" v-for="(img, index) in backgroundImages" :key="index" :style="{
                                backgroundImage: `url(${img.url})`,
                                animationDelay: `${img.delay}s`,
                                left: `${img.position.x}%`,
                                top: `${img.position.y}%`,
                                filter: `brightness(${img.brightness}) saturate(${img.saturation})`
                            }">
                                <div class="image-overlay"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Subtle gradient overlays for better contrast -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/40 via-transparent to-purple-50/30">
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-white/60 via-transparent to-transparent"></div>

                    <!-- Keep the original gradient blobs but with adjusted colors -->
                    <div
                        class="absolute -top-40 -right-32 w-80 h-80 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-2000">
                    </div>
                    <div
                        class="absolute top-60 -left-20 w-80 h-80 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob animation-delay-4000">
                    </div>
                    <div
                        class="absolute -bottom-20 left-40 w-80 h-80 bg-teal-200 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-blob">
                    </div>
                </div>

                <LoginModal ref="loginModal" />

                <!-- Hero Section -->
                <section class="relative z-10">
                    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
                        <div class="text-center max-w-3xl mx-auto reveal-animation">
                            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                                Find Perfect Vendors for Your Perfect <span class="text-blue-600">Event</span>
                            </h1>
                            <p class="text-lg md:text-xl text-gray-600 mb-12 max-w-2xl mx-auto leading-relaxed">
                                Book trusted caterers, photographers, and more with our streamlined platform
                            </p>

                            <!-- Search Bar -->
                            <div
                                class="bg-white/90 backdrop-blur-md border border-gray-200/60 rounded-xl p-4 max-w-2xl mx-auto mb-10 shadow-lg reveal-animation animation-delay-100">
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <!-- Search Input -->
                                    <div class="flex-1 relative">
                                        <Search class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"
                                            :size="20" />
                                        <input v-model="search" type="text"
                                            placeholder="What service are you looking for?"
                                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500 transition-all duration-300 shadow-sm">
                                    </div>

                                    <!-- Filter Button -->
                                    <button @click="showFiltersDialog = true"
                                        class="relative flex items-center gap-2 px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-300 shadow-sm hover:shadow-md reveal-animation animation-delay-200">
                                        <Sliders :size="18" />
                                        <span class="hidden sm:inline">Filters</span>
                                        <!-- Active Filter Count Badge -->
                                        <div v-if="activeFilterCount > 0"
                                            class="absolute -top-2 -right-2 w-6 h-6 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center font-medium shadow-md">
                                            {{ activeFilterCount }}
                                        </div>
                                    </button>

                                    <!-- Search Button -->
                                    <button @click="handleSearch"
                                        class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-300 font-medium shadow-md hover:shadow-lg reveal-animation animation-delay-300">
                                        Search
                                    </button>
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <button @click="loginModal.show()"
                                class="inline-flex items-center px-6 py-3 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition-all duration-300 font-medium shadow-md hover:shadow-lg reveal-animation animation-delay-400">
                                <Calendar class="h-5 w-5 mr-2" />
                                Start Planning Now
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Featured Services -->
                <section class="py-16 bg-white relative z-10">
                    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center mb-10 reveal-animation">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900">Featured Services</h2>
                                <p class="text-gray-600 mt-2">Top-rated vendors in your area</p>
                            </div>
                            <Link href="/services"
                                class="text-blue-600 hover:text-blue-700 font-medium flex items-center group">
                            View All
                            <ArrowRight
                                class="ml-1 h-4 w-4 group-hover:translate-x-1 transition-transform duration-300" />
                            </Link>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <div v-for="(service, index) in services.data" :key="service.id"
                                class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 reveal-animation"
                                :class="`animation-delay-${(index % 3) * 100}`">
                                <NewServiceCard :service="service" />
                            </div>
                        </div>
                    </div>
                </section>

                <!-- How It Works -->
                <section id="how-it-works" class="py-16 bg-gray-50 relative z-10">
                    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 class="text-3xl font-bold text-center text-gray-900 mb-16 reveal-animation">How Eventory
                            Works
                        </h2>
                        <div class="grid md:grid-cols-4 gap-8">
                            <div class="text-center reveal-animation">
                                <div
                                    class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-blue-600 font-bold text-xl">1</span>
                                </div>
                                <h3 class="font-semibold mb-2">Search Vendors</h3>
                                <p class="text-gray-600 text-sm">
                                    Find vendors by service type, location, and availability
                                </p>
                            </div>
                            <div class="text-center reveal-animation animation-delay-100">
                                <div
                                    class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-blue-600 font-bold text-xl">2</span>
                                </div>
                                <h3 class="font-semibold mb-2">Compare Options</h3>
                                <p class="text-gray-600 text-sm">
                                    View profiles, portfolios, ratings, and pricing
                                </p>
                            </div>
                            <div class="text-center reveal-animation animation-delay-200">
                                <div
                                    class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-blue-600 font-bold text-xl">3</span>
                                </div>
                                <h3 class="font-semibold mb-2">Book Services</h3>
                                <p class="text-gray-600 text-sm">
                                    Reserve vendors and get instant confirmation
                                </p>
                            </div>
                            <div class="text-center reveal-animation animation-delay-300">
                                <div
                                    class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-blue-600 font-bold text-xl">4</span>
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
                <section class="py-16 relative z-10">
                    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center mb-14 reveal-animation">
                            <h2 class="text-3xl font-bold text-gray-900 mb-3">Vendor Categories</h2>
                            <p class="text-gray-600 text-lg">Find vendors by service type</p>
                        </div>

                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <Link href="/categories/catering"
                                class="group p-8 bg-white border border-gray-100 rounded-2xl hover:border-blue-100 hover:shadow-lg transition-all duration-300 reveal-animation">
                            <div
                                class="w-14 h-14 bg-blue-50 rounded-xl mb-5 flex items-center justify-center group-hover:bg-blue-100 transition-colors duration-300">
                                <ChefHat class="h-7 w-7 text-blue-600" />
                            </div>
                            <h3
                                class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors duration-300">
                                Catering Services</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Find the perfect food and beverage
                                providers
                                for
                                your event</p>
                            </Link>

                            <Link href="/categories/photography"
                                class="group p-8 bg-white border border-gray-100 rounded-2xl hover:border-blue-100 hover:shadow-lg transition-all duration-300 reveal-animation animation-delay-100">
                            <div
                                class="w-14 h-14 bg-blue-50 rounded-xl mb-5 flex items-center justify-center group-hover:bg-blue-100 transition-colors duration-300">
                                <Camera class="h-7 w-7 text-blue-600" />
                            </div>
                            <h3
                                class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors duration-300">
                                Photography</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Professional photographers and photo booth
                                services
                            </p>
                            </Link>

                            <Link href="/categories/sound-systems"
                                class="group p-8 bg-white border border-gray-100 rounded-2xl hover:border-blue-100 hover:shadow-lg transition-all duration-300 reveal-animation animation-delay-200">
                            <div
                                class="w-14 h-14 bg-blue-50 rounded-xl mb-5 flex items-center justify-center group-hover:bg-blue-100 transition-colors duration-300">
                                <Music class="h-7 w-7 text-blue-600" />
                            </div>
                            <h3
                                class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors duration-300">
                                Sound Systems</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Audio equipment and technical support for
                                events
                            </p>
                            </Link>

                            <Link href="/categories/entertainers"
                                class="group p-8 bg-white border border-gray-100 rounded-2xl hover:border-blue-100 hover:shadow-lg transition-all duration-300 reveal-animation animation-delay-300">
                            <div
                                class="w-14 h-14 bg-blue-50 rounded-xl mb-5 flex items-center justify-center group-hover:bg-blue-100 transition-colors duration-300">
                                <Mic2 class="h-7 w-7 text-blue-600" />
                            </div>
                            <h3
                                class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors duration-300">
                                Entertainers</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Bands, DJs, performers, and other
                                entertainment
                            </p>
                            </Link>

                            <Link href="/categories/makeup"
                                class="group p-8 bg-white border border-gray-100 rounded-2xl hover:border-blue-100 hover:shadow-lg transition-all duration-300 reveal-animation animation-delay-400">
                            <div
                                class="w-14 h-14 bg-blue-50 rounded-xl mb-5 flex items-center justify-center group-hover:bg-blue-100 transition-colors duration-300">
                                <Sparkles class="h-7 w-7 text-blue-600" />
                            </div>
                            <h3
                                class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors duration-300">
                                Makeup Artists</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Professional beauty services for special
                                occasions
                            </p>
                            </Link>

                            <Link href="/categories/attire"
                                class="group p-8 bg-white border border-gray-100 rounded-2xl hover:border-blue-100 hover:shadow-lg transition-all duration-300 reveal-animation animation-delay-500">
                            <div
                                class="w-14 h-14 bg-blue-50 rounded-xl mb-5 flex items-center justify-center group-hover:bg-blue-100 transition-colors duration-300">
                                <Shirt class="h-7 w-7 text-blue-600" />
                            </div>
                            <h3
                                class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors duration-300">
                                Attire Rentals</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">Formal wear and costume rentals for your
                                event
                            </p>
                            </Link>
                        </div>
                    </div>
                </section>

                <!-- CTA Section -->
                <section class="py-16 bg-blue-600 text-white relative z-10">
                    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                        <h2 class="text-3xl font-bold mb-6 reveal-animation">Ready to Plan Your Event?</h2>
                        <p class="text-xl mb-8 max-w-2xl mx-auto reveal-animation animation-delay-100">
                            Join thousands of event planners who found their perfect vendors through Eventory
                        </p>
                        <div
                            class="flex flex-col sm:flex-row justify-center gap-4 reveal-animation animation-delay-200">
                            <Link v-if="canRegister" :href="route('register')"
                                class="px-8 py-3 rounded-xl bg-white text-blue-600 hover:bg-gray-100 transition-all duration-300 font-semibold text-lg shadow-md hover:shadow-lg">
                            Start Planning Now
                            </Link>
                            <Link href="#how-it-works"
                                class="px-8 py-3 rounded-xl border-2 border-white text-white hover:bg-blue-700 transition-all duration-300 font-semibold text-lg">
                            How It Works
                            </Link>
                        </div>
                    </div>
                </section>

                <!-- Footer -->
                <footer class="bg-gradient-to-b from-gray-900 to-gray-800 text-white py-16 relative z-10">
                    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid md:grid-cols-4 gap-10">
                            <div class="reveal-animation">
                                <div class="text-2xl font-bold mb-5">Eventory</div>
                                <p class="text-gray-400 text-sm leading-relaxed">
                                    The centralized platform for event planners and local vendors to connect,
                                    collaborate,
                                    and
                                    create memorable experiences.
                                </p>
                            </div>

                            <div class="reveal-animation animation-delay-100">
                                <h3 class="font-semibold mb-5 text-lg">Platform</h3>
                                <ul class="space-y-3 text-sm">
                                    <li>
                                        <Link href="/features"
                                            class="text-gray-400 hover:text-white transition-colors duration-300">
                                        Features
                                        </Link>
                                    </li>
                                    <li>
                                        <Link href="/pricing"
                                            class="text-gray-400 hover:text-white transition-colors duration-300">
                                        Pricing
                                        </Link>
                                    </li>
                                    <li>
                                        <Link href="/services"
                                            class="text-gray-400 hover:text-white transition-colors duration-300">
                                        Vendors
                                        </Link>
                                    </li>
                                </ul>
                            </div>

                            <div class="reveal-animation animation-delay-200">
                                <h3 class="font-semibold mb-5 text-lg">Company</h3>
                                <ul class="space-y-3 text-sm">
                                    <li>
                                        <Link href="/about"
                                            class="text-gray-400 hover:text-white transition-colors duration-300">About
                                        </Link>
                                    </li>
                                    <li>
                                        <Link href="/contact"
                                            class="text-gray-400 hover:text-white transition-colors duration-300">
                                        Contact
                                        </Link>
                                    </li>
                                    <li>
                                        <Link href="/careers"
                                            class="text-gray-400 hover:text-white transition-colors duration-300">
                                        Careers
                                        </Link>
                                    </li>
                                </ul>
                            </div>

                            <div class="reveal-animation animation-delay-300">
                                <h3 class="font-semibold mb-5 text-lg">Legal</h3>
                                <ul class="space-y-3 text-sm">
                                    <li>
                                        <Link href="/privacy"
                                            class="text-gray-400 hover:text-white transition-colors duration-300">
                                        Privacy
                                        </Link>
                                    </li>
                                    <li>
                                        <Link href="/terms"
                                            class="text-gray-400 hover:text-white transition-colors duration-300">Terms
                                        </Link>
                                    </li>
                                    <li>
                                        <Link href="/cookie-policy"
                                            class="text-gray-400 hover:text-white transition-colors duration-300">Cookie
                                        Policy
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400 reveal-animation">
                            &copy; 2024 Eventory. All rights reserved. | Capstone Project
                        </div>
                    </div>
                </footer>
            </div>

            <!-- Enhanced Filters Dialog -->
            <Dialog v-model:open="showFiltersDialog">
                <DialogContent class="sm:max-w-4xl w-full max-h-[85vh] overflow-hidden flex flex-col rounded-2xl">
                    <DialogHeader class="pb-4 border-b border-gray-200">
                        <DialogTitle class="text-2xl font-bold text-gray-900">Advanced Filters</DialogTitle>
                        <DialogDescription class="text-gray-600 mt-1">
                            Refine your search to find exactly what you need
                        </DialogDescription>
                    </DialogHeader>

                    <div class="flex-1 overflow-y-auto py-6">
                        <!-- Grid layout for filters -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Category Filter -->
                            <div>
                                <div class="flex items-center gap-3 mb-5">
                                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <Filter :size="18" class="text-blue-600" />
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Service Categories</h3>
                                </div>
                                <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
                                    <button v-for="category in categories" :key="category.id"
                                        @click="toggleCategory(category.id)" :class="[
                                            'w-full flex items-center justify-between px-4 py-3 rounded-xl border text-sm transition-all duration-200',
                                            selectedCategories.includes(category.id)
                                                ? 'bg-blue-50 border-blue-200 text-blue-700 shadow-sm'
                                                : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300'
                                        ]">
                                        <span class="font-medium">{{ category.name }}</span>
                                        <div :class="[
                                            'w-5 h-5 rounded border-2 flex items-center justify-center transition-colors',
                                            selectedCategories.includes(category.id)
                                                ? 'bg-blue-600 border-blue-600'
                                                : 'border-gray-300'
                                        ]">

                                            <Check v-if="selectedCategories.includes(category.id)" :size="14"
                                                class="text-white" />
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <div class="flex items-center gap-3 mb-5">
                                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                        <Database :size="18" class="text-green-600" />
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Budget Range</h3>
                                </div>
                                <div class="space-y-3">
                                    <button v-for="range in priceRanges" :key="range.id"
                                        @click="selectedPriceRange = selectedPriceRange === range.id ? '' : range.id"
                                        :class="[
                                            'w-full flex items-center justify-between px-4 py-3 rounded-xl border text-sm transition-all duration-200',
                                            selectedPriceRange === range.id
                                                ? 'bg-green-50 border-green-200 text-green-700 shadow-sm'
                                                : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300'
                                        ]">
                                        <span class="font-medium">{{ range.label }}</span>
                                        <div :class="[
                                            'w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors',
                                            selectedPriceRange === range.id
                                                ? 'bg-green-600 border-green-600'
                                                : 'border-gray-300'
                                        ]">
                                            <div v-if="selectedPriceRange === range.id"
                                                class="w-2 h-2 bg-white rounded-full">
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Rating Filter -->
                            <div>
                                <div class="flex items-center gap-3 mb-5">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                                        <Star :size="18" class="text-yellow-600" />
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Minimum Rating</h3>
                                </div>
                                <div class="space-y-3">
                                    <button v-for="rating in ratingFilters" :key="rating.value"
                                        @click="selectedRating = selectedRating === rating.value ? '' : rating.value"
                                        :class="[
                                            'w-full flex items-center justify-between px-4 py-3 rounded-xl border text-sm transition-all duration-200',
                                            selectedRating === rating.value
                                                ? 'bg-yellow-50 border-yellow-200 text-yellow-700 shadow-sm'
                                                : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300'
                                        ]">
                                        <div class="flex items-center gap-3">
                                            <div class="flex">
                                                <Star v-for="i in 5" :key="i" :size="16"
                                                    :class="i <= rating.value ? 'text-yellow-400 fill-current' : 'text-gray-300'" />
                                            </div>
                                            <span class="font-medium">{{ rating.value }}+ stars</span>
                                        </div>
                                        <div :class="[
                                            'w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors',
                                            selectedRating === rating.value
                                                ? 'bg-yellow-600 border-yellow-600'
                                                : 'border-gray-300'
                                        ]">
                                            <div v-if="selectedRating === rating.value"
                                                class="w-2 h-2 bg-white rounded-full">
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <button @click="clearFilters"
                            class="flex items-center gap-2 px-5 py-3 text-sm text-gray-700 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all duration-300 font-medium">
                            <X :size="16" />
                            Clear All Filters
                        </button>
                        <button @click="applyFilters"
                            class="flex items-center gap-2 px-6 py-3 text-sm bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-300 font-medium shadow-md hover:shadow-lg">
                            <Check :size="16" />
                            Apply Filters
                        </button>
                    </div>
                </DialogContent>
            </Dialog>
        </ClientLayout>
    </template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import LoginModal from '@/Components/LoginModal.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import MyDropdown from '@/Components/MyDropdown.vue';
import ClientNavbar from '@/Components/ClientNavbar.vue';
import NewServiceCard from '@/Components/Client/NewServiceCard.vue';

import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from '@/Components/ui/dialog';
import {
    Sliders,
    Database,
    Filter,
    Check,
    Star,
    X,
    Search,
    Calendar,
    Users,
    Heart,
    ArrowRight,
    ChefHat,
    Camera,
    Music,
    Mic2,
    Sparkles,
    Shirt,
    Car
} from 'lucide-vue-next';
import ClientLayout from '@/Layouts/ClientLayout.vue';

const page = usePage()
const loginModal = ref(null)
const isDropdownShowing = ref(false)
const isMobileMenuOpen = ref(false)

// Search and filter functionality
const search = ref("")
const selectedCategories = ref([]);
const selectedPriceRange = ref('');
const selectedRating = ref('');

// Dialog state
const showFiltersDialog = ref(false);

// Animation state
const animatedElements = ref([]);

// Background images data with improved styling
const backgroundImages = ref([
    { url: 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=300&h=200&fit=crop', delay: 0, position: { x: 5, y: 10 }, brightness: '1.1', saturation: '1.1' },
    // { url: 'https://images.unsplash.com/photo-1532634922-8fe0b757fb13?w=300&h=200&fit=crop', delay: 2, position: { x: 15, y: 25 }, brightness: '1.05', saturation: '1.05' },
    { url: 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=300&h=200&fit=crop', delay: 4, position: { x: 25, y: 5 }, brightness: '1.15', saturation: '1.1' },
    // { url: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=200&fit=crop', delay: 1, position: { x: 35, y: 35 }, brightness: '1.08', saturation: '1.05' },
    { url: 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=300&h=200&fit=crop', delay: 3, position: { x: 11, y: 18 }, brightness: '1.12', saturation: '1.08' },
    { url: 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=300&h=200&fit=crop', delay: 3, position: { x: 65, y: 18 }, brightness: '1.12', saturation: '1.08' },
    // { url: 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=300&h=200&fit=crop', delay: 5, position: { x: 55, y: 30 }, brightness: '1.06', saturation: '1.1' },
    { url: 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=300&h=200&fit=crop', delay: 2, position: { x: 65, y: 8 }, brightness: '1.09', saturation: '1.06' },
    // { url: 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=300&h=200&fit=crop', delay: 4, position: { x: 75, y: 22 }, brightness: '1.13', saturation: '1.07' },
    { url: 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=300&h=200&fit=crop', delay: 1, position: { x: 85, y: 12 }, brightness: '1.07', saturation: '1.09' }
]);

// Initialize animation observer
onMounted(() => {
    animatedElements.value = document.querySelectorAll('.reveal-animation');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    animatedElements.value.forEach(element => {
        observer.observe(element);
    });
});

// Price ranges
const priceRanges = [
    { id: 'under_5000', label: 'Under ₱5,000' },
    { id: '5000_15000', label: '₱5,000 - ₱15,000' },
    { id: '15000_30000', label: '₱15,000 - ₱30,000' },
    { id: '30000_50000', label: '₱30,000 - ₱50,000' },
    { id: 'over_50000', label: 'Over ₱50,000' }
];

// Rating filters
const ratingFilters = [
    { value: 4, label: '4+ stars' },
    { value: 3, label: '3+ stars' },
    { value: 2, label: '2+ stars' },
    { value: 1, label: '1+ stars' }
];

// Toggle category selection
const toggleCategory = (categoryId) => {
    const index = selectedCategories.value.indexOf(categoryId);
    if (index > -1) {
        selectedCategories.value.splice(index, 1);
    } else {
        selectedCategories.value.push(categoryId);
    }
};

const handleSearch = () => {
    showFiltersDialog.value = false;
    // console.log('Test');

    router.get('/services', {
        search: search.value || undefined,
        categories: selectedCategories.value.length > 0 ? selectedCategories.value : undefined,
        price_range: selectedPriceRange.value || undefined,
        rating: selectedRating.value || undefined,
    }, {
        preserveState: true,
        preserveScroll: true
    });
}

const applyFilters = () => {
    showFiltersDialog.value = false
}

const clearFilters = () => {
    selectedCategories.value = []
    selectedPriceRange.value = ''
    selectedRating.value = ''
    showFiltersDialog.value = false
}

// Get active filter count
const activeFilterCount = computed(() => {
    let count = 0;

    if (selectedCategories.value && selectedCategories.value.length > 0) count++;
    if (selectedPriceRange.value) count++;
    if (selectedRating.value) count++;
    return count;
});

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
    categories: {
        type: Array
    }
});
</script>

<style>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }

    33% {
        transform: translate(30px, -50px) scale(1.1);
    }

    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }

    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0) rotate(0deg) scale(1);
    }

    33% {
        transform: translateY(-30px) rotate(2deg) scale(1.02);
    }

    66% {
        transform: translateY(15px) rotate(-1deg) scale(0.98);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.reveal-animation {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.reveal-animation.animated {
    opacity: 1;
    transform: translateY(0);
}

.animation-delay-100 {
    transition-delay: 100ms;
}

.animation-delay-200 {
    transition-delay: 200ms;
}

.animation-delay-300 {
    transition-delay: 300ms;
}

.animation-delay-400 {
    transition-delay: 400ms;
}

.animation-delay-500 {
    transition-delay: 500ms;
}

/* Enhanced Collage background styles */
.collage-grid {
    position: absolute;
    width: 120%;
    height: 120%;
    top: -10%;
    left: -10%;
}

.collage-item {
    position: absolute;
    width: 380px;
    height: 300px;
    border-radius: 16px;
    background-size: cover;
    background-position: center;
    box-shadow:
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 2px 8px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    animation: float 20s ease-in-out infinite;
    opacity: 0.7;
    transform-origin: center;
    overflow: hidden;
    transition: all 0.5s ease;
}

.collage-item:hover {
    opacity: 0.9;
    transform: scale(1.05);
}

.collage-item:nth-child(odd) {
    animation-duration: 25s;
}

.collage-item:nth-child(even) {
    animation-duration: 30s;
}

/* Diagonal movement effect with subtle rotation */
.collage-item {
    transform: rotate(-3deg);
}

.collage-item:nth-child(even) {
    transform: rotate(2deg);
}

.collage-item:nth-child(3n) {
    transform: rotate(1deg);
}

.collage-item:nth-child(4n) {
    transform: rotate(-2deg);
}

/* Image overlay for better contrast */
.image-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.05) 100%);
    border-radius: 16px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .collage-item {
        width: 200px;
        height: 140px;
        opacity: 0.5;
    }
}
</style>
