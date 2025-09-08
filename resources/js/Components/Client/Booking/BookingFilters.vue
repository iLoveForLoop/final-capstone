<template>
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Bookings</label>
                    <input :value="searchQuery" @input="$emit('update:searchQuery', $event.target.value)" type="text"
                        placeholder="Search by booking ID, service, or provider..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select :value="selectedCategory" @change="$emit('update:selectedCategory', $event.target.value)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
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
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option v-for="status in bookingStatuses" :key="status.value" :value="status.value">
                            {{ status.label }}
                        </option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <select :value="selectedDateRange" @change="$emit('update:selectedDateRange', $event.target.value)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Dates</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="past">Past Events</option>
                        <option value="thisMonth">This Month</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    {{ totalBookings }} booking{{ totalBookings !== 1 ? 's' : '' }} found
                </div>
                <div class="flex items-center space-x-4">
                    <button @click="$emit('clear-filters')" class="text-sm text-gray-600 hover:text-gray-800">
                        Clear filters
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    searchQuery: String,
    selectedCategory: String,
    selectedStatus: String,
    selectedDateRange: String,
    categories: Array,
    bookingStatuses: Array,
    totalBookings: Number
})

defineEmits([
    'update:searchQuery',
    'update:selectedCategory',
    'update:selectedStatus',
    'update:selectedDateRange',
    'clear-filters'
])
</script>
