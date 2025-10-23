import { router } from '@inertiajs/vue3';

export function chatClient(userId) {
    router.visit(route('vendor.messages.index'), {
        method: 'get',
        data: { participants: [userId], type: 'direct' },
        preserveScroll: false,
        preserveState: false,
    });
}
