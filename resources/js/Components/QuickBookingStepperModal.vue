<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import EventForm from './EventForm.vue';
import CategorySelection from './CategorySelection.vue';
import VendorServiceSelection from './VendorServiceSelection.vue';
import ReviewEvent from './ReviewEvent.vue';

const props = defineProps({
    categories: Array
})

const page = usePage()



const showModal = ref(false);

const open = () => {
    showModal.value = true;
    resetForm();
    // Disable body scroll
    // document.body.style.overflow = 'hidden';
};

const closeModal = () => {
    showModal.value = false;
    // Re-enable body scroll
    document.body.style.overflow = 'unset';
};

defineExpose({ open });

// Stepper setup
const steps = [
    { id: '1', name: 'Categories', status: 'current' },
    { id: '2', name: 'Event Info', status: 'upcoming' },
    { id: '3', name: 'Vendors', status: 'upcoming' },
    { id: '4', name: 'Confirm', status: 'upcoming' }
];

const currentStep = ref(0);



const eventForm = useForm({
    user_id: page.props.auth.user.id,
    name: '',
    location: '',
    event_date: '',
    event_time: '',
    description: '',
    final_notes: '',
    pax: null,
    vendors: [],
    dishes: []

})


const selectedCategories = ref([]);
const selectedServices = ref([]);
const selectedDishes = ref([]);


// Form methods
const resetForm = () => {
    currentStep.value = 0;
    steps.forEach((step, index) => {
        step.status = index === 0 ? 'current' : 'upcoming';
    });
    //reset the form here
    eventForm.reset()
    selectedCategories.value = [];
    selectedServices.value = [];
    selectedDishes.value = [];
};

const nextStep = () => {

    if (selectedCategories.value) {
        console.log('selected cats', selectedCategories.value)
    }

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
        // if (!eventForm.name || !eventForm.location || !eventForm.event_date) {
        //     alert('Please fill in all required event details');
        //     return false;
        // }
    } else if (currentStep.value === 1 && selectedCategories.value.length === 0) {
        alert('Please select at least one category');
        return false;
    } else if (currentStep.value === 2 && selectedServices.value.length === 0) {
        alert('Please select at least one vendor');
        return false;
    }
    return true;
};

const submitSelection = () => {

    eventForm.vendors = selectedServices.value
    eventForm.dishes = selectedDishes.value

    console.log(eventForm.dishes);
    console.log(eventForm.pax);

    eventForm.post(route('client.bookings.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // toast.success('Service created successfully');
            resetForm()
        },
        onError: () => {
            alert('Error');
        }
    })


    // Submit to backend here

    closeModal();

};

// Helper functions for the new UI
const canProceed = () => {
    if (currentStep.value === 0) {
        return selectedCategories.value.length > 0;
    } else if (currentStep.value === 1) {
        return eventForm.name && eventForm.location && eventForm.event_date;
    } else if (currentStep.value === 2) {
        return selectedServices.value.length === selectedCategories.value.length;
    }
    return true;
};

const getNextButtonText = () => {
    switch (currentStep.value) {
        case 0: return 'Event Details';
        case 1: return 'Select Vendors';
        case 2: return 'Review Event';
        default: return 'Next';
    }
};

const finalNotes = ref('');

</script>

<template>
    <!-- Modal Overlay -->
    <transition name="modal-backdrop">
        <div v-if="showModal" @click.self="closeModal"
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <!-- Modal Container -->
            <transition name="modal-content" appear>
                <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[95vh] overflow-hidden flex flex-col">
                    <!-- Modal Header -->
                    <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-5 flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Plan Event</h2>
                                <p class="text-gray-500 text-xs mt-1">Complete all steps to organize your event</p>
                            </div>
                            <button @click="closeModal"
                                class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="flex-1 overflow-y-auto">
                        <div class="px-6 py-6">
                            <!-- Progress Stepper -->
                            <div class="mb-8">
                                <div class="flex items-center justify-between relative">
                                    <!-- Progress Line Background -->
                                    <div class="absolute top-4 left-0 right-0 h-[2px] bg-gray-100 -z-10"></div>
                                    <!-- Active Progress Line -->
                                    <div class="absolute top-4 left-0 h-[2px] bg-[#239BA7] -z-10 transition-all duration-500"
                                        :style="{ width: `${(currentStep / (steps.length - 1)) * 100}%` }"></div>

                                    <div v-for="(step) in steps" :key="step.name"
                                        class="flex flex-col items-center relative z-10">
                                        <!-- Step Circle -->
                                        <div :class="{
                                            'bg-[#239BA7] text-white': step.status === 'current',
                                            'bg-[#E6F4F6] border border-[#239BA7] text-[#239BA7]': step.status === 'complete',
                                            'bg-white border border-gray-200 text-gray-400': step.status === 'upcoming'
                                        }"
                                            class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-medium transition-all mb-2">
                                            <span v-if="step.status === 'complete'">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            <span v-else>{{ step.id }}</span>
                                        </div>
                                        <!-- Step Label -->
                                        <span :class="{
                                            'text-[#239BA7] font-medium': step.status === 'current',
                                            'text-gray-900 font-medium': step.status === 'complete',
                                            'text-gray-400': step.status === 'upcoming'
                                        }" class="text-xs text-center max-w-16 leading-tight">
                                            {{ step.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Step Content Container -->
                            <div class="min-h-[400px]">
                                <!-- Step 1: Category Selection-->
                                <div v-if="currentStep === 0" class="step-content">

                                    <CategorySelection :categories="categories"
                                        v-model:selectedCategories="selectedCategories" />
                                </div>

                                <!-- Step 2:Event Information  -->
                                <div v-if="currentStep === 1" class="step-content">
                                    <EventForm :eventForm="eventForm" :selectedCategories="selectedCategories" />
                                </div>

                                <!-- Step 3: Vendor Selection -->
                                <div v-if="currentStep === 2" class="step-content">
                                    <div class="mb-6">
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">Select Vendors</h3>
                                        <p class="text-gray-500 text-sm">Choose vendors for each category</p>
                                    </div>

                                    <VendorServiceSelection :selectedCategories="selectedCategories"
                                        v-model:selectedServices="selectedServices"
                                        v-model:selectedDishes="selectedDishes" />
                                </div>

                                <!-- Step 4: Review & Confirmation -->
                                <div v-if="currentStep === 3" class="step-content">
                                    <ReviewEvent :selectedServices="selectedServices" v-model:eventForm="eventForm" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex-shrink-0">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                            <!-- Back Button -->
                            <button v-if="currentStep > 0" @click="prevStep"
                                class="order-2 sm:order-1 px-5 py-2.5 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Previous
                            </button>

                            <!-- Progress Text (Mobile) -->
                            <div class="order-3 sm:order-2 text-center text-xs text-gray-500 sm:hidden">
                                Step {{ currentStep + 1 }} of {{ steps.length }}
                            </div>

                            <!-- Next/Submit Button -->
                            <div class="order-1 sm:order-3">
                                <button v-if="currentStep < 3" @click="nextStep" :disabled="!canProceed()" :class="{
                                    'bg-[#239BA7] hover:bg-[#1D8A95] text-white': canProceed(),
                                    'bg-gray-100 text-gray-400 cursor-not-allowed': !canProceed()
                                }"
                                    class="w-full sm:w-auto px-6 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center justify-center">
                                    {{ getNextButtonText() }}
                                    <svg class="w-3.5 h-3.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>

                                <button v-else @click="submitSelection"
                                    class="w-full sm:w-auto px-6 py-2.5 bg-[#239BA7] hover:bg-[#1D8A95] text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Confirm Booking
                                </button>
                            </div>
                        </div>

                        <!-- Progress Text (Desktop) -->
                        <div class="hidden sm:block text-center text-xs text-gray-500 mt-3">
                            Step {{ currentStep + 1 }} of {{ steps.length }}
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>

<style scoped>
/* Backdrop transition */
.modal-backdrop-enter-active,
.modal-backdrop-leave-active {
    transition: opacity 0.2s ease;
}

.modal-backdrop-enter-from,
.modal-backdrop-leave-to {
    opacity: 0;
}

/* Modal content transition */
.modal-content-enter-active {
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.modal-content-leave-active {
    transition: all 0.2s cubic-bezier(0.4, 0, 1, 1);
}

.modal-content-enter-from {
    opacity: 0;
    transform: scale(0.95) translateY(-10px);
}

.modal-content-leave-to {
    opacity: 0;
    transform: scale(0.98) translateY(5px);
}

.step-content {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f8fafc;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
</style>
