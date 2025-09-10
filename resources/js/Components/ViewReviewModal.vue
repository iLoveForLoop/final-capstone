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
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Label } from '@/Components/ui/label'
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
        <DialogContent class="sm:max-w-md rounded-lg">
            <DialogHeader class="pb-3 border-b">
                <DialogTitle class="text-lg font-semibold text-gray-900">Review Details</DialogTitle>
                <DialogDescription class="text-sm text-gray-500">
                    {{ review.serviceName }} by {{ review.serviceProvider }}
                </DialogDescription>
            </DialogHeader>

            <div class="py-4 space-y-5">
                <!-- Service and Rating Header -->
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-base font-medium text-gray-900">{{ review.serviceName }}</h3>
                        <div class="flex items-center mt-1 text-sm text-gray-500">
                            <User class="w-4 h-4 mr-1" />
                            <span>{{ review.serviceProvider }}</span>
                        </div>
                    </div>
                    <Badge variant="secondary" class="px-2 py-1 text-xs">#{{ review.id }}</Badge>
                </div>

                <!-- Rating Display -->
                <div class="flex items-center">
                    <div class="flex mr-2">
                        <Star v-for="star in 5" :key="star" :size="18"
                            :class="star <= review.rating ? 'text-amber-500 fill-amber-500' : 'text-gray-300'" />
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ review.rating }}/5</span>
                    <span class="mx-2 text-gray-300">•</span>
                    <span class="text-sm text-amber-700">{{ getRatingDescription(review.rating) }}</span>
                </div>

                <!-- Review Date -->
                <div class="flex items-center text-sm text-gray-500">
                    <Calendar class="w-4 h-4 mr-1" />
                    <span>Reviewed on {{ formatDate(review.reviewDate) }}</span>
                </div>

                <!-- Review Title -->
                <div v-if="review.title">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Review Title</h4>
                    <p class="text-gray-900 font-medium px-3 py-2 bg-gray-50 rounded-lg">{{ review.title }}</p>
                </div>

                <!-- Review Comment -->
                <div v-if="review.comment">
                    <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <MessageSquare class="w-4 h-4 mr-1" />
                        Customer Feedback
                    </h4>
                    <div class="px-3 py-2 bg-gray-50 rounded-lg">
                        <p class="text-gray-700 leading-relaxed">{{ review.comment }}</p>
                    </div>
                </div>

                <!-- Vendor Response -->
                <div v-if="review.vendorResponse" class="pt-3 border-t">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Vendor Response</h4>
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <p class="text-sm text-gray-700 mb-2">{{ review.vendorResponse.message }}</p>
                        <p class="text-xs text-gray-500">Responded on {{ formatDate(review.vendorResponse.date) }}</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <DialogFooter class="pt-4 border-t">
                <Button variant="outline" @click="closeModal" class="flex-1 sm:flex-none">
                    Close
                </Button>
                <Button v-if="showActions" @click="handleAction"
                    class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700">
                    {{ actionText }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
/* Custom scrollbar for dialog */
:deep(.dialog-content) {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
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
