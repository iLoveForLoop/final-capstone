<script setup>
import { LMap, LTileLayer, LMarker, LPopup } from "@vue-leaflet/vue-leaflet";
import { ref } from "vue";

// Example vendor location (lat, lng)
const vendorLocation = ref([14.5995, 120.9842]); // Manila example
</script>

<template>
    <LMap style="height: 400px; width: 100%" :zoom="13" :center="vendorLocation">
        <!-- Free OpenStreetMap tiles -->
        <LTileLayer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            attribution="&copy; <a href='https://www.openstreetmap.org/copyright'>OpenStreetMap</a> contributors" />

        <!-- Marker at vendor location -->
        <LMarker :lat-lng="vendorLocation">
            <LPopup>
                <strong>Vendor Name</strong><br />
                Vendor address here
            </LPopup>
        </LMarker>
    </LMap>
</template>
