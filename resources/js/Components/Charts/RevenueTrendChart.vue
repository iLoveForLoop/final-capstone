<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import {
    Chart,
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend
} from 'chart.js'

Chart.register(
    BarController,
    BarElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend
)

const props = defineProps({
    data: {
        type: Object,
        required: true,
        validator: data => (
            Array.isArray(data?.labels) &&
            Array.isArray(data?.values)
        )
    },
    barColor: {
        type: String,
        default: '#10b981'
    },
    height: {
        type: Number,
        default: 300
    }
})

const chartRef = ref(null)
const chartInstance = ref(null)

const initChart = () => {
    if (chartInstance.value) {
        chartInstance.value.destroy()
    }

    const ctx = chartRef.value.getContext('2d')

    chartInstance.value = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: props.data.labels,
            datasets: [{
                label: 'Revenue (₱)',
                data: props.data.values,
                backgroundColor: props.barColor,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#f9fafb',
                    bodyColor: '#f9fafb',
                    padding: 12,
                    usePointStyle: true,
                    callbacks: {
                        label: (context) => {
                            return '₱' + context.raw.toLocaleString()
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        color: '#64748b',
                        callback: (value) => {
                            if (value >= 1000000) return '₱' + (value / 1000000).toFixed(1) + 'M'
                            if (value >= 1000) return '₱' + (value / 1000).toFixed(0) + 'k'
                            return '₱' + value
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b'
                    }
                }
            }
        }
    })
}

onMounted(() => {
    nextTick(() => initChart())
})

watch(() => props.data, () => {
    nextTick(() => initChart())
}, { deep: true })
</script>

<template>
    <div class="chart-container" :style="{ height: `${height}px` }">
        <canvas ref="chartRef"></canvas>
    </div>
</template>

<style scoped>
.chart-container {
    position: relative;
    width: 100%;
}
</style>
