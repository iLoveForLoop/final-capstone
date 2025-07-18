<script setup>
import TestLayout from '@/Layouts/TestLayout.vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import UserCreateModal from '@/Components/Admin/UserCreateModal.vue';
import Pagination from '@/Components/Pagination.vue';
import UserEditModal from '@/Components/Admin/UserEditModal.vue';

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
            search: ''
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

const selectedRole = ref(props.filters.role || 'all');
const searchQuery = ref(props.filters.search || '');

const applyFilters = () => {
    router.get(route('admin.users.index'), {
        role: selectedRole.value,
        search: searchQuery.value
    }, {
        preserveState: true,
        replace: true
    });
};

const resetFilters = () => {
    selectedRole.value = 'all';
    searchQuery.value = '';
    applyFilters();
};

const deleteUser = (userId) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('admin.users.destroy', userId), {
            onSuccess: () => {
                toast.success('User deleted successfully');
            },
            onError: () => {
                toast.error('Failed to delete user');
            }
        });
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
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
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <div class="relative">
                            <input v-model="searchQuery" @input="applyFilters" type="text" placeholder="Search users..."
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 pl-9 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
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
                                    User</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Registered</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">


                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full overflow-hidden">
                                            <!-- Show image if available, otherwise show initial -->
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
                                    <span v-if="user.roles.some(r => r.name === 'vendor')" :class="{
                                        'bg-green-100 text-green-800': user.vendor && user.vendor.is_approved,
                                        'bg-yellow-100 text-yellow-800': !(user.vendor && user.vendor.is_approved)
                                    }" class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ user.vendor?.is_approved ? 'Approved' : 'Pending' }}
                                        <!-- {{ console.log(user.service_categories) }} -->
                                    </span>
                                    <span v-else
                                        class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        Active
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-3">
                                        <button @click="viewUser(user.id)"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd"
                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button @click="editUser(user)" class="text-yellow-600 hover:text-yellow-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                        <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
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
                <Pagination :users="users" />
            </div>
            <!-- Create Modal -->
            <UserCreateModal ref="userCreateModal" :categories="service_categories" />
            <!-- Edit Modal -->
            <UserEditModal ref="userEditModal" :categories="service_categories" />

        </div>
    </TestLayout>
</template>

<style scoped>
/* Smooth transitions */
button,
select,
input {
    transition: all 0.15s ease;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    height: 6px;
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
