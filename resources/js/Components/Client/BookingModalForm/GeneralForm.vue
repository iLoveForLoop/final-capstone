<script setup>
import { ref, computed, onMounted } from 'vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import axios from 'axios';

const props = defineProps({
    form: Object,
    vendorId: [Number, String],
});

// Static booked dates for now
const booked = ref([])

// Local date/time state for the picker (Date objects)
const pickerDate = ref(null);
const pickerTime = ref(null);


if (props.form.event_date) {
    pickerDate.value = new Date(props.form.event_date);
}
if (props.form.event_time) {

    const [hours, minutes] = props.form.event_time.split(':');
    pickerTime.value = {
        hours: parseInt(hours),
        minutes: parseInt(minutes)
    };
}

// Watch for changes and convert to string format
const updateEventDate = (date) => {
    if (date) {
        // Convert Date object to YYYY-MM-DD string (same format as input type="date")
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        props.form.event_date = `${year}-${month}-${day}`;
    } else {
        props.form.event_date = null;
    }
};

const updateEventTime = (time) => {
    if (time) {
        // Convert time object to HH:MM format (24-hour, same as input type="time")
        const hours = String(time.hours).padStart(2, '0');
        const minutes = String(time.minutes).padStart(2, '0');
        props.form.event_time = `${hours}:${minutes}`;
    } else {
        props.form.event_time = null;
    }
};

// Disable booked or past dates
const isDateDisabled = (date) => {
    const today = new Date()
    today.setHours(0, 0, 0, 0)

    return (
        date < today ||
        booked.value.some((b) => {
            const bookedDate = new Date(b)
            return (
                date.getFullYear() === bookedDate.getFullYear() &&
                date.getMonth() === bookedDate.getMonth() &&
                date.getDate() === bookedDate.getDate()
            )
        })
    )
}


onMounted(async () => {
    try {
        const res = await axios.get(`/api/vendor/${props.vendorId}/booked-dates`);
        booked.value = res.data.bookedDates || []
    } catch (error) {
        console.error('Failed to fetch booked dates:', error)
    }

});
</script>

<template>
    <h3 class="text-xl font-semibold text-gray-900 mb-4">Event Information</h3>

    <!-- Event Name -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            Event Name *
        </label>
        <input id="name" v-model="form.name" type="text" maxlength="255" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            :class="{ 'border-red-500': form.errors.name }" placeholder="e.g., Maria and Juan's Wedding Reception" />
        <div v-if="form.errors.name" class="mt-2 text-sm text-red-600 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd" />
            </svg>
            {{ form.errors.name }}
        </div>
    </div>

    <!-- Location -->
    <div>
        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
            Event Location *
        </label>
        <input id="location" v-model="form.location" type="text" maxlength="255" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            :class="{ 'border-red-500': form.errors.location }" placeholder="Enter the full address of your event" />
        <div v-if="form.errors.location" class="mt-2 text-sm text-red-600 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd" />
            </svg>
            {{ form.errors.location }}
        </div>
    </div>

    <!-- Event Date and Time -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Event Date -->
        <div class="date-picker-wrapper">
            <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
            <VueDatePicker v-model="pickerDate" @update:model-value="updateEventDate" :disabled-dates="isDateDisabled"
                :enable-time-picker="false" :min-date="new Date()" placeholder="Select date" class="w-full" auto-apply
                :teleport="true" :markers="booked.map(date => ({ date, type: 'dot', color: 'red' }))" />
            <div v-if="form.errors.event_date" class="mt-2 text-sm text-red-600">
                {{ form.errors.event_date }}
            </div>
        </div>

        <!-- Event Time -->
        <div class="date-picker-wrapper">
            <label class="block text-sm font-medium text-gray-700 mb-2">Event Time *</label>
            <VueDatePicker v-model="pickerTime" @update:model-value="updateEventTime" time-picker
                placeholder="Select time" class="w-full" auto-apply :teleport="true" :is-24="false" />
            <div v-if="form.errors.event_time" class="mt-2 text-sm text-red-600">
                {{ form.errors.event_time }}
            </div>
        </div>
    </div>

    <!-- Description -->
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Event Description
        </label>
        <textarea id="description" v-model="form.description" rows="3"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
            :class="{ 'border-red-500': form.errors.description }"
            placeholder="Tell us more about your event..."></textarea>
        <div v-if="form.errors.description" class="mt-2 text-sm text-red-600 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd" />
            </svg>
            {{ form.errors.description }}
        </div>
    </div>
</template>

<style>
.date-picker-wrapper {
    position: relative;
    z-index: 1;
}

.booked-date {
    background-color: rgba(255, 0, 0, 0.2) !important;
    border-radius: 50% !important;
    position: relative;
}

.booked-date::after {
    content: '';
    position: absolute;
    bottom: 3px;
    left: 50%;
    transform: translateX(-50%);
    width: 6px;
    height: 6px;
    background-color: red;
    border-radius: 50%;
}

/* Enhanced Date Picker Styling */
:deep(.dp__input) {
    padding: 0.75rem 1rem !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    font-size: 0.875rem !important;
    transition: all 0.2s !important;
}

:deep(.dp__input:hover) {
    border-color: #9ca3af !important;
}

:deep(.dp__input:focus) {
    outline: none !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

:deep(.dp__menu) {
    border: 1px solid #e5e7eb !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    border-radius: 0.75rem !important;
    padding: 0.5rem !important;
}

:deep(.dp__calendar_header) {
    font-weight: 600 !important;
}

:deep(.dp__cell_inner) {
    border-radius: 0.5rem !important;
    transition: all 0.2s !important;
}

:deep(.dp__cell_inner:hover) {
    background-color: #eff6ff !important;
    color: #3b82f6 !important;
}

:deep(.dp__active_date) {
    background-color: #3b82f6 !important;
    color: white !important;
}

:deep(.dp__today) {
    border: 1px solid #3b82f6 !important;
}

:deep(.dp__disabled) {
    color: #d1d5db !important;
    cursor: not-allowed !important;
}

:deep(.dp__time_display) {
    padding: 0.5rem !important;
    font-size: 1.25rem !important;
    font-weight: 500 !important;
    color: #1f2937 !important;
}

:deep(.dp__button) {
    transition: all 0.2s !important;
}

:deep(.dp__button:hover) {
    background-color: #eff6ff !important;
    color: #3b82f6 !important;
}

:deep(.dp__action_row) {
    padding: 0.5rem !important;
}

:deep(.dp__selection_preview) {
    color: #3b82f6 !important;
    font-weight: 500 !important;
}

/* Time picker specific styling */
:deep(.dp__time_input) {
    font-size: 1.5rem !important;
    font-weight: 600 !important;
}

:deep(.dp__time_col_reg_block) {
    border-radius: 0.5rem !important;
}

:deep(.dp__pm_am_button) {
    background-color: #f3f4f6 !important;
    border-radius: 0.375rem !important;
    padding: 0.5rem 1rem !important;
    font-weight: 500 !important;
    transition: all 0.2s !important;
}

:deep(.dp__pm_am_button:hover) {
    background-color: #e5e7eb !important;
}

:deep(.dp__pm_am_button.dp__active_el) {
    background-color: #3b82f6 !important;
    color: white !important;
}

/* Booked date markers */
:deep(.dp__marker_dot) {
    background-color: #ef4444 !important;
    width: 6px !important;
    height: 6px !important;
    border-radius: 50% !important;
}

:deep(.dp__cell_inner.dp__marker_dot::after) {
    background-color: #ef4444 !important;
}
</style>
