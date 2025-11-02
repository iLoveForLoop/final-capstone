<script setup>
import { defineProps, defineEmits } from 'vue';


const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    review: {
        type: Object,
        default: null,
    },
});



const emit = defineEmits(['close', 'delete-review']);

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const handleDelete = async () => {

    emit('delete-review', props.review.id);
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-40"
        @click.self="emit('close')">
        <div class="bg-white rounded-2xl w-full max-w-2xl shadow-xl p-6 relative animate-fadeIn">
            <!-- Close button -->
            <button @click="emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 transition">
                ✕
            </button>

            <!-- Header -->
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Review Details</h2>

            <div v-if="review" class="space-y-5">
                <!-- Customer Info -->
                <div>
                    <p class="text-xs uppercase text-gray-500 mb-1">Customer</p>
                    <p class="text-gray-800 font-medium">{{ review.user.name }}</p>
                    <p class="text-gray-500 text-sm">{{ review.user.email }}</p>
                </div>

                <!-- Vendor Info -->
                <div>
                    <p class="text-xs uppercase text-gray-500 mb-1">Vendor</p>
                    <p class="text-gray-800 font-medium">{{ review.vendor.business_name }}</p>
                </div>

                <!-- Rating -->
                <div>
                    <p class="text-xs uppercase text-gray-500 mb-1">Rating</p>
                    <div class="text-yellow-500">
                        <span v-for="n in review.rating" :key="n">★</span>
                        <span v-for="n in 5 - review.rating" :key="'empty' + n" class="text-gray-300">★</span>
                    </div>
                </div>

                <!-- Comment -->
                <div>
                    <p class="text-xs uppercase text-gray-500 mb-1">Customer Comment</p>
                    <p class="text-gray-700 leading-relaxed bg-gray-50 p-3 rounded-lg">
                        {{ review.comment }}
                    </p>
                </div>

                <!-- Vendor Response -->
                <div v-if="review.response">
                    <p class="text-xs uppercase text-gray-500 mb-1">Vendor Response</p>
                    <p class="italic text-gray-700 bg-indigo-50 p-3 rounded-lg">
                        {{ review.response }}
                    </p>
                </div>

                <!-- Date -->
                <div class="pt-2 border-t text-sm text-gray-400">
                    Submitted on {{ formatDate(review.created_at) }}
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end mt-6 border-t pt-4 space-x-3">
                <button @click="emit('close')"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Close
                </button>
                <button @click="handleDelete"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Delete Review
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.2s ease-out;
}
</style>
