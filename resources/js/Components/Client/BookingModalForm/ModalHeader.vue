<script setup>
defineProps({
    currentStep: Number,
    totalSteps: Number
})

const emit = defineEmits(['close-modal'])
</script>

<template>
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Book Your Event</h2>
                <p class="text-blue-100 mt-1">Complete your booking in just a few steps</p>
            </div>
            <button @click="emit('close-modal')"
                class="text-white hover:text-gray-200 transition-colors p-1 rounded-full hover:bg-blue-800 w-8 h-8 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
</template>

<style></style>
