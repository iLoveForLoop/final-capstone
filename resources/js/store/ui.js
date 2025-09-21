import { defineStore } from 'pinia';

export const useUIStore = defineStore('ui', {
    state: () => ({
        sidebarCollapsed: false,
        isEventCalendarView: false,
        isVendorNotificationOpen: false,
    }),

    actions: {
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
        },
        toggleEventCalendarView() {
            this.isEventCalendarView = !this.isEventCalendarView;
        },
        toggleVendorNotificationOpen() {
            console.log('event');
            this.isVendorNotificationOpen = !this.isVendorNotificationOpen;
        },
    },

    persist: true,
});
