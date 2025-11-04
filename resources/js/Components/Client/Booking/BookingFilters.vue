<template>
    <div class="bg-white border-b border-gray-200/60 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
            <!-- Mobile: Compact header with filter trigger -->
            <div class="lg:hidden">
                <div class="flex items-center justify-between mb-3">
                    <!-- Results count -->
                    <div class="text-sm text-gray-600">
                        <span class="font-medium text-gray-900">{{ totalBookings }}</span>
                        booking{{ totalBookings !== 1 ? 's' : '' }}
                    </div>

                    <!-- Filter toggle button for mobile -->
                    <button @click="showMobileFilters = !showMobileFilters"
                        class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:border-gray-400 transition-colors">
                        <Filter class="w-4 h-4" />
                        <span>Filters</span>
                        <ChevronDown v-if="!showMobileFilters" class="w-4 h-4" />
                        <ChevronUp v-else class="w-4 h-4" />
                    </button>
                </div>

                <!-- Active filters indicator for mobile -->
                <div v-if="hasActiveFilters" class="flex items-center space-x-2 mb-3">
                    <div class="w-1 h-1 bg-blue-500 rounded-full"></div>
                    <span class="text-xs text-blue-600 font-medium">Filters active</span>
                    <button @click="$emit('clear-filters')"
                        class="ml-auto text-xs text-red-600 hover:text-red-800 font-medium">
                        Clear all
                    </button>
                </div>

                <!-- Mobile filters dropdown -->
                <div v-if="showMobileFilters" class="space-y-3 p-4 bg-gray-50 rounded-lg border border-gray-200 mb-3">
                    <!-- Search -->
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input :value="searchQuery" @input="$emit('update:searchQuery', $event.target.value)"
                            type="text" placeholder="Search bookings..."
                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select :value="selectedCategory"
                            @change="$emit('update:selectedCategory', $event.target.value)"
                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="all">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select :value="selectedStatus" @change="$emit('update:selectedStatus', $event.target.value)"
                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="all">All Status</option>
                            <option v-for="status in bookingStatuses" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Date Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <select :value="selectedDateRange"
                            @change="$emit('update:selectedDateRange', $event.target.value)"
                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="all">All Dates</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="past">Past Events</option>
                            <option value="thisMonth">This Month</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Desktop: Single line filters -->
            <div class="hidden lg:flex lg:items-center lg:justify-between">
                <!-- Left side: Results count -->
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium text-gray-900">{{ totalBookings }}</span>
                        booking{{ totalBookings !== 1 ? 's' : '' }} found
                    </div>

                    <!-- Active filters indicator -->
                    <div v-if="hasActiveFilters" class="flex items-center space-x-2">
                        <div class="w-1 h-1 bg-blue-500 rounded-full"></div>
                        <span class="text-xs text-blue-600 font-medium">Filters active</span>
                    </div>
                </div>

                <!-- Right side: Filters in single line -->
                <div class="flex items-center space-x-3">
                    <!-- Search -->
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input :value="searchQuery" @input="$emit('update:searchQuery', $event.target.value)"
                            type="text" placeholder="Search bookings..."
                            class="w-64 pl-10 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Category Filter -->
                    <div class="relative">
                        <select :value="selectedCategory"
                            @change="$emit('update:selectedCategory', $event.target.value)"
                            class="appearance-none w-40 pl-3 pr-8 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="all">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <ChevronDown
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>

                    <!-- Status Filter -->
                    <div class="relative">
                        <select :value="selectedStatus" @change="$emit('update:selectedStatus', $event.target.value)"
                            class="appearance-none w-40 pl-3 pr-8 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="all">All Status</option>
                            <option v-for="status in bookingStatuses" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </option>
                        </select>
                        <ChevronDown
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>

                    <!-- Date Range Filter -->
                    <div class="relative">
                        <select :value="selectedDateRange"
                            @change="$emit('update:selectedDateRange', $event.target.value)"
                            class="appearance-none w-40 pl-3 pr-8 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="all">All Dates</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="past">Past Events</option>
                            <option value="thisMonth">This Month</option>
                        </select>
                        <ChevronDown
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>

                    <!-- Clear Filters Button (only show when filters are active) -->
                    <button v-if="hasActiveFilters" @click="$emit('clear-filters')"
                        class="flex items-center space-x-2 px-3 py-2.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:border-gray-400 transition-colors">
                        <X class="w-4 h-4" />
                        <span>Clear</span>
                    </button>
                </div>
            </div>

            <!-- Active Filter Tags - Show on all screen sizes -->
            <div v-if="hasActiveFilters" class="mt-3 flex flex-wrap gap-2">
                <span v-if="searchQuery"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                    Search: "{{ searchQuery }}"
                    <button @click="$emit('update:searchQuery', '')"
                        class="ml-1.5 hover:bg-blue-200 rounded-full p-0.5 transition-colors">
                        <X class="w-3 h-3" />
                    </button>
                </span>

                <span v-if="selectedCategory !== 'all'"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                    Category: {{ getCategoryName(selectedCategory) }}
                    <button @click="$emit('update:selectedCategory', 'all')"
                        class="ml-1.5 hover:bg-green-200 rounded-full p-0.5 transition-colors">
                        <X class="w-3 h-3" />
                    </button>
                </span>

                <span v-if="selectedStatus !== 'all'"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                    Status: {{ getStatusLabel(selectedStatus) }}
                    <button @click="$emit('update:selectedStatus', 'all')"
                        class="ml-1.5 hover:bg-purple-200 rounded-full p-0.5 transition-colors">
                        <X class="w-3 h-3" />
                    </button>
                </span>

                <span v-if="selectedDateRange !== 'all'"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                    Date: {{ getDateRangeLabel(selectedDateRange) }}
                    <button @click="$emit('update:selectedDateRange', 'all')"
                        class="ml-1.5 hover:bg-amber-200 rounded-full p-0.5 transition-colors">
                        <X class="w-3 h-3" />
                    </button>
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Search, Filter, X, ChevronDown, ChevronUp } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps({
    searchQuery: String,
    selectedCategory: String,
    selectedStatus: String,
    selectedDateRange: String,
    categories: Array,
    bookingStatuses: Array,
    totalBookings: Number
});

const showMobileFilters = ref(false);

// Compute if any filters are active
const hasActiveFilters = computed(() => {
    return props.searchQuery ||
        props.selectedCategory !== 'all' ||
        props.selectedStatus !== 'all' ||
        props.selectedDateRange !== 'all';
});

// Helper functions for filter tags
const getCategoryName = (categoryId) => {
    const category = props.categories.find(cat => cat.id === categoryId);
    return category ? category.name : categoryId;
};

const getStatusLabel = (statusValue) => {
    const status = props.bookingStatuses.find(s => s.value === statusValue);
    return status ? status.label : statusValue;
};

const getDateRangeLabel = (dateRange) => {
    const labels = {
        upcoming: 'Upcoming',
        past: 'Past Events',
        thisMonth: 'This Month'
    };
    return labels[dateRange] || dateRange;
};

defineEmits([
    'update:searchQuery',
    'update:selectedCategory',
    'update:selectedStatus',
    'update:selectedDateRange',
    'clear-filters'
]);
</script>

<style scoped>
/* Custom select arrow styling for desktop */
@media (min-width: 1024px) {
    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
