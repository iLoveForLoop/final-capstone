<script setup>
defineProps({
    errors: Object,
    permitFiles: Array
})

const emit = defineEmits(['handle-file-upload', 'remove-file'])
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Business Permits (Optional but
            recomended)</label>
        <div :class="['mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md hover:border-indigo-400',
            errors.businessPermits ? 'border-red-300' : 'border-gray-300']">
            <div class="space-y-1 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path
                        d="M9 12h6m6 0h6m-6 0v6m-6-6v6m6-6a3 3 0 013 3v6a3 3 0 01-3 3H9a3 3 0 01-3-3v-6a3 3 0 013-3" />
                </svg>
                <div class="flex text-sm text-gray-600">
                    <label for="permits-upload"
                        class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                        <span>Upload permits</span>
                        <input id="permits-upload" @change="emit('handle-file-upload', $event, 'permits')" type="file"
                            multiple accept=".pdf,.jpg,.jpeg,.png" class="sr-only">
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF, PNG, JPG up to 10MB each</p>
                <p v-if="permitFiles.length > 0" class="text-sm text-gray-600">
                    {{ permitFiles.length }} files uploaded
                </p>
            </div>
        </div>
        <p v-if="errors.businessPermits" class="mt-1 text-sm text-red-600">{{ errors.businessPermits
            }}</p>

        <!-- Permits Preview -->
        <div v-if="permitFiles.length > 0" class="mt-4 space-y-2">
            <div v-for="(file, index) in permitFiles" :key="index"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="ml-2 text-sm text-gray-700">{{ file.name }}</span>
                </div>
                <button @click="emit('remove-file', index, 'permits')" type="button"
                    class="text-red-500 hover:text-red-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<style></style>
