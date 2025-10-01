<template>
    <Modal :show="showModal" @close="closeModal" max-width="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ title }}
            </h2>

            <div class="space-y-4">
                <!-- Reason Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Reason {{ action === 'activate' ? '(Optional)' : '' }}
                    </label>
                    <textarea v-model="form.reason" rows="3"
                        class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        :placeholder="reasonPlaceholder"></textarea>
                </div>

                <!-- Suspension Duration (only for suspend action) -->
                <div v-if="action === 'suspend'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Suspension Duration
                    </label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="radio" id="permanent" value="permanent" v-model="suspensionType"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                            <label for="permanent" class="ml-2 block text-sm text-gray-700">
                                Permanent
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio" id="temporary" value="temporary" v-model="suspensionType"
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                            <label for="temporary" class="ml-2 block text-sm text-gray-700">
                                Temporary
                            </label>
                        </div>

                        <div v-if="suspensionType === 'temporary'" class="ml-6">
                            <input type="datetime-local" v-model="form.suspended_until" :min="minDateTime"
                                class="border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">
                                User will be automatically unsuspended after this time
                            </p>
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="bg-gray-50 p-3 rounded-md">
                    <p class="text-sm text-gray-600">
                        User: <strong>{{ user.name }}</strong> ({{ user.email }})
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        Current Status: <span :class="statusColor">{{ user.status }}</span>
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button @click="closeModal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </button>
                <button @click="submit" :class="buttonColor"
                    class="px-4 py-2 text-sm font-medium text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2">
                    {{ confirmButtonText }}
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import Modal from '@/Components/Modal.vue';

const toast = useToast();

const showModal = ref(false);
const user = ref({});
const action = ref('');
const suspensionType = ref('permanent');
const form = ref({
    reason: '',
    suspended_until: null
});

const minDateTime = computed(() => {
    return new Date(Date.now() + 60 * 60 * 1000).toISOString().slice(0, 16);
});

const title = computed(() => {
    const actions = {
        activate: 'Activate User',
        suspend: 'Suspend User',
        ban: 'Ban User'
    };
    return actions[action.value] || 'User Action';
});

const reasonPlaceholder = computed(() => {
    const placeholders = {
        activate: 'Reason for activation (optional)...',
        suspend: 'Reason for suspension...',
        ban: 'Reason for ban...'
    };
    return placeholders[action.value] || 'Reason...';
});

const confirmButtonText = computed(() => {
    const texts = {
        activate: 'Activate User',
        suspend: 'Suspend User',
        ban: 'Ban User'
    };
    return texts[action.value] || 'Confirm';
});

const buttonColor = computed(() => {
    const colors = {
        activate: 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
        suspend: 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500',
        ban: 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
    };
    return colors[action.value] || 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500';
});

const statusColor = computed(() => {
    const colors = {
        active: 'text-green-600',
        suspended: 'text-yellow-600',
        banned: 'text-red-600'
    };
    return colors[user.value.status] || 'text-gray-600';
});

const show = (userData, actionType) => {
    user.value = userData;
    action.value = actionType;
    form.value.reason = '';
    form.value.suspended_until = null;
    suspensionType.value = 'permanent';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    user.value = {};
    action.value = '';
    form.value = {
        reason: '',
        suspended_until: null
    };
};

const submit = () => {
    if ((action.value === 'suspend' || action.value === 'ban') && !form.value.reason.trim()) {
        toast.error('Please provide a reason for this action');
        return;
    }

    if (action.value === 'suspend' && suspensionType.value === 'temporary' && !form.value.suspended_until) {
        toast.error('Please select a suspension end date');
        return;
    }

    const payload = {
        action: action.value,
        reason: form.value.reason,
        suspended_until: action.value === 'suspend' && suspensionType.value === 'temporary' ? form.value.suspended_until : null
    };

    router.patch(route('admin.users.status.update', user.value.id), payload, {
        onSuccess: () => {
            toast.success(`User ${action.value}ed successfully`);
            closeModal();
        },
        onError: () => {
            toast.error(`Failed to ${action.value} user`);
        }
    });
};

defineExpose({
    show
});
</script>
