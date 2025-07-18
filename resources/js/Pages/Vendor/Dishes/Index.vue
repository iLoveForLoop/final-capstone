<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import DishCreateModal from '@/Components/Vendor/DishCreateModal.vue';
import DishEditModal from '@/Components/Vendor/DishEditModal.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';

const toast = useToast();

const dishCreateModal = ref(null);
const dishEditModal = ref(null);

const props = defineProps({
    dishes: {
        type: Object,
        default: () => ({ data: [] })
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            availability: 'all'
        })
    }
});

const availabilityOptions = [
    { value: 'all', label: 'All Dishes' },
    { value: 'available', label: 'Available Only' },
    { value: 'unavailable', label: 'Unavailable Only' }
];

const searchQuery = ref(props.filters.search || '');
const availabilityFilter = ref(props.filters.availability || 'all');

const applyFilters = () => {
    router.get(route('vendor.dishes.index'), {
        search: searchQuery.value,
        availability: availabilityFilter.value
    }, {
        preserveState: true,
        replace: true
    });
};

const resetFilters = () => {
    searchQuery.value = '';
    availabilityFilter.value = 'all';
    applyFilters();
};

const toggleAvailability = (dish) => {
    router.patch(route('vendor.dishes.toggle-availability', dish.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(`Dish ${dish.is_available ? 'deactivated' : 'activated'} successfully`);
        },
        onError: () => {
            toast.error('Failed to update dish availability');
        }
    });
};

const deleteDish = (dishId) => {
    if (confirm('Are you sure you want to delete this dish? This action cannot be undone.')) {
        router.delete(route('vendor.dishes.destroy', dishId), {
            onSuccess: () => {
                toast.success('Dish deleted successfully');
            },
            onError: () => {
                toast.error('Failed to delete dish');
            }
        });
    }
};

const editDish = (dish) => {
    dishEditModal.value.show(dish);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(price);
};
</script>

<template>
    <VendorLayout>
        <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Dishes Management</h1>
                    <p class="text-gray-500 text-sm mt-1">Manage your catering service dishes</p>
                </div>
                <button @click="dishCreateModal.show()"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Add New Dish
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="flex flex-col md:flex-row gap-4">
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
                                placeholder="Search dishes..."
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

            <!-- Dish Cards Grid -->
            <div v-if="dishes.data.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="dish in dishes.data" :key="dish.id"
                    class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition">
                    <!-- Dish Image -->
                    <div class="relative h-48 w-full overflow-hidden">
                        <img :src="dish.image_url || '/images/default-dish.jpg'" :alt="dish.name"
                            class="w-full h-full object-cover">
                        <!-- Availability Badge -->
                        <div class="absolute top-2 right-2">
                            <span :class="{
                                'bg-green-100 text-green-800': dish.is_available,
                                'bg-red-100 text-red-800': !dish.is_available
                            }" class="px-2 py-1 text-xs font-medium rounded-full">
                                {{ dish.is_available ? 'Available' : 'Unavailable' }}
                            </span>
                        </div>
                    </div>

                    <!-- Dish Details -->
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-semibold text-gray-800">{{ dish.name }}</h3>
                            <span class="text-lg font-bold text-indigo-600">{{ formatPrice(dish.price) }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ dish.description }}</p>

                        <!-- Categories -->
                        <div class="mt-3 flex flex-wrap gap-1">
                            <span v-for="category in dish.categories" :key="category.id"
                                class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">
                                {{ category.name }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="mt-4 flex justify-between items-center border-t pt-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" :checked="dish.is_available" @change="toggleAvailability(dish)"
                                    class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                                </div>
                            </label>
                            <div class="flex gap-2">
                                <button @click="editDish(dish)"
                                    class="text-gray-500 hover:text-indigo-600 p-1 rounded-full hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>
                                <button @click="deleteDish(dish.id)"
                                    class="text-gray-500 hover:text-red-600 p-1 rounded-full hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="mx-auto max-w-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 10h18M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No dishes found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria</p>
                    <div class="mt-6">
                        <button @click="resetFilters"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                            Reset Filters
                        </button>
                        <button @click="dishCreateModal.show()"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Add Dish
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination v-if="dishes.data.length > 0" :links="dishes.links" class="mt-6" />

            <!-- Create Modal -->
            <DishCreateModal ref="dishCreateModal" />
            <!-- Edit Modal -->
            <DishEditModal ref="dishEditModal" />
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
