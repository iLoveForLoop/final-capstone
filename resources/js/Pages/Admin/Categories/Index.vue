<script setup>
import TestLayout from '@/Layouts/TestLayout.vue';
import { ref } from 'vue';
import { Search, Plus, Pencil, Trash2 } from 'lucide-vue-next';
import CategoryCreateModal from '@/Components/Admin/CategoryCreateModal.vue';
import CategoryEditModal from '@/Components/Admin/CategoryEditModal.vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    categories: {
        type: Object,
        default: () => ({
            data: [
                { id: 1, name: 'Catering', created_at: '2025-07-01' },
                { id: 2, name: 'Photography', created_at: '2025-07-02' }
            ],
            links: []
        })
    }
});

const categoryCreateModal = ref(null);
const categoryEditModal = ref(null)

const search = ref('');


const deleteCategory = (categoryId) => {

    if (confirm('Are you sure do you want to delete this category?')) {
        router.delete(route('admin.categories.destroy', categoryId), {
            onSuccess: () => {
                toast.success('User deleted successfully');
            },
            onError: () => {
                toast.error('Failed to delete user');
            }
        })
    }

}
</script>

<template>
    <TestLayout>
        <div class="p-6 space-y-6 max-w-7xl mx-auto">
            <!-- Header and Action Row -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage your service categories</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <!-- <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <Search class="h-5 w-5 text-gray-400" />
                        </div>
                        <input v-model="search" type="text" placeholder="Search categories..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-64 text-sm" />
                    </div> -->
                    <button @click="categoryCreateModal.show()"
                        class="flex items-center justify-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                        <Plus class="h-5 w-5" />
                        <span>Add Category</span>
                    </button>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="category in categories.data" :key="category.id"
                                class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ category.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ new Date(category.created_at).toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'short', day: 'numeric'
                                    }) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        <button @click="categoryEditModal.show(category)"
                                            class="text-indigo-600 hover:text-indigo-900 flex items-center gap-1">
                                            <Pencil class="h-4 w-4" />
                                            <span>Edit</span>
                                        </button>
                                        <button @click="deleteCategory(category.id)"
                                            class="text-red-600 hover:text-red-900 flex items-center gap-1">
                                            <Trash2 class="h-4 w-4" />
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="categories.data.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center">
                                    <div class="text-gray-400 flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-2 text-sm font-medium">No categories found</p>
                                        <p class="text-xs mt-1">Add your first category to get started</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create Category Modal -->
            <CategoryCreateModal ref="categoryCreateModal" />

            <!-- Edit Category Modal -->
            <CategoryEditModal ref="categoryEditModal" />

            <!-- Pagination would go here -->
        </div>
    </TestLayout>
</template>

<style scoped>
/* Smooth transitions for interactive elements */
button,
input,
[type='button'],
[type='reset'],
[type='submit'] {
    transition: all 0.15s ease;
}

/* Custom scrollbar for the table */
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
