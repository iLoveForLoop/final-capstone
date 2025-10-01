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
                toast.success('User suspended successfully');
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
                toast.success('User banned successfully');
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
                toast.success('User activated successfully');
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
    if (!deleteForm.value.password.trim()) {
        toast.error('Please enter your password to confirm deletion');
        return;
    }

    try {
        await router.delete(route('admin.users.destroy', currentUser.value.id), {
            data: { password: deleteForm.value.password },
            onSuccess: () => {
                toast.success('User deleted successfully');
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
                <div class="w-screen max-w-md">
                    <div class="h-full flex flex-col bg-white shadow-xl">
                        <!-- Header -->
                        <div class="bg-indigo-700 px-4 py-6 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-white">
                                        User Status
                                    </h2>
                                    <!-- <p class="text-indigo-100 text-sm mt-1">Manage user status and
                                        permissions</p> -->
                                </div>
                                <button @click="closeActionModal"
                                    class="text-indigo-100 hover:text-white transition-colors rounded-md p-1">
                                    <X :size="20" />
                                </button>
                            </div>

                            <!-- User Info -->
                            <div class="mt-4 flex items-center space-x-3">
                                <div
                                    class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden border-2 border-indigo-200">
                                    <template v-if="currentUser?.image_url">
                                        <img :src="currentUser.image_url" :alt="currentUser.name"
                                            class="h-full w-full object-cover">
                                    </template>
                                    <template v-else>
                                        <div
                                            class="h-full w-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-base">
                                            {{ currentUser?.name?.charAt(0)?.toUpperCase() }}
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{
                                        currentUser?.name }}</p>
                                    <p class="text-indigo-100 text-xs truncate">{{ currentUser?.email }}</p>
                                    <div class="flex items-center mt-1">
                                        <span
                                            class="text-xs font-medium px-2 py-0.5 rounded-full bg-white bg-opacity-20 text-white">
                                            {{ currentUser?.status?.charAt(0)?.toUpperCase() +
                                                currentUser?.status?.slice(1) }}
                                        </span>
                                        <span class="text-indigo-200 text-xs ml-2">ID: {{ currentUser?.id
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto">
                            <!-- Action Selection -->
                            <div v-if="currentAction === 'selection'" class="p-6 space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Select Action</h3>
                                <p class="text-sm text-gray-500 mb-6">
                                    Choose an action to perform on this user account
                                </p>

                                <div class="space-y-3">
                                    <button v-for="action in availableActions" :key="action.key"
                                        @click="action.action ? action.action() : currentAction = action.key" :class="[
                                            'w-full flex items-center p-4 rounded-lg border transition-all duration-200',
                                            action.color === 'green' ? 'border-green-200 hover:border-green-300 hover:bg-green-50' :
                                                action.color === 'yellow' ? 'border-yellow-200 hover:border-yellow-300 hover:bg-yellow-50' :
                                                    action.color === 'red' ? 'border-red-200 hover:border-red-300 hover:bg-red-50' :
                                                        'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                                        ]">
                                        <component :is="action.icon" :class="[
                                            'h-5 w-5 mr-3',
                                            action.color === 'green' ? 'text-green-600' :
                                                action.color === 'yellow' ? 'text-yellow-600' :
                                                    action.color === 'red' ? 'text-red-600' :
                                                        'text-gray-600'
                                        ]" />
                                        <div class="text-left flex-1">
                                            <div class="font-medium text-gray-900">{{ action.label }}</div>
                                            <div class="text-sm text-gray-500">{{ action.description }}
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Suspend Form -->
                            <div v-if="currentAction === 'suspend'" class="p-6 space-y-6">
                                <div class="flex items-center space-x-3 mb-2">
                                    <button @click="currentAction = 'selection'"
                                        class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <h3 class="text-lg font-medium text-gray-900">Suspend User</h3>
                                </div>

                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex">
                                        <AlertCircle class="h-5 w-5 text-yellow-400 flex-shrink-0 mt-0.5" />
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-yellow-800">Temporary
                                                Restriction</h4>
                                            <p class="text-sm text-yellow-700 mt-1">
                                                User will be unable to access the platform during the
                                                suspension period.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Suspension
                                            Type</label>
                                        <div class="space-y-3">
                                            <div class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                                                @click="suspendForm.suspension_type = 'permanent'">
                                                <input type="radio" v-model="suspendForm.suspension_type"
                                                    value="permanent"
                                                    class="mt-1 focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300">
                                                <div class="ml-3">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700">Permanent</label>
                                                    <p class="text-sm text-gray-500 mt-1">Manual
                                                        reinstatement required
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                                                @click="suspendForm.suspension_type = 'temporary'">
                                                <input type="radio" v-model="suspendForm.suspension_type"
                                                    value="temporary"
                                                    class="mt-1 focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300">
                                                <div class="ml-3">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700">Temporary</label>
                                                    <p class="text-sm text-gray-500 mt-1">Automatic
                                                        reinstatement after
                                                        specified date</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="suspendForm.suspension_type === 'temporary'">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">End
                                            Date</label>
                                        <input type="datetime-local" v-model="suspendForm.suspended_until"
                                            :min="minDateTime"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                        <p class="text-xs text-gray-500 mt-1">Suspension will be
                                            automatically lifted
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                                        <textarea v-model="suspendForm.reason" rows="3"
                                            placeholder="Provide reason for suspension..."
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Ban Form -->
                            <div v-if="currentAction === 'ban'" class="p-6 space-y-6">
                                <div class="flex items-center space-x-3 mb-2">
                                    <button @click="currentAction = 'selection'"
                                        class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <h3 class="text-lg font-medium text-gray-900">Ban User</h3>
                                </div>

                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex">
                                        <AlertTriangle class="h-5 w-5 text-red-400 flex-shrink-0 mt-0.5" />
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-red-800">Permanent
                                                Restriction</h4>
                                            <p class="text-sm text-red-700 mt-1">
                                                This action cannot be automatically reversed. User will be
                                                permanently
                                                barred.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                                    <textarea v-model="banForm.reason" rows="4"
                                        placeholder="Provide detailed reason for permanent ban..."
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                                </div>
                            </div>

                            <!-- Delete Form -->
                            <div v-if="currentAction === 'delete'" class="p-6 space-y-6">
                                <div class="flex items-center space-x-3 mb-2">
                                    <button @click="currentAction = 'selection'"
                                        class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <h3 class="text-lg font-medium text-gray-900">Delete User</h3>
                                </div>

                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex">
                                        <AlertTriangle class="h-5 w-5 text-red-400 flex-shrink-0 mt-0.5" />
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-red-800">Critical Action
                                            </h4>
                                            <p class="text-sm text-red-700 mt-1">
                                                This action cannot be undone. All user data will be
                                                permanently deleted.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm
                                        Password</label>
                                    <input type="password" v-model="deleteForm.password"
                                        placeholder="Enter your admin password..."
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="border-t border-gray-200 px-4 py-4">
                            <div class="flex justify-end space-x-3">
                                <button @click="closeActionModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Cancel
                                </button>
                                <button v-if="currentAction === 'suspend'" @click="performSuspend"
                                    :disabled="!suspendForm.reason.trim()" :class="[
                                        'px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors',
                                        suspendForm.reason.trim()
                                            ? 'bg-yellow-600 hover:bg-yellow-700'
                                            : 'bg-yellow-400 cursor-not-allowed'
                                    ]">
                                    Suspend User
                                </button>
                                <button v-if="currentAction === 'ban'" @click="performBan"
                                    :disabled="!banForm.reason.trim()" :class="[
                                        'px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors',
                                        banForm.reason.trim()
                                            ? 'bg-red-600 hover:bg-red-700'
                                            : 'bg-red-400 cursor-not-allowed'
                                    ]">
                                    Ban User
                                </button>
                                <button v-if="currentAction === 'delete'" @click="performDelete"
                                    :disabled="!deleteForm.password.trim()" :class="[
                                        'px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors',
                                        deleteForm.password.trim()
                                            ? 'bg-gray-700 hover:bg-gray-800'
                                            : 'bg-gray-400 cursor-not-allowed'
                                    ]">
                                    Delete User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
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
