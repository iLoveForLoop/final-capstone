<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import EditPhotographyServiceForm from './Photograpy/EditPhotographyServiceForm.vue';
import EditCateringServiceForm from './Catering/EditCateringServiceForm.vue';
import ViewPhotographyService from './Photograpy/ViewPhotographyService.vue';
import ViewCateringService from './Catering/ViewCateringService.vue';
import { push } from 'notivue';


const toast = useToast();
const showEditModal = ref(false);
const showDeleteModal = ref(false)

const props = defineProps({
    service: {
        type: Object,
        required: true
    }
});



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

    router.delete(route('vendor.services.destroy', serviceId), {
        onSuccess: () => {
            // toast.success('Service deleted successfully');
            push.success('Service deleted successfully')
        },
        onError: () => {
            // toast.error('Failed to delete service');
            push.error('Failed to delete service')
        }
    });

};

const editService = () => {
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

const handleServiceUpdated = () => {
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


const showViewModal = ref(false);
const selectedService = ref(null);
const selectedCateringService = ref(null);

const openViewModal = () => {
    selectedService.value = props.service;
    selectedCateringService.value = props.service?.catering_service;
    showViewModal.value = true;
};




</script>

<template>

    <div>

        <ViewPhotographyService v-if="showViewModal && service.category.name === 'Photography'"
            :service="selectedService" @close="showViewModal = false" />
        <ViewCateringService v-if="showViewModal && service.category.name === 'Catering'" :service="selectedService"
            :catering-service="selectedCateringService" @close="showViewModal = false" />

        <!-- Service Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden ">


            <!-- Service Image with Top Info -->
            <div class="relative h-48 w-full overflow-hidden">
                <img :src="service.image_url" :alt="service.name"
                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">

                <!-- Top Info Bar -->
                <div
                    class="absolute top-0 left-0 right-0 bg-gradient-to-b from-black/40 to-transparent p-3 flex justify-between items-start">
                    <!-- Status Toggle -->
                    <div
                        class="absolute top-4 left-4 bg-white bg-opacity-95 backdrop-blur-sm px-3 py-2 rounded-lg border shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-medium"
                                :class="service.isActive ? 'text-green-700' : 'text-gray-500'">
                                {{ service.is_available ? 'Active' : 'Inactive' }}
                            </span>
                            <label class="relative inline-flex items-center cursor-pointer group">
                                <input type="checkbox" :checked="service.is_available"
                                    @change="toggleAvailability(service)" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none  rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500">
                                </div>

                            </label>
                        </div>
                    </div>

                    <!-- Category Badge -->
                    <div class="absolute top-4 right-4">
                        <span
                            class="bg-white bg-opacity-95 backdrop-blur-sm px-3 py-1 text-xs font-medium rounded-full text-gray-700 border shadow-sm">
                            {{ service.category.name }}
                        </span>
                    </div>



                    <!-- Active indicator ribbon -->
                    <div v-if="service.is_available"
                        class="absolute -right-8 top-4 rotate-45 bg-green-500 text-white px-8 py-1 text-xs font-bold z-10 shadow-md">
                        ACTIVE
                    </div>
                </div>
            </div>

            <!-- Service Details -->
            <div class="p-5">
                <!-- Title and Price -->
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-xl font-bold text-gray-900 line-clamp-1 flex-1 mr-3">{{ service.name }}</h3>
                    <div class="text-right">
                        <div v-if="service.category.name !== 'Catering'" class="text-lg font-bold text-indigo-600">{{
                            formatPrice(service.price) }}</div>
                        <div v-if="service.max_price" class="text-sm text-gray-500">
                            up to {{ formatPrice(service.max_price) }}
                        </div>

                        <div v-if="service.category.name === 'Catering'" class="text-lg font-bold text-indigo-600">{{
                            formatPrice(service.catering_service?.price ||
                                service?.price) }} <span class="text-black"
                                v-if="service.catering_service?.price !== service.catering_service?.package_price">/
                                Pax</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <p class="text-gray-600 mb-4 line-clamp-3 leading-relaxed">{{ service.description }}</p>



                <!-- Actions -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-100 gap-2">



                    <button
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 px-4 rounded-lg font-medium transition-colors text-sm flex items-center justify-center gap-1"
                        @click="editService(service)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Edit
                    </button>
                    <button
                        class="px-3 py-2.5 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors flex items-center justify-center"
                        @click="openViewModal(service)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </button>
                    <button
                        class="px-3 py-2.5 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors flex items-center justify-center"
                        @click="showDeleteModal = true">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <teleport to="body">


            <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
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
                        <EditPhotographyServiceForm v-if="service.category.name === 'Photography'" :service="service"
                            :category_id="service.service_category_id" @close="closeEditModal"
                            @updated="handleServiceUpdated" />

                        <EditCateringServiceForm v-if="service.category.name === 'Catering'" :service="service"
                            :catering-service="service.catering_service" @close="closeEditModal"
                            @updated="handleServiceUpdated" />


                    </div>
                </div>
            </div>
        </teleport>

        <!-- Delete Confirmation Modal -->
        <teleport to="body">
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Delete Service
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Are you sure you want to delete "{{ service?.name }}"? This action
                                            cannot be undone and all related transactions and bookings will be
                                            permanently removed.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" @click="deleteService(service.id)"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Delete Service
                            </button>
                            <button type="button" @click="showDeleteModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </teleport>
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
