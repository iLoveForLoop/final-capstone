<script setup>
import { Link } from '@inertiajs/vue3';
import NewServiceCard from './NewServiceCard.vue';
import { nextTick, onMounted, watch } from 'vue';
import ListViewServiceCard from './ListViewServiceCard.vue';

const props = defineProps({
    services: {
        type: Object
    },
    viewMode: {
        type: String,
        default: 'grid'
    }
})

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};

// Function to get service-specific information
const getServiceInfo = (service) => {
    if (service.catering_service) {
        return {
            type: 'catering',
            icon: '🍽️',
            details: [
                `${service.catering_service.min_pax}-${service.catering_service.max_pax} pax`,
                `${service.catering_service.lead_time_days} days lead time`,
                service.catering_service.is_customizable ? 'Customizable' : null
            ].filter(Boolean),
            price: service.catering_service.price || service.price,
            hasDelivery: !!service.catering_service.delivery_fee,
            deliveryFee: service.catering_service.delivery_fee,
            menuCount: service.catering_service.dishes ?
                Object.values(service.catering_service.dishes).reduce((total, dishes) => total + (dishes?.length || 0), 0) : 0
        };
    }

    if (service.photography_service) {
        return {
            type: 'photography',
            icon: '📸',
            details: [
                `${service.photography_service.duration}h session`,
                service.photography_service.style,
                service.photography_service.equipment ? 'Pro Equipment' : null
            ].filter(Boolean),
            price: service.photography_service.price || service.price,
            hasDelivery: false,
            deliveryFee: null,
            menuCount: 0
        };
    }

    // Default service
    return {
        type: 'general',
        icon: '⭐',
        details: [service.category?.name || 'Professional Service'],
        price: service.price,
        hasDelivery: false,
        deliveryFee: null,
        menuCount: 0
    };
};

let observer = null;

// Animation observer
onMounted(() => {
    initializeAnimations();
});

const initializeAnimations = () => {
    // Clean up existing observer
    if (observer) {
        observer.disconnect();
    }
    // Remove animated class from all service cards to reset animations
    const existingElements = document.querySelectorAll('.service-card-reveal');
    existingElements.forEach(element => {
        element.classList.remove('animated');
    });

    // Wait a bit for DOM to update
    setTimeout(() => {
        const animatedElements = document.querySelectorAll('.service-card-reveal');

        observer = new IntersectionObserver((entries) => {
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

        animatedElements.forEach(element => {
            observer.observe(element);
        });
    }, 50);
};

// Re-initialize animations when services change
watch(() => props.services.data, () => {
    nextTick(() => {
        setTimeout(() => {
            initializeAnimations();
        }, 100);
    });
});

// Re-initialize animations when view mode changes
watch(() => props.viewMode, () => {
    nextTick(() => {
        setTimeout(() => {
            initializeAnimations();
        }, 150); // Slightly longer delay for view mode changes
    });
});
</script>

<template>
    <!-- Grid View -->
    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="(service, index) in services.data" :key="service.id"
            class="service-card-reveal bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300"
            :style="{ transitionDelay: `${index * 100}ms` }">
            <NewServiceCard :service="service" />
        </div>
    </div>

    <!-- List View -->
    <div v-else class="space-y-4">
        <div v-for="(service, index) in services.data" :key="service.id"
            class="service-card-reveal bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300"
            :style="{ transitionDelay: `${index * 100}ms` }">
            <ListViewServiceCard :service="service" />
        </div>
    </div>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Service Card Reveal Animation */
.service-card-reveal {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.5s ease;
}

.service-card-reveal.animated {
    opacity: 1;
    transform: translateY(0);
}

/* Performance optimizations */
.service-card-reveal {
    will-change: transform, opacity;
}
</style>
