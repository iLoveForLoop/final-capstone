<script setup>
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
    service: {
        type: Object,
        required: true
    },
    modelValue: {
        type: Object,
        default: () => ({})
    },
    isSelected: {
        type: Boolean,
        default: false
    },
});

const emit = defineEmits(['update:modelValue', 'close']);

// Modal visibility state
const isOpen = ref(false);

// Initialize selected dishes from modelValue - now supports multiple selections
const selectedDishes = ref({ ...props.modelValue });

// Get dish selection limits from service
const dishSelectionLimits = computed(() => {
    return props.service?.catering_service?.dish_selection_limits || {};
});

// Initialize empty arrays for categories not in modelValue
const initializeCategories = () => {
    Object.keys(props.service.catering_service.dishes || {}).forEach(category => {
        if (!selectedDishes.value.hasOwnProperty(category)) {
            selectedDishes.value[category] = [];
        }
    });
};

// Watch for changes in modelValue and update local state
watch(() => props.modelValue, (newValue) => {
    selectedDishes.value = { ...newValue };
    initializeCategories();
}, { deep: true });

// Initialize categories on component creation
initializeCategories();

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
};

// Clear selection for a category
const clearCategorySelection = (category) => {
    selectedDishes.value[category] = [];
};

// Clear all selections
const clearSelection = () => {
    Object.keys(selectedDishes.value).forEach(category => {
        selectedDishes.value[category] = [];
    });
};

// Check if all required selections are made
const hasValidSelections = () => {
    return Object.keys(props.service.catering_service.dishes || {}).every(category => {
        const selectedCount = getSelectedCount(category);
        const minRequired = Math.min(1, getSelectionLimit(category)); // At least 1 dish per category
        return selectedCount >= minRequired;
    });
};

// Get total selected dishes count
const getTotalSelectedCount = () => {
    return Object.values(selectedDishes.value).reduce((total, categoryDishes) => {
        return total + (Array.isArray(categoryDishes) ? categoryDishes.length : 0);
    }, 0);
};

// Save selection
const saveSelection = () => {
    emit('update:modelValue', selectedDishes.value);
    closeModal();
};

// Modal control methods
const openModal = () => {
    isOpen.value = true;
};

const closeModal = () => {
    if (props.isSelected && !hasValidSelections()) {
        alert('Please select at least one dish from each category!');
        return;
    }

    isOpen.value = false;
    emit('close');
};

const toggleModal = () => {
    isOpen.value = !isOpen.value;
    if (!isOpen.value) {
        emit('close');
    }
};

defineExpose({
    openModal,
    closeModal,
    toggleModal,
    selectedDishes,
    clearSelection,
    saveSelection
});
</script>

<template>
    <Transition name="modal" @after-leave="$emit('close')">
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Backdrop -->
            <Transition name="fade">
                <div v-if="isOpen" class="absolute inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
            </Transition>

            <!-- Modal -->
            <Transition name="slide">
                <div v-if="isOpen"
                    class="relative bg-white rounded-lg max-w-4xl w-full max-h-[90vh] flex flex-col shadow-xl transform transition-all duration-300">
                    <!-- Modal Header -->
                    <div class="px-4 md:px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="text-base md:text-lg font-semibold text-gray-900">Select Your Dishes</h3>
                            <p class="text-xs md:text-sm text-gray-600 mt-1">
                                Total selected: {{ getTotalSelectedCount() }} dishes
                            </p>
                        </div>
                        <button @click="closeModal"
                            class="text-gray-400 hover:text-gray-600 p-1 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="overflow-y-auto flex-1 px-4 md:px-6 py-4 md:py-6">
                        <TransitionGroup name="category" tag="div">
                            <div v-for="(dishes, category) in service.catering_service.dishes" :key="category"
                                class="mb-6 md:mb-8">

                                <!-- Category Header -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                                    <div class="flex items-center gap-3">
                                        <h4 class="text-base md:text-lg font-medium text-gray-900">{{ category }}</h4>
                                        <span class="bg-[#239BA7] text-white rounded px-2 py-1 text-xs font-medium">
                                            {{ dishes.length }} available
                                        </span>
                                        <span class="bg-gray-100 text-gray-700 rounded px-2 py-1 text-xs font-medium">
                                            Select up to {{ getSelectionLimit(category) }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <span class="text-xs md:text-sm text-gray-600">
                                            {{ getSelectedCount(category) }}/{{ getSelectionLimit(category) }} selected
                                        </span>
                                        <button v-if="getSelectedCount(category) > 0"
                                            @click="clearCategorySelection(category)"
                                            class="text-red-600 hover:text-red-800 text-xs md:text-sm font-medium transition-colors duration-200">
                                            Clear
                                        </button>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mb-4">
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-[#239BA7] h-1.5 rounded-full transition-all duration-500 ease-out"
                                            :style="{ width: `${(getSelectedCount(category) / getSelectionLimit(category)) * 100}%` }">
                                        </div>
                                    </div>
                                </div>

                                <!-- Dishes Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
                                    <TransitionGroup name="dish" tag="div" class="contents">
                                        <div v-for="dish in dishes" :key="dish"
                                            @click="toggleDishSelection(category, dish)"
                                            class="border rounded-lg p-3 cursor-pointer transition-all duration-300 ease-in-out transform hover:scale-[1.02]"
                                            :class="{
                                                'border-[#239BA7] bg-[#E6F4F6]': isDishSelected(category, dish),
                                                'border-gray-200 hover:border-[#239BA7]': !isDishSelected(category, dish) && !isSelectionLimitReached(category),
                                                'border-gray-200 opacity-50 cursor-not-allowed': !isDishSelected(category, dish) && isSelectionLimitReached(category)
                                            }">

                                            <div class="flex items-center">
                                                <div class="mr-3">
                                                    <div class="w-4 h-4 border-2 rounded flex items-center justify-center transition-all duration-300"
                                                        :class="{
                                                            'bg-[#239BA7] border-[#239BA7]': isDishSelected(category, dish),
                                                            'border-gray-300': !isDishSelected(category, dish)
                                                        }">
                                                        <svg v-if="isDishSelected(category, dish)"
                                                            class="w-3 h-3 text-white transition-all duration-300"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <h5 class="text-sm md:text-base font-medium text-gray-900 transition-colors duration-300"
                                                        :class="{ 'text-[#239BA7]': isDishSelected(category, dish) }">
                                                        {{ dish }}
                                                    </h5>
                                                    <p v-if="!isDishSelected(category, dish) && isSelectionLimitReached(category)"
                                                        class="text-xs text-gray-500 mt-1">
                                                        Selection limit reached
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </TransitionGroup>
                                </div>

                                <!-- Selected Items -->
                                <Transition name="fade">
                                    <div v-if="getSelectedCount(category) > 0"
                                        class="p-3 bg-gray-50 rounded-lg transition-all duration-300">
                                        <h6 class="text-sm font-medium text-gray-700 mb-2">Selected from {{ category }}:
                                        </h6>
                                        <div class="flex flex-wrap gap-2">
                                            <TransitionGroup name="tag">
                                                <span v-for="dish in selectedDishes[category]" :key="dish"
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-[#239BA7] text-white transition-all duration-300">
                                                    {{ dish }}
                                                    <button @click.stop="toggleDishSelection(category, dish)"
                                                        class="ml-1 hover:bg-white hover:bg-opacity-20 rounded p-0.5 transition-colors duration-200">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </span>
                                            </TransitionGroup>
                                        </div>
                                    </div>
                                </Transition>
                            </div>
                        </TransitionGroup>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="px-4 md:px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-3">
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            <button @click="clearSelection"
                                class="px-3 py-2 text-sm text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-all duration-300"
                                :disabled="getTotalSelectedCount() === 0">
                                Clear All
                            </button>

                            <div class="text-xs md:text-sm text-gray-600 transition-all duration-300">
                                <span v-if="hasValidSelections()" class="text-green-600 font-medium">
                                    ✓ All categories completed
                                </span>
                                <span v-else class="text-amber-600 font-medium">
                                    Please select dishes from all categories
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-3 w-full sm:w-auto">
                            <button @click="closeModal"
                                class="flex-1 sm:flex-initial px-4 py-2 text-sm text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-all duration-300">
                                Cancel
                            </button>
                            <button @click="saveSelection" :disabled="!hasValidSelections()"
                                class="flex-1 sm:flex-initial px-4 py-2 bg-[#239BA7] text-white text-sm font-medium rounded-lg hover:bg-[#1D8E99] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                Save Selection ({{ getTotalSelectedCount() }})
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
/* Modal transition */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

/* Backdrop transition */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Slide transition for modal */
.slide-enter-active {
    transition: all 0.3s ease-out;
}

.slide-leave-active {
    transition: all 0.3s ease-in;
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
}

/* Category transitions */
.category-enter-active,
.category-leave-active {
    transition: all 0.4s ease;
}

.category-enter-from,
.category-leave-to {
    opacity: 0;
    transform: translateY(20px);
}

.category-move {
    transition: transform 0.4s ease;
}

/* Dish transitions */
.dish-enter-active,
.dish-leave-active {
    transition: all 0.3s ease;
}

.dish-enter-from,
.dish-leave-to {
    opacity: 0;
    transform: scale(0.9);
}

.dish-move {
    transition: transform 0.3s ease;
}

/* Tag transitions */
.tag-enter-active,
.tag-leave-active {
    transition: all 0.3s ease;
}

.tag-enter-from,
.tag-leave-to {
    opacity: 0;
    transform: scale(0.8) translateY(10px);
}

.tag-move {
    transition: transform 0.3s ease;
}
</style>
