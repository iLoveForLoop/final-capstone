<script setup>
import { Link } from '@inertiajs/vue3';
import {
    ChevronDown,
    BarChart3,
    ShoppingCart,
    Building2,
    MessageCircle,
    Users,
    FolderOpen,
    Mail,
    CheckSquare
} from 'lucide-vue-next';
import { ref } from 'vue';

const isBlogOpen = ref(false);
const isMailOpen = ref(false);

// Active states - you can manage these through props or route checking
const activeItem = ref('overview'); // Current active main item
const activeSubItem = ref(''); // Current active sub item

const toggleBlog = () => {
    isBlogOpen.value = !isBlogOpen.value;
}

const toggleMail = () => {
    isMailOpen.value = !isMailOpen.value;
}

const setActiveItem = (item) => {
    activeItem.value = item;
    activeSubItem.value = null;
}

const setActiveSubItem = (subItem) => {
    activeSubItem.value = subItem;
}
</script>

<template>
    <div class="w-52 flex flex-col h-full bg-gray-200 shadow-xl border-r border-gray-100 ">
        <!-- Dashboards Section -->
        <div class="px-4 py-6">
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                    DASHBOARDS
                </h3>
                <div class="space-y-1">
                    <Link href="#" @click="setActiveItem('overview')" :class="[
                        'group flex items-center px-3 py-2 text-md font-medium rounded-lg transition-all duration-200',
                        activeItem === 'overview'
                            ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]">
                    <BarChart3 :class="[
                        'mr-3 h-4 w-4 transition-colors duration-200',
                        activeItem === 'overview'
                            ? 'text-blue-600'
                            : 'text-gray-400 group-hover:text-gray-600'
                    ]" />
                    Dashboard
                    </Link>
                </div>
            </div>


            <div class="mb-6">
                <div class="space-y-1">
                    <!-- Users Section -->
                    <!-- <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                        MANAGE USERS
                    </h3> -->
                    <Link href="#" @click="setActiveItem('chat')" :class="[
                        'group flex items-center px-3 py-2 text-md font-medium rounded-lg transition-all duration-200',
                        activeItem === 'chat'
                            ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]">
                    <Users :class="[
                        'mr-3 h-4 w-4 transition-colors duration-200',
                        activeItem === 'chat'
                            ? 'text-blue-600'
                            : 'text-gray-400 group-hover:text-gray-600'
                    ]" />
                    Users
                    </Link>

                    <Link href="#" @click="setActiveItem('chat')" :class="[
                        'group flex items-center px-3 py-2 text-md font-medium rounded-lg transition-all duration-200',
                        activeItem === 'chat'
                            ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]">
                    <Users :class="[
                        'mr-3 h-4 w-4 transition-colors duration-200',
                        activeItem === 'chat'
                            ? 'text-blue-600'
                            : 'text-gray-400 group-hover:text-gray-600'
                    ]" />
                    Organizers
                    </Link>
                    <!-- Contennt and Listing Section -->
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                        Content and Listing
                    </h3>
                    <Link href="#" @click="setActiveItem('chat')" :class="[
                        'group flex items-center px-3 py-2 text-md font-medium rounded-lg transition-all duration-200',
                        activeItem === 'chat'
                            ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]">
                    <Users :class="[
                        'mr-3 h-4 w-4 transition-colors duration-200',
                        activeItem === 'chat'
                            ? 'text-blue-600'
                            : 'text-gray-400 group-hover:text-gray-600'
                    ]" />
                    Services
                    </Link>

                    <Link href="#" @click="setActiveItem('chat')" :class="[
                        'group flex items-center px-3 py-2 text-md font-medium rounded-lg transition-all duration-200',
                        activeItem === 'chat'
                            ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]">
                    <Users :class="[
                        'mr-3 h-4 w-4 transition-colors duration-200',
                        activeItem === 'chat'
                            ? 'text-blue-600'
                            : 'text-gray-400 group-hover:text-gray-600'
                    ]" />
                    Bookings
                    </Link>

                    <Link href="#" @click="setActiveItem('chat')" :class="[
                        'group flex items-center px-3 py-2 text-md font-medium rounded-lg transition-all duration-200',
                        activeItem === 'chat'
                            ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]">
                    <Users :class="[
                        'mr-3 h-4 w-4 transition-colors duration-200',
                        activeItem === 'chat'
                            ? 'text-blue-600'
                            : 'text-gray-400 group-hover:text-gray-600'
                    ]" />
                    Reviews
                    </Link>

                    <Link href="#" @click="setActiveItem('chat')" :class="[
                        'group flex items-center px-3 py-2 text-md font-medium rounded-lg transition-all duration-200',
                        activeItem === 'chat'
                            ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]">
                    <Users :class="[
                        'mr-3 h-4 w-4 transition-colors duration-200',
                        activeItem === 'chat'
                            ? 'text-blue-600'
                            : 'text-gray-400 group-hover:text-gray-600'
                    ]" />
                    Payments
                    </Link>

                    <Link href="#" @click="setActiveItem('chat')" :class="[
                        'group flex items-center px-3 py-2 text-md font-medium rounded-lg transition-all duration-200',
                        activeItem === 'chat'
                            ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]">
                    <Users :class="[
                        'mr-3 h-4 w-4 transition-colors duration-200',
                        activeItem === 'chat'
                            ? 'text-blue-600'
                            : 'text-gray-400 group-hover:text-gray-600'
                    ]" />
                    Reports
                    </Link>

                    <Link href="#" @click="setActiveItem('chat')" :class="[
                        'group flex items-center px-3 py-2 text-md font-medium rounded-lg transition-all duration-200',
                        activeItem === 'chat'
                            ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    ]">
                    <Users :class="[
                        'mr-3 h-4 w-4 transition-colors duration-200',
                        activeItem === 'chat'
                            ? 'text-blue-600'
                            : 'text-gray-400 group-hover:text-gray-600'
                    ]" />
                    Settings
                    </Link>


                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar for better aesthetics */
.sidebar-scroll::-webkit-scrollbar {
    width: 4px;
}

.sidebar-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar-scroll::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.3);
    border-radius: 2px;
}

.sidebar-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.5);
}
</style>
