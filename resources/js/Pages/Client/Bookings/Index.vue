<script setup>
import ClientNavbar from '@/Components/ClientNavbar.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    bookings: {
        type: Object
    },
    categories: {
        type: Array
    }
})

// Mock booking statuses
const bookingStatuses = [
    { value: 'all', label: 'All Bookings', color: 'gray' },
    { value: 'confirmed', label: 'Confirmed', color: 'green' },
    { value: 'pending', label: 'Pending', color: 'yellow' },
    { value: 'completed', label: 'Completed', color: 'blue' },
    { value: 'cancelled', label: 'Cancelled', color: 'red' }
];


// Reactive filters
const searchQuery = ref('');
const selectedCategory = ref('all');
const selectedStatus = ref('all');
const selectedDateRange = ref('all');
const viewMode = ref('list');

// Computed filtered bookings
const filteredBookings = computed(() => {
    let filtered = props.bookings.data;

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(booking =>
            booking.title.toLowerCase().includes(query) ||
            booking.id.toLowerCase().includes(query) ||
            booking.provider.name.toLowerCase().includes(query) ||
            booking.location.toLowerCase().includes(query)
        );
    }

    // Category filter
    if (selectedCategory.value !== 'all') {
        filtered = filtered.filter(booking => booking.category.id == selectedCategory.value);
    }

    // Status filter
    if (selectedStatus.value !== 'all') {
        filtered = filtered.filter(booking => booking.status === selectedStatus.value);
    }

    // Date range filter
    if (selectedDateRange.value !== 'all') {
        const today = new Date();
        filtered = filtered.filter(booking => {
            const eventDate = new Date(booking.eventDate);
            switch (selectedDateRange.value) {
                case 'upcoming':
                    return eventDate >= today;
                case 'past':
                    return eventDate < today;
                case 'thisMonth':
                    return eventDate.getMonth() === today.getMonth() && eventDate.getFullYear() === today.getFullYear();
                default:
                    return true;
            }
        });
    }

    return {
        data: filtered,
        total: filtered.length
    };
});

// Booking statistics
const bookingStats = computed(() => {
    const stats = {
        total: props.bookings.data.length,
        confirmed: 0,
        pending: 0,
        completed: 0,
        cancelled: 0,
        totalSpent: 0,
        totalPaid: 0,
        totalBalance: 0
    };

    props.bookings.data.forEach(booking => {
        stats[booking.status]++;

        const price = Number(booking.raw_amount) || 0;
        const amountPaid = Number(booking.amountPaid) || 0;
        const balanceAmount = Number(booking.balanceAmount) || 0;

        if (booking.status === 'completed') stats.totalSpent += price;

        stats.totalPaid += amountPaid;
        stats.totalBalance += balanceAmount;
    });

    return stats;
});


const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = 'all';
    selectedStatus.value = 'all';
    selectedDateRange.value = 'all';
};

// Booking actions
const cancelBooking = (bookingId) => {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        const booking = props.bookings.data.find(b => b.id === bookingId);
        if (booking) {
            booking.status = 'cancelled';
            alert('Booking cancelled successfully. You will receive a confirmation email shortly.');
        }
    }
};

const rescheduleBooking = (bookingId) => {
    alert(`Reschedule booking ${bookingId} - This would open a date picker modal.`);
};

const contactProvider = (booking) => {
    alert(`Contact ${booking.provider.name}:\nPhone: ${booking.provider.phone}\nEmail: ${booking.provider.email}`);
};

const downloadInvoice = (bookingId) => {
    alert(`Downloading invoice for booking ${bookingId}...`);
};

// Helper functions
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getStatusColor = (status) => {
    const colors = {
        confirmed: 'bg-green-100 text-green-800',
        pending: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-blue-100 text-blue-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getPaymentStatusColor = (status) => {
    const colors = {
        paid: 'text-green-600',
        partial: 'text-yellow-600',
        unpaid: 'text-red-600',
        refunded: 'text-blue-600'
    };
    return colors[status] || 'text-gray-600';
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <ClientNavbar />

        <!-- Header Section -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Bookings</h1>
                        <p class="text-gray-600">Manage your service bookings and reservations</p>
                    </div>
                    <div>
                        <button
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            New Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                            <p class="text-2xl font-bold text-gray-900">{{ bookingStats.total }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Confirmed</p>
                            <p class="text-2xl font-bold text-gray-900">{{ bookingStats.confirmed }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-gray-900">{{ bookingStats.pending }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Estimated Total Spent</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatPrice(bookingStats.totalSpent) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Bookings</label>
                        <input v-model="searchQuery" type="text"
                            placeholder="Search by booking ID, service, or provider..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select v-model="selectedCategory"
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
                        <select v-model="selectedStatus"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option v-for="status in bookingStatuses" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Date Range Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <select v-model="selectedDateRange"
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
                        {{ filteredBookings.total }} booking{{ filteredBookings.total !== 1 ? 's' : '' }} found
                    </div>
                    <div class="flex items-center space-x-4">
                        <button @click="clearFilters" class="text-sm text-gray-600 hover:text-gray-800">
                            Clear filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div v-if="filteredBookings.data.length > 0" class="space-y-6">
                <div v-for="booking in filteredBookings.data" :key="booking.id"
                    class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <!-- Booking Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-start space-x-4">
                                <img :src="booking.service_image" :alt="booking.title"
                                    class="w-20 h-20 rounded-lg object-cover">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="text-sm font-mono text-gray-500">{{ booking.id }}</span>
                                        <span
                                            :class="['px-2 py-1 text-xs rounded-full', getStatusColor(booking.status)]">
                                            {{ booking.status.charAt(0).toUpperCase() + booking.status.slice(1) }}
                                        </span>
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                            {{ booking.category.name }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ booking.event_name }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">{{ booking.description }}</p>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <span class="font-medium">{{ booking.vendor.business_name }}</span>
                                        <span class="mx-2">•</span>
                                        <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span v-if="booking.vendor_rating">Rating: {{ booking.vendor_rating }}/5</span>
                                        <span v-else>No ratings yet</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900">{{ formatPrice(booking.raw_amount) }}
                                </div>
                                <!-- <div class="text-sm mt-1" :class="getPaymentStatusColor(booking.paymentStatus)">
                                    {{ booking.paymentStatus.charAt(0).toUpperCase() + booking.paymentStatus.slice(1) }}
                                </div> -->
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 bg-gray-50 rounded-lg p-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">EVENT DATE</p>
                                <p class="text-sm font-medium text-gray-900">{{ formatDate(booking.event_date) }}</p>
                                <p class="text-xs text-gray-600">{{ booking.eventTime }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-1">LOCATION</p>
                                <p class="text-sm font-medium text-gray-900">{{ booking.event_location }}</p>
                            </div>
                            <!-- <div>
                    <p class="text-xs font-medium text-gray-500 mb-1">PAYMENT</p>
                    <p class="text-sm font-medium text-gray-900">Paid: {{ formatPrice(booking.amountPaid) }}
                    </p>
                    <p class="text-xs text-gray-600" v-if="booking.balanceAmount > 0">
                        Balance: {{ formatPrice(booking.balanceAmount) }}
                    </p>
                </div> -->
                        </div>

                        <!-- Notes -->
                        <div v-if="booking.notes" class="mb-4">
                            <p class="text-xs font-medium text-gray-500 mb-1">NOTES</p>
                            <p class="text-sm text-gray-700 bg-gray-50 rounded p-3">{{ booking.notes }}</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="flex space-x-3">
                                <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                    Contact Provider
                                </button>
                                <!-- <button @click="downloadInvoice(booking.id)" class="text-sm text-gray-600 hover:text-gray-700">
                        Download Invoice
                    </button> -->
                            </div>
                            <div class="flex space-x-2">
                                <!-- <button v-if="booking.status === 'confirmed' || booking.status === 'pending'"
                        @click="rescheduleBooking(booking.id)"
                        class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                        Reschedule
                    </button> -->
                                <button v-if="booking.status === 'confirmed' || booking.status === 'pending'"
                                    class="px-4 py-2 text-sm border border-red-300 text-red-700 rounded hover:bg-red-50 transition-colors">
                                    Cancel
                                </button>
                                <button v-if="booking.status === 'completed'"
                                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                    Leave Review
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Empty State -->
            <div v-else-if="bookings.data.length === 0" class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                <h3 class="text-xl font-medium text-gray-900 mb-3">No bookings yet</h3>
                <p class="text-gray-600 mb-6 max-w-sm mx-auto">
                    You haven't made any bookings yet. Start browsing our services to make your first reservation.
                </p>
                <button
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Browse Services
                </button>
            </div>

            <!-- No Results State -->
            <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your search or filter criteria.</p>
                <button @click="clearFilters"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                    Clear filters
                </button>
            </div>
        </div>

        <!-- Quick Actions Footer -->
        <div class="bg-white border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Need help with your bookings? <a href="#" class="text-blue-600 hover:text-blue-700">Contact
                            Support</a>
                    </div>
                    <div class="flex space-x-3">
                        <button
                            class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 transition-colors text-sm">
                            Export Bookings
                        </button>
                        <button
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                            Browse Services
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
