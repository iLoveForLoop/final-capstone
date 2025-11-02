<script setup>
import { AlertTriangle, Calendar, Info, MapPin, User, X } from 'lucide-vue-next'

defineProps({
    showCancelModal: Boolean,
    selectedBooking: Object,
    isLoading: Function,
    formatDate: Function
})

const cancelReason = defineModel('cancelReason')

const emit = defineEmits(['close-modals', 'cancel-booking', 'cancel'])
</script>

<template>
    <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showCancelModal" class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Simple Professional Backdrop -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="emit('close-modals')"></div>

            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <Transition enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0" leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-4">
                    <div v-if="showCancelModal"
                        class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-lg">
                                    <X class="w-5 h-5 text-red-600" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Cancel Booking</h3>
                                    <p class="text-sm text-gray-500">This action cannot be undone</p>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div v-if="selectedBooking" class="mb-4 p-4 bg-gray-50 rounded-lg border">
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <User class="w-4 h-4 text-gray-400" />
                                    <span class="text-sm font-medium text-gray-900">{{ selectedBooking.client }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Calendar class="w-4 h-4 text-gray-400" />
                                    <span class="text-sm text-gray-600">{{ formatDate(selectedBooking.date) }} at {{
                                        selectedBooking.time }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <MapPin class="w-4 h-4 text-gray-400" />
                                    <span class="text-sm text-gray-600">{{ selectedBooking.event_name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Section -->
                        <div class="p-4 mb-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex space-x-3">
                                <AlertTriangle class="w-5 h-5 text-red-500 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-red-900">Please confirm your decision</h4>
                                    <p class="mt-1 text-xs text-red-700">
                                        The client will be notified that you're canceling this booking. This
                                        action cannot be reversed and may affect your reputation.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Cancellation Policy Notice -->
                        <div class="p-3 mb-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex space-x-2">
                                <Info class="w-4 h-4 text-blue-500 mt-0.5" />
                                <div class="flex-1">
                                    <p class="text-xs text-blue-700">
                                        Please review your cancellation policy before proceeding. Cancellations may
                                        incur fees or affect your ratings.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Reason Input -->
                        <div class="mb-6">
                            <label for="cancelReason" class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for cancellation (required)
                            </label>
                            <textarea id="cancelReason" v-model="cancelReason" rows="3"
                                placeholder="Please explain why you need to cancel this booking..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"></textarea>
                            <p class="mt-1 text-xs text-gray-500">Providing a clear reason helps maintain trust with
                                clients.</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-3">
                            <button @click="emit('cancel')"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                Cancel
                            </button>
                            <button @click="emit('cancel-booking')"
                                :disabled="isLoading(selectedBooking?.raw_id, 'cancelling') || !cancelReason"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <div v-if="isLoading(selectedBooking?.raw_id, 'cancelling')"
                                    class="flex items-center justify-center space-x-2">
                                    <div
                                        class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <span>Cancelling...</span>
                                </div>
                                <span v-else class="flex items-center justify-center space-x-1">
                                    <X class="w-4 h-4" />
                                    <span>Cancel Booking</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </Transition>
</template>
