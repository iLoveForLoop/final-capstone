<template>
    <div>
        <h2 class="text-2xl font-semibold mb-6">Services & Specialties</h2>

        <!-- Specialties Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Event Specialties</h3>
                <button v-if="isEditing" @click="showModal = true"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Add Specialty
                </button>
            </div>
            <div class="flex flex-wrap gap-3">
                <div v-for="(specialty, index) in formData.specialties" :key="index"
                    class="flex items-center bg-blue-100 text-blue-800 px-4 py-2 rounded-full">
                    <span>{{ specialty }}</span>
                    <button v-if="isEditing" @click="emit('remove-specialty', index)"
                        class="ml-2 text-blue-600 hover:text-red-600 transition-colors font-bold text-lg">
                        ×
                    </button>
                </div>
                <div v-if="formData.specialties.length === 0" class="text-gray-500 italic">
                    No specialties added yet
                </div>
            </div>
        </div>

        <!-- Modal for Adding Specialty -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showModal = false"></div>

            <!-- Modal Container -->
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Modal Panel -->
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-enter-active">
                    <!-- Header -->
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Add New Specialty
                                </h3>
                                <div class="mt-2">
                                    <input v-model="newSpecialty" type="text" placeholder="Enter specialty name"
                                        class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        @keyup.enter="addSpecialty" ref="specialtyInput">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="addSpecialty"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Add Specialty
                        </button>
                        <button type="button" @click="showModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue'

defineProps({
    formData: Object,
    isEditing: Boolean,
})

const emit = defineEmits(['add-specialty', 'remove-specialty'])

const showModal = ref(false)
const newSpecialty = ref('')
const specialtyInput = ref(null)

// Focus on input when modal opens
watch(showModal, async (newValue) => {
    if (newValue) {
        await nextTick()
        specialtyInput.value.focus()
    }
})

const addSpecialty = () => {
    if (newSpecialty.value.trim()) {
        emit('add-specialty', newSpecialty.value.trim())
        newSpecialty.value = ''
        showModal.value = false
    }
}
</script>

<style scoped>
/* Modal Animation Only */
.modal-enter-active {
    animation: modal-enter 0.3s ease-out;
}

@keyframes modal-enter {
    0% {
        opacity: 0;
        transform: scale(0.9) translateY(-10px);
    }

    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Backdrop Animation */
.fixed.inset-0 {
    animation: fade-in 0.2s ease-out;
}

@keyframes fade-in {
    0% {
        opacity: 0;
    }

    100% {
        opacity: 1;
    }
}
</style>
