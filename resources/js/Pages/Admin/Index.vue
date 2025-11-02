<script setup>
import TestLayout from '@/Layouts/TestLayout.vue'
import { ref, computed } from 'vue'
import { Users, Store, FileClock, Calendar, ChevronRight } from 'lucide-vue-next'
import BookingTrendsChart from '@/Components/Charts/BookingTrendsChart.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    stats: {
        type: Object,
    },
})

const statCards = computed(() => [
    {
        title: 'Total Users',
        value: props.stats?.total_users ?? 0,
        icon: Users,
        trend: props.stats?.user_growth ?? 0,
        color: 'bg-blue-50 text-blue-600'
    },
    {
        title: 'Total Vendors',
        value: props.stats?.total_vendors ?? 0,
        icon: Store,
        trend: props.stats?.vendor_growth ?? 0,
        color: 'bg-purple-50 text-purple-600'
    },
    {
        title: 'Pending Applications',
        value: props.stats?.pending_applications ?? 0,
        icon: FileClock,
        color: 'bg-amber-50 text-amber-600'
    },
    {
        title: 'Total Bookings',
        value: props.stats?.total_bookings ?? 0,
        icon: Calendar,
        trend: props.stats?.booking_growth ?? 0,
        color: 'bg-green-50 text-green-600'
    }
])

const pendingVendors = ref([
    { id: 1, name: 'Grace Catering', category: 'Catering' },
    { id: 2, name: 'Flash Photography', category: 'Photography' }
])
</script>

<template>
    <TestLayout>
        <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
                <div class="text-sm text-gray-500">Last updated: {{ new Date().toLocaleDateString() }}</div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="card in statCards" :key="card.title"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">{{ card.title }}</p>
                            <h3 class="text-2xl font-bold text-gray-800">{{ card.value }}</h3>
                            <p v-if="card.trend !== undefined" class="text-xs mt-2"
                                :class="card.trend >= 0 ? 'text-green-500' : 'text-red-500'">
                                {{ card.trend >= 0 ? '↑' : '↓' }} {{ Math.abs(card.trend) }}% from last month
                            </p>
                        </div>
                        <div class="h-12 w-12 rounded-lg flex items-center justify-center" :class="card.color">
                            <component :is="card.icon" class="h-5 w-5" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Widgets -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Booking Trend Chart -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Booking Trends</h3>
                        <div class="flex space-x-2">
                            <button class="text-xs px-3 py-1 bg-gray-100 rounded-lg">Monthly</button>
                            <button class="text-xs px-3 py-1 text-gray-500 rounded-lg hover:bg-gray-100">Yearly</button>
                        </div>
                    </div>
                    <div class="h-72">
                        <BookingTrendsChart :labels="stats.labels" :data="stats.data" chart-label="Monthly Bookings" />
                    </div>
                </div>

                <!-- Pending Vendor Applications -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Pending Applications</h3>
                        <button class="text-sm text-primary-600 hover:text-primary-800 font-medium">View All</button>
                    </div>
                    <ul class="space-y-4">
                        <li v-for="vendor in stats.pending_vendors" :key="vendor.id"
                            class="p-3 hover:bg-gray-50 rounded-lg transition-colors">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-800">{{ vendor.business_name }}</p>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        <span v-for="category in vendor.service_categories" :key="category.id"
                                            class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">
                                            {{ category.name }}
                                        </span>
                                    </div>
                                </div>
                                <button @click="router.visit(route('admin.vendor-application.index'))"
                                    class="text-gray-400 hover:text-primary-600 transition-colors">
                                    <ChevronRight class="h-5 w-5" />
                                </button>
                            </div>
                        </li>
                        <li v-if="pendingVendors.length === 0" class="text-center py-8 text-gray-400">
                            No pending applications
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Top Vendors Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Top Rated Vendors</h3>
                        <button class="text-sm text-primary-600 hover:text-primary-800 font-medium">View All</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vendor</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rating</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bookings</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="vendor in stats.top_vendors" :key="vendor.id"
                                class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                            {{ vendor.user?.name?.charAt(0) ?? 'N/A' }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ vendor.user?.name ?? 'N/A'
                                            }}</div>
                                            <div class="text-sm text-gray-500">{{ vendor.business_name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="category in vendor.service_categories" :key="category.id"
                                            class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">
                                            {{ category.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-yellow-500">★</span>
                                        <!-- <span class="ml-1 text-sm font-medium text-gray-900">
                                            {{ vendor.reviews_avg_rating?.toFixed(1) ?? 'N/A' }}
                                        </span> -->
                                        <span class="ml-1 text-xs text-gray-500">
                                            ({{ vendor.reviews_count ?? 0 }} reviews)
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ vendor.bookings_count }}
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </TestLayout>
</template>

<style scoped>
/* Custom scrollbar for tables */
::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
