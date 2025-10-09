<script setup>
import { Calendar, ChevronDown, Search, Filter } from 'lucide-vue-next';

defineProps({
    statusOptions: Array,
    dateRangeOptions: Array,
    sortOptions: Array
})

const searchQuery = defineModel('searchQuery')
const selectedStatus = defineModel('selectedStatus')
const selectedDateRange = defineModel('selectedDateRange')
const selectedSort = defineModel('selectedSort')
</script>

<template>
    <div class="mb-6 bg-white rounded-xl shadow-xs border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <Search class="h-5 w-5 text-gray-400" />
                </div>
                <input v-model="searchQuery" type="text" placeholder="Search bookings..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            </div>

            <!-- Status Filter -->
            <div>
                <label class="sr-only">Status</label>
                <div class="relative">
                    <select v-model="selectedStatus"
                        class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg">
                        <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <ChevronDown class="h-4 w-4 text-gray-400" />
                    </div>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div>
                <label class="sr-only">Date Range</label>
                <div class="relative">
                    <select v-model="selectedDateRange"
                        class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg">
                        <option v-for="option in dateRangeOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <Calendar class="h-4 w-4 text-gray-400" />
                    </div>
                </div>
            </div>

            <!-- Sort -->
            <div>
                <label class="sr-only">Sort By</label>
                <div class="relative">
                    <select v-model="selectedSort"
                        class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg">
                        <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <Filter class="h-4 w-4 text-gray-400" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style></style>
