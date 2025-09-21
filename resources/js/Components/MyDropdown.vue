<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3';


const page = usePage()

const isDropdownShowing = ref(false)
const dropdownButton = ref(null)

const toggleDropdown = () => {
    isDropdownShowing.value = !isDropdownShowing.value
}

const closeDropdown = () => {
    isDropdownShowing.value = false
}

const handleBlur = (event) => {
    // Close dropdown when clicking outside
    setTimeout(() => {
        if (!event.relatedTarget || !event.relatedTarget.closest('.absolute')) {
            closeDropdown()
        }
    }, 150)
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        closeDropdown()
    }
}

const getInitials = (name) => {
    return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .substring(0, 2)
        .toUpperCase()
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <!-- Profile Dropdown -->
    <div class="relative ">
        <button class="flex items-center space-x-2 p-1.5 rounded-lg transition-colors duration-150  focus:outline-none "
            @click="toggleDropdown" @blur="handleBlur" ref="dropdownButton">
            <!-- Avatar -->
            <div
                class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center text-white text-sm font-medium overflow-hidden">
                <template v-if="page.props.auth.avatar">
                    <img :src="page.props.auth.avatar" alt="User Avatar" class="w-full h-full object-cover" />
                </template>
                <template v-else>
                    {{ getInitials(page.props.auth.user.name) }}
                </template>
            </div>


            <!-- Chevron icon -->
            <!-- <svg class="w-4 h-4 text-gray-500 transition-transform duration-150"
                :class="{ 'rotate-180': isDropdownShowing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7" />
            </svg> -->
        </button>

        <!-- Dropdown Menu -->
        <Transition enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <div v-if="isDropdownShowing"
                class="absolute right-0 mt-1 w-56 bg-white rounded-lg shadow-lg ring-1 ring-gray-900/5 py-1 z-50"
                @click="closeDropdown">
                <!-- User info header -->
                <div class="px-3 py-2 border-b border-gray-100">
                    <div class="text-sm font-medium text-gray-900">
                        {{ page.props.auth.user.name }}
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">
                        {{ page.props.auth.user.email }}
                    </div>
                </div>

                <!-- Menu items -->
                <div class="py-1">
                    <Link :href="route('profile.edit')"
                        class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile
                    </Link>

                    <Link :href="route('settings')"
                        class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150"
                        v-if="route().has('settings')">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                    </Link>

                    <hr class="border-gray-100 my-1">

                    <Link :href="route('logout')" method="post" as="button"
                        class="flex items-center w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Sign out
                    </Link>
                </div>
            </div>
        </Transition>
    </div>
</template>



<style></style>
