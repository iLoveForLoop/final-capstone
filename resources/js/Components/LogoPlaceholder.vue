<template>
    <div class="inline-block rounded-full">
        <img v-if="src" :src="src" :alt="alt" :class="imageClass" class="h-12 w-auto object-contain" />
        <div v-else class="h-10 w-8 bg-gray-200  flex items-center justify-center text-gray-500 text-sm font-medium">
            T
        </div>
    </div>
</template>

<script setup>
defineProps({
    src: {
        type: String,
        required: false,
        default: null
    },
    alt: {
        type: String,
        default: 'Logo'
    },
    imageClass: {
        type: String,
        default: ''
    }
})
</script>
