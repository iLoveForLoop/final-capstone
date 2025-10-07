<script setup>
import ClientNavbar from '@/Components/ClientNavbar.vue';
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from '@/Components/ui/dialog';

import ServiceCard from '@/Components/Client/ServiceCard.vue';
import { Search, ArrowUpDown, ChevronDown, Filter, Database, X, Grid3X3, List, Star, Check, Sliders, ChevronLeft, ChevronRight } from 'lucide-vue-next';

// Props from backend
const props = defineProps({
    services: Object,
    categories: Array,
    filters: Object
});

const page = usePage();

// Reactive filters
const searchQuery = ref(props.filters.search || '');
const selectedCategories = ref(
    Array.isArray(props.filters.categories)
        ? props.filters.categories.map(c => Number(c)) // convert to numbers
        : []
);
const selectedPriceRange = ref(props.filters.price_range || '');
const selectedRating = ref(
    props.filters.rating ? Number(props.filters.rating) : '' // convert to number
);
const sortBy = ref('relevance');
const viewMode = ref('grid');

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

// Filter services
const applyFilters = () => {
    showFiltersDialog.value = false;
    router.get('/client/services', {
        search: searchQuery.value || undefined,
        categories: selectedCategories.value.length > 0 ? selectedCategories.value : undefined,
        price_range: selectedPriceRange.value || undefined,
        rating: selectedRating.value || undefined,
        sort: sortBy.value !== 'relevance' ? sortBy.value : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Handle sort change
const handleSort = () => {
    applyFilters();
};

// Handle search button click
const handleSearch = () => {
    applyFilters();
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategories.value = [];
    selectedPriceRange.value = '';
    selectedRating.value = '';
    sortBy.value = 'relevance';
    applyFilters();
};

// Check for active filters from URL params (not temporary state)
const hasActiveFilters = computed(() => {
    return !!(props.filters.search ||
        (props.filters.categories && props.filters.categories.length > 0) ||
        props.filters.price_range ||
        props.filters.rating);
});

// Get active filter count
const activeFilterCount = computed(() => {
    let count = 0;
    if (props.filters.search) count++;
    if (props.filters.categories && props.filters.categories.length > 0) count++;
    if (props.filters.price_range) count++;
    if (props.filters.rating) count++;
    return count;
});

// Get category name by id
const getCategoryName = (id) => {
    const category = props.categories.find(cat => cat.id == id);
    return category ? category.name : '';
};

// Pagination functions
const goToPage = (url) => {
    if (url) {
        router.visit(url, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                // Scroll to top of services section
                const servicesSection = document.querySelector('.services-section');
                if (servicesSection) {
                    servicesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    }
};

// Generate page numbers for pagination
const pageNumbers = computed(() => {
    const current = props.services.current_page;
    const last = props.services.last_page;
    const delta = 2; // number of pages to show on each side of current
    const range = [];

    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
        range.push(i);
    }

    if (current - delta > 2) {
        range.unshift('...');
    }
    if (current + delta < last - 1) {
        range.push('...');
    }

    range.unshift(1);
    if (last > 1) range.push(last);

    return range;
});
</script>

<template>
    <ClientNavbar />
    <div class="min-h-screen bg-gray-50">

        <!-- Enhanced Filters Section -->
        <div class="bg-white border-b border-gray-100 shadow-sm">
            <div class=" max-w-7xl mx-auto px-6 py-6">
                <!-- Main Filter Row -->
                <div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-6">
                    <!-- Search with Icon and Button -->
                    <div class="flex-1 flex gap-2 ">
                        <div class="relative flex-1">
                            <Search :size="18"
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                            <input v-model="searchQuery" type="text"
                                placeholder="Search services, categories, vendors..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 transition-all duration-200 text-sm placeholder-gray-500">
                        </div>
                        <button @click="handleSearch"
                            class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span class="hidden sm:inline">Search</span>
                            <Search :size="16" class="sm:ml-2" />
                        </button>
                    </div>

                    <!-- Filter Controls -->
                    <div class="flex items-center gap-3">
                        <!-- Sort Dropdown with Icon -->
                        <div class="relative">
                            <div class="flex items-center">
                                <ArrowUpDown :size="16"
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 z-10" />
                                <select v-model="sortBy"
                                    class="pl-10 pr-10 py-3 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 text-sm appearance-none transition-all duration-200 min-w-[180px] font-medium"
                                    @change="handleSort">
                                    <option value="relevance">Most Relevant</option>
                                    <option value="rating">Highest Rated</option>
                                    <option value="reviews">Most Reviews</option>
                                    <option value="price_low">Price: Low to High</option>
                                    <option value="price_high">Price: High to Low</option>
                                </select>
                                <ChevronDown :size="16"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none" />
                            </div>
                        </div>

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
                    </div>
                </div>

                <!-- Results Bar with Enhanced Styling -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- Results Count with Active Filters -->
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <Database :size="16" class="text-gray-400" />
                            <span class="font-medium text-gray-900">{{ services.total }}</span>
                            <span>services found</span>
                        </div>

                        <!-- Active Filters Display -->
                        <div v-if="hasActiveFilters" class="flex items-center gap-2">
                            <div class="w-1 h-4 bg-gray-300 rounded-full"></div>
                            <div class="flex gap-2 flex-wrap">
                                <span v-if="props.filters.search"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-blue-50 border border-blue-200 text-blue-700 text-xs font-medium">
                                    Search: "{{ props.filters.search }}"
                                    <button @click="searchQuery = ''; applyFilters()" class="hover:bg-blue-100 rounded">
                                        <X :size="12" />
                                    </button>
                                </span>

                                <span v-if="props.filters.categories && props.filters.categories.length > 0"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-purple-50 border border-purple-200 text-purple-700 text-xs font-medium">
                                    {{ props.filters.categories.length }} Categories
                                    <button @click="selectedCategories = []; applyFilters()"
                                        class="hover:bg-purple-100 rounded">
                                        <X :size="12" />
                                    </button>
                                </span>

                                <span v-if="props.filters.price_range"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-green-50 border border-green-200 text-green-700 text-xs font-medium">
                                    Budget Filter
                                    <button @click="selectedPriceRange = ''; applyFilters()"
                                        class="hover:bg-green-100 rounded">
                                        <X :size="12" />
                                    </button>
                                </span>

                                <span v-if="props.filters.rating"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-medium">
                                    {{ props.filters.rating }}+ Stars
                                    <button @click="selectedRating = ''; applyFilters()"
                                        class="hover:bg-yellow-100 rounded">
                                        <X :size="12" />
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side Controls -->
                    <div class="flex items-center gap-4">
                        <!-- Clear All Filters Button -->
                        <button v-if="hasActiveFilters" @click="clearFilters"
                            class="flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors font-medium">
                            <X :size="14" />
                            Clear all
                        </button>

                        <!-- View Mode Toggle -->
                        <div class="flex items-center bg-gray-100 rounded-lg p-1">
                            <button @click="viewMode = 'grid'" :class="[
                                'flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200',
                                viewMode === 'grid'
                                    ? 'bg-white text-gray-900 shadow-sm'
                                    : 'text-gray-600 hover:text-gray-800'
                            ]">
                                <Grid3X3 :size="16" />
                                <span class="hidden sm:inline">Grid</span>
                            </button>
                            <button @click="viewMode = 'list'" :class="[
                                'flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200',
                                viewMode === 'list'
                                    ? 'bg-white text-gray-900 shadow-sm'
                                    : 'text-gray-600 hover:text-gray-800'
                            ]">
                                <List :size="16" />
                                <span class="hidden sm:inline">List</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <div class="max-w-7xl mx-auto px-6 py-8 services-section">
            <!-- Services Grid/List -->
            <div v-if="services.data.length > 0">
                <ServiceCard :services="services" :viewMode="viewMode" />

                <!-- Modern Pagination -->
                <div v-if="services.last_page > 1" class="mt-12">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <!-- Results Info -->
                        <div class="text-sm text-gray-600">
                            Showing
                            <span class="font-semibold text-gray-900">{{ services.from }}-{{ services.to }}</span>
                            of
                            <span class="font-semibold text-gray-900">{{ services.total }}</span>
                            results
                        </div>

                        <!-- Pagination Controls -->
                        <div class="flex items-center space-x-1">
                            <!-- Previous Button -->
                            <button @click="goToPage(services.prev_page_url)" :disabled="!services.prev_page_url"
                                :class="[
                                    'flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg border transition-all duration-200',
                                    services.prev_page_url
                                        ? 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50 hover:border-gray-400 hover:shadow-sm'
                                        : 'text-gray-400 bg-gray-100 border-gray-200 cursor-not-allowed'
                                ]">
                                <ChevronLeft :size="16" />
                                <span class="hidden sm:inline">Previous</span>
                            </button>

                            <!-- Page Numbers -->
                            <div class="flex items-center space-x-1">
                                <button v-for="page in pageNumbers" :key="page"
                                    @click="typeof page === 'number' ? goToPage(services.path + '?page=' + page) : null"
                                    :disabled="typeof page !== 'number'" :class="[
                                        'min-w-[40px] h-10 px-3 text-sm font-medium rounded-lg border transition-all duration-200',
                                        typeof page !== 'number'
                                            ? 'text-gray-500 bg-transparent border-transparent cursor-default'
                                            : page === services.current_page
                                                ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                                                : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50 hover:border-gray-400'
                                    ]">
                                    {{ page }}
                                </button>
                            </div>

                            <!-- Next Button -->
                            <button @click="goToPage(services.next_page_url)" :disabled="!services.next_page_url"
                                :class="[
                                    'flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg border transition-all duration-200',
                                    services.next_page_url
                                        ? 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50 hover:border-gray-400 hover:shadow-sm'
                                        : 'text-gray-400 bg-gray-100 border-gray-200 cursor-not-allowed'
                                ]">
                                <span class="hidden sm:inline">Next</span>
                                <ChevronRight :size="16" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <Database :size="32" class="text-gray-400" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No services found</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">We couldn't find any services matching your criteria. Try
                    adjusting your filters or search terms.</p>
                <button @click="clearFilters"
                    class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <X :size="16" />
                    Clear all filters
                </button>
            </div>
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
                                    @click="selectedPriceRange = selectedPriceRange === range.id ? '' : range.id"
                                    :class="[
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
                                        <div v-if="selectedPriceRange === range.id"
                                            class="w-2 h-2 bg-white rounded-full"></div>
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
                                    @click="selectedRating = selectedRating === rating.value ? '' : rating.value"
                                    :class="[
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
                                        <div v-if="selectedRating === rating.value"
                                            class="w-2 h-2 bg-white rounded-full"></div>
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
