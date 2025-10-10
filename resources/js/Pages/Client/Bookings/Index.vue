<script setup>
import BookingCard from '@/Components/Client/Booking/BookingCard.vue';
import BookingFilters from '@/Components/Client/Booking/BookingFilters.vue';
import StatsSection from '@/Components/Client/Booking/StatsSection.vue';
import ClientNavbar from '@/Components/ClientNavbar.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';
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
    <ClientLayout>
        <div class="min-h-screen bg-gray-50">

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
            <StatsSection :bookingStats="bookingStats" />

            <!-- Filters Section -->
            <BookingFilters v-model:searchQuery="searchQuery" v-model:selectedCategory="selectedCategory"
                v-model:selectedStatus="selectedStatus" v-model:selectedDateRange="selectedDateRange"
                :categories="categories" :bookingStatuses="bookingStatuses" :totalBookings="filteredBookings.total"
                @clear-filters="clearFilters" />

            <!-- Bookings List -->
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div v-if="filteredBookings.data.length > 0" class="space-y-6">
                    <div v-for="booking in filteredBookings.data" :key="booking.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <BookingCard :booking="booking" />
                    </div>

                </div>

                <!-- Empty State -->
                <div v-else-if="bookings.data.length === 0" class="text-center py-16">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
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
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
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
            <!-- <div class="bg-white border-t border-gray-200">
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
        </div> -->
        </div>
    </ClientLayout>
</template>
