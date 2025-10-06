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
    },
    pax: {
        type: Number
    }
});

// Reactive state
const isOpen = ref(false);
const currentStep = ref(1);
const isLoading = ref(false);
const acceptedTerms = ref(false);

// Form data using Inertia's useForm
const form = useForm({
    name: '',
    location: '',
    event_date: props.date ?? '',
    event_time: props.time ?? '',
    description: '',
    pax: props.pax ?? 50,
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
    return props.service?.catering_service?.is_customizable;
});

// Get all dish categories
const dishCategories = computed(() => {
    if (props.service?.category_name === 'Catering' && props.service?.catering_service?.dishes) {
        return Object.keys(props.service.catering_service.dishes);
    }
    return [];
});

// Check if at least one dish is selected from EVERY category (for customizable catering)
const hasSelectedDishesFromAllCategories = computed(() => {
    // If it's not catering or not customizable, no dish selection required
    if (props.service?.category_name !== 'Catering' || !isCateringCustomizable.value) {
        return true;
    }

    // Check if every category has at least one dish selected
    return dishCategories.value.every(category =>
        selectedDishes.value[category] && selectedDishes.value[category].length > 0
    );
});

// Check which categories are missing selections
const missingSelectionCategories = computed(() => {
    if (props.service?.category_name !== 'Catering' || !isCateringCustomizable.value) {
        return [];
    }

    return dishCategories.value.filter(category =>
        !selectedDishes.value[category] || selectedDishes.value[category].length === 0
    );
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
            // For step 1, check basic form validity AND dish selection if it's customizable catering
            const basicInfoValid = form.name && form.location && form.event_date && form.event_time;

            // If it's catering and customizable, also check dish selection from all categories
            if (props.service?.category_name === 'Catering' && isCateringCustomizable.value) {
                return basicInfoValid && hasSelectedDishesFromAllCategories.value;
            }

            // For non-catering or non-customizable catering, just check basic info
            return basicInfoValid;

        case 2:
            // For step 2, check terms acceptance and dish selection (if applicable)
            if (props.service?.category_name === 'Catering' && isCateringCustomizable.value) {
                return hasSelectedDishesFromAllCategories.value && acceptedTerms.value;
            }
            return acceptedTerms.value;

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
    acceptedTerms.value = false;
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
        acceptedTerms.value = false;
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
    if (currentStep.value < totalSteps && isStepValid.value) {
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

// Computed property to format missing categories as text
const missingSelectionCategoriesText = computed(() => {
    return missingSelectionCategories.value.join(', ');
});
</script>

<template>
    <!-- Modal Overlay -->
    <Transition name="modal-overlay">
        <Teleport to="body">
            <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                @click.self="closeModal">

                <!-- Modal Content -->
                <Transition name="modal-content">
                    <div v-if="isOpen" class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden">

                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold">Book Your Event</h2>
                                    <p class="text-blue-100 mt-1">Complete your booking in just a few steps</p>
                                </div>
                                <button @click="closeModal"
                                    class="text-white hover:text-gray-200 transition-colors p-1 rounded-full hover:bg-blue-800 w-8 h-8 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Event Information</h3>

                                    <!-- Event Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Event Name *
                                        </label>
                                        <input id="name" v-model="form.name" type="text" maxlength="255" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                            :class="{ 'border-red-500': form.errors.name }"
                                            placeholder="e.g., Maria and Juan's Wedding Reception" />
                                        <div v-if="form.errors.name"
                                            class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ form.errors.name }}
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    <div>
                                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                            Event Location *
                                        </label>
                                        <input id="location" v-model="form.location" type="text" maxlength="255"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                            :class="{ 'border-red-500': form.errors.location }"
                                            placeholder="Enter the full address of your event" />
                                        <div v-if="form.errors.location"
                                            class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ form.errors.location }}
                                        </div>
                                    </div>

                                    <!-- Event Date and Time -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="event_date"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Event Date *
                                            </label>
                                            <input id="event_date" v-model="form.event_date" type="date" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                :class="{ 'border-red-500': form.errors.event_date }" />
                                            <div v-if="form.errors.event_date"
                                                class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                {{ form.errors.event_date }}
                                            </div>
                                        </div>

                                        <div>
                                            <label for="event_time"
                                                class="block text-sm font-medium text-gray-700 mb-2">
                                                Event Time *
                                            </label>
                                            <input id="event_time" v-model="form.event_time" type="time" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                                :class="{ 'border-red-500': form.errors.event_time }" />
                                            <div v-if="form.errors.event_time"
                                                class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                {{ form.errors.event_time }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                            Event Description
                                        </label>
                                        <textarea id="description" v-model="form.description" rows="3"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                            :class="{ 'border-red-500': form.errors.description }"
                                            placeholder="Tell us more about your event..."></textarea>
                                        <div v-if="form.errors.description"
                                            class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ form.errors.description }}
                                        </div>
                                    </div>

                                    <!-- Number of People (PAX) -->
                                    <div v-if="service?.category_name === 'Catering'">
                                        <label for="pax" class="block text-sm font-medium text-gray-700 mb-2">
                                            Number of Guests *
                                        </label>
                                        <input id="pax" v-model="form.pax" type="number" min="1" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                            :class="{ 'border-red-500': form.errors.pax }"
                                            placeholder="Enter the number of guests" />
                                        <div v-if="form.errors.pax" class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ form.errors.pax }}
                                        </div>
                                    </div>

                                    <!-- Dishes Section -->
                                    <div v-if="service?.category_name === 'Catering'">
                                        <div class="flex items-center justify-between mb-4">
                                            <label class="block text-lg font-semibold text-gray-900">
                                                Menu Selection
                                            </label>

                                            <!-- Show customization status -->
                                            <div v-if="!isCateringCustomizable"
                                                class="flex items-center text-amber-600 bg-amber-50 px-3 py-1 rounded-full">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span class="text-sm font-medium">Fixed Menu</span>
                                            </div>
                                        </div>

                                        <!-- Dish Categories -->
                                        <div
                                            class="space-y-6 border border-gray-200 rounded-lg p-6 max-h-96 overflow-y-auto bg-gray-50">

                                            <div v-for="(dishes, category) in service.catering_service.dishes"
                                                :key="category" class="space-y-4">

                                                <!-- Category Header -->
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <h4 class="text-lg font-semibold text-gray-900">{{ category }}
                                                        </h4>
                                                        <span v-if="isCateringCustomizable"
                                                            class="bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-xs font-medium">
                                                            Select up to {{ getSelectionLimit(category) }}
                                                        </span>
                                                        <span v-else
                                                            class="bg-gray-200 text-gray-700 rounded-full px-3 py-1 text-xs font-medium">
                                                            Included in package
                                                        </span>
                                                    </div>

                                                    <div v-if="isCateringCustomizable" class="flex items-center gap-3">
                                                        <span class="text-sm text-gray-600">
                                                            {{ getSelectedCount(category) }}/{{
                                                                getSelectionLimit(category)
                                                            }} selected
                                                        </span>
                                                        <button v-if="getSelectedCount(category) > 0"
                                                            @click="clearCategorySelection(category)" type="button"
                                                            class="text-red-600 hover:text-red-800 text-sm font-medium bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-colors">
                                                            Clear Selection
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Dishes Grid -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div v-for="dish in dishes" :key="dish"
                                                        @click="toggleDishSelection(category, dish)"
                                                        class="border rounded-lg p-4 transition-all duration-200 cursor-pointer"
                                                        :class="{
                                                            // Customizable styles
                                                            'border-blue-500 bg-blue-50 shadow-sm': isCateringCustomizable && isDishSelected(category, dish),
                                                            'border-gray-200 bg-white hover:border-blue-300 hover:shadow-md': isCateringCustomizable && !isDishSelected(category, dish) && !isSelectionLimitReached(category),
                                                            'border-gray-200 bg-gray-100 opacity-50 cursor-not-allowed': isCateringCustomizable && !isDishSelected(category, dish) && isSelectionLimitReached(category),
                                                            // Non-customizable styles
                                                            'border-gray-200 bg-white': !isCateringCustomizable
                                                        }">
                                                        <div class="flex items-start">
                                                            <!-- Selection indicator - only show if customizable -->
                                                            <div v-if="isCateringCustomizable" class="mr-3 mt-0.5">
                                                                <div class="w-5 h-5 border-2 rounded flex items-center justify-center transition-colors"
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
                                                            <div v-else class="mr-3 mt-0.5">
                                                                <div
                                                                    class="w-5 h-5 bg-green-500 rounded flex items-center justify-center">
                                                                    <svg class="w-3 h-3 text-white" viewBox="0 0 20 20"
                                                                        fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                </div>
                                                            </div>

                                                            <div class="flex-1">
                                                                <h5 class="text-base font-medium"
                                                                    :class="isCateringCustomizable ? 'text-gray-900' : 'text-gray-700'">
                                                                    {{ dish }}
                                                                </h5>
                                                                <p v-if="!isCateringCustomizable"
                                                                    class="text-sm text-gray-500 mt-1">
                                                                    Included in your package
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Selected Items - only show for customizable -->
                                                <div v-if="isCateringCustomizable && getSelectedCount(category) > 0"
                                                    class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                                                    <h6 class="text-sm font-medium text-blue-800 mb-2">
                                                        Selected from {{ category }}:
                                                    </h6>
                                                    <div class="flex flex-wrap gap-2">
                                                        <span v-for="dish in selectedDishes[category]" :key="dish"
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                            {{ dish }}
                                                            <button @click.stop="toggleDishSelection(category, dish)"
                                                                type="button"
                                                                class="ml-1.5 hover:bg-blue-200 rounded-full p-0.5 transition-colors">
                                                                <svg class="w-3.5 h-3.5" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                        clip-rule="evenodd" />
                                                                </svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Missing selection warning for this category -->
                                                <div v-if="isCateringCustomizable && getSelectedCount(category) === 0"
                                                    class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-amber-500 mr-2" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        <span class="text-amber-700 text-sm font-medium">Please select
                                                            at least one dish from {{ category }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dish Selection Requirement Message -->
                                        <div v-if="isCateringCustomizable && !hasSelectedDishesFromAllCategories"
                                            class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <div>
                                                    <span class="text-red-700 font-medium block mb-1">Menu Selection
                                                        Required</span>
                                                    <p class="text-red-600 text-sm">
                                                        Please select at least one dish from each category to continue:
                                                        <span class="font-medium">{{ missingSelectionCategoriesText
                                                        }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </Transition>

                            <!-- Step 2: Review & Confirm -->
                            <Transition name="step" mode="out-in">
                                <div v-if="currentStep === 2" key="step2" class="space-y-6">

                                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Review Your Booking</h3>

                                    <!-- Service Details -->
                                    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                                        <h4 class="font-semibold text-lg text-gray-900 mb-4">Service Details</h4>
                                        <div class="flex items-start space-x-4">
                                            <!-- Service Image -->
                                            <div
                                                class="flex-shrink-0 w-24 h-24 rounded-lg overflow-hidden border border-gray-200">
                                                <img v-if="service.image_url" :src="service.image_url"
                                                    :alt="service.name" class="w-full h-full object-cover">
                                                <div v-else
                                                    class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                    <svg class="w-10 h-10 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>

                                            <!-- Service Info -->
                                            <div class="flex-1">
                                                <h5 class="text-xl font-bold text-gray-900">{{ service.name }}</h5>
                                                <p class="text-sm text-gray-600 mb-3">{{ service.category_name }}</p>

                                                <!-- Catering Service Details -->
                                                <div v-if="service.category_name === 'Catering' && service.catering_service"
                                                    class="text-sm text-gray-700 space-y-2">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        <span>Base Price: {{ formatPrice(service.catering_service.price)
                                                        }}
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
                                                    class="text-sm text-gray-700 space-y-2">
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
                                                <div v-else class="text-sm text-gray-700 space-y-2">
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
                                        class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                                        <h4 class="font-semibold text-lg text-gray-900 mb-4">Selected Menu Items</h4>
                                        <div class="space-y-4">
                                            <div v-for="(dishes, category) in selectedDishes" :key="category">
                                                <h5 class="text-base font-medium text-gray-700 mb-2">{{ category }}</h5>
                                                <div class="flex flex-wrap gap-2">
                                                    <span v-for="dish in dishes" :key="dish"
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                        {{ dish }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Event Summary -->
                                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                                        <h4 class="font-semibold text-lg text-gray-900 mb-4">Event Summary</h4>
                                        <div class="space-y-3 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Event Name:</span>
                                                <span class="font-medium text-gray-900">{{ form.name }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Location:</span>
                                                <span class="font-medium text-gray-900">{{ form.location }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Date & Time:</span>
                                                <span class="font-medium text-gray-900">{{ form.event_date }} at {{
                                                    form.event_time
                                                }}</span>
                                            </div>
                                            <div v-if="form.pax" class="flex justify-between">
                                                <span class="text-gray-600">Number of Guests:</span>
                                                <span class="font-medium text-gray-900">{{ form.pax }} people</span>
                                            </div>
                                            <div v-if="form.description" class="flex justify-between">
                                                <span class="text-gray-600">Description:</span>
                                                <span class="font-medium text-gray-900 text-right max-w-xs">{{
                                                    form.description
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price Breakdown -->
                                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Price Breakdown</h4>

                                        <div class="space-y-3 text-sm mb-4">
                                            <div v-if="service.category_name === 'Catering'"
                                                class="flex justify-between">
                                                <span class="text-gray-600">Base price ({{
                                                    formatPrice(service.catering_service?.price || service.price) }} ×
                                                    {{
                                                        form.pax || 0 }} guests)</span>
                                                <span class="font-medium text-gray-900">{{
                                                    formatPrice((service.catering_service?.price ||
                                                        service.price) * (form.pax || 0)) }}</span>
                                            </div>
                                            <div v-else class="flex justify-between">
                                                <span class="text-gray-600">Service fee</span>
                                                <span class="font-medium text-gray-900">{{
                                                    formatPrice(service.photography_service?.price
                                                        || service.price) }}</span>
                                            </div>

                                            <!-- Additional charges could be added here -->
                                            <div class="flex justify-between text-green-600">
                                                <span>Discount</span>
                                                <span>-{{ formatPrice(0) }}</span>
                                            </div>
                                        </div>

                                        <div class="border-t border-blue-200 pt-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-lg font-semibold text-gray-900">Total Estimated
                                                    Cost:</span>
                                                <span class="text-2xl font-bold text-blue-600">{{
                                                    formatPrice(totalPrice)
                                                }}</span>
                                            </div>
                                        </div>

                                        <p class="text-sm text-gray-600 mt-3">Final pricing may vary based on specific
                                            requirements</p>
                                    </div>

                                    <!-- Terms and Conditions -->
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                                        <div class="flex items-start">
                                            <input id="terms" v-model="acceptedTerms" type="checkbox"
                                                class="mt-1 mr-3 h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                                            <label for="terms" class="text-sm text-gray-700">
                                                <span class="font-medium">I agree to the Terms and Conditions</span>
                                                <p class="mt-1 text-gray-600">
                                                    By submitting this booking request, you agree to our terms of
                                                    service.
                                                    A confirmation email will be sent upon acceptance of your request.
                                                    Payment details and final confirmation will be provided by the
                                                    service provider.
                                                </p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-6 py-5 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                            <button v-if="currentStep > 1" @click="prevStep"
                                class="px-5 py-2.5 text-gray-700 hover:text-gray-900 transition-colors font-medium flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </button>
                            <div v-else></div>

                            <div class="flex space-x-3">
                                <button @click="closeModal"
                                    class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                    Cancel
                                </button>

                                <button v-if="currentStep < totalSteps" @click="nextStep" :disabled="!isStepValid"
                                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed font-medium flex items-center">
                                    Continue
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>

                                <button v-else @click="submitBooking" :disabled="isLoading || !isStepValid"
                                    class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed font-medium flex items-center">
                                    <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    {{ isLoading ? 'Processing...' : 'Confirm Booking' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Teleport>
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
