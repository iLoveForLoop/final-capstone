<script setup>
import ClientNavbar from '@/Components/ClientNavbar.vue';
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

import ServiceCard from '@/Components/Client/ServiceCard.vue';

// Props from backend
const props = defineProps({
    services: Object,
    categories: Array,
    filters: Object
});

const page = usePage();

// Reactive filters
const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || 'all');
const selectedAvailability = ref(props.filters.availability || 'all');
const viewMode = ref('grid'); // grid or list

// Filter services
const applyFilters = () => {
    router.get('/client/services', {
        search: searchQuery.value || undefined,
        category: selectedCategory.value !== 'all' ? selectedCategory.value : undefined,
        availability: selectedAvailability.value !== 'all' ? selectedAvailability.value : undefined,
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

// Debounced search
let searchTimeout;
const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = 'all';
    selectedAvailability.value = 'all';
    applyFilters();
};


</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <ClientNavbar />

        <!-- Header Section -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Services</h1>
                <p class="text-gray-600">Find the perfect service for your event</p>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input v-model="searchQuery" @input="handleSearch" type="text" placeholder="Search services..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select v-model="selectedCategory" @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Availability Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Availability</label>
                        <select v-model="selectedAvailability" @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All</option>
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        {{ services.total }} services found
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

        <!-- Services Section -->
        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Services Grid/List -->
            <div v-if="services.data.length > 0">

                <ServiceCard :services="services" :viewMode="viewMode" />
                <!-- Pagination  Component Here-->

            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No services found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your search or filter criteria.</p>
                <button @click="clearFilters"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                    Clear filters
                </button>
            </div>
        </div>
    </div>
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
