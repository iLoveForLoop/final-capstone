<script setup>
import { ref, computed } from 'vue';
import { Eye, X, Image as ImageIcon, MapPin, Calendar, Users, Clock, Star, CheckCircle } from 'lucide-vue-next';

const props = defineProps({
    service: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['close']);
const selectedImageIndex = ref(0);
const showImageModal = ref(false);

// Format price function
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

// Format date function
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Get service images
const serviceImages = computed(() => {
    if (props.service.media && props.service.media.length > 0) {
        return props.service.media.map(media => ({
            url: media.original_url,
            is_primary: media.custom_properties?.is_primary || false,
            is_cover: media.custom_properties?.is_cover || false,
            is_portfolio: media.custom_properties?.is_portfolio || false,
        }));
    }
    return props.service.image_url ? [{ url: props.service.image_url, is_cover: true }] : [];
});

// Get primary image
const primaryImage = computed(() => {
    return serviceImages.value.find(img => img.is_cover || img.is_primary) || serviceImages.value[0];
});

// Get portfolio images (excluding primary)
const portfolioImages = computed(() => {
    return serviceImages.value.filter(img => !img.is_cover && !img.is_primary);
});

// Open image modal
const openImageModal = (index) => {
    selectedImageIndex.value = index;
    showImageModal.value = true;
};

// Navigate images in modal
const nextImage = () => {
    selectedImageIndex.value = (selectedImageIndex.value + 1) % serviceImages.value.length;
};

const prevImage = () => {
    selectedImageIndex.value = (selectedImageIndex.value - 1 + serviceImages.value.length) % serviceImages.value.length;
};

// Close modal with escape key
const handleKeydown = (event) => {
    if (event.key === 'Escape') {
        showImageModal.value = false;
    }
};
</script>

<template>
    <teleport to="body">


        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <!-- Modal panel -->
                <div
                    class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full max-h-[90vh] overflow-y-auto">

                    <!-- Modal header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">
                                    {{ service.name }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">Complete overview of your service listing</p>
                            </div>
                            <button @click="emit('close')"
                                class="text-gray-400 hover:text-gray-500 p-2 rounded-full hover:bg-gray-100 transition-colors">
                                <X class="h-6 w-6" />
                            </button>
                        </div>
                    </div>

                    <!-- Modal content -->
                    <div class="px-6 py-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Left Column - Images & Basic Info -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Main Image -->
                                <div class="bg-gray-50 rounded-xl overflow-hidden">
                                    <div v-if="primaryImage" class="relative aspect-[4/3]">
                                        <img :src="primaryImage.url" :alt="service.name"
                                            class="w-full h-full object-cover cursor-pointer"
                                            @click="openImageModal(0)">
                                        <div
                                            class="absolute top-4 left-4 bg-white bg-opacity-90 px-3 py-1 rounded-full text-sm font-medium text-gray-700">
                                            Cover Photo
                                        </div>
                                        <button @click="openImageModal(0)"
                                            class="absolute bottom-4 right-4 bg-white bg-opacity-90 p-2 rounded-full text-gray-600 hover:text-indigo-600 transition-colors">
                                            <Eye class="w-5 h-5" />
                                        </button>
                                    </div>
                                    <div v-else class="aspect-[4/3] flex items-center justify-center bg-gray-200">
                                        <ImageIcon class="w-16 h-16 text-gray-400" />
                                    </div>
                                </div>

                                <!-- Portfolio Images Grid -->
                                <div v-if="portfolioImages.length > 0">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Portfolio Images</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        <div v-for="(image, index) in portfolioImages" :key="index"
                                            class="relative aspect-square bg-gray-100 rounded-lg overflow-hidden group cursor-pointer">
                                            <img :src="image.url" :alt="`Portfolio image ${index + 1}`"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                                @click="openImageModal(index + 1)">
                                            <div
                                                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 flex items-center justify-center">
                                                <Eye
                                                    class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Service Description -->
                                <div class="bg-white border border-gray-200 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Service Description</h4>
                                    <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ service.description
                                        ||
                                        'No description provided' }}</p>
                                </div>
                            </div>

                            <!-- Right Column - Details & Stats -->
                            <div class="space-y-6">
                                <!-- Service Status & Category -->
                                <div class="bg-white border border-gray-200 rounded-xl p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-3 h-3 rounded-full"
                                                    :class="service.is_available ? 'bg-green-500' : 'bg-gray-400'">
                                                </div>
                                                <span class="text-sm font-medium"
                                                    :class="service.is_available ? 'text-green-700' : 'text-gray-500'">
                                                    {{ service.is_available ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                            <span
                                                class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">
                                                {{ service.category?.name || 'Uncategorized' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Pricing -->
                                    <div class="space-y-3">
                                        <h4 class="text-lg font-semibold text-gray-900">Pricing</h4>
                                        <div class="flex items-baseline space-x-2">
                                            <span class="text-2xl font-bold text-indigo-600">{{
                                                formatPrice(service.price)
                                            }}</span>
                                            <span v-if="service.max_price" class="text-sm text-gray-500">
                                                up to {{ formatPrice(service.max_price) }}
                                            </span>
                                        </div>
                                        <div v-if="service.category?.name === 'Catering' && service.catering_service"
                                            class="text-sm text-gray-600">
                                            <div class="flex items-center space-x-1">
                                                <span>Minimum order: {{ service.catering_service.minimum_pax }}
                                                    pax</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Service Statistics -->
                                <div class="bg-white border border-gray-200 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Service Statistics</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Total Bookings</span>
                                            <span class="font-semibold text-gray-900">{{ service.bookings_count || 0
                                            }}</span>
                                        </div>
                                        <div v-if="service.avg_rating" class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Average Rating</span>
                                            <div class="flex items-center space-x-1">
                                                <Star class="w-4 h-4 text-yellow-400 fill-current" />
                                                <span class="font-semibold text-gray-900">{{
                                                    service.avg_rating.toFixed(1)
                                                }}</span>
                                                <span class="text-sm text-gray-500">({{ service.reviews_count || 0
                                                }})</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Created</span>
                                            <span class="font-medium text-gray-900">{{ formatDate(service.created_at)
                                            }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Last Updated</span>
                                            <span class="font-medium text-gray-900">{{ formatDate(service.updated_at)
                                            }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Photography Specific Details -->
                                <div v-if="service.category?.name === 'Photography' && service.photography_service"
                                    class="bg-white border border-gray-200 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Photography Details</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Studio Shoot</span>
                                            <div class="flex items-center space-x-1">
                                                <CheckCircle class="w-4 h-4"
                                                    :class="service.photography_service.studio_shoot_available ? 'text-green-500' : 'text-gray-400'" />
                                                <span class="font-medium"
                                                    :class="service.photography_service.studio_shoot_available ? 'text-green-700' : 'text-gray-500'">
                                                    {{ service.photography_service.studio_shoot_available ? 'Available'
                                                        :
                                                        'Not Available' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div
                                            v-if="service.photography_service.specifications && service.photography_service.specifications.length > 0">
                                            <span class="text-sm text-gray-600 block mb-2">Specifications:</span>
                                            <div class="flex flex-wrap gap-2">
                                                <span
                                                    v-for="(spec, index) in service.photography_service.specifications"
                                                    :key="index"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ spec }}
                                                </span>
                                            </div>
                                        </div>
                                        <div v-if="service.photography_service.notes">
                                            <span class="text-sm text-gray-600 block mb-1">Additional Notes:</span>
                                            <p class="text-sm text-gray-700">{{ service.photography_service.notes }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Catering Specific Details -->
                                <div v-if="service.category?.name === 'Catering' && service.catering_service"
                                    class="bg-white border border-gray-200 rounded-xl p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Catering Details</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Cuisine Type</span>
                                            <span class="font-medium text-gray-900">{{
                                                service.catering_service.cuisine_type
                                                || 'Not specified' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Minimum Pax</span>
                                            <span class="font-medium text-gray-900">{{
                                                service.catering_service.minimum_pax
                                            }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Price per Pax</span>
                                            <span class="font-medium text-gray-900">{{
                                                formatPrice(service.catering_service.price) }}</span>
                                        </div>
                                        <div v-if="service.catering_service.package_price"
                                            class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600">Package Price</span>
                                            <span class="font-medium text-gray-900">{{
                                                formatPrice(service.catering_service.package_price) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex justify-end">
                            <button @click="emit('close')"
                                class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div v-if="showImageModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-90"
            @keydown="handleKeydown">
            <div class="relative max-w-4xl max-h-[90vh] mx-4">
                <!-- Close button -->
                <button @click="showImageModal = false"
                    class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors z-10">
                    <X class="w-8 h-8" />
                </button>

                <!-- Navigation buttons -->
                <button v-if="serviceImages.length > 1" @click="prevImage"
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors z-10 bg-black bg-opacity-50 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button v-if="serviceImages.length > 1" @click="nextImage"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors z-10 bg-black bg-opacity-50 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Image -->
                <img :src="serviceImages[selectedImageIndex]?.url" :alt="`Service image ${selectedImageIndex + 1}`"
                    class="max-w-full max-h-[80vh] object-contain rounded-lg">

                <!-- Image counter -->
                <div
                    class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-sm bg-black bg-opacity-50 px-3 py-1 rounded-full">
                    {{ selectedImageIndex + 1 }} / {{ serviceImages.length }}
                </div>
            </div>
        </div>
    </teleport>
</template>

<style scoped>
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f7fafc;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 3px;
}
</style>
