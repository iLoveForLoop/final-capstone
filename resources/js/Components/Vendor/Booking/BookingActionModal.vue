<script setup>
import { ref, computed } from 'vue'
import { X, AlertCircle, Check, CircleCheck, XCircle } from 'lucide-vue-next'

const isOpen = ref(false)
const actionType = ref('') // 'accept', 'decline', 'complete'
const booking = ref(null)
const resolveCallback = ref(null)
const declineReason = ref('')

const modalConfig = computed(() => {
    const configs = {
        accept: {
            title: 'Accept Booking',
            icon: Check,
            iconClass: 'text-green-600 bg-green-100',
            description: 'Are you sure you want to accept this booking?',
            confirmText: 'Accept Booking',
            confirmClass: 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
            cancelText: 'Cancel'
        },
        decline: {
            title: 'Decline Booking',
            icon: XCircle,
            iconClass: 'text-red-600 bg-red-100',
            description: 'Are you sure you want to decline this booking? This action cannot be undone.',
            confirmText: 'Decline Booking',
            confirmClass: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
            cancelText: 'Cancel',
            showReason: true
        },
        complete: {
            title: 'Complete Booking',
            icon: CircleCheck,
            iconClass: 'text-blue-600 bg-blue-100',
            description: 'Mark this booking as completed? This will finalize the booking and notify the client.',
            confirmText: 'Mark as Complete',
            confirmClass: 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
            cancelText: 'Cancel'
        }
    }
    return configs[actionType.value] || configs.accept
})

const open = (type, bookingData) => {
    actionType.value = type
    booking.value = bookingData
    declineReason.value = ''
    isOpen.value = true

    return new Promise((resolve) => {
        resolveCallback.value = resolve
    })
}

const confirm = () => {
    if (actionType.value === 'decline') {
        resolveCallback.value({ confirmed: true, reason: declineReason.value })
    } else {
        resolveCallback.value({ confirmed: true })
    }
    close()
}

const cancel = () => {
    resolveCallback.value({ confirmed: false })
    close()
}

const close = () => {
    isOpen.value = false
    actionType.value = ''
    booking.value = null
    declineReason.value = ''
}

defineExpose({
    open
})
</script>

<template>
    <!-- Modal Overlay -->
    <Transition name="modal-fade">
        <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <!-- Background overlay -->
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="cancel" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"></div>

                <!-- Center modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <Transition name="modal-slide">
                    <div v-if="isOpen"
                        class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <!-- Close button -->
                        <button @click="cancel" type="button"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md">
                            <span class="sr-only">Close</span>
                            <X class="h-6 w-6" />
                        </button>

                        <div class="sm:flex sm:items-start">
                            <!-- Icon -->
                            <div
                                :class="[modalConfig.iconClass, 'mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10']">
                                <component :is="modalConfig.icon" class="h-6 w-6" />
                            </div>

                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <!-- Title -->
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    {{ modalConfig.title }}
                                </h3>

                                <!-- Description -->
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        {{ modalConfig.description }}
                                    </p>
                                </div>

                                <!-- Booking Details -->
                                <div v-if="booking" class="mt-4 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <dl class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <dt class="text-gray-500">Booking ID:</dt>
                                            <dd class="font-medium text-gray-900">{{ booking.id }}</dd>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <dt class="text-gray-500">Client:</dt>
                                            <dd class="font-medium text-gray-900">{{ booking.client }}</dd>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <dt class="text-gray-500">Service:</dt>
                                            <dd class="font-medium text-gray-900">{{ booking.service.name }}</dd>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <dt class="text-gray-500">Event:</dt>
                                            <dd class="font-medium text-gray-900">{{ booking.event_name }}</dd>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <dt class="text-gray-500">Date:</dt>
                                            <dd class="font-medium text-gray-900">{{ booking.date }}</dd>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <dt class="text-gray-500">Time:</dt>
                                            <dd class="font-medium text-gray-900">{{ booking.time }}</dd>
                                        </div>
                                        <div class="flex justify-between text-sm border-t border-gray-200 pt-2">
                                            <dt class="text-gray-500">Amount:</dt>
                                            <dd class="font-semibold text-gray-900">{{ booking.price }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Decline Reason Input -->
                                <div v-if="modalConfig.showReason" class="mt-4">
                                    <label for="decline-reason" class="block text-sm font-medium text-gray-700 mb-2">
                                        Reason for declining (optional)
                                    </label>
                                    <textarea v-model="declineReason" id="decline-reason" rows="3"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Provide a reason for the client..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                            <button @click="confirm" type="button"
                                :class="[modalConfig.confirmClass, 'w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm']">
                                {{ modalConfig.confirmText }}
                            </button>
                            <button @click="cancel" type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                                {{ modalConfig.cancelText }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Modal transitions */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-slide-enter-active {
    transition: all 0.3s ease;
}

.modal-slide-leave-active {
    transition: all 0.2s ease;
}

.modal-slide-enter-from {
    opacity: 0;
    transform: translateY(1rem) scale(0.95);
}

.modal-slide-leave-to {
    opacity: 0;
    transform: translateY(0) scale(0.95);
}
</style>
