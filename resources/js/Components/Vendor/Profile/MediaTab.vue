<script setup>

defineProps({
    isEditing: Boolean
})

const emit = defineEmits(['show-video-modal', 'remove-video'])

const showcaseVideos = defineModel('showcaseVideos');




</script>

<template>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Showcase Videos</h2>
        <button v-if="isEditing && showcaseVideos.length < 1" @click="emit('show-video-modal')"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Add Video
        </button>
        <p v-else-if="isEditing" class="text-red-500 text-sm">
            Only one showcase video is allowed.
        </p>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div v-for="video in showcaseVideos" :key="video.id" class="bg-gray-50 rounded-lg p-6">
            <div class="flex justify-between items-start mb-3">
                <h3 class="font-medium text-lg">{{ video.title }}</h3>
                <button v-if="isEditing" @click="emit('remove-video', video.id)"
                    class="text-gray-400 hover:text-red-600 transition-colors">
                    ×
                </button>
            </div>
            <div class="bg-gray-200 rounded-lg h-32 flex items-center justify-center mb-3">
                <video v-if="video.url" :src="video.url" class="w-full h-full object-cover rounded-lg" controls></video>
                <svg v-else class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                        clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-sm text-gray-600 truncate">{{ video.title }}</p>
        </div>
    </div>
</template>

<style></style>
