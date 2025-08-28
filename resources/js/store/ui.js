import { defineStore } from 'pinia';

export const useUIStore = defineStore('ui', {
    state: () => ({
        sidebarCollapsed: false,
        isEventCalendarView: false,
    }),

    actions: {
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
        },
        toggleEventCalendarView() {
            console.log('event');
            this.isEventCalendarView = !this.isEventCalendarView;
        },
    },

    persist: true,
});
