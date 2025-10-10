<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import NewServiceCard from '@/Components/Client/NewServiceCard.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';

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

console.log(props.favorites)

// Reactive filters
const searchQuery = ref('');
const selectedCategory = ref('all');
const selectedAvailability = ref('all');
const viewMode = ref('grid'); // grid or list

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


        <div class="min-h-screen bg-gray-50">

            <!-- Header Section -->
            <div class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-6 py-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Favorites</h1>
                            <p class="text-gray-600">Your saved services for easy access</p>
                        </div>
                        <div v-if="favorites.data.length > 0">
                            <button @click="clearAllFavorites"
                                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors text-sm">
                                Clear All Favorites
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div v-if="favorites.data.length > 0" class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Favorites</label>
                            <input v-model="searchQuery" type="text" placeholder="Search your favorites..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select v-model="selectedCategory"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="all">All</option>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            {{ filteredFavorites.total }} favorite{{ filteredFavorites.total !== 1 ? 's' : '' }} found
                        </div>
                        <div class="flex items-center space-x-4">
                            <button @click="clearFilters" class="text-sm text-gray-600 hover:text-gray-800">
                                Clear filters
                            </button>
                            <div class="flex rounded border border-gray-300">
                                <button @click="viewMode = 'grid'" :class="[
                                    'px-3 py-1 text-sm',
                                    viewMode === 'grid'
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-600 hover:text-gray-800'
                                ]">
                                    Grid
                                </button>
                                <button @click="viewMode = 'list'" :class="[
                                    'px-3 py-1 text-sm border-l border-gray-300',
                                    viewMode === 'list'
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-600 hover:text-gray-800'
                                ]">
                                    List
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Favorites Section -->
            <div class="max-w-7xl mx-auto px-6 py-8">
                <!-- Favorites Grid/List -->
                <div v-if="filteredFavorites.data.length > 0">
                    <!-- Grid View -->
                    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="service in filteredFavorites.data" :key="service.id"
                            class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                            <NewServiceCard :service="service">
                                <template #favorite>
                                    <div class="absolute top-3 right-3 flex space-x-2">
                                        <button @click="removeFromFavorites(service.id)"
                                            class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition-colors">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </NewServiceCard>

                        </div>
                    </div>

                    <!-- List View -->
                    <div v-else class="space-y-4">
                        <div v-for="service in filteredFavorites.data" :key="service.id"
                            class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                            <div class="flex">
                                <img :src="service.image" :alt="service.name" class="w-48 h-32 object-cover">
                                <div class="flex-1 p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-2">
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">
                                                    {{ service.category.name }}
                                                </span>
                                                <span v-if="service.availability === 'available'"
                                                    class="bg-green-500 text-white px-2 py-1 text-xs rounded">
                                                    Available
                                                </span>
                                                <span v-else class="bg-red-500 text-white px-2 py-1 text-xs rounded">
                                                    Unavailable
                                                </span>
                                            </div>
                                            <h3 class="font-semibold text-gray-900 mb-1">{{ service.name }}</h3>
                                            <p class="text-sm text-gray-600 mb-2 line-clamp-1">{{ service.description }}
                                            </p>
                                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                                <span class="font-medium">{{ service.vendor.name }}</span>
                                                <span class="mx-2">•</span>
                                                <span>{{ service.location }}</span>
                                                <span class="mx-2">•</span>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    {{ service.vendor.rating }}
                                                </div>
                                            </div>
                                            <div class="text-lg font-bold text-green-600">{{ formatPrice(service.price)
                                                }}
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end ml-4">
                                            <button @click="removeFromFavorites(service.id)"
                                                class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition-colors mb-2">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <div class="flex space-x-2">
                                                <button
                                                    class="bg-blue-600 text-white py-1 px-3 rounded text-sm hover:bg-blue-700 transition-colors">
                                                    Book Now
                                                </button>
                                                <button
                                                    class="border border-gray-300 text-gray-700 py-1 px-3 rounded text-sm hover:bg-gray-50 transition-colors">
                                                    Details
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="favorites.data.length === 0" class="text-center py-16">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900 mb-3">No favorites yet</h3>
                    <p class="text-gray-600 mb-6 max-w-sm mx-auto">
                        Start adding services to your favorites by clicking the heart icon on service cards.
                    </p>
                    <button
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Browse Services
                    </button>
                </div>

                <!-- No Results State -->
                <div v-else class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No favorites found</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your search or filter criteria.</p>
                    <button @click="clearFilters"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                        Clear filters
                    </button>
                </div>
            </div>

            <!-- Quick Actions for non-empty favorites -->
            <div v-if="favorites.data.length > 0" class="bg-white border-t border-gray-200">
                <div class="max-w-7xl mx-auto px-6 py-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Manage your favorite services quickly
                        </div>
                        <div class="flex space-x-3">
                            <button
                                class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 transition-colors text-sm">
                                Browse More Services
                            </button>
                            <button
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                                Book a Service
                            </button>
                        </div>
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
