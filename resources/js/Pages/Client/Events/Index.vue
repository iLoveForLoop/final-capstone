<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import { useUIStore } from '@/store/ui';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import QuickBookingStepperModal from '@/Components/QuickBookingStepperModal.vue';
import {
    Calendar as CalendarIcon,
    Plus,
    Search,
    Filter,
    X,
    MapPin,
    Clock,
    Tag,
    BarChart3,
    ChevronUp,
    ChevronDown
} from 'lucide-vue-next';

const props = defineProps({
    events: {
        type: Object
    },
    categories: Object
})

const ui = useUIStore()

// Calendar instance
const calendar = ref(null);
const calendarEl = ref(null);

// Modal states
const showEventModal = ref(false);
const selectedEvent = ref(null);

// Filters
const selectedStatus = ref('all');
const searchQuery = ref('');
const showFilters = ref(false);

// Computed properties
const filteredEvents = computed(() => {
    let filtered = props.events?.data || [];

    if (selectedStatus.value !== 'all') {
        filtered = filtered.filter(event => event.status === selectedStatus.value);
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(event =>
            event.title?.toLowerCase().includes(query) ||
            event.location?.toLowerCase().includes(query) ||
            event.description?.toLowerCase().includes(query)
        );
    }

    return filtered;
});

const upcomingEvents = computed(() => {
    const today = new Date();
    const events = props.events?.data || [];
    return events
        .filter(event => new Date(event.start) >= today)
        .sort((a, b) => new Date(a.start) - new Date(b.start))
        .slice(0, 5);
});

const eventStats = computed(() => {
    const today = new Date();
    const events = props.events?.data || [];

    return {
        total: events.length,
        upcoming: events.filter(e => new Date(e.start) >= today && e.status !== 'cancelled').length,
        confirmed: events.filter(e => e.status === 'confirmed').length,
        completed: events.filter(e => e.status === 'completed').length,
        pending: events.filter(e => e.status === 'pending').length,
        cancelled: events.filter(e => e.status === 'cancelled').length
    };
});

// Calendar methods
const initializeCalendar = () => {
    if (!calendarEl.value) return;


    console.log('EVENT CAL: ', props.events.data);

    calendar.value = new Calendar(calendarEl.value, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: (props.events?.data || []).map(event => ({
            id: event.id,
            title: event.title,
            start: event.start,
            backgroundColor: getEventColor(event.status),
            borderColor: getEventColor(event.status),
            extendedProps: event,
            // allDay: true,
        })),
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true,
            meridiem: 'short'
        },

        eventClick: (info) => {
            selectedEvent.value = info.event.extendedProps;
            showEventModal.value = true;
        },
        height: 'auto',
        dayMaxEvents: 3,
        moreLinkClick: 'popover'
    });

    calendar.value.render();
};

const getEventColor = (status) => {
    const colors = {
        confirmed: '#10b981',
        pending: '#f59e0b',
        completed: '#3b82f6',
        cancelled: '#ef4444'
    };
    return colors[status] || '#6b7280';
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-amber-100 text-amber-800 border-amber-200',
        confirmed: 'bg-green-100 text-green-800 border-green-200',
        completed: 'bg-blue-100 text-blue-800 border-blue-200',
        cancelled: 'bg-red-100 text-red-800 border-red-200'
    };
    return colors[status] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatDateTime = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const clearFilters = () => {
    selectedStatus.value = 'all';
    searchQuery.value = '';
    showFilters.value = false;
};

// Lifecycle
onMounted(() => {
    nextTick(() => {
        if (ui.isEventCalendarView) {
            initializeCalendar();
        }
    });
});

const eventModal = ref(null)
</script>

<template>
    <ClientLayout>
        <QuickBookingStepperModal ref="eventModal" :categories="categories" />
        <div class="min-h-screen bg-gray-50/30">

            <!-- Header Section -->
            <div class="bg-white border-b border-gray-200/60">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <CalendarIcon class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" />
                            </div>
                            <div>
                                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">My Events</h1>
                                <p class="text-gray-600 text-xs sm:text-sm">Manage and track all your events</p>
                            </div>
                        </div>
                        <button @click="eventModal?.open()"
                            class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium flex items-center justify-center space-x-2 shadow-sm hover:shadow-md">
                            <Plus class="w-4 h-4" />
                            <span>Plan Event</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- View Toggle & Filters -->
            <div class="bg-white border-b border-gray-200/60 sticky top-0 z-10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- View Toggle -->
                        <div class="flex rounded-lg bg-gray-100 p-1 w-full sm:w-auto">
                            <button @click="ui.toggleEventCalendarView(); nextTick(() => initializeCalendar())" :class="[
                                'px-3 sm:px-4 py-2 text-sm rounded-md transition-all duration-200 font-medium flex items-center justify-center space-x-2 flex-1 sm:flex-none',
                                ui.isEventCalendarView
                                    ? 'bg-white text-blue-600 shadow-sm'
                                    : 'text-gray-600 hover:text-gray-800'
                            ]">
                                <CalendarIcon class="w-4 h-4" />
                                <span>Calendar</span>
                            </button>
                            <button @click="ui.toggleEventCalendarView" :class="[
                                'px-3 sm:px-4 py-2 text-sm rounded-md transition-all duration-200 font-medium flex items-center justify-center space-x-2 flex-1 sm:flex-none',
                                !ui.isEventCalendarView
                                    ? 'bg-white text-blue-600 shadow-sm'
                                    : 'text-gray-600 hover:text-gray-800'
                            ]">
                                <Tag class="w-4 h-4" />
                                <span>List</span>
                            </button>
                        </div>

                        <!-- Filter Toggle -->
                        <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4">
                            <div v-if="!ui.isEventCalendarView" class="text-sm text-gray-600">
                                {{ filteredEvents.length }} event{{ filteredEvents.length !== 1 ? 's' : '' }}
                            </div>
                            <button @click="showFilters = !showFilters"
                                class="flex items-center space-x-2 px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:border-gray-400 transition-colors">
                                <Filter class="w-4 h-4" />
                                <span class="hidden xs:inline">Filters</span>
                                <ChevronDown v-if="!showFilters" class="w-4 h-4" />
                                <ChevronUp v-else class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Expandable Filters -->
                    <div v-if="showFilters && !ui.isEventCalendarView" class="mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Search -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search Events</label>
                                <div class="relative">
                                    <Search
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                    <input v-model="searchQuery" type="text"
                                        placeholder="Search by event name, location..."
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select v-model="selectedStatus"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="all">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <!-- Clear Filters -->
                            <div class="md:col-span-3 flex justify-end">
                                <button @click="clearFilters"
                                    class="flex items-center space-x-2 px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:border-gray-400 transition-colors">
                                    <X class="w-4 h-4" />
                                    <span>Clear Filters</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6">
                    <!-- Calendar/List View -->
                    <div class="lg:col-span-3">
                        <!-- Calendar View -->
                        <div v-if="ui.isEventCalendarView"
                            class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200/60 overflow-hidden">
                            <div class="p-3 sm:p-4 border-b border-gray-200/60 bg-white">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <h2 class="text-lg font-semibold text-gray-900">Event Calendar</h2>
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm">
                                        <div v-for="status in ['confirmed', 'pending', 'completed', 'cancelled']"
                                            :key="status" class="flex items-center space-x-1.5">
                                            <div class="w-2 h-2 sm:w-3 sm:h-3 rounded-sm"
                                                :style="{ backgroundColor: getEventColor(status) }"></div>
                                            <span class="text-gray-600 capitalize text-xs sm:text-sm">{{ status
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div ref="calendarEl" class="p-2 sm:p-4"></div>
                        </div>

                        <!-- List View -->
                        <div v-else class="space-y-3 sm:space-y-4">
                            <div v-if="filteredEvents.length === 0"
                                class="text-center py-8 sm:py-12 bg-white rounded-lg sm:rounded-xl border border-gray-200/60">
                                <div class="max-w-sm mx-auto px-4">
                                    <CalendarIcon
                                        class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400 mb-3 sm:mb-4" />
                                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-2">No events found</h3>
                                    <p class="text-gray-600 text-sm sm:text-base mb-4 sm:mb-6">Try adjusting your search
                                        or filters to find events.</p>
                                    <button @click="clearFilters"
                                        class="bg-blue-600 text-white px-4 sm:px-6 py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                        Clear Filters
                                    </button>
                                </div>
                            </div>

                            <div v-for="event in filteredEvents" :key="event.id"
                                class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200/60 p-4 sm:p-6 hover:shadow-md transition-all duration-200 cursor-pointer group"
                                @click="selectedEvent = event; showEventModal = true">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2 sm:mb-3">
                                            <span
                                                :class="['px-2 sm:px-3 py-1 text-xs font-medium rounded-full border', getStatusColor(event.status)]">
                                                {{ event.status?.charAt(0).toUpperCase() + event.status?.slice(1) }}
                                            </span>
                                            <span class="text-xs text-gray-500">{{ event.id }}</span>
                                        </div>

                                        <h3
                                            class="text-base sm:text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
                                            {{ event.title }}
                                        </h3>

                                        <p class="text-gray-600 text-sm mb-3 sm:mb-4 line-clamp-2">{{ event.description
                                        }}</p>

                                        <div
                                            class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 text-sm text-gray-500">
                                            <div class="flex items-center space-x-1.5">
                                                <Clock class="w-4 h-4" />
                                                <span class="text-xs sm:text-sm">{{ formatDateTime(event.start)
                                                }}</span>
                                            </div>
                                            <div v-if="event.location" class="flex items-center space-x-1.5">
                                                <MapPin class="w-4 h-4" />
                                                <span class="text-xs sm:text-sm">{{ event.location }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-left sm:text-right">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ event.services?.length || 0 }} service{{ (event.services?.length || 0)
                                                !== 1 ? 's' : '' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Services Preview -->
                                <div v-if="event.services?.length > 0"
                                    class="flex flex-wrap gap-1 sm:gap-2 mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-100">
                                    <span v-for="service in event.services.slice(0, 3)" :key="service.name"
                                        class="px-2 py-1 bg-gray-50 text-gray-700 text-xs rounded border border-gray-200">
                                        {{ service.name }}
                                    </span>
                                    <span v-if="event.services.length > 3"
                                        class="px-2 py-1 bg-gray-50 text-gray-500 text-xs rounded border border-gray-200">
                                        +{{ event.services.length - 3 }} more
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-4 sm:space-y-6">
                        <!-- Stats Cards -->
                        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200/60 p-4 sm:p-6">
                            <h3
                                class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                <BarChart3 class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" />
                                <span>Event Overview</span>
                            </h3>
                            <div class="space-y-3 sm:space-y-4">
                                <div v-for="stat in [
                                    { label: 'Total Events', value: eventStats.total, color: 'text-gray-900' },
                                    { label: 'Upcoming', value: eventStats.upcoming, color: 'text-blue-600' },
                                    { label: 'Confirmed', value: eventStats.confirmed, color: 'text-green-600' },
                                    { label: 'Pending', value: eventStats.pending, color: 'text-amber-600' },
                                    { label: 'Completed', value: eventStats.completed, color: 'text-blue-600' },
                                    { label: 'Cancelled', value: eventStats.cancelled, color: 'text-red-600' }
                                ]" :key="stat.label" class="flex justify-between items-center">
                                    <span class="text-xs sm:text-sm text-gray-600">{{ stat.label }}</span>
                                    <span :class="['text-base sm:text-lg font-semibold', stat.color]">{{ stat.value
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Events -->
                        <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200/60 p-4 sm:p-6">
                            <h3
                                class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                <Clock class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" />
                                <span>Upcoming Events</span>
                            </h3>
                            <div class="space-y-2 sm:space-y-3">
                                <div v-for="event in upcomingEvents" :key="event.id"
                                    class="p-2 sm:p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors group"
                                    @click="selectedEvent = event; showEventModal = true">
                                    <div class="flex items-start space-x-2 sm:space-x-3">
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors truncate">
                                                {{ event.title }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">{{ formatDate(event.start) }}</p>
                                            <span
                                                :class="['inline-block mt-1 sm:mt-2 px-2 py-0.5 text-xs rounded-full', getStatusColor(event.status)]">
                                                {{ event.status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="upcomingEvents.length === 0"
                                    class="text-center py-3 sm:py-4 text-gray-500 text-xs sm:text-sm">
                                    No upcoming events
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Detail Modal -->
            <div v-if="showEventModal && selectedEvent"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-2 sm:p-4 z-50 transition-opacity duration-200">
                <div
                    class="bg-white rounded-lg sm:rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-200 scale-100">
                    <div class="p-4 sm:p-6">
                        <!-- Modal Header -->
                        <div class="flex items-start justify-between mb-4 sm:mb-6">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-2 sm:mb-3">
                                    <span
                                        :class="['px-2 sm:px-3 py-1 text-xs sm:text-sm font-medium rounded-full border', getStatusColor(selectedEvent.status)]">
                                        {{ selectedEvent.status?.charAt(0).toUpperCase() +
                                            selectedEvent.status?.slice(1) }}
                                    </span>
                                    <span class="text-xs sm:text-sm text-gray-500">{{ selectedEvent.id }}</span>
                                </div>
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">{{ selectedEvent.title }}
                                </h2>
                                <p class="text-gray-600 text-sm sm:text-base">{{ selectedEvent.description }}</p>
                            </div>
                            <button @click="showEventModal = false"
                                class="p-1 sm:p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0 ml-2">
                                <X class="w-4 h-4 sm:w-5 sm:h-5" />
                            </button>
                        </div>

                        <!-- Event Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                            <!-- Basic Info -->
                            <div class="bg-gray-50 rounded-lg sm:rounded-xl p-3 sm:p-5">
                                <h3 class="font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                    <CalendarIcon class="w-4 h-4" />
                                    <span class="text-sm sm:text-base">Event Details</span>
                                </h3>
                                <div class="space-y-2 sm:space-y-3">
                                    <div class="flex items-center text-sm">
                                        <Clock class="w-4 h-4 mr-2 sm:mr-3 text-gray-400 flex-shrink-0" />
                                        <div>
                                            <span class="text-gray-600 text-xs sm:text-sm">Date & Time</span>
                                            <p class="font-medium text-gray-900 text-sm sm:text-base">{{
                                                formatDateTime(selectedEvent.start)
                                            }}</p>
                                        </div>
                                    </div>
                                    <div v-if="selectedEvent.location" class="flex items-center text-sm">
                                        <MapPin class="w-4 h-4 mr-2 sm:mr-3 text-gray-400 flex-shrink-0" />
                                        <div>
                                            <span class="text-gray-600 text-xs sm:text-sm">Location</span>
                                            <p class="font-medium text-gray-900 text-sm sm:text-base">{{
                                                selectedEvent.location }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Services -->
                            <div class="bg-gray-50 rounded-lg sm:rounded-xl p-3 sm:p-5">
                                <h3 class="font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center space-x-2">
                                    <Tag class="w-4 h-4" />
                                    <span class="text-sm sm:text-base">Booked Services</span>
                                </h3>
                                <div v-if="selectedEvent.services?.length > 0" class="space-y-2 sm:space-y-3">
                                    <div v-for="service in selectedEvent.services" :key="service.name"
                                        class="flex items-center justify-between p-2 sm:p-3 bg-white rounded-lg border border-gray-200">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ service.name }}</p>
                                            <p class="text-xs font-medium text-gray-900 truncate my-2">{{
                                                service.category }}
                                            </p>
                                            <p class="text-xs text-gray-600 mt-1 truncate">{{ service.provider }}</p>
                                        </div>
                                        <span :class="[
                                            'px-2 py-1 text-xs font-medium rounded-full border flex-shrink-0 ml-2',
                                            service.status === 'confirmed' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-amber-100 text-amber-800 border-amber-200'
                                        ]">
                                            {{ service.status }}
                                        </span>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4 sm:py-6 text-gray-500">
                                    <Tag class="w-6 h-6 sm:w-8 sm:h-8 mx-auto mb-2 text-gray-400" />
                                    <p class="text-xs sm:text-sm">No services booked yet</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-2 sm:space-x-3 pt-4 sm:pt-6 border-t border-gray-200">
                            <button @click="showEventModal = false"
                                class="px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<style>
/* Enhanced FullCalendar Styles */
.fc {
    font-family: inherit;
    --fc-border-color: #e2e8f0;
    --fc-today-bg-color: #f0f9ff;
    --fc-neutral-bg-color: #f8fafc;
}

.fc .fc-toolbar {
    padding: 0.5rem 1rem;
    margin-bottom: 0;
    border-bottom: 1px solid #e2e8f0;
}

.fc .fc-toolbar-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

/* Calendar Grid Improvements */
.fc .fc-scrollgrid {
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px;
    overflow: hidden;
}

.fc .fc-scrollgrid thead {
    background: #f8fafc;
}

.fc .fc-col-header-cell {
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    background: transparent !important;
    padding: 8px 4px;
}

.fc .fc-col-header-cell:last-child {
    border-right: none;
}

.fc .fc-col-header-cell-cushion {
    padding: 8px 4px;
    text-transform: uppercase;
    font-size: 10px;
    letter-spacing: 0.05em;
    font-weight: 600;
    color: #374151;
}

/* Day Cells */
.fc .fc-daygrid-day {
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    background: white;
    transition: background-color 0.15s ease;
}

.fc .fc-daygrid-day:hover {
    background: #f8fafc !important;
}

.fc .fc-daygrid-day:last-child {
    border-right: none;
}

.fc .fc-daygrid-day.fc-day-other {
    background-color: #fafafa;
}

.fc .fc-daygrid-day.fc-day-other .fc-daygrid-day-number {
    color: #9ca3af;
}

.fc .fc-daygrid-day-number {
    color: #1f2937;
    font-weight: 500;
    padding: 4px;
    font-size: 12px;
}

/* Today Highlight */
.fc .fc-day-today {
    background: #eff6ff !important;
    position: relative;
}

.fc .fc-day-today::before {
    content: '';
    position: absolute;
    top: 1px;
    left: 1px;
    right: 1px;
    bottom: 1px;
    border: 2px solid #3b82f6;
    border-radius: 4px;
    pointer-events: none;
}

.fc .fc-day-today .fc-daygrid-day-number {
    color: #1e40af;
    font-weight: 600;
}

/* Events Styling */
.fc-event {
    border-radius: 4px;
    font-size: 10px;
    padding: 2px 4px;
    margin: 1px;
    cursor: pointer;
    border: none;
    font-weight: 500;
    transition: all 0.15s ease;
    border-left: 2px solid transparent;
    line-height: 1.2;
}

.fc-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Mobile optimizations */
@media (max-width: 640px) {
    .fc .fc-toolbar {
        flex-direction: column;
        gap: 0.5rem;
    }

    .fc .fc-toolbar .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
    }
}

/* Extra small breakpoint */
@media (max-width: 475px) {
    .xs\:inline {
        display: inline;
    }
}
</style>
