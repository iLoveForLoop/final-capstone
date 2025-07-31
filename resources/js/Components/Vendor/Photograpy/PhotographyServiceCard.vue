<script setup>
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();

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
    // Implement your edit modal logic here
    console.log('Edit service:', service);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const showPriceRange = (service) => {
    if (service.max_price) {
        return `${formatPrice(service.price)} - ${formatPrice(service.max_price)}`;
    }
    return formatPrice(service.price);
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
                class="absolute top-0 left-0 right-0 bg-gradient-to-b from-black/30 to-transparent p-3 flex justify-between items-start">
                <!-- Category Badge -->
                <span class="text-xs font-medium px-2 py-1 bg-white/90 text-gray-800 rounded-full">
                    {{ service.category.name }}
                </span>

                <!-- Availability Badge -->
                <span :class="{
                    'bg-green-500 text-white': service.is_available,
                    'bg-gray-500 text-white': !service.is_available
                }" class="text-xs font-medium px-3 py-1 rounded-full shadow-sm">
                    {{ service.is_available ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <!-- Service Details -->
        <div class="p-4">
            <!-- Title and Coverage Type -->
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-lg font-bold text-gray-800 line-clamp-1">{{ service.name }}</h3>
                <span v-if="service.photography_service?.coverage_type"
                    class="text-xs font-semibold text-indigo-700 bg-indigo-50 px-2 py-1 rounded-full whitespace-nowrap">
                    {{ service.photography_service.coverage_type }}
                </span>
            </div>

            <!-- Description -->
            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ service.description }}</p>

            <!-- Key Features Grid -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <!-- Photographers -->
                <div class="flex items-center text-sm text-gray-700">
                    <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>{{ service.photography_service?.number_of_photographers || 1 }} photographer{{
                        service.photography_service?.number_of_photographers > 1 ? 's' : '' }}</span>
                </div>

                <!-- Coverage Hours -->
                <div v-if="service.photography_service?.hours_of_coverage"
                    class="flex items-center text-sm text-gray-700">
                    <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ service.photography_service.hours_of_coverage }} hours</span>
                </div>

                <!-- Delivery Time -->
                <div class="flex items-center text-sm text-gray-700">
                    <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ service.photography_service?.delivery_time_days || 30 }} days delivery</span>
                </div>

                <!-- Price -->
                <div class="flex items-center text-sm text-gray-700">
                    <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ showPriceRange(service) }}</span>
                </div>
            </div>

            <!-- Deliverables -->
            <div v-if="service.photography_service?.deliverables?.length" class="mb-3">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Deliverables</h4>
                <div class="flex flex-wrap gap-1">
                    <span v-for="(item, index) in service.photography_service.deliverables.slice(0, 5)" :key="index"
                        class="text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded-full">
                        {{ item }}
                    </span>
                    <span v-if="service.photography_service.deliverables.length > 5"
                        class="text-xs px-2 py-1 bg-gray-50 text-gray-500 rounded-full">
                        +{{ service.photography_service.deliverables.length - 5 }} more
                    </span>
                </div>
            </div>

            <!-- Specifications -->
            <div v-if="service.photography_service?.specifications?.length" class="mb-3">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Features</h4>
                <div class="flex flex-wrap gap-1">
                    <span v-for="(spec, index) in service.photography_service.specifications.slice(0, 3)" :key="index"
                        class="text-xs px-2 py-1 bg-purple-50 text-purple-700 rounded-full">
                        {{ spec }}
                    </span>
                    <span v-if="service.photography_service.specifications.length > 3"
                        class="text-xs px-2 py-1 bg-gray-50 text-gray-500 rounded-full">
                        +{{ service.photography_service.specifications.length - 3 }} more
                    </span>
                </div>
            </div>

            <!-- Studio Shoot Badge -->
            <div v-if="service.photography_service?.studio_shoot_available" class="mb-3">
                <span class="text-xs px-2 py-1 bg-yellow-50 text-yellow-700 rounded-full font-medium">
                    🏢 Studio Shoot Available
                </span>
            </div>

            <!-- Delivery Fee -->
            <div v-if="service.delivery_fee" class="mb-3 flex items-center text-sm text-gray-700">
                <svg class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
                <span>Delivery: {{ formatPrice(service.delivery_fee) }}</span>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                <!-- Toggle Switch -->
                <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" :checked="service.is_available" @change="toggleAvailability(service)"
                            class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500">
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-600">
                            {{ service.is_available ? 'Active' : 'Inactive' }}
                        </span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <button @click="editService(service)"
                        class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-150"
                        title="Edit service">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button>
                    <button @click="deleteService(service.id)"
                        class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150"
                        title="Delete service">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
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

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
