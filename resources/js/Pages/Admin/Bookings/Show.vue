<script setup>
import TestLayout from '@/Layouts/TestLayout.vue';
import { format } from 'date-fns';

const props = defineProps({
    booking: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return format(new Date(dateString), 'PPPp');
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(amount);
};

console.log('Booking', props.booking)
</script>

<template>
    <TestLayout>
        <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">
                    Booking #{{ booking.id }}
                </h1>
                <span :class="{
                    'bg-yellow-100 text-yellow-800': booking.status === 'pending',
                    'bg-green-100 text-green-800': booking.status === 'confirmed',
                    'bg-blue-100 text-blue-800': booking.status === 'completed',
                    'bg-red-100 text-red-800': booking.status === 'cancelled'
                }" class="px-3 py-1 text-sm font-semibold rounded-full capitalize">
                    {{ booking.status }}
                </span>
            </div>

            <!-- Booking Summary Card -->
            <div class="bg-white p-6 rounded-lg shadow-sm space-y-4">
                <h2 class="font-semibold text-gray-700 text-lg border-b pb-2">Booking Summary</h2>

                <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div>
                        <p><strong>Booking ID:</strong> #{{ booking.id }}</p>
                        <p><strong>Booked At:</strong> {{ formatDate(booking.created_at) }}</p>
                        <p><strong>Event Date:</strong> {{ formatDate(booking.event.event_date) }}</p>
                        <p><strong>Event Name:</strong> {{ booking.event.name }}</p>
                        <!-- <p><strong>Total Amount:</strong> {{ formatCurrency(booking.service.price) }}</p> -->
                    </div>
                    <div>
                        <!-- <p><strong>Payment Method:</strong> {{ booking.payment_method ?? 'Not specified' }}</p>
                        <p><strong>Payment Status:</strong>
                            <span class="capitalize">{{ booking.payment_status ?? 'Unpaid' }}</span>
                        </p>
                        <p><strong>Notes:</strong> {{ booking.notes ?? 'No additional notes.' }}</p> -->
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="font-semibold text-gray-700 text-lg border-b pb-2">Customer Information</h2>
                <div class="mt-3 text-sm text-gray-600">
                    <p><strong>Name:</strong> {{ booking.user.name }}</p>
                    <p><strong>Email:</strong> {{ booking.user.email }}</p>
                    <p><strong>Phone:</strong> {{ booking.user.phone ?? 'Not provided' }}</p>
                </div>
            </div>

            <!-- Service Info -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="font-semibold text-gray-700 text-lg border-b pb-2">Service Details</h2>
                <div class="mt-3 text-sm text-gray-600">
                    <p><strong>Service:</strong> {{ booking.service.name }}</p>
                    <p><strong>Vendor:</strong> {{ booking.service.vendor.business_name }}</p>
                    <p><strong>Category:</strong> {{ booking.service.category.name ?? 'N/A' }}</p>
                    <p><strong>Price:</strong> {{ formatCurrency(booking.service.price) }}</p>
                </div>
            </div>

            <!-- Back Button -->
            <div class="flex justify-end">
                <button @click="$inertia.visit(route('admin.bookings.index'))"
                    class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                    Back to Bookings
                </button>
            </div>
        </div>
    </TestLayout>
</template>
