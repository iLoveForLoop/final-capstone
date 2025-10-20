<template>
    <GuestLayout>
        <div
            class="min-h-screen bg-gradient-to-br from-gray-300 via-white to-gray-500 flex items-center justify-center p-4 relative overflow-hidden">

            <Head title="Log in to Eventory" />

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

            <!-- Main Content -->
            <div class="relative z-10 w-full max-w-6xl mx-auto grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">

                <!-- Left Side - Branding -->
                <div class="text-center lg:text-left space-y-8">
                    <!-- Logo -->
                    <div class="flex items-center justify-center lg:justify-start reveal-animation animation-delay-100">
                        <span class="text-3xl font-bold text-gray-900">Eventory</span>
                    </div>

                    <div class="space-y-6">
                        <h1
                            class="text-4xl lg:text-5xl font-bold leading-tight text-gray-900 reveal-animation animation-delay-200">
                            Plan Events<br />
                            <span class="text-blue-600">Like a Pro</span>
                        </h1>

                        <p class="text-lg text-gray-700 leading-relaxed max-w-lg reveal-animation animation-delay-300">
                            Connect with the best vendors in your area. From catering to photography, we've got your
                            event
                            covered.
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-8 pt-8">
                        <div class="text-center reveal-animation animation-delay-400">
                            <div class="text-2xl font-bold text-gray-900">10K+</div>
                            <div class="text-sm text-gray-600">Vendors</div>
                        </div>
                        <div class="text-center reveal-animation animation-delay-500">
                            <div class="text-2xl font-bold text-gray-900">50K+</div>
                            <div class="text-sm text-gray-600">Events</div>
                        </div>
                        <div class="text-center reveal-animation animation-delay-600">
                            <div class="text-2xl font-bold text-gray-900">99%</div>
                            <div class="text-sm text-gray-600">Satisfaction</div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="w-full max-w-md mx-auto lg:mx-0">
                    <!-- Card -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-xl border border-white/60">
                        <div class="text-center mb-8 reveal-animation animation-delay-300">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome back</h2>
                            <p class="text-gray-600">Sign in to continue your journey</p>
                        </div>

                        <!-- Status Message -->
                        <div v-if="status"
                            class="mb-6 p-4 bg-blue-500/20 border border-blue-500/30 rounded-xl reveal-animation animation-delay-400">
                            <p class="text-sm font-medium text-blue-700">{{ status }}</p>
                        </div>

                        <!-- Login Form -->
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Email Field -->
                            <div class="space-y-2 reveal-animation animation-delay-500">
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email address
                                </label>
                                <div class="relative">
                                    <input id="email" type="email" v-model="form.email" required autofocus
                                        autocomplete="username"
                                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                                        placeholder="you@example.com" />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
                            </div>

                            <!-- Password Field -->
                            <div class="space-y-2 reveal-animation animation-delay-600">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Password
                                </label>
                                <div class="relative">
                                    <input id="password" type="password" v-model="form.password" required
                                        autocomplete="current-password"
                                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                                        placeholder="••••••••" />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}
                                </p>
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between reveal-animation animation-delay-700">
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="form.remember"
                                        class="w-4 h-4 text-blue-500 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2" />
                                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                                </label>

                                <Link v-if="canResetPassword" :href="route('password.request')"
                                    class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors duration-200">
                                Forgot password?
                                </Link>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" :disabled="form.processing"
                                class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed reveal-animation animation-delay-800">
                                <span v-if="!form.processing" class="flex items-center justify-center">
                                    <span>Sign in to Eventory</span>
                                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </span>
                                <div v-else class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Signing in...
                                </div>
                            </button>
                        </form>

                        <!-- Sign up link -->
                        <p class="mt-6 text-center text-sm text-gray-600 reveal-animation animation-delay-900">
                            Don't have an account?
                            <Link :href="route('register')"
                                class="font-semibold text-blue-600 hover:text-blue-700 transition-colors duration-200">
                            Create one now
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>

</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

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

.animation-delay-700 {
    transition-delay: 700ms;
}

.animation-delay-800 {
    transition-delay: 800ms;
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
