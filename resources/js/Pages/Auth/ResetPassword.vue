<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Key, Lock } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
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
            <main class="relative z-10 max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
                <!-- Hero Section -->
                <section class="text-center mb-8 sm:mb-12">
                    <div
                        class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 reveal-animation">
                        <Key class="w-8 h-8 text-blue-600" />
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 reveal-animation animation-delay-100">
                        Reset Your Password
                    </h1>
                    <p class="text-gray-700 text-base sm:text-lg reveal-animation animation-delay-200">
                        Create a new secure password for your account
                    </p>
                </section>

                <!-- Form Section -->
                <section
                    class="bg-white/80 backdrop-blur-sm rounded-xl border-2 border-white/60 p-6 shadow-sm reveal-animation animation-delay-300">
                    <form @submit.prevent="submit">
                        <!-- Email Field -->
                        <div class="mb-6">
                            <InputLabel for="email" value="Email" class="text-gray-700 font-medium" />

                            <TextInput id="email" type="email"
                                class="mt-2 block w-full bg-white/70 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg transition-all duration-300"
                                v-model="form.email" required autofocus autocomplete="username" />

                            <InputError class="mt-2 text-sm" :message="form.errors.email" />
                        </div>

                        <!-- Password Field -->
                        <div class="mb-6">
                            <InputLabel for="password" value="New Password" class="text-gray-700 font-medium" />

                            <div class="relative">
                                <TextInput id="password" type="password"
                                    class="mt-2 block w-full bg-white/70 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg transition-all duration-300 pr-10"
                                    v-model="form.password" required autocomplete="new-password" />
                                <Lock
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                            </div>

                            <InputError class="mt-2 text-sm" :message="form.errors.password" />
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="mb-6">
                            <InputLabel for="password_confirmation" value="Confirm New Password"
                                class="text-gray-700 font-medium" />

                            <div class="relative">
                                <TextInput id="password_confirmation" type="password"
                                    class="mt-2 block w-full bg-white/70 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg transition-all duration-300 pr-10"
                                    v-model="form.password_confirmation" required autocomplete="new-password" />
                                <Lock
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                            </div>

                            <InputError class="mt-2 text-sm" :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <PrimaryButton :class="[
                                'w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white transition-all duration-300',
                                form.processing ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
                            ]" :disabled="form.processing">
                                <span v-if="form.processing" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Resetting Password...
                                </span>
                                <span v-else class="flex items-center">
                                    <Key class="w-4 h-4 mr-2" />
                                    Reset Password
                                </span>
                            </PrimaryButton>
                        </div>
                    </form>
                </section>

                <!-- Additional Help Text -->
                <div class="mt-6 text-center reveal-animation animation-delay-500">
                    <p class="text-sm text-gray-600">
                        Remember your password?
                        <Link :href="route('login')"
                            class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                        Back to login
                        </Link>
                    </p>
                </div>
            </main>

            <!-- Footer -->
            <footer
                class="relative z-10 bg-white/50 backdrop-blur-sm border-t border-white/30 mt-12 reveal-animation animation-delay-700">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="text-center text-gray-700 text-sm">
                        <p>&copy; {{ new Date().getFullYear() }} Eventory. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    </GuestLayout>
</template>

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
