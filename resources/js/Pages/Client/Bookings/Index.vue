<script setup>
import BookingCard from '@/Components/Client/Booking/BookingCard.vue';
import BookingFilters from '@/Components/Client/Booking/BookingFilters.vue';
import QuickBookingStepperModal from '@/Components/QuickBookingStepperModal.vue';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { Calendar, Plus, BarChart3 } from 'lucide-vue-next';

const props = defineProps({
    bookings: {
        type: Object
    },
    categories: {
        type: Array
    },
    filters: Object,
    booking_stats: {
        type: Object,
        default: () => ({
            total: 0,
            confirmed: 0,
            pending: 0,
            completed: 0,
            cancelled: 0,
            total_spent: 0
        })
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

// Reactive filters - these will trigger backend filtering via API calls
const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || 'all');
const selectedStatus = ref(props.filters.status || 'all');
const selectedDateRange = ref(props.filters.date_range || 'all');
const showFilters = ref(false);

// Watch filters and make API calls to backend for filtering
watch([searchQuery, selectedCategory, selectedStatus, selectedDateRange], debounce(() => {
    router.get(route('client.bookings.index'), {
        search: searchQuery.value,
        category: selectedCategory.value,
        status: selectedStatus.value,
        date_range: selectedDateRange.value,
    }, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}, 500));

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = 'all';
    selectedStatus.value = 'all';
    selectedDateRange.value = 'all';
    showFilters.value = false;
    router.get(route('client.bookings.index'));
};

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

const eventModal = ref(null);
</script>

<template>
    <ClientLayout>
        <QuickBookingStepperModal ref="eventModal" :categories="categories" />
        <div class="min-h-screen bg-gray-50/30">

            <!-- Header Section -->
            <div class="bg-white border-b border-gray-200/60">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <Calendar class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" />
                            </div>
                            <div>
                                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">My Bookings</h1>
                                <p class="text-gray-600 text-xs sm:text-sm">Manage your service bookings and
                                    reservations</p>
                            </div>
                        </div>
                        <button @click="eventModal?.open()"
                            class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium flex items-center justify-center space-x-2 shadow-sm hover:shadow-md">
                            <Plus class="w-4 h-4" />
                            <span>New Booking</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Section - Uses backend stats that don't change with filters -->
            <div class="bg-white border-b border-gray-200/60">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                        <div class="bg-gray-50 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">{{ booking_stats.total
                                }}
                            </div>
                            <div class="text-xs sm:text-sm text-gray-600">Total</div>
                        </div>
                        <div class="bg-green-50 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold text-green-600">{{
                                booking_stats.confirmed }}</div>
                            <div class="text-xs sm:text-sm text-green-600">Confirmed</div>
                        </div>
                        <div class="bg-amber-50 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold text-amber-600">{{
                                booking_stats.pending
                                }}</div>
                            <div class="text-xs sm:text-sm text-amber-600">Pending</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold text-blue-600">{{
                                booking_stats.completed }}</div>
                            <div class="text-xs sm:text-sm text-blue-600">Completed</div>
                        </div>
                        <div class="bg-red-50 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold text-red-600">{{
                                booking_stats.cancelled
                                }}</div>
                            <div class="text-xs sm:text-sm text-red-600">Cancelled</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg sm:rounded-xl p-3 sm:p-4 text-center">
                            <div class="text-lg sm:text-xl lg:text-2xl font-bold text-purple-600">
                                {{ new Intl.NumberFormat('en-PH', {
                                    style: 'currency', currency: 'PHP'
                                }).format(booking_stats.total_spent) }}
                            </div>
                            <div class="text-xs sm:text-sm text-purple-600">Est. Total Spent</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section - These trigger API calls to backend -->
            <BookingFilters v-model:searchQuery="searchQuery" v-model:selectedCategory="selectedCategory"
                v-model:selectedStatus="selectedStatus" v-model:selectedDateRange="selectedDateRange"
                :categories="categories" :bookingStatuses="bookingStatuses" :totalBookings="bookings.total"
                :showFilters="showFilters" @clear-filters="clearFilters" @toggle-filters="toggleFilters" />

            <!-- Bookings List - Uses backend-filtered data directly -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <div v-if="bookings.data.length > 0" class="space-y-3 sm:space-y-4">
                    <div v-for="booking in bookings.data" :key="booking.id"
                        class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200/60 hover:shadow-md transition-all duration-200">
                        <BookingCard :booking="booking" />
                        {{ console.log('BOOKING DATA: ', booking)
                        }}
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="bookings.data.length === 0"
                    class="text-center py-12 sm:py-16 bg-white rounded-lg sm:rounded-xl border border-gray-200/60">
                    <div class="max-w-sm mx-auto px-4">
                        <Calendar class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-gray-400 mb-4 sm:mb-6" />
                        <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-2 sm:mb-3">No bookings yet</h3>
                        <p class="text-gray-600 text-sm sm:text-base mb-4 sm:mb-6">
                            You haven't made any bookings yet. Start browsing our services to make your first
                            reservation.
                        </p>
                        <button @click="eventModal?.open()"
                            class="w-full sm:w-auto bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Browse Services
                        </button>
                    </div>
                </div>

                <!-- No Results State -->
                <div v-else
                    class="text-center py-8 sm:py-12 bg-white rounded-lg sm:rounded-xl border border-gray-200/60">
                    <div class="max-w-sm mx-auto px-4">
                        <BarChart3 class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400 mb-3 sm:mb-4" />
                        <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No bookings found</h3>
                        <p class="text-gray-600 text-sm sm:text-base mb-3 sm:mb-4">Try adjusting your search or filter
                            criteria.</p>
                        <button @click="clearFilters"
                            class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Clear filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
