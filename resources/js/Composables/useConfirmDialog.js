import { ref } from 'vue';

const isOpen = ref(false);
const resolveFn = ref(null);
const options = ref({
    title: '',
    message: '',
    type: 'default', // e.g. 'default', 'danger', 'success', etc.
    confirmText: 'Confirm',
    cancelText: 'Cancel',
});

export function useConfirmDialog() {
    const confirm = (opts) => {
        return new Promise((resolve) => {
            options.value = { ...options.value, ...opts };
            isOpen.value = true;
            resolveFn.value = resolve;
        });
    };

    const confirmAction = () => {
        resolveFn.value?.(true);
        isOpen.value = false;
    };

    const cancelAction = () => {
        resolveFn.value?.(false);
        isOpen.value = false;
    };

    return { isOpen, options, confirm, confirmAction, cancelAction };
}
