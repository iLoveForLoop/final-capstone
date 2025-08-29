<script setup>
import { ref, computed, watch } from 'vue';

// Props (keeping for future use)
const props = defineProps({
    service: {
        type: Object,
        default: () => null
    }
});

// Mock service data for demonstration
const mockService = ref({
    id: 1,
    name: 'Premium Wedding Photography Package',
    category: { name: 'Photography' },
    catering_service: null,
    photography_service: {
        price: 25000,
        duration: 8,
        style: 'Documentary',
        equipment: true
    },
    price: 25000,
    image_url: 'https://images.unsplash.com/photo-1606216794074-735e91aa2c92?w=400'
});

// Use mock data if no service is passed
const currentService = computed(() => props.service || mockService.value);

// Reactive state
const isOpen = ref(false);
const currentStep = ref(1);
const isLoading = ref(false);

// Form data
const formData = ref({
    eventDate: '',
    eventTime: '',
    eventType: '',
    guestCount: '',
    venue: '',
    specialRequests: '',
    contactName: '',
    contactEmail: '',
    contactPhone: '',
    budget: '',
    additionalServices: []
});

// Mock data for event types and additional services
const eventTypes = [
    'Wedding', 'Birthday Party', 'Corporate Event', 'Anniversary',
    'Graduation', 'Baby Shower', 'Engagement', 'Reunion', 'Other'
];

const additionalServices = [
    { id: 1, name: 'Photography Add-on', price: 5000 },
    { id: 2, name: 'Live Streaming', price: 3000 },
    { id: 3, name: 'Extra Equipment', price: 2000 },
    { id: 4, name: 'Extended Hours', price: 4000 }
];

// Computed properties
const totalSteps = 3;

const isStepValid = computed(() => {
    switch (currentStep.value) {
        case 1:
            return formData.value.eventDate && formData.value.eventTime &&
                formData.value.eventType && formData.value.guestCount;
        case 2:
            return formData.value.contactName && formData.value.contactEmail &&
                formData.value.contactPhone;
        case 3:
            return true;
        default:
            return false;
    }
});

const totalPrice = computed(() => {
    let base = currentService.value?.catering_service?.price || currentService.value?.photography_service?.price || currentService.value?.price || 0;
    let additionalCost = formData.value.additionalServices.reduce((sum, serviceId) => {
        const service = additionalServices.find(s => s.id === serviceId);
        return sum + (service ? service.price : 0);
    }, 0);

    // For catering, multiply by guest count
    if (currentService.value?.catering_service && formData.value.guestCount) {
        base = base * parseInt(formData.value.guestCount);
    }

    return base + additionalCost;
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};

// Methods
const openModal = (service) => {
    isOpen.value = true;
    currentStep.value = 1;
    resetForm();
};

const closeModal = () => {
    isOpen.value = false;
    setTimeout(() => {
        currentStep.value = 1;
        resetForm();
    }, 300);
};

const resetForm = () => {
    formData.value = {
        eventDate: '',
        eventTime: '',
        eventType: '',
        guestCount: '',
        venue: '',
        specialRequests: '',
        contactName: '',
        contactEmail: '',
        contactPhone: '',
        budget: '',
        additionalServices: []
    };
};

const nextStep = () => {
    if (currentStep.value < totalSteps) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const submitBooking = async () => {
    isLoading.value = true;

    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000));

    // Here you would make your actual API call
    console.log('Booking submitted:', {
        service: currentService.value,
        booking: formData.value,
        totalPrice: totalPrice.value
    });

    isLoading.value = false;
    closeModal();

    // Show success message (you can emit an event or use a toast notification)
    alert('Booking request submitted successfully!');
};

// Watch for escape key
watch(isOpen, (newValue) => {
    if (newValue) {
        document.addEventListener('keydown', handleEscape);
    } else {
        document.removeEventListener('keydown', handleEscape);
    }
});

const handleEscape = (e) => {
    if (e.key === 'Escape') {
        closeModal();
    }
};

// Add a method to open modal with mock data for demo
const openModalDemo = () => {
    isOpen.value = true;
    currentStep.value = 1;
    resetForm();
};

// Expose methods for parent component
defineExpose({
    openModal,
    closeModal,
    openModalDemo // For demo purposes
});
</script>

<template>
    <!-- Demo Button (Remove this when integrating) -->
    <div class="p-4 bg-gray-100 rounded-lg mb-4 text-center">
        <p class="text-gray-600 mb-2">Demo: Click to see the booking modal in action</p>
        <button @click="openModalDemo"
            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Open Booking Modal (Demo)
        </button>
    </div>

    <!-- Modal Overlay -->
    <Transition name="modal-overlay">
        <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click.self="closeModal">

            <!-- Modal Content -->
            <Transition name="modal-content">
                <div v-if="isOpen"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">

                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold">Book Service</h2>
                                <p class="text-blue-100 mt-1">{{ currentService?.name }}</p>
                            </div>
                            <button @click="closeModal" class="text-white hover:text-gray-200 transition-colors p-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between text-sm text-blue-100 mb-2">
                                <span>Step {{ currentStep }} of {{ totalSteps }}</span>
                                <span>{{ Math.round((currentStep / totalSteps) * 100) }}% Complete</span>
                            </div>
                            <div class="w-full bg-blue-800 rounded-full h-2">
                                <div class="bg-white rounded-full h-2 transition-all duration-500 ease-out"
                                    :style="{ width: (currentStep / totalSteps) * 100 + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">

                        <!-- Step 1: Event Details -->
                        <Transition name="step" mode="out-in">
                            <div v-if="currentStep === 1" key="step1" class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Event Date -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
                                        <input v-model="formData.eventDate" type="date" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <!-- Event Time -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Time *</label>
                                        <input v-model="formData.eventTime" type="time" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Event Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
                                        <select v-model="formData.eventType" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Select event type</option>
                                            <option v-for="type in eventTypes" :key="type" :value="type">{{ type }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Guest Count -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Number of Guests
                                            *</label>
                                        <input v-model="formData.guestCount" type="number" min="1" required
                                            placeholder="e.g. 50"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <!-- Venue -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Venue/Location</label>
                                    <input v-model="formData.venue" type="text"
                                        placeholder="Enter event venue or location"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Special Requests -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                                    <textarea v-model="formData.specialRequests" rows="3"
                                        placeholder="Any special requirements or notes for this event..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                                </div>
                            </div>
                        </Transition>

                        <!-- Step 2: Contact Information -->
                        <Transition name="step" mode="out-in">
                            <div v-if="currentStep === 2" key="step2" class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>

                                <!-- Contact Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                    <input v-model="formData.contactName" type="text" required
                                        placeholder="Enter your full name"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Email -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address
                                            *</label>
                                        <input v-model="formData.contactEmail" type="email" required
                                            placeholder="your@email.com"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <!-- Phone -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number
                                            *</label>
                                        <input v-model="formData.contactPhone" type="tel" required
                                            placeholder="+63 912 345 6789"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <!-- Budget -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Budget Range
                                        (Optional)</label>
                                    <input v-model="formData.budget" type="text" placeholder="e.g. ₱50,000 - ₱100,000"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </Transition>

                        <!-- Step 3: Review & Additional Services -->
                        <Transition name="step" mode="out-in">
                            <div v-if="currentStep === 3" key="step3" class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Review & Additional Services</h3>

                                <!-- Service Summary -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium text-gray-900 mb-3">Booking Summary</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Service:</span>
                                            <span class="font-medium">{{ currentService?.name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Date & Time:</span>
                                            <span class="font-medium">{{ formData.eventDate }} at {{ formData.eventTime
                                                }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Event Type:</span>
                                            <span class="font-medium">{{ formData.eventType }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Guests:</span>
                                            <span class="font-medium">{{ formData.guestCount }} people</span>
                                        </div>
                                        <div v-if="formData.venue" class="flex justify-between">
                                            <span class="text-gray-600">Venue:</span>
                                            <span class="font-medium">{{ formData.venue }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Services -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-3">Additional Services</h4>
                                    <div class="space-y-2">
                                        <label v-for="service in additionalServices" :key="service.id"
                                            class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input v-model="formData.additionalServices" :value="service.id"
                                                type="checkbox" class="text-blue-600 rounded">
                                            <div class="ml-3 flex-1">
                                                <span class="font-medium">{{ service.name }}</span>
                                                <span class="text-green-600 font-semibold ml-2">{{
                                                    formatPrice(service.price) }}</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Total Price -->
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-gray-900">Total Estimated Cost:</span>
                                        <span class="text-2xl font-bold text-blue-600">{{ formatPrice(totalPrice)
                                            }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">Final pricing may vary based on specific
                                        requirements</p>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                        <button v-if="currentStep > 1" @click="prevStep"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                            ← Previous
                        </button>
                        <div v-else></div>

                        <div class="flex space-x-3">
                            <button @click="closeModal"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>

                            <button v-if="currentStep < totalSteps" @click="nextStep" :disabled="!isStepValid"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
                                Continue →
                            </button>

                            <button v-else @click="submitBooking" :disabled="isLoading || !isStepValid"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center">
                                <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ isLoading ? 'Submitting...' : 'Submit Booking Request' }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
/* Modal overlay transitions */
.modal-overlay-enter-active,
.modal-overlay-leave-active {
    transition: opacity 0.3s ease;
}

.modal-overlay-enter-from,
.modal-overlay-leave-to {
    opacity: 0;
}

/* Modal content transitions */
.modal-content-enter-active {
    transition: all 0.3s ease;
}

.modal-content-leave-active {
    transition: all 0.2s ease;
}

.modal-content-enter-from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
}

.modal-content-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(-10px);
}

/* Step transitions */
.step-enter-active,
.step-leave-active {
    transition: all 0.3s ease;
}

.step-enter-from {
    opacity: 0;
    transform: translateX(30px);
}

.step-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}
</style>
