<script setup>
import { ref, computed } from 'vue';
import { Eye, X, Image as ImageIcon, Star, MapPin, CheckCircle, ChevronLeft, ChevronRight, Users, Utensils, Camera, Wrench, Mail, Phone } from 'lucide-vue-next';

const props = defineProps({
    service: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close']);

// Modal state
const isOpen = ref(false);
const isClosing = ref(false);
const selectedImageIndex = ref(0);
const showImageModal = ref(false);

// Computed property to determine service type
const serviceType = computed(() => {
    if (props.service.catering_service) return 'catering';
    if (props.service.photography_service) return 'photography';
    return 'generic';
});

// Format price helper
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

// Get total dish count for catering
const getTotalDishCount = (dishes) => {
    if (!dishes || typeof dishes !== 'object') return 0;
    return Object.values(dishes).reduce((total, categoryDishes) => total + (categoryDishes?.length || 0), 0);
};

// Get service-specific data
const serviceData = computed(() => {
    switch (serviceType.value) {
        case 'catering':
            return props.service.catering_service;
        case 'photography':
            return props.service.photography_service;
        default:
            return {};
    }
});

// Get service type display name
const serviceTypeDisplay = computed(() => {
    switch (serviceType.value) {
        case 'catering':
            return 'Catering Service';
        case 'photography':
            return 'Photography Service';
        default:
            return 'Service';
    }
});

// Get service icon
const serviceIcon = computed(() => {
    switch (serviceType.value) {
        case 'catering':
            return Utensils;
        case 'photography':
            return Camera;
        default:
            return Wrench;
    }
});

// Get service images
const serviceImages = computed(() => {
    if (props.service.media && props.service.media.length > 0) {
        return props.service.media.map(media => ({
            url: media.original_url,
            is_primary: media.custom_properties?.is_primary || false,
            is_cover: media.custom_properties?.is_cover || false,
        }));
    }
    return props.service.image_url ? [{ url: props.service.image_url, is_cover: true }] : [];
});

// Get primary image
const primaryImage = computed(() => {
    return serviceImages.value.find(img => img.is_cover || img.is_primary) || serviceImages.value[0];
});

// Get other images (excluding primary)
const otherImages = computed(() => {
    return serviceImages.value.filter(img => !img.is_cover && !img.is_primary);
});

// Check if there are multiple images
const hasMultipleImages = computed(() => {
    return serviceImages.value.length > 1;
});

// Modal control methods
const openModal = () => {
    isOpen.value = true;
    isClosing.value = false;
};

const closeModal = () => {
    isClosing.value = true;
    setTimeout(() => {
        isOpen.value = false;
        isClosing.value = false;
        emit('close');
    }, 300);
};

// Image modal controls
const openImageModal = (index) => {
    selectedImageIndex.value = index;
    showImageModal.value = true;
};

const closeImageModal = () => {
    showImageModal.value = false;
};

const nextImage = () => {
    selectedImageIndex.value = (selectedImageIndex.value + 1) % serviceImages.value.length;
};

const prevImage = () => {
    selectedImageIndex.value = (selectedImageIndex.value - 1 + serviceImages.value.length) % serviceImages.value.length;
};

// Handle keyboard events
const handleKeydown = (event) => {
    if (event.key === 'Escape') {
        if (showImageModal.value) {
            closeImageModal();
        } else {
            closeModal();
        }
    }
};

defineExpose({
    openModal,
    closeModal
});
</script>

<template>
    <Transition name="modal-fade">
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4"
            @keydown="handleKeydown" tabindex="0">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-gray-600/40 backdrop-blur-sm transition-opacity duration-300"
                :class="isClosing ? 'opacity-0' : 'opacity-100'" @click="closeModal"></div>

            <!-- Main Modal -->
            <Transition name="modal-scale">
                <div v-if="isOpen && !isClosing"
                    class="relative bg-white rounded-lg sm:rounded-xl w-full max-w-4xl max-h-[95vh] flex flex-col shadow-xl overflow-hidden border border-gray-200">

                    <!-- Header -->
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center">
                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                            <div class="p-2 bg-gray-100 rounded-lg flex-shrink-0">
                                <component :is="serviceIcon" :size="20" class="text-gray-700" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 truncate">{{ service.name }}
                                </h2>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1 truncate">
                                    {{ service.category?.name || serviceTypeDisplay }}
                                </p>
                            </div>
                        </div>
                        <button @click="closeModal"
                            class="text-gray-400 hover:text-gray-600 p-1.5 transition-all duration-200 hover:bg-gray-100 rounded-lg flex-shrink-0">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto">
                        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                            <!-- Images Section -->
                            <div class="space-y-3 sm:space-y-4">
                                <!-- Main Image -->
                                <div class="bg-gray-100 rounded-lg overflow-hidden">
                                    <div v-if="primaryImage" class="relative aspect-[4/3]">
                                        <img :src="primaryImage.url" :alt="service.name"
                                            class="w-full h-full object-cover cursor-pointer transition-all duration-200 hover:scale-105"
                                            @click="openImageModal(0)">
                                        <button @click="openImageModal(0)"
                                            class="absolute bottom-3 sm:bottom-4 right-3 sm:right-4 bg-white/90 backdrop-blur-sm p-1.5 sm:p-2 rounded-lg text-gray-600 hover:text-gray-800 transition-all duration-200 hover:bg-white">
                                            <Eye class="w-4 h-4" />
                                        </button>
                                        <div v-if="hasMultipleImages"
                                            class="absolute top-3 sm:top-4 left-3 sm:left-4 bg-gray-900 text-white px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium">
                                            {{ serviceImages.length }} photos
                                        </div>
                                    </div>
                                    <div v-else class="aspect-[4/3] flex items-center justify-center bg-gray-200">
                                        <ImageIcon class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400" />
                                    </div>
                                </div>

                                <!-- Thumbnail Images -->
                                <div v-if="otherImages.length > 0" class="grid grid-cols-4 gap-2">
                                    <div v-for="(image, index) in otherImages.slice(0, 4)" :key="index"
                                        class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer group relative"
                                        @click="openImageModal(index + 1)">
                                        <img :src="image.url" :alt="`Service image ${index + 2}`"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-all duration-200">
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-200">
                                        </div>
                                    </div>
                                    <!-- Show remaining images count if there are more than 4 -->
                                    <div v-if="otherImages.length > 4"
                                        class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center cursor-pointer hover:bg-gray-200 transition-colors"
                                        @click="openImageModal(0)">
                                        <div class="text-center">
                                            <div class="text-base sm:text-lg font-bold text-gray-600">+{{
                                                otherImages.length - 3 }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">More</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Price and Service-Specific Tags -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <div
                                        class="flex items-center bg-gray-900 text-white px-3 sm:px-4 py-2 rounded-lg font-semibold text-base sm:text-lg">
                                        {{ formatPrice(service.price) }}
                                        <div class="flex" v-if="service.category?.name === 'Catering'">
                                            <span
                                                v-if="service?.catering_service?.price !== service?.catering_service?.package_price"
                                                class="text-xs font-normal text-gray-300 ml-1">/person</span>
                                            <span v-else class="text-xs font-normal text-gray-300 ml-1">package</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-2 sm:px-3 py-1 rounded-lg text-xs sm:text-sm font-medium"
                                        :class="service.is_available ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'">
                                        {{ service.is_available ? 'Available' : 'Unavailable' }}
                                    </span>

                                    <!-- Catering customizable tag -->
                                    <span v-if="serviceType === 'catering' && serviceData.is_customizable"
                                        class="inline-flex items-center text-xs sm:text-sm px-2 sm:px-3 py-1 bg-gray-100 text-gray-800 rounded-lg font-medium border border-gray-300">
                                        Customizable
                                    </span>

                                    <!-- Photography studio tag -->
                                    <span v-if="serviceType === 'photography' && serviceData.studio_shoot_available"
                                        class="inline-flex items-center text-xs sm:text-sm px-2 sm:px-3 py-1 bg-gray-100 text-gray-800 rounded-lg font-medium border border-gray-300">
                                        Studio Available
                                    </span>
                                </div>
                            </div>

                            <!-- Rating -->
                            <div v-if="service.avg_rating" class="flex items-center space-x-2">
                                <div class="flex items-center space-x-1">
                                    <Star class="w-4 h-4 text-yellow-400 fill-current" />
                                    <span class="text-sm font-medium text-gray-900">{{ service.avg_rating?.toFixed(1)
                                        }}</span>
                                </div>
                                <span class="text-xs text-gray-500">({{ service.reviews_count || 0 }} reviews)</span>
                            </div>

                            <!-- Service Overview -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                                <!-- Left Column - Service Details -->
                                <div class="space-y-4 sm:space-y-6">
                                    <!-- Description -->
                                    <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4">
                                        <h3 class="font-semibold text-gray-900 mb-2 sm:mb-3 text-sm sm:text-base">About
                                            This Service</h3>
                                        <p class="text-gray-700 leading-relaxed text-xs sm:text-sm">
                                            {{ service.description || 'No description provided.' }}
                                        </p>
                                    </div>

                                    <!-- Specifications -->
                                    <div v-if="service.specifications && service.specifications.length > 0"
                                        class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4">
                                        <h3 class="font-semibold text-gray-900 mb-2 sm:mb-3 text-sm sm:text-base">What's
                                            Included</h3>
                                        <div class="space-y-2">
                                            <div v-for="(spec, index) in service.specifications" :key="index"
                                                class="flex items-center space-x-2 sm:space-x-3 text-xs sm:text-sm text-gray-700">
                                                <CheckCircle class="w-4 h-4 text-green-500 flex-shrink-0" />
                                                <span class="flex-1">{{ spec }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CATERING SERVICE DETAILS -->
                                    <TransitionGroup name="staggered-fade" tag="div" v-if="serviceType === 'catering'"
                                        class="space-y-4">
                                        <div key="catering-header" class="border-b border-gray-200 pb-2">
                                            <h4 class="text-sm sm:text-base font-semibold text-gray-900">Catering
                                                Service
                                                Details</h4>
                                        </div>

                                        <!-- Basic Information -->
                                        <div key="catering-basic"
                                            class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                            <!-- Capacity -->
                                            <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
                                                <div class="flex items-center mb-2">
                                                    <div
                                                        class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center mr-3">
                                                        <Users :size="16" class="text-white" />
                                                    </div>
                                                    <h5 class="text-xs sm:text-sm font-medium text-gray-900">Capacity
                                                    </h5>
                                                </div>
                                                <p class="text-base sm:text-lg font-semibold text-gray-900">
                                                    {{ serviceData.min_pax }}-{{ serviceData.max_pax }}
                                                    <span
                                                        class="text-xs sm:text-sm font-normal text-gray-500">pax</span>
                                                </p>
                                            </div>

                                            <!-- Cuisine Type -->
                                            <div v-if="serviceData.cuisine_type"
                                                class="bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
                                                <div class="flex items-center mb-2">
                                                    <div
                                                        class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center mr-3">
                                                        <Utensils :size="16" class="text-white" />
                                                    </div>
                                                    <h5 class="text-xs sm:text-sm font-medium text-gray-900">Cuisine
                                                        Type
                                                    </h5>
                                                </div>
                                                <p class="text-sm sm:text-base font-medium text-gray-900">{{
                                                    serviceData.cuisine_type }}</p>
                                            </div>
                                        </div>

                                        <!-- Menu -->
                                        <div v-if="serviceData.dishes && Object.keys(serviceData.dishes).length"
                                            key="catering-menu">
                                            <div class="flex items-center justify-between mb-3">
                                                <h5 class="text-sm sm:text-base font-semibold text-gray-900">Menu</h5>
                                                <span
                                                    class="text-xs bg-gray-800 text-white px-3 py-1 rounded-full font-medium">
                                                    {{ getTotalDishCount(serviceData.dishes) }} dishes total
                                                </span>
                                            </div>

                                            <div class="space-y-3">
                                                <div v-for="(dishes, category) in serviceData.dishes" :key="category"
                                                    class="bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <h6 class="text-sm font-medium text-gray-900">{{ category }}
                                                        </h6>
                                                        <span
                                                            class="text-xs text-gray-600 bg-white px-2 py-1 rounded-full border">
                                                            {{ dishes?.length || 0 }} items
                                                        </span>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                        <div v-for="dish in dishes" :key="dish"
                                                            class="text-xs sm:text-sm text-gray-700 bg-white px-3 py-2 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                                            {{ dish }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </TransitionGroup>



                                </div>

                                <!-- Right Column - Service Info & Vendor -->
                                <div class="space-y-4 sm:space-y-6">
                                    <!-- Service Features -->
                                    <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4">
                                        <h3 class="font-semibold text-gray-900 mb-2 sm:mb-3 text-sm sm:text-base">
                                            Service Information</h3>
                                        <div class="space-y-2 sm:space-y-3">
                                            <!-- Availability -->
                                            <div class="flex items-center justify-between text-xs sm:text-sm">
                                                <span class="text-gray-600">Availability</span>
                                                <span class="font-medium"
                                                    :class="service.is_available ? 'text-green-600' : 'text-red-600'">
                                                    {{ service.is_available ? 'Available' : 'Not Available' }}
                                                </span>
                                            </div>

                                            <!-- Category -->
                                            <div class="flex items-center justify-between text-xs sm:text-sm">
                                                <span class="text-gray-600">Service Type</span>
                                                <span class="font-medium text-gray-900">{{ service.category?.name ||
                                                    'General Service' }}</span>
                                            </div>

                                            <!-- Bookings Count -->
                                            <div v-if="service.bookings_count"
                                                class="flex items-center justify-between text-xs sm:text-sm">
                                                <span class="text-gray-600">Total Bookings</span>
                                                <span class="font-medium text-gray-900">{{ service.bookings_count
                                                    }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Vendor Information -->
                                    <div v-if="service.vendor"
                                        class="bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
                                        <h3 class="font-semibold text-gray-900 mb-2 sm:mb-3 text-sm sm:text-base">Vendor
                                            Information</h3>
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-800 rounded-full flex items-center justify-center text-white font-semibold text-sm sm:text-base">
                                                {{ service.vendor.business_name?.charAt(0) || 'V' }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-900 text-sm sm:text-base truncate">{{
                                                    service.vendor.business_name }}
                                                </p>
                                                <div
                                                    class="flex items-center space-x-1 text-gray-500 text-xs sm:text-sm mt-1">
                                                    <MapPin class="w-3 h-3 flex-shrink-0" />
                                                    <span class="truncate">{{ service.vendor.location }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3 space-y-2">
                                            <div v-if="service.vendor.email"
                                                class="flex items-center justify-between text-xs sm:text-sm">
                                                <span class="text-gray-600">Email</span>
                                                <span class="font-medium text-gray-900 break-all">{{
                                                    service.vendor.email }}</span>
                                            </div>
                                            <div v-if="service.vendor.phone"
                                                class="flex items-center justify-between text-xs sm:text-sm">
                                                <span class="text-gray-600">Phone</span>
                                                <span class="font-medium text-gray-900">{{ service.vendor.phone
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-4 sm:px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex justify-end">
                            <button @click="closeModal"
                                class="px-4 sm:px-6 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-all duration-200 shadow-sm hover:shadow-md">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>

    <!-- Image Preview Modal -->
    <Transition name="modal-fade">
        <div v-if="showImageModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90 p-2 sm:p-4"
            @keydown="handleKeydown">
            <div class="relative max-w-4xl max-h-[80vh] w-full">
                <!-- Close button -->
                <button @click="closeImageModal"
                    class="absolute -top-10 sm:-top-12 right-0 text-white hover:text-gray-300 transition-colors z-10 p-2">
                    <X class="w-5 h-5 sm:w-6 sm:h-6" />
                </button>

                <!-- Navigation buttons -->
                <button v-if="hasMultipleImages" @click="prevImage"
                    class="absolute left-2 sm:left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors z-10 bg-gray-800/80 rounded-full p-2 hover:bg-gray-700/80">
                    <ChevronLeft class="w-4 h-4 sm:w-5 sm:h-5" />
                </button>

                <button v-if="hasMultipleImages" @click="nextImage"
                    class="absolute right-2 sm:right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors z-10 bg-gray-800/80 rounded-full p-2 hover:bg-gray-700/80">
                    <ChevronRight class="w-4 h-4 sm:w-5 sm:h-5" />
                </button>

                <!-- Image -->
                <img :src="serviceImages[selectedImageIndex]?.url" :alt="`Service image ${selectedImageIndex + 1}`"
                    class="w-full h-full max-h-[70vh] object-contain rounded-lg">

                <!-- Image counter -->
                <div v-if="hasMultipleImages"
                    class="absolute bottom-3 sm:bottom-4 left-1/2 transform -translate-x-1/2 text-white text-xs sm:text-sm bg-gray-800/80 px-3 py-1 rounded-full">
                    {{ selectedImageIndex + 1 }} / {{ serviceImages.length }}
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Modal transitions */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-scale-enter-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-scale-leave-active {
    transition: all 0.2s cubic-bezier(0.4, 0, 1, 1);
}

.modal-scale-enter-from,
.modal-scale-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(10px);
}

/* Fade transition */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Slide up transition */
.slide-up-enter-active {
    transition: all 0.3s ease-out;
}

.slide-up-leave-active {
    transition: all 0.2s ease-in;
}

.slide-up-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

.slide-up-leave-to {
    opacity: 0;
}

/* Staggered fade for transition groups */
.staggered-fade-move,
.staggered-fade-enter-active,
.staggered-fade-leave-active {
    transition: all 0.3s ease;
}

.staggered-fade-enter-from,
.staggered-fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

.staggered-fade-leave-active {
    position: absolute;
}

/* Smooth scrolling */
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}

/* Mobile optimizations */
@media (max-width: 640px) {
    .overflow-y-auto {
        scrollbar-width: none;
    }

    .overflow-y-auto::-webkit-scrollbar {
        display: none;
    }
}

/* Image hover effects */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Ensure smooth transitions for all interactive elements */
button,
img {
    transition: all 0.2s ease-in-out;
}
</style>
