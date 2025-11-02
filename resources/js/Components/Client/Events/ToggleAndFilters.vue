<script setup>
import { useUIStore } from '@/store/ui';
import { nextTick } from 'vue';

defineProps({
    initializeCalendar: Function,
    clearFilters: Function,
    filteredEvents: Array

})

const selectedStatus = defineModel('selectedStatus');
const searchQuery = defineModel('searchQuery');

const ui = useUIStore();
</script>

<template>
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-6">
            <!-- View Toggle -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex rounded-lg border border-gray-300 p-1">
                    <button @click="ui.toggleEventCalendarView(); nextTick(() => initializeCalendar())" :class="[
                        'px-4 py-2 text-sm rounded-md transition-colors',
                        ui.isEventCalendarView
                            ? 'bg-blue-600 text-white'
                            : 'text-gray-600 hover:text-gray-800'
                    ]">
                        Calendar View
                    </button>
                    <button @click="ui.toggleEventCalendarView" :class="[
                        'px-4 py-2 text-sm rounded-md transition-colors',
                        !ui.isEventCalendarView
                            ? 'bg-blue-600 text-white'
                            : 'text-gray-600 hover:text-gray-800'
                    ]">
                        List View
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div v-if="!ui.isEventCalendarView" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Events</label>
                    <input v-model="searchQuery" type="text" placeholder="Search events..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>



                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select v-model="selectedStatus"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="planning">Planning</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>


            </div>

            <div v-if="!ui.isEventCalendarView" class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    {{ filteredEvents.length }} event{{ filteredEvents.length !== 1 ? 's' : '' }} found
                </div>
                <button @click="clearFilters" class="text-sm text-gray-600 hover:text-gray-800">
                    Clear filters
                </button>
            </div>
        </div>
    </div>
</template>

<style></style>
