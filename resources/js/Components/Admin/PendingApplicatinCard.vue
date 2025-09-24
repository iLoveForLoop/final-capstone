<script setup>
import { ref } from 'vue'
import VendorViewModal from '@/Components/Admin/VendorViewModal.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    application: Object
})

const emit = defineEmits(['approved', 'rejected'])

// Modal state
const showViewModal = ref(false)

const approveVendor = (vendorId) => {
    if (confirm('Are you sure you want to approve this vendor?')) {
        router.put(route('admin.vendor-application.approve', vendorId), {}, {
            onSuccess: () => {
                toast.success('Vendor approved successfully');
                emit('approved', vendorId)
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
                emit('rejected', vendorId)
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

const getInitials = (name) => {
    return name.split(' ').map(n => n.charAt(0)).join('').substring(0, 2).toUpperCase();
};

// Modal handlers
const openViewModal = () => {
    showViewModal.value = true
}

const handleModalApprove = (vendorId) => {
    showViewModal.value = false
    approveVendor(vendorId)
}

const handleModalReject = (vendorId) => {
    showViewModal.value = false
    rejectVendor(vendorId)
}

</script>

<template>
    <div class="h-full flex flex-col">
        <!-- View Modal -->
        <VendorViewModal :application="application" :open="showViewModal" @update:open="showViewModal = $event"
            @approve="handleModalApprove" @reject="handleModalReject" />
        <!-- Header -->
        <div class="p-5 border-b border-gray-100">
            <div class="flex items-start gap-3 mb-4">
                <img v-if="application.avatar" :src="application.avatar" alt="Profile Photo"
                    class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-200" />
                <div v-else
                    class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                    {{ getInitials(application.full_name) }}
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 text-lg truncate">{{ application.business_name }}</h3>
                    <p class="text-sm text-gray-600 truncate">{{ application.full_name }}</p>
                    <p class="text-sm text-gray-500 truncate">{{ application.user.email }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500">{{ formatDate(application.created_at) }}</span>
                <span class="px-2 py-1 bg-yellow-50 text-yellow-700 rounded-md border border-yellow-200">
                    Pending
                </span>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 p-5 space-y-4">
            <!-- Contact Info -->
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                    </svg>
                    <span class="text-gray-900 truncate">{{ application.contact_number }}</span>
                </div>

                <div class="flex items-start gap-2 text-sm">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-gray-900 line-clamp-2">{{ application.location }}</span>
                </div>
            </div>

            <!-- Description -->
            <div v-if="application.description" class="text-sm">
                <p class="text-gray-600 line-clamp-3 leading-relaxed">{{ application.description }}</p>
            </div>

            <!-- Categories -->
            <div v-if="application.vendor_categories && application.vendor_categories.length"
                class="flex flex-wrap gap-1">
                <span v-for="category in application.vendor_categories.slice(0, 2)" :key="category"
                    class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                    {{ category }}
                </span>
                <span v-if="application.vendor_categories.length > 2"
                    class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded">
                    +{{ application.vendor_categories.length - 2 }}
                </span>
            </div>
        </div>

        <!-- Actions -->
        <div class="p-5 border-t border-gray-100 bg-gray-50">
            <div class="flex gap-2">
                <button @click="openViewModal"
                    class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    View
                </button>
                <button @click="rejectVendor(application.id)"
                    class="flex-1 px-3 py-2 text-sm font-medium text-white bg-red-600 border border-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    Reject
                </button>
                <button @click="approveVendor(application.id)"
                    class="flex-1 px-3 py-2 text-sm font-medium text-white bg-green-600 border border-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    Approve
                </button>
            </div>
        </div>


    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
