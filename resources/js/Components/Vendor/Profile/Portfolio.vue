<script setup>
import axios from 'axios'
import { onMounted } from 'vue'

defineProps({
    isEditing: Boolean
})

const emit = defineEmits(['show-image-upload', 'remove-image'])

const portfolioImages = defineModel('portfolioImages')




</script>

<template>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Portfolio Gallery</h2>
        <button v-if="isEditing && portfolioImages.length < 9" @click="emit('show-image-upload')"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Upload Images
        </button>
        <p v-else-if="isEditing" class="text-red-500 text-sm">
            You can only upload up to 9 images.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="(image, index) in portfolioImages" :key="index" class="relative group z-40">
            <!-- {{ console.log(image) }} -->
            <img :src="image.url" :alt="`Portfolio image ${index + 1}`" loading="lazy"
                class="w-full h-48 object-cover rounded-lg shadow-sm" />
            <div v-if="isEditing"
                class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                <button @click="emit('remove-image', index)"
                    class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Upload placeholder -->
        <div v-if="isEditing && portfolioImages.length < 9" @click="emit('show-image-upload')"
            class="border-2 border-dashed border-gray-300 rounded-lg h-48 flex flex-col items-center justify-center text-gray-500 hover:border-blue-400 transition-colors cursor-pointer">
            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span class="font-medium">Add Image</span>
        </div>

    </div>
</template>

<style></style>
