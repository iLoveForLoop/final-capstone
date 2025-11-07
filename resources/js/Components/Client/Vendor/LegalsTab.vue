<script setup>
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { FileText, Image, Download, Eye, File, X } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    vendor: {
        type: Object
    }
})

// Reactive states
const previewDocument = ref(null);
const showPreview = ref(false);

// Get legal documents from vendor media
const legalDocuments = props.vendor?.legalDocuments ?? [];

// Helper function to get file type from mime_type
const getFileType = (mimeType) => {
    if (mimeType?.includes('pdf')) return 'pdf';
    if (mimeType?.includes('image')) return 'image';
    if (mimeType?.includes('word') || mimeType?.includes('document')) return 'document';
    return 'file';
}

// Helper function to format file size
const formatFileSize = (bytes) => {
    if (!bytes) return 'Unknown size';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

// Helper function to format date
const formatDate = (dateString) => {
    if (!dateString) return 'Unknown date';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Helper function to get file category from file name or type
const getFileCategory = (fileName, mimeType) => {
    const name = fileName?.toLowerCase() || '';

    if (name.includes('license') || name.includes('permit')) return 'Licenses & Permits';
    if (name.includes('tax') || name.includes('registration')) return 'Tax Documents';
    if (name.includes('insurance') || name.includes('policy')) return 'Insurance';
    if (name.includes('certificate') || name.includes('certification')) return 'Certifications';
    if (name.includes('contract') || name.includes('agreement')) return 'Contracts';
    if (mimeType?.includes('image')) return 'Images';

    return 'Business Documents';
}

const getFileIcon = (mimeType) => {
    const fileType = getFileType(mimeType);
    switch (fileType) {
        case 'pdf':
            return FileText;
        case 'image':
            return Image;
        default:
            return File;
    }
}

const getFileColor = (mimeType) => {
    const fileType = getFileType(mimeType);
    switch (fileType) {
        case 'pdf':
            return 'text-red-600';
        case 'image':
            return 'text-green-600';
        case 'document':
            return 'text-blue-600';
        default:
            return 'text-gray-600';
    }
}

const handleViewDocument = (document) => {
    previewDocument.value = document;
    showPreview.value = true;
}

const handleDownloadDocument = (document) => {

    if (document.url) {
        // Simple approach - just open in new tab
        window.open(document.url, '_blank');
    }
}

const closePreview = () => {
    showPreview.value = false;
    previewDocument.value = null;
}

// Check if document is previewable (images and PDFs)
const isPreviewable = (document) => {
    const fileType = getFileType(document.mime_type);
    return fileType === 'image' || fileType === 'pdf';
}
</script>

<template>
    <div class="space-y-6 md:space-y-8">
        <!-- Legal Documents Header -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-3">
                    <FileText class="h-6 w-6 text-indigo-600" />
                    Legal Documents & Certifications
                </CardTitle>
            </CardHeader>
            <CardContent>
                <p class="text-gray-600 text-sm md:text-base">
                    This vendor has provided the following legal documents and certifications for their business
                    operations. You can view or download these documents as needed.
                </p>
            </CardContent>
        </Card>

        <!-- Documents Grid -->
        <div v-if="legalDocuments.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            <Card v-for="document in legalDocuments" :key="document.id"
                class="document-card hover:shadow-lg transition-shadow duration-200 group">
                <CardContent class="pt-6">
                    <!-- Document Preview Thumbnail -->
                    <div v-if="isPreviewable(document)"
                        class="mb-4 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 cursor-pointer"
                        @click="handleViewDocument(document)">
                        <div v-if="getFileType(document.mime_type) === 'image'"
                            class="aspect-video flex items-center justify-center bg-white">
                            <img :src="document.url" :alt="document.file_name"
                                class="w-full h-32 object-contain hover:scale-105 transition-transform duration-200" />
                        </div>
                        <div v-else-if="getFileType(document.mime_type) === 'pdf'"
                            class="aspect-video flex flex-col items-center justify-center bg-red-50 p-4">
                            <FileText :size="48" class="text-red-400 mb-2" />
                            <span class="text-xs text-red-600 font-medium text-center">PDF Document</span>
                            <span class="text-xs text-red-500 mt-1">Click to preview</span>
                        </div>
                    </div>

                    <!-- Fallback for non-previewable files -->
                    <div v-else
                        class="mb-4 rounded-lg border border-gray-200 bg-gray-50 aspect-video flex flex-col items-center justify-center p-4">
                        <File :size="48" class="text-gray-400 mb-2" />
                        <span class="text-xs text-gray-600 text-center">Document Preview<br>Not Available</span>
                    </div>

                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <component :is="getFileIcon(document.mime_type)" :size="20"
                                :class="getFileColor(document.mime_type)" class="flex-shrink-0" />
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-gray-800 text-sm line-clamp-2">
                                    {{ document.file_name }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ getFileCategory(document.file_name, document.mime_type) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>Type:</span>
                            <span class="font-medium uppercase">{{ getFileType(document.mime_type) }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>Size:</span>
                            <span class="font-medium">{{ formatFileSize(document.size) }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>Uploaded:</span>
                            <span class="font-medium">{{ formatDate(document.created_at) }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <Button @click="handleViewDocument(document)" variant="outline" class="flex-1 text-xs" size="sm"
                            :disabled="!isPreviewable(document)">
                            <Eye :size="14" class="mr-1" />
                            {{ isPreviewable(document) ? 'View' : 'View' }}
                        </Button>
                        <Button @click="handleDownloadDocument(document)"
                            class="flex-1 text-xs bg-black text-white hover:bg-gray-800" size="sm">
                            <Download :size="14" class="mr-1" />
                            Download
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
            <FileText class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-4 text-lg font-semibold text-gray-900">No documents available</h3>
            <p class="mt-2 text-sm text-gray-600">
                This vendor hasn't uploaded any legal documents yet.
            </p>
        </div>

        <!-- Document Preview Modal -->
        <Dialog :open="showPreview" @update:open="closePreview">
            <DialogContent class="max-w-4xl h-[80vh] flex flex-col">
                <DialogHeader class="flex-shrink-0 pb-4">
                    <DialogTitle class="flex items-center justify-between">
                        <span class="flex items-center gap-2 truncate">
                            <component :is="getFileIcon(previewDocument?.mime_type)" :size="20"
                                :class="getFileColor(previewDocument?.mime_type)" />
                            <span class="truncate">{{ previewDocument?.file_name }}</span>
                        </span>
                        <!-- <Button variant="ghost" size="sm" @click="closePreview" class="h-8 w-8 p-0 flex-shrink-0 ml-2">
                            <X :size="16" />
                        </Button> -->
                    </DialogTitle>
                </DialogHeader>

                <div class="flex-1 overflow-auto min-h-0">
                    <div v-if="previewDocument" class="h-full">
                        <!-- Image Preview -->
                        <div v-if="getFileType(previewDocument.mime_type) === 'image'"
                            class="h-full flex items-center justify-center bg-gray-50 rounded-lg p-4">
                            <img :src="previewDocument.url" :alt="previewDocument.file_name"
                                class="max-w-full max-h-full object-contain rounded-lg shadow-sm" />
                        </div>

                        <!-- PDF Preview -->
                        <div v-else-if="getFileType(previewDocument.mime_type) === 'pdf'" class="h-full">
                            <iframe :src="previewDocument.url" class="w-full h-full border-0 rounded-lg"
                                frameborder="0">
                                <p>Your browser does not support PDFs.
                                    <a :href="previewDocument.url" class="text-indigo-600 hover:underline">
                                        Download the PDF instead.
                                    </a>
                                </p>
                            </iframe>
                        </div>

                        <!-- Unsupported File Type -->
                        <div v-else class="h-full flex items-center justify-center bg-gray-50 rounded-lg p-8">
                            <div class="text-center">
                                <File :size="80" class="mx-auto text-gray-400 mb-4" />
                                <h3 class="text-xl font-semibold text-gray-900 mb-3">Preview Not Available</h3>
                                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                    This file type cannot be previewed in the browser. Please download the file to view
                                    its contents.
                                </p>
                                <Button @click="handleDownloadDocument(previewDocument)"
                                    class="bg-black text-white hover:bg-gray-800 px-6 py-2">
                                    <Download :size="18" class="mr-2" />
                                    Download File
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div v-if="previewDocument" class="flex justify-end gap-3 pt-4 border-t flex-shrink-0 mt-4">
                    <Button variant="outline" @click="closePreview">
                        Close
                    </Button>
                    <!-- <Button @click="handleDownloadDocument(previewDocument)"
                        class="bg-black text-white hover:bg-gray-800">
                        <Download :size="16" class="mr-2" />
                        Download
                    </Button> -->
                </div>
            </DialogContent>
        </Dialog>

        <!-- Additional Legal Information -->
        <!-- Additional Legal Information -->
        <Card>
            <CardHeader>
                <CardTitle>Document Information</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div class="compliance-item">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <span class="font-semibold text-gray-800">Secure Document Storage</span>
                            <p class="text-sm text-gray-600 mt-1">All documents are stored securely and are only
                                accessible to authorized users</p>
                        </div>
                    </div>
                    <div class="compliance-item">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <span class="font-semibold text-gray-800">Document Verification</span>
                            <p class="text-sm text-gray-600 mt-1">Vendor documents are verified during the onboarding
                                process</p>
                        </div>
                    </div>
                    <div class="compliance-item">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <span class="font-semibold text-gray-800">File Access</span>
                            <p class="text-sm text-gray-600 mt-1">You can view or download any document for your records
                            </p>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

<style scoped>
.document-card {
    @apply border border-gray-200 rounded-lg hover:border-indigo-200 transition-colors duration-200;
}

.compliance-item {
    @apply flex items-start gap-3;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom scrollbar for dialog */
:deep(.max-h-\[90vh\]) {
    scrollbar-width: thin;
    scrollbar-color: #c7d2fe #f8fafc;
}

:deep(.max-h-\[90vh\])::-webkit-scrollbar {
    width: 6px;
}

:deep(.max-h-\[90vh\])::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 3px;
}

:deep(.max-h-\[90vh\])::-webkit-scrollbar-thumb {
    background: #c7d2fe;
    border-radius: 3px;
}
</style>
