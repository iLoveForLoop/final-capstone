<script setup>
import { ref, computed } from 'vue'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog'
import { Badge } from '@/Components/ui/badge'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Separator } from '@/Components/ui/separator'
import {
    User,
    Building,
    Phone,
    Mail,
    MapPin,
    Calendar,
    FileText,
    Image,
    Download,
    ExternalLink,
    Tag
} from 'lucide-vue-next'

const props = defineProps({
    application: {
        type: Object,
        default: () => ({})
    },
    open: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:open', 'approve', 'reject'])


// Use sample data if no application provided
const applicationData = computed(() => props.application.id ? props.application : sampleApplication)

const formatDate = (dateString) => {
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }
    return new Date(dateString).toLocaleDateString(undefined, options)
}

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}

const isImageFile = (file) => {
    return file.mime_type && file.mime_type.startsWith('image/')
}

const getFileIcon = (file) => {
    if (isImageFile(file)) return Image
    if (file.type === 'application/pdf') return FileText
    return FileText
}

const selectedImageModal = ref(null)

const openImageModal = (image) => {
    selectedImageModal.value = image
}

const closeImageModal = () => {
    selectedImageModal.value = null
}

const getInitials = (name) => {
    return name.split(' ').map(n => n.charAt(0)).join('').substring(0, 2).toUpperCase();
};

const handleApprove = () => {
    emit('approve', applicationData.value.id)
}

const handleReject = () => {
    emit('reject', applicationData.value.id)
}
</script>

<template>
    <!-- Main Modal -->
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-w-5xl max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-3">
                    <img v-if="application.avatar" :src="application.avatar" alt="Profile Photo"
                        class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-200" />
                    <div v-else
                        class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                        {{ getInitials(application.full_name) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">{{ applicationData.business_name }}</h2>
                        <p class="text-sm text-muted-foreground font-normal">Vendor Application Details</p>
                    </div>
                </DialogTitle>
            </DialogHeader>

            <div class="grid gap-6 py-4">
                <!-- Basic Information -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <User class="w-5 h-5" />
                            Basic Information
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Full Name</label>
                                <p class="font-medium">{{ applicationData.full_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Email Address</label>
                                <p class="font-medium flex items-center gap-2">
                                    <Mail class="w-4 h-4" />
                                    {{ applicationData.user.email }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Contact Number</label>
                                <p class="font-medium flex items-center gap-2">
                                    <Phone class="w-4 h-4" />
                                    {{ applicationData.contact_number }}
                                </p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Business Name</label>
                                <p class="font-medium flex items-center gap-2">
                                    <Building class="w-4 h-4" />
                                    {{ applicationData.business_name }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Application Date</label>
                                <p class="font-medium flex items-center gap-2">
                                    <Calendar class="w-4 h-4" />
                                    {{ formatDate(applicationData.created_at) }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Status</label>
                                <Badge variant="secondary" class="bg-yellow-100 text-yellow-800">
                                    Pending Review
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Business Details -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Building class="w-5 h-5" />
                            Business Details
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Business Description</label>
                            <p class="mt-1 text-sm leading-relaxed">{{ applicationData.description }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Categories</label>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <Badge v-for="category in applicationData.vendor_categories" :key="category"
                                    variant="outline">
                                    <Tag class="w-3 h-3 mr-1" />
                                    {{ category }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Location & Service Areas -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MapPin class="w-5 h-5" />
                            Location & Service Areas
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Business Address</label>
                            <p class="font-medium">{{ applicationData.location }}</p>
                            <!-- <div v-if="applicationData.latitude && applicationData.longitude"
                                class="text-xs text-muted-foreground mt-1">
                                Coordinates: {{ applicationData.latitude }}, {{ applicationData.longitude }}
                            </div> -->
                        </div>

                        <div v-if="applicationData.service_coverage_areas?.length">
                            <label class="text-sm font-medium text-muted-foreground">Service Coverage Areas</label>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <Badge v-for="area in applicationData.service_coverage_areas" :key="area"
                                    variant="secondary">
                                    {{ area }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Service Photos -->
                <Card v-if="applicationData.service_photos?.length">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Image class="w-5 h-5" />
                            Service Photos ({{ applicationData.service_photos.length }})
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div v-for="photo in applicationData.service_photos" :key="photo.id"
                                class="group cursor-pointer" @click="openImageModal(photo)">
                                <div
                                    class="aspect-square rounded-lg overflow-hidden bg-gray-100 group-hover:ring-2 group-hover:ring-blue-500 transition-all">
                                    <img :src="photo.url" :alt="photo.name"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                                </div>
                                <p class="text-xs text-muted-foreground mt-1 truncate">{{ photo.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ photo.size }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Permit Files -->
                <Card v-if="applicationData.permit_files?.length">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <FileText class="w-5 h-5" />
                            Permit & Legal Documents ({{ applicationData.permit_files.length }})
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-for="file in applicationData.permit_files" :key="file.id"
                                class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <component :is="getFileIcon(file)" class="w-8 h-8 text-blue-600 flex-shrink-0" />
                                    <div>
                                        <p class="font-medium text-sm">{{ file.name }}</p>
                                        <p class="text-xs text-muted-foreground">{{ file.size }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Button v-if="isImageFile(file)" variant="ghost" size="sm"
                                        @click="openImageModal(file)">
                                        <ExternalLink class="w-4 h-4" />
                                    </Button>
                                    <Button variant="ghost" size="sm">
                                        <Download class="w-4 h-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <Button variant="outline" @click="emit('update:open', false)">
                        Close
                    </Button>
                    <Button variant="destructive" class="bg-red-600 text-white hover:bg-red-700" @click="handleReject">
                        Reject Application
                    </Button>
                    <Button class="bg-green-600 text-white hover:bg-green-700" @click="handleApprove">
                        Approve Application
                    </Button>

                </div>
            </div>
        </DialogContent>
    </Dialog>

    <!-- Image Preview Modal -->
    <Dialog :open="!!selectedImageModal" @update:open="selectedImageModal = null">
        <DialogContent class="max-w-3xl">
            <DialogHeader v-if="selectedImageModal">
                <DialogTitle>{{ selectedImageModal.name }}</DialogTitle>
                <DialogDescription>
                    {{ selectedImageModal.size }} • Click outside to close
                </DialogDescription>
            </DialogHeader>
            <div v-if="selectedImageModal" class="flex justify-center">
                <img :src="selectedImageModal.url" :alt="selectedImageModal.name"
                    class="max-w-full max-h-[70vh] object-contain rounded-lg" />
            </div>
        </DialogContent>
    </Dialog>
</template>
