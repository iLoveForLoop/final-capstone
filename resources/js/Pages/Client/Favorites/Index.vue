<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import NewServiceCard from '@/Components/Client/NewServiceCard.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Heart, Plus, Search, Filter, X, Grid, List, ChevronDown, ChevronUp } from 'lucide-vue-next';

const props = defineProps({
    favorites: {
        type: Object,
    }
})

// Mock data
const mockCategories = [
    { id: 1, name: 'Photography' },
    { id: 2, name: 'Catering' },
    { id: 3, name: 'Entertainment' },
    { id: 4, name: 'Decoration' },
    { id: 5, name: 'Transportation' }
];

// Reactive filters
const searchQuery = ref('');
const selectedCategory = ref('all');
const selectedAvailability = ref('all');
const viewMode = ref('grid');
const showFilters = ref(false);

// Filter favorites
const filteredFavorites = computed(() => {
    let filtered = props.favorites.data;

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(service =>
            service.name.toLowerCase().includes(query) ||
            service.description.toLowerCase().includes(query) ||
            service.vendor.name.toLowerCase().includes(query)
        );
    }

    // Category filter
    if (selectedCategory.value !== 'all') {
        filtered = filtered.filter(service => service.category.id == selectedCategory.value);
    }

    // Availability filter
    if (selectedAvailability.value !== 'all') {
        filtered = filtered.filter(service => service.availability === selectedAvailability.value);
    }

    return {
        data: filtered,
        total: filtered.length
    };
});

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = 'all';
    selectedAvailability.value = 'all';
    showFilters.value = false;
};

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

// Remove from favorites
const removeFromFavorites = (serviceId) => {
    if (confirm('Remove this service from your favorites?')) {
        props.favorites.data = props.favorites.data.filter(service => service.id !== serviceId);
        props.favorites.total = props.favorites.data.length;
        try {
            router.delete(route('client.favorites.destroy', serviceId))
        } catch (error) {
            console.log('Error: ', error.message)
        }
    }
};

// Clear all favorites
const clearAllFavorites = () => {
    if (confirm('Are you sure you want to remove all services from your favorites?')) {
        props.favorites.data = [];
        props.favorites.total = 0;
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};
</script>

<template>
    <ClientLayout>
        <div class="min-h-screen bg-gray-50/30">
            <!-- Header Section -->
            <div class="bg-white border-b border-gray-200/60">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <div class="p-2 bg-red-50 rounded-lg">
                                <Heart class="w-5 h-5 sm:w-6 sm:h-6 text-red-600 fill-current" />
                            </div>
                            <div>
                                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">My Favorites</h1>
                                <p class="text-gray-600 text-xs sm:text-sm">Your saved services for easy access</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <button v-if="favorites.data.length > 0" @click="clearAllFavorites"
                                class="w-full sm:w-auto bg-red-600 text-white px-4 py-2.5 rounded-lg hover:bg-red-700 transition-all duration-200 font-medium flex items-center justify-center space-x-2 shadow-sm hover:shadow-md">
                                <X class="w-4 h-4" />
                                <span>Clear All</span>
                            </button>
                            <button
                                class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium flex items-center justify-center space-x-2 shadow-sm hover:shadow-md">
                                <Plus class="w-4 h-4" />
                                <span>Browse Services</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div v-if="favorites.data.length > 0" class="bg-white border-b border-gray-200/60 sticky top-0 z-30">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-600">
                            {{ filteredFavorites.total }} favorite{{ filteredFavorites.total !== 1 ? 's' : '' }} found
                        </div>

                        <div class="flex items-center space-x-3">
                            <!-- View Toggle -->
                            <div class="flex rounded-lg bg-gray-100 p-1">
                                <button @click="viewMode = 'grid'" :class="[
                                    'px-3 py-2 text-sm rounded-md transition-all duration-200 font-medium flex items-center space-x-2',
                                    viewMode === 'grid'
                                        ? 'bg-white text-blue-600 shadow-sm'
                                        : 'text-gray-600 hover:text-gray-800'
                                ]">
                                    <Grid class="w-4 h-4" />
                                    <span class="hidden sm:inline">Grid</span>
                                </button>
                                <button @click="viewMode = 'list'" :class="[
                                    'px-3 py-2 text-sm rounded-md transition-all duration-200 font-medium flex items-center space-x-2',
                                    viewMode === 'list'
                                        ? 'bg-white text-blue-600 shadow-sm'
                                        : 'text-gray-600 hover:text-gray-800'
                                ]">
                                    <List class="w-4 h-4" />
                                    <span class="hidden sm:inline">List</span>
                                </button>
                            </div>

                            <!-- Filter Toggle -->
                            <button @click="toggleFilters"
                                class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:border-gray-400 transition-colors">
                                <Filter class="w-4 h-4" />
                                <span>Filters</span>
                                <ChevronDown v-if="!showFilters" class="w-4 h-4" />
                                <ChevronUp v-else class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Expandable Filters -->
                    <div v-if="showFilters" class="mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search Favorites</label>
                                <div class="relative">
                                    <Search
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <input v-model="searchQuery" type="text" placeholder="Search your favorites..."
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select v-model="selectedCategory"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="all">All Categories</option>
                                    <option v-for="category in mockCategories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Availability Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Availability</label>
                                <select v-model="selectedAvailability"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="all">All</option>
                                    <option value="available">Available</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>

                            <!-- Clear Filters -->
                            <div class="md:col-span-4 flex justify-end">
                                <button @click="clearFilters"
                                    class="flex items-center space-x-2 px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:border-gray-400 transition-colors">
                                    <X class="w-4 h-4" />
                                    <span>Clear Filters</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Favorites Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <!-- Favorites Grid/List -->
                <div v-if="filteredFavorites.data.length > 0">
                    <!-- Grid View -->
                    <div v-if="viewMode === 'grid'"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <div v-for="service in filteredFavorites.data" :key="service.id"
                            class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200/60 overflow-hidden hover:shadow-md transition-all duration-200">
                            <NewServiceCard :service="service">
                                <template #favorite>
                                    <div class="absolute top-3 right-3">
                                        <button @click="removeFromFavorites(service.id)"
                                            class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition-all duration-200 shadow-sm hover:shadow-md">
                                            <Heart class="w-4 h-4 fill-current" />
                                        </button>
                                    </div>
                                </template>
                            </NewServiceCard>
                        </div>
                    </div>

                    <!-- List View -->
                    <div v-else class="space-y-3 sm:space-y-4">
                        <div v-for="service in filteredFavorites.data" :key="service.id"
                            class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200/60 p-4 sm:p-6 hover:shadow-md transition-all duration-200">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <img :src="service.image" :alt="service.name"
                                    class="w-full sm:w-48 h-32 object-cover rounded-lg">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-3">
                                        <div class="flex-1">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <span
                                                    class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full border border-blue-200">
                                                    {{ service.category.name }}
                                                </span>
                                                <span v-if="service.availability === 'available'"
                                                    class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full border border-green-200">
                                                    Available
                                                </span>
                                                <span v-else
                                                    class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full border border-red-200">
                                                    Unavailable
                                                </span>
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ service.name }}</h3>
                                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ service.description }}
                                            </p>
                                        </div>
                                        <div class="flex sm:flex-col items-center sm:items-end gap-2">
                                            <button @click="removeFromFavorites(service.id)"
                                                class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition-all duration-200 shadow-sm hover:shadow-md">
                                                <Heart class="w-4 h-4 fill-current" />
                                            </button>
                                            <div class="text-lg font-bold text-green-600">{{ formatPrice(service.price)
                                                }}</div>
                                        </div>
                                    </div>
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-gray-100">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <span class="font-medium text-gray-900">{{ service.vendor.name }}</span>
                                            <span class="mx-2 text-gray-300">•</span>
                                            <span>{{ service.location }}</span>
                                            <span class="mx-2 text-gray-300">•</span>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                {{ service.vendor.rating }}
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button
                                                class="bg-blue-600 text-white py-2 px-4 rounded-lg text-sm hover:bg-blue-700 transition-colors font-medium">
                                                Book Now
                                            </button>
                                            <button
                                                class="border border-gray-300 text-gray-700 py-2 px-4 rounded-lg text-sm hover:bg-gray-50 transition-colors font-medium">
                                                Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="favorites.data.length === 0"
                    class="text-center py-12 sm:py-16 bg-white rounded-lg sm:rounded-xl border border-gray-200/60">
                    <div class="max-w-sm mx-auto px-4">
                        <Heart class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-gray-400 mb-4 sm:mb-6" />
                        <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-2 sm:mb-3">No favorites yet</h3>
                        <p class="text-gray-600 text-sm sm:text-base mb-4 sm:mb-6">
                            Start adding services to your favorites by clicking the heart icon on service cards.
                        </p>
                        <button
                            class="w-full sm:w-auto bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Browse Services
                        </button>
                    </div>
                </div>

                <!-- No Results State -->
                <div v-else
                    class="text-center py-8 sm:py-12 bg-white rounded-lg sm:rounded-xl border border-gray-200/60">
                    <div class="max-w-sm mx-auto px-4">
                        <Search class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400 mb-3 sm:mb-4" />
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No favorites found</h3>
                        <p class="text-gray-600 text-sm sm:text-base mb-3 sm:mb-4">Try adjusting your search or filter
                            criteria.</p>
                        <button @click="clearFilters"
                            class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Clear filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
