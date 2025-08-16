<script setup>
const props = defineProps({
    eventForm: Object,
    selectedCategories: {
        type: Array
    }
})

function doesCategoryExist(categoryName) {
    return props.selectedCategories.some(category => category.name == categoryName);
}

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

            <!-- Date, Time, and Guest Count Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
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

                <!-- Dynamic Catering Guest Count - Positioned naturally in the grid -->
                <div class="form-group catering-field" v-if="doesCategoryExist('Catering')" key="catering-pax">
                    <label for="event-pax" class="form-label">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Guest count
                        </div>
                        <!-- <span class=" ml-1">*</span> -->
                    </label>
                    <div class="relative">
                        <input id="event-pax" type="number" v-model="eventForm.pax"
                            class="form-input catering-input pr-16" placeholder="50" min="1" max="5000">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-sm text-gray-500 font-medium">guests</span>
                        </div>
                    </div>
                    <p class="form-hint text-[#239BA7]">If you have exact guest </p>
                </div>
            </div>

            <!-- Catering Enhancement Banner -->
            <!-- <div class="catering-banner" v-if="doesCategoryExist('Catering')" key="catering-banner">
                <div
                    class="bg-gradient-to-r from-[#239BA7]/5 to-[#239BA7]/10 border border-[#239BA7]/20 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-8 h-8 bg-[#239BA7] rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-[#239BA7] mb-1">
                                Catering service selected
                            </h4>
                            <p class="text-xs text-gray-600 mb-3">
                                Help caterers provide accurate quotes by including additional details below
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-600">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-[#239BA7] rounded-full"></span>
                                    <span>Dietary restrictions or preferences</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-[#239BA7] rounded-full"></span>
                                    <span>Meal type (breakfast/lunch/dinner)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-[#239BA7] rounded-full"></span>
                                    <span>Service style preference</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-[#239BA7] rounded-full"></span>
                                    <span>Budget range (optional)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Description with enhanced placeholder for catering -->
            <div class="form-group">
                <label for="event-description" class="form-label">
                    Additional details
                </label>
                <div class="relative">
                    <textarea id="event-description" rows="4" v-model="eventForm.description" class="form-textarea"
                        placeholder='Special requirements, theme, or other important details'>
                    </textarea>
                    <div class="absolute bottom-2 right-2 text-xs text-gray-400 bg-white px-2 py-1 rounded shadow-sm">
                        {{ eventForm.description?.length || 0 }}/500
                    </div>
                </div>
                <p class="form-hint" :class="{ 'text-[#239BA7]': doesCategoryExist('Catering') }">Help vendors
                    understand your needs
                </p>
            </div>
        </div>

        <!-- Tips Card with dynamic content -->
        <div class="bg-[#F5FBFB] border border-[#D6EDF0] rounded-lg p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="h-4 w-4 text-[#239BA7]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-[#239BA7] mb-2">
                        <!-- {{ doesCategoryExist('Catering') ? 'Catering tips for better quotes' : 'Helpful tips' }}  -->
                        Helpful tips
                    </h4>
                    <ul class="text-xs text-gray-600 space-y-1.5">
                        <!-- <template v-if="doesCategoryExist('Catering')">
                            <li class="flex items-start">
                                <span class="text-[#239BA7] mr-1.5">•</span> Be as accurate as possible with guest count
                            </li>
                            <li class="flex items-start">
                                <span class="text-[#239BA7] mr-1.5">•</span> Mention any food allergies or dietary
                                restrictions
                            </li>
                            <li class="flex items-start">
                                <span class="text-[#239BA7] mr-1.5">•</span> Specify if you need serving equipment or
                                staff
                            </li>
                            <li class="flex items-start">
                                <span class="text-[#239BA7] mr-1.5">•</span> Include your preferred budget range
                            </li>
                        </template>
<template v-else> -->
                        <li class="flex items-start">
                            <span class="text-[#239BA7] mr-1.5">•</span> Include guest count and budget in
                            description
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#239BA7] mr-1.5">•</span> Specify any venue restrictions or
                            requirements
                        </li>
                        <li class="flex items-start">
                            <span class="text-[#239BA7] mr-1.5">•</span> Mention preferred style or theme if
                            relevant
                        </li>
                        <!-- </template> -->
                    </ul>
                </div>
            </div>
        </div>
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

/* Enhanced Catering Field Styles */
.catering-field {
    @apply relative;
    animation: slideInFromRight 0.4s ease-out;
}

.catering-field .form-label {
    @apply font-semibold;
}

.catering-input {
    @apply focus:border-[#239BA7] focus:ring-[#239BA7]/20 font-medium;
    box-shadow: 0 1px 3px rgba(35, 155, 167, 0.1);
}

.catering-input:focus {
    @apply ring-2;
}

/* Catering Banner Styles */
.catering-banner {
    animation: slideInFromTop 0.5s ease-out;
}

/* Date and Time Input Styling */
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
    @apply cursor-pointer opacity-70 hover:opacity-100;
    filter: invert(48%) sepia(13%) saturate(1519%) hue-rotate(143deg) brightness(90%) contrast(85%);
}

/* Number input styling */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
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

.form-group:nth-child(5) {
    animation-delay: 0.5s;
}

/* Keyframe animations */
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

@keyframes slideInFromRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInFromTop {
    from {
        opacity: 0;
        transform: translateY(-15px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .grid-cols-3 {
        @apply grid-cols-1;
    }

    .catering-field {
        @apply mt-3;
    }
}

/* Smooth transitions for dynamic content */
.catering-field,
.catering-banner {
    transition: all 0.3s ease-in-out;
}

/* Enhanced focus states for better accessibility */
.catering-input:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(35, 155, 167, 0.15);
}
</style>
