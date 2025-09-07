<script setup>
import { ref, computed } from 'vue'
import ClientNavbar from '@/Components/ClientNavbar.vue'
import { Search, Filter, X, Star, MapPin, ChevronLeft, ChevronRight, Check } from 'lucide-vue-next'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/Components/ui/dialog'

// Reactive state
const searchQuery = ref('')
const sortBy = ref('relevance')
const selectedCategories = ref([])
const selectedPriceRange = ref('')
const selectedRating = ref('')
const showFiltersDialog = ref(false)
const currentPage = ref(1)
const itemsPerPage = ref(8)

// Static data for demo
const categories = ref([
    { id: 'photography', name: 'Photography' },
    { id: 'catering', name: 'Catering' },
    { id: 'music', name: 'Music & DJ' },
    { id: 'decoration', name: 'Decoration' },
    { id: 'venue', name: 'Venues' },
    { id: 'planning', name: 'Event Planning' },
    { id: 'floral', name: 'Floral Design' },
    { id: 'transportation', name: 'Transportation' }
])

const priceRanges = ref([
    { id: 'budget', label: '$0 - $500' },
    { id: 'mid', label: '$500 - $2,000' },
    { id: 'premium', label: '$2,000 - $5,000' },
    { id: 'luxury', label: '$5,000+' }
])

const ratingFilters = ref([
    { value: 4, label: '4+ Stars' },
    { value: 3, label: '3+ Stars' },
    { value: 2, label: '2+ Stars' }
])

const vendors = ref([
    {
        id: 1,
        name: 'Elite Wedding Photography',
        rating: 5,
        reviewCount: 127,
        description: 'Professional wedding and event photography with over 10 years of experience. We capture your special moments with artistic flair and attention to detail that will make your memories last a lifetime.',
        services: ['Wedding Photography', 'Event Photography', 'Portrait Sessions', 'Photo Editing', 'Drone Photography'],
        location: 'New York, NY',
        responseTime: '2 hours',
        startingPrice: '$1,200',
        image: 'https://images.unsplash.com/photo-1606216794074-735e91aa2c92?w=400&h=250&fit=crop&auto=format',
        category: 'photography',
        priceRange: 'premium',
        priceValue: 1200
    },
    {
        id: 2,
        name: 'Gourmet Catering Co.',
        rating: 5,
        reviewCount: 89,
        description: 'Full-service catering company specializing in corporate events, weddings, and private parties. Fresh, locally-sourced ingredients and customizable menus to fit any dietary requirement.',
        services: ['Wedding Catering', 'Corporate Events', 'Private Parties', 'Menu Planning', 'Bartending'],
        location: 'Los Angeles, CA',
        responseTime: '1 hour',
        startingPrice: '$45/person',
        image: 'https://images.unsplash.com/photo-1555244162-803834f70033?w=400&h=250&fit=crop&auto=format',
        category: 'catering',
        priceRange: 'mid',
        priceValue: 900
    },
    {
        id: 3,
        name: 'Soundwave DJ Services',
        rating: 5,
        reviewCount: 156,
        description: 'Professional DJ and music services for all types of events. State-of-the-art equipment, extensive music library spanning all genres, and experienced MC services to keep your event flowing perfectly.',
        services: ['DJ Services', 'Sound System Rental', 'Lighting', 'MC Services', 'Karaoke Setup'],
        location: 'Chicago, IL',
        responseTime: '30 minutes',
        startingPrice: '$800',
        image: 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400&h=250&fit=crop&auto=format',
        category: 'music',
        priceRange: 'mid',
        priceValue: 800
    },
    {
        id: 4,
        name: 'Enchanted Events Decor',
        rating: 5,
        reviewCount: 73,
        description: 'Transform your event space with our creative decoration and design services. From elegant wedding setups to corporate event styling, we bring your vision to life.',
        services: ['Wedding Decoration', 'Corporate Events', 'Balloon Arrangements', 'Table Settings', 'Lighting Design'],
        location: 'Miami, FL',
        responseTime: '4 hours',
        startingPrice: '$650',
        image: 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=400&h=250&fit=crop&auto=format',
        category: 'decoration',
        priceRange: 'mid',
        priceValue: 650
    },
    {
        id: 5,
        name: 'Grand Ballroom Venue',
        rating: 4,
        reviewCount: 98,
        description: 'Elegant ballroom venue perfect for weddings, corporate events, and celebrations. Features crystal chandeliers, marble floors, and capacity for up to 300 guests.',
        services: ['Wedding Venue', 'Corporate Events', 'Private Parties', 'Catering Kitchen', 'Parking'],
        location: 'Boston, MA',
        responseTime: '6 hours',
        startingPrice: '$3,500',
        image: 'https://images.unsplash.com/photo-1519167758481-83f29c8bb115?w=400&h=250&fit=crop&auto=format',
        category: 'venue',
        priceRange: 'luxury',
        priceValue: 3500
    },
    {
        id: 6,
        name: 'Perfect Day Planners',
        rating: 5,
        reviewCount: 134,
        description: 'Full-service event planning company with 15 years of experience. We handle every detail from concept to execution, ensuring your special day is flawless and stress-free.',
        services: ['Wedding Planning', 'Corporate Events', 'Birthday Parties', 'Vendor Coordination', 'Timeline Management'],
        location: 'San Francisco, CA',
        responseTime: '3 hours',
        startingPrice: '$2,000',
        image: 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=400&h=250&fit=crop&auto=format',
        category: 'planning',
        priceRange: 'premium',
        priceValue: 2000
    },
    {
        id: 7,
        name: 'Bloom & Blossom Florals',
        rating: 5,
        reviewCount: 67,
        description: 'Award-winning floral design studio creating breathtaking arrangements for weddings and events. We specialize in fresh, seasonal flowers and custom designs.',
        services: ['Wedding Florals', 'Event Arrangements', 'Bridal Bouquets', 'Centerpieces', 'Floral Installations'],
        location: 'Seattle, WA',
        responseTime: '2 hours',
        startingPrice: '$400',
        image: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=250&fit=crop&auto=format',
        category: 'floral',
        priceRange: 'budget',
        priceValue: 400
    },
    {
        id: 8,
        name: 'Luxury Transportation Services',
        rating: 4,
        reviewCount: 112,
        description: 'Premium transportation services for weddings and special events. Fleet includes luxury sedans, limousines, and party buses with professional chauffeurs.',
        services: ['Wedding Transportation', 'Airport Transfers', 'Corporate Events', 'Party Bus', 'Limousine Service'],
        location: 'Las Vegas, NV',
        responseTime: '1 hour',
        startingPrice: '$300',
        image: 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=400&h=250&fit=crop&auto=format',
        category: 'transportation',
        priceRange: 'budget',
        priceValue: 300
    },
    {
        id: 9,
        name: 'Artisan Wedding Photography',
        rating: 4,
        reviewCount: 89,
        description: 'Contemporary wedding photography with a photojournalistic approach. We capture candid moments and genuine emotions that tell your unique love story.',
        services: ['Wedding Photography', 'Engagement Sessions', 'Bridal Portraits', 'Digital Gallery', 'Print Packages'],
        location: 'Austin, TX',
        responseTime: '4 hours',
        startingPrice: '$950',
        image: 'https://images.unsplash.com/photo-1519741497674-611481863552?w=400&h=250&fit=crop&auto=format',
        category: 'photography',
        priceRange: 'mid',
        priceValue: 950
    },
    {
        id: 10,
        name: 'Signature Catering & Events',
        rating: 4,
        reviewCount: 156,
        description: 'Innovative catering company offering farm-to-table cuisine and exceptional service. We create memorable dining experiences for weddings and corporate events.',
        services: ['Wedding Catering', 'Corporate Catering', 'Cocktail Receptions', 'Plated Dinners', 'Buffet Service'],
        location: 'Portland, OR',
        responseTime: '2 hours',
        startingPrice: '$55/person',
        image: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400&h=250&fit=crop&auto=format',
        category: 'catering',
        priceRange: 'premium',
        priceValue: 1100
    },
    {
        id: 11,
        name: 'Beat Masters DJ Collective',
        rating: 4,
        reviewCount: 201,
        description: 'Professional DJ collective specializing in weddings, corporate events, and parties. Multiple DJs available with expertise in various music genres and crowd engagement.',
        services: ['Wedding DJ', 'Corporate Events', 'Dance Parties', 'Sound Equipment', 'Photo Booth'],
        location: 'Denver, CO',
        responseTime: '1 hour',
        startingPrice: '$600',
        image: 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=400&h=250&fit=crop&auto=format',
        category: 'music',
        priceRange: 'mid',
        priceValue: 600
    },
    {
        id: 12,
        name: 'Elegant Affairs Decor',
        rating: 3,
        reviewCount: 45,
        description: 'Creative event decoration and styling services. We transform spaces with beautiful linens, lighting, and custom installations for unforgettable celebrations.',
        services: ['Event Styling', 'Linen Rentals', 'Uplighting', 'Drapery', 'Custom Installations'],
        location: 'Phoenix, AZ',
        responseTime: '5 hours',
        startingPrice: '$750',
        image: 'https://images.unsplash.com/photo-1464207687429-7505649dae38?w=400&h=250&fit=crop&auto=format',
        category: 'decoration',
        priceRange: 'mid',
        priceValue: 750
    }
])

// Computed properties
const filteredVendors = computed(() => {
    let filtered = vendors.value.filter(vendor => {
        // Search filter
        const searchLower = searchQuery.value.toLowerCase()
        const matchesSearch = !searchQuery.value ||
            vendor.name.toLowerCase().includes(searchLower) ||
            vendor.description.toLowerCase().includes(searchLower) ||
            vendor.services.some(service => service.toLowerCase().includes(searchLower)) ||
            vendor.location.toLowerCase().includes(searchLower)

        // Category filter
        const matchesCategory = selectedCategories.value.length === 0 ||
            selectedCategories.value.includes(vendor.category)

        // Price range filter
        const matchesPrice = !selectedPriceRange.value ||
            vendor.priceRange === selectedPriceRange.value

        // Rating filter
        const matchesRating = !selectedRating.value ||
            vendor.rating >= selectedRating.value

        return matchesSearch && matchesCategory && matchesPrice && matchesRating
    })

    // Sort results
    switch (sortBy.value) {
        case 'rating':
            filtered.sort((a, b) => b.rating - a.rating)
            break
        case 'reviews':
            filtered.sort((a, b) => b.reviewCount - a.reviewCount)
            break
        case 'price_low':
            filtered.sort((a, b) => a.priceValue - b.priceValue)
            break
        case 'price_high':
            filtered.sort((a, b) => b.priceValue - a.priceValue)
            break
        default:
            // Keep original order for relevance
            break
    }

    return filtered
})

const paginatedVendors = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value
    const end = start + itemsPerPage.value
    return filteredVendors.value.slice(start, end)
})

const totalPages = computed(() => {
    return Math.ceil(filteredVendors.value.length / itemsPerPage.value)
})

const visiblePages = computed(() => {
    const pages = []
    const total = totalPages.value
    const current = currentPage.value
    const delta = 2

    for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
        pages.push(i)
    }

    if (current - delta > 2) {
        pages.unshift('...')
    }
    if (current + delta < total - 1) {
        pages.push('...')
    }

    pages.unshift(1)
    if (total !== 1) {
        pages.push(total)
    }

    return pages.filter((page, index, array) => array.indexOf(page) === index)
})

// Methods
const handleSearch = () => {
    currentPage.value = 1
}

const handleSort = () => {
    currentPage.value = 1
}

const clearFilters = () => {
    selectedCategories.value = []
    selectedPriceRange.value = ''
    selectedRating.value = ''
    searchQuery.value = ''
    sortBy.value = 'relevance'
    currentPage.value = 1
    showFiltersDialog.value = false
}

const viewVendor = (vendorId) => {
    // Navigate to vendor detail page
    console.log('View vendor:', vendorId)
}

const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--
    }
}

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++
    }
}

const toggleCategory = (categoryId) => {
    const index = selectedCategories.value.indexOf(categoryId)
    if (index > -1) {
        selectedCategories.value.splice(index, 1)
    } else {
        selectedCategories.value.push(categoryId)
    }
}

const applyFilters = () => {
    showFiltersDialog.value = false
    currentPage.value = 1
}
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <ClientNavbar />

        <!-- Simplified Header -->
        <div class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <!-- Search Bar - More prominent -->
                    <div class="flex-1 max-w-2xl w-full">
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <Search :size="20" />
                            </div>
                            <input v-model="searchQuery" type="text"
                                class="block w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base"
                                placeholder="Search vendors, services, or locations..." @input="handleSearch" />
                        </div>
                    </div>

                    <!-- Filter and Sort in one row -->
                    <div class="flex items-center gap-3">
                        <!-- Sort Dropdown -->
                        <div class="relative">
                            <select v-model="sortBy"
                                class="pl-3 pr-10 py-2.5 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm appearance-none"
                                @change="handleSort">
                                <option value="relevance">Relevance</option>
                                <option value="rating">Highest Rated</option>
                                <option value="reviews">Most Reviews</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                            </select>
                        </div>

                        <!-- Filter Button -->
                        <button @click="showFiltersDialog = true"
                            class="flex items-center gap-2 px-3 py-2.5 text-sm text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                            <Filter :size="16" />
                            <span class="hidden sm:inline">Filters</span>
                        </button>
                    </div>
                </div>

                <!-- Active Filters Bar -->
                <div v-if="selectedCategories.length > 0 || selectedPriceRange || selectedRating"
                    class="mt-4 flex flex-wrap gap-2">
                    <span v-if="selectedCategories.length > 0" class="text-xs text-gray-500 mr-2">Categories:</span>
                    <button v-for="category in selectedCategories" :key="`active-${category}`"
                        @click="toggleCategory(category)"
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{categories.find(c => c.id === category)?.name}}
                        <X :size="12" class="ml-1" />
                    </button>

                    <span v-if="selectedPriceRange" class="text-xs text-gray-500 mx-2">Price:</span>
                    <button v-if="selectedPriceRange" @click="selectedPriceRange = ''"
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{priceRanges.find(r => r.id === selectedPriceRange)?.label}}
                        <X :size="12" class="ml-1" />
                    </button>

                    <span v-if="selectedRating" class="text-xs text-gray-500 mx-2">Rating:</span>
                    <button v-if="selectedRating" @click="selectedRating = ''"
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        {{ selectedRating }}+ Stars
                        <X :size="12" class="ml-1" />
                    </button>

                    <button @click="clearFilters" class="ml-2 text-xs text-blue-600 font-medium hover:text-blue-800">
                        Clear all
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Main Content - More prominent -->
                <div class="w-full">
                    <!-- Results Header - Simplified -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">{{ filteredVendors.length }} vendors
                                available</h1>
                        </div>
                    </div>

                    <!-- Vendor Cards - Cleaner design -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div v-for="vendor in paginatedVendors" :key="vendor.id"
                            class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 cursor-pointer overflow-hidden group"
                            @click="viewVendor(vendor.id)">
                            <div class="p-5">
                                <div class="flex flex-col">
                                    <!-- Vendor Image and Basic Info -->
                                    <div class="flex gap-4">
                                        <img :src="vendor.image" :alt="vendor.name"
                                            class="w-20 h-20 object-cover rounded-xl flex-shrink-0" />

                                        <div class="min-w-0 flex-1">
                                            <h2
                                                class="text-lg font-semibold text-gray-900 truncate group-hover:text-blue-600 transition-colors">
                                                {{ vendor.name }}
                                            </h2>

                                            <div class="flex items-center mt-1">
                                                <div class="flex items-center">
                                                    <Star v-for="i in 5" :key="i" :size="14"
                                                        :class="i <= vendor.rating ? 'text-yellow-400 fill-current' : 'text-gray-300'" />
                                                </div>
                                                <span class="ml-2 text-sm text-gray-600">({{ vendor.reviewCount
                                                }})</span>
                                            </div>

                                            <div class="flex items-center mt-2 text-sm text-gray-500">
                                                <MapPin :size="14" class="mr-1" />
                                                <span class="truncate">{{ vendor.location }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <p class="mt-4 text-gray-600 text-sm line-clamp-2">{{ vendor.description }}</p>

                                    <!-- Services -->
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        <span v-for="service in vendor.services.slice(0, 3)" :key="service"
                                            class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">
                                            {{ service }}
                                        </span>
                                        <span v-if="vendor.services.length > 3"
                                            class="px-2.5 py-1 text-gray-500 text-xs">
                                            +{{ vendor.services.length - 3 }} more
                                        </span>
                                    </div>

                                    <!-- Footer with Price and CTA -->
                                    <div class="mt-4 flex items-center justify-between">
                                        <div>
                                            <div class="text-lg font-semibold text-gray-900">{{ vendor.startingPrice }}
                                            </div>
                                            <div class="text-xs text-gray-500">starting price</div>
                                        </div>
                                        <button
                                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Results State -->
                    <div v-if="filteredVendors.length === 0" class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <Search :size="32" class="text-gray-400" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No vendors found</h3>
                        <p class="text-gray-500 mb-4">Try adjusting your search or filters</p>
                        <button @click="clearFilters"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Clear all filters
                        </button>
                    </div>

                    <!-- Simplified Pagination -->
                    <div v-if="filteredVendors.length > 0" class="mt-8 flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            Showing {{ Math.min(currentPage * itemsPerPage, filteredVendors.length) }} of {{
                                filteredVendors.length }}
                            results
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="previousPage" :disabled="currentPage === 1"
                                class="p-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <ChevronLeft :size="16" />
                            </button>

                            <template v-for="page in visiblePages" :key="page">
                                <button v-if="page !== '...'" @click="currentPage = page" :class="[
                                    'w-8 h-8 text-sm font-medium rounded-lg transition-colors',
                                    currentPage === page
                                        ? 'bg-blue-600 text-white'
                                        : 'text-gray-700 hover:bg-gray-100'
                                ]">
                                    {{ page }}
                                </button>
                                <span v-else class="px-1 text-gray-500">...</span>
                            </template>

                            <button @click="nextPage" :disabled="currentPage === totalPages"
                                class="p-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <ChevronRight :size="16" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Dialog using shadcn/vue -->
        <Dialog v-model:open="showFiltersDialog">
            <DialogContent class="sm:max-w-3xl w-full max-h-[80vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle class="text-left">Filters</DialogTitle>
                    <DialogDescription class="text-left">
                        Narrow down your search results
                    </DialogDescription>
                </DialogHeader>

                <!-- Grid layout for filters -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-4">
                    <!-- Category Filter -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Service Type</h3>
                        <div class="space-y-2">
                            <button v-for="category in categories" :key="category.id"
                                @click="toggleCategory(category.id)" :class="[
                                    'w-full flex items-center justify-between px-3 py-2 rounded-lg border text-sm transition-colors',
                                    selectedCategories.includes(category.id)
                                        ? 'bg-blue-50 border-blue-200 text-blue-700'
                                        : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'
                                ]">
                                <span>{{ category.name }}</span>
                                <Check v-if="selectedCategories.includes(category.id)" :size="16" />
                            </button>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Budget</h3>
                        <div class="space-y-2">
                            <button v-for="range in priceRanges" :key="range.id"
                                @click="selectedPriceRange = selectedPriceRange === range.id ? '' : range.id" :class="[
                                    'w-full flex items-center justify-between px-3 py-2 rounded-lg border text-sm transition-colors',
                                    selectedPriceRange === range.id
                                        ? 'bg-blue-50 border-blue-200 text-blue-700'
                                        : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'
                                ]">
                                <span>{{ range.label }}</span>
                                <Check v-if="selectedPriceRange === range.id" :size="16" />
                            </button>
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Minimum Rating</h3>
                        <div class="space-y-2">
                            <button v-for="rating in ratingFilters" :key="rating.value"
                                @click="selectedRating = selectedRating === rating.value ? '' : rating.value" :class="[
                                    'w-full flex items-center justify-between px-3 py-2 rounded-lg border text-sm transition-colors',
                                    selectedRating === rating.value
                                        ? 'bg-blue-50 border-blue-200 text-blue-700'
                                        : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'
                                ]">
                                <div class="flex items-center">
                                    <div class="flex mr-2">
                                        <Star v-for="i in 5" :key="i" :size="14"
                                            :class="i <= rating.value ? 'text-yellow-400 fill-current' : 'text-gray-300'" />
                                    </div>
                                    <span>{{ rating.value }}+ stars</span>
                                </div>
                                <Check v-if="selectedRating === rating.value" :size="16" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex gap-3 justify-end pt-4 border-t border-gray-200">
                    <button @click="clearFilters"
                        class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        Clear All
                    </button>
                    <button @click="applyFilters"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Show {{ filteredVendors.length }} Results
                    </button>
                </div>
            </DialogContent>
        </Dialog>

    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
