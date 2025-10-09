<script setup>
defineProps({
    stats: Object
})
</script>

<template>
    <div v-if="stats" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ stats.total || 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-semibold text-yellow-600">{{ stats.pending || 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500">Confirmed</p>
                    <p class="text-2xl font-semibold text-green-600">{{ stats.confirmed || 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500">This Month</p>
                    <p class="text-2xl font-semibold text-blue-600">{{ stats.this_month || 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style></style>
