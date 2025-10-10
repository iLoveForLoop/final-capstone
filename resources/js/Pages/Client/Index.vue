<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

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

// Animation state
const animatedElements = ref([]);

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
    // handleSearch();
    showFiltersDialog.value = false
}

const clearFilters = () => {
    selectedCategories.value = []
    selectedPriceRange.value = ''
    selectedRating.value = ''
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
    <ClientLayout>



        <Head title="Home" />
        <QuickBookingStepperModal ref="eventModal" :categories="categories" />

        <div class="min-h-screen bg-gradient-to-br from-gray-300 via-white to-gray-500 relative overflow-hidden">
            <!-- Enhanced animated background -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
                <div
                    class="absolute -top-40 -right-32 w-80 h-80 bg-blue-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000">
                </div>
                <div
                    class="absolute top-60 -left-20 w-80 h-80 bg-purple-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000">
                </div>
                <div
                    class="absolute -bottom-20 left-40 w-80 h-80 bg-teal-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob">
                </div>
            </div>

            <div class="absolute inset-0">
                <div
                    class="absolute top-1/4 left-3/4 w-52 h-52 bg-teal-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000">
                </div>
                <!-- <div class="absolute animate-float-delayed top-1/3 right-1/3 w-5 h-5 bg-purple-300 rounded-full opacity-80">
            </div>
            <div class="absolute animate-float-slow top-2/3 left-1/5 w-10 h-10 bg-pink-400 rounded-full opacity-40">
            </div>
            <div class="absolute animate-float top-1/2 right-1/4 w-6 h-6 bg-blue-300 rounded-full opacity-70"></div>
            <div
                class="absolute animate-float-delayed bottom-1/4 left-2/3 w-5 h-5 bg-purple-400 rounded-full opacity-50">
            </div> -->
            </div>



            <!-- Hero Section -->
            <section class="relative z-10">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
                    <div class="text-center max-w-3xl mx-auto reveal-animation">
                        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                            Find the Perfect Vendors for Your <span class="text-blue-600">Event</span>
                        </h1>
                        <p class="text-lg md:text-xl text-gray-600 mb-12 max-w-2xl mx-auto leading-relaxed">
                            Connect with verified professionals and manage your bookings in one place
                        </p>

                        <!-- Search Bar -->
                        <div
                            class="bg-white/80 backdrop-blur-sm border border-gray-200/60 rounded-xl p-4 max-w-2xl mx-auto mb-10 shadow-lg reveal-animation animation-delay-100">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <!-- Search Input -->
                                <div class="flex-1 relative">
                                    <Search class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"
                                        :size="20" />
                                    <input v-model="search" type="text" placeholder="Search vendors or services..."
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
                        <button @click="eventModal.open()"
                            class="inline-flex items-center px-6 py-3 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition-all duration-300 font-medium shadow-md hover:shadow-lg reveal-animation animation-delay-400">
                            <Calendar class="h-5 w-5 mr-2" />
                            Start Planning
                        </button>
                    </div>
                </div>
            </section>

            <!-- Quick Stats -->
            <section class="py-16 relative z-10">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            class="text-center bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 reveal-animation">
                            <div class="text-3xl font-bold text-blue-600 mb-2">3</div>
                            <div class="text-sm text-gray-600 font-medium flex items-center justify-center">
                                <Calendar class="h-4 w-4 mr-1" /> Active Bookings
                            </div>
                        </div>
                        <div
                            class="text-center bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 reveal-animation animation-delay-100">
                            <div class="text-3xl font-bold text-blue-600 mb-2">12</div>
                            <div class="text-sm text-gray-600 font-medium flex items-center justify-center">
                                <Users class="h-4 w-4 mr-1" /> Completed Events
                            </div>
                        </div>
                        <div
                            class="text-center bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 reveal-animation animation-delay-200">
                            <div class="text-3xl font-bold text-blue-600 mb-2">8</div>
                            <div class="text-sm text-gray-600 font-medium flex items-center justify-center">
                                <Heart class="h-4 w-4 mr-1" /> Saved Vendors
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Featured Services -->
            <section class="py-16 relative z-10">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center mb-10 reveal-animation">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Featured Services</h2>
                            <p class="text-gray-600 mt-2">Top-rated vendors in your area</p>
                        </div>
                        <Link href="/services"
                            class="text-blue-600 hover:text-blue-700 font-medium flex items-center group">
                        View All
                        <ArrowRight class="ml-1 h-4 w-4 group-hover:translate-x-1 transition-transform duration-300" />
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



            <!-- Footer -->
            <footer class="bg-gradient-to-b from-gray-900 to-gray-800 text-white py-16 relative z-10">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid md:grid-cols-4 gap-10">
                        <div class="reveal-animation">
                            <div class="text-2xl font-bold mb-5">Eventory</div>
                            <p class="text-gray-400 text-sm leading-relaxed">
                                Connecting event planners with trusted local vendors.
                            </p>
                        </div>

                        <!-- <div class="reveal-animation animation-delay-100">
                        <h3 class="font-semibold mb-5 text-lg">Platform</h3>
                        <ul class="space-y-3 text-sm">
                            <li>
                                <Link href="/dashboard"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">Dashboard
                                </Link>
                            </li>
                            <li>
                                <Link href="/bookings"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">My Bookings
                                </Link>
                            </li>
                            <li>
                                <Link href="/services"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">Browse
                                Services</Link>
                            </li>
                            <li>
                                <Link href="/vendors"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">Find Vendors
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div class="reveal-animation animation-delay-200">
                        <h3 class="font-semibold mb-5 text-lg">Categories</h3>
                        <ul class="space-y-3 text-sm">
                            <li>
                                <Link href="/categories/catering"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">Catering
                                </Link>
                            </li>
                            <li>
                                <Link href="/categories/photography"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">Photography
                                </Link>
                            </li>
                            <li>
                                <Link href="/categories/sound-systems"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">Audio &
                                Sound</Link>
                            </li>
                            <li>
                                <Link href="/categories/entertainers"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">
                                Entertainment</Link>
                            </li>
                        </ul>
                    </div> -->

                        <!-- <div class="reveal-animation animation-delay-300">
                        <h3 class="font-semibold mb-5 text-lg">Support</h3>
                        <ul class="space-y-3 text-sm">
                            <li>
                                <Link href="/help"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">Help Center
                                </Link>
                            </li>
                            <li>
                                <Link href="/contact"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">Contact</Link>
                            </li>
                            <li>
                                <Link href="/about"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">About</Link>
                            </li>
                            <li>
                                <Link href="/privacy"
                                    class="text-gray-400 hover:text-white transition-colors duration-300">Privacy</Link>
                            </li>
                        </ul>
                    </div> -->
                    </div>

                    <div class="border-t border-gray-800 mt-12 pt-8 reveal-animation">
                        <p class="text-gray-400 text-sm text-center">
                            © 2024 Eventory. All rights reserved. | Capstone Project
                        </p>
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

<style>
@keyframes float {

    0%,
    100% {
        transform: translateY(0) scale(1);
        opacity: 0.2;
    }

    50% {
        transform: translateY(-20px) scale(1.05);
        opacity: 0.25;
    }
}

@keyframes reveal {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-float {
    animation: float 8s ease-in-out infinite;
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


/* BLOB */
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

.animate-blob {
    animation: blob 7s infinite;
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
</style>
