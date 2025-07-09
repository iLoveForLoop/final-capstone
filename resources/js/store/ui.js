import { defineStore } from 'pinia';

export const useUIStore = defineStore('ui', {
    state: () => ({
        sidebarCollapsed: false,
    }),

    actions: {
        toggleSidebar() {
            console.log('clicked');
            this.sidebarCollapsed = !this.sidebarCollapsed;
        },
    },

    persist: true,
});
