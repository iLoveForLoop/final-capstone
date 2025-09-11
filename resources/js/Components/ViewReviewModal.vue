<script setup>
import { ref } from 'vue'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from '@/Components/ui/dialog'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Star, Calendar, User, MessageSquare } from 'lucide-vue-next'

// Props
const props = defineProps({
    review: {
        type: Object,
        default: () => ({
            id: 'rev_001',
            rating: 5,
            title: "Great service overall",
            comment: "The team delivered exactly what I needed. Communication was clear throughout the project and the final result exceeded my expectations.",
            reviewDate: "2024-09-01T14:30:00Z",
            serviceName: "Website Development",
            serviceProvider: "WebCraft Pro",
            vendorResponse: {
                message: "Thank you for your positive feedback! We're glad we could meet your expectations and look forward to working with you again.",
                date: "2024-09-02T09:15:00Z"
            }
        })
    },
    isOpen: {
        type: Boolean,
        default: false
    },
    showActions: {
        type: Boolean,
        default: false
    },
    actionText: {
        type: String,
        default: 'Take Action'
    }
})

// Emits
const emit = defineEmits(['close', 'action'])

// Rating descriptions
const ratingDescriptions = [
    'Poor - Very dissatisfied',
    'Fair - Below expectations',
    'Good - Met expectations',
    'Very Good - Exceeded expectations',
    'Excellent - Outstanding experience'
]

const handleOpenChange = (open) => {
    if (!open) {
        closeModal()
    }
}

const closeModal = () => {
    emit('close')
}

const handleAction = () => {
    emit('action', props.review)
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getRatingDescription = (rating) => {
    return ratingDescriptions[rating - 1] || 'No rating'
}
</script>

<template>
    <Dialog :open="isOpen" @update:open="handleOpenChange">
        <DialogContent class="sm:max-w-2xl rounded-xl">
            <DialogHeader class="px-6 pt-6 pb-4 border-b border-gray-100">
                <DialogTitle class="text-xl font-semibold text-gray-900">Review Details</DialogTitle>
                <DialogDescription class="text-sm text-gray-500 mt-1">
                    {{ review.serviceName }} by {{ review.serviceProvider }}
                </DialogDescription>
            </DialogHeader>

            <div class="px-6 py-4 space-y-6 max-h-[60vh] overflow-y-auto">
                <!-- Service and Rating Header -->
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ review.serviceName }}</h3>
                        <div class="flex items-center mt-1 text-sm text-gray-500">
                            <User class="w-4 h-4 mr-1.5" />
                            <span>{{ review.serviceProvider }}</span>
                        </div>
                    </div>
                    <Badge variant="secondary" class="px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-700">
                        #{{ review.id }}
                    </Badge>
                </div>

                <!-- Rating Display -->
                <div class="flex items-center flex-wrap gap-2">
                    <div class="flex">
                        <Star v-for="star in 5" :key="star" :size="18"
                            :class="star <= review.rating ? 'text-amber-500 fill-amber-500' : 'text-gray-300'" />
                    </div>
                    <div class="flex items-center ml-2">
                        <span class="text-sm font-medium text-gray-700 mr-2">{{ review.rating }}/5</span>
                        <span class="text-gray-300">•</span>
                        <span class="text-sm text-amber-700 ml-2">{{ getRatingDescription(review.rating) }}</span>
                    </div>
                </div>

                <!-- Review Date -->
                <div class="flex items-center text-sm text-gray-500">
                    <Calendar class="w-4 h-4 mr-1.5" />
                    <span>Reviewed on {{ formatDate(review.reviewDate) }}</span>
                </div>

                <!-- Review Title -->
                <!-- <div v-if="review.title">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Review Title</h4>
                    <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-gray-900 font-medium">{{ review.title }}</p>
                    </div>
                </div> -->

                <!-- Review Comment -->
                <div v-if="review.comment">
                    <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <MessageSquare class="w-4 h-4 mr-1.5" />
                        Customer Feedback
                    </h4>
                    <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-gray-700 leading-relaxed">{{ review.comment }}</p>
                    </div>
                </div>

                <!-- Vendor Response -->
                <!-- {{ console.log('vendor response: ', review.vendorResponse.message) }} -->
                <div v-if="review.vendorResponse.message" class="pt-4 border-t border-gray-100">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Vendor Response</h4>
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm text-gray-700 mb-2">{{ review.vendorResponse.message }}</p>
                        <p class="text-xs text-gray-500">Responded on {{ formatDate(review.vendorResponse.date) }}</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <DialogFooter class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-100">
                <div class="flex w-full gap-3">
                    <Button variant="outline" @click="closeModal" class="flex-1">
                        Close
                    </Button>
                    <Button v-if="showActions" @click="handleAction"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white shadow-sm">
                        {{ actionText }}
                    </Button>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
/* Custom scrollbar for dialog */
:deep(.dialog-content) {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
    padding: 0;
}

:deep(.dialog-content)::-webkit-scrollbar {
    width: 6px;
}

:deep(.dialog-content)::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

:deep(.dialog-content)::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 3px;
}
</style>
