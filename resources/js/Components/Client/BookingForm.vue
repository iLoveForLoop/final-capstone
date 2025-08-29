<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

// Props for available vendors/services data
const props = defineProps({
    service: {
        type: Object
    }
});

const form = useForm({
    name: '',
    location: '',
    event_date: '',
    event_time: '',
    description: '',
    pax: '',
    dishes: [],
    vendors: []
});

const isFormOpen = ref(false);
const isSubmitting = ref(false);




// Form submission
const submit = () => {
    isSubmitting.value = true;
    form.post('/events', {
        onSuccess: () => {
            close();
            // Reset form
            form.reset();
        },
        onError: () => {
            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};

const open = () => {
    isFormOpen.value = true;
    // Reset form when opening
    form.reset();
    form.clearErrors();
};

const close = () => {
    isFormOpen.value = false;
    form.reset();
    form.clearErrors();
};


defineExpose({
    open
});
</script>

<template>
    <!-- Modal Backdrop -->
    {{ console.log('Booking page: ', service.catering_dishes) }}
    <div v-if="isFormOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-800">Create New Event</h2>
                <button @click="close" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <!-- Event Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Event Name *
                    </label>
                    <input id="name" v-model="form.name" type="text" maxlength="255" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': form.errors.name }" placeholder="Enter event name" />
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                        {{ form.errors.name }}
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                        Location *
                    </label>
                    <input id="location" v-model="form.location" type="text" maxlength="255" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': form.errors.location }" placeholder="Enter event location" />
                    <div v-if="form.errors.location" class="mt-1 text-sm text-red-600">
                        {{ form.errors.location }}
                    </div>
                </div>

                <!-- Event Date and Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Event Date *
                        </label>
                        <input id="event_date" v-model="form.event_date" type="date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.event_date }" />
                        <div v-if="form.errors.event_date" class="mt-1 text-sm text-red-600">
                            {{ form.errors.event_date }}
                        </div>
                    </div>

                    <div>
                        <label for="event_time" class="block text-sm font-medium text-gray-700 mb-1">
                            Event Time
                        </label>
                        <input id="event_time" v-model="form.event_time" type="time"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            :class="{ 'border-red-500': form.errors.event_time }" />
                        <div v-if="form.errors.event_time" class="mt-1 text-sm text-red-600">
                            {{ form.errors.event_time }}
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea id="description" v-model="form.description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': form.errors.description }"
                        placeholder="Enter event description"></textarea>
                    <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                        {{ form.errors.description }}
                    </div>
                </div>

                <!-- Number of People (PAX) -->
                <div>
                    <label for="pax" class="block text-sm font-medium text-gray-700 mb-1">
                        Number of People (PAX)
                    </label>
                    <input id="pax" v-model="form.pax" type="number" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{ 'border-red-500': form.errors.pax }" placeholder="Enter number of people" />
                    <div v-if="form.errors.pax" class="mt-1 text-sm text-red-600">
                        {{ form.errors.pax }}
                    </div>
                </div>

                <!-- Dishes -->
                <div v-if="service.category_name === 'Catering'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Catering Dishes
                    </label>

                    <!-- Get to choose dishes if the condition is met -->

                </div>




                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button @click="close" type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                        :disabled="isSubmitting">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="isSubmitting || form.vendors.length === 0">
                        <span v-if="isSubmitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Creating Event...
                        </span>
                        <span v-else>Create Event</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar for service selection area */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Animation for modal */
.fixed {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}
</style>
