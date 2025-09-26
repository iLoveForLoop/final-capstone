<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import L from 'leaflet'

const props = defineProps({
    formData: Object,
    serviceCoverageOptions: Array,
    errors: Object
})

// Map instance and marker
let map = null
let marker = null

// Reactive references
const mapContainer = ref(null)
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
    const mapElement = document.getElementById('map')
    if (!mapElement) {
        console.error('Map container not found')
        return
    }

    try {
        // Initialize the map
        map = L.map('map').setView(defaultCoords, 13)

        // Add tile layer
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

        // If there's already an address, try to geocode it
        if (props.formData.address && props.formData.address.trim()) {
            geocodeAddress(props.formData.address, true) // true for map update
        } else if (props.formData.latitude && props.formData.longitude) {
            // If coordinates exist, center map there
            const coords = [props.formData.latitude, props.formData.longitude]
            map.setView(coords, 15)
            addMarker(coords)
        }

        // Add click event to map for manual pin placement
        map.on('click', (e) => {
            const coords = [e.latlng.lat, e.latlng.lng]
            addMarker(coords)
            updateCoordinates(e.latlng.lat, e.latlng.lng)

            // Reverse geocode to get address
            reverseGeocode(e.latlng.lat, e.latlng.lng)
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
        reverseGeocode(position.lat, position.lng)
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
        // Try multiple search approaches for better flexibility
        const searchQueries = [
            address, // Original address
            `${address}, Philippines`, // Add country for better results
        ]

        let foundResult = null

        for (const query of searchQueries) {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=3&addressdetails=1&countrycodes=ph`)

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }

            const data = await response.json()

            if (data && data.length > 0) {
                // Look for the best match - prefer more specific locations but accept general ones
                foundResult = data.find(result =>
                    result.importance > 0.1 && // Basic importance threshold
                    (result.type !== 'country') // Avoid country-level results
                ) || data[0] // Fallback to first result

                if (foundResult) break
            }
        }

        if (foundResult) {
            const lat = parseFloat(foundResult.lat)
            const lng = parseFloat(foundResult.lon)
            const coords = [lat, lng]

            // Always update coordinates regardless of precise mapping
            updateCoordinates(lat, lng)

            // Set validation message with location type info
            const locationType = getLocationTypeDescription(foundResult)
            addressValidationMessage.value = `✓ ${locationType} found: ${foundResult.display_name}`

            // Only update map if precise mapping is enabled and updateMap is true
            if (updateMap && map && enablePreciseMapping.value) {
                map.setView(coords, getZoomLevel(foundResult))
                addMarker(coords)
            }
        } else {
            // More lenient error message - don't clear coordinates immediately
            addressValidationMessage.value = '⚠️ Location not found, but you can still proceed if this is a valid address'
            addressError.value = ''
            // Don't clear coordinates on not found - keep them for manual entry
        }
    } catch (error) {
        console.error('Geocoding error:', error)
        addressError.value = 'Unable to validate address. Please check your internet connection and try again.'
        addressValidationMessage.value = ''
        // Don't clear coordinates on network error - user might have manually entered them
    } finally {
        isGeocoding.value = false
    }
}

// Helper function to describe location type
const getLocationTypeDescription = (result) => {
    const type = result.type || result.class
    const category = result.category || result.class

    if (type === 'administrative' || category === 'boundary') {
        return 'Administrative area'
    } else if (type === 'city' || type === 'town' || type === 'municipality') {
        return 'City/Municipality'
    } else if (type === 'village' || type === 'hamlet' || category === 'place') {
        return 'Barangay/Village'
    } else if (type === 'state' || type === 'province') {
        return 'Province/State'
    } else if (category === 'highway' || type === 'road') {
        return 'Road/Street'
    } else {
        return 'Location'
    }
}

// Helper function to determine appropriate zoom level based on location type
const getZoomLevel = (result) => {
    const type = result.type || result.class

    if (type === 'state' || type === 'province') {
        return 10 // Province level
    } else if (type === 'city' || type === 'municipality') {
        return 12 // City/Municipality level
    } else if (type === 'village' || type === 'hamlet' || type === 'administrative') {
        return 14 // Barangay level
    } else {
        return 15 // Default/specific location
    }
}

const reverseGeocode = async (lat, lng) => {
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`)

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }

        const data = await response.json()

        if (data && data.display_name) {
            props.formData.address = data.display_name
            addressError.value = ''
            addressValidationMessage.value = `✓ Address updated from map`
        }
    } catch (error) {
        console.error('Reverse geocoding error:', error)
        addressError.value = 'Unable to get address from coordinates'
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
                reverseGeocode(lat, lng)
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
    const address = props.formData.address
    if (address && address.trim()) {
        geocodeAddress(address, true) // true to update map if enabled
    } else {
        alert('Please enter an address first.')
    }
}

const validateAddressOnly = () => {
    const address = props.formData.address
    if (address && address.trim()) {
        geocodeAddress(address, false) // false to not update map, just validate
    }
}

const togglePreciseMapping = async () => {
    enablePreciseMapping.value = !enablePreciseMapping.value

    if (enablePreciseMapping.value && !map) {
        // Wait for DOM update and ensure map container exists
        await nextTick()

        // Double check that the element exists
        const mapElement = document.getElementById('map')
        if (mapElement) {
            initializeMap()
        } else {
            // If still not found, wait a bit more
            setTimeout(() => {
                const mapEl = document.getElementById('map')
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

        // Don't clean up coordinates when disabling - keep them for the address
        // props.formData.latitude = null
        // props.formData.longitude = null
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
            props.formData.latitude = null
            props.formData.longitude = null
        }
    }, 1500) // Wait 1.5 seconds after user stops typing
}

// Watch for address changes
watch(() => props.formData.address, (newAddress) => {
    // Always validate address for coordinates, regardless of precise mapping
    debouncedAddressValidation(newAddress)

    // Additional map update if precise mapping is enabled
    if (newAddress && newAddress.trim() && isMapReady.value && !isGeocoding.value && enablePreciseMapping.value) {
        setTimeout(() => {
            geocodeAddress(newAddress, true)
        }, 2000) // Slightly longer delay for map updates
    }
})

// Initialize formData coordinates if they don't exist
if (!props.formData.latitude) props.formData.latitude = null
if (!props.formData.longitude) props.formData.longitude = null
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Business Address *</label>
        <input v-model="formData.address" type="text"
            :class="['w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                errors.address || addressError ? 'border-red-300' : addressValidationMessage ? 'border-green-300' : 'border-gray-300']"
            placeholder="Enter your business address" @blur="enablePreciseMapping && searchAddress()">

        <!-- Address validation feedback -->
        <div class="mt-1">
            <p v-if="errors.address" class="text-sm text-red-600">{{ errors.address }}</p>
            <p v-else-if="addressError" class="text-sm text-red-600">{{ addressError }}</p>
            <p v-else-if="addressValidationMessage" class="text-sm text-green-600">{{ addressValidationMessage }}</p>
            <div v-else-if="isGeocoding" class="flex items-center text-sm text-blue-600">
                <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-blue-600 mr-2"></div>
                Validating address...
            </div>
        </div>

        <!-- Coordinates display (always shown when available) -->
        <div v-if="formData.latitude && formData.longitude"
            class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-md">
            <div class="flex items-center">
                <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div>
                    <p class="text-xs font-medium text-blue-800">Location Coordinates Available</p>
                    <p class="text-xs text-blue-600">
                        {{ parseFloat(formData.latitude).toFixed(6) }}, {{ parseFloat(formData.longitude).toFixed(6) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Precise Location Toggle -->
        <div class="mt-3 flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex-1">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Precise Location Mapping</h4>
                        <p class="text-xs text-gray-500">Enable interactive map to pinpoint exact coordinates</p>
                    </div>
                </div>
            </div>
            <button type="button" @click="togglePreciseMapping" :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
                enablePreciseMapping ? 'bg-indigo-600' : 'bg-gray-200'
            ]">
                <span :class="[
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    enablePreciseMapping ? 'translate-x-5' : 'translate-x-0'
                ]" />
            </button>
        </div>
    </div>

    <!-- Map Container - Only show when precise mapping is enabled -->
    <div v-if="enablePreciseMapping">
        <label class="block text-sm font-medium text-gray-700 mb-2">Location on Map</label>
        <div class="border border-gray-300 rounded-md overflow-hidden">
            <div id="map" class="h-64 bg-gray-100 relative">
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
                        <p class="text-sm">Click to pin your exact location</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 flex gap-2 flex-wrap">
                <button type="button" @click="useCurrentLocation"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm transition-colors">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    Use Current Location
                </button>
                <button type="button" @click="searchAddress"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm transition-colors"
                    :disabled="!formData.address || isGeocoding">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search Address
                </button>
            </div>
        </div>

        <!-- Coordinates display for precise mapping -->
        <div v-if="formData.latitude && formData.longitude"
            class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-4 h-4 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm font-medium text-green-800">Precise Location Set</p>
                    <p class="text-xs text-green-600">
                        Coordinates: {{ parseFloat(formData.latitude).toFixed(6) }}, {{
                            parseFloat(formData.longitude).toFixed(6) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Service Coverage Areas *</label>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div v-for="area in serviceCoverageOptions" :key="area" class="flex items-center">
                <input :id="area" v-model="formData.serviceCoverageAreas" :value="area" type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label :for="area" class="ml-2 text-sm text-gray-700">{{ area }}</label>
            </div>
        </div>
        <p v-if="errors.serviceCoverageAreas" class="mt-1 text-sm text-red-600">
            {{ errors.serviceCoverageAreas }}
        </p>
    </div>
</template>

<style>
/* Fix for marker icons in Leaflet with bundlers */
.leaflet-marker-icon {
    background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAApCAYAAADAk4LOAAAGmklEQVRYw7VXeUyTZxjvNnfELFuyLLNkypJl2fYPI+OrtuFUFEM8UTCBRFBYJjAyHZCJgNY2mFfGLchUCCJTOm5hFuQCEOQSBBCRgUDIAAwQZoUNOjHp3/t+/fp1QHGY5f+ynxJK37fn9//e5znf55A5IpIiWkCLl2rYXb/gZRlGVHIK8zOXsKBXRAK8xUODQY0hXCk8Uia7A5mVs9fAeGgqBTdXhJOB/VYCmBJ7/Vf3aepqI7YBGWkj8MfQcxfBN1ooVRjN7lJBEJI+Ey3p6mpmm1xoUE5/TZRZs+5Q2lhZKS1Gq8OxNjgFnD3mAGQcBHUDRgQoE4iKbH9FqvDKL1I2mwuvj9CYlJxSNE1GakqL8c6Zc+jq7EBTQz1qq6vw5MED3L97B3dvzeCXH37A7atXcevyZVw8fx5nzp5Ft1YLQx5BDlgKpvNL8ZOC0JfNnYO5s6egMC8XpqZmOIU5CKzJf5MMDB9eM0JqNOGODqNRAjKzfr+STN8U0PwIrTe5YMF/cPsQhC8QnLMJT2Y7eC8mCp4+YeCLx8P7/Qvw9BaCtcsXkP3iBTy9BQhaI2jPykJfby9qKivw6PgJtPl4Y2LKIvpBqeN8TXTwATPfLfMJjYR3QAT8hZ+gLJnPJgb6B0Mk/hgSiQw/6HQ4q7oDX+8weBJKJpYLxGM4K2p2TEnGKtxMcsQcWydbZHrH4JdObXQfNJQGvQG+oWIExCjwG0hFHLXoeBJMSMZ8RtIJGRHQ+JcBUyFCRJ1DqgNVczAy3s4JOqnJgBYjeAcBo9Gb7UfvwL7OaGKBdNswlIFgj3mAaWCG3n/KgcKWCHIU4U8l7gf6S6TLIyM6m1jERiKZwxIXi7GNnVJOPMhgOFO+JE7PEpLlU8wZjSuUJCrWQkEZu9gT5nGYxlLSDOoKfLnPNUIGcR7AqKj2VuG4c3y/9TxPz/AeVXZ/9kTFV8zLnMGb1g+DKPrjfTa3Y0tPNCcqorYwRz3DzOgCsLtQNnr0H0XoaYAr1bKgxiHHlgJhZ4KY7PvRv8/vNq7MJYZrxBQI2lRdkx21X4QG4N2SbE5t4lKJCOADKplIVF5gV3fT+wt8dCJW3nwpuCgJkUHVOG+CxBjkR9AaRLn8N8WJ6pCJ3tNp+6MN2+pLNx69YXBJJODjhHpWDmfD0hxc9q3CCNQGkFPFbqzEhfY9STqFNK+YmUU5u1/KmgYVGHjKR2KZmKmzJW/jjSW7HTmF5iqvZuE4VJD4lI3SkF7xD9DI5Pf+4y8fUPqckd6aSJdGqQ9bkJzUZNdME6vQTCQOKfWgJQMqb2Cz1mPJ5jfxqZlBcCjE1b2XzE3BFYhm9YGlR9pB5MWM/XeUxm0zxRSsj0HYrM3Y9/4DJuPSo3KI8GJfmNvNfJSGnyBv7GNPjPUaRXJYJQEGCqoWHMZPeHtgB1G8Z5XiHzJl4gOF4l84VkPjMOkc0oFzP8V8k2z7wnQTwn7MXy+Ef9ZXzpFFqgWsKC3l6KS1aBeLxLlYKYkwvXl7CDNPZWd7g8uQKJcJnLzEFsAhQ4UBsZ7eRjqvz5jNJfFXvMNWJKoJcHGQKPK+AiEixJUNh3Qhfq5aevxQFxI0Z5qXpJVOy8HUGxLKoF7fO3f1dVmjJZdJq6KS9FJlUHtHe4VITSzC3tLt/VTBXFlYwS5z7eKEKpLLd2G99NWWZ2Xf8U2kZWNa4U7pSqOjxIdHmO1B6eC/YLmQvTXrNrk7Qn6rKmRTJKRxfkCkmqMBwvLvSyJl1lqrJJWZKVa9a6fCGPBH6q3MgaklKttLqyKMxkKOW3vPzpvGpKv3+wbLUOz3Q98mzMxX4jD9MgNn3A5GpJ6Mq7dTqp4nxPh7gf+iqnFpCFtgNW2K+lbLHFLlJG9F7O2KMrqfmZGxnYfG/7hq5MZrJh7K3ynq8jS7LKQ1ePJI+ck3N1s8SBDy/aHcHZqLqLDu9jq0CYqKfq/k7PRpOa8r7mLzrSvXgHDc7qO8bHVHuKJhMOV1eG2bvvVlvxYJMX7K8iU8vrHRiuWKyqoO5zPnZXCmNW5Fqr8GEFvQeKBdGNbJqH5bLaEWqHJ9q5I6KyKimLFoKJM7oWB9lFaRFB8fNWzYFWRFUJvKfWoFQrFOK8SfbfFX34F4c9qNh9mWekAAAAASUVORK5CYII=');
}

.leaflet-marker-shadow {
    background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACkAAAApCAQAAAACach9AAACMUlEQVR4Ae3ShY7jQBAE0Aoz/f9/HTMzhg1zrdKUrJbdx+Kd2nD8VNudfsL/Th///dyQN2TH6f3y/BGpC379rVYS7t1RLWNaRl3L4jKyfN3CIOeQU5FBK9mIXYPOl/n94mPKxkMB9Vn3nLyGVQPFPCPX0Vc4c0kxPCjmdwGGNzVtTXSZwgOznGMo5XQnPIBXK9WOVwh2OWYJh4cDCQWD0bYr8GNBMrwXM1iJ5FlZh7C8D1wGNdXZS8wr+BhAUr5rydZjN6l6KHZP6oHwDFRKJR+b5TvpP6KL9LgKfXOJoMQhq5KYi3VpB8Y3OLRyJ7m8dBzAVX5n4PLYpvs2vFB5V9CdKHk1n35mzHTaS7BcVfGhVMmrU4eFTlB7OL4H9k1D/GzUEepOzJKXmKKl/lq2r8W7WVSdNnmLs3lE4+dOg+LJfgFMzJF7ZpLGpKe5zZ/pKzN/V2jcKqEMbWVWLRKzTNpKbE9TLJrVD0mLG5Gv2ky7hnCl9HNTnE4YneTCGrCaKFbXRrHlCBjZYaDBKB9lE3HWHBzEoKa5zKc2DVHrQ8aQQ1Gzx4D7gczGGTg3MfnYJ+vQKnqJVhHh8kkrbp/DGqtD+fdhgF4l+mObacMp+5j7+LKLYJJLdgK9rn0LLr9IrwLDnR+Hx1CnGGFsJfzfgEsPm0N8qRkTJKlIyafg7N1Kb5yTONH8bgKmCaXKUTGO8kSNt5JUALHEKjGnhsL6kG7/VKlP8M+8aTEFIzN7s0iMm5nw3Y39rlHnIh0lxj/YhFD3VLOFnNuOp2iahSoEZYhbUvJ7rFyGQfNd8VnqFhpLlHmrYtBECJM6m3K5ynC8gQZK');
}
</style>
