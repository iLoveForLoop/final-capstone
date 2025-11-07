<script setup>
defineProps({
    currentStep: Number,
    totalSteps: Number
})

const emit = defineEmits(['close-modal'])
</script>

<template>
    <div class="bg-gray-900 p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Book Service</h2>
                <p class="text-gray-400 mt-1 text-sm">
                    Step-by-step booking process
                </p>
            </div>
            <button @click="emit('close-modal')"
                class="text-gray-400 hover:text-white transition-all duration-200 p-1 rounded hover:bg-gray-800 w-8 h-8 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Progress Bar -->
        <div class="mt-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Progress</span>
                <span>{{ currentStep }}/{{ totalSteps }}</span>
            </div>
            <div class="w-full bg-gray-800 rounded-full h-1">
                <div class="bg-gray-200 rounded-full h-1 transition-all duration-500 ease-out"
                    :style="{ width: (currentStep / totalSteps) * 100 + '%' }"></div>
            </div>
        </div>
    </div>
</template>

<style></style>
