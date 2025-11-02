<script setup>
defineProps({
    formData: Object,
    vendor: Object,
    isEditing: Boolean
})

const emit = defineEmits(['show-profile-modal', 'toggle-edit', 'save-changes'])
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm mb-8 p-6">
        <div class="flex justify-between items-start">
            <div class="flex items-start space-x-6">
                <!-- Profile/Business Image -->
                <div class="relative">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 border-4 border-white shadow-lg">
                        <img v-if="formData.profileImage" :src="formData.profileImage" alt="Business Logo/Profile"
                            class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <!-- Edit Profile Image Button -->
                    <button v-if="isEditing" @click="emit('show-profile-modal')"
                        class="absolute -bottom-2 -right-2 bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 transition-colors shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                    </button>
                </div>

                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ formData.businessName }}</h1>

                    <span v-for="cat in vendor.categories" :key="cat"
                        class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">
                        {{ cat }}
                    </span>
                    <p class="text-gray-600 mt-2">{{ formData.contactPerson }}</p>
                    <p class="text-gray-500 mt-2">Manage your business information and showcase your
                        services</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <button @click="emit('toggle-edit')"
                    :class="isEditing ? 'bg-gray-600 hover:bg-gray-700' : 'bg-blue-600 hover:bg-blue-700'"
                    class="px-5 py-2 text-white rounded-lg font-medium transition-colors min-w-[120px]">
                    {{ isEditing ? 'Cancel' : 'Edit Profile' }}
                </button>
                <button v-if="isEditing" @click="emit('save-changes')"
                    class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors min-w-[120px]">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</template>

<style></style>
