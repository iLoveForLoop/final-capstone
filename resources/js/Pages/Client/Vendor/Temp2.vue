<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Vendor Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">My Services</h1>
                        <p class="text-gray-600 mt-1">Manage your service offerings and track performance</p>
                    </div>
                    <button
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Service
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Services</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ services.length }}</p>
                        </div>
                        <div class="p-3 bg-indigo-100 rounded-xl">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Services</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ activeServicesCount }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-xl">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">This Month</p>
                            <p class="text-3xl font-bold text-blue-600 mt-2">₱342K</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-xl">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Bookings</p>
                            <p class="text-3xl font-bold text-orange-600 mt-2">28</p>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-xl">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3a4 4 0 118 0v4m-4 7v10"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Search -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                    <div class="flex flex-col sm:flex-row gap-4 flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input v-model="searchQuery" type="text" placeholder="Search services..."
                                class="pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent w-full sm:w-80">
                        </div>

                        <select v-model="selectedCategory"
                            class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">All Categories</option>
                            <option value="catering">Catering</option>
                            <option value="photography">Photography</option>
                            <option value="music">Music & DJ</option>
                            <option value="decoration">Decoration</option>
                            <option value="venue">Venue</option>
                        </select>

                        <select v-model="selectedStatus"
                            class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button @click="viewMode = 'grid'" :class="[
                            'p-3 rounded-lg transition-colors',
                            viewMode === 'grid' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        ]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                </path>
                            </svg>
                        </button>
                        <button @click="viewMode = 'list'" :class="[
                            'p-3 rounded-lg transition-colors',
                            viewMode === 'list' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        ]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Services Grid View -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="service in filteredServices" :key="service.id"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 group relative"
                    :class="{ 'ring-2 ring-green-500': service.isActive, 'opacity-80': !service.isActive }">

                    <!-- Active indicator ribbon -->
                    <div v-if="service.isActive"
                        class="absolute -right-8 top-4 rotate-45 bg-green-500 text-white px-8 py-1 text-xs font-bold z-10 shadow-md">
                        ACTIVE
                    </div>

                    <!-- Service Image -->
                    <div class="relative h-48 bg-gray-100 overflow-hidden">
                        <img :src="service.image" :alt="service.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            :class="{ 'brightness-95': !service.isActive }">

                        <!-- Status Toggle -->
                        <div
                            class="absolute top-4 left-4 bg-white bg-opacity-95 backdrop-blur-sm px-3 py-2 rounded-lg border shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-xs font-medium"
                                    :class="service.isActive ? 'text-green-700' : 'text-gray-500'">
                                    {{ service.isActive ? 'Active' : 'Inactive' }}
                                </span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="service.isActive" class="sr-only peer"
                                        @change="updateServiceStatus(service)">
                                    <div
                                        class="w-9 h-5 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition-colors duration-300">
                                        <div
                                            class="absolute top-0.5 left-0.5 bg-white w-4 h-4 rounded-full transition-transform duration-300 transform peer-checked:translate-x-4">
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Category Badge -->
                        <div class="absolute top-4 right-4">
                            <span
                                class="bg-white bg-opacity-95 backdrop-blur-sm px-3 py-1 text-xs font-medium rounded-full text-gray-700 border shadow-sm">
                                {{ getCategoryDisplay(service.category) }}
                            </span>
                        </div>
                    </div>

                    <!-- Service Content -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ service.name }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ service.description }}</p>

                        <!-- Service Details -->
                        <div class="space-y-3 mb-6">
                            <!-- Price -->
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                                <span class="font-semibold text-gray-900">₱{{ service.price.toLocaleString() }}</span>
                                <span class="text-gray-500 ml-1">/ {{ service.priceUnit }}</span>
                            </div>

                            <!-- Performance Stats -->
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                        </path>
                                    </svg>
                                    <span>{{ service.rating }} ({{ service.reviews }})</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    <span>{{ service.views }} views</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <button
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 px-4 rounded-lg font-medium transition-colors text-sm flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Edit
                            </button>
                            <button
                                class="px-3 py-2.5 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </button>
                            <button
                                class="px-3 py-2.5 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services List View -->
            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Service</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Category</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Price</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Performance</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="service in filteredServices" :key="service.id"
                                class="hover:bg-gray-50 transition-colors" :class="{ 'bg-green-50': service.isActive }">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="relative">
                                            <img :src="service.image" :alt="service.name"
                                                class="h-14 w-14 rounded-xl object-cover"
                                                :class="{ 'brightness-95': !service.isActive }">
                                            <div v-if="service.isActive"
                                                class="absolute -top-1 -right-1 h-5 w-5 bg-green-500 rounded-full flex items-center justify-center">
                                                <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900 mb-1">{{ service.name }}
                                            </div>
                                            <div class="text-sm text-gray-500 max-w-xs truncate">{{ service.description
                                                }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900">{{
                                        getCategoryDisplay(service.category) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">₱{{ service.price.toLocaleString()
                                        }}</div>
                                    <div class="text-sm text-gray-500">per {{ service.priceUnit }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <!-- Status Toggle -->
                                    <div class="flex items-center gap-2">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" v-model="service.isActive" class="sr-only peer"
                                                @change="updateServiceStatus(service)">
                                            <div
                                                class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition-colors duration-300">
                                                <div
                                                    class="absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full transition-transform duration-300 transform peer-checked:translate-x-5">
                                                </div>
                                            </div>
                                        </label>
                                        <span class="text-xs font-medium px-2 py-1 rounded-full"
                                            :class="service.isActive ? 'text-green-700 bg-green-100' : 'text-gray-500 bg-gray-100'">
                                            {{ service.isActive ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 font-medium">{{ service.rating }} ⭐ ({{
                                        service.reviews }})</div>
                                    <div class="text-sm text-gray-500">{{ service.views }} views</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <button
                                            class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            class="text-gray-600 hover:text-blue-600 text-sm flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                            View
                                        </button>
                                        <button
                                            class="text-gray-600 hover:text-red-600 text-sm flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredServices.length === 0"
                class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="max-w-md mx-auto">
                    <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No services found</h3>
                    <p class="text-gray-500 mb-6">Try adjusting your search or filters, or create a new service to get
                        started.</p>
                    <button
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                        Create Your First Service
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'VendorServicesPage',
    data() {
        return {
            viewMode: 'grid',
            selectedCategory: '',
            selectedStatus: '',
            searchQuery: '',
            services: [
                {
                    id: 1,
                    name: 'Premium Wedding Catering Package',
                    description: 'Complete catering service for weddings with international and local cuisine options. Includes setup, serving staff, and cleanup service.',
                    category: 'catering',
                    price: 2500,
                    priceUnit: 'person',
                    status: 'active',
                    rating: 4.8,
                    reviews: 156,
                    views: 2847,
                    image: 'https://images.unsplash.com/photo-1555244162-803834f70033?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                },
                {
                    id: 2,
                    name: 'Professional Wedding Photography',
                    description: 'Complete wedding photography coverage including pre-wedding shoot, ceremony, reception, and edited high-resolution photos with online gallery.',
                    category: 'photography',
                    price: 75000,
                    priceUnit: 'event',
                    status: 'active',
                    rating: 4.9,
                    reviews: 89,
                    views: 1923,
                    image: 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                },
                {
                    id: 3,
                    name: 'Corporate Event Catering',
                    description: 'Professional catering services for corporate events, seminars, and business meetings. Flexible menu options and dietary accommodations available.',
                    category: 'catering',
                    price: 1200,
                    priceUnit: 'person',
                    status: 'active',
                    rating: 4.7,
                    reviews: 234,
                    views: 3156,
                    image: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                },
                {
                    id: 4,
                    name: 'Live Band Performance',
                    description: 'Professional live band for weddings, parties, and events. Repertoire includes classic hits, modern songs, and Filipino favorites with sound system.',
                    category: 'music',
                    price: 35000,
                    priceUnit: 'event',
                    status: 'active',
                    rating: 4.6,
                    reviews: 67,
                    views: 876,
                    image: 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                },
                {
                    id: 5,
                    name: 'Elegant Wedding Decoration',
                    description: 'Complete wedding decoration service including floral arrangements, lighting design, table settings, and venue transformation with setup team.',
                    category: 'decoration',
                    price: 120000,
                    priceUnit: 'event',
                    status: 'inactive',
                    rating: 4.8,
                    reviews: 78,
                    views: 1543,
                    image: 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                },
                {
                    id: 6,
                    name: 'Garden Wedding Venue',
                    description: 'Beautiful outdoor garden venue perfect for intimate weddings and celebrations. Includes basic facilities, parking, and bridal suite.',
                    category: 'venue',
                    price: 50000,
                    priceUnit: 'day',
                    status: 'active',
                    rating: 4.5,
                    reviews: 45,
                    views: 2134,
                    image: 'https://images.unsplash.com/photo-1519741497674-611481863552?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                },
                {
                    id: 7,
                    name: 'Portrait Photography Session',
                    description: 'Professional portrait photography for individuals, families, or couples. Includes studio and outdoor location options with edited photos.',
                    category: 'photography',
                    price: 8500,
                    priceUnit: 'session',
                    status: 'draft',
                    rating: 4.7,
                    reviews: 112,
                    views: 634,
                    image: 'https://images.unsplash.com/photo-1554080353-a576cf803bda?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                },
                {
                    id: 8,
                    name: 'DJ & Sound System Package',
                    description: 'Professional DJ services with complete sound system setup for parties, weddings, and corporate events. Includes lighting effects and microphones.',
                    category: 'music',
                    price: 18000,
                    priceUnit: 'event',
                    status: 'active',
                    rating: 4.4,
                    reviews: 93,
                    views: 1287,
                    image: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                }
            ]
        }
    },
    computed: {
        filteredServices() {
            return this.services.filter(service => {
                const matchesSearch = service.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    service.description.toLowerCase().includes(this.searchQuery.toLowerCase());
                const matchesCategory = !this.selectedCategory || service.category === this.selectedCategory;
                const matchesStatus = !this.selectedStatus || service.status === this.selectedStatus;

                return matchesSearch && matchesCategory && matchesStatus;
            });
        },
        activeServicesCount() {
            return this.services.filter(service => service.status === 'active').length;
        }
    },
    methods: {
        getCategoryDisplay(category) {
            const categories = {
                catering: 'Catering',
                photography: 'Photography',
                music: 'Music & DJ',
                decoration: 'Decoration',
                venue: 'Venue'
            };
            return categories[category] || category;
        },
        getStatusClass(status) {
            const classes = {
                active: 'bg-green-100 text-green-800 border border-green-200',
                inactive: 'bg-red-100 text-red-800 border border-red-200',
                draft: 'bg-gray-100 text-gray-800 border border-gray-200'
            };
            return classes[status] || classes.draft;
        }
    }
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
