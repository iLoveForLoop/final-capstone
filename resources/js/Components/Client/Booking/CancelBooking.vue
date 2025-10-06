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
import { Badge } from '@/Components/ui/badge'
import { Textarea } from '@/Components/ui/textarea'
import { Label } from '@/Components/ui/label'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select'
import { AlertTriangle, Calendar, User, Clock, MapPin, MessageSquare } from 'lucide-vue-next'

// Props
const props = defineProps({
    booking: {
        type: Object, default: () => ({
            id: 'BK-2024-001',
            serviceName: 'House Cleaning Service',
            serviceProvider: 'CleanPro Services',
            date: '2024-03-15',
            startTime: '10:00',
            endTime: '12:00',
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
const emit = defineEmits(['close', 'confirm', 'cancel'])

// Reactive data
const cancellationReason = ref('')
const selectedReason = ref('')

// Predefined cancellation reasons
const cancellationReasons = [
    'Schedule conflict',
    'No longer need the service',
    'Found a better alternative',
    'Emergency situation',
    'Service provider unavailable',
    'Budget constraints',
    'Other'
]

// Watch for modal open/close to reset form
watch(() => props.isOpen, (newValue) => {
    if (!newValue) {
        // Reset form when modal closes
        cancellationReason.value = ''
        selectedReason.value = ''
    }
})

const handleOpenChange = (open) => {
    if (!open) {
        closeModal()
    }
}

const closeModal = () => {
    // Reset form when closing
    cancellationReason.value = ''
    selectedReason.value = ''
    emit('close')
}

const handleConfirmCancellation = () => {
    const cancellationData = {
        ...props.booking,
        cancellationReason: selectedReason.value,
        additionalComments: cancellationReason.value
    }
    // console.log('Cancel Reason', cancellationData.additionalComments)
    emit('confirm', cancellationData)
}

const handleKeepBooking = () => {
    emit('cancel')
    closeModal()
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const formatTime = (timeString) => {
    return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    })
}
</script>

<template>
    <Dialog :open="isOpen" @update:open="handleOpenChange">
        <DialogContent class="sm:max-w-md w-[95vw] max-h-[90vh] overflow-hidden rounded-xl">
            <!-- Compact Header -->
            <DialogHeader class="px-4 pt-4 pb-3 border-b border-red-100">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-red-100 rounded-full flex-shrink-0">
                        <AlertTriangle class="w-4 h-4 text-red-600" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <DialogTitle class="text-lg font-semibold text-gray-900">Cancel Booking</DialogTitle>
                        <DialogDescription class="text-xs text-gray-500">
                            This action cannot be undone
                        </DialogDescription>
                    </div>
                    <Badge variant="secondary" class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 flex-shrink-0">
                        #{{ booking.id }}
                    </Badge>
                </div>
            </DialogHeader>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-4 py-3 space-y-3 max-h-[60vh]">
                <!-- Compact Booking Details -->
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">{{ booking.serviceName }}</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-xs text-gray-600">
                        <div class="flex items-center">
                            <User class="w-3 h-3 mr-1.5 flex-shrink-0" />
                            <span class="truncate">{{ booking.serviceProvider }}</span>
                        </div>
                        <div class="flex items-center">
                            <Calendar class="w-3 h-3 mr-1.5 flex-shrink-0" />
                            <span class="truncate">{{ formatDate(booking.date) }}</span>
                        </div>
                        <div class="flex items-center">
                            <Clock class="w-3 h-3 mr-1.5 flex-shrink-0" />
                            <span>{{ booking.startTime }}</span>
                        </div>
                        <div v-if="booking.location" class="flex items-center">
                            <MapPin class="w-3 h-3 mr-1.5 flex-shrink-0" />
                            <span class="truncate">{{ booking.location }}</span>
                        </div>
                    </div>
                </div>

                <!-- Compact Warning -->
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start">
                        <AlertTriangle class="w-4 h-4 text-amber-600 mt-0.5 mr-2 flex-shrink-0" />
                        <div class="min-w-0">
                            <h4 class="text-xs font-medium text-amber-800 mb-1">Cancellation Policy</h4>
                            <p class="text-xs text-amber-700 leading-tight">
                                Cancellation fees may apply. Check with your service provider for terms.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Compact Cancellation Reason -->
                <div class="space-y-2">
                    <div class="flex items-center gap-1.5">
                        <MessageSquare class="w-3 h-3 text-gray-600" />
                        <Label class="text-xs font-medium text-gray-700">Reason</Label>
                    </div>

                    <Select v-model="selectedReason">
                        <SelectTrigger class="h-9 text-sm">
                            <SelectValue placeholder="Choose a reason..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="reason in cancellationReasons" :key="reason" :value="reason"
                                class="text-sm">
                                {{ reason }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <div class="space-y-1.5">
                        <Label for="additional-comments" class="text-xs text-gray-600">Additional comments
                            (Optional)</Label>
                        <Textarea id="additional-comments" v-model="cancellationReason"
                            placeholder="Any additional details..." class="min-h-[60px] text-sm resize-none"
                            maxlength="500" />
                        <div class="flex justify-end">
                            <span class="text-xs text-gray-400">{{ cancellationReason?.length || 0 }}/500</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compact Footer -->
            <DialogFooter class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex-shrink-0">
                <div class="flex w-full gap-2">
                    <Button variant="outline" @click="handleKeepBooking" class="flex-1 h-9 text-sm"
                        :disabled="isLoading">
                        Keep Booking
                    </Button>
                    <Button @click="handleConfirmCancellation"
                        class="flex-1 h-9 text-sm bg-red-600 hover:bg-red-700 text-white" :disabled="isLoading">
                        <span v-if="!isLoading">Cancel Booking</span>
                        <span v-else class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-1.5 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Cancelling...
                        </span>
                    </Button>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
/* Custom scrollbar for dialog content */
:deep(.overflow-y-auto) {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

:deep(.overflow-y-auto)::-webkit-scrollbar {
    width: 4px;
}

:deep(.overflow-y-auto)::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 2px;
}

:deep(.overflow-y-auto)::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 2px;
}

/* Ensure dialog stays within viewport */
/* @media (max-height: 600px) {
    :deep(.max-h-[60vh]) {
        max-height: 50vh;
    }
} */
</style>
