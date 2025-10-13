<script setup>
defineProps({
    service: Object,
    form: Object,
    isCateringCustomizable: Boolean,
    getSelectionLimit: Function,
    getSelectedCount: Function,
    isDishSelected: Function,
    isSelectionLimitReached: Function,
    hasSelectedDishesFromAllCategories: Boolean,
    missingSelectionCategoriesText: String,
    selectedDishes: Object
})

const emit = defineEmits(['clear-category-selection', 'toggle-dish-selection'])
</script>

<template>
    <!-- Number of People (PAX) -->
    <div v-if="service?.category_name === 'Catering'">
        <label for="pax" class="block text-sm font-medium text-gray-700 mb-2">
            Number of Guests *
        </label>
        <input id="pax" v-model="form.pax" type="number" min="1" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            :class="{ 'border-red-500': form.errors.pax }" placeholder="Enter the number of guests" />
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
        <div class="space-y-6 border border-gray-200 rounded-lg p-6 max-h-96 overflow-y-auto bg-gray-50">

            <div v-for="(dishes, category) in service.catering_service.dishes" :key="category" class="space-y-4">

                <!-- Category Header -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h4 class="text-lg font-semibold text-gray-900">{{ category }}
                        </h4>
                        <span v-if="isCateringCustomizable"
                            class="bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-xs font-medium">
                            Select up to {{ getSelectionLimit(category) }}
                        </span>
                        <span v-else class="bg-gray-200 text-gray-700 rounded-full px-3 py-1 text-xs font-medium">
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
                            @click="emit('clear-category-selection', category)" type="button"
                            class="text-red-600 hover:text-red-800 text-sm font-medium bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-colors">
                            Clear Selection
                        </button>
                    </div>
                </div>

                <!-- Dishes Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="dish in dishes" :key="dish" @click="emit('toggle-dish-selection', category, dish)"
                        class="border rounded-lg p-4 transition-all duration-200 cursor-pointer" :class="{
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
                                    <svg v-if="isDishSelected(category, dish)" class="w-3 h-3 text-white"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Included indicator - only show if not customizable -->
                            <div v-else class="mr-3 mt-0.5">
                                <div class="w-5 h-5 bg-green-500 rounded flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
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
                                <p v-if="!isCateringCustomizable" class="text-sm text-gray-500 mt-1">
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
                            <button @click.stop="emit('toggle-dish-selection', category, dish)" type="button"
                                class="ml-1.5 hover:bg-blue-200 rounded-full p-0.5 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
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
                        <svg class="w-4 h-4 text-amber-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
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
                <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
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
</template>

<style></style>
