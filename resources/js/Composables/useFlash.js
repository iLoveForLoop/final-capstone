// resources/js/Composables/useFlash.js
import { usePage } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

export default function useFlash() {
    const toast = useToast();
    const { props } = usePage();

    if (props.flash.success) {
        toast.success(props.flash.success);
        props.flash.success = null;
    }

    if (props.flash.error) {
        toast.error(props.flash.error);
        props.flash.error = null;
    }

    if (props.flash.info) {
        toast.info(props.flash.info);
        props.flash.info = null;
    }
}
