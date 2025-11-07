import axios from 'axios';
import { defineStore } from 'pinia';

export const usePendingVendorStore = defineStore('pendingVendor', {
    state: () => ({
        pendingCount: 0,
        initialized: false,
        isLoading: false,
        pollIntervalId: null,
    }),

    actions: {
        // 🔹 Fetch pending vendors count once
        async fetchPendingCount() {
            try {
                this.isLoading = true;
                const { data } = await axios.get(
                    '/admin/unread-pending-counts',
                );
                this.pendingCount = data.pending_vendors ?? 0;
                this.initialized = true;
            } catch (error) {
                console.error('Failed to fetch pending vendor count:', error);
            } finally {
                this.isLoading = false;
            }
        },

        // 🔹 Start polling at a given interval (default: every 15s)
        startPolling(intervalMs = 15000) {
            // Prevent multiple intervals
            if (this.pollIntervalId) return;

            this.fetchPendingCount(); // Fetch immediately on start
            this.pollIntervalId = setInterval(() => {
                this.fetchPendingCount();
            }, intervalMs);
        },

        // 🔹 Stop polling (e.g. on component unmount)
        stopPolling() {
            if (this.pollIntervalId) {
                clearInterval(this.pollIntervalId);
                this.pollIntervalId = null;
            }
        },
    },
});
