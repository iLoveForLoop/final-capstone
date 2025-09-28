<script setup>
import TestLayout from '@/Layouts/TestLayout.vue';
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';

// Sample data
const reports = ref([
    {
        id: 1,
        vendor: {
            name: "Golden Gate Catering",
            email: "contact@goldencatering.com",
            category: "Catering",
            location: "Quezon City",
            image_url: null
        },
        reporter: {
            name: "Maria Santos",
            email: "maria@email.com"
        },
        reason: "Not responding",
        description: "Vendor hasn't responded to messages for 2 weeks. Multiple customers complained about lack of communication.",
        status: "pending",
        priority: "high",
        created_at: "2025-09-25T10:30:00Z"
    },
    {
        id: 2,
        vendor: {
            name: "Lens Photography",
            email: "info@lens.ph",
            category: "Photography",
            location: "Makati",
            image_url: null
        },
        reporter: {
            name: "John Cruz",
            email: "john@email.com"
        },
        reason: "No show",
        description: "Didn't show up on event day without any prior notice",
        status: "investigating",
        priority: "critical",
        created_at: "2025-09-24T14:15:00Z"
    },
    {
        id: 3,
        vendor: {
            name: "Sound Pro Audio",
            email: "info@soundpro.ph",
            category: "Sound Systems",
            location: "Manila",
            image_url: null
        },
        reporter: {
            name: "Carlos Rodriguez",
            email: "carlos@email.com"
        },
        reason: "Poor service",
        description: "Equipment was faulty during the event",
        status: "resolved",
        priority: "medium",
        created_at: "2025-09-22T11:45:00Z"
    }
]);

const selectedStatus = ref('all');
const selectedPriority = ref('all');
const searchQuery = ref('');
const selectedReport = ref(null);
const showModal = ref(false);

const statuses = [
    { value: 'all', label: 'All Statuses' },
    { value: 'pending', label: 'Pending' },
    { value: 'investigating', label: 'Investigating' },
    { value: 'resolved', label: 'Resolved' },
    { value: 'dismissed', label: 'Dismissed' }
];

const priorities = [
    { value: 'all', label: 'All Priorities' },
    { value: 'low', label: 'Low' },
    { value: 'medium', label: 'Medium' },
    { value: 'high', label: 'High' },
    { value: 'critical', label: 'Critical' }
];

const filteredReports = computed(() => {
    let filtered = reports.value;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(report =>
            report.vendor.name.toLowerCase().includes(query) ||
            report.reporter.name.toLowerCase().includes(query) ||
            report.reason.toLowerCase().includes(query)
        );
    }

    if (selectedStatus.value !== 'all') {
        filtered = filtered.filter(r => r.status === selectedStatus.value);
    }

    if (selectedPriority.value !== 'all') {
        filtered = filtered.filter(r => r.priority === selectedPriority.value);
    }

    return filtered;
});

const viewReport = (report) => {
    selectedReport.value = report;
    showModal.value = true;
};

const updateStatus = (id, status) => {
    const report = reports.value.find(r => r.id === id);
    if (report) {
        report.status = status;
    }
    showModal.value = false;
};

const resetFilters = () => {
    selectedStatus.value = 'all';
    selectedPriority.value = 'all';
    searchQuery.value = '';
};

const applyFilters = () => {
    // Filters are applied automatically via computed
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800',
        investigating: 'bg-blue-100 text-blue-800',
        resolved: 'bg-green-100 text-green-800',
        dismissed: 'bg-gray-100 text-gray-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getPriorityColor = (priority) => {
    const colors = {
        low: 'bg-green-100 text-green-800',
        medium: 'bg-yellow-100 text-yellow-800',
        high: 'bg-orange-100 text-orange-800',
        critical: 'bg-red-100 text-red-800'
    };
    return colors[priority] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>

    <Head title="Reports - Admin" />

    <TestLayout>
        <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Vendor Reports</h1>
                    <p class="text-gray-500 text-sm mt-1">Monitor and manage customer reports about vendors</p>
                </div>
                <button
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Export Reports
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select v-model="selectedStatus" @change="applyFilters"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option v-for="status in statuses" :key="status.value" :value="status.value">{{ status.label
                                }}</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Priority</label>
                        <select v-model="selectedPriority" @change="applyFilters"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option v-for="priority in priorities" :key="priority.value" :value="priority.value">{{
                                priority.label }}</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <div class="relative">
                            <input v-model="searchQuery" @input="applyFilters" type="text"
                                placeholder="Search reports..."
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

            <!-- Reports Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vendor</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reporter</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Issue</th>
                                <!-- <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Priority</th> -->
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="report in filteredReports" :key="report.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">

                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ report.vendor.name }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ report.vendor.category }} • {{
                                                report.vendor.location }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ report.reporter.name }}</div>
                                    <div class="text-xs text-gray-500">{{ report.reporter.email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ report.reason }}</div>
                                    <div class="text-xs text-gray-500 max-w-xs truncate">{{ report.description }}</div>
                                </td>
                                <!-- <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getPriorityColor(report.priority)"
                                        class="px-2 py-1 text-xs font-medium rounded-full capitalize">
                                        {{ report.priority }}
                                    </span>
                                </td> -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusColor(report.status)"
                                        class="px-2 py-1 text-xs font-medium rounded-full capitalize">
                                        {{ report.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(report.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-3">
                                        <button @click="viewReport(report)"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd"
                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button @click="updateStatus(report.id, 'investigating')"
                                            v-if="report.status === 'pending'"
                                            class="text-blue-600 hover:text-blue-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button @click="updateStatus(report.id, 'resolved')"
                                            v-if="report.status === 'investigating'"
                                            class="text-green-600 hover:text-green-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button @click="updateStatus(report.id, 'dismissed')"
                                            class="text-red-600 hover:text-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredReports.length === 0">
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No reports found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            @click.self="showModal = false">
            <div class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Report Details</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div v-if="selectedReport" class="space-y-4">
                    <!-- Vendor Info -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">REPORTED VENDOR</h4>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                    <template v-if="selectedReport.vendor.image_url">
                                        <img :src="selectedReport.vendor.image_url" :alt="selectedReport.vendor.name"
                                            class="h-full w-full object-cover">
                                    </template>
                                    <template v-else>
                                        <div
                                            class="h-full w-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-medium text-sm">
                                            {{ selectedReport.vendor.name.charAt(0).toUpperCase() }}
                                        </div>
                                    </template>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">{{ selectedReport.vendor.name }}</div>
                                    <div class="text-sm text-gray-600">{{ selectedReport.vendor.email }}</div>
                                    <div class="text-xs text-gray-500">{{ selectedReport.vendor.category }} • {{
                                        selectedReport.vendor.location }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reporter Info -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">REPORTED BY</h4>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="font-medium text-gray-900">{{ selectedReport.reporter.name }}</div>
                            <div class="text-sm text-gray-600">{{ selectedReport.reporter.email }}</div>
                        </div>
                    </div>

                    <!-- Report Details -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-2">REPORT DETAILS</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Reason:</span>
                                <span class="text-sm text-gray-900">{{ selectedReport.reason }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Priority:</span>
                                <span :class="getPriorityColor(selectedReport.priority)"
                                    class="px-2 py-1 text-xs font-medium rounded-full capitalize">
                                    {{ selectedReport.priority }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Status:</span>
                                <span :class="getStatusColor(selectedReport.status)"
                                    class="px-2 py-1 text-xs font-medium rounded-full capitalize">
                                    {{ selectedReport.status }}
                                </span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Description:</span>
                                <p class="text-sm text-gray-900 mt-1">{{ selectedReport.description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button @click="showModal = false"
                            class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Close
                        </button>
                        <button v-if="selectedReport.status === 'pending'"
                            @click="updateStatus(selectedReport.id, 'investigating')"
                            class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                            Start Investigation
                        </button>
                        <button v-if="selectedReport.status === 'investigating'"
                            @click="updateStatus(selectedReport.id, 'resolved')"
                            class="px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700 transition">
                            Mark Resolved
                        </button>
                        <button @click="updateStatus(selectedReport.id, 'dismissed')"
                            class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                            Dismiss Report
                        </button>
                    </div>
                </div>
            </div>
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
