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
import { Sliders, Database, Filter, Check, Star, X } from 'lucide-vue-next';


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


// Check for active filters from URL params (not temporary state)
// const hasActiveFilters = computed(() => {
//     return !!(search.value ||
//         (selectedCategories.value && selectedCategories.value.length > 0) ||
//         selectedPriceRange.value ||
//         selectedRating.value);
// });

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
                            <!-- Enhanced Filter Button -->
                            <button @click="showFiltersDialog = true"
                                class="relative flex items-center gap-2 px-4 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200 shadow-sm hover:shadow">
                                <Sliders :size="16" />
                                <span class="hidden sm:inline">Filters</span>
                                <!-- Active Filter Count Badge -->
                                <div v-if="activeFilterCount > 0"
                                    class="absolute -top-2 -right-2 w-5 h-5 bg-blue-500 text-white text-xs rounded-full flex items-center justify-center font-medium">
                                    {{ activeFilterCount }}
                                </div>
                            </button>

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

    <!-- Enhanced Filters Dialog -->
    <Dialog v-model:open="showFiltersDialog">
        <DialogContent class="sm:max-w-4xl w-full max-h-[85vh] overflow-hidden flex flex-col">
            <DialogHeader class="pb-4 border-b border-gray-200">
                <DialogTitle class="text-xl font-semibold text-gray-900">Advanced Filters</DialogTitle>
                <DialogDescription class="text-gray-600">
                    Refine your search to find exactly what you need
                </DialogDescription>
            </DialogHeader>

            <div class="flex-1 overflow-y-auto py-6">
                <!-- Grid layout for filters -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Category Filter -->
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <Filter :size="16" class="text-blue-600" />
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">Service Categories</h3>
                        </div>
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            <button v-for="category in categories" :key="category.id"
                                @click="toggleCategory(category.id)" :class="[
                                    'w-full flex items-center justify-between px-4 py-3 rounded-lg border text-sm transition-all duration-200',
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
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <Database :size="16" class="text-green-600" />
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">Budget Range</h3>
                        </div>
                        <div class="space-y-3">



                            <button v-for="range in priceRanges" :key="range.id"
                                @click="selectedPriceRange = selectedPriceRange === range.id ? '' : range.id" :class="[
                                    'w-full flex items-center justify-between px-4 py-3 rounded-lg border text-sm transition-all duration-200',
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
                                    <div v-if="selectedPriceRange === range.id" class="w-2 h-2 bg-white rounded-full">
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <Star :size="16" class="text-yellow-600" />
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">Minimum Rating</h3>
                        </div>
                        <div class="space-y-3">
                            <button v-for="rating in ratingFilters" :key="rating.value"
                                @click="selectedRating = selectedRating === rating.value ? '' : rating.value" :class="[
                                    'w-full flex items-center justify-between px-4 py-3 rounded-lg border text-sm transition-all duration-200',
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
                                    <div v-if="selectedRating === rating.value" class="w-2 h-2 bg-white rounded-full">
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
                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    <X :size="16" />
                    Clear All Filters
                </button>
                <button @click="applyFilters"
                    class="flex items-center gap-2 px-6 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-sm">
                    <Check :size="16" />
                    Apply Filters
                </button>
            </div>
        </DialogContent>
    </Dialog>
</template>
