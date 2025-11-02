<script setup>
const activeTab = defineModel('activeTab')
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm mb-8">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6">
                <button v-for="tab in [
                    { id: 'basic', label: 'Basic Info' },
                    { id: 'services', label: 'Services & Specialties' },
                    { id: 'portfolio', label: 'Portfolio' },
                    { id: 'media', label: 'Media' },
                    { id: 'permits', label: 'Permits' },
                ]" :key="tab.id" @click="activeTab = tab.id"
                    :class="activeTab === tab.id ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    {{ tab.label }}
                </button>
            </nav>
        </div>
    </div>
</template>

<style></style>
