<script setup>
import TestLayout from '@/Layouts/TestLayout.vue'
import { ref, computed } from 'vue'
import { Users, Store, FileClock, Calendar } from 'lucide-vue-next'
import BookingTrendsChart from '@/Components/Charts/BookingTrendsChart.vue'

const props = defineProps({
    stats: {
        type: Object,
    },
})

const statCards = computed(() => [
    { title: 'Total Users', value: props.stats?.total_users ?? 0, icon: Users },
    { title: 'Total Vendors', value: props.stats?.total_vendors ?? 0, icon: Store },
    { title: 'Pending Applications', value: props.stats?.pending_applications ?? 0, icon: FileClock },
    { title: 'Total Bookings', value: props.stats?.total_bookings ?? 0, icon: Calendar }
])

const pendingVendors = ref([
    { id: 1, name: 'Grace Catering', category: 'Catering' },
    { id: 2, name: 'Flash Photography', category: 'Photography' }
])


</script>

<template>
    <TestLayout>
        <div class="p-6 space-y-6">
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="card in statCards" :key="card.title"
                    class="bg-white rounded-2xl shadow p-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-500">{{ card.title }}</h2>
                        <p class="text-2xl font-bold text-gray-900">{{ card.value }}</p>
                    </div>
                    <div class="text-3xl text-primary">
                        <component :is="card.icon" />
                    </div>
                </div>
            </div>

            <!-- Charts and Widgets -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Booking Trend Chart -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow p-4">
                    <h3 class="text-lg font-semibold mb-2">Booking Trends</h3>
                    <!-- Chart Placeholder -->
                    <div class="h-64 flex items-center justify-center text-gray-400">
                        <BookingTrendsChart :labels="stats.labels" :data="stats.data" chart-label="Monthly Bookings" />
                    </div>
                </div>

                <!-- Pending Vendor Applications -->
                <div class="bg-white rounded-2xl shadow p-4">
                    <h3 class="text-lg font-semibold mb-4">Pending Vendor Applications</h3>
                    <ul class="divide-y divide-gray-200">
                        <li v-for="vendor in stats.pending_vendors" :key="vendor.id" class="py-2">
                            <div class="flex justify-between items-center">
                                <div>

                                    <p class="font-medium">{{ vendor.business_name }}</p>
                                    <p class="text-sm" v-for="category in vendor.service_categories" :key="category.id">
                                        {{ category.name }}
                                    </p>
                                </div>
                                <button class="text-primary hover:underline text-sm">View</button>
                            </div>
                        </li>
                        <li v-if="pendingVendors.length === 0" class="text-gray-400 text-sm">No pending applications
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Top Vendors Table -->
            <div class="bg-white rounded-2xl shadow p-4">
                <h3 class="text-lg font-semibold mb-4">Top Rated Vendors</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="px-4 py-2 text-left">Vendor</th>
                                <th class="px-4 py-2 text-left">Category</th>
                                <th class="px-4 py-2 text-left">Rating</th>
                                <th class="px-4 py-2 text-left">Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="vendor in stats.top_vendors" :key="vendor.id" class="border-b">
                                <td class="px-4 py-2">
                                    {{ vendor.user?.name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-2">
                                    <ul class="list-disc ml-4">
                                        <li v-for="category in vendor.service_categories" :key="category.id">
                                            {{ category.name }}
                                        </li>
                                    </ul>
                                </td>
                                <td class="px-4 py-2">⭐ {{ vendor.reviews_avg_rating?.toFixed(1) ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ vendor.bookings_count }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </TestLayout>

</template>



<style scoped></style>
