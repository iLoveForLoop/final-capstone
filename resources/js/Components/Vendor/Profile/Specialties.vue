<script setup>
defineProps({
    formData: Object,
    isEditing: Boolean,
})

const emit = defineEmits(['add-specialty', 'remove-specialty'])
</script>

<template>
    <h2 class="text-2xl font-semibold mb-6">Services & Specialties</h2>

    <!-- Specialties Section -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Event Specialties</h3>
            <button v-if="isEditing" @click="emit('add-specialty')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Add Specialty
            </button>
        </div>
        <div class="flex flex-wrap gap-3">
            <div v-for="(specialty, index) in formData.specialties" :key="index"
                class="flex items-center bg-blue-100 text-blue-800 px-4 py-2 rounded-full">
                <span>{{ specialty }}</span>
                <button v-if="isEditing" @click="emit('remove-specialty', index)"
                    class="ml-2 text-blue-600 hover:text-red-600 transition-colors">
                    ×
                </button>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <!-- <div>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Services Offered</h3>
            <button v-if="isEditing" @click="addService"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Add Service
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            <div v-for="(service, index) in formData.services" :key="index"
                class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                <span>{{ service }}</span>
                <button v-if="isEditing" @click="removeService(index)"
                    class="text-gray-400 hover:text-red-600 transition-colors">
                    ×
                </button>
            </div>
        </div>
    </div> -->
</template>

<style></style>
