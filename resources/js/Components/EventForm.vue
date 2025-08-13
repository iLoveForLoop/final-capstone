<script setup>
defineProps({
    eventForm: Object
})
</script>

<template>
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="mb-6">
            <h3 class="text-xl font-medium text-gray-900">Event Details</h3>
            <p class="text-gray-500 text-sm mt-1">Provide essential information about your event</p>
        </div>

        <!-- Form Fields -->
        <div class="space-y-5">
            <!-- Event Name -->
            <div class="form-group">
                <label for="event-name" class="form-label">
                    Event name
                    <span class="text-[#239BA7] ml-1">*</span>
                </label>
                <input id="event-name" type="text" v-model="eventForm.name" class="form-input"
                    placeholder="Wedding, corporate event, birthday...">
                <p class="form-hint">Give your event a descriptive name</p>
            </div>

            <!-- Location -->
            <div class="form-group">
                <label for="event-location" class="form-label">
                    Location
                    <span class="text-[#239BA7] ml-1">*</span>
                </label>
                <input id="event-location" type="text" v-model="eventForm.location" class="form-input"
                    placeholder="Venue name or full address">
                <p class="form-hint">Where your event will take place</p>
            </div>

            <!-- Date and Time Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Date -->
                <div class="form-group">
                    <label for="event-date" class="form-label">
                        Date
                        <span class="text-[#239BA7] ml-1">*</span>
                    </label>
                    <input id="event-date" type="date" v-model="eventForm.event_date" class="form-input">
                    <p class="form-hint">When your event will occur</p>
                </div>

                <!-- Time -->
                <div class="form-group">
                    <label for="event-time" class="form-label">
                        Start time
                    </label>
                    <input id="event-time" type="time" v-model="eventForm.event_time" class="form-input">
                    <p class="form-hint">Optional start time</p>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="event-description" class="form-label">
                    Additional details
                </label>
                <div class="relative">
                    <textarea id="event-description" rows="4" v-model="eventForm.description" class="form-textarea"
                        placeholder="Special requirements, theme, or other important details"></textarea>
                    <div class="absolute bottom-2 right-2 text-xs text-gray-400 bg-white px-1 rounded">
                        {{ eventForm.description?.length || 0 }}/500
                    </div>
                </div>
                <p class="form-hint">Help vendors understand your needs</p>
            </div>
        </div>

        <!-- Quick Tips Card -->
        <!-- <div class="bg-[#F5FBFB] border border-[#D6EDF0] rounded-lg p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="h-4 w-4 text-[#239BA7]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-[#239BA7] mb-2">Helpful tips</h4>
                    <ul class="text-xs text-gray-600 space-y-1.5">
                        <li class="flex items-start">
                            <span class="text-[#239BA7] mr-1.5">•</span> Include guest count and budget in description
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#239BA7] mr-1.5">•</span> Specify any venue restrictions or requirements
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#239BA7] mr-1.5">•</span> Mention preferred style or theme if relevant
                        </li>
                    </ul>
                </div>
            </div>
        </div> -->
    </div>
</template>

<style scoped>
/* Form Base Styles */
.form-group {
    @apply space-y-2;
}

.form-label {
    @apply text-sm font-medium text-gray-700 flex items-center;
}

.form-input {
    @apply w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#239BA7] focus:border-[#239BA7] transition-colors bg-white;
}

.form-textarea {
    @apply w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#239BA7] focus:border-[#239BA7] transition-colors bg-white resize-none;
}

.form-hint {
    @apply text-xs text-gray-500;
}

/* Date and Time Input Styling */
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
    @apply cursor-pointer opacity-70 hover:opacity-100;
    filter: invert(48%) sepia(13%) saturate(1519%) hue-rotate(143deg) brightness(90%) contrast(85%);
}

/* Animation for form fields */
.form-group {
    animation: fadeIn 0.3s ease-out;
    animation-fill-mode: both;
}

.form-group:nth-child(1) {
    animation-delay: 0.1s;
}

.form-group:nth-child(2) {
    animation-delay: 0.2s;
}

.form-group:nth-child(3) {
    animation-delay: 0.3s;
}

.form-group:nth-child(4) {
    animation-delay: 0.4s;
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
</style>
