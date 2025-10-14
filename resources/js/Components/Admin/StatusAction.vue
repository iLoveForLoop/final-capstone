<script setup>
import { AlertCircle, AlertTriangle, X } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps({
    currentUser: Object,
    availableActions: Array
})

const emit = defineEmits(['close-action-modal', 'perform-suspend', 'perform-delete', 'perform-ban'])

const suspendForm = defineModel('suspendForm')
const deleteForm = defineModel('deleteForm')
const banForm = defineModel('banForm')
const currentAction = defineModel('currentAction')

const minDateTime = computed(() => {
    return new Date(Date.now() + 60 * 60 * 1000).toISOString().slice(0, 16);
});
</script>

<template>
    <!-- Drawer Panel -->
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
                    <button @click="emit('close-action-modal')"
                        class="text-indigo-100 hover:text-white transition-colors rounded-md p-1">
                        <X :size="20" />
                    </button>
                </div>

                <!-- User Info -->
                <div class="mt-4 flex items-center space-x-3">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden border-2 border-indigo-200">
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
                                    <input type="radio" v-model="suspendForm.suspension_type" value="permanent"
                                        class="mt-1 focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300">
                                    <div class="ml-3">
                                        <label class="block text-sm font-medium text-gray-700">Permanent</label>
                                        <p class="text-sm text-gray-500 mt-1">Manual
                                            reinstatement required
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                                    @click="suspendForm.suspension_type = 'temporary'">
                                    <input type="radio" v-model="suspendForm.suspension_type" value="temporary"
                                        class="mt-1 focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300">
                                    <div class="ml-3">
                                        <label class="block text-sm font-medium text-gray-700">Temporary</label>
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
                            <input type="datetime-local" v-model="suspendForm.suspended_until" :min="minDateTime"
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
                        <input type="password" v-model="deleteForm.password" placeholder="Enter your admin password..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 px-4 py-4">
                <div class="flex justify-end space-x-3">
                    <button @click="emit('close-action-modal')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button v-if="currentAction === 'suspend'" @click="emit('perform-suspend')"
                        :disabled="!suspendForm.reason.trim()" :class="[
                            'px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors',
                            suspendForm.reason.trim()
                                ? 'bg-yellow-600 hover:bg-yellow-700'
                                : 'bg-yellow-400 cursor-not-allowed'
                        ]">
                        Suspend User
                    </button>
                    <button v-if="currentAction === 'ban'" @click="emit('perform-ban')"
                        :disabled="!banForm.reason.trim()" :class="[
                            'px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors',
                            banForm.reason.trim()
                                ? 'bg-red-600 hover:bg-red-700'
                                : 'bg-red-400 cursor-not-allowed'
                        ]">
                        Ban User
                    </button>
                    <button v-if="currentAction === 'delete'" @click="emit('perform-delete')"
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
</template>

<style></style>
