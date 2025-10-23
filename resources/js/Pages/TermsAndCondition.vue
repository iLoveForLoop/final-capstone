<script setup>
import { ref, onMounted, nextTick } from 'vue'

const activeTab = ref<'terms' | 'privacy'>('terms')

// Read tab from query (?tab=terms|privacy)
const qp = new URLSearchParams(window.location.search)
const initial = qp.get('tab')
if (initial === 'privacy' || initial === 'terms') activeTab.value = initial

onMounted(async () => {
    await nextTick()
    if (location.hash) {
        const el = document.querySelector(location.hash)
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
})

const termsSections = [
    { id: 't-i', num: 'I.', title: 'Acceptance of Terms' },
    { id: 't-ii', num: 'II.', title: 'User Accounts & Eligibility' },
    { id: 't-iii', num: 'III.', title: 'Booking & Transactions' },
    { id: 't-iv', num: 'IV.', title: 'Fees, Payments & Taxes' },
    { id: 't-v', num: 'V.', title: 'Content Ownership & Licenses' },
    { id: 't-vi', num: 'VI.', title: 'Acceptable Use & Prohibited Conduct' },
    { id: 't-vii', num: 'VII.', title: 'Disclaimers & Limitation of Liability' },
    { id: 't-viii', num: 'VIII.', title: 'Indemnification' },
    { id: 't-ix', num: 'IX.', title: 'Termination & Suspension' },
    { id: 't-x', num: 'X.', title: 'Governing Law & Dispute Resolution' },
    { id: 't-xi', num: 'XI.', title: 'Changes to These Terms' },
    { id: 't-xii', num: 'XII.', title: 'Contact Information' },
]

const privacySections = [
    { id: 'p-i', num: 'I.', title: 'Overview & Scope' },
    { id: 'p-ii', num: 'II.', title: 'Information We Collect' },
    { id: 'p-iii', num: 'III.', title: 'How We Use Information' },
    { id: 'p-iv', num: 'IV.', title: 'Sharing & Disclosure' },
    { id: 'p-v', num: 'V.', title: 'Cookies & Similar Technologies' },
    { id: 'p-vi', num: 'VI.', title: 'Data Retention' },
    { id: 'p-vii', num: 'VII.', title: 'Your Rights & Choices' },
    { id: 'p-viii', num: 'VIII.', title: 'Security Measures' },
    { id: 'p-ix', num: 'IX.', title: 'International Transfers' },
    { id: 'p-x', num: 'X.', title: 'Children’s Privacy' },
    { id: 'p-xi', num: 'XI.', title: 'Changes to This Policy' },
    { id: 'p-xii', num: 'XII.', title: 'Contact Information' },
]

// Smooth-scroll to hash if present
onMounted(async () => {
    await nextTick()
    if (location.hash) {
        const el = document.querySelector(location.hash)
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
})
</script>

<template>
    <div class="relative min-h-screen bg-white overflow-hidden">
        <!-- Animated Gold Blobs Background -->
        <div aria-hidden="true">
            <!-- big ambient gradient layer for depth -->
            <div class="pointer-events-none absolute inset-0 opacity-90">
                <div
                    class="absolute -top-32 -left-32 w-[44rem] h-[44rem] rounded-full bg-gradient-to-br from-yellow-300 via-amber-300 to-yellow-500 blur-3xl mix-blend-multiply animate-blob-slow" />
                <div
                    class="absolute -bottom-24 -right-24 w-[40rem] h-[40rem] rounded-full bg-gradient-to-tr from-amber-200 via-yellow-300 to-amber-400 blur-3xl mix-blend-multiply animate-blob" />
                <div
                    class="absolute top-1/3 right-1/3 w-[30rem] h-[30rem] rounded-full bg-gradient-to-b from-yellow-200 to-amber-300 blur-3xl mix-blend-multiply animate-blob-delayed" />
            </div>
            <!-- subtle particles -->
            <div class="pointer-events-none absolute inset-0 opacity-30">
                <div class="gold-sparkles" />
            </div>
        </div>

        <!-- Content Container -->
        <main class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
            <!-- Header -->
            <header class="mx-auto max-w-5xl text-center mb-10">
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-900">
                    Eventory Policies
                </h1>
                <p class="mt-3 text-gray-600">
                    Read our <span class="font-semibold">Terms & Conditions</span> and <span
                        class="font-semibold">Privacy Policy</span>. Paste your content under each labeled section.
                </p>
            </header>

            <!-- Tabs -->
            <div class="mx-auto max-w-5xl mb-6">
                <div class="inline-flex rounded-2xl border border-gray-200 bg-white/80 backdrop-blur p-1 shadow-sm">
                    <button class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all" :class="activeTab === 'terms'
                        ? 'bg-gray-900 text-white shadow'
                        : 'text-gray-700 hover:bg-gray-100'" @click="activeTab = 'terms'">
                        Terms & Conditions
                    </button>
                    <button class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all" :class="activeTab === 'privacy'
                        ? 'bg-gray-900 text-white shadow'
                        : 'text-gray-700 hover:bg-gray-100'" @click="activeTab = 'privacy'">
                        Privacy Policy
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="grid grid-cols-1 lg:grid-cols-[260px,1fr] gap-6 items-start">
                <!-- Sticky TOC -->
                <aside class="lg:sticky lg:top-6">
                    <div class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wider text-gray-500 mb-3">Table of Contents</p>
                        <nav class="space-y-1 text-sm">
                            <template v-if="activeTab === 'terms'">
                                <a v-for="s in termsSections" :key="s.id" :href="'#' + s.id"
                                    class="block rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100">
                                    <span class="font-semibold mr-1">{{ s.num }}</span>{{ s.title }}
                                </a>
                            </template>
                            <template v-else>
                                <a v-for="s in privacySections" :key="s.id" :href="'#' + s.id"
                                    class="block rounded-lg px-3 py-2 text-gray-700 hover:bg-gray-100">
                                    <span class="font-semibold mr-1">{{ s.num }}</span>{{ s.title }}
                                </a>
                            </template>
                        </nav>
                    </div>
                </aside>

                <!-- Sections Surface -->
                <section class="rounded-3xl border border-gray-200 bg-white/90 backdrop-blur-xl shadow-xl">
                    <div class="p-6 sm:p-10">
                        <!-- TERMS -->
                        <div v-show="activeTab === 'terms'" class="space-y-10">
                            <div v-for="s in termsSections" :key="s.id" :id="s.id" class="group">
                                <header class="flex items-baseline gap-3">
                                    <div class="shrink-0 text-lg font-black text-yellow-600">{{ s.num }}</div>
                                    <h2 class="text-2xl font-bold text-gray-900">
                                        {{ s.title }}
                                    </h2>
                                </header>
                                <div class="mt-3 rounded-xl border border-gray-100 bg-white p-4 sm:p-5">
                                    <!-- Paste your TERMS content for {{ s.title }} below -->
                                    <p class="text-gray-600 italic">
                                        [Write your content for <span class="font-medium not-italic">{{ s.title
                                            }}</span> here.]
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- PRIVACY -->
                        <div v-show="activeTab === 'privacy'" class="space-y-10">
                            <div v-for="s in privacySections" :key="s.id" :id="s.id" class="group">
                                <header class="flex items-baseline gap-3">
                                    <div class="shrink-0 text-lg font-black text-yellow-600">{{ s.num }}</div>
                                    <h2 class="text-2xl font-bold text-gray-900">
                                        {{ s.title }}
                                    </h2>
                                </header>
                                <div class="mt-3 rounded-xl border border-gray-100 bg-white p-4 sm:p-5">
                                    <!-- Paste your PRIVACY content for {{ s.title }} below -->
                                    <p class="text-gray-600 italic">
                                        [Write your content for <span class="font-medium not-italic">{{ s.title
                                            }}</span> here.]
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Last updated (optional; edit manually) -->
                        <div class="mt-12 pt-6 border-t border-gray-100 text-sm text-gray-500">
                            Last updated: <span class="font-medium">October 21, 2025</span>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</template>

<style scoped>
/* Smooth anchor scrolling */
:root {
    scroll-behavior: smooth;
}

/* Gold blob animations */
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }

    33% {
        transform: translate(20px, -30px) scale(1.08);
    }

    66% {
        transform: translate(-25px, 20px) scale(0.95);
    }

    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

@keyframes blobSlow {
    0% {
        transform: translate(0px, 0px) scale(1);
    }

    33% {
        transform: translate(-40px, 25px) scale(1.06);
    }

    66% {
        transform: translate(30px, -15px) scale(0.98);
    }

    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

@keyframes blobDelayed {
    0% {
        transform: translate(0px, 0px) scale(1);
    }

    33% {
        transform: translate(25px, 10px) scale(1.05);
    }

    66% {
        transform: translate(-15px, -25px) scale(0.97);
    }

    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 14s infinite ease-in-out;
}

.animate-blob-slow {
    animation: blobSlow 22s infinite ease-in-out;
}

.animate-blob-delayed {
    animation: blobDelayed 18s infinite ease-in-out;
}

/* Subtle sparkle field (pure CSS) */
.gold-sparkles {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 10% 20%, rgba(250, 204, 21, 0.15) 0 2px, transparent 3px),
        radial-gradient(circle at 30% 80%, rgba(251, 191, 36, 0.14) 0 2px, transparent 3px),
        radial-gradient(circle at 70% 30%, rgba(253, 224, 71, 0.12) 0 2px, transparent 3px),
        radial-gradient(circle at 85% 60%, rgba(234, 179, 8, 0.12) 0 2px, transparent 3px);
    background-size: 1400px 900px;
    animation: drift 40s linear infinite;
    filter: blur(0.25px);
}

@keyframes drift {
    0% {
        background-position: 0 0, 0 0, 0 0, 0 0;
    }

    50% {
        background-position: 50px -30px, -40px 25px, 30px 40px, -25px -20px;
    }

    100% {
        background-position: 0 0, 0 0, 0 0, 0 0;
    }
}
</style>
