<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

// Props for available vendors/services data
const props = defineProps({
    service: {
        type: Object,
        default: () => null
    },
    date: {
        type: String
    },
    time: {
        type: String
    }
});

// Reactive state
const isOpen = ref(false);
const currentStep = ref(1);
const isLoading = ref(false);

// Form data using Inertia's useForm
const form = useForm({
    name: '',
    location: '',
    event_date: props.date ?? '',
    event_time: props.time ?? '',
    description: '',
    pax: '',
    dishes: {},
    vendors: [props.service]
});



// Dish selection functionality
const selectedDishes = ref({});

// Get dish selection limits from service
const dishSelectionLimits = computed(() => {
    return props.service?.catering_service?.dish_selection_limits || {};
});

// Check if catering service is customizable
const isCateringCustomizable = computed(() => {
    return props.service?.catering_service?.is_customizable === 1;
});

// Get selection limit for a category
const getSelectionLimit = (category) => {
    return dishSelectionLimits.value[category] || 1;
};

// Check if a dish is selected
const isDishSelected = (category, dish) => {
    if (!Array.isArray(selectedDishes.value[category])) {
        selectedDishes.value[category] = [];
    }
    return selectedDishes.value[category].includes(dish);
};

// Get selected count for a category
const getSelectedCount = (category) => {
    if (!Array.isArray(selectedDishes.value[category])) {
        return 0;
    }
    return selectedDishes.value[category].length;
};

// Check if category has reached selection limit
const isSelectionLimitReached = (category) => {
    return getSelectedCount(category) >= getSelectionLimit(category);
};

// Toggle dish selection
const toggleDishSelection = (category, dish) => {
    // Only allow selection if catering is customizable
    if (!isCateringCustomizable.value) {
        return;
    }

    if (!Array.isArray(selectedDishes.value[category])) {
        selectedDishes.value[category] = [];
    }

    const currentSelection = selectedDishes.value[category];
    const dishIndex = currentSelection.indexOf(dish);

    if (dishIndex > -1) {
        // Remove dish if already selected
        currentSelection.splice(dishIndex, 1);
    } else {
        // Add dish if not selected and limit not reached
        if (!isSelectionLimitReached(category)) {
            currentSelection.push(dish);
        }
    }

    // Update form data
    form.dishes = { ...selectedDishes.value };
};

// Clear selection for a category
const clearCategorySelection = (category) => {
    // Only allow clearing if catering is customizable
    if (!isCateringCustomizable.value) {
        return;
    }

    selectedDishes.value[category] = [];
    form.dishes = { ...selectedDishes.value };
};

// Computed properties
const totalSteps = 2;

const isStepValid = computed(() => {
    switch (currentStep.value) {
        case 1:
            return form.name && form.location && form.event_date && form.event_time;
        case 2:
            return true;
        default:
            return false;
    }
});

const totalPrice = computed(() => {
    let base = props.service?.catering_service?.price || props.service?.photography_service?.price || props.service?.price || 0;

    // For catering, multiply by guest count
    if (props.service?.catering_service && form.pax) {
        base = base * parseInt(form.pax);
    }

    return base;
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};

// Methods
const openModal = (date, time) => {
    isOpen.value = true;
    currentStep.value = 1;
    resetForm();
    if (date && time) {
        form.event_date = date
        form.event_time = time

    }

};

const closeModal = () => {
    isOpen.value = false;
    setTimeout(() => {
        currentStep.value = 1;
        resetForm();
    }, 300);
};

const resetForm = () => {
    form.reset();
    form.clearErrors();
    selectedDishes.value = {};

    // Initialize dish categories if this is a catering service
    if (props.service?.category_name === 'Catering' && props.service?.catering_service?.dishes) {
        selectedDishes.value = {};
        Object.keys(props.service.catering_service.dishes).forEach(category => {
            selectedDishes.value[category] = [];
        });
        form.dishes = { ...selectedDishes.value };
    }
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

    // console.log(form);

    form.post(route('client.bookings.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            // Reset form
            form.reset();
        },
        onError: () => {
            isLoading.value = false;
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
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

// Expose methods for parent component
defineExpose({
    openModal,
    closeModal
});
</script>

<template>
    <!-- Modal Overlay -->
    <Transition name="modal-overlay">
        <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            @click.self="closeModal">

            <!-- Modal Content -->
            <Transition name="modal-content">
                <div v-if="isOpen" class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden">

                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-2xl font-bold">Create New Event</h2>
                                <p class="text-blue-100 mt-1">{{ service?.name }}</p>
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
                            <form v-if="currentStep === 1" key="step1" class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h3>

                                <!-- Event Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Event Name *
                                    </label>
                                    <input id="name" v-model="form.name" type="text" maxlength="255" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="{ 'border-red-500': form.errors.name }"
                                        placeholder="Enter event name" />
                                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <!-- Location -->
                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                        Location *
                                    </label>
                                    <input id="location" v-model="form.location" type="text" maxlength="255" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="{ 'border-red-500': form.errors.location }"
                                        placeholder="Enter event location" />
                                    <div v-if="form.errors.location" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.location }}
                                    </div>
                                </div>

                                <!-- Event Date and Time -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1">
                                            Event Date *
                                        </label>
                                        <input id="event_date" v-model="form.event_date" type="date" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            :class="{ 'border-red-500': form.errors.event_date }" />
                                        <div v-if="form.errors.event_date" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.event_date }}
                                        </div>
                                    </div>

                                    <div>
                                        <label for="event_time" class="block text-sm font-medium text-gray-700 mb-1">
                                            Event Time
                                        </label>
                                        <input id="event_time" v-model="form.event_time" type="time"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            :class="{ 'border-red-500': form.errors.event_time }" />
                                        <div v-if="form.errors.event_time" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.event_time }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <textarea id="description" v-model="form.description" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="{ 'border-red-500': form.errors.description }"
                                        placeholder="Enter event description"></textarea>
                                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.description }}
                                    </div>
                                </div>

                                <!-- Number of People (PAX) -->
                                <div v-if="service?.category_name === 'Catering'">
                                    <label for="pax" class="block text-sm font-medium text-gray-700 mb-1">
                                        Number of People (PAX)
                                    </label>
                                    <input id="pax" v-model="form.pax" type="number" min="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="{ 'border-red-500': form.errors.pax }"
                                        placeholder="Enter number of people" />
                                    <div v-if="form.errors.pax" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.pax }}
                                    </div>
                                </div>

                                <!-- Dishes -->
                                <div v-if="service?.category_name === 'Catering'">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="block text-sm font-medium text-gray-700">
                                            Catering Dishes
                                        </label>

                                        <!-- Show customization status -->
                                        <div v-if="!isCateringCustomizable" class="flex items-center text-amber-600">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-sm font-medium">Fixed Menu - No customization
                                                available</span>
                                        </div>
                                    </div>

                                    <!-- Dish Categories -->
                                    <div
                                        class="space-y-6 border border-gray-200 rounded-lg p-4 max-h-96 overflow-y-auto">

                                        <div v-for="(dishes, category) in service.catering_service.dishes"
                                            :key="category" class="space-y-3">

                                            <!-- Category Header -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <h4 class="text-base font-medium text-gray-900">{{ category }}</h4>
                                                    <span v-if="isCateringCustomizable"
                                                        class="bg-blue-100 text-blue-800 rounded px-2 py-1 text-xs font-medium">
                                                        Select up to {{ getSelectionLimit(category) }}
                                                    </span>
                                                    <span v-else
                                                        class="bg-gray-100 text-gray-600 rounded px-2 py-1 text-xs font-medium">
                                                        Included in package
                                                    </span>
                                                </div>

                                                <div v-if="isCateringCustomizable" class="flex items-center gap-3">
                                                    <span class="text-sm text-gray-600">
                                                        {{ getSelectedCount(category) }}/{{ getSelectionLimit(category)
                                                        }} selected
                                                    </span>
                                                    <button v-if="getSelectedCount(category) > 0"
                                                        @click="clearCategorySelection(category)" type="button"
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                        Clear
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Dishes Grid -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div v-for="dish in dishes" :key="dish"
                                                    @click="toggleDishSelection(category, dish)"
                                                    class="border rounded-lg p-3 transition-all duration-200" :class="{
                                                        // Customizable styles
                                                        'cursor-pointer border-blue-500 bg-blue-50': isCateringCustomizable && isDishSelected(category, dish),
                                                        'cursor-pointer border-gray-200 hover:border-blue-300': isCateringCustomizable && !isDishSelected(category, dish) && !isSelectionLimitReached(category),
                                                        'border-gray-200 opacity-50 cursor-not-allowed': isCateringCustomizable && !isDishSelected(category, dish) && isSelectionLimitReached(category),
                                                        // Non-customizable styles
                                                        'border-gray-200 bg-gray-50': !isCateringCustomizable
                                                    }">
                                                    <div class="flex items-center">
                                                        <!-- Selection indicator - only show if customizable -->
                                                        <div v-if="isCateringCustomizable" class="mr-3">
                                                            <div class="w-4 h-4 border-2 rounded flex items-center justify-center"
                                                                :class="{
                                                                    'bg-blue-500 border-blue-500': isDishSelected(category, dish),
                                                                    'border-gray-300': !isDishSelected(category, dish)
                                                                }">
                                                                <svg v-if="isDishSelected(category, dish)"
                                                                    class="w-3 h-3 text-white" viewBox="0 0 20 20"
                                                                    fill="currentColor">
                                                                    <path fill-rule="evenodd"
                                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </div>
                                                        </div>

                                                        <!-- Included indicator - only show if not customizable -->
                                                        <div v-else class="mr-3">
                                                            <div
                                                                class="w-4 h-4 bg-green-500 rounded flex items-center justify-center">
                                                                <svg class="w-3 h-3 text-white" viewBox="0 0 20 20"
                                                                    fill="currentColor">
                                                                    <path fill-rule="evenodd"
                                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </div>
                                                        </div>

                                                        <div class="flex-1">
                                                            <h5 class="text-sm font-medium"
                                                                :class="isCateringCustomizable ? 'text-gray-900' : 'text-gray-700'">
                                                                {{ dish }}
                                                            </h5>
                                                            <p v-if="!isCateringCustomizable"
                                                                class="text-xs text-gray-500 mt-1">
                                                                Included in fixed menu
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Selected Items - only show for customizable -->
                                            <div v-if="isCateringCustomizable && getSelectedCount(category) > 0"
                                                class="p-3 bg-gray-50 rounded-lg">
                                                <h6 class="text-sm font-medium text-gray-700 mb-2">
                                                    Selected from {{ category }}:
                                                </h6>
                                                <div class="flex flex-wrap gap-2">
                                                    <span v-for="dish in selectedDishes[category]" :key="dish"
                                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ dish }}
                                                        <button @click.stop="toggleDishSelection(category, dish)"
                                                            type="button" class="ml-1 hover:bg-blue-200 rounded p-0.5">
                                                            <svg class="w-3 h-3" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </Transition>

                        <!-- Step 2: Review & Confirm -->
                        <Transition name="step" mode="out-in">
                            <div v-if="currentStep === 2" key="step2" class="space-y-6">

                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Review & Confirm</h3>

                                <!-- Service Details -->
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-3">Service Details</h4>
                                    <div class="flex items-start space-x-4">
                                        <!-- Service Image -->
                                        <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden">
                                            <img v-if="service.image_url" :src="service.image_url" :alt="service.name"
                                                class="w-full h-full object-cover">
                                            <div v-else
                                                class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Service Info -->
                                        <div class="flex-1">
                                            <h5 class="text-lg font-semibold text-gray-900">{{ service.name }}</h5>
                                            <p class="text-sm text-gray-600 mb-2">{{ service.category_name }}</p>

                                            <!-- Catering Service Details -->
                                            <div v-if="service.category_name === 'Catering' && service.catering_service"
                                                class="text-sm text-gray-700 space-y-1">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Base Price: {{ formatPrice(service.catering_service.price) }}
                                                        per person</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Menu Type: {{ service.catering_service.is_customizable ?
                                                        'Customizable' : 'Fixed' }}</span>
                                                </div>
                                            </div>

                                            <!-- Photography Service Details -->
                                            <div v-else-if="service.category_name === 'Photography' && service.photography_service"
                                                class="text-sm text-gray-700 space-y-1">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Duration: {{ service.photography_service.duration }}
                                                        hours</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Style: {{ service.photography_service.style }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Equipment: {{ service.photography_service.equipment ?
                                                        'Included' : 'Not Included' }}</span>
                                                </div>
                                            </div>

                                            <!-- General Service Details -->
                                            <div v-else class="text-sm text-gray-700 space-y-1">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    <span>Base Price: {{ formatPrice(service.price) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected Dishes (for catering) -->
                                <div v-if="service.category_name === 'Catering' && Object.keys(selectedDishes).some(category => selectedDishes[category].length > 0)"
                                    class="bg-white border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-3">Selected Menu Items</h4>
                                    <div class="space-y-3">
                                        <div v-for="(dishes, category) in selectedDishes" :key="category">
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">{{ category }}</h5>
                                            <div class="flex flex-wrap gap-2">
                                                <span v-for="dish in dishes" :key="dish"
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ dish }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Event Summary -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium text-gray-900 mb-3">Event Summary</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Event Name:</span>
                                            <span class="font-medium">{{ form.name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Location:</span>
                                            <span class="font-medium">{{ form.location }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Date & Time:</span>
                                            <span class="font-medium">{{ form.event_date }} at {{ form.event_time
                                                }}</span>
                                        </div>
                                        <div v-if="form.pax" class="flex justify-between">
                                            <span class="text-gray-600">Number of Guests:</span>
                                            <span class="font-medium">{{ form.pax }} people</span>
                                        </div>
                                        <div v-if="form.description" class="flex justify-between">
                                            <span class="text-gray-600">Description:</span>
                                            <span class="font-medium text-right max-w-xs">{{ form.description }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price Breakdown -->
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Price Breakdown</h4>

                                    <div class="space-y-2 text-sm mb-3">
                                        <div v-if="service.category_name === 'Catering'" class="flex justify-between">
                                            <span class="text-gray-600">Base price ({{
                                                formatPrice(service.catering_service?.price || service.price) }} × {{
                                                    form.pax || 0 }} guests)</span>
                                            <span class="font-medium">{{ formatPrice((service.catering_service?.price ||
                                                service.price) * (form.pax || 0)) }}</span>
                                        </div>
                                        <div v-else class="flex justify-between">
                                            <span class="text-gray-600">Service fee</span>
                                            <span class="font-medium">{{ formatPrice(service.photography_service?.price
                                                || service.price) }}</span>
                                        </div>

                                        <!-- Additional charges could be added here -->
                                        <div class="flex justify-between text-green-600">
                                            <span>Discount</span>
                                            <span>-{{ formatPrice(0) }}</span>
                                        </div>
                                    </div>

                                    <div class="border-t border-blue-200 pt-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-semibold text-gray-900">Total Estimated
                                                Cost:</span>
                                            <span class="text-2xl font-bold text-blue-600">{{ formatPrice(totalPrice)
                                                }}</span>
                                        </div>
                                    </div>

                                    <p class="text-sm text-gray-600 mt-2">Final pricing may vary based on specific
                                        requirements</p>
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-amber-900 mb-2">Terms & Conditions</h4>
                                    <p class="text-xs text-amber-700">
                                        By submitting this booking request, you agree to our terms of service. A
                                        confirmation email will be sent
                                        upon acceptance of your request. Payment details and final confirmation will be
                                        provided by the service provider.
                                    </p>
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
                                {{ isLoading ? 'Creating Event...' : 'Create Event' }}
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

/* Custom scrollbar for service selection area */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Animation for modal */
.fixed {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}
</style>
