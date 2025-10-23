<script setup>
import { Calendar, Check, CreditCard, Info, MapPin, User } from 'lucide-vue-next'

defineProps({
    showAcceptModal: Boolean,
    selectedBooking: Object,
    isLoading: Function,
    formatDate: Function
})

const emit = defineEmits(['close-modals', 'accept-booking'])

</script>

<template>
    <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="showAcceptModal" class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Simple Professional Backdrop -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="emit('close-modals')"></div>

            <!-- Modal Content -->
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <Transition enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0" leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-4">
                    <div v-if="showAcceptModal"
                        class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg">
                                    <Check class="w-5 h-5 text-green-600" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Accept Booking</h3>
                                    <p class="text-sm text-gray-500">Confirm this booking request</p>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div v-if="selectedBooking" class="mb-6 p-4 bg-gray-50 rounded-lg border">
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
                                <div class="flex items-center space-x-2">
                                    <CreditCard class="w-4 h-4 text-gray-400" />
                                    <span class="text-sm font-medium text-gray-900">{{ selectedBooking.price }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Information Section -->
                        <div class="p-4 mb-6 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex space-x-3">
                                <Info class="w-5 h-5 text-blue-500 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-blue-900">What happens next?</h4>
                                    <ul class="mt-2 text-xs text-blue-700 space-y-1">
                                        <li>• Client will be notified of your acceptance</li>
                                        <li>• Booking will be marked as confirmed</li>
                                        <li>• This time slot will be reserved</li>
                                        <li>• You can contact the client for any additional details</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex space-x-3">
                            <button @click="emit('close-modals')"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                Cancel
                            </button>
                            <button @click="emit('accept-booking')"
                                :disabled="isLoading(selectedBooking?.raw_id, 'accepting')"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-green-600 border border-transparent rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <div v-if="isLoading(selectedBooking?.raw_id, 'accepting')"
                                    class="flex items-center justify-center space-x-2">
                                    <div
                                        class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <span>Accepting...</span>
                                </div>
                                <span v-else class="flex items-center justify-center space-x-1">
                                    <Check class="w-4 h-4" />
                                    <span>Accept Booking</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </Transition>
</template>
