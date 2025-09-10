<script setup>

import BookingsChart from '@/Components/Charts/BookingsChart.vue'
import RevenueChart from '@/Components/Charts/RevenueChart.vue'
import RevenueTrendChart from '@/Components/Charts/RevenueTrendChart.vue'
import VendorBookingsTrendChart from '@/Components/Charts/VendorBookingsTrendChart.vue'
import VendorLayout from '@/Layouts/VendorLayout.vue'
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: {
        type: Object
    },
    chartData: {
        type: Object,
        required: true
    },
    upcomingEvents: {
        type: Array,
        required: true
    },
    recentBookings: {
        type: Array,
        required: true
    },
    notifications: {
        type: Array,
        required: true
    },
    unreadNotificationCount: {
        type: Number,
        default: 0
    }
})

const formatDate = (dateString) => {
    const options = { weekday: 'short', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const getStatusBadgeClass = (status) => {
    const statusClasses = {
        'completed': 'bg-green-100 text-green-800',
        'confirmed': 'bg-amber-100 text-amber-800',
        'ongoing': 'bg-amber-100 text-amber-800',
        'pending': 'bg-gray-100 text-gray-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
};

const getEventPriorityIcon = (event) => {
    if (event.is_today) return '🔴'; // Red circle for today
    if (event.is_tomorrow) return '🟡'; // Yellow circle for tomorrow
    if (event.days_until <= 7) return '🟢'; // Green circle for this week
    return '⚪'; // White circle for later
};

// Chart data from backend
const revenueData = computed(() => props.chartData.revenue)
const bookingsData = computed(() => props.chartData.bookings)

// Optional: Add loading state and error handling
const isChartDataValid = computed(() => {
    return props.chartData &&
        props.chartData.revenue &&
        Array.isArray(props.chartData.revenue.labels) &&
        Array.isArray(props.chartData.revenue.revenue) &&
        props.chartData.bookings &&
        Array.isArray(props.chartData.bookings.labels) &&
        Array.isArray(props.chartData.bookings.bookings)
})


//NOTIFICATIONS
const getNotificationIconClass = (notification) => {
    const baseClass = 'h-10 w-10 rounded-full flex items-center justify-center';
    const colorClass = notification.read ? 'bg-gray-100 text-gray-400' : `bg-${notification.color}-100 text-${notification.color}-600`;
    return `${baseClass} ${colorClass}`;
};

const getNotificationIcon = (type) => {
    const icons = {
        'calendar-plus': `<path d="M8 2v4" /><path d="M16 2v4" /><rect width="18" height="18" x="3" y="4" rx="2" /><path d="M3 10h18" /><path d="M10 16h4" /><path d="M12 14v4" />`,
        'check-circle': `<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" /><polyline points="22 4 12 14.01 9 11.01" />`,
        'x-circle': `<circle cx="12" cy="12" r="10" /><path d="m15 9-6 6" /><path d="m9 9 6 6" />`,
        'calendar-check': `<rect width="18" height="18" x="3" y="4" rx="2" /><line x1="16" x2="16" y1="2" y2="6" /><line x1="8" x2="8" y1="2" y2="6" /><line x1="3" x2="21" y1="10" y2="10" /><path d="m9 16 2 2 4-4" />`,
        'settings': `<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" /><circle cx="12" cy="12" r="3" />`,
        'credit-card': `<rect width="20" height="14" x="2" y="5" rx="2" /><line x1="2" x2="22" y1="10" y2="10" />`,
        'star': `<polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" />`,
        'alert-circle': `<circle cx="12" cy="12" r="10" /><line x1="12" x2="12" y1="8" y2="12" /><line x1="12" x2="12.01" y1="16" y2="16" />`,
        'bell': `<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" /><path d="m13.73 21a2 2 0 0 1-3.46 0" />`
    };
    return icons[type] || icons['bell'];
};

const markNotificationAsRead = async (notificationId) => {
    try {
        await router.post(route('vendor.notifications.read', { notification: notificationId }));
        // Optionally refresh or update the notification status locally
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
};

</script>

<template>
    <VendorLayout>

        <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
                    <p class="text-gray-500 text-sm mt-1">Welcome back! Here's what's happening with your business.</p>
                </div>
                <div class="text-sm text-gray-500">Last updated: {{ new Date().toLocaleDateString() }}</div>
            </div>

            <!-- Stats Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                <!-- Total Bookings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ stats.total_bookings }}</h3>
                        </div>
                        <div class="p-3 rounded-lg bg-blue-50 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-calendar-days">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                                <path d="M8 14h.01" />
                                <path d="M12 14h.01" />
                                <path d="M16 14h.01" />
                                <path d="M8 18h.01" />
                                <path d="M12 18h.01" />
                                <path d="M16 18h.01" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">+2 from last week</p>
                </div>

                <!-- Pending Bookings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Pending</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ stats.pending_bookings }}</h3>
                        </div>
                        <div class="p-3 rounded-lg bg-yellow-50 text-yellow-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-alert-circle">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" x2="12" y1="8" y2="12" />
                                <line x1="12" x2="12.01" y1="16" y2="16" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">+{{ stats.pending_bookings }} from last week</p>
                </div>

                <!-- Ongoing Bookings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Ongoing</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ stats.ongoing_bookings }}</h3>
                        </div>
                        <div class="p-3 rounded-lg bg-amber-50 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-clock">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">+1 from yesterday</p>
                </div>

                <!-- Completed Bookings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Completed</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ stats.completed_bookings }}</h3>
                        </div>
                        <div class="p-3 rounded-lg bg-green-50 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-check-circle">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">+3 from last week</p>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">₱{{ stats.total_revenue }}</h3>
                        </div>
                        <div class="p-3 rounded-lg bg-purple-50 text-purple-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-credit-card">
                                <rect width="20" height="14" x="2" y="5" rx="2" />
                                <line x1="2" x2="22" y1="10" y2="10" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">+12% from last month</p>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-5">
                    <!-- Revenue Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="text-lg font-semibold text-gray-800">Revenue Overview</h2>
                            <select
                                class="text-sm border border-gray-200 rounded-lg px-3 py-1 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option>Last 6 Months &nbsp;&nbsp;&nbsp;&nbsp;</option>
                                <option>This Year</option>
                                <option>Last Year</option>
                            </select>
                        </div>
                        <div class="h-64">
                            <RevenueChart v-if="isChartDataValid" :data="revenueData" :height="256" />
                            <div v-else class="flex items-center justify-center h-full bg-gray-50 rounded-lg">
                                <div class="text-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-line-chart mx-auto mb-2">
                                        <path d="M3 3v18h18" />
                                        <path d="m19 9-5 5-4-4-3 3" />
                                    </svg>
                                    <p>Loading revenue data...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bookings Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="text-lg font-semibold text-gray-800">Bookings Overview</h2>
                            <select
                                class="text-sm border border-gray-200 rounded-lg px-3 py-1 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option>Last 6 Months &nbsp;&nbsp;&nbsp;&nbsp;</option>
                                <option>This Year</option>
                                <option>Last Year</option>
                            </select>
                        </div>
                        <div class="h-64">
                            <BookingsChart v-if="isChartDataValid" :data="bookingsData" :height="256" />
                            <div v-else class="flex items-center justify-center h-full bg-gray-50 rounded-lg">
                                <div class="text-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-line-chart mx-auto mb-2">
                                        <path d="M3 3v18h18" />
                                        <path d="m19 9-5 5-4-4-3 3" />
                                    </svg>
                                    <p>Loading bookings data...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-5">
                    <!-- Upcoming Events -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="text-lg font-semibold text-gray-800">Upcoming Events</h2>
                            <Link :href="route('vendor.calendar.index')"
                                class="text-sm text-indigo-600 hover:underline">
                            View Calendar</Link>
                        </div>
                        <ul class="space-y-4">
                            <li v-for="event in upcomingEvents" :key="event.id" class="flex items-start">
                                <div class="flex-shrink-0 mt-1 mr-3">
                                    <div
                                        class="h-10 w-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-calendar">
                                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                            <line x1="16" x2="16" y1="2" y2="6" />
                                            <line x1="8" x2="8" y1="2" y2="6" />
                                            <line x1="3" x2="21" y1="10" y2="10" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ event.event }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDate(event.date) }} • {{ event.time }}</p>
                                    <p class="text-xs text-gray-400">{{ event.location || 'Location TBD' }}</p>
                                </div>
                                <div class="ml-2">
                                    <span v-if="event.priority === 'high'" class="text-red-500">🔴</span>
                                    <span v-else-if="event.priority === 'medium'" class="text-yellow-500">🟡</span>
                                    <span v-else class="text-green-500">🟢</span>
                                </div>
                            </li>
                            <li v-if="upcomingEvents.length === 0" class="text-center py-4 text-gray-400 text-sm">
                                No upcoming events scheduled
                            </li>
                        </ul>
                    </div>

                    <!-- Notifications -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="text-lg font-semibold text-gray-800">
                                Notifications
                                <span v-if="unreadNotificationCount > 0"
                                    class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                    {{ unreadNotificationCount }}
                                </span>
                            </h2>
                            <a href="#" class="text-sm text-indigo-600 hover:underline">View All</a>
                        </div>
                        <ul class="space-y-4">
                            <li v-for="notification in notifications" :key="notification.id"
                                class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors"
                                @click="notification.read ? null : markNotificationAsRead(notification.id)">
                                <div class="flex-shrink-0 mt-1 mr-3">
                                    <div :class="getNotificationIconClass(notification)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            v-html="getNotificationIcon(notification.icon)">
                                        </svg>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p :class="{
                                        'font-medium text-gray-900': !notification.read,
                                        'text-gray-500': notification.read,
                                    }" class="text-sm">{{ notification.text }}</p>
                                    <p class="text-xs text-gray-400">{{ notification.time }}</p>
                                </div>
                                <div v-if="!notification.read" class="ml-2 mt-1">
                                    <span class="h-2 w-2 rounded-full bg-blue-500 block"></span>
                                </div>
                            </li>
                            <li v-if="notifications.length === 0" class="text-center py-4 text-gray-400 text-sm">
                                No notifications
                            </li>
                        </ul>
                    </div>


                    <!-- Recent Bookings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="text-lg font-semibold text-gray-800">Recent Bookings</h2>
                            <a href="#" class="text-sm text-indigo-600 hover:underline">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Client</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Service</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">

                                    <tr v-for="booking in recentBookings" :key="booking.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-gray-600">
                                                            {{ booking.client.charAt(0).toUpperCase() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ booking.client }}
                                                    </div>
                                                    <div v-if="booking.time_ago" class="text-xs text-gray-500">{{
                                                        booking.time_ago }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ booking.service }}</div>
                                            <div v-if="booking.event_date" class="text-xs text-gray-500">
                                                Event: {{ formatDate(booking.event_date) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ booking.formatted_amount
                                            }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span :class="getStatusBadgeClass(booking.status)"
                                                    class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                                                    {{ booking.status }}
                                                </span>
                                                <span v-if="booking.is_new"
                                                    class="ml-2 h-2 w-2 rounded-full bg-blue-500"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-if="recentBookings.length === 0" class="text-center py-8 text-gray-400 text-sm">
                                No recent bookings found
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<style scoped>
/* Custom scrollbar for tables */
.table-scroll::-webkit-scrollbar {
    height: 6px;
}

.table-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-scroll::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.table-scroll::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Smooth hover transitions */
.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
}

/* Animation for stats cards */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stats-card {
    animation: fadeIn 0.3s ease-out forwards;
}

.stats-card:nth-child(1) {
    animation-delay: 0.1s;
}

.stats-card:nth-child(2) {
    animation-delay: 0.2s;
}

.stats-card:nth-child(3) {
    animation-delay: 0.3s;
}

.stats-card:nth-child(4) {
    animation-delay: 0.4s;
}
</style>
