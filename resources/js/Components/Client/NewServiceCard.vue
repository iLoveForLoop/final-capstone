<script setup>
import { ref } from 'vue';
// import BookingForm from './BookingForm.vue';
import BookingModal from './BookingModal.vue';
import { Link, router } from '@inertiajs/vue3'


const props = defineProps({
    service: {
        type: Object
    }
})



const bookingForm = ref(null)

// console.log('Service: ', props.service)

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};

const bookingModal = ref(null);

const handleViewDetails = () => {
    router.get(route('client.service.show', props.service.id))
}

const isPricePackage = () => {

    if (props.service.catering_service) {
        if (props.service.catering_service.price !== props.service.catering_service.package_price) {
            return 'per pax'
        } else {
            return ''
        }
    }
    return ''
}

</script>

<template>
    <BookingModal ref="bookingModal" :service="service" />
    <div class="relative">
        <slot name="favorite"></slot>
        <img :src="service.image_url" :alt="service.title" class="w-full h-48 object-cover">
    </div>
    <div class="p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                {{ service.category_name }}
            </span>
            <div class="flex items-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                        clip-rule="evenodd" />
                </svg>
                {{ service.dateAdded }}
            </div>
        </div>
        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-1">{{ service.name }}</h3>
        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ service.description }}</p>
        <div class="flex items-center justify-between mb-3">
            <!-- {{ console.log(isPricePackage()) }} -->
            <div class="text-lg font-bold text-green-600">{{ formatPrice(service.price) }} <span
                    class="text-sm text-gray-600">{{ isPricePackage()
                    }}</span> </div>
            <div class="flex items-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                {{ service.rating ?? 'No ratings yet' }}
            </div>
        </div>
        <div class="text-sm text-gray-600 mb-3">
            <Link :href="route('client.vendor.show', service.vendor.id)"
                class="font-medium cursor-pointer text-blue-500">{{
                    service.vendor.business_name }}</Link> • {{
                service.vendor.location }}
        </div>
        <div class="flex space-x-2">
            <button @click="bookingModal.openModal"
                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded text-sm hover:bg-blue-700 transition-colors">
                Book Now
            </button>
            <button @click="handleViewDetails"
                class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded text-sm hover:bg-gray-50 transition-colors">
                View Details
            </button>
        </div>
    </div>
</template>

<style></style>
