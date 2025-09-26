<script setup>

</script>

<template>
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
        <!-- <div class="absolute animate-float-delayed top-1/3 right-1/3 w-5 h-5 bg-purple-300 rounded-full opacity-80">
            </div>
            <div class="absolute animate-float-slow top-2/3 left-1/5 w-10 h-10 bg-pink-400 rounded-full opacity-40">
            </div>
            <div class="absolute animate-float top-1/2 right-1/4 w-6 h-6 bg-blue-300 rounded-full opacity-70"></div>
            <div
                class="absolute animate-float-delayed bottom-1/4 left-2/3 w-5 h-5 bg-purple-400 rounded-full opacity-50">
            </div> -->
    </div>
</template>

<style scoped>
@keyframes float {

    0%,
    100% {
        transform: translateY(0) scale(1);
        opacity: 0.2;
    }

    50% {
        transform: translateY(-20px) scale(1.05);
        opacity: 0.25;
    }
}

@keyframes reveal {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.reveal-animation {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.reveal-animation.animated {
    opacity: 1;
    transform: translateY(0);
}

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


/* BLOB */
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

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 8s ease-in-out infinite;
    animation-delay: 2s;
}

.animate-float-slow {
    animation: float-slow 10s ease-in-out infinite;
    animation-delay: 1s;
}
</style>
