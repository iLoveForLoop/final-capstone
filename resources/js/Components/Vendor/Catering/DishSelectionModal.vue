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

// Initialize selected dishes from modelValue
const selectedDishes = ref({ ...props.modelValue });

// Initialize empty objects for categories not in modelValue
const initializeCategories = () => {
    Object.keys(props.service.catering_service.dishes || {}).forEach(category => {
        if (!selectedDishes.value.hasOwnProperty(category)) {
            selectedDishes.value[category] = null;
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

const toggleDishSelection = (category, dish) => {
    if (selectedDishes.value[category] === dish) {
        selectedDishes.value[category] = null;
    } else {
        selectedDishes.value[category] = dish;
    }
};

const isDishSelected = (category, dish) => {
    return selectedDishes.value[category] === dish || false;
};

const saveSelection = () => {
    emit('update:modelValue', selectedDishes.value);
    closeModal();
};

const clearSelection = () => {
    Object.keys(selectedDishes.value).forEach(category => {
        selectedDishes.value[category] = null;
    });
};

// Modal control methods
const openModal = () => {
    isOpen.value = true;
};

const closeModal = () => {
    // if(props.isSelected && selectedDishes.value)
    const allNull = Object.values(selectedDishes.value).every(dish => dish === null);
    const hasNull = Object.values(selectedDishes.value).some(dish => dish === null);

    console.log('is selected: ', props.isSelected)

    if (allNull && props.isSelected) {
        alert('You cant leave this empty so already selected this!')
    } else if (hasNull && props.isSelected) {
        alert('You cant leave a category empty!')
    } else {
        console.log(selectedDishes.value)
        isOpen.value = false;
        emit('close');
    }



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
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Select Your Dishes</h3>
                <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <!-- {{ console.log('doshes: ', selectedDishes) }} -->
            <div class="overflow-y-auto flex-1 p-4">
                <div v-for="(dishes, category) in service.catering_service.dishes" :key="category" class="mb-6">
                    <h4 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                        <span
                            class="bg-[#239BA7] text-white rounded-full w-6 h-6 flex items-center justify-center mr-2">
                            {{ dishes.length }}
                        </span>
                        {{ category }}
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div v-for="dish in dishes" :key="dish" @click="toggleDishSelection(category, dish)"
                            class="border rounded-lg p-3 cursor-pointer transition-colors" :class="{
                                'border-[#239BA7] bg-[#E6F4F6]': isDishSelected(category, dish),
                                'border-gray-200 hover:border-[#239BA7]': !isDishSelected(category, dish)
                            }">
                            <div class="flex items-start">
                                <div class="mr-3 mt-0.5">
                                    <div class="w-5 h-5 border rounded flex items-center justify-center" :class="{
                                        'bg-[#239BA7] border-[#239BA7]': isDishSelected(category, dish),
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
                                <div>
                                    <h5 class="font-medium text-gray-900">{{ dish }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-gray-200 flex justify-between">
                <button @click="clearSelection"
                    class="px-4 py-2 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                    Clear All
                </button>
                <div class="flex gap-3">
                    <button @click="closeModal"
                        class="px-4 py-2 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                        Cancel
                    </button>
                    <button @click="saveSelection"
                        class="px-4 py-2 bg-[#239BA7] text-white font-medium rounded-lg hover:bg-[#1D8E99] transition-colors">
                        Save Selection
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
