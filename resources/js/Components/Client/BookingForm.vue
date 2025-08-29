<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

// Props for available vendors/services data
const props = defineProps({
    service: {
        type: Object
    }
});

const form = useForm({
    name: '',
    location: '',
    event_date: '',
    event_time: '',
    description: '',
    pax: '',
    dishes: {},
    vendors: []
});

const isFormOpen = ref(false);
const isSubmitting = ref(false);

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

// Form submission
const submit = () => {
    isSubmitting.value = true;
    form.post('/events', {
        onSuccess: () => {
            close();
            // Reset form
            form.reset();
        },
        onError: () => {
            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};

const open = () => {
    isFormOpen.value = true;
    // Reset form when opening
    form.reset();
    form.clearErrors();

    // Initialize dish categories if this is a catering service
    if (props.service?.category_name === 'Catering' && props.service?.catering_service?.dishes) {
        selectedDishes.value = {};
        Object.keys(props.service.catering_service.dishes).forEach(category => {
            selectedDishes.value[category] = [];
        });
        form.dishes = { ...selectedDishes.value };
    }
};

const close = () => {
    isFormOpen.value = false;
    form.reset();
    form.clearErrors();
    selectedDishes.value = {};
};

defineExpose({
    open
});
</script>

<template>
    <!-- Modal Backdrop -->
    <div v-if="isFormOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">Create New Event</h2>
                <button @click="close" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <!-- Event Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Event Name *
                    </label>
                    <input id="name" v-model="form.name" type="text" maxlength="255" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': form.errors.name }" placeholder="Enter event name" />
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
                        :class="{ 'border-red-500': form.errors.location }" placeholder="Enter event location" />
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
                <div v-if="service.category_name === 'Catering'">
                    <label for="pax" class="block text-sm font-medium text-gray-700 mb-1">
                        Number of People (PAX)
                    </label>
                    <input id="pax" v-model="form.pax" type="number" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': form.errors.pax }" placeholder="Enter number of people" />
                    <div v-if="form.errors.pax" class="mt-1 text-sm text-red-600">
                        {{ form.errors.pax }}
                    </div>
                </div>

                <!-- Dishes -->
                <!-- {{ console.log(service.catering_service.is_customizable) }} -->
                <div v-if="service.category_name === 'Catering'">
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
                            <span class="text-sm font-medium">Fixed Menu - No customization available</span>
                        </div>
                    </div>

                    <!-- Dish Categories -->
                    <div class="space-y-6 border border-gray-200 rounded-lg p-4 max-h-96 overflow-y-auto">

                        <div v-for="(dishes, category) in service.catering_service.dishes" :key="category"
                            class="space-y-3">

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
                                        {{ getSelectedCount(category) }}/{{ getSelectionLimit(category) }} selected
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
                                <div v-for="dish in dishes" :key="dish" @click="toggleDishSelection(category, dish)"
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
                                                <svg v-if="isDishSelected(category, dish)" class="w-3 h-3 text-white"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Included indicator - only show if not customizable -->
                                        <div v-else class="mr-3">
                                            <div class="w-4 h-4 bg-green-500 rounded flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
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
                                            <p v-if="!isCateringCustomizable" class="text-xs text-gray-500 mt-1">
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
                                        <button @click.stop="toggleDishSelection(category, dish)" type="button"
                                            class="ml-1 hover:bg-blue-200 rounded p-0.5">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
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

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button @click="close" type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                        :disabled="isSubmitting">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="isSubmitting">
                        <span v-if="isSubmitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Creating Event...
                        </span>
                        <span v-else>Create Event</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
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
