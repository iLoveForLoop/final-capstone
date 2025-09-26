<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center space-x-3 group reveal-animation">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                        <Calendar class="w-4 h-4 text-white" />
                    </div>
                    <span class="text-xl font-semibold text-gray-900">
                        Eventory
                    </span>
                    </Link>

                    <!-- Back to Login -->
                    <Link href="/login"
                        class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 transition-all duration-200 px-3 py-2 rounded-md hover:bg-gray-50 text-sm reveal-animation animation-delay-100">
                    <ArrowLeft class="w-4 h-4" />
                    <span class="hidden sm:inline">Back to Login</span>
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <!-- Hero Section -->
            <section class="text-center mb-12 sm:mb-16">
                <!-- <div
                    class="w-16 h-16 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-6 reveal-animation">
                    <Users class="w-8 h-8 text-purple-600" />
                </div> -->
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 reveal-animation animation-delay-100">
                    Join Eventory as
                </h1>
                <p class="text-gray-600 max-w-md mx-auto text-base sm:text-lg reveal-animation animation-delay-200">
                    Choose your role to get started with your event planning journey
                </p>
            </section>

            <!-- Role Selection Cards -->
            <section class="grid md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                <!-- Event Organizer Card -->
                <div class="group reveal-animation animation-delay-300">
                    <div :class="[
                        'relative bg-white rounded-xl border-2 p-6 transition-all duration-300 h-full flex flex-col',
                        selectedRole === 'organizer'
                            ? 'border-blue-500 shadow-lg'
                            : 'border-gray-200 hover:border-blue-300 hover:shadow-md'
                    ]">
                        <!-- Icon -->
                        <div :class="[
                            'w-12 h-12 rounded-xl flex items-center justify-center mb-4 transition-colors duration-300',
                            selectedRole === 'organizer'
                                ? 'bg-blue-500'
                                : 'bg-blue-100 group-hover:bg-blue-200'
                        ]">
                            <Users :class="[
                                'w-6 h-6 transition-colors duration-300',
                                selectedRole === 'organizer' ? 'text-white' : 'text-blue-600'
                            ]" />
                        </div>

                        <!-- Content -->
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Event Organizer</h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                Plan and manage events. Find trusted vendors and create unforgettable experiences.
                            </p>

                            <!-- Key Features -->
                            <div class="space-y-2 mb-6">
                                <div v-for="(feature, index) in organizerFeatures" :key="feature"
                                    class="flex items-center space-x-2 reveal-animation"
                                    :style="{ animationDelay: `${index * 100}ms` }">
                                    <div :class="[
                                        'w-4 h-4 rounded-full flex items-center justify-center transition-colors duration-300',
                                        selectedRole === 'organizer' ? 'bg-blue-100' : 'bg-gray-100'
                                    ]">
                                        <Check class="w-2 h-2 text-blue-600" />
                                    </div>
                                    <span class="text-xs text-gray-600">{{ feature }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Button -->
                        <Link href="/register?type=organizer" @click="selectRole('organizer')" :class="[
                            'w-full py-2.5 px-4 rounded-lg font-medium text-sm transition-all duration-300 text-center border-2 reveal-animation animation-delay-400',
                            selectedRole === 'organizer'
                                ? 'bg-blue-50 border-blue-200 text-blue-700 hover:bg-blue-100 hover:border-blue-300'
                                : 'bg-white border-gray-200 text-gray-700 hover:border-blue-300 hover:text-blue-700'
                        ]">
                        <span class="flex items-center justify-center space-x-2">
                            <span>Continue as Organizer</span>
                            <ArrowRight class="w-4 h-4" />
                        </span>
                        </Link>

                        <!-- Selection Indicator -->
                        <div :class="[
                            'absolute top-4 right-4 w-5 h-5 rounded-full border-2 transition-all duration-300 flex items-center justify-center reveal-animation animation-delay-500',
                            selectedRole === 'organizer'
                                ? 'border-blue-500 bg-blue-500'
                                : 'border-gray-300 group-hover:border-blue-300'
                        ]">
                            <Check v-if="selectedRole === 'organizer'" class="w-3 h-3 text-white" />
                        </div>
                    </div>
                </div>

                <!-- Vendor Card -->
                <div class="group reveal-animation animation-delay-400">
                    <div :class="[
                        'relative bg-white rounded-xl border-2 p-6 transition-all duration-300 h-full flex flex-col',
                        selectedRole === 'vendor'
                            ? 'border-purple-500 shadow-lg'
                            : 'border-gray-200 hover:border-purple-300 hover:shadow-md'
                    ]">
                        <!-- Icon -->
                        <div :class="[
                            'w-12 h-12 rounded-xl flex items-center justify-center mb-4 transition-colors duration-300',
                            selectedRole === 'vendor'
                                ? 'bg-purple-500'
                                : 'bg-purple-100 group-hover:bg-purple-200'
                        ]">
                            <Store :class="[
                                'w-6 h-6 transition-colors duration-300',
                                selectedRole === 'vendor' ? 'text-white' : 'text-purple-600'
                            ]" />
                        </div>

                        <!-- Content -->
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Service Vendor</h3>
                            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                Showcase your services. Connect with event organizers and grow your business.
                            </p>

                            <!-- Key Features -->
                            <div class="space-y-2 mb-6">
                                <div v-for="(feature, index) in vendorFeatures" :key="feature"
                                    class="flex items-center space-x-2 reveal-animation"
                                    :style="{ animationDelay: `${index * 100 + 200}ms` }">
                                    <div :class="[
                                        'w-4 h-4 rounded-full flex items-center justify-center transition-colors duration-300',
                                        selectedRole === 'vendor' ? 'bg-purple-100' : 'bg-gray-100'
                                    ]">
                                        <Check class="w-2 h-2 text-purple-600" />
                                    </div>
                                    <span class="text-xs text-gray-600">{{ feature }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Button -->
                        <Link href="/register?type=vendor" @click="selectRole('vendor')" :class="[
                            'w-full py-2.5 px-4 rounded-lg font-medium text-sm transition-all duration-300 text-center border-2 reveal-animation animation-delay-500',
                            selectedRole === 'vendor'
                                ? 'bg-purple-50 border-purple-200 text-purple-700 hover:bg-purple-100 hover:border-purple-300'
                                : 'bg-white border-gray-200 text-gray-700 hover:border-purple-300 hover:text-purple-700'
                        ]">
                        <span class="flex items-center justify-center space-x-2">
                            <span>Continue as Vendor</span>
                            <ArrowRight class="w-4 h-4" />
                        </span>
                        </Link>

                        <!-- Selection Indicator -->
                        <div :class="[
                            'absolute top-4 right-4 w-5 h-5 rounded-full border-2 transition-all duration-300 flex items-center justify-center reveal-animation animation-delay-600',
                            selectedRole === 'vendor'
                                ? 'border-purple-500 bg-purple-500'
                                : 'border-gray-300 group-hover:border-purple-300'
                        ]">
                            <Check v-if="selectedRole === 'vendor'" class="w-3 h-3 text-white" />
                        </div>
                    </div>
                </div>
            </section>


            <!-- Quick Stats -->
            <!-- <section class="max-w-2xl mx-auto mt-12 reveal-animation animation-delay-800">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div v-for="(stat, index) in stats" :key="stat.label"
                        :class="['bg-white rounded-lg p-4 shadow-sm border border-gray-100 reveal-animation']"
                        :style="{ animationDelay: `${index * 150 + 900}ms` }">
                        <div class="text-lg font-bold text-gray-900">{{ stat.value }}</div>
                        <div class="text-xs text-gray-600">{{ stat.label }}</div>
                    </div>
                </div>
            </section> -->
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12 reveal-animation animation-delay-900">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="text-center text-gray-600 text-sm">
                    <p>&copy; {{ new Date().getFullYear() }} Eventory. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import {
    Calendar,
    ArrowLeft,
    Users,
    Store,
    Check,
    ArrowRight
} from 'lucide-vue-next';

const selectedRole = ref(null);

// Animation observer
const animatedElements = ref([]);

onMounted(() => {
    animatedElements.value = document.querySelectorAll('.reveal-animation');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    animatedElements.value.forEach(element => {
        observer.observe(element);
    });
});

const selectRole = (role) => {
    selectedRole.value = role;
};

const organizerFeatures = [
    'Find trusted vendors',
    'Manage event details',
    'Real-time coordination'
];

const vendorFeatures = [
    'Showcase portfolio',
    'Get booking requests',
    'Manage availability'
];

const stats = [
    { value: '1,000+', label: 'Vendors' },
    { value: '5,000+', label: 'Events' },
    { value: '98%', label: 'Satisfaction' }
];
</script>

<style scoped>
/* Smooth transitions */
* {
    transition-property: color, background-color, border-color, box-shadow;
    transition-duration: 300ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Reveal Animation - Based on your existing code */
.reveal-animation {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.reveal-animation.animated {
    opacity: 1;
    transform: translateY(0);
}

/* Animation Delays */
.animation-delay-100 {
    transition-delay: 100ms;
}

.animation-delay-200 {
    transition-delay: 200ms;
}

.animation-delay-300 {
    transition-delay: 300ms;
}

.animation-delay-400 {
    transition-delay: 400ms;
}

.animation-delay-500 {
    transition-delay: 500ms;
}

.animation-delay-600 {
    transition-delay: 600ms;
}

.animation-delay-700 {
    transition-delay: 700ms;
}

.animation-delay-800 {
    transition-delay: 800ms;
}

.animation-delay-900 {
    transition-delay: 900ms;
}

/* Keep only essential hover effects */
.group:hover .group-hover\:translate-x-1 {
    transform: translateX(0.25rem);
}

/* Performance optimizations */
.reveal-animation {
    will-change: transform, opacity;
}
</style>
