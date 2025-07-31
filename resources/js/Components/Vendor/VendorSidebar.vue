<script setup>
import {
    ChevronDown,
    BarChart3,
    ShieldUser,
    Table2,
    Briefcase,
    Users,
    Calendar,
    Star,
    CreditCard,
    Settings,
    ArrowLeftFromLine
} from 'lucide-vue-next';
import { useUIStore } from '@/store/ui';
import NewNavLink from '../NewNavLink.vue';
import LogoPlaceholder from '../LogoPlaceholder.vue';
import { ref } from 'vue';

const ui = ref(useUIStore())
</script>

<template>
    <div class="hidden lg:flex md:flex-shrink-0 max-h-screen bg-slate-700">
        <div :class="[ui.sidebarCollapsed ? 'w-20' : 'w-64', 'flex flex-col transition-all duration-300']">
            <!-- Logo/Brand -->
            <div :class="['flex items-center  h-16 px-5', ui.sidebarCollapsed ? 'justify-center' : '']">
                <div class="w-full flex items-center"
                    :class="ui.sidebarCollapsed ? 'justify-center' : 'justify-between'">
                    <div v-if="!ui.sidebarCollapsed" class="flex gap-2 items-center">
                        <LogoPlaceholder />
                        <span class="text-xl font-bold text-gray-50">Eventory</span>
                    </div>

                    <div class="flex items-center ">
                        <button class="text-gray-400 rounded-full  " @click="ui.toggleSidebar">
                            <ArrowLeftFromLine
                                :class="[ui.sidebarCollapsed ? 'rotate-180' : '', 'h-5 w-5 transition-all duration-700']" />
                        </button>
                    </div>

                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 flex flex-col overflow-y-auto no-scrollbar">
                <nav class="flex-1 px-2 py-4 space-y-3">
                    <NewNavLink :href="route('vendor.index')" :active="route().current('vendor.index')"
                        :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <BarChart3 class="h-5 w-5" />
                        </template>


                        <span class="text-gray-400" v-if="!ui.sidebarCollapsed">
                            Dashboard</span>
                    </NewNavLink>

                    <NewNavLink :href="route('vendor.services.index')"
                        :active="route().current('vendor.services.index')" :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <Briefcase class="h-5 w-5" />
                        </template>


                        <span class="text-gray-400 truncate" v-if="!ui.sidebarCollapsed">
                            My Services</span>
                    </NewNavLink>

                    <!-- <NewNavLink v-if="$page.props.auth?.hasCatering" :href="route('vendor.dishes.index')"
                        :active="route().current('vendor.dishes.index')" :isCollapsed="ui.sidebarCollapsed">

                        <template #icon>
                            <Briefcase class="h-5 w-5" />
                        </template>


                        <span class="text-gray-400 truncate" v-if="!ui.sidebarCollapsed">
                            Dishes</span>
                    </NewNavLink> -->

                    <NewNavLink :href="route('admin.vendor-application.index')"
                        :active="route().current('admin.vendor-application.index')" :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <ShieldUser class="h-5 w-5" />
                        </template>


                        <span class="text-gray-400 truncate" v-if="!ui.sidebarCollapsed">
                            Bookings</span>
                    </NewNavLink>

                    <NewNavLink :href="route('admin.categories.index')"
                        :active="route().current('admin.categories.index')" :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <Table2 class="h-5 w-5" />
                        </template>


                        <span class="text-gray-400" v-if="!ui.sidebarCollapsed">
                            Reviews</span>
                    </NewNavLink>



                    <NewNavLink :href="route('dashboard')" :active="route().current('dashboard')"
                        :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <Calendar class="h-5 w-5" />
                        </template>


                        <span class="text-gray-400" v-if="!ui.sidebarCollapsed">
                            Messages</span>
                    </NewNavLink>

                    <NewNavLink :href="route('dashboard')" :active="route().current('dashboard')"
                        :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <Star class="h-5 w-5" />
                        </template>


                        <span class="text-gray-400" v-if="!ui.sidebarCollapsed">
                            Calendar</span>
                    </NewNavLink>

                    <NewNavLink :href="route('dashboard')" :active="route().current('dashboard')"
                        :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <CreditCard class="h-5 w-5" />
                        </template>


                        <span class="text-gray-400 truncate" v-if="!ui.sidebarCollapsed">
                            Profile Settings</span>
                    </NewNavLink>

                    <NewNavLink :href="route('dashboard')" :active="route().current('dashboard')"
                        :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <Settings class="h-5 w-5" />
                        </template>


                        <span class="text-gray-400" v-if="!ui.sidebarCollapsed">
                            Account
                        </span>
                    </NewNavLink>
                </nav>


                <!-- <div class="px-4 py-4  ">
                    <div class="flex justify-center items-center ">

                        <div class="flex items-center ">
                            <button class="text-gray-400 rounded-full hover:bg-gray-50 p-2 " @click="ui.toggleSidebar">
                                <ArrowLeftFromLine
                                    :class="[ui.sidebarCollapsed ? 'rotate-180' : '', 'h-5 w-5 transition-all duration-700']" />
                            </button>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</template>


<style></style>
