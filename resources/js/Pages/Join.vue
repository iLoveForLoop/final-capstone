<template>
    <GuestLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-300 via-white to-gray-500 relative overflow-hidden">
            <!-- Animated Background -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
                <div
                    class="absolute -top-40 -right-32 w-80 h-80 bg-blue-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000">
                </div>
                <div
                    class="absolute top-60 -left-20 w-80 h-80 bg-purple-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000">
                </div>
                <div
                    class="absolute -bottom-20 left-40 w-80 h-80 bg-teal-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob">
                </div>
            </div>

            <div class="absolute inset-0">
                <div
                    class="absolute top-1/4 left-3/4 w-52 h-52 bg-teal-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000">
                </div>
            </div>

            <!-- Simple Back Button Header -->
            <header class="relative z-10">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-start py-6">
                        <Link href="/"
                            class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 transition-all duration-200 px-4 py-2 rounded-lg hover:bg-white/50 backdrop-blur-sm text-sm reveal-animation">
                        <ArrowLeft class="w-4 h-4" />
                        <span>Back to Home</span>
                        </Link>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
                <!-- Hero Section -->
                <section class="text-center mb-12 sm:mb-16">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 reveal-animation animation-delay-100">
                        Join Eventory as
                    </h1>
                    <p class="text-gray-700 max-w-md mx-auto text-base sm:text-lg reveal-animation animation-delay-200">
                        Choose your role to get started with your event planning journey
                    </p>
                </section>

                <!-- Role Selection Cards -->
                <section class="grid md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                    <!-- Event Organizer Card -->
                    <div class="group reveal-animation animation-delay-300">
                        <div :class="[
                            'relative bg-white/80 backdrop-blur-sm rounded-xl border-2 p-6 transition-all duration-300 h-full flex flex-col',
                            selectedRole === 'organizer'
                                ? 'border-blue-500 shadow-lg'
                                : 'border-white/60 hover:border-blue-300 hover:shadow-md'
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
                                <p class="text-gray-700 text-sm leading-relaxed mb-4">
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
                                        <span class="text-xs text-gray-700">{{ feature }}</span>
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
                            <!-- <div :class="[
                                'absolute top-4 right-4 w-5 h-5 rounded-full border-2 transition-all duration-300 flex items-center justify-center reveal-animation animation-delay-500',
                                selectedRole === 'organizer'
                                    ? 'border-blue-500 bg-blue-500'
                                    : 'border-gray-300 group-hover:border-blue-300'
                            ]">
                                <Check v-if="selectedRole === 'organizer'" class="w-3 h-3 text-white" />
                            </div> -->
                        </div>
                    </div>

                    <!-- Vendor Card -->
                    <div class="group reveal-animation animation-delay-400">
                        <div :class="[
                            'relative bg-white/80 backdrop-blur-sm rounded-xl border-2 p-6 transition-all duration-300 h-full flex flex-col',
                            selectedRole === 'vendor'
                                ? 'border-purple-500 shadow-lg'
                                : 'border-white/60 hover:border-purple-300 hover:shadow-md'
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
                                <p class="text-gray-700 text-sm leading-relaxed mb-4">
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
                                        <span class="text-xs text-gray-700">{{ feature }}</span>
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
                            <!-- <div :class="[
                                'absolute top-4 right-4 w-5 h-5 rounded-full border-2 transition-all duration-300 flex items-center justify-center reveal-animation animation-delay-600',
                                selectedRole === 'vendor'
                                    ? 'border-purple-500 bg-purple-500'
                                    : 'border-gray-300 group-hover:border-purple-300'
                            ]">
                                <Check v-if="selectedRole === 'vendor'" class="w-3 h-3 text-white" />
                            </div> -->
                        </div>
                    </div>
                </section>
            </main>

            <!-- Footer -->
            <footer
                class="relative z-10 bg-white/50 backdrop-blur-sm border-t border-white/30 mt-12 reveal-animation animation-delay-900">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="text-center text-gray-700 text-sm">
                        <p>&copy; {{ new Date().getFullYear() }} Eventory. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    </GuestLayout>

</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import {
    ArrowLeft,
    Users,
    Store,
    Check,
    ArrowRight
} from 'lucide-vue-next';
import GuestLayout from '@/Layouts/GuestLayout.vue';

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
</script>

<style scoped>
/* BLOB Animation */
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }

    33% {
        transform: translate(30px, -50px) scale(1.1);
    }

    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }

    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Reveal Animation */
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

.animation-delay-900 {
    transition-delay: 900ms;
}

/* Smooth transitions */
* {
    transition-property: color, background-color, border-color, box-shadow;
    transition-duration: 300ms;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Performance optimizations */
.reveal-animation {
    will-change: transform, opacity;
}
</style>
