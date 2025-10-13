<script setup>
const props = defineProps({
    currentStep: Number,
    totalSteps: Number,
    isStepValid: {
        type: Boolean,
        default: false
    },
    isLoading: Boolean
})

console.log('Step valid? ', props.isStepValid)

const emit = defineEmits(['prev-step', 'close-modal', 'next-step', 'submit-booking'])
</script>

<template>
    <!-- Modal Footer -->
    <div class="px-6 py-5 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
        <button v-if="currentStep > 1" @click="emit('prev-step')"
            class="px-5 py-2.5 text-gray-700 hover:text-gray-900 transition-colors font-medium flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Previous
        </button>
        <div v-else></div>

        <div class="flex space-x-3">
            <button @click="emit('close-modal')"
                class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                Cancel
            </button>

            <button v-if="currentStep < totalSteps" @click="emit('next-step')" :disabled="!isStepValid"
                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed font-medium flex items-center">
                Continue
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <button v-else @click="emit('submit-booking')" :disabled="isLoading || !isStepValid"
                class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed font-medium flex items-center">
                <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                {{ isLoading ? 'Processing...' : 'Confirm Booking' }}
            </button>
        </div>
    </div>
</template>

<style></style>
