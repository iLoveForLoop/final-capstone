<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import QuickBookingStepperModal from '@/Components/QuickBookingStepperModal.vue';
import ClientNavbar from '@/Components/ClientNavbar.vue'
import NewServiceCard from '@/Components/Client/NewServiceCard.vue';

import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from '@/Components/ui/dialog';
import { Sliders, Database, Filter, Check, Star, X, Sparkles, Calendar, Users, Heart } from 'lucide-vue-next';


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
const selectedCategories = ref([]);
const selectedPriceRange = ref('');
const selectedRating = ref('');

// Dialog state
const showFiltersDialog = ref(false);

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
    router.get('/client/services', {
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
    selectedRating.value = []
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
</script>

<template>

    <Head title="Home" />

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 opacity-20">
            <!-- Floating Orbs -->
            <div
                class="absolute top-20 left-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl animate-pulse">
            </div>
            <div
                class="absolute top-40 right-10 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-2000">
            </div>
            <div
                class="absolute -bottom-32 left-1/3 w-80 h-80 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-4000">
            </div>
        </div>

        <!-- Animated Particles -->
        <div class="absolute inset-0">
            <div class="absolute animate-float top-1/4 left-1/4 w-5 h-5 bg-white rounded-full opacity-60"></div>
            <div class="absolute animate-float-delayed top-1/3 right-1/3 w-1 h-1 bg-purple-300 rounded-full opacity-80">
            </div>
            <div class="absolute animate-float-slow top-2/3 left-1/5 w-3 h-3 bg-pink-400 rounded-full opacity-40"></div>
            <div class="absolute animate-float top-1/2 right-1/4 w-1.5 h-1.5 bg-blue-300 rounded-full opacity-70"></div>
            <div
                class="absolute animate-float-delayed bottom-1/4 left-2/3 w-2 h-2 bg-purple-400 rounded-full opacity-50">
            </div>
        </div>

        <QuickBookingStepperModal ref="eventModal" :categories="categories" />

        <!-- Navigation -->
        <ClientNavbar />

        <!-- Hero Section -->
        <section class="relative z-10 pt-20 pb-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-5xl mx-auto">
                    <!-- Hero Badge -->
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-white text-sm font-medium mb-8 animate-fade-in-up">
                        <Sparkles class="w-4 h-4 text-purple-300" />
                        <span>Your Dream Event Starts Here</span>
                    </div>

                    <h1
                        class="text-6xl md:text-7xl lg:text-8xl font-bold bg-gradient-to-r from-white via-purple-200 to-pink-200 bg-clip-text text-transparent mb-8 leading-tight animate-fade-in-up animation-delay-200">
                        Find Perfect
                        <br />
                        <span class="bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Event
                            Vendors</span>
                    </h1>

                    <p
                        class="text-xl md:text-2xl text-gray-300 mb-16 max-w-3xl mx-auto leading-relaxed animate-fade-in-up animation-delay-400">
                        Connect with verified professionals and transform your vision into unforgettable moments
                    </p>

                    <!-- Enhanced Search Bar -->
                    <div
                        class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-6 max-w-4xl mx-auto mb-12 shadow-2xl animate-fade-in-up animation-delay-600">
                        <div class="flex flex-col lg:flex-row gap-4">
                            <!-- Search Input -->
                            <div class="flex-1 relative">
                                <input v-model="search" type="text" placeholder="Search vendors or services..."
                                    class="w-full px-6 py-4 bg-white/90 backdrop-blur-sm border border-gray-200/50 rounded-2xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-800 placeholder-gray-500 text-lg shadow-lg transition-all duration-300 hover:shadow-xl">
                            </div>

                            <!-- Filter Button -->
                            <button @click="showFiltersDialog = true"
                                class="relative flex items-center justify-center gap-3 px-6 py-4 text-white bg-white/20 backdrop-blur-sm border border-white/30 rounded-2xl hover:bg-white/30 focus:ring-2 focus:ring-purple-500/50 transition-all duration-300 font-medium shadow-lg hover:shadow-xl transform hover:scale-105">
                                <Sliders class="w-5 h-5" />
                                <span class="hidden sm:inline">Filters</span>
                                <!-- Active Filter Count Badge -->
                                <div v-if="activeFilterCount > 0"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs rounded-full flex items-center justify-center font-bold shadow-lg animate-pulse">
                                    {{ activeFilterCount }}
                                </div>
                            </button>

                            <!-- Search Button -->
                            <button @click="handleSearch"
                                class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 font-semibold text-lg shadow-lg hover:shadow-xl transform hover:scale-105">
                                Search
                            </button>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <button @click="eventModal.open()"
                        class="group inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-purple-600 via-purple-700 to-pink-600 text-white rounded-2xl hover:from-purple-700 hover:via-purple-800 hover:to-pink-700 transition-all duration-300 font-semibold text-lg shadow-2xl hover:shadow-purple-500/25 transform hover:scale-105 animate-fade-in-up animation-delay-800">
                        <Calendar class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" />
                        <span>Start Planning Your Event</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Glass Morphism Stats Section -->
        <section class="relative z-10 -mt-16 mb-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="text-center group">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <Calendar class="w-8 h-8 text-white" />
                            </div>
                            <div class="text-4xl font-bold text-white mb-2">3</div>
                            <div class="text-gray-300 font-medium">Active Bookings</div>
                        </div>
                        <div class="text-center group">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <Check class="w-8 h-8 text-white" />
                            </div>
                            <div class="text-4xl font-bold text-white mb-2">12</div>
                            <div class="text-gray-300 font-medium">Completed Events</div>
                        </div>
                        <div class="text-center group">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-500 rounded-2xl mb-4 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <Heart class="w-8 h-8 text-white" />
                            </div>
                            <div class="text-4xl font-bold text-white mb-2">8</div>
                            <div class="text-gray-300 font-medium">Saved Vendors</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Services -->
        <section class="relative z-10 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-end mb-12">
                    <div>
                        <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Featured Services</h2>
                        <p class="text-xl text-gray-300">Top-rated vendors creating magic</p>
                    </div>
                    <Link href="/services"
                        class="group inline-flex items-center gap-2 text-purple-300 hover:text-white font-semibold text-lg transition-colors duration-300">
                    <span>View All</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="service in services.data" :key="service.id"
                        class="group bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl overflow-hidden hover:bg-white/20 transition-all duration-300 shadow-xl hover:shadow-2xl hover:shadow-purple-500/20 transform hover:scale-105">
                        <NewServiceCard :service="service" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Enhanced Categories Section -->
        <section class="relative z-10 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Browse Categories</h2>
                    <p class="text-xl text-gray-300">Find your perfect vendor by service type</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <Link href="/categories/catering"
                        class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl p-8 hover:bg-white/20 transition-all duration-500 shadow-xl hover:shadow-2xl hover:shadow-purple-500/20 transform hover:scale-105 overflow-hidden">
                    <!-- Gradient Overlay -->
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-pink-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl mb-6 shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H19M7 13v4a2 2 0 002 2h4a2 2 0 002-2v-4m-6 0a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <h3
                            class="text-2xl font-bold text-white mb-3 group-hover:text-purple-200 transition-colors duration-300">
                            Catering Services</h3>
                        <p class="text-gray-300 group-hover:text-gray-200 transition-colors duration-300">Professional
                            food and beverage experiences</p>
                    </div>
                    </Link>

                    <Link href="/categories/photography"
                        class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl p-8 hover:bg-white/20 transition-all duration-500 shadow-xl hover:shadow-2xl hover:shadow-blue-500/20 transform hover:scale-105 overflow-hidden">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl mb-6 shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3
                            class="text-2xl font-bold text-white mb-3 group-hover:text-blue-200 transition-colors duration-300">
                            Photography</h3>
                        <p class="text-gray-300 group-hover:text-gray-200 transition-colors duration-300">Capture every
                            precious moment</p>
                    </div>
                    </Link>

                    <Link href="/categories/sound-systems"
                        class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl p-8 hover:bg-white/20 transition-all duration-500 shadow-xl hover:shadow-2xl hover:shadow-green-500/20 transform hover:scale-105 overflow-hidden">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-blue-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-blue-500 rounded-2xl mb-6 shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M12 8l-4 4H5a1 1 0 00-1 1v2a1 1 0 001 1h3l4 4V8z">
                                </path>
                            </svg>
                        </div>
                        <h3
                            class="text-2xl font-bold text-white mb-3 group-hover:text-green-200 transition-colors duration-300">
                            Sound Systems</h3>
                        <p class="text-gray-300 group-hover:text-gray-200 transition-colors duration-300">Crystal clear
                            audio experiences</p>
                    </div>
                    </Link>

                    <Link href="/categories/entertainers"
                        class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl p-8 hover:bg-white/20 transition-all duration-500 shadow-xl hover:shadow-2xl hover:shadow-orange-500/20 transform hover:scale-105 overflow-hidden">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-orange-500/20 to-red-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl mb-6 shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3">
                                </path>
                            </svg>
                        </div>
                        <h3
                            class="text-2xl font-bold text-white mb-3 group-hover:text-orange-200 transition-colors duration-300">
                            Entertainment</h3>
                        <p class="text-gray-300 group-hover:text-gray-200 transition-colors duration-300">Unforgettable
                            performances</p>
                    </div>
                    </Link>

                    <Link href="/categories/makeup"
                        class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl p-8 hover:bg-white/20 transition-all duration-500 shadow-xl hover:shadow-2xl hover:shadow-pink-500/20 transform hover:scale-105 overflow-hidden">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-pink-500/20 to-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-500 rounded-2xl mb-6 shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <Users class="h-8 w-8 text-white" />
                        </div>
                        <h3
                            class="text-2xl font-bold text-white mb-3 group-hover:text-pink-200 transition-colors duration-300">
                            Makeup Artists</h3>
                        <p class="text-gray-300 group-hover:text-gray-200 transition-colors duration-300">Professional
                            beauty transformations</p>
                    </div>
                    </Link>

                    <Link href="/categories/attire"
                        class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl p-8 hover:bg-white/20 transition-all duration-500 shadow-xl hover:shadow-2xl hover:shadow-indigo-500/20 transform hover:scale-105 overflow-hidden">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>

                    <div class="relative z-10">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-2xl mb-6 shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3
                            class="text-2xl font-bold text-white mb-3 group-hover:text-indigo-200 transition-colors duration-300">
                            Attire Rentals</h3>
                        <p class="text-gray-300 group-hover:text-gray-200 transition-colors duration-300">Perfect
                            outfits for any occasion</p>
                    </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Enhanced Footer -->
        <footer class="relative z-10 mt-20 bg-black/40 backdrop-blur-lg border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid md:grid-cols-4 gap-12">
                    <div class="col-span-2 md:col-span-1">
                        <div
                            class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-6">
                            Eventory</div>
                        <p class="text-gray-400 leading-relaxed mb-6">
                            Connecting event planners with trusted local vendors to create unforgettable experiences.
                        </p>
                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors cursor-pointer">
                                <Heart class="w-5 h-5 text-purple-400" />
                            </div>
                            <div
                                class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors cursor-pointer">
                                <Users class="w-5 h-5 text-purple-400" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-white font-semibold text-lg mb-6">Platform</h3>
                        <ul class="space-y-4">
                            <li>
                                <Link href="/dashboard" class="text-gray-400 hover:text-purple-300 transition-colors">
                                Dashboard</Link>
                            </li>
                            <li>
                                <Link href="/bookings" class="text-gray-400 hover:text-purple-300 transition-colors">My
                                Bookings</Link>
                            </li>
                            <li>
                                <Link href="/services" class="text-gray-400 hover:text-purple-300 transition-colors">
                                Browse Services</Link>
                            </li>
                            <li>
                                <Link href="/vendors" class="text-gray-400 hover:text-purple-300 transition-colors">Find
                                Vendors</Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-white font-semibold text-lg mb-6">Categories</h3>
                        <ul class="space-y-4">
                            <li>
                                <Link href="/categories/catering"
                                    class="text-gray-400 hover:text-purple-300 transition-colors">Catering</Link>
                            </li>
                            <li>
                                <Link href="/categories/photography"
                                    class="text-gray-400 hover:text-purple-300 transition-colors">Photography</Link>
                            </li>
                            <li>
                                <Link href="/categories/sound-systems"
                                    class="text-gray-400 hover:text-purple-300 transition-colors">Audio & Sound</Link>
                            </li>
                            <li>
                                <Link href="/categories/entertainers"
                                    class="text-gray-400 hover:text-purple-300 transition-colors">Entertainment</Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-white font-semibold text-lg mb-6">Support</h3>
                        <ul class="space-y-4">
                            <li>
                                <Link href="/help" class="text-gray-400 hover:text-purple-300 transition-colors">Help
                                Center</Link>
                            </li>
                            <li>
                                <Link href="/contact" class="text-gray-400 hover:text-purple-300 transition-colors">
                                Contact</Link>
                            </li>
                            <li>
                                <Link href="/about" class="text-gray-400 hover:text-purple-300 transition-colors">About
                                </Link>
                            </li>
                            <li>
                                <Link href="/privacy" class="text-gray-400 hover:text-purple-300 transition-colors">
                                Privacy</Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-white/10 mt-12 pt-8">
                    <p class="text-gray-400 text-center">
                        © 2024 Eventory. All rights reserved. | Capstone Project
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Enhanced Filters Dialog -->
    <Dialog v-model:open="showFiltersDialog">
        <DialogContent
            class="sm:max-w-4xl w-full max-h-[85vh] overflow-hidden flex flex-col bg-white/95 backdrop-blur-xl border border-white/20 rounded-3xl">
            <DialogHeader class="pb-6 border-b border-gray-200">
                <DialogTitle
                    class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Advanced Filters</DialogTitle>
                <DialogDescription class="text-gray-600 text-lg">
                    Refine your search to find exactly what you need
                </DialogDescription>
            </DialogHeader>

            <div class="flex-1 overflow-y-auto py-8">
                <!-- Grid layout for filters -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Category Filter -->
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <Filter :size="20" class="text-white" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Service Categories</h3>
                        </div>
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            <button v-for="category in categories" :key="category.id"
                                @click="toggleCategory(category.id)" :class="[
                                    'w-full flex items-center justify-between px-6 py-4 rounded-2xl border text-sm transition-all duration-300 transform hover:scale-105',
                                    selectedCategories.includes(category.id)
                                        ? 'bg-gradient-to-r from-purple-50 to-pink-50 border-purple-300 text-purple-700 shadow-lg'
                                        : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-sm hover:shadow-md'
                                ]">
                                <span class="font-semibold">{{ category.name }}</span>
                                <div :class="[
                                    'w-6 h-6 rounded-xl border-2 flex items-center justify-center transition-all duration-300',
                                    selectedCategories.includes(category.id)
                                        ? 'bg-gradient-to-r from-purple-500 to-pink-500 border-purple-500 shadow-lg'
                                        : 'border-gray-300'
                                ]">
                                    <Check v-if="selectedCategories.includes(category.id)" :size="16"
                                        class="text-white" />
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <Database :size="20" class="text-white" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Budget Range</h3>
                        </div>
                        <div class="space-y-3">
                            <button v-for="range in priceRanges" :key="range.id"
                                @click="selectedPriceRange = selectedPriceRange === range.id ? '' : range.id" :class="[
                                    'w-full flex items-center justify-between px-6 py-4 rounded-2xl border text-sm transition-all duration-300 transform hover:scale-105',
                                    selectedPriceRange === range.id
                                        ? 'bg-gradient-to-r from-green-50 to-blue-50 border-green-300 text-green-700 shadow-lg'
                                        : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-sm hover:shadow-md'
                                ]">
                                <span class="font-semibold">{{ range.label }}</span>
                                <div :class="[
                                    'w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-300',
                                    selectedPriceRange === range.id
                                        ? 'bg-gradient-to-r from-green-500 to-blue-500 border-green-500 shadow-lg'
                                        : 'border-gray-300'
                                ]">
                                    <div v-if="selectedPriceRange === range.id"
                                        class="w-3 h-3 bg-white rounded-full shadow-sm"></div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <Star :size="20" class="text-white" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Minimum Rating</h3>
                        </div>
                        <div class="space-y-3">
                            <button v-for="rating in ratingFilters" :key="rating.value"
                                @click="selectedRating = selectedRating === rating.value ? '' : rating.value" :class="[
                                    'w-full flex items-center justify-between px-6 py-4 rounded-2xl border text-sm transition-all duration-300 transform hover:scale-105',
                                    selectedRating === rating.value
                                        ? 'bg-gradient-to-r from-yellow-50 to-orange-50 border-yellow-300 text-yellow-700 shadow-lg'
                                        : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-sm hover:shadow-md'
                                ]">
                                <div class="flex items-center gap-4">
                                    <div class="flex">
                                        <Star v-for="i in 5" :key="i" :size="18"
                                            :class="i <= rating.value ? 'text-yellow-400 fill-current' : 'text-gray-300'" />
                                    </div>
                                    <span class="font-semibold">{{ rating.value }}+ stars</span>
                                </div>
                                <div :class="[
                                    'w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-300',
                                    selectedRating === rating.value
                                        ? 'bg-gradient-to-r from-yellow-500 to-orange-500 border-yellow-500 shadow-lg'
                                        : 'border-gray-300'
                                ]">
                                    <div v-if="selectedRating === rating.value"
                                        class="w-3 h-3 bg-white rounded-full shadow-sm"></div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                <button @click="clearFilters"
                    class="flex items-center gap-3 px-6 py-3 text-gray-700 border border-gray-300 rounded-2xl hover:bg-gray-50 transition-all duration-300 font-semibold shadow-sm hover:shadow-md transform hover:scale-105">
                    <X :size="18" />
                    Clear All Filters
                </button>
                <button @click="applyFilters"
                    class="flex items-center gap-3 px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                    <Check :size="18" />
                    Apply Filters
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
/* Custom animations */
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0px) rotate(0deg);
    }

    50% {
        transform: translateY(-20px) rotate(180deg);
    }
}

@keyframes float-delayed {

    0%,
    100% {
        transform: translateY(0px) rotate(0deg);
    }

    50% {
        transform: translateY(-15px) rotate(-180deg);
    }
}

@keyframes float-slow {

    0%,
    100% {
        transform: translateY(0px) scale(1);
    }

    50% {
        transform: translateY(-10px) scale(1.1);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

.animation-delay-200 {
    animation-delay: 0.2s;
}

.animation-delay-400 {
    animation-delay: 0.4s;
}

.animation-delay-600 {
    animation-delay: 0.6s;
}

.animation-delay-800 {
    animation-delay: 0.8s;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 8s ease-in-out infinite;
    animation-delay: 2s;
}

.animate-float-slow {
    animation: float-slow 10s ease-in-out infinite;
    animation-delay: 1s;
}

/* Glassmorphism effects */
.backdrop-blur-xl {
    backdrop-filter: blur(16px);
}

.backdrop-blur-lg {
    backdrop-filter: blur(12px);
}

.backdrop-blur-md {
    backdrop-filter: blur(8px);
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Custom gradient text */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
}
</style>
