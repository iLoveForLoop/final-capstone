<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

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
</script>

<template>
    <div class="min-h-screen bg-slate-950 flex items-center justify-center p-4 relative overflow-hidden">

        <Head title="Log in to Eventory" />

        <!-- Animated Background -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950"></div>
            <div
                class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob">
            </div>
            <div
                class="absolute top-0 -right-4 w-72 h-72 bg-cyan-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000">
            </div>
        </div>

        <!-- Main Container -->
        <div class="relative z-10 w-full max-w-6xl mx-auto grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">

            <!-- Left Side - Branding -->
            <div class="text-center lg:text-left space-y-8">
                <!-- Logo -->
                <div class="flex items-center justify-center lg:justify-start">
                    <div class="relative">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-cyan-400 to-purple-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-purple-500/25">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14l-5-5 1.41-1.41L12 14.17l7.59-7.59L20.41 8 12 17z" />
                            </svg>
                        </div>
                        <div
                            class="absolute -inset-1 bg-gradient-to-br from-cyan-400 to-purple-600 rounded-2xl blur opacity-30 animate-pulse">
                        </div>
                    </div>
                    <span
                        class="ml-4 text-3xl font-bold bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">Eventory</span>
                </div>

                <div class="space-y-6">
                    <h1 class="text-4xl lg:text-6xl font-bold leading-tight">
                        <span
                            class="bg-gradient-to-r from-white via-gray-100 to-gray-300 bg-clip-text text-transparent">Plan
                            Events</span><br />
                        <span
                            class="bg-gradient-to-r from-cyan-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">Like
                            a Pro</span>
                    </h1>

                    <p class="text-xl text-gray-400 leading-relaxed max-w-lg">
                        Connect with the best vendors in your area. From catering to photography, we've got your event
                        covered.
                    </p>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-8 pt-8">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-white">10K+</div>
                        <div class="text-sm text-gray-500">Vendors</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-white">50K+</div>
                        <div class="text-sm text-gray-500">Events</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-white">99%</div>
                        <div class="text-sm text-gray-500">Satisfaction</div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="w-full max-w-md mx-auto lg:mx-0">
                <!-- Glass Card -->
                <div class="backdrop-blur-xl bg-white/10 rounded-3xl p-8 shadow-2xl border border-white/20">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-white mb-2">Welcome back</h2>
                        <p class="text-gray-400">Sign in to continue your journey</p>
                    </div>

                    <!-- Status Message -->
                    <div v-if="status"
                        class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/30 rounded-2xl backdrop-blur-sm">
                        <p class="text-sm font-medium text-emerald-300">{{ status }}</p>
                    </div>

                    <!-- Login Form -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-300">
                                Email address
                            </label>
                            <div class="relative group">
                                <input id="email" type="email" v-model="form.email" required autofocus
                                    autocomplete="username"
                                    class="w-full px-4 py-4 bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl text-white placeholder-gray-500 focus:ring-2 focus:ring-cyan-400/50 focus:border-cyan-400/50 transition-all duration-300 group-hover:border-white/20"
                                    placeholder="you@example.com" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <p v-if="form.errors.email" class="text-sm text-red-400">{{ form.errors.email }}</p>
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-300">
                                Password
                            </label>
                            <div class="relative group">
                                <input id="password" type="password" v-model="form.password" required
                                    autocomplete="current-password"
                                    class="w-full px-4 py-4 bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl text-white placeholder-gray-500 focus:ring-2 focus:ring-cyan-400/50 focus:border-cyan-400/50 transition-all duration-300 group-hover:border-white/20"
                                    placeholder="••••••••" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <p v-if="form.errors.password" class="text-sm text-red-400">{{ form.errors.password }}</p>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.remember"
                                    class="w-4 h-4 text-cyan-400 bg-white/5 border-white/20 rounded focus:ring-cyan-400/50 focus:ring-2" />
                                <span class="ml-2 text-sm text-gray-400">Remember me</span>
                            </label>

                            <Link v-if="canResetPassword" :href="route('password.request')"
                                class="text-sm font-medium text-cyan-400 hover:text-cyan-300 transition-colors duration-200">
                            Forgot password?
                            </Link>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" :disabled="form.processing"
                            class="w-full bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-cyan-400 hover:to-purple-500 disabled:from-gray-600 disabled:to-gray-700 text-white font-semibold py-4 px-6 rounded-2xl transition-all duration-300 focus:ring-2 focus:ring-cyan-400/50 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:cursor-not-allowed transform hover:scale-105 active:scale-95 shadow-lg shadow-cyan-500/25">
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
                    <p class="mt-6 text-center text-sm text-gray-400">
                        Don't have an account?
                        <Link :href="route('register')"
                            class="font-semibold text-cyan-400 hover:text-cyan-300 transition-colors duration-200">
                        Create one now
                        </Link>
                    </p>
                </div>
            </div>
        </div>


    </div>
</template>

<!-- Custom CSS for animations -->
<style>
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
</style>
