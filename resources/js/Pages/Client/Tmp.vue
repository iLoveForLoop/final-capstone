<script setup>
import ClientNavbar from '@/Components/ClientNavbar.vue';
import { ref, computed } from 'vue';

// Mock data
const mockCategories = [
    { id: 1, name: 'Photography' },
    { id: 2, name: 'Catering' },
    { id: 3, name: 'Entertainment' },
    { id: 4, name: 'Decoration' },
    { id: 5, name: 'Transportation' }
];

const mockFavorites = ref({
    data: [
        {
            id: 1,
            title: 'Professional Wedding Photography',
            description: 'Capture your special moments with our professional wedding photography service. High-quality photos and video coverage.',
            price: 15000,
            category: { id: 1, name: 'Photography' },
            provider: { name: 'John\'s Photography Studio', rating: 4.8 },
            image: 'https://images.unsplash.com/photo-1606216794074-735e91aa2c92?w=400',
            availability: 'available',
            location: 'Metro Manila',
            dateAdded: '2024-01-15'
        },
        {
            id: 2,
            title: 'Premium Catering Service',
            description: 'Delicious Filipino and international cuisine for your events. Professional service and presentation.',
            price: 800,
            category: { id: 2, name: 'Catering' },
            provider: { name: 'Taste of Manila', rating: 4.9 },
            image: 'https://images.unsplash.com/photo-1555244162-803834f70033?w=400',
            availability: 'available',
            location: 'Quezon City',
            dateAdded: '2024-01-12'
        },
        {
            id: 3,
            title: 'Live Band Entertainment',
            description: 'Professional live band for weddings, corporate events, and parties. Versatile repertoire and engaging performance.',
            price: 25000,
            category: { id: 3, name: 'Entertainment' },
            provider: { name: 'Manila Music Collective', rating: 4.7 },
            image: 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400',
            availability: 'unavailable',
            location: 'Makati City',
            dateAdded: '2024-01-10'
        },
        {
            id: 4,
            title: 'Elegant Event Decoration',
            description: 'Transform your venue with our elegant decoration services. Custom themes and professional setup.',
            price: 12000,
            category: { id: 4, name: 'Decoration' },
            provider: { name: 'Dream Decorators', rating: 4.6 },
            image: 'https://images.unsplash.com/photo-1519167758481-83f29da78d23?w=400',
            availability: 'available',
            location: 'Pasig City',
            dateAdded: '2024-01-08'
        },
        {
            id: 5,
            title: 'Luxury Wedding Car Rental',
            description: 'Premium wedding car rental service with professional chauffeur. Make your special day even more memorable.',
            price: 8000,
            category: { id: 5, name: 'Transportation' },
            provider: { name: 'Elite Car Rentals', rating: 4.5 },
            image: 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=400',
            availability: 'available',
            location: 'BGC, Taguig',
            dateAdded: '2024-01-05'
        }
    ],
    total: 5
});

// Reactive filters
const searchQuery = ref('');
const selectedCategory = ref('all');
const selectedAvailability = ref('all');
const viewMode = ref('grid'); // grid or list

// Filter favorites
const filteredFavorites = computed(() => {
    let filtered = mockFavorites.value.data;

    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(service =>
            service.title.toLowerCase().includes(query) ||
            service.description.toLowerCase().includes(query) ||
            service.provider.name.toLowerCase().includes(query)
        );
    }

    // Category filter
    if (selectedCategory.value !== 'all') {
        filtered = filtered.filter(service => service.category.id == selectedCategory.value);
    }

    // Availability filter
    if (selectedAvailability.value !== 'all') {
        filtered = filtered.filter(service => service.availability === selectedAvailability.value);
    }

    return {
        data: filtered,
        total: filtered.length
    };
});

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = 'all';
    selectedAvailability.value = 'all';
};

// Remove from favorites
const removeFromFavorites = (serviceId) => {
    if (confirm('Remove this service from your favorites?')) {
        mockFavorites.value.data = mockFavorites.value.data.filter(service => service.id !== serviceId);
        mockFavorites.value.total = mockFavorites.value.data.length;
    }
};

// Clear all favorites
const clearAllFavorites = () => {
    if (confirm('Are you sure you want to remove all services from your favorites?')) {
        mockFavorites.value.data = [];
        mockFavorites.value.total = 0;
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};
</script>

<template>

    <!-- Grid View -->
    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="service in filteredFavorites.data" :key="service.id"
            class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="relative">
                <img :src="service.image_url" :alt="service.title" class="w-full h-48 object-cover">
                <div class="absolute top-3 right-3 flex space-x-2">
                    <!-- <span v-if="service.availability === 'available'"
                        class="bg-green-500 text-white px-2 py-1 text-xs rounded">
                        Available
                    </span> -->
                    <!-- <span v-else class="bg-red-500 text-white px-2 py-1 text-xs rounded">
                        Unavailable
                    </span> -->
                    <button class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                        {{ service.category_name }}
                    </span>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ service.dateAdded }}
                    </div>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-1">{{ service.title }}</h3>
                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ service.description }}</p>
                <div class="flex items-center justify-between mb-3">
                    <div class="text-lg font-bold text-green-600">{{ formatPrice(service.price) }}</div>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        {{ service.provider.rating }}
                    </div>
                </div>
                <div class="text-sm text-gray-600 mb-3">
                    <span class="font-medium">{{ service.business_name }}</span> • {{ service.location }}
                </div>
                <div class="flex space-x-2">
                    <button
                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded text-sm hover:bg-blue-700 transition-colors">
                        Book Now
                    </button>
                    <button
                        class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded text-sm hover:bg-gray-50 transition-colors">
                        View Details
                    </button>
                </div>
            </div>
        </div>
    </div>


</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
