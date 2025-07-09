<script setup>
import TestLayout from '@/Layouts/TestLayout.vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();

defineProps({
    pending_applications: {
        type: Array,
        default: () => []
    }
});

const approveVendor = (vendorId) => {
    if (confirm('Are you sure you want to approve this vendor?')) {
        router.put(route('admin.vendor-application.approve', vendorId), {}, {
            onSuccess: () => {
                toast.success('Vendor approved successfully');
            },
            onError: () => {
                toast.error('Failed to approve vendor');
            }
        });
    }
};

const rejectVendor = (vendorId) => {
    if (confirm('Are you sure you want to reject this vendor?')) {
        router.delete(route('admin.vendor-application.reject', vendorId), {
            onSuccess: () => {
                toast.success('Vendor rejected successfully');
            },
            onError: () => {
                toast.error('Failed to reject vendor');
            }
        });
    }
};

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
    <TestLayout>
        <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Vendor Applications</h1>
                    <p class="text-gray-500 text-sm mt-1">Review and approve new vendor applications</p>
                </div>
                <div class="text-sm text-gray-500">
                    {{ pending_applications.length }} pending applications
                </div>
            </div>

            <!-- Application Cards -->
            <div v-if="pending_applications.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="application in pending_applications" :key="application.id"
                    class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-shadow">
                    <!-- Header with Avatar -->
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 p-4 flex items-center gap-4">
                        <div
                            class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-medium text-lg">
                            {{ application.full_name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800">{{ application.business_name }}</h3>
                            <p class="text-xs text-gray-500">{{ application.user.email }}</p>
                        </div>
                    </div>

                    <!-- Application Details -->
                    <div class="p-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Contact</p>
                                <p class="font-medium">{{ application.contact_number }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Location</p>
                                <p class="font-medium">{{ application.location }}</p>
                            </div>
                        </div>

                        <div v-if="application.description">
                            <p class="text-gray-500 text-sm mb-1">Business Description</p>
                            <p class="text-sm text-gray-700 line-clamp-3">{{ application.description }}</p>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <p>Applied {{ formatDate(application.created_at) }}</p>
                            <p class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">Pending</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-gray-50 px-4 py-3 flex justify-end gap-2 border-t border-gray-100">
                        <button @click="approveVendor(application.id)"
                            class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            Approve
                        </button>
                        <button @click="rejectVendor(application.id)"
                            class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            Reject
                        </button>
                        <button
                            class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            View
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="mx-auto max-w-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No pending applications</h3>
                    <p class="mt-2 text-sm text-gray-500">All vendor applications have been processed.</p>
                </div>
            </div>
        </div>
    </TestLayout>
</template>

<style scoped>
/* Smooth transitions */
button {
    transition: all 0.15s ease;
}

/* Line clamp for description */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
