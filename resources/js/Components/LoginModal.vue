<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    canResetPassword: {
        type: Boolean,
        default: false
    },
    status: {
        type: String,
        default: ''
    },
});

const show = ref(false);
const emit = defineEmits(['close']);
const passwordVisible = ref(false);

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

const closeModal = () => {
    show.value = false;
    emit('close');
};

defineExpose({
    show: () => show.value = true
});
</script>

<template>
    <!-- Overlay -->
    <transition name="fade">
        <Teleport to="body">
            <div v-if="show" @click.self="closeModal"
                class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <!-- Modal Container -->
                <transition name="slide-up">
                    <div
                        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-200">
                        <!-- Close Button -->
                        <button @click="closeModal"
                            class="absolute top-4 right-4 p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 transition-colors duration-200 z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Modal Content -->
                        <div class="p-8">

                            <Head title="Log in" />

                            <!-- Logo -->
                            <div class="flex justify-center mb-8">
                                <span class="text-3xl font-bold text-gray-900">Eventory</span>
                            </div>

                            <!-- Status Message -->
                            <div v-if="status" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm">
                                <p class="font-medium text-blue-700">{{ status }}</p>
                            </div>

                            <div class="text-center mb-8">
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                                <p class="text-gray-600">Sign in to continue to your account</p>
                            </div>

                            <form @submit.prevent="submit" class="space-y-6">
                                <!-- Email Field -->
                                <div class="space-y-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email
                                        address</label>
                                    <div class="relative">
                                        <input id="email" type="email" v-model="form.email" required autofocus
                                            autocomplete="username"
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
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
                                <div class="space-y-2">
                                    <label for="password"
                                        class="block text-sm font-medium text-gray-700">Password</label>
                                    <div class="relative">
                                        <input id="password" :type="passwordVisible ? 'text' : 'password'"
                                            v-model="form.password" required autocomplete="current-password"
                                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                            placeholder="••••••••" />
                                        <button type="button" @click="passwordVisible = !passwordVisible"
                                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path v-if="!passwordVisible" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="1.5"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                <path v-if="passwordVisible" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="1.5"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password
                                    }}
                                    </p>
                                </div>

                                <!-- Remember Me & Forgot Password -->
                                <div class="flex items-center justify-between">
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
                                <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:bg-gray-400 disabled:cursor-not-allowed"
                                    :disabled="form.processing">
                                    <span v-if="!form.processing" class="flex items-center justify-center">
                                        <span>Sign in</span>
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

                            <!-- Sign Up Link -->
                            <div class="mt-6 text-center text-sm text-gray-600">
                                Don't have an account?
                                <Link :href="route('register')"
                                    class="font-semibold text-blue-600 hover:text-blue-700 transition-colors duration-200 ml-1">
                                Create one now
                                </Link>
                            </div>
                        </div>
                    </div>
                </transition>
            </div>
        </Teleport>
    </transition>
</template>

<style scoped>
/* Fade animation for overlay */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Slide up animation for modal content */
.slide-up-enter-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-up-leave-active {
    transition: all 0.2s cubic-bezier(0.4, 0, 1, 1);
}

.slide-up-enter-from {
    opacity: 0;
    transform: translateY(16px) scale(0.98);
}

.slide-up-leave-to {
    opacity: 0;
    transform: translateY(-8px) scale(0.98);
}

.slide-up-enter-to {
    opacity: 1;
    transform: translateY(0) scale(1);
}
</style>
