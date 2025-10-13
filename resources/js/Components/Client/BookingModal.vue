<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ModalHeader from './BookingModalForm/ModalHeader.vue';
import GeneralForm from './BookingModalForm/GeneralForm.vue';
import CateringDetails from './BookingModalForm/CateringDetails.vue';
import ReviewConfirm from './BookingModalForm/ReviewConfirm.vue';
import ModalFooter from './BookingModalForm/ModalFooter.vue';

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
            const basicInfoValid = Boolean(
                form.name?.trim() &&
                form.location?.trim() &&
                form.event_date?.trim() &&
                form.event_time?.trim()
            );

            // If it's catering and customizable, also check dish selection from all categories
            if (props.service?.category_name === 'Catering' && isCateringCustomizable.value) {
                return basicInfoValid && hasSelectedDishesFromAllCategories.value;
            }

            console.log('Basic Info Valid: ', form.name && form.location && form.event_date && form.event_time);


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
    // Get base price as a number (fallback to 0 if not valid)
    let base = Number(
        props.service?.catering_service?.price ??
        props.service?.photography_service?.price ??
        props.service?.price ??
        0
    );

    // For catering, multiply by guest count (convert form.pax to number)
    if (props.service?.catering_service && form.pax) {
        const pax = Number(form.pax);
        if (!isNaN(pax)) {
            base *= pax;
        }
    }

    // Always return a valid number (even if NaN)
    return isNaN(base) ? 0 : base;
});



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
    console.log('here');

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
                        <ModalHeader :currentStep="currentStep" :totalSteps="totalSteps" @close-modal="closeModal" />

                        <!-- Modal Body -->
                        <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">

                            <!-- Step 1: Event Details -->
                            <Transition name="step" mode="out-in">
                                <form v-if="currentStep === 1" key="step1" class="space-y-6">
                                    <!-- General Details -->
                                    <GeneralForm :form="form" :vendorId="service.vendor.id" />

                                    <CateringDetails :service="service" :form="form"
                                        :isCateringCustomizable="isCateringCustomizable"
                                        :getSelectionLimit="getSelectionLimit" :getSelectedCount="getSelectedCount"
                                        :isDishSelected="isDishSelected"
                                        :isSelectionLimitReached="isSelectionLimitReached"
                                        :hasSelectedDishesFromAllCategories="hasSelectedDishesFromAllCategories"
                                        :missingSelectionCategoriesText="missingSelectionCategoriesText"
                                        :selectedDishes="selectedDishes"
                                        @clear-category-selection="clearCategorySelection"
                                        @toggle-dish-selection="toggleDishSelection" />
                                </form>
                            </Transition>

                            <!-- Step 2: Review & Confirm -->
                            <Transition name="step" mode="out-in">
                                <div v-if="currentStep === 2" key="step2" class="space-y-6">

                                    <ReviewConfirm :service="service" :form="form" :selectedDishes="selectedDishes"
                                        v-model:acceptedTerms="acceptedTerms" :totalPrice="totalPrice" />
                                </div>
                            </Transition>
                        </div>

                        <!-- Modal Footer -->
                        <ModalFooter :currentStep="currentStep" :totalSteps="totalSteps" :isStepValid="isStepValid"
                            :isLoading="isLoading" @prev-step="prevStep" @next-step="nextStep" @close-modal="closeModal"
                            @submit-booking="submitBooking" />
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
