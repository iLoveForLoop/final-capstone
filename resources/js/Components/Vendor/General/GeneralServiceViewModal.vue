<script setup>
import { ref, computed } from 'vue';
import { Eye, X, Image as ImageIcon, Star, MapPin, CheckCircle, ChevronLeft, ChevronRight, Sparkles } from 'lucide-vue-next';

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

// Format price function
const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

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

console.log('Media', props.service);
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
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 truncate">{{ service.name }}</h2>
                            <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ service.category?.name || 'Service' }}
                            </p>
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

                            <!-- Service Overview -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                                <!-- Left Column - Service Details -->
                                <div class="space-y-4 sm:space-y-6">
                                    <!-- Rating and Price -->
                                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <div class="flex items-center space-x-1">
                                                    <Star class="w-4 h-4 text-yellow-400 fill-current" />
                                                    <span class="font-semibold text-gray-900 text-sm sm:text-base">{{
                                                        service.avg_rating?.toFixed(1)
                                                        || 'New' }}</span>
                                                </div>
                                                <span class="text-gray-500 text-xs sm:text-sm">({{ service.reviews_count
                                                    || 0
                                                    }} reviews)</span>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xl sm:text-2xl font-bold text-gray-900">{{
                                                    formatPrice(service.price)
                                                    }}</div>
                                                <div v-if="service.max_price" class="text-xs sm:text-sm text-gray-600">
                                                    up to {{ formatPrice(service.max_price) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
