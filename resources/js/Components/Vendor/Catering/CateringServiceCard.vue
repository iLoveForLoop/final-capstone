<script setup>
import { useToast } from 'vue-toastification'
import { router } from '@inertiajs/vue3';

defineProps({
    service: {
        type: Object
    }
})

const toast = useToast()

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

// Helper function to get total dish count
const getTotalDishCount = (dishes) => {
    if (!dishes || typeof dishes !== 'object') return 0;
    return Object.values(dishes).reduce((total, categoryDishes) => total + (categoryDishes?.length || 0), 0);
};
</script>

<template>
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

            <!-- Buffet Type Badge (Bottom Right) -->
            <div v-if="service.catering_service?.buffet_type" class="absolute bottom-3 right-3">
                <span
                    class="text-xs font-semibold text-white bg-indigo-600/90 px-3 py-1 rounded-full shadow-sm backdrop-blur-sm">
                    {{ service.catering_service.buffet_type }}
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
                        service?.price) }} <span class="text-black" v-if="!service.catering_service.package_price">/
                            Pax</span>
                    </div>
                    <div v-if="service.catering_service?.delivery_fee" class="text-xs text-gray-500">
                        + {{ formatPrice(service.catering_service.delivery_fee) }} delivery
                    </div>
                </div>
            </div>

            <!-- Description -->
            <p class="text-gray-600 mb-4 line-clamp-3 leading-relaxed">{{ service.description }}</p>

            <!-- Key Info Grid -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <!-- Capacity -->
                <div class="flex items-center gap-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <svg class="h-5 w-5 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <div>
                        <div class="text-sm font-semibold text-blue-800">Capacity</div>
                        <div class="text-xs text-blue-700">{{ service.catering_service?.min_pax }}-{{
                            service.catering_service?.max_pax }} pax</div>
                    </div>
                </div>

                <!-- Lead Time -->
                <div class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <svg class="h-5 w-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <div class="text-sm font-semibold text-amber-800">Lead Time</div>
                        <div class="text-xs text-amber-700">{{ service.catering_service?.lead_time_days }} days advance
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Features -->
            <div class="space-y-4 mb-4">
                <!-- Customizable Badge -->
                <div v-if="service.catering_service?.is_customizable" class="flex items-center">
                    <div class="flex items-center gap-2 px-3 py-2 bg-purple-50 border border-purple-200 rounded-lg">
                        <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a1 1 0 01-1-1V9a1 1 0 011-1h1a2 2 0 100-4H4a1 1 0 01-1-1V4a1 1 0 011-1h3a1 1 0 001-1v-1a2 2 0 012-2z" />
                        </svg>
                        <span class="text-sm font-medium text-purple-800">Customizable Menu Available</span>
                    </div>
                </div>

                <!-- Service Areas -->
                <!-- <div v-if="service.catering_service?.service_area?.length" class="space-y-2">
                    <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Service Areas
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="area in service.catering_service.service_area" :key="area"
                            class="text-sm px-3 py-1 bg-green-50 text-green-700 rounded-full border border-green-200">
                            {{ area }}
                        </span>
                    </div>
                </div> -->

                <!-- Specifications -->
                <div v-if="service.catering_service?.specifications?.length" class="space-y-2">
                    <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Specifications
                    </h4>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <div class="space-y-1">
                            <div v-for="(spec, index) in service.catering_service.specifications" :key="index"
                                class="flex items-start text-sm text-yellow-800">
                                <span
                                    class="inline-block w-1.5 h-1.5 bg-yellow-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                                <span>{{ spec }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Categories -->
                <div v-if="service.catering_service?.dishes && Object.keys(service.catering_service.dishes).length"
                    class="space-y-2">
                    <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Menu Categories
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                            {{ Object.keys(service.catering_service.dishes).length }} categories
                        </span>
                    </h4>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 space-y-2">
                        <div v-for="(dishes, category) in service.catering_service.dishes" :key="category"
                            class="flex items-center justify-between py-1">
                            <span class="text-sm font-medium text-gray-700">{{ category }}</span>
                            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">
                                {{ dishes?.length || 0 }} items
                            </span>
                        </div>

                        <!-- Total Summary -->
                        <div class="pt-2 border-t border-gray-200 text-center">
                            <span class="text-sm font-semibold text-gray-700">
                                Total: {{ getTotalDishCount(service.catering_service.dishes) }} dishes
                            </span>
                        </div>
                    </div>
                </div>
            </div>

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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Edit
                    </button>
                    <button @click="deleteService(service.id)"
                        class="flex items-center gap-1 px-3 py-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150 text-sm font-medium"
                        title="Delete service">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
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
