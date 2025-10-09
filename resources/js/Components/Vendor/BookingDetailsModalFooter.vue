<script setup>
import { MessageCircle } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
const props = defineProps({
    booking: Object
})

const emit = defineEmits(['close', 'accept-booking', 'decline-booking', 'complete-booking'])



const chatClient = () => {
    console.log(props.booking.user.id)

    router.visit(route('vendor.messages.index'), { method: 'get', data: { participants: [props.booking.user.id], type: 'direct' }, preserveScroll: false, preserveState: false });

    // console.log('hereereer')
}

</script>

<template>
    <!-- Footer with Actions -->
    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex-shrink-0">

        <div class="flex justify-end space-x-3">
            <button @click="emit('close')"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">
                Close
            </button>
            <button @click="emit('accept-booking', booking)" v-if="booking?.status === 'pending'"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm">
                Accept Booking
            </button>
            <button @click="emit('decline-booking', booking)" v-if="booking?.status === 'pending'"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm">
                Decline Booking
            </button>
            <button @click="emit('complete-booking', booking)" v-if="booking?.status === 'confirmed'"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                Mark as Complete
            </button>
            <button @click="chatClient"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm flex items-center space-x-2">
                <MessageCircle :size="16" />
                <span>Chat Client</span>
            </button>
        </div>
    </div>
</template>

<style></style>
