import { router } from '@inertiajs/vue3';
import { Ban, Clock, Trash2, UserCheck } from 'lucide-vue-next';
import { push } from 'notivue';
import { computed, ref } from 'vue';
import { useToast } from 'vue-toastification';

export function useUserActions() {
    const toast = useToast();

    // State
    const showActionModal = ref(false);
    const currentUser = ref(null);
    const currentAction = ref('selection');

    // Forms
    const suspendForm = ref({
        reason: '',
        suspended_until: null,
        suspension_type: 'permanent',
    });

    const banForm = ref({
        reason: '',
    });

    const deleteForm = ref({
        password: '',
    });

    // 🧠 Helpers
    const openActionModal = (user) => {
        currentUser.value = user;
        currentAction.value = 'selection';
        showActionModal.value = true;

        suspendForm.value = {
            reason: '',
            suspended_until: null,
            suspension_type: 'permanent',
        };
        banForm.value = { reason: '' };
        deleteForm.value = { password: '' };
    };

    const closeActionModal = () => {
        showActionModal.value = false;
        currentUser.value = null;
        currentAction.value = 'selection';
    };

    const isSuspended = (user) => {
        if (user.status !== 'suspended') return false;
        if (!user.suspended_until) return true;
        return new Date(user.suspended_until) > new Date();
    };

    const minDateTime = computed(() => {
        return new Date(Date.now() + 60 * 60 * 1000).toISOString().slice(0, 16);
    });

    // 🧩 Perform actions
    const performSuspend = async () => {
        if (!suspendForm.value.reason.trim()) {
            toast.error('Please provide a suspension reason');
            return;
        }
        if (
            suspendForm.value.suspension_type === 'temporary' &&
            !suspendForm.value.suspended_until
        ) {
            toast.error('Please select a suspension end date');
            return;
        }

        try {
            await router.patch(
                route('admin.users.status.update', currentUser.value.id),
                {
                    action: 'suspend',
                    reason: suspendForm.value.reason,
                    suspended_until:
                        suspendForm.value.suspension_type === 'temporary'
                            ? suspendForm.value.suspended_until
                            : null,
                },
                {
                    onSuccess: () => {
                        push.success('User suspended successfully');
                        closeActionModal();
                    },
                    onError: () => toast.error('Failed to suspend user'),
                },
            );
        } catch (error) {
            toast.error('Failed to suspend user');
        }
    };

    const performBan = async () => {
        if (!banForm.value.reason.trim()) {
            toast.error('Please provide a ban reason');
            return;
        }

        try {
            await router.patch(
                route('admin.users.status.update', currentUser.value.id),
                {
                    action: 'ban',
                    reason: banForm.value.reason,
                },
                {
                    onSuccess: () => {
                        push.success('User banned successfully');
                        closeActionModal();
                    },
                    onError: () => toast.error('Failed to ban user'),
                },
            );
        } catch (error) {
            toast.error('Failed to ban user');
        }
    };

    const performActivate = async () => {
        try {
            await router.patch(
                route('admin.users.status.update', currentUser.value.id),
                {
                    action: 'activate',
                    reason: '',
                },
                {
                    onSuccess: () => {
                        push.success('User activated successfully');
                        closeActionModal();
                    },
                    onError: () => toast.error('Failed to activate user'),
                },
            );
        } catch (error) {
            toast.error('Failed to activate user');
        }
    };

    const performDelete = async () => {
        if (!deleteForm.value.password.trim()) {
            toast.error('Please enter your password to confirm deletion');
            return;
        }

        try {
            await router.delete(
                route('admin.users.destroy', currentUser.value.id),
                {
                    data: { password: deleteForm.value.password },
                    onSuccess: () => {
                        push.success('User deleted successfully');
                        closeActionModal();
                    },
                    onError: (errors) => {
                        if (errors.password) {
                            toast.error(errors.password);
                        } else {
                            toast.error('Failed to delete user');
                        }
                    },
                },
            );
        } catch (error) {
            toast.error('Failed to delete user');
        }
    };

    const availableActions = computed(() => {
        const actions = [];
        if (
            currentUser.value?.status !== 'active' ||
            (currentUser.value?.status === 'suspended' &&
                isSuspended(currentUser.value))
        ) {
            actions.push({
                key: 'activate',
                label: 'Activate User',
                description: 'Restore full access and permissions',
                icon: UserCheck,
                color: 'green',
                action: performActivate,
            });
        }
        if (
            currentUser.value?.status !== 'suspended' ||
            !isSuspended(currentUser.value)
        ) {
            actions.push({
                key: 'suspend',
                label: 'Suspend User',
                description: 'Temporarily restrict access',
                icon: Clock,
                color: 'yellow',
            });
        }
        if (currentUser.value?.status !== 'banned') {
            actions.push({
                key: 'ban',
                label: 'Ban User',
                description: 'Permanently restrict access',
                icon: Ban,
                color: 'red',
            });
        }
        actions.push({
            key: 'delete',
            label: 'Delete User',
            description: 'Permanently remove from system',
            icon: Trash2,
            color: 'gray',
        });
        return actions;
    });

    return {
        // state
        showActionModal,
        currentUser,
        currentAction,
        suspendForm,
        banForm,
        deleteForm,
        minDateTime,
        availableActions,

        // methods
        openActionModal,
        closeActionModal,
        performSuspend,
        performBan,
        performActivate,
        performDelete,
        isSuspended,
    };
}
