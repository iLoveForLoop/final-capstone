// /resources/js/Composables/useFlash.js
import { usePage } from '@inertiajs/vue3';
import { push } from 'notivue';
import { watch } from 'vue';

export default function useFlash() {
    const page = usePage();

    // Watch for flash changes
    watch(
        () => page.props.flash,
        (flash) => {
            if (flash.success) {
                push.success(flash.success);
                flash.success = null;
            }

            if (flash.error) {
                push.error(flash.error);
                flash.error = null;
            }

            if (flash.info) {
                push.info(flash.info);
                flash.info = null;
            }
        },
        { deep: true, immediate: true },
    );
}
