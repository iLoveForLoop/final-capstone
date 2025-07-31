<script setup>
import { ref } from 'vue';

// Define expose to open modal from parent
const showModal = ref(false);
const open = () => {
    showModal.value = true;
    resetForm();
};
defineExpose({ open });

// Stepper setup
const steps = [
    { id: '1', name: 'Event Info', status: 'current' },
    { id: '2', name: 'Categories', status: 'upcoming' },
    { id: '3', name: 'Vendors', status: 'upcoming' },
    { id: '4', name: 'Confirm', status: 'upcoming' }
];

const currentStep = ref(0);
const eventForm = ref({
    name: '',
    location: '',
    event_date: '',
    event_time: '',
    description: ''
});
const selectedCategories = ref([]);
const selectedVendors = ref([]);

// Sample data
const categories = [
    { id: 1, name: 'Catering', icon: '🍽️' },
    { id: 2, name: 'Photography', icon: '📸' },
    { id: 3, name: 'Sound Systems', icon: '🔊' },
    { id: 4, name: 'Entertainers', icon: '🎤' },
    { id: 5, name: 'Makeup Artists', icon: '💄' },
    { id: 6, name: 'Attire Rentals', icon: '👔' }
];

const vendors = {
    1: [
        { id: 101, name: 'Gourmet Delights', price: '₱15,000+', rating: 4.8, reviews: 124 },
        { id: 102, name: 'Wedding Feast Co.', price: '₱12,000+', rating: 4.6, reviews: 89 },
        { id: 103, name: 'Organic Catering', price: '₱18,000+', rating: 4.9, reviews: 156 }
    ],
    2: [
        { id: 201, name: 'Perfect Shots', price: '₱20,000+', rating: 4.7, reviews: 112 },
        { id: 202, name: 'Memory Makers', price: '₱25,000+', rating: 4.9, reviews: 203 },
        { id: 203, name: 'Candid Moments', price: '₱18,000+', rating: 4.5, reviews: 76 }
    ]
};

// Form methods
const resetForm = () => {
    currentStep.value = 0;
    steps.forEach((step, index) => {
        step.status = index === 0 ? 'current' : 'upcoming';
    });
    eventForm.value = {
        name: '',
        location: '',
        event_date: '',
        event_time: '',
        description: ''
    };
    selectedCategories.value = [];
    selectedVendors.value = [];
};

const toggleCategory = (category) => {
    const index = selectedCategories.value.findIndex(c => c.id === category.id);
    if (index === -1) {
        selectedCategories.value.push(category);
    } else {
        selectedCategories.value.splice(index, 1);
    }
};

const toggleVendor = (vendor) => {
    const index = selectedVendors.value.findIndex(v => v.id === vendor.id);
    if (index === -1) {
        selectedVendors.value.push(vendor);
    } else {
        selectedVendors.value.splice(index, 1);
    }
};

const nextStep = () => {
    if (validateStep()) {
        if (currentStep.value < steps.length - 1) {
            steps[currentStep.value].status = 'complete';
            steps[currentStep.value + 1].status = 'current';
            currentStep.value++;
        }
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        steps[currentStep.value].status = 'upcoming';
        steps[currentStep.value - 1].status = 'current';
        currentStep.value--;
    }
};

const validateStep = () => {
    if (currentStep.value === 0) {
        if (!eventForm.value.name || !eventForm.value.location || !eventForm.value.event_date) {
            alert('Please fill in all required event details');
            return false;
        }
    } else if (currentStep.value === 1 && selectedCategories.value.length === 0) {
        alert('Please select at least one category');
        return false;
    } else if (currentStep.value === 2 && selectedVendors.value.length === 0) {
        alert('Please select at least one vendor');
        return false;
    }
    return true;
};

const submitSelection = () => {
    const eventData = {
        ...eventForm.value,
        categories: selectedCategories.value,
        vendors: selectedVendors.value
    };
    console.log('Form submission:', eventData);
    // Submit to backend here
    showModal.value = false;
    alert('Event created successfully!');
};
</script>

<template>
    <!-- Modal Overlay -->
    <transition name="fade">
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <!-- Modal Container -->
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="sticky top-0 bg-white p-6 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Create New Event</h2>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6">
                    <!-- Stepper Navigation -->
                    <nav class="flex items-center justify-center mb-8">
                        <ol class="flex items-center space-x-5 w-full">
                            <li v-for="(step, index) in steps" :key="step.name" class="flex-1">
                                <div class="flex flex-col items-center">
                                    <span :class="{
                                        'bg-purple-600 border-purple-600 text-white': step.status === 'current' || step.status === 'complete',
                                        'border-gray-300 text-gray-500': step.status === 'upcoming'
                                    }"
                                        class="flex items-center justify-center w-8 h-8 border-2 rounded-full font-medium text-sm">
                                        <span v-if="step.status === 'complete'">✓</span>
                                        <span v-else>{{ step.id }}</span>
                                    </span>
                                    <span :class="{
                                        'text-purple-600': step.status === 'current' || step.status === 'complete',
                                        'text-gray-500': step.status === 'upcoming'
                                    }" class="mt-2 text-xs sm:text-sm font-medium">
                                        {{ step.name }}
                                    </span>
                                </div>
                                <div v-if="index < steps.length - 1" :class="{
                                    'bg-purple-600': steps[index + 1].status === 'complete' || steps[index + 1].status === 'current',
                                    'bg-gray-300': steps[index + 1].status === 'upcoming'
                                }" class="absolute top-4 left-1/2 w-full h-0.5 -z-10"></div>
                            </li>
                        </ol>
                    </nav>

                    <!-- Step 1: Event Information -->
                    <div v-if="currentStep === 0" class="step-content">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h3>

                        <div class="space-y-4">
                            <div>
                                <label for="event-name" class="block text-sm font-medium text-gray-700 mb-1">Event Name
                                    *</label>
                                <input id="event-name" type="text" v-model="eventForm.name"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                    placeholder="Wedding, Birthday, Conference..." required>
                            </div>

                            <div>
                                <label for="event-location"
                                    class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                                <input id="event-location" type="text" v-model="eventForm.location"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                    placeholder="Venue or address" required>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="event-date" class="block text-sm font-medium text-gray-700 mb-1">Date
                                        *</label>
                                    <input id="event-date" type="date" v-model="eventForm.event_date"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                        required>
                                </div>
                                <div>
                                    <label for="event-time"
                                        class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                                    <input id="event-time" type="time" v-model="eventForm.event_time"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                                </div>
                            </div>

                            <div>
                                <label for="event-description"
                                    class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="event-description" rows="3" v-model="eventForm.description"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                    placeholder="Tell us about your event..."></textarea>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button @click="nextStep"
                                class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition">
                                Next: Choose Categories
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Category Selection -->
                    <div v-if="currentStep === 1" class="step-content">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Vendor Categories</h3>
                        <p class="text-gray-600 mb-6">Choose the types of vendors you need for your event</p>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <button v-for="category in categories" :key="category.id" @click="toggleCategory(category)"
                                :class="{
                                    'ring-2 ring-purple-600 bg-purple-50': selectedCategories.some(c => c.id === category.id),
                                    'border border-gray-200 hover:border-purple-300': !selectedCategories.some(c => c.id === category.id)
                                }" class="p-3 rounded-lg transition-all flex flex-col items-center">
                                <span class="text-2xl mb-1">{{ category.icon }}</span>
                                <span class="font-medium text-gray-900 text-sm">{{ category.name }}</span>
                            </button>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button @click="prevStep"
                                class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">
                                Back
                            </button>
                            <button @click="nextStep" :disabled="selectedCategories.length === 0" :class="{
                                'bg-purple-600 hover:bg-purple-700': selectedCategories.length > 0,
                                'bg-gray-300 cursor-not-allowed': selectedCategories.length === 0
                            }" class="px-6 py-2 rounded-lg text-white font-medium transition">
                                Next: Select Vendors
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Vendor Selection -->
                    <div v-if="currentStep === 2" class="step-content">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Your Vendors</h3>
                        <p class="text-gray-600 mb-6">Choose from our verified vendors in your selected categories</p>

                        <div v-for="category in selectedCategories" :key="category.id" class="mb-6">
                            <h4 class="text-md font-medium text-gray-900 mb-3 flex items-center">
                                <span class="text-xl mr-2">{{ category.icon }}</span>
                                {{ category.name }}
                            </h4>

                            <div class="grid grid-cols-1 gap-3">
                                <div v-for="vendor in vendors[category.id] || []" :key="vendor.id"
                                    @click="toggleVendor(vendor)" :class="{
                                        'ring-2 ring-purple-600 bg-purple-50': selectedVendors.some(v => v.id === vendor.id),
                                        'border border-gray-200 hover:border-purple-300': !selectedVendors.some(v => v.id === vendor.id)
                                    }" class="p-4 rounded-lg transition-all cursor-pointer">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="font-medium text-gray-900">{{ vendor.name }}</h5>
                                            <p class="text-purple-600 font-medium mt-1">{{ vendor.price }}</p>
                                        </div>
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="ml-1 text-sm text-gray-600">{{ vendor.rating }} ({{
                                                vendor.reviews }})</span>
                                        </div>
                                    </div>
                                    <div v-if="selectedVendors.some(v => v.id === vendor.id)"
                                        class="mt-2 flex items-center text-sm text-purple-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Selected
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between">
                            <button @click="prevStep"
                                class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">
                                Back
                            </button>
                            <button @click="nextStep" :disabled="selectedVendors.length === 0" :class="{
                                'bg-purple-600 hover:bg-purple-700': selectedVendors.length > 0,
                                'bg-gray-300 cursor-not-allowed': selectedVendors.length === 0
                            }" class="px-6 py-2 rounded-lg text-white font-medium transition">
                                Next: Review
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Confirmation -->
                    <div v-if="currentStep === 3" class="step-content">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Review Your Event</h3>

                        <div class="space-y-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Event Details</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Event Name</p>
                                        <p class="font-medium">{{ eventForm.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Location</p>
                                        <p class="font-medium">{{ eventForm.location }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Date</p>
                                        <p class="font-medium">{{ eventForm.event_date }}</p>
                                    </div>
                                    <div v-if="eventForm.event_time">
                                        <p class="text-sm text-gray-600">Time</p>
                                        <p class="font-medium">{{ eventForm.event_time }}</p>
                                    </div>
                                </div>
                                <div v-if="eventForm.description" class="mt-3">
                                    <p class="text-sm text-gray-600">Description</p>
                                    <p class="font-medium">{{ eventForm.description }}</p>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Selected Vendors</h4>
                                <div v-for="vendor in selectedVendors" :key="vendor.id"
                                    class="mb-3 pb-3 border-b border-gray-200 last:border-0">
                                    <p class="font-medium">{{ vendor.name }}</p>
                                    <div class="flex justify-between mt-1">
                                        <span class="text-purple-600 text-sm">{{ vendor.price }}</span>
                                        <span class="text-gray-600 text-sm flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-yellow-400 mr-1"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            {{ vendor.rating }} ({{ vendor.reviews }})
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="final-notes" class="block text-sm font-medium text-gray-700 mb-1">Additional
                                    Notes</label>
                                <textarea id="final-notes" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                    placeholder="Any special instructions for the vendors..."></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between">
                            <button @click="prevStep"
                                class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">
                                Back
                            </button>
                            <button @click="submitSelection"
                                class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition">
                                Confirm & Create Event
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.step-content {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

ol {
    position: relative;
}

ol li {
    position: relative;
}
</style>
