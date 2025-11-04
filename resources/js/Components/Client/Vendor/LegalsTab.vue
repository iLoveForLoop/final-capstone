<script setup>
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { FileText, Image, Download, Eye, File } from 'lucide-vue-next';

defineProps({
    vendor: {
        type: Object
    }
})

// Static data for legal documents
const legalDocuments = [
    {
        id: 1,
        name: 'Business License',
        type: 'pdf',
        size: '2.4 MB',
        uploadDate: '2024-01-15',
        category: 'Licenses'
    },
    {
        id: 2,
        name: 'Tax Registration Certificate',
        type: 'pdf',
        size: '1.8 MB',
        uploadDate: '2024-01-10',
        category: 'Tax Documents'
    },
    {
        id: 3,
        name: 'Insurance Policy',
        type: 'pdf',
        size: '3.2 MB',
        uploadDate: '2024-01-08',
        category: 'Insurance'
    },
    {
        id: 4,
        name: 'Health & Safety Certificate',
        type: 'image',
        size: '1.1 MB',
        uploadDate: '2024-01-12',
        category: 'Certifications'
    },
    {
        id: 5,
        name: 'Service Agreement Template',
        type: 'pdf',
        size: '4.1 MB',
        uploadDate: '2024-01-05',
        category: 'Contracts'
    },
    {
        id: 6,
        name: 'Vendor Permit',
        type: 'image',
        size: '0.8 MB',
        uploadDate: '2024-01-18',
        category: 'Permits'
    }
]

const getFileIcon = (fileType) => {
    switch (fileType) {
        case 'pdf':
            return FileText;
        case 'image':
            return Image;
        default:
            return File;
    }
}

const getFileColor = (fileType) => {
    switch (fileType) {
        case 'pdf':
            return 'text-red-600';
        case 'image':
            return 'text-green-600';
        default:
            return 'text-gray-600';
    }
}

const handleViewDocument = (document) => {
    // In a real app, this would open the document in a viewer
    console.log('Viewing document:', document.name);
    alert(`Opening: ${document.name}\n\nThis would open the document in a viewer in a real application.`);
}

const handleDownloadDocument = (document) => {
    // In a real app, this would trigger download
    console.log('Downloading document:', document.name);
    alert(`Downloading: ${document.name}\n\nThis would download the file in a real application.`);
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
                    operations.
                    You can view or download these documents as needed.
                </p>
            </CardContent>
        </Card>

        <!-- Documents Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            <Card v-for="document in legalDocuments" :key="document.id"
                class="document-card hover:shadow-lg transition-shadow duration-200">
                <CardContent class="pt-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <component :is="getFileIcon(document.type)" :size="24" :class="getFileColor(document.type)"
                                class="flex-shrink-0" />
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm md:text-base line-clamp-2">
                                    {{ document.name }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-1">{{ document.category }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>Type:</span>
                            <span class="font-medium uppercase">{{ document.type }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>Size:</span>
                            <span class="font-medium">{{ document.size }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600">
                            <span>Uploaded:</span>
                            <span class="font-medium">{{ document.uploadDate }}</span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <Button @click="handleViewDocument(document)" variant="outline" class="flex-1 text-xs"
                            size="sm">
                            <Eye :size="14" class="mr-1" />
                            View
                        </Button>
                        <Button @click="handleDownloadDocument(document)" class="flex-1 text-xs" size="sm">
                            <Download :size="14" class="mr-1" />
                            Download
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Additional Legal Information -->
        <Card>
            <CardHeader>
                <CardTitle>Legal Compliance</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div class="compliance-item">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <span class="font-semibold text-gray-800">All licenses are current and valid</span>
                            <p class="text-sm text-gray-600 mt-1">Last verified: January 2024</p>
                        </div>
                    </div>
                    <div class="compliance-item">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <span class="font-semibold text-gray-800">Insurance coverage active</span>
                            <p class="text-sm text-gray-600 mt-1">Policy renewal due: December 2024</p>
                        </div>
                    </div>
                    <div class="compliance-item">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                        <div>
                            <span class="font-semibold text-gray-800">Tax compliance verified</span>
                            <p class="text-sm text-gray-600 mt-1">All tax documents are up to date</p>
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
</style>
