<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';




import VendorSidebar from '@/Components/Vendor/VendorSidebar.vue';
import MyDropdown from '@/Components/MyDropdown.vue';
import VendorTopbar from '@/Components/Vendor/VendorTopbar.vue';


const showingNavigationDropdown = ref(false);
const showingSidebar = ref(false)

const lgQuery = window.matchMedia('(min-width: 1024px)')

function handleScreenChange(e) {
    showingSidebar.value = e.matches
}

onMounted(() => {
    handleScreenChange(lgQuery)
    lgQuery.addEventListener('change', handleScreenChange)
})

onBeforeUnmount(() => {
    lgQuery.removeEventListener('change', handleScreenChange)
})

</script>

<template>

    <div class="h-screen  flex flex-col">


        <!-- Page Heading -->
        <header class="bg-white shadow dark:bg-gray-800" v-if="$slots.header">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="h-full overflow-hidden flex relative">
            <!-- Sidebar with fixed positioning -->
            <VendorSidebar />
            <!-- Main content with animated margin -->
            <div class="flex-1 transition-all duration-300 ease-out overflow-scroll"
                style="scrollbar-width: none; -ms-overflow-style: none;"
                :class="{ '': showingSidebar, 'ml-0': !showingSidebar }">
                <!-- Topbar -->
                <VendorTopbar />
                <slot />
            </div>
        </main>
    </div>
</template>
