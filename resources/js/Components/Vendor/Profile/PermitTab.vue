<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Permits & Documents</h3>
                <p class="text-sm text-gray-600">Manage your business permits and legal documents</p>
            </div>

            <button v-if="isEditing" @click="$emit('show-permit-upload')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Upload Files</span>
            </button>
        </div>

        <!-- Files Grid -->
        <div v-if="permitFiles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="(file, index) in permitFiles" :key="file.id || index"
                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <!-- Image Preview -->
                <div v-if="isImageFile(file)" class="mb-3">
                    <img :src="file.url || file.original_url" :alt="file.name"
                        class="w-full h-32 object-cover rounded-lg border border-gray-200" />
                </div>

                <!-- Document Icon -->
                <div v-else class="mb-3 flex justify-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                </div>

                <!-- File Info -->
                <div class="space-y-2">
                    <h4 class="font-medium text-gray-900 truncate text-sm">
                        {{ file.name }}
                    </h4>
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>{{ formatFileSize(file.size) }}</span>
                        <span class="px-2 py-1 bg-gray-100 rounded-full text-gray-600 capitalize">
                            {{ getFileType(file).toLowerCase() }}
                        </span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2 pt-2">
                        <!-- View Button -->
                        <a :href="file.url || file.original_url" target="_blank"
                            class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-1 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <span>View</span>
                        </a>

                        <!-- Download Button -->
                        <a :href="file.url || file.original_url" :download="file.name"
                            class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center space-x-1 text-sm"
                            title="Download file">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        </a>

                        <!-- Remove Button (only in edit mode) -->
                        <button v-if="isEditing" @click.stop="$emit('remove-permit', index)"
                            class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors flex items-center justify-center space-x-1 text-sm"
                            title="Remove file">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No permit files uploaded</h3>
            <p class="text-gray-500 mb-4">Upload your business permits and legal documents</p>
            <button v-if="isEditing" @click="$emit('show-permit-upload')"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Upload Files
            </button>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    permitFiles: {
        type: Array,
        default: () => []
    },
    isEditing: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['show-permit-upload', 'remove-permit']);

const formatFileSize = (bytes) => {
    if (!bytes) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const isImageFile = (file) => {
    const mimeType = file.mime_type || file.type;
    const fileName = file.name?.toLowerCase() || '';
    return mimeType?.includes('image') ||
        file.type === 'image' ||
        /\.(jpg|jpeg|png|gif|bmp|webp|svg)$/i.test(fileName);
};

const isPdfFile = (file) => {
    const mimeType = file.mime_type || file.type;
    const fileName = file.name?.toLowerCase() || '';
    return mimeType?.includes('pdf') ||
        /\.pdf$/i.test(fileName);
};

const isDocumentFile = (file) => {
    const mimeType = file.mime_type || file.type;
    const fileName = file.name?.toLowerCase() || '';
    return mimeType?.includes('document') ||
        mimeType?.includes('word') ||
        mimeType?.includes('excel') ||
        mimeType?.includes('powerpoint') ||
        /\.(doc|docx|xls|xlsx|ppt|pptx|txt|rtf)$/i.test(fileName);
};

const getFileType = (file) => {
    if (isImageFile(file)) return 'Image';
    if (isPdfFile(file)) return 'PDF';
    if (isDocumentFile(file)) return 'Document';
    return 'File';
};
</script>
