<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import L from 'leaflet'

const props = defineProps({
    formData: Object,
    isEditing: Boolean
})

// Map instance and marker
let map = null
let marker = null

// Reactive references
const isMapReady = ref(false)
const isGeocoding = ref(false)
const enablePreciseMapping = ref(false)
const addressError = ref('')
const addressValidationMessage = ref('')

// Default coordinates (you can change this to your preferred default location)
const defaultCoords = [9.951529, 123.961890] // Manila, Philippines

onMounted(async () => {
    await nextTick()
    // Check if coordinates already exist, auto-enable precise mapping
    if (props.formData.latitude && props.formData.longitude) {
        enablePreciseMapping.value = true
        await nextTick() // Wait for DOM update
        initializeMap()
    }
})

const initializeMap = () => {
    // Double check that map element exists
    const mapElement = document.getElementById('basic-info-map')
    if (!mapElement) {
        console.error('Map container not found')
        return
    }

    try {
        // Initialize the map
        map = L.map('basic-info-map').setView(defaultCoords, 13)

        // Add tile layer with normal style
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map)

        // Set map as ready
        isMapReady.value = true

        // Force map to resize (important for proper rendering)
        setTimeout(() => {
            if (map) {
                map.invalidateSize()
            }
        }, 100)

        // If coordinates exist, center map there
        if (props.formData.latitude && props.formData.longitude) {
            const coords = [props.formData.latitude, props.formData.longitude]
            map.setView(coords, 15)
            addMarker(coords)
        }

        // Add click event to map for manual pin placement
        map.on('click', (e) => {
            const coords = [e.latlng.lat, e.latlng.lng]
            addMarker(coords)
            updateCoordinates(e.latlng.lat, e.latlng.lng)

            // Auto-update address with simplified version
            reverseGeocode(e.latlng.lat, e.latlng.lng, true)
        })
    } catch (error) {
        console.error('Error initializing map:', error)
    }
}

const addMarker = (coords) => {
    // Remove existing marker
    if (marker) {
        map.removeLayer(marker)
    }

    // Add new marker
    marker = L.marker(coords, {
        draggable: true
    }).addTo(map)

    // Add drag event to marker
    marker.on('dragend', (e) => {
        const position = e.target.getLatLng()
        updateCoordinates(position.lat, position.lng)
        reverseGeocode(position.lat, position.lng, true)
    })
}

const updateCoordinates = (lat, lng) => {
    props.formData.latitude = lat
    props.formData.longitude = lng
}

const geocodeAddress = async (address, updateMap = false) => {
    if (!address || !address.trim()) return

    isGeocoding.value = true
    addressError.value = ''
    addressValidationMessage.value = ''

    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1&addressdetails=1&countrycodes=ph`)

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }

        const data = await response.json()

        if (data && data.length > 0) {
            const result = data[0]
            const lat = parseFloat(result.lat)
            const lng = parseFloat(result.lon)
            const coords = [lat, lng]

            // Always update coordinates regardless of precise mapping
            updateCoordinates(lat, lng)

            // Get simplified address in the requested format
            const simplifiedAddress = getSimplifiedAddress(result)
            addressValidationMessage.value = `✓ Location found: ${simplifiedAddress}`

            // Only update map if precise mapping is enabled and updateMap is true
            if (updateMap && map && enablePreciseMapping.value) {
                map.setView(coords, 15) // Always zoom to street level
                addMarker(coords)
            }
        } else {
            addressValidationMessage.value = '⚠️ Location not found. You can still pin exact location manually on the map.'
            addressError.value = ''
        }
    } catch (error) {
        console.error('Geocoding error:', error)
        addressError.value = 'Unable to validate address. You can still pin exact location manually on the map.'
        addressValidationMessage.value = ''
    } finally {
        isGeocoding.value = false
    }
}

// Helper function to get simplified address in the format: Pinayagan Norte, Tubigon, Bohol, Philippines
const getSimplifiedAddress = (result) => {
    const address = result.address

    // Build address in the specific format: Pinayagan Norte, Tubigon, Bohol, Philippines
    const addressParts = []

    // Level 1: Village/Barangay
    if (address.village) {
        addressParts.push(address.village)
    } else if (address.neighbourhood) {
        addressParts.push(address.neighbourhood)
    } else if (address.barangay) {
        addressParts.push(address.barangay)
    }

    // Level 2: Municipality/City
    if (address.municipality) {
        addressParts.push(address.municipality)
    } else if (address.town) {
        addressParts.push(address.town)
    } else if (address.city) {
        addressParts.push(address.city)
    }

    // Level 3: Province
    if (address.state) {
        addressParts.push(address.state)
    } else if (address.province) {
        addressParts.push(address.province)
    }

    // Level 4: Country
    if (address.country) {
        addressParts.push(address.country)
    } else {
        addressParts.push('Philippines') // Default to Philippines if not specified
    }

    return addressParts.join(', ')
}

const reverseGeocode = async (lat, lng, updateAddress = false) => {
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }

        const data = await response.json()

        if (data && data.address) {
            // Get simplified address
            const simplifiedAddress = getSimplifiedAddress(data)

            if (updateAddress) {
                // Update the actual address field
                props.formData.businessAddress = simplifiedAddress
                addressValidationMessage.value = `✓ Address updated from pinned location`
            } else {
                // Just show information without updating the field
                addressValidationMessage.value = `✓ Nearby location: ${simplifiedAddress}`
            }
        }
    } catch (error) {
        console.error('Reverse geocoding error:', error)
        // Don't show error for reverse geocoding - it's just for information
    }
}

const useCurrentLocation = () => {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by this browser.')
        return
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude
            const lng = position.coords.longitude
            const coords = [lat, lng]

            if (map) {
                map.setView(coords, 15)
                addMarker(coords)
                updateCoordinates(lat, lng)
                reverseGeocode(lat, lng, true)
            }
        },
        (error) => {
            console.error('Geolocation error:', error)
            let errorMessage = 'Unable to retrieve your location. '
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage += 'Location access denied by user.'
                    break
                case error.POSITION_UNAVAILABLE:
                    errorMessage += 'Location information unavailable.'
                    break
                case error.TIMEOUT:
                    errorMessage += 'Location request timed out.'
                    break
                default:
                    errorMessage += 'An unknown error occurred.'
                    break
            }
            alert(errorMessage + ' Please try again or pin your location manually.')
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000
        }
    )
}

const searchAddress = () => {
    const address = props.formData.businessAddress
    if (address && address.trim()) {
        geocodeAddress(address, true) // true to update map if enabled
    } else {
        alert('Please enter an address first.')
    }
}

const validateAddressOnly = () => {
    const address = props.formData.businessAddress
    if (address && address.trim()) {
        geocodeAddress(address, false) // false to not update map, just validate
    }
}

const togglePreciseMapping = async () => {
    if (!props.isEditing) return; // Don't allow toggling when not in edit mode

    enablePreciseMapping.value = !enablePreciseMapping.value

    if (enablePreciseMapping.value && !map) {
        // Wait for DOM update and ensure map container exists
        await nextTick()

        // Double check that the element exists
        const mapElement = document.getElementById('basic-info-map')
        if (mapElement) {
            initializeMap()
        } else {
            // If still not found, wait a bit more
            setTimeout(() => {
                const mapEl = document.getElementById('basic-info-map')
                if (mapEl) {
                    initializeMap()
                }
            }, 100)
        }
    } else if (!enablePreciseMapping.value && map) {
        // Clean up map instance
        if (marker) {
            map.removeLayer(marker)
            marker = null
        }
        map.remove()
        map = null
        isMapReady.value = false
    }
}

// Debounced address validation
let addressValidationTimeout = null
const debouncedAddressValidation = (newAddress) => {
    if (addressValidationTimeout) {
        clearTimeout(addressValidationTimeout)
    }

    addressValidationTimeout = setTimeout(() => {
        if (newAddress && newAddress.trim()) {
            validateAddressOnly()
        } else {
            addressError.value = ''
            addressValidationMessage.value = ''
            // Don't clear coordinates when address is cleared
        }
    }, 1500) // Wait 1.5 seconds after user stops typing
}

// Watch for address changes
watch(() => props.formData.businessAddress, (newAddress) => {
    // Always validate address for coordinates, regardless of precise mapping
    debouncedAddressValidation(newAddress)

    // Additional map update if precise mapping is enabled
    if (newAddress && newAddress.trim() && isMapReady.value && !isGeocoding.value && enablePreciseMapping.value) {
        setTimeout(() => {
            geocodeAddress(newAddress, true)
        }, 2000) // Slightly longer delay for map updates
    }
})

// Initialize props.formData coordinates if they don't exist
if (!props.formData.latitude) props.formData.latitude = null
if (!props.formData.longitude) props.formData.longitude = null
</script>

<template>
    <h2 class="text-2xl font-semibold mb-6 text-gray-800">Basic Information</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Business Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
            <input v-model="formData.businessName" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="text" />
        </div>

        <!-- Contact Person -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person *</label>
            <input v-model="formData.contactPerson" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="text" />
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
            <input v-model="formData.email" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="email" />
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
            <input v-model="formData.phone" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="tel" />
        </div>

        <!-- Website -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
            <input v-model="formData.website" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="url" />
        </div>

        <!-- Years of Experience -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Years of Experience</label>
            <input v-model="formData.yearsOfExperience" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="number" />
        </div>
    </div>

    <!-- Business Description -->
    <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Business Description</label>
        <textarea v-model="formData.businessDescription" :disabled="!isEditing" rows="4"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
            placeholder="Tell potential clients about your business..."></textarea>
    </div>

    <!-- Business Address with Map Integration -->
    <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Business Address</label>
        <textarea v-model="formData.businessAddress" :disabled="!isEditing" rows="2"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
            placeholder="Enter your business address name (street, barangay, city)"></textarea>

        <!-- Address validation feedback - Only show when editing -->
        <div v-if="isEditing" class="mt-1">
            <p v-if="addressError" class="text-sm text-red-600">{{ addressError }}</p>
            <p v-else-if="addressValidationMessage" class="text-sm text-green-600">{{ addressValidationMessage }}</p>
            <div v-else-if="isGeocoding" class="flex items-center text-sm text-blue-600">
                <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-blue-600 mr-2"></div>
                Validating address...
            </div>
            <p v-else class="text-xs text-gray-500">
                Enter address name only (street, barangay, city). Use the map below for exact coordinates.
            </p>
        </div>

        <!-- Coordinates display - Only show when editing and coordinates exist -->
        <div v-if="isEditing && formData.latitude && formData.longitude"
            class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md">
            <div class="flex items-center">
                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div>
                    <p class="text-xs font-medium text-blue-800">Exact Location Coordinates</p>
                    <p class="text-xs text-blue-600">
                        {{ parseFloat(formData.latitude).toFixed(6) }}, {{ parseFloat(formData.longitude).toFixed(6) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Precise Location Toggle - Only show when editing -->
        <div v-if="isEditing"
            class="mt-3 flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex-1">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Pin Exact Location on Map</h4>
                        <p class="text-xs text-gray-500">Click anywhere on the map to set precise coordinates</p>
                    </div>
                </div>
            </div>
            <button type="button" @click="togglePreciseMapping" :disabled="!isEditing" :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
                enablePreciseMapping ? 'bg-indigo-600' : 'bg-gray-200',
                !isEditing ? 'opacity-50 cursor-not-allowed' : ''
            ]">
                <span :class="[
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    enablePreciseMapping ? 'translate-x-5' : 'translate-x-0'
                ]" />
            </button>
        </div>
    </div>

    <!-- Map Container - Only show when precise mapping is enabled and editing -->
    <div v-if="enablePreciseMapping && isEditing">
        <label class="block text-sm font-medium text-gray-700 mb-2">Pin Exact Location on Map</label>
        <div class="border border-gray-300 rounded-md overflow-hidden">
            <div id="basic-info-map" class="h-64 bg-gray-100 relative">
                <!-- Loading overlay -->
                <div v-if="isGeocoding"
                    class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-[1000]">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
                        <p class="mt-2 text-sm text-gray-600">Finding location...</p>
                    </div>
                </div>

                <!-- Map instructions overlay when map is not ready -->
                <div v-if="!isMapReady" class="absolute inset-0 flex items-center justify-center text-gray-500">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="mt-2">Interactive Map</p>
                        <p class="text-sm">Click anywhere to pin exact location</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 flex gap-2 flex-wrap">
                <button type="button" @click="useCurrentLocation" :disabled="!isEditing"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    Use Current Location
                </button>
                <button type="button" @click="searchAddress"
                    :disabled="!formData.businessAddress || isGeocoding || !isEditing"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Find on Map
                </button>
            </div>
        </div>

        <!-- Coordinates display for precise mapping - Only show when editing -->
        <div v-if="isEditing && formData.latitude && formData.longitude"
            class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-4 h-4 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-green-800">Exact Location Pinned</p>
                    <p class="text-xs text-green-600">
                        Coordinates: {{ parseFloat(formData.latitude).toFixed(6) }}, {{
                            parseFloat(formData.longitude).toFixed(6) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Media Links -->
    <div class="mt-8">
        <h3 class="text-lg font-medium mb-4 text-gray-800">Social Media</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="(value, platform) in formData.socialMedia" :key="platform">
                <label class="block text-sm font-medium text-gray-700 mb-2 capitalize">{{ platform
                }}</label>
                <input v-model="formData.socialMedia[platform]" :disabled="!isEditing"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                    type="text" />
            </div>
        </div>
    </div>
</template>

<style>
/* Fix for marker icons in Leaflet with bundlers */
.leaflet-marker-icon {
    background-image: url('data:image/png;base64,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');
}

.leaflet-marker-shadow {
    background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACkAAAApCAQAAAACach9AAACMUlEQVR4Ae3ShY7jQBAE0Aoz/f9/HTMzhg1zrdKUrJbdx+Kd2nD8VNudfsL/Th///dyQN2TH6f3y/BGpC379rVYS7t1RLWNaRl3L4jKyfN3CIOeQU5FBK9mIXYPOl/n94mPKxkMB9Vn3nLyGVQPFPCPX0Vc4c0kxPCjmdwGGNzVtTXSZwgOznGMo5XQnPIBXK9WOVwh2OWYJh4cDCQWD0bYr8GNBMrwXM1iJ5FlZh7C8D1wGMdXZy8wr+BhAUr5rydZjN6l6KHZP6oHwDFRKJR+b5TvpP6KL9LgKfXOJoMQhq5KYi3VpB8Y3OLRyJ7m8dBzAVX5n4PLYpvs2vFB5V9CdKHk1n35mzHTaS7BcVfGhVMmrU4eFTlB7OL4H9k1D/GzUEepOzJKXmKKl/lq2r8W7WVSdNnmLs3lE4+dOg+LJfgFMzJF7ZpLGpKe5zZ/pKzN/V2jcKqEMbWVWLRKzTNpKbE9TLJrVD0mLG5Gv2ky7hnCl9HNTnE4YneTCGrCaKFbXRrHlCBjZYaDBKB9lE3HWHBzEoKa5zKc2DVHrQ8aQQ1Gzx4D7gczGGTg3MfnYJ+vQKnqJVhHh8kkrbp/DGqtD+fdhgF4l+mObacMp+5j7+LKLYJJLdgK9rn0LLr9IrwLDnR+Hx1CnGGFsJfzfgEsPm0N8qRkTJKlIyafg7N1Kb5yTONH8bgKmCaXKUTGM8kSNt5JUALHEKjGnhsL6kG7/VKlP8M+8aTEFIzN7s0iMm5nw3Y39rlHnIh0lxj/YhFD3VLOFnNuOp2iahSoEZYhbUvJ7rFyGQfNd8VnqFhpLlHmrYtBECJM6m3K5ynC8gQZK');
}

/* Leaflet map container styles */
#basic-info-map {
    width: 100%;
    height: 100%;
    min-height: 256px;
}

/* Custom styles for the map controls */
.leaflet-container {
    font-family: inherit;
    background: #f8fafc;
}

.leaflet-popup-content-wrapper {
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

.leaflet-popup-content {
    margin: 12px 16px;
    font-size: 14px;
    line-height: 1.5;
}

/* Loading animation enhancements */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

/* Custom scrollbar for textareas */
textarea::-webkit-scrollbar {
    width: 6px;
}

textarea::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Focus states for better accessibility */
input:focus,
textarea:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

/* Disabled state improvements */
input:disabled,
textarea:disabled {
    cursor: not-allowed;
    opacity: 0.7;
}

/* Smooth transitions for interactive elements */
button,
input,
textarea,
.leaflet-marker-icon {
    transition: all 0.2s ease-in-out;
}

/* Custom styles for the toggle switch */
.toggle-bg::after {
    content: '';
    position: absolute;
    top: 0.125rem;
    left: 0.125rem;
    background: white;
    border-radius: 9999px;
    height: 1.25rem;
    width: 1.25rem;
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 0.15s;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

input:checked+.toggle-bg::after {
    transform: translateX(100%);
    border-color: white;
}

input:checked+.toggle-bg {
    background-color: #4f46e5;
    border-color: #4f46e5;
}

/* Responsive design improvements */
@media (max-width: 768px) {
    .grid.grid-cols-1.md\:grid-cols-2 {
        grid-template-columns: 1fr;
    }

    #basic-info-map {
        height: 300px;
    }

    .p-4.bg-gray-50.flex.gap-2.flex-wrap {
        flex-direction: column;
    }

    .p-4.bg-gray-50.flex.gap-2.flex-wrap button {
        width: 100%;
        justify-content: center;
    }
}

/* Print styles */
@media print {

    .leaflet-container,
    #basic-info-map,
    .bg-gray-50,
    button {
        display: none !important;
    }

    input:disabled,
    textarea:disabled {
        background: transparent !important;
        border: none !important;
        color: #000 !important;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .border-gray-300 {
        border-color: #000;
    }

    .bg-gray-50 {
        background-color: #fff;
    }

    .text-gray-500 {
        color: #000;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {

    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}

/* Custom utility classes for the component */
.business-info-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.map-instructions {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.coordinates-display {
    border-left: 4px solid #3b82f6;
}

.location-pinned {
    border-left: 4px solid #10b981;
}

/* Ensure proper z-index stacking */
.leaflet-container {
    z-index: 1;
}

.absolute.inset-0 {
    z-index: 1000;
}

/* Custom marker cluster styles (if using clustering in future) */
.marker-cluster-small {
    background-color: rgba(181, 226, 140, 0.6);
}

.marker-cluster-small div {
    background-color: rgba(110, 204, 57, 0.6);
}

.marker-cluster-medium {
    background-color: rgba(241, 211, 87, 0.6);
}

.marker-cluster-medium div {
    background-color: rgba(240, 194, 12, 0.6);
}

.marker-cluster-large {
    background-color: rgba(253, 156, 115, 0.6);
}

.marker-cluster-large div {
    background-color: rgba(241, 128, 23, 0.6);
}
</style>
