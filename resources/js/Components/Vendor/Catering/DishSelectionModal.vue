<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import {
    X,
    Check,
    Utensils,
    Trash2,
    ShoppingCart,
    AlertCircle,
    ChevronRight,
    Star,
    Users,
    Sparkles
} from 'lucide-vue-next';

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
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4">
            <!-- Backdrop -->
            <Transition name="fade">
                <div v-if="isOpen" class="absolute inset-0 bg-gray-600/40 backdrop-blur-sm" @click="closeModal"></div>
            </Transition>

            <!-- Modal -->
            <Transition name="slide">
                <div v-if="isOpen"
                    class="relative bg-white rounded-lg sm:rounded-xl max-w-full sm:max-w-6xl w-full max-h-[95vh] flex flex-col shadow-lg transform transition-all duration-300 border border-gray-200">

                    <!-- Modal Header -->
                    <div
                        class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100 bg-white rounded-t-lg sm:rounded-t-xl">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <div class="p-1.5 sm:p-2 bg-gray-100 rounded-lg">
                                    <Utensils :size="18" class="text-gray-700 sm:text-gray-800" />
                                </div>
                                <div>
                                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900">Customize Your Menu</h3>
                                    <p class="text-gray-600 text-xs sm:text-sm mt-1 flex items-center">
                                        <Sparkles :size="12" class="mr-1 text-gray-500 sm:text-gray-600" />
                                        Total selected: {{ getTotalSelectedCount() }} dishes
                                    </p>
                                </div>
                            </div>
                            <button @click="closeModal"
                                class="text-gray-400 hover:text-gray-600 p-1.5 sm:p-2 transition-all duration-200 hover:bg-gray-100 rounded-lg">
                                <X :size="18" class="sm:w-5 sm:h-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Modal Content -->
                    <div class="overflow-y-auto flex-1 px-3 sm:px-6 py-4 sm:py-6 bg-gray-50/30">
                        <TransitionGroup name="category" tag="div" class="space-y-4 sm:space-y-6">
                            <div v-for="(dishes, category) in service.catering_service.dishes" :key="category"
                                class="bg-white rounded-lg border border-gray-200 shadow-sm transition-all duration-300">

                                <!-- Category Header -->
                                <div class="p-3 sm:p-5 border-b border-gray-100 bg-white rounded-t-lg">
                                    <div
                                        class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4">
                                        <div class="flex items-center gap-3 sm:gap-4">
                                            <div class="flex items-center space-x-2 sm:space-x-3">
                                                <div
                                                    class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <Star :size="16" class="text-gray-700 sm:text-gray-800" />
                                                </div>
                                                <div>
                                                    <h4 class="text-base sm:text-lg font-semibold text-gray-900">{{
                                                        category }}</h4>
                                                    <div class="flex flex-wrap gap-2 mt-1">
                                                        <span
                                                            class="bg-gray-100 text-gray-700 rounded-full px-2 py-1 text-xs font-medium flex items-center">
                                                            <Check :size="10" class="mr-1" />
                                                            {{ dishes.length }} option
                                                        </span>
                                                        <span
                                                            class="bg-gray-50 text-gray-600 rounded-full px-2 py-1 text-xs font-medium flex items-center">
                                                            <Users :size="10" class="mr-1" />
                                                            Select up to {{ getSelectionLimit(category) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3 sm:gap-4 mt-2 sm:mt-0">
                                            <div class="text-right">
                                                <div class="text-xs sm:text-sm font-medium text-gray-700">
                                                    {{ getSelectedCount(category) }}/{{ getSelectionLimit(category) }}
                                                    selected
                                                </div>
                                                <div class="w-20 sm:w-24 bg-gray-200 rounded-full h-1.5 mt-1">
                                                    <div class="bg-gray-800 h-1.5 rounded-full transition-all duration-500 ease-out"
                                                        :style="{ width: `${(getSelectedCount(category) / getSelectionLimit(category)) * 100}%` }">
                                                    </div>
                                                </div>
                                            </div>
                                            <button v-if="getSelectedCount(category) > 0"
                                                @click="clearCategorySelection(category)"
                                                class="text-gray-500 hover:text-red-500 p-1.5 sm:p-2 transition-all duration-200 hover:bg-red-50 rounded-lg flex items-center space-x-1">
                                                <Trash2 :size="14" class="sm:w-4 sm:h-4" />
                                                <span
                                                    class="text-xs sm:text-sm font-medium hidden sm:block">Clear</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dishes Grid -->
                                <div class="p-3 sm:p-5">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2 sm:gap-3">
                                        <TransitionGroup name="dish" tag="div" class="contents">
                                            <div v-for="dish in dishes" :key="dish"
                                                @click="toggleDishSelection(category, dish)"
                                                class="group border rounded-lg p-3 sm:p-4 cursor-pointer transition-all duration-200 ease-in-out hover:shadow-sm"
                                                :class="{
                                                    'border-gray-800 bg-gray-50 shadow-sm': isDishSelected(category, dish),
                                                    'border-gray-200 hover:border-gray-400 bg-white': !isDishSelected(category, dish) && !isSelectionLimitReached(category),
                                                    'border-gray-100 opacity-50 cursor-not-allowed': !isDishSelected(category, dish) && isSelectionLimitReached(category)
                                                }">

                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-2 sm:space-x-3">
                                                        <div class="w-4 h-4 sm:w-5 sm:h-5 border rounded flex items-center justify-center transition-all duration-200 group-hover:border-gray-600"
                                                            :class="{
                                                                'bg-gray-800 border-gray-800': isDishSelected(category, dish),
                                                                'border-gray-300': !isDishSelected(category, dish)
                                                            }">
                                                            <Check v-if="isDishSelected(category, dish)" :size="10"
                                                                class="text-white transition-all duration-200 sm:w-3 sm:h-3" />
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <h5 class="font-medium text-sm sm:text-base transition-colors duration-200 line-clamp-1"
                                                                :class="{ 'text-gray-800': isDishSelected(category, dish), 'text-gray-900': !isDishSelected(category, dish) }">
                                                                {{ dish }}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div v-if="isDishSelected(category, dish)"
                                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex-shrink-0 ml-2">
                                                        <ChevronRight :size="14" class="text-gray-600 sm:w-4 sm:h-4" />
                                                    </div>
                                                </div>
                                            </div>
                                        </TransitionGroup>
                                    </div>

                                    <!-- Selected Items Preview -->
                                    <Transition name="fade">
                                        <div v-if="getSelectedCount(category) > 0"
                                            class="mt-3 sm:mt-4 p-2 sm:p-4 bg-gray-50 rounded-lg border border-gray-200 transition-all duration-200">
                                            <div class="flex items-center space-x-2 mb-2 sm:mb-3">
                                                <ShoppingCart :size="12"
                                                    class="text-gray-600 sm:w-4 sm:h-4 flex-shrink-0" />
                                                <h6 class="text-xs font-medium text-gray-900">Selected from {{ category
                                                    }}:</h6>
                                            </div>
                                            <div class="flex flex-wrap gap-1.5 sm:gap-2">
                                                <TransitionGroup name="tag">
                                                    <span v-for="dish in selectedDishes[category]" :key="dish"
                                                        class="inline-flex items-center px-2 py-0.5 sm:px-3 sm:py-1.5 rounded-full text-xs font-medium bg-gray-800 text-white border border-gray-800 transition-all duration-200 hover:bg-gray-700">
                                                        <Utensils :size="8"
                                                            class="mr-1 sm:mr-2 sm:w-3 sm:h-3 flex-shrink-0" />
                                                        <span
                                                            class="line-clamp-1 max-w-[80px] xs:max-w-[100px] sm:max-w-[120px] md:max-w-none">{{
                                                            dish }}</span>
                                                        <button @click.stop="toggleDishSelection(category, dish)"
                                                            class="ml-1 sm:ml-2 hover:bg-gray-600 rounded-full p-0.5 transition-colors duration-200 flex-shrink-0">
                                                            <X :size="8" class="text-white sm:w-3 sm:h-3" />
                                                        </button>
                                                    </span>
                                                </TransitionGroup>
                                            </div>
                                        </div>
                                    </Transition>
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="px-3 sm:px-6 py-4 sm:py-5 border-t border-gray-200 bg-white rounded-b-lg sm:rounded-b-xl">
                        <div class="flex flex-col lg:flex-row justify-between items-center gap-3 sm:gap-4">
                            <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4 w-full sm:w-auto">
                                <button @click="clearSelection" :disabled="getTotalSelectedCount() === 0"
                                    class="flex items-center space-x-2 px-3 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm text-gray-600 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto justify-center">
                                    <Trash2 :size="14" class="sm:w-4 sm:h-4" />
                                    <span>Clear All</span>
                                </button>

                                <div class="flex items-center space-x-2 text-xs sm:text-sm w-full sm:w-auto justify-center sm:justify-start"
                                    :class="hasValidSelections() ? 'text-green-600' : 'text-amber-600'">
                                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-full flex items-center justify-center"
                                        :class="hasValidSelections() ? 'bg-green-100' : 'bg-amber-100'">
                                        <Check v-if="hasValidSelections()" :size="12"
                                            class="text-green-600 sm:w-3.5 sm:h-3.5" />
                                        <AlertCircle v-else :size="12" class="text-amber-600 sm:w-3.5 sm:h-3.5" />
                                    </div>
                                    <span class="font-medium text-center sm:text-left">
                                        {{ hasValidSelections() ? 'All categories completed' :
                                            'Select dishes from all categories' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-2 sm:gap-3 w-full sm:w-auto">
                                <button @click="closeModal"
                                    class="flex-1 sm:flex-initial flex items-center justify-center space-x-2 px-3 py-2.5 sm:px-5 sm:py-2.5 text-xs sm:text-sm text-gray-700 font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-all duration-200 min-h-[44px]">
                                    <span>Cancel</span>
                                </button>
                                <button @click="saveSelection" :disabled="!hasValidSelections()"
                                    class="flex-1 sm:flex-initial flex items-center justify-center space-x-2 px-3 py-2.5 sm:px-5 sm:py-2.5 bg-gray-900 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-800 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md min-h-[44px]">
                                    <ShoppingCart :size="14" class="sm:w-4 sm:h-4" />
                                    <span>Save ({{ getTotalSelectedCount() }})</span>
                                </button>
                            </div>
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
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-leave-active {
    transition: all 0.25s cubic-bezier(0.4, 0, 1, 1);
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    transform: translateY(20px) scale(0.98);
}

/* Category transitions */
.category-enter-active,
.category-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.category-enter-from,
.category-leave-to {
    opacity: 0;
    transform: translateY(20px);
}

.category-move {
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Dish transitions */
.dish-enter-active,
.dish-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dish-enter-from,
.dish-leave-to {
    opacity: 0;
    transform: scale(0.9) translateY(8px);
}

.dish-move {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Tag transitions */
.tag-enter-active,
.tag-leave-active {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.tag-enter-from,
.tag-leave-to {
    opacity: 0;
    transform: scale(0.9) translateY(5px);
}

.tag-move {
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Line clamp utility */
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Mobile touch targets */
@media (max-width: 640px) {
    button {
        min-height: 44px;
    }

    .min-h-\[44px\] {
        min-height: 44px;
    }
}
</style>
