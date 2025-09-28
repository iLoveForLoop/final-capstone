<script setup>
import ClientNavbar from '@/Components/ClientNavbar.vue';
import { ref, computed, onMounted, nextTick } from 'vue';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import { useUIStore } from '@/store/ui';

const props = defineProps({
    events: {
        type: Object
    }
})

const ui = ref(useUIStore())
console.log('Events: ', props.events);

// Calendar instance
const calendar = ref(null);
const calendarEl = ref(null);

// View modes
const viewMode = ref('calendar'); // calendar, list, timeline
const calendarView = ref('dayGridMonth'); // dayGridMonth, timeGridWeek, timeGridDay, listWeek

// Modal states
const showEventModal = ref(false);
const showCreateEventModal = ref(false);
const selectedEvent = ref(null);

// Form data for new events
const newEvent = ref({
    title: '',
    type: '',
    date: '',
    startTime: '',
    endTime: '',
    location: '',
    description: '',
    budget: '',
    priority: 'medium',
    reminder: '1day'
});

// Event types and categories
const eventTypes = [
    { value: 'wedding', label: 'Wedding', color: '#e91e63', icon: '💒' },
    { value: 'birthday', label: 'Birthday Party', color: '#ff9800', icon: '🎂' },
    { value: 'corporate', label: 'Corporate Event', color: '#2196f3', icon: '💼' },
    { value: 'anniversary', label: 'Anniversary', color: '#9c27b0', icon: '💕' },
    { value: 'graduation', label: 'Graduation', color: '#4caf50', icon: '🎓' },
    { value: 'conference', label: 'Conference', color: '#607d8b', icon: '🏢' },
    { value: 'party', label: 'Party', color: '#ff5722', icon: '🎉' },
    { value: 'meeting', label: 'Meeting', color: '#795548', icon: '👥' },
    { value: 'other', label: 'Other', color: '#9e9e9e', icon: '📅' }
];

// Mock events data


// Filters
const selectedEventType = ref('all');
const selectedStatus = ref('all');
const selectedPriority = ref('all');
const searchQuery = ref('');

// Computed properties
const filteredEvents = computed(() => {
    let filtered = props.events.data;

    if (selectedEventType.value !== 'all') {
        filtered = filtered.filter(event => event.type === selectedEventType.value);
    }

    if (selectedStatus.value !== 'all') {
        filtered = filtered.filter(event => event.status === selectedStatus.value);
    }

    if (selectedPriority.value !== 'all') {
        filtered = filtered.filter(event => event.priority === selectedPriority.value);
    }

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(event =>
            event.title.toLowerCase().includes(query) ||
            event.location.toLowerCase().includes(query) ||
            event.description.toLowerCase().includes(query)
        );
    }

    return filtered;
});

const upcomingEvents = computed(() => {
    const today = new Date();
    return props.events.data
        .filter(event => new Date(event.start) >= today)
        .sort((a, b) => new Date(a.start) - new Date(b.start))
        .slice(0, 3);
});

const eventStats = computed(() => {
    const today = new Date();
    const thisMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);

    return {
        total: props.events.data.length,
        upcoming: props.events.data.filter(e => new Date(e.start) >= today).length,
        thisMonth: props.events.data.filter(e => {
            const eventDate = new Date(e.start);
            return eventDate >= thisMonth && eventDate < nextMonth;
        }).length,
        totalBudget: props.events.data.reduce((sum, e) => sum + e.budget, 0)
    };
});

// Calendar methods
const initializeCalendar = () => {
    if (!calendarEl.value) return;

    calendar.value = new Calendar(calendarEl.value, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView: calendarView.value,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: props.events.data.map(event => ({
            id: event.id,
            title: event.title,
            start: event.start,
            end: event.end,
            backgroundColor: event.color,
            borderColor: event.color,
            extendedProps: event
        })),
        eventClick: (info) => {
            selectedEvent.value = info.event.extendedProps;
            showEventModal.value = true;
        },
        dateClick: (info) => {
            newEvent.value.date = info.dateStr;
            showCreateEventModal.value = true;
        },
        height: 'auto',
        dayMaxEvents: 3,
        moreLinkClick: 'popover'
    });

    calendar.value.render();
};

// Event methods
const openCreateEventModal = () => {
    newEvent.value = {
        title: '',
        type: '',
        date: '',
        startTime: '',
        endTime: '',
        location: '',
        description: '',
        budget: '',
        priority: 'medium',
        reminder: '1day'
    };
    showCreateEventModal.value = true;
};

const createEvent = () => {
    if (!newEvent.value.title || !newEvent.value.date) return;

    const eventType = eventTypes.find(t => t.value === newEvent.value.type);
    const newEventData = {
        id: 'evt' + Date.now(),
        title: newEvent.value.title,
        type: newEvent.value.type,
        start: `${newEvent.value.date}T${newEvent.value.startTime || '09:00'}:00`,
        end: `${newEvent.value.date}T${newEvent.value.endTime || '17:00'}:00`,
        location: newEvent.value.location,
        description: newEvent.value.description,
        budget: parseInt(newEvent.value.budget) || 0,
        priority: newEvent.value.priority,
        status: 'draft',
        services: [],
        tasks: [],
        reminders: [],
        createdDate: new Date().toISOString().split('T')[0],
        color: eventType?.color || '#9e9e9e'
    };

    props.events.data.push(newEventData);

    // Add to calendar
    if (calendar.value) {
        calendar.value.addEvent({
            id: newEventData.id,
            title: newEventData.title,
            start: newEventData.start,
            end: newEventData.end,
            backgroundColor: newEventData.color,
            borderColor: newEventData.color,
            extendedProps: newEventData
        });
    }

    showCreateEventModal.value = false;
    alert('Event created successfully!');
};

const deleteEvent = (eventId) => {
    if (confirm('Are you sure you want to delete this event?')) {
        const index = props.events.data.findIndex(e => e.id === eventId);
        if (index !== -1) {
            props.events.data.splice(index, 1);
            if (calendar.value) {
                const calendarEvent = calendar.value.getEventById(eventId);
                if (calendarEvent) calendarEvent.remove();
            }
            showEventModal.value = false;
            alert('Event deleted successfully!');
        }
    }
};

const getEventTypeInfo = (type) => {
    return eventTypes.find(t => t.value === type) || eventTypes[eventTypes.length - 1];
};

const getStatusColor = (status) => {
    const colors = {
        draft: 'bg-gray-100 text-gray-800',
        planning: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        completed: 'bg-blue-100 text-blue-800',
        cancelled: 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getPriorityColor = (priority) => {
    const colors = {
        low: 'text-green-600',
        medium: 'text-yellow-600',
        high: 'text-red-600'
    };
    return colors[priority] || 'text-gray-600';
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatDateTime = (dateStr) => {
    return new Date(dateStr).toLocaleString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const clearFilters = () => {
    selectedEventType.value = 'all';
    selectedStatus.value = 'all';
    selectedPriority.value = 'all';
    searchQuery.value = '';
};

// Lifecycle
onMounted(() => {
    nextTick(() => {
        if (viewMode.value === 'calendar') {
            initializeCalendar();
        }
    });
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <ClientNavbar />

        <!-- Header Section -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Events</h1>
                        <p class="text-gray-600">Plan, organize, and manage all your events in one place</p>
                    </div>
                    <div class="flex space-x-3">
                        <button @click="openCreateEventModal"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Create Event</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V8a1 1 0 011-1h3z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Events</p>
                            <p class="text-2xl font-bold text-gray-900">{{ eventStats.total }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Upcoming</p>
                            <p class="text-2xl font-bold text-gray-900">{{ eventStats.upcoming }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V8a1 1 0 011-1h3z">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">This Month</p>
                            <p class="text-2xl font-bold text-gray-900">{{ eventStats.thisMonth }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Budget</p>
                            <p class="text-2xl font-bold text-gray-900">{{ formatPrice(eventStats.totalBudget) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Toggle & Filters -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <!-- View Toggle -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex rounded-lg border border-gray-300 p-1">
                        <button @click="ui.toggleEventCalendarView(); nextTick(() => initializeCalendar())" :class="[
                            'px-4 py-2 text-sm rounded-md transition-colors',
                            ui.isEventCalendarView
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-600 hover:text-gray-800'
                        ]">
                            Calendar View
                        </button>
                        <button @click="ui.toggleEventCalendarView" :class="[
                            'px-4 py-2 text-sm rounded-md transition-colors',
                            !ui.isEventCalendarView
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-600 hover:text-gray-800'
                        ]">
                            List View
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div v-if="!ui.isEventCalendarView" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Events</label>
                        <input v-model="searchQuery" type="text" placeholder="Search events..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Event Type -->
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                        <select v-model="selectedEventType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Types</option>
                            <option v-for="type in eventTypes" :key="type.value" :value="type.value">
                                {{ type.icon }} {{ type.label }}
                            </option>
                        </select>
                    </div> -->

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select v-model="selectedStatus"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="planning">Planning</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <!-- Priority -->
                    <!-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select v-model="selectedPriority"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Priority</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div> -->
                </div>

                <div v-if="!ui.isEventCalendarView" class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        {{ filteredEvents.length }} event{{ filteredEvents.length !== 1 ? 's' : '' }} found
                    </div>
                    <button @click="clearFilters" class="text-sm text-gray-600 hover:text-gray-800">
                        Clear filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Calendar/List View -->
                <div class="lg:col-span-3">
                    <!-- Calendar View -->
                    <div v-if="ui.isEventCalendarView" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div ref="calendarEl"></div>
                    </div>

                    <!-- List View -->
                    <div v-else class="space-y-4">
                        <div v-if="filteredEvents.length === 0" class="text-center py-16">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V8a1 1 0 011-1h3z">
                                </path>
                            </svg>
                            <h3 class="text-xl font-medium text-gray-900 mb-3">No events found</h3>
                            <p class="text-gray-600 mb-6">Try adjusting your filters or create a new event to get
                                started.</p>
                            <button @click="openCreateEventModal"
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                Create Your First Event
                            </button>
                        </div>

                        <div v-for="event in filteredEvents" :key="event.id"
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer"
                            @click="selectedEvent = event; showEventModal = true">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-start space-x-4">
                                    <div class="p-3 rounded-lg text-2xl"
                                        :style="{ backgroundColor: event.color + '20' }">
                                        {{ getEventTypeInfo(event.type).icon }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span
                                                :class="['px-2 py-1 text-xs rounded-full', getStatusColor(event.status)]">
                                                {{ event.status.charAt(0).toUpperCase() + event.status.slice(1) }}
                                            </span>
                                            <!-- <span class="text-xs" :class="getPriorityColor(event.priority)">
                                                {{ event.priority.toUpperCase() }} PRIORITY
                                            </span> -->
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ event.title }}</h3>
                                        <p class="text-sm text-gray-600 mb-2">{{ event.description }}</p>
                                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V8a1 1 0 011-1h3z">
                                                    </path>
                                                </svg>
                                                {{ formatDateTime(event.start) }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ event.location }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <!-- <div class="text-lg font-bold text-gray-900">{{ formatPrice(event.budget) }}</div> -->
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ event.services.length }} service{{ event.services.length !== 1 ? 's' : '' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <!-- <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-600">Tasks Progress</span> -->
                            <!-- <span class="text-sm text-gray-500">
                                        {{event.tasks.filter(t => t.completed).length}}/{{ event.tasks.length }}
                                    </span> -->
                            <!-- </div>
                                <div class="w-full bg-gray-200 rounded-full h-2"> -->
                            <!-- <div class="bg-blue-600 h-2 rounded-full"
                                        :style="{ width: event.tasks.length > 0 ? (event.tasks.filter(t => t.completed).length / event.tasks.length * 100) + '%' : '0%' }">
                                    </div> -->
                            <!-- </div>
                            </div> -->

                            <!-- Services Preview -->
                            <div v-if="event.services.length > 0" class="flex flex-wrap gap-2">
                                <span v-for="service in event.services.slice(0, 3)" :key="service.name"
                                    class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">
                                    {{ service.name }}
                                </span>
                                <span v-if="event.services.length > 3"
                                    class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded">
                                    +{{ event.services.length - 3 }} more
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Upcoming Events -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Upcoming Events
                        </h3>
                        <div class="space-y-3">
                            <div v-for="event in upcomingEvents" :key="event.id"
                                class="p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors"
                                @click="selectedEvent = event; showEventModal = true">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg"
                                        :style="{ backgroundColor: event.color + '20' }">
                                        {{ getEventTypeInfo(event.type).icon }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ event.title }}</p>
                                        <p class="text-xs text-gray-500">{{ formatDate(event.start) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-if="upcomingEvents.length === 0" class="text-center py-4 text-gray-500 text-sm">
                                No upcoming events
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <button @click="openCreateEventModal"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                Create New Event
                            </button>
                            <button
                                class="w-full border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                Browse Services
                            </button>
                            <button
                                class="w-full border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                View Templates
                            </button>
                        </div>
                    </div>

                    <!-- Event Categories -->
                    <!-- <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Categories</h3>
                        <div class="space-y-2">
                            <div v-for="type in eventTypes" :key="type.value"
                                class="flex items-center justify-between p-2 rounded hover:bg-gray-50 cursor-pointer"
                                @click="selectedEventType = type.value; ui.toggleEventCalendarView">
                                <div class="flex items-center space-x-3">
                                    <span class="text-lg">{{ type.icon }}</span>
                                    <span class="text-sm text-gray-700">{{ type.label }}</span>
                                </div>
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                    {{mockEvents.filter(e => e.type === type.value).length}}
                                </span>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

        <!-- Event Detail Modal -->
        <div v-if="showEventModal && selectedEvent"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-start space-x-4">
                            <div class="p-3 rounded-lg text-3xl"
                                :style="{ backgroundColor: selectedEvent.color + '20' }">
                                {{ getEventTypeInfo(selectedEvent.type).icon }}
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ selectedEvent.title }}</h2>
                                <div class="flex items-center space-x-2 mb-2">
                                    <span
                                        :class="['px-3 py-1 text-sm rounded-full', getStatusColor(selectedEvent.status)]">
                                        {{ selectedEvent.status.charAt(0).toUpperCase() + selectedEvent.status.slice(1)
                                        }}
                                    </span>
                                    <!-- <span class="text-sm" :class="getPriorityColor(selectedEvent.priority)">
                                        {{ selectedEvent.priority.toUpperCase() }} PRIORITY
                                    </span> -->
                                </div>
                                <p class="text-gray-600">{{ selectedEvent.description }}</p>
                            </div>
                        </div>
                        <button @click="showEventModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Event Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Basic Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Event Details</h3>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V8a1 1 0 011-1h3z">
                                        </path>
                                    </svg>
                                    <span class="text-gray-600">Start:</span>
                                    <span class="ml-2 font-medium">{{ formatDateTime(selectedEvent.start) }}</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a1 1 0 011 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V8a1 1 0 011-1h3z">
                                        </path>
                                    </svg>
                                    <span class="text-gray-600">End:</span>
                                    <span class="ml-2 font-medium">{{ formatDateTime(selectedEvent.end) }}</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-gray-600">Location:</span>
                                    <span class="ml-2 font-medium">{{ selectedEvent.location }}</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                        </path>
                                    </svg>
                                    <span class="text-gray-600">Budget:</span>
                                    <span class="ml-2 font-medium">{{ formatPrice(selectedEvent.budget) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Services -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3">Booked Services</h3>
                            <div v-if="selectedEvent.services.length > 0" class="space-y-2">
                                <div v-for="service in selectedEvent.services" :key="service.name"
                                    class="flex items-center justify-between p-2 bg-white rounded border">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ service.name }}</p>
                                        <p class="text-xs text-gray-600">{{ service.provider }}</p>
                                    </div>
                                    <span :class="[
                                        'px-2 py-1 text-xs rounded-full',
                                        service.status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                    ]">
                                        {{ service.status }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500 text-center py-4">
                                No services booked yet
                            </div>
                        </div>
                    </div>

                    <!-- Tasks -->
                    <!-- <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3">Tasks & Checklist</h3>
                        <div v-if="selectedEvent.tasks.length > 0" class="space-y-2">
                            <div v-for="(task, index) in selectedEvent.tasks" :key="index"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <input type="checkbox" :checked="task.completed"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span :class="[
                                        'text-sm',
                                        task.completed ? 'line-through text-gray-500' : 'text-gray-900'
                                    ]">
                                        {{ task.task }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">Due: {{ formatDate(task.dueDate) }}</span>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-500 text-center py-4 bg-gray-50 rounded-lg">
                            No tasks added yet
                        </div>
                    </div> -->

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <div class="flex space-x-3">
                            <button
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                Edit Event
                            </button>
                            <button
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                Add Task
                            </button>
                            <button
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                Book Service
                            </button>
                        </div>
                        <button @click="deleteEvent(selectedEvent.id)"
                            class="px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 transition-colors text-sm">
                            Delete Event
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Event Modal -->
        <div v-if="showCreateEventModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Create New Event</h2>
                        <button @click="showCreateEventModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="createEvent" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Event Title -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                                <input v-model="newEvent.title" type="text" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter event title">
                            </div>

                            <!-- Event Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
                                <select v-model="newEvent.type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select event type</option>
                                    <option v-for="type in eventTypes" :key="type.value" :value="type.value">
                                        {{ type.icon }} {{ type.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                                <select v-model="newEvent.priority"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>

                            <!-- Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
                                <input v-model="newEvent.date" type="date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Time -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                    <input v-model="newEvent.startTime" type="time"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                    <input v-model="newEvent.endTime" type="time"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                                <input v-model="newEvent.location" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter event location">
                            </div>

                            <!-- Budget -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Budget (PHP)</label>
                                <input v-model="newEvent.budget" type="number" min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0">
                            </div>

                            <!-- Reminder -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reminder</label>
                                <select v-model="newEvent.reminder"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="none">No reminder</option>
                                    <option value="1hour">1 hour before</option>
                                    <option value="1day">1 day before</option>
                                    <option value="1week">1 week before</option>
                                </select>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea v-model="newEvent.description" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Add event description, notes, or special requirements..."></textarea>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <button type="button" @click="showCreateEventModal = false"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                Create Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
