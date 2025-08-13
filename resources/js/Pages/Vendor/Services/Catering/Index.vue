<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import ServiceCreateModal from '@/Components/Vendor/ServiceCreateModal.vue';
// import ServiceEditModal from '@/Components/Vendor/ServiceEditModal.vue';
// import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';
import CateringServiceCard from '@/Components/Vendor/Catering/CateringServiceCard.vue';
import PhotographyServiceCard from '@/Components/Vendor/Photograpy/PhotographyServiceCard.vue';

const toast = useToast();

const serviceCreateModal = ref(null);
const serviceEditModal = ref(null);

const props = defineProps({
    services: {
        type: Object,
        default: () => ({ data: [] })
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            availability: 'all'
        })
    },
    dishes: {
        type: Object,
    },
    service_categories: {
        type: Array
    }
});

const availabilityOptions = [
    { value: 'all', label: 'All Services' },
    { value: 'available', label: 'Available Only' },
    { value: 'unavailable', label: 'Unavailable Only' }
];

const searchQuery = ref(props.filters.search || '');
const availabilityFilter = ref(props.filters.availability || 'all');
const categoryFilter = ref('all');

const applyFilters = () => {
    router.get(route('vendor.services.index'), {
        search: searchQuery.value,
        availability: availabilityFilter.value,
        category: categoryFilter.value
    }, {
        preserveState: true,
        replace: true
    });
};

const resetFilters = () => {
    searchQuery.value = '';
    availabilityFilter.value = 'all';
    categoryFilter.value = 'all';
    applyFilters();
};



const editService = (service) => {
    serviceEditModal.value.show(service);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

</script>

<template>
    <VendorLayout>
        <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Catering Services Management</h1>
                    <p class="text-gray-500 text-sm mt-1">Manage your catering services</p>
                </div>
                <button @click="serviceCreateModal.show()"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Add New Service
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                        <select v-model="categoryFilter" @change="applyFilters"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Availability</label>
                        <select v-model="availabilityFilter" @change="applyFilters"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option v-for="option in availabilityOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <div class="relative">
                            <input v-model="searchQuery" @input="applyFilters" type="text"
                                placeholder="Search services..."
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 pl-9 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button @click="resetFilters"
                            class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center gap-1 h-[36px]">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Services Cards Grid -->
            <div v-if="services.data?.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="service in services.data" :key="service.id"
                    class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition">


                    <CateringServiceCard :service="service" v-if="service.category.name === 'Catering'" />

                    <PhotographyServiceCard :service="service" v-if="service.category.name === 'Photography'" />
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="mx-auto max-w-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No services found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria</p>
                    <div class="mt-6">
                        <button @click="resetFilters"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                            Reset Filters
                        </button>
                        <button @click="serviceCreateModal.show()"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Add Service
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <!-- <Pagination v-if="services.data.length > 0" :links="services.links" class="mt-6" /> -->

            <!-- Create Modal -->
            <ServiceCreateModal ref="serviceCreateModal" :dishes="dishes" :categories="service_categories" />
            <!-- Edit Modal -->
            <!-- <ServiceEditModal ref="serviceEditModal" :categories="categories" /> -->
        </div>
    </VendorLayout>
</template>

<style scoped>
/* Smooth transitions */
button,
.toggle-switch {
    transition: all 0.15s ease;
}

/* Line clamp for description */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
