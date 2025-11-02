<template>
    <Transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-xl w-[90%] max-w-md p-6">
                <h2 class="text-lg font-semibold mb-2">{{ options.title }}</h2>
                <p class="text-gray-600 mb-6">{{ options.message }}</p>

                <div class="flex justify-end gap-3">
                    <button @click="cancelAction"
                        class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">
                        {{ options.cancelText }}
                    </button>

                    <button @click="confirmAction" :class="[
                        'px-4 py-2 rounded-lg text-white',
                        options.type === 'danger'
                            ? 'bg-red-500 hover:bg-red-600'
                            : options.type === 'success'
                                ? 'bg-green-500 hover:bg-green-600'
                                : 'bg-blue-500 hover:bg-blue-600'
                    ]">
                        {{ options.confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { useConfirmDialog } from '@/Composables/useConfirmDialog'

const { isOpen, options, confirmAction, cancelAction } = useConfirmDialog()
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
