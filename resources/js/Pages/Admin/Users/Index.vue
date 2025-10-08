<script setup>
import TestLayout from '@/Layouts/TestLayout.vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import UserCreateModal from '@/Components/Admin/UserCreateModal.vue';
import Pagination from '@/Components/Pagination.vue';
import UserEditModal from '@/Components/Admin/UserEditModal.vue';
import {
    Eye,
    Edit3,
    MoreVertical,
    X,
    UserCheck,
    AlertCircle,
    Ban,
    Trash2,
    Plus,
    Search,
    Shield,
    Clock,
    AlertTriangle
} from 'lucide-vue-next';
import { push } from 'notivue'
import StatusAction from '@/Components/Admin/StatusAction.vue';

const toast = useToast();

const userCreateModal = ref(null);
const userEditModal = ref(null);

const props = defineProps({
    users: {
        type: Object,
        default: () => ({ data: [] })
    },
    filters: {
        type: Object,
        default: () => ({
            role: 'all',
            search: '',
            status: 'all'
        })
    },
    service_categories: {
        type: Array,
        default: []
    }
});

const roles = [
    { value: 'all', label: 'All Users' },
    { value: 'admin', label: 'Admins' },
    { value: 'vendor', label: 'Vendors' },
    { value: 'client', label: 'Clients' }
];

const statuses = [
    { value: 'all', label: 'All Statuses' },
    { value: 'active', label: 'Active' },
    { value: 'suspended', label: 'Suspended' },
    { value: 'banned', label: 'Banned' }
];

const selectedRole = ref(props.filters.role || 'all');
const searchQuery = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || 'all');

// Modal state
const showActionModal = ref(false);
const currentUser = ref(null);
const currentAction = ref('selection');

// Form states
const suspendForm = ref({
    reason: '',
    suspended_until: null,
    suspension_type: 'permanent'
});

const banForm = ref({
    reason: ''
});

const deleteForm = ref({
    password: ''
});

const applyFilters = () => {
    router.get(route('admin.users.index'), {
        role: selectedRole.value,
        search: searchQuery.value,
        status: selectedStatus.value
    }, {
        preserveState: true,
        replace: true
    });
};

const resetFilters = () => {
    selectedRole.value = 'all';
    searchQuery.value = '';
    selectedStatus.value = 'all';
    applyFilters();
};

const openActionModal = (user) => {
    currentUser.value = user;
    currentAction.value = 'selection';
    showActionModal.value = true;

    // Reset forms
    suspendForm.value = {
        reason: '',
        suspended_until: null,
        suspension_type: 'permanent'
    };
    banForm.value = { reason: '' };
    deleteForm.value = { password: '' };
};

const closeActionModal = () => {
    showActionModal.value = false;
    currentUser.value = null;
    currentAction.value = 'selection';
};

const performSuspend = async () => {
    if (!suspendForm.value.reason.trim()) {
        toast.error('Please provide a suspension reason');
        return;
    }

    if (suspendForm.value.suspension_type === 'temporary' && !suspendForm.value.suspended_until) {
        toast.error('Please select a suspension end date');
        return;
    }


    // console.log('Suspend: ', suspendForm);


    try {
        await router.patch(route('admin.users.status.update', currentUser.value.id), {
            action: 'suspend',
            reason: suspendForm.value.reason,
            suspended_until: suspendForm.value.suspension_type === 'temporary' ? suspendForm.value.suspended_until : null
        }, {
            onSuccess: () => {
                // toast.success('User suspended successfully');
                push.success('User suspended successfully')
                closeActionModal();
            },
            onError: () => {
                toast.error('Failed to suspend user');
            }
        });
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
        await router.patch(route('admin.users.status.update', currentUser.value.id), {
            action: 'ban',
            reason: banForm.value.reason
        }, {
            onSuccess: () => {
                push.success('User banned successfully')
                closeActionModal();
            },
            onError: () => {
                toast.error('Failed to ban user');
            }
        });
    } catch (error) {
        toast.error('Failed to ban user');
    }
};

const performActivate = async () => {
    try {
        await router.patch(route('admin.users.status.update', currentUser.value.id), {
            action: 'activate',
            reason: ''
        }, {
            onSuccess: () => {
                // toast.success('User activated successfully');
                push.success('User activated successfully')
                closeActionModal();
            },
            onError: () => {
                toast.error('Failed to activate user');
            }
        });
    } catch (error) {
        toast.error('Failed to activate user');
    }
};

const performDelete = async () => {

    // push.success('User deleted successfully')



    if (!deleteForm.value.password.trim()) {
        toast.error('Please enter your password to confirm deletion');
        return;
    }

    try {
        await router.delete(route('admin.users.destroy', currentUser.value.id), {
            data: { password: deleteForm.value.password },
            onSuccess: () => {
                // toast.success('User deleted successfully');
                console.log('Test');
                push.success('User deleted successfully')

                closeActionModal();
            },
            onError: (errors) => {
                if (errors.password) {
                    toast.error(errors.password);
                } else {
                    toast.error('Failed to delete user');
                }
            }
        });
    } catch (error) {
        toast.error('Failed to delete user');
    }
};

const editUser = (user) => {
    userEditModal.value.show(user);
}

const viewUser = (userId) => {
    router.get(route('admin.users.show', userId))
}

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const isSuspended = (user) => {
    if (user.status !== 'suspended') return false;
    if (!user.suspended_until) return true;
    return new Date(user.suspended_until) > new Date();
};

const minDateTime = computed(() => {
    return new Date(Date.now() + 60 * 60 * 1000).toISOString().slice(0, 16);
});

const availableActions = computed(() => {
    const actions = [];

    if (currentUser.value?.status !== 'active' || (currentUser.value?.status === 'suspended' && isSuspended(currentUser.value))) {
        actions.push({
            key: 'activate',
            label: 'Activate User',
            description: 'Restore full access and permissions',
            icon: UserCheck,
            color: 'green',
            action: performActivate
        });
    }

    if (currentUser.value?.status !== 'suspended' || !isSuspended(currentUser.value)) {
        actions.push({
            key: 'suspend',
            label: 'Suspend User',
            description: 'Temporarily restrict access',
            icon: Clock,
            color: 'yellow'
        });
    }

    if (currentUser.value?.status !== 'banned') {
        actions.push({
            key: 'ban',
            label: 'Ban User',
            description: 'Permanently restrict access',
            icon: Ban,
            color: 'red'
        });
    }

    actions.push({
        key: 'delete',
        label: 'Delete User',
        description: 'Permanently remove from system',
        icon: Trash2,
        color: 'gray'
    });

    return actions;
});

const getActionIcon = (action) => {
    return availableActions.value.find(a => a.key === action)?.icon;
};
</script>

<template>
    <TestLayout>
        <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
                    <p class="text-gray-500 text-sm mt-1">Manage all platform users and permissions</p>
                </div>
                <button @click="userCreateModal.show()"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 text-sm">
                    <Plus :size="16" />
                    Add User
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
                        <select v-model="selectedRole" @change="applyFilters"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option v-for="role in roles" :key="role.value" :value="role.value">{{ role.label }}
                            </option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select v-model="selectedStatus" @change="applyFilters"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option v-for="status in statuses" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <div class="relative">
                            <input v-model="searchQuery" @input="applyFilters" type="text" placeholder="Search users..."
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 pl-9 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Search :size="16" class="text-gray-400" />
                            </div>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button @click="resetFilters"
                            class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center gap-1 h-[36px]">
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- User Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Registered
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full overflow-hidden">
                                            <template v-if="user.image_url">
                                                <img :src="user.image_url" :alt="user.name"
                                                    class="h-full w-full object-cover">
                                            </template>
                                            <template v-else>
                                                <div
                                                    class="h-full w-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-medium text-sm">
                                                    {{ user.name.charAt(0).toUpperCase() }}
                                                </div>
                                            </template>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                            <div class="text-xs text-gray-500">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex gap-1">
                                        <span v-for="role in user.roles" :key="role.id" :class="{
                                            'bg-purple-100 text-purple-800': role.name === 'admin',
                                            'bg-green-100 text-green-800': role.name === 'vendor',
                                            'bg-blue-100 text-blue-800': role.name === 'client'
                                        }" class="px-2 py-1 text-xs font-medium rounded-full capitalize">
                                            {{ role.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="{
                                        'bg-green-100 text-green-800': user.status === 'active',
                                        'bg-yellow-100 text-yellow-800': user.status === 'suspended' && isSuspended(user),
                                        'bg-gray-100 text-gray-800': user.status === 'suspended' && !isSuspended(user),
                                        'bg-red-100 text-red-800': user.status === 'banned',
                                    }" class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ user.status.charAt(0).toUpperCase() + user.status.slice(1) }}
                                        <span v-if="user.status === 'suspended' && !isSuspended(user)"
                                            class="ml-1 text-xs">
                                            (Expired)
                                        </span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <button @click="viewUser(user.id)"
                                            class="text-indigo-600 hover:text-indigo-900 p-1 rounded transition-colors"
                                            title="View User">
                                            <Eye :size="16" />
                                        </button>
                                        <button @click="editUser(user)"
                                            class="text-yellow-600 hover:text-yellow-900 p-1 rounded transition-colors"
                                            title="Edit User">
                                            <Edit3 :size="16" />
                                        </button>
                                        <button @click="openActionModal(user)"
                                            class="text-gray-400 hover:text-gray-600 p-1 rounded transition-colors"
                                            title="User Actions">
                                            <MoreVertical :size="16" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No users found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination :data="users" />
            </div>



            <!-- Modals -->
            <UserCreateModal ref="userCreateModal" :categories="service_categories" />
            <UserEditModal ref="userEditModal" :categories="service_categories" />
        </div>
        <!-- Action Drawer -->

        <!-- Backdrop -->
        <div v-if="showActionModal" class="absolute inset-0 bg-black bg-opacity-50 transition-opacity duration-300"
            @click="closeActionModal">
        </div>

        <transition enter-active-class="transform transition ease-in-out duration-300"
            enter-from-class="translate-x-full" enter-to-class="translate-x-0"
            leave-active-class="transform transition ease-in-out duration-300" leave-from-class="translate-x-0"
            leave-to-class="translate-x-full">
            <!-- Drawer Panel -->


            <div v-if="showActionModal" class="absolute min-h-screen inset-y-0 right-0 pl-10 max-w-full flex 0">
                <StatusAction :currentUser="currentUser" :availableActions="availableActions"
                    @close-action-modal="closeActionModal" @perform-suspend="performSuspend"
                    @perform-delete="performDelete" @perform-ban="performBan" v-model:suspendForm="suspendForm"
                    v-model:banForm="banForm" v-model:currentAction="currentAction" />
            </div>
        </transition>
    </TestLayout>


</template>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
