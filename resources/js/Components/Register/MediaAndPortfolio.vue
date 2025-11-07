<script setup>
defineProps({
    errors: Object,
    profilePhoto: Object,
    servicePhotos: Array,
    profilePhotoUrl: String,
    servicePhotoUrls: Array,
    formData: Object

})

const emit = defineEmits(['handle-file-upload', 'remove-file'])
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo/Logo (Optional but
            recomended)</label>
        <div :class="['mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md hover:border-indigo-400',
            errors.profilePhoto ? 'border-red-300' : 'border-gray-300']">
            <div class="space-y-1 text-center">
                <svg v-if="!profilePhoto" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                    viewBox="0 0 48 48">
                    <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div v-if="profilePhoto" class="text-center">
                    <img :src="profilePhotoUrl" alt="Profile preview"
                        class="mx-auto h-20 w-20 object-cover rounded-full">
                </div>
                <div class="flex text-sm text-gray-600">
                    <label for="profile-upload"
                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                        <span>{{ profilePhoto ? 'Change photo' : 'Upload a file' }}</span>
                        <input id="profile-upload" @change="emit('handle-file-upload', $event, 'profile')" type="file"
                            accept="image/*" class="sr-only">
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
            </div>
        </div>
        <p v-if="errors.profilePhoto" class="mt-1 text-sm text-red-600">{{ errors.profilePhoto }}
        </p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Service Portfolio Photos
            (Optional but
            recomended)</label>
        <div :class="['mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md hover:border-indigo-400',
            errors.servicePhotos ? 'border-red-300' : 'border-gray-300']">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="flex text-sm text-gray-600">
                    <label for="service-photos"
                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                        <span>Upload photos</span>
                        <input id="service-photos" @change="emit('handle-file-upload', $event, 'service')" type="file"
                            multiple accept="image/*" class="sr-only">
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PNG, JPG up to 5MB each (min 5 photos)</p>
                <p v-if="servicePhotos.length > 0" class="text-sm text-gray-600">
                    {{ servicePhotos.length }} photos uploaded (minimum 5 required)
                </p>
            </div>
        </div>
        <p v-if="errors.servicePhotos" class="mt-1 text-sm text-red-600">{{ errors.servicePhotos }}
        </p>

        <!-- Service Photos Preview -->
        <div v-if="servicePhotos.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-for="(photo, index) in servicePhotos" :key="index" class="relative">
                <img :src="servicePhotoUrls[index]" alt="Service photo" class="h-24 w-full object-cover rounded-lg">
                <button @click="emit('remove-file', index, 'service')" type="button"
                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Facebook Page</label>
        <input v-model="formData.facebook" type="url"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="">
    </div>
</template>

<style></style>
