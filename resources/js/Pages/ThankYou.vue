<template>
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
            <!-- Thank You Section -->
            <section class="text-center mb-12 sm:mb-16">
                <div class="flex justify-center mb-6 reveal-animation animation-delay-100">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center">
                        <CheckCircle class="w-10 h-10 text-purple-600" />
                    </div>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 reveal-animation animation-delay-200">
                    Thank You for Registering!
                </h1>
                <p class="text-gray-700 max-w-md mx-auto text-base sm:text-lg reveal-animation animation-delay-300">
                    Your vendor registration request has been successfully submitted.
                </p>
            </section>

            <!-- Status Card -->
            <section class="max-w-2xl mx-auto mb-8 reveal-animation animation-delay-400">
                <div class="bg-white/80 backdrop-blur-sm rounded-xl border-2 border-purple-200 p-6 shadow-md">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <Clock class="w-5 h-5 text-purple-600" />
                            </div>
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Pending Admin Approval</h3>
                            <p class="text-gray-700 text-sm leading-relaxed mb-4">
                                Your registration is currently under review by our admin team. This process typically
                                takes 1-2 business days.
                            </p>
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                <p class="text-purple-700 text-sm">
                                    <span class="font-medium">What happens next?</span> Once approved, you'll receive a
                                    confirmation email and gain full access to your vendor dashboard.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Next Steps -->
            <section class="max-w-2xl mx-auto mb-8 reveal-animation animation-delay-500">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 text-center">What You Can Do Now</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <Mail class="w-4 h-4 text-blue-600" />
                            </div>
                            <h3 class="font-medium text-gray-900">Check Your Email</h3>
                        </div>
                        <p class="text-gray-700 text-sm">We've sent a confirmation to your email with your registration
                            details.</p>
                    </div>
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <User class="w-4 h-4 text-green-600" />
                            </div>
                            <h3 class="font-medium text-gray-900">Complete Your Profile</h3>
                        </div>
                        <p class="text-gray-700 text-sm">Once approved, you can add photos, services, and business
                            details.</p>
                    </div>
                </div>
            </section>

            <!-- Contact Support -->
            <section class="max-w-2xl mx-auto text-center reveal-animation animation-delay-700">
                <p class="text-gray-700 text-sm mb-4">
                    Have questions about your registration?
                </p>
                <Link href="/contact"
                    class="inline-flex items-center space-x-2 text-purple-600 hover:text-purple-700 font-medium text-sm transition-colors duration-200">
                <MessageCircle class="w-4 h-4" />
                <span>Contact Support</span>
                </Link>
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
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import {
    ArrowLeft,
    CheckCircle,
    Clock,
    Mail,
    User,
    MessageCircle
} from 'lucide-vue-next';

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
