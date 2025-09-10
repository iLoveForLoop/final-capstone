<template>
    <div class="notification-container" :class="`notification--${type}`">
        <div class="notification-content">
            <!-- Animated Icon -->
            <div class="notification-icon">
                <svg v-if="type === 'error'" class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M12 8V12V8ZM12 16H12.01H12ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <svg v-else-if="type === 'success'" class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.709 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24015C11.8996 1.76493 14.1003 1.98232 16.07 2.86"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                <svg v-else class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>

            <!-- Message Content -->
            <div class="notification-text">
                <h3 class="notification-title">{{ title }}</h3>
                <p class="notification-message">{{ message }}</p>
            </div>

            <!-- Progress Bar (handled by Toastification duration) -->
            <div v-if="progressBar" class="notification-progress">
                <div class="notification-progress-bar" :style="{ width: `${progress}%` }"></div>
            </div>

            <!-- Close Button -->
            <button v-if="closable" @click="closeToast" class="notification-close">
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { useToast } from "vue-toastification";

// Toastification automatically injects these props
const props = defineProps({
    type: { type: String, default: "info" },
    title: { type: String, default: "Notification" },
    message: { type: String, default: "Something happened!" },
    closable: { type: Boolean, default: true },
    progressBar: { type: Boolean, default: true },
    toastId: String,   // comes from Toastification
    closeToast: Function, // provided by Toastification
});

const { updateDefaults } = useToast();

// progress bar animation handled by Toastification internally,
// but you can still simulate if you want custom timing:
const progress = 100;
</script>

<style scoped>
.notification-container {
    @apply rounded-xl overflow-hidden shadow-2xl w-full max-w-xs;
}

.notification-content {
    @apply relative p-4 flex items-start gap-3;
}

.notification-icon {
    @apply flex-shrink-0 mt-0.5;
}

.notification-text {
    @apply flex-1;
}

.notification-title {
    @apply font-bold text-sm leading-tight;
}

.notification-message {
    @apply text-sm opacity-90 mt-1;
}

.notification-progress {
    @apply absolute bottom-0 left-0 w-full h-1 bg-black bg-opacity-10;
}

.notification-progress-bar {
    @apply h-full transition-all duration-100 ease-linear bg-white;
}

.notification-close {
    @apply text-current opacity-70 hover:opacity-100 transition-opacity duration-200;
}

/* Variants */
.notification--error {
    @apply bg-gradient-to-r from-red-500 to-rose-600 text-white;
}

.notification--success {
    @apply bg-gradient-to-r from-emerald-500 to-green-600 text-white;
}

.notification--warning {
    @apply bg-gradient-to-r from-amber-500 to-yellow-600 text-white;
}

.notification--info {
    @apply bg-gradient-to-r from-blue-500 to-cyan-600 text-white;
}
</style>
