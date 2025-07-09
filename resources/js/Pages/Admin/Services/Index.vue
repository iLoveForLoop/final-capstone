<script setup>
import TestLayout from '@/Layouts/TestLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    services: {
        type: Array,
        default: () => [
            {
                id: 1,
                name: 'Prenup Shoot',
                description: '2-hour outdoor prenup photoshoot.',
                price: 10000,
                category: 'Photography',
                vendor: 'LuxeLens Studio',
                created_at: '2025-07-01'
            },
            {
                id: 2,
                name: 'Buffet Package A',
                description: 'Buffet good for 100 pax with drinks.',
                price: 25000,
                category: 'Catering',
                vendor: 'Foodies Events',
                created_at: '2025-07-02'
            }
        ]
    },
    categories: {
        type: Array,
        default: () => ['All', 'Photography', 'Catering', 'Sound System']
    },
    vendors: {
        type: Array,
        default: () => ['All', 'LuxeLens Studio', 'Foodies Events']
    }
});

const filters = ref({
    category: 'All',
    vendor: 'All',
    search: ''
});

const filteredServices = computed(() => {
    return props.services.filter(service => {
        const matchesCategory = filters.value.category === 'All' || service.category === filters.value.category;
        const matchesVendor = filters.value.vendor === 'All' || service.vendor === filters.value.vendor;
        const matchesSearch = service.name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
            service.vendor.toLowerCase().includes(filters.value.search.toLowerCase());

        return matchesCategory && matchesVendor && matchesSearch;
    });
});
</script>

<template>
    <TestLayout>
        <div class="p-6 space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-2xl font-bold text-gray-800">Manage Services</h1>
                <button class="bg-primary text-white px-4 py-2 rounded-xl shadow hover:bg-indigo-600 transition">
                    + Add Service
                </button>
            </div>

            <!-- Filters -->
            <div class="flex flex-col md:flex-row gap-4 md:items-center">
                <select v-model="filters.category" class="border rounded-xl px-3 py-2 text-sm">
                    <option v-for="cat in props.categories" :key="cat">{{ cat }}</option>
                </select>
                <select v-model="filters.vendor" class="border rounded-xl px-3 py-2 text-sm">
                    <option v-for="v in props.vendors" :key="v">{{ v }}</option>
                </select>
                <input v-model="filters.search" type="text" placeholder="Search services..."
                    class="border px-4 py-2 rounded-xl text-sm w-full md:w-64" />
            </div>

            <!-- Services Table -->
            <div class="overflow-x-auto bg-white rounded-2xl shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created At
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="service in filteredServices" :key="service.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ service.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ service.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ service.category }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ service.vendor }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">₱{{
                                service.price.toLocaleString() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(service.created_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm flex flex-wrap gap-2">
                                <button class="text-blue-600 hover:underline">Edit</button>
                                <button class="text-red-600 hover:underline">Delete</button>
                                <button class="text-orange-600 hover:underline">Suspend</button>
                            </td>
                        </tr>
                        <tr v-if="filteredServices.length === 0">
                            <td colspan="7" class="text-center text-gray-400 py-6">No services found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </TestLayout>
</template>

<style scoped></style>
