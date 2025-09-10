<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { ref, computed } from 'vue';
import { Star, MessageCircle, Calendar, User, Filter, Search, TrendingUp, Award, Clock, Reply, ChevronDown, ExternalLink, Download, MoreVertical } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
const props = defineProps({
    reviews: {}
})

// Sample data - replace with your actual API calls
// const reviews = ref([
//     {
//         id: 1,
//         customer: {
//             name: 'Sarah Johnson',
//             avatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=40&h=40&fit=crop&crop=face',
//             verified: true
//         },
//         service: 'Wedding Photography',
//         booking: {
//             id: 'BK-2024-001',
//             date: '2024-08-15',
//             event_date: '2024-08-20'
//         },
//         rating: 5,
//         title: 'Absolutely Amazing Experience!',
//         comment: 'The photographer was incredibly professional and captured every special moment perfectly. The quality of photos exceeded our expectations and the team was so pleasant to work with.',
//         date: '2024-08-22',
//         status: 'published',
//         helpful_votes: 12,
//         response: null,
//         photos: ['photo1.jpg', 'photo2.jpg']
//     },
//     {
//         id: 2,
//         customer: {
//             name: 'Michael Chen',
//             avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=40&h=40&fit=crop&crop=face',
//             verified: true
//         },
//         service: 'Event Catering',
//         booking: {
//             id: 'BK-2024-002',
//             date: '2024-07-10',
//             event_date: '2024-07-18'
//         },
//         rating: 4,
//         title: 'Great food, minor timing issues',
//         comment: 'The food quality was excellent and guests loved the variety. However, there was a slight delay in service during the main course. Overall very satisfied.',
//         date: '2024-07-20',
//         status: 'published',
//         helpful_votes: 8,
//         response: {
//             message: 'Thank you for your feedback! We apologize for the timing issue and have improved our coordination process.',
//             date: '2024-07-21'
//         }
//     },
//     {
//         id: 3,
//         customer: {
//             name: 'Emily Rodriguez',
//             avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&crop=face',
//             verified: false
//         },
//         service: 'DJ Services',
//         booking: {
//             id: 'BK-2024-003',
//             date: '2024-09-01',
//             event_date: '2024-09-05'
//         },
//         rating: 2,
//         title: 'Not what we expected',
//         comment: 'The DJ seemed unprepared and didn\'t follow our playlist requests. Music was too loud despite multiple requests to lower it.',
//         date: '2024-09-06',
//         status: 'pending_response',
//         helpful_votes: 3,
//         response: null
//     }
// ]);

const filterStatus = ref('all');
const filterRating = ref('all');
const searchQuery = ref('');
const selectedReview = ref(null);
const showResponseModal = ref(false);
const responseText = ref('');
const showFilters = ref(false);

// Computed properties for analytics
const totalReviews = computed(() => props.reviews.length);
const averageRating = computed(() => {
    const sum = props.reviews.reduce((acc, review) => acc + review.rating, 0);
    return (sum / props.reviews.length).toFixed(1);
});

const ratingDistribution = computed(() => {
    const dist = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
    props.reviews.forEach(review => {
        dist[review.rating]++;
    });
    return dist;
});

const pendingResponses = computed(() =>
    props.reviews.filter(review => !review.response.message && review.rating <= 3).length
);

const filteredReviews = computed(() => {
    return props.reviews.filter(review => {
        const matchesStatus = filterStatus.value === 'all' ||
            (filterStatus.value === 'pending' && !review.response.message && review.rating <= 3) ||
            (filterStatus.value === 'responded' && review.response.message) ||
            (filterStatus.value === 'published' && review.status === 'published');

        const matchesRating = filterRating.value === 'all' || review.rating == filterRating.value;

        const matchesSearch = searchQuery.value === '' ||
            review.customer.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            review.service.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            review.comment.toLowerCase().includes(searchQuery.value.toLowerCase());

        return matchesStatus && matchesRating && matchesSearch;
    });
});

const openResponseModal = (review) => {
    selectedReview.value = review;
    responseText.value = review.response?.message || '';
    showResponseModal.value = true;
};

const submitResponse = () => {
    if (selectedReview.value && responseText.value.trim()) {

        router.put(route('vendor.reviews.update-response', selectedReview.value.id), {
            response: responseText.value
        }, {
            onSuccess: () => {
                selectedReview.value.response = {
                    message: responseText.value,
                    date: new Date().toISOString().split('T')[0]
                };
                showResponseModal.value = false;
                responseText.value = '';
                selectedReview.value = null;

                alert('done');
            }
        })


        // selectedReview.value.response = {
        //     message: responseText.value,
        //     date: new Date().toISOString().split('T')[0]
        // };

    }
};



const getRatingColor = (rating) => {
    if (rating >= 4) return 'text-green-600 bg-green-50';
    if (rating >= 3) return 'text-yellow-600 bg-yellow-50';
    return 'text-red-600 bg-red-50';
};

const getStatusBadge = (review) => {

    console.log('Response', review.response)

    if (!review.response.message && review.rating <= 3) {
        return 'bg-red-100 text-red-800';
    }
    if (review.response.message) {
        return 'bg-green-100 text-green-800';
    }
    return 'bg-blue-100 text-blue-800';
};

const getStatusText = (review) => {
    if (!review.response.message && review.rating <= 3) return 'Needs Response';
    if (review.response.message) return 'Responded';
    return 'Published';
};
</script>

<template>
    <VendorLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
                    <div>
                        <h1 class="text-2xl md:text-2xl font-bold text-gray-900">Customer Reviews</h1>
                        <p class="text-gray-500 mt-2">Monitor and respond to customer feedback</p>
                    </div>
                    <div class="mt-4 lg:mt-0">
                        <button
                            class="flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                            <Download class="h-5 w-5 mr-2" />
                            Export Reviews
                        </button>
                    </div>
                </div>

                <!-- Analytics Dashboard -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                    <!-- Overall Rating -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Overall Rating</p>
                                <p v-if="reviews.length + 1 > 1" class="text-2xl font-bold text-gray-900 mt-1">{{
                                    averageRating }}/5</p>
                                <p v-else class="text-l font-bold text-gray-900 mt-1">No ratings yet</p>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <Star class="h-6 w-6 text-blue-600 fill-current" />
                            </div>
                        </div>
                        <div class="flex items-center mt-3">
                            <div class="flex">
                                <Star v-for="i in 5" :key="i" class="h-4 w-4 text-yellow-400 fill-current" />
                            </div>
                            <span class="text-sm text-gray-500 ml-2">{{ totalReviews }} reviews</span>
                        </div>
                    </div>

                    <!-- Total Reviews -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Reviews</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ totalReviews }}</p>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg">
                                <MessageCircle class="h-6 w-6 text-green-600" />
                            </div>
                        </div>
                        <p class="text-sm text-green-600 mt-2 flex items-center">
                            <TrendingUp class="h-4 w-4 mr-1" />
                            +12% from last month
                        </p>
                    </div>

                    <!-- Pending Responses -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Need Response</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ pendingResponses }}</p>
                            </div>
                            <div class="bg-amber-50 p-3 rounded-lg">
                                <Clock class="h-6 w-6 text-amber-600" />
                            </div>
                        </div>
                        <p class="text-sm text-amber-600 mt-2">Requires attention</p>
                    </div>

                    <!-- Response Rate -->
                    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Response Rate</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">85%</p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg">
                                <Award class="h-6 w-6 text-purple-600" />
                            </div>
                        </div>
                        <p class="text-sm text-purple-600 mt-2">Above average</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Filters and Search -->
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <!-- Search -->
                                <div class="relative flex-1">
                                    <Search
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" />
                                    <input v-model="searchQuery" type="text" placeholder="Search reviews..."
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors" />
                                </div>

                                <!-- Filter Toggle for Mobile -->
                                <button @click="showFilters = !showFilters"
                                    class="sm:hidden flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700">
                                    <Filter class="h-5 w-5 mr-2" />
                                    Filters
                                    <ChevronDown class="h-4 w-4 ml-2" :class="{ 'rotate-180': showFilters }" />
                                </button>

                                <!-- Filters -->
                                <div class="hidden sm:flex items-center space-x-3">
                                    <div class="flex items-center space-x-2">
                                        <Filter class="h-5 w-5 text-gray-400" />
                                        <select v-model="filterStatus"
                                            class="border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                                            <option value="all">All Status</option>
                                            <option value="pending">Need Response</option>
                                            <option value="responded">Responded</option>
                                            <option value="published">Published</option>
                                        </select>
                                    </div>

                                    <select v-model="filterRating"
                                        class="border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                                        <option value="all">All Ratings</option>
                                        <option value="5">5 Stars</option>
                                        <option value="4">4 Stars</option>
                                        <option value="3">3 Stars</option>
                                        <option value="2">2 Stars</option>
                                        <option value="1">1 Star</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Mobile Filters -->
                            <div v-if="showFilters" class="mt-4 grid grid-cols-2 gap-3 sm:hidden">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select v-model="filterStatus"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                        <option value="all">All Status</option>
                                        <option value="pending">Need Response</option>
                                        <option value="responded">Responded</option>
                                        <option value="published">Published</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                    <select v-model="filterRating"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                        <option value="all">All Ratings</option>
                                        <option value="5">5 Stars</option>
                                        <option value="4">4 Stars</option>
                                        <option value="3">3 Stars</option>
                                        <option value="2">2 Stars</option>
                                        <option value="1">1 Star</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews List -->
                        <div class="space-y-4">
                            <div v-for="review in filteredReviews" :key="review.id"
                                class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200">
                                {{ console.log() }}
                                <!-- Review Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <img :src="review.customer.avatar" :alt="review.customer.name"
                                            class="w-10 h-10 rounded-full object-cover" />
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <h4 class="font-semibold text-gray-900">{{ review.customer.name }}</h4>
                                                <span v-if="review.customer.verified"
                                                    class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                                    Verified
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600">{{ review.service }}</p>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <div class="flex items-center justify-end space-x-1 mb-1">
                                            <span :class="getRatingColor(review.rating)"
                                                class="font-medium text-sm px-2 py-1 rounded-full">
                                                {{ review.rating }}.0
                                            </span>
                                            <div class="flex">
                                                <Star v-for="i in 5" :key="i" class="h-4 w-4"
                                                    :class="i <= review.rating ? 'text-yellow-400 fill-current' : 'text-gray-300'" />
                                            </div>
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                            :class="getStatusBadge(review)">
                                            {{ getStatusText(review) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Review Content -->
                                <div class="mb-4">
                                    <h5 class="font-medium text-gray-900 mb-2">{{ review.title }}</h5>
                                    <p class="text-gray-700 leading-relaxed">{{ review.comment }}</p>
                                </div>

                                <!-- Review Meta Info -->
                                <div class="flex flex-wrap items-center justify-between text-sm text-gray-500 mb-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center space-x-1">
                                            <Calendar class="h-4 w-4" />
                                            <span>Event: {{ new Date(review.booking.event_date).toLocaleDateString()
                                                }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <span>Booking: {{ review.booking.id }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <span>{{ review.helpful_votes }} found helpful</span>
                                        </div>
                                    </div>
                                    <span>{{ new Date(review.date).toLocaleDateString() }}</span>
                                </div>

                                <!-- Vendor Response -->
                                <div v-if="review.response.message"
                                    class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500 mb-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="bg-blue-100 p-2 rounded-lg">
                                            <Reply class="h-4 w-4 text-blue-600" />
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <p class="font-medium text-gray-900 mb-1">Your Response</p>
                                                <button @click="openResponseModal(review)"
                                                    class="text-blue-600 hover:text-blue-800 text-sm">
                                                    Edit
                                                </button>
                                            </div>
                                            <p class="text-gray-700">{{ review.response.message }}</p>
                                            <p class="text-sm text-gray-500 mt-2">{{ new
                                                Date(review.response.date).toLocaleDateString() }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <button @click="openResponseModal(review)" :class="[
                                        'px-4 py-2 rounded-lg transition-colors text-sm font-medium',
                                        review.response.message
                                            ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                            : 'bg-blue-600 text-white hover:bg-blue-700'
                                    ]">
                                        {{ review.response.message ? 'Edit Response' : 'Respond to Review' }}
                                    </button>

                                    <div class="flex items-center space-x-4">
                                        <button class="text-gray-500 hover:text-gray-700 transition-colors text-sm">
                                            Share
                                        </button>
                                        <button class="text-gray-500 hover:text-gray-700 transition-colors text-sm">
                                            Report
                                        </button>
                                        <button class="text-gray-500 hover:text-gray-700">
                                            <MoreVertical class="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="filteredReviews.length === 0"
                            class="text-center py-12 bg-white rounded-xl border border-gray-200">
                            <MessageCircle class="h-12 w-12 text-gray-300 mx-auto mb-4" />
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews found</h3>
                            <p class="text-gray-500 max-w-md mx-auto">
                                {{ searchQuery || filterStatus !== 'all' || filterRating !== 'all'
                                    ? 'Try adjusting your filters or search terms.'
                                    : 'Customer reviews will appear here once they start booking your services.'
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Rating Distribution -->
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rating Distribution</h3>
                            <div v-if="reviews.length + 1 > 1" class="space-y-4">
                                <div v-for="rating in [5, 4, 3, 2, 1]" :key="rating" class="flex items-center">
                                    <span class="text-sm font-medium text-gray-600 w-8">{{ rating }}</span>
                                    <Star class="h-4 w-4 text-yellow-400 fill-current mx-2" />
                                    <div class="flex-1 bg-gray-100 rounded-full h-2.5 mx-2">
                                        <div class="bg-yellow-400 h-2.5 rounded-full transition-all duration-300"
                                            :style="{ width: `${(ratingDistribution[rating] / totalReviews) * 100}%` }">
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-600 w-12 text-right">{{
                                        Math.round((ratingDistribution[rating] / totalReviews) * 100) }}%</span>
                                </div>

                            </div>
                            <span v-else class="text-sm text-gray-600 w-12 text-right">No ratings yet</span>
                        </div>

                        <!-- Quick Stats -->
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Review Insights</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Average Response Time</span>
                                    <span class="font-medium">12 hours</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Public Visibility</span>
                                    <span class="font-medium text-green-600">98%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Review Replies</span>
                                    <span class="font-medium">67%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Response Tips -->
                        <div class="bg-blue-50 rounded-xl p-5 border border-blue-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                <Award class="h-5 w-5 text-blue-600 mr-2" />
                                Response Tips
                            </h3>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-start">
                                    <span class="text-blue-500 mr-2">•</span>
                                    Respond within 24 hours for best results
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-500 mr-2">•</span>
                                    Address customers by name
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-500 mr-2">•</span>
                                    Thank them for their feedback
                                </li>
                                <li class="flex items-start">
                                    <span class="text-blue-500 mr-2">•</span>
                                    Offer solutions to any issues raised
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Response Modal -->
        <div v-if="showResponseModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 transition-opacity duration-300">
            <div class="bg-white rounded-xl p-6 w-full max-w-2xl max-h-[80vh] overflow-y-auto shadow-xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <Reply class="h-5 w-5 text-blue-600 mr-2" />
                    {{ selectedReview?.response.message ? 'Edit Response' : 'Respond to Review' }}
                </h3>

                <!-- Original Review -->
                <div class="bg-gray-50 rounded-lg p-4 mb-5 border border-gray-200">
                    <div class="flex items-center space-x-2 mb-2">
                        <img :src="selectedReview?.customer.avatar" :alt="selectedReview?.customer.name"
                            class="w-8 h-8 rounded-full" />
                        <span class="font-medium">{{ selectedReview?.customer.name }}</span>
                        <div class="flex items-center">
                            <Star v-for="i in selectedReview?.rating" :key="i"
                                class="h-4 w-4 text-yellow-400 fill-current" />
                        </div>
                    </div>
                    <p class="text-gray-700">{{ selectedReview?.comment }}</p>
                </div>

                <!-- Response Textarea -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Response</label>
                    <textarea v-model="responseText" rows="4"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                        placeholder="Thank you for your feedback. We appreciate your business and..."></textarea>
                    <p class="text-sm text-gray-500 mt-2">
                        Be professional, address specific concerns, and thank the customer for their feedback.
                    </p>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-3">
                    <button @click="showResponseModal = false"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium">
                        Cancel
                    </button>
                    <button @click="submitResponse" :disabled="!responseText.trim()"
                        class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors font-medium">
                        {{ selectedReview?.response.message ? 'Update Response' : 'Post Response' }}
                    </button>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
    transition: transform 0.2s ease;
}
</style>
