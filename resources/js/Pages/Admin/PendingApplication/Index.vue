<script setup>
import PendingApplicationCard from '@/Components/Admin/PendingApplicationCard.vue';
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
                    <PendingApplicationCard :application="application" />
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
