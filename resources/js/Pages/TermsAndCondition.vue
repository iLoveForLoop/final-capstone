<script setup>
import { ref, onMounted, nextTick } from 'vue'

const activeTab = ref('terms')

// Read ?tab=terms|privacy from URL
const qp = new URLSearchParams(window.location.search)
const initial = qp.get('tab')
if (initial === 'privacy' || initial === 'terms') activeTab.value = initial

// Smooth scroll to anchor when hash exists
onMounted(async () => {
    await nextTick()
    if (location.hash) {
        const el = document.querySelector(location.hash)
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
})

const termsSections = [
    { id: 't-i', num: '1', title: 'Acceptance of Terms' },
    { id: 't-ii', num: '2', title: 'User Accounts & Responsibilities' },
    { id: 't-iii', num: '3', title: 'Vendor and Client Obligations' },
    { id: 't-iv', num: '4', title: 'Bookings, Payments & Cancellations' },
    { id: 't-v', num: '5', title: 'Content, Reviews & Conduct' },
    { id: 't-vi', num: '6', title: 'Service Availability & Limitations' },
    { id: 't-vii', num: '7', title: 'Intellectual Property Rights' },
    { id: 't-viii', num: '8', title: 'Liability & Disclaimers' },
    { id: 't-ix', num: '9', title: 'Termination & Suspension' },
    { id: 't-x', num: '10', title: 'Governing Law & Dispute Resolution' },
    { id: 't-xi', num: '11', title: 'Changes to These Terms' },
    { id: 't-xii', num: '12', title: 'Contact Information' },
]

const privacySections = [
    { id: 'p-i', num: '1', title: 'Overview & Purpose' },
    { id: 'p-ii', num: '2', title: 'Information We Collect' },
    { id: 'p-iii', num: '3', title: 'How We Use Your Information' },
    { id: 'p-iv', num: '4', title: 'Information Sharing & Disclosure' },
    { id: 'p-v', num: '5', title: 'Data Security & Retention' },
    { id: 'p-vi', num: '6', title: 'Your Data Rights & Choices' },
    { id: 'p-vii', num: '7', title: 'Cookies & Tracking Technologies' },
    { id: 'p-viii', num: '8', title: 'Third-Party Services' },
    { id: 'p-ix', num: '9', title: 'Children’s Privacy' },
    { id: 'p-x', num: '10', title: 'Policy Updates' },
    { id: 'p-xi', num: '11', title: 'Contact Information' },
]
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <!-- Header -->
            <header class="text-center mb-12">
                <h1 class="text-3xl font-bold text-gray-900 mb-3">Eventory Legal Policies</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Review our Terms & Conditions and Privacy Policy to understand your rights, responsibilities,
                    and how we protect your information while using Eventory.
                </p>
            </header>

            <!-- Tabs -->
            <div class="flex justify-center mb-10">
                <div class="inline-flex rounded-lg border border-gray-300 bg-white p-1 shadow-sm">
                    <button class="px-6 py-3 rounded-md text-sm font-medium transition-all"
                        :class="activeTab === 'terms' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                        @click="activeTab = 'terms'">
                        Terms & Conditions
                    </button>
                    <button class="px-6 py-3 rounded-md text-sm font-medium transition-all"
                        :class="activeTab === 'privacy' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                        @click="activeTab = 'privacy'">
                        Privacy Policy
                    </button>
                </div>
            </div>

            <!-- Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-[280px,1fr] gap-8 items-start">
                <!-- TOC -->
                <aside class="lg:sticky lg:top-8">
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Contents</h3>
                        <nav class="space-y-2">
                            <template v-if="activeTab === 'terms'">
                                <a v-for="s in termsSections" :key="s.id" :href="'#' + s.id"
                                    class="flex items-start rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <span class="font-medium text-blue-600 mr-2 mt-0.5">{{ s.num }}</span>
                                    <span>{{ s.title }}</span>
                                </a>
                            </template>
                            <template v-else>
                                <a v-for="s in privacySections" :key="s.id" :href="'#' + s.id"
                                    class="flex items-start rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                    <span class="font-medium text-blue-600 mr-2 mt-0.5">{{ s.num }}</span>
                                    <span>{{ s.title }}</span>
                                </a>
                            </template>
                        </nav>
                    </div>
                </aside>

                <!-- Main Content -->
                <section class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="p-8 space-y-12">

                        <!-- Terms & Conditions -->
                        <div v-show="activeTab === 'terms'" class="space-y-12">
                            <div v-for="s in termsSections" :key="s.id" :id="s.id" class="scroll-mt-16">
                                <h2 class="text-xl font-semibold text-gray-900 mb-3">
                                    {{ s.num }}. {{ s.title }}
                                </h2>
                                <p class="text-gray-600 leading-relaxed">
                                    <template v-if="s.id === 't-i'">
                                        By accessing and using Eventory, you agree to comply with these Terms &
                                        Conditions. If you disagree with any part of these terms, you may not access the
                                        platform.
                                    </template>
                                    <template v-else-if="s.id === 't-ii'">
                                        Users must register with accurate and complete information. You are responsible
                                        for maintaining your login credentials and for any activities under your
                                        account.
                                    </template>
                                    <template v-else-if="s.id === 't-iii'">
                                        Vendors must ensure that all service listings, pricing, and availability details
                                        are accurate. Clients must use the platform only for legitimate event planning
                                        and booking purposes.
                                    </template>
                                    <template v-else-if="s.id === 't-iv'">
                                        <p>
                                            Eventory currently does not process or handle any payments directly through
                                            the platform.
                                            All financial transactions and service fees are handled outside of the
                                            Eventory system,
                                            based on mutual agreement between the client and the vendor.
                                        </p>
                                        <p class="mt-2">
                                            Eventory’s role is limited to facilitating connections and communication
                                            between clients
                                            and vendors. We are not involved in payment collection, refund processing,
                                            or any monetary
                                            disputes between parties.
                                        </p>
                                        <p class="mt-2">
                                            Vendors are solely responsible for managing their own payment methods,
                                            pricing, and any
                                            applicable taxes related to their services. Clients are encouraged to
                                            confirm payment
                                            arrangements securely and directly with vendors before proceeding with any
                                            booking.
                                        </p>
                                    </template>

                                    <template v-else-if="s.id === 't-v'">
                                        Users may submit reviews, images, and comments, provided they are respectful and
                                        truthful. Eventory reserves the right to remove inappropriate or false content.
                                    </template>
                                    <template v-else-if="s.id === 't-vi'">
                                        Eventory strives to maintain 24/7 platform availability but does not guarantee
                                        uninterrupted access due to maintenance or unforeseen technical issues.
                                    </template>
                                    <template v-else-if="s.id === 't-vii'">
                                        All logos, trademarks, and content displayed on Eventory belong to their
                                        respective owners. You may not copy, reproduce, or distribute any materials
                                        without prior written consent.
                                    </template>
                                    <template v-else-if="s.id === 't-viii'">
                                        Eventory shall not be liable for indirect or consequential losses, vendor
                                        misconduct, or service dissatisfaction. The platform acts only as a facilitator
                                        between vendors and clients.
                                    </template>
                                    <template v-else-if="s.id === 't-ix'">
                                        Eventory reserves the right to suspend or terminate accounts that violate our
                                        policies, engage in fraud, or misuse the platform.
                                    </template>
                                    <template v-else-if="s.id === 't-x'">
                                        These Terms are governed by the laws of the Republic of the Philippines. Any
                                        disputes shall be resolved exclusively in the courts of Manila.
                                    </template>
                                    <template v-else-if="s.id === 't-xi'">
                                        Eventory may modify these Terms at any time. Updates will be effective
                                        immediately upon posting. Continued use of the platform constitutes acceptance.
                                    </template>
                                    <template v-else>
                                        For questions, contact us at <strong>support@eventory.com</strong>.
                                    </template>
                                </p>
                            </div>
                        </div>

                        <!-- Privacy Policy -->
                        <div v-show="activeTab === 'privacy'" class="space-y-12">
                            <div v-for="s in privacySections" :key="s.id" :id="s.id" class="scroll-mt-16">
                                <h2 class="text-xl font-semibold text-gray-900 mb-3">{{ s.num }}. {{ s.title }}</h2>
                                <p class="text-gray-600 leading-relaxed">
                                    <template v-if="s.id === 'p-i'">
                                        This Privacy Policy explains how Eventory collects, uses, and protects your
                                        personal data in connection with our vendor booking and event management
                                        services.
                                    </template>
                                    <template v-else-if="s.id === 'p-ii'">
                                        We collect personal information such as your name, email, contact number, event
                                        details, and payment information when you use our services.
                                    </template>
                                    <template v-else-if="s.id === 'p-iii'">
                                        Eventory uses collected information to process bookings, communicate updates,
                                        ensure security, and improve platform functionality.
                                    </template>
                                    <template v-else-if="s.id === 'p-iv'">
                                        We may share necessary data with vendors and third-party payment processors to
                                        complete transactions. We never sell your personal information.
                                    </template>
                                    <template v-else-if="s.id === 'p-v'">
                                        Your information is stored securely and retained only as long as necessary for
                                        legal or operational reasons. Encryption and access controls are applied to
                                        safeguard your data.
                                    </template>
                                    <template v-else-if="s.id === 'p-vi'">
                                        You may request access, correction, or deletion of your data at any time by
                                        contacting our support team.
                                    </template>
                                    <template v-else-if="s.id === 'p-vii'">
                                        We use cookies to remember preferences, analyze usage patterns, and enhance your
                                        browsing experience. You may disable cookies through your browser settings.
                                    </template>
                                    <template v-else-if="s.id === 'p-viii'">
                                        Eventory integrates with trusted third-party services (e.g., payment gateways,
                                        analytics tools) which may have their own privacy practices.
                                    </template>
                                    <template v-else-if="s.id === 'p-ix'">
                                        Eventory does not knowingly collect personal data from children under 13 years
                                        of age.
                                    </template>
                                    <template v-else-if="s.id === 'p-x'">
                                        We may update this policy from time to time. Material changes will be
                                        communicated through the platform or email notifications.
                                    </template>
                                    <template v-else>
                                        For privacy concerns, contact us at <strong>privacy@eventory.com</strong>.
                                    </template>
                                </p>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-gray-200 text-sm text-gray-500">
                            Last updated: <span class="font-medium">October 21, 2025</span>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</template>

<style scoped>
:root {
    scroll-behavior: smooth;
}

.scroll-mt-16 {
    scroll-margin-top: 4rem;
}
</style>
