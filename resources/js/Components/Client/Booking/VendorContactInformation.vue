<script setup>
import { ref, watch } from 'vue'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from '@/Components/ui/dialog'
import { Button } from '@/Components/ui/button'
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar'
import {
    Phone,
    Mail,
    MapPin,
    MessageCircle,
    Shield,
    Copy
} from 'lucide-vue-next'

// Props
const props = defineProps({
    vendor: {
        type: Object,
        default: () => ({
            id: 'VND-001',
            name: 'CleanPro Services',
            email: 'contact@cleanpro.com',
            phone: '+1 (555) 123-4567',
            address: '123 Business Ave, City, State 12345',
            avatar: null,
            isVerified: true
        }),
    },
    isOpen: {
        type: Boolean,
        default: false,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
})

// Emits
const emit = defineEmits(['close', 'message'])

// Reactive data
const copySuccess = ref('')

// Watch for modal open/close
watch(() => props.isOpen, (newValue) => {
    if (!newValue) {
        copySuccess.value = ''
    }
})

const handleOpenChange = (open) => {
    if (!open) {
        emit('close')
    }
}

const handleMessage = () => {
    emit('message', props.vendor)
}

const copyToClipboard = async (text, type) => {
    try {
        await navigator.clipboard.writeText(text)
        copySuccess.value = type
        setTimeout(() => {
            copySuccess.value = ''
        }, 2000)
    } catch (err) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea')
        textArea.value = text
        document.body.appendChild(textArea)
        textArea.select()
        document.execCommand('copy')
        document.body.removeChild(textArea)
        copySuccess.value = type
        setTimeout(() => {
            copySuccess.value = ''
        }, 2000)
    }
}

const getInitials = (name) => {
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .slice(0, 2)
}
</script>

<template>
    <Dialog :open="isOpen" @update:open="handleOpenChange">
        <DialogContent class="sm:max-w-md w-[95vw] max-h-[80vh] overflow-hidden rounded-xl">
            <!-- Header -->
            <DialogHeader class="px-4 pt-4 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <!-- Avatar -->
                    <Avatar class="w-10 h-10 flex-shrink-0">
                        <AvatarImage :src="vendor.avatar" :alt="vendor.name" />
                        <AvatarFallback class="bg-blue-100 text-blue-700 font-medium text-sm">
                            {{ getInitials(vendor.name) }}
                        </AvatarFallback>
                    </Avatar>

                    <!-- Vendor Name -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <DialogTitle class="text-lg font-semibold text-gray-900 truncate">
                                {{ vendor.name }}
                            </DialogTitle>
                            <Shield v-if="vendor.isVerified" class="w-4 h-4 text-green-500 flex-shrink-0" />
                        </div>
                        <DialogDescription class="text-sm text-gray-600">
                            Contact Information
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>

            <!-- Contact Information -->
            <div class="px-4 py-4 space-y-3">

                <!-- Phone -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="p-2 bg-green-100 rounded-full">
                            <Phone class="w-4 h-4 text-green-600" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium text-gray-700 mb-0.5">Phone</p>
                            <p class="text-sm text-gray-900">{{ vendor.phone }}</p>
                        </div>
                    </div>
                    <Button variant="ghost" size="sm" @click="copyToClipboard(vendor.phone, 'phone')"
                        class="flex-shrink-0 ml-2 h-8 w-8 p-0" :class="copySuccess === 'phone' ? 'text-green-600' : ''">
                        <Copy class="w-3 h-3" />
                    </Button>
                </div>

                <!-- Email -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="p-2 bg-blue-100 rounded-full">
                            <Mail class="w-4 h-4 text-blue-600" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium text-gray-700 mb-0.5">Email</p>
                            <p class="text-sm text-gray-900 break-all">{{ vendor.email }}</p>
                        </div>
                    </div>
                    <Button variant="ghost" size="sm" @click="copyToClipboard(vendor.email, 'email')"
                        class="flex-shrink-0 ml-2 h-8 w-8 p-0" :class="copySuccess === 'email' ? 'text-green-600' : ''">
                        <Copy class="w-3 h-3" />
                    </Button>
                </div>

                <!-- Address -->
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-orange-100 rounded-full">
                            <MapPin class="w-4 h-4 text-orange-600" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium text-gray-700 mb-1">Address</p>
                            <p class="text-sm text-gray-900 leading-relaxed">{{ vendor.address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Copy Success Message -->
                <div v-if="copySuccess" class="p-2 bg-green-100 border border-green-200 rounded-lg">
                    <p class="text-xs text-green-800 text-center">
                        {{ copySuccess === 'phone' ? 'Phone number' : 'Email address' }} copied!
                    </p>
                </div>
            </div>

            <!-- Message Button -->
            <DialogFooter class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                <Button @click="handleMessage" class="w-full h-10 bg-blue-600 hover:bg-blue-700 text-white"
                    :disabled="isLoading">
                    <MessageCircle class="w-4 h-4 mr-2" />
                    <span v-if="!isLoading">Send Message</span>
                    <span v-else class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Sending...
                    </span>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
/* Ensure dialog stays within viewport */
/* @media (max-height: 600px) {
    :deep(.max-h-[80vh]) {
        max-height: 70vh;
    }
} */
</style>
