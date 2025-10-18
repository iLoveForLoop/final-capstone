<script setup>
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3';
import EditCateringServiceForm from './EditCateringServiceForm.vue';

const props = defineProps({
    service: {
        type: Object
    },
    categories: {
        type: Array,
        default: () => []
    }
})

const toast = useToast()
const showEditModal = ref(false)

const toggleAvailability = (service) => {
    router.patch(route('vendor.services.toggle-availability', service.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(`Service ${service.is_available ? 'deactivated' : 'activated'} successfully`);
        },
        onError: () => {
            toast.error('Failed to update service availability');
        }
    });
};

const deleteService = (serviceId) => {
    if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
        router.delete(route('vendor.services.destroy', serviceId), {
            onSuccess: () => {
                toast.success('Service deleted successfully');
            },
            onError: () => {
                toast.error('Failed to delete service');
            }
        });
    }
};

const editService = (service) => {
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

const handleServiceUpdated = () => {
    // Optionally refresh the page or emit an event to parent component
    router.reload({ only: ['services'] });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

// Helper function to get total dish count
const getTotalDishCount = (dishes) => {
    if (!dishes || typeof dishes !== 'object') return 0;
    return Object.values(dishes).reduce((total, categoryDishes) => total + (categoryDishes?.length || 0), 0);
};
</script>

<template>
    <div>
        <!-- Service Card -->
        <div
            class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-shadow duration-200">
            <!-- Service Image with Top Info -->
            <div class="relative h-48 w-full overflow-hidden">
                <img :src="service.image_url" :alt="service.name"
                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">

                <!-- Top Info Bar -->
                <div
                    class="absolute top-0 left-0 right-0 bg-gradient-to-b from-black/40 to-transparent p-3 flex justify-between items-start">
                    <!-- Category Badge -->
                    <span class="text-xs font-medium px-3 py-1 bg-white/95 text-gray-800 rounded-full shadow-sm">
                        {{ service.category.name }}
                    </span>

                    <!-- Availability Badge -->
                    <span :class="{
                        'bg-green-500 text-white': service.is_available,
                        'bg-red-500 text-white': !service.is_available
                    }" class="text-xs font-semibold px-3 py-1 rounded-full shadow-sm">
                        {{ service.is_available ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <!-- Service Details -->
            <div class="p-5">
                <!-- Title and Price -->
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-xl font-bold text-gray-900 line-clamp-1 flex-1 mr-3">{{ service.name }}</h3>
                    <div class="text-right">
                        <div class="text-lg font-bold text-indigo-600">{{ formatPrice(service.catering_service?.price ||
                            service?.price) }} <span class="text-black"
                                v-if="service.catering_service.price !== service.catering_service.package_price">/
                                Pax</span>

                        </div>
                        <div v-if="service.catering_service?.delivery_fee" class="text-xs text-gray-500">
                            + {{ formatPrice(service.catering_service.delivery_fee) }} delivery
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <p class="text-gray-600 mb-4 line-clamp-3 leading-relaxed">{{ service.description }}</p>





                <!-- Actions -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                    <!-- Toggle Switch -->
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer group">
                            <input type="checkbox" :checked="service.is_available" @change="toggleAvailability(service)"
                                class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500">
                            </div>
                            <span class="ml-3 text-sm font-medium" :class="{
                                'text-green-600': service.is_available,
                                'text-gray-600': !service.is_available
                            }">
                                {{ service.is_available ? 'Active' : 'Inactive' }}
                            </span>
                        </label>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button @click="editService(service)"
                            class="flex items-center gap-1 px-3 py-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-150 text-sm font-medium"
                            title="Edit service">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            Edit
                        </button>
                        <button @click="deleteService(service.id)"
                            class="flex items-center gap-1 px-3 py-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150 text-sm font-medium"
                            title="Delete service">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    @click="closeEditModal"></div>

                <!-- Modal panel -->
                <div
                    class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <!-- Modal header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Edit Service: {{ service.name }}
                            </h3>
                            <button @click="closeEditModal" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal content -->
                    <EditCateringServiceForm :service="service" :catering-service="service.catering_service"
                        :categories="categories" @close="closeEditModal" @updated="handleServiceUpdated" />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
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
