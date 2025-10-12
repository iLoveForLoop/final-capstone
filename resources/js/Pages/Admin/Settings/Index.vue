<script setup>
import TestLayout from '@/Layouts/TestLayout.vue'
import { ref, reactive } from 'vue'

// Reactive state for settings and active section
const activeSection = ref('general')

const settings = reactive({
    general: {
        platformName: 'Eventory',
        defaultCurrency: 'PHP',
        platformDescription: 'Eventory: Vendor Booking & Management Platform'
    },
    account: {
        adminEmail: 'admin@eventory.com',
        twoFactorEnabled: false
    },
    preferences: {
        emailNotifications: true,
        maintenanceMode: false,
        autoApproveVendors: false,
        bookingConfirmation: true
    },
    notifications: {
        pushNotifications: true,
        smsNotifications: false,
        weeklyReports: true
    }
})

// Methods
const saveSetting = (section, key) => {
    console.log(`Saving ${section}.${key}:`, settings[section][key])
    // In a real application, you would make an API call here
    alert(`Setting ${key} saved successfully!`)
}

const toggleTwoFactor = () => {
    settings.account.twoFactorEnabled = !settings.account.twoFactorEnabled
    console.log('Two-factor authentication:', settings.account.twoFactorEnabled ? 'enabled' : 'disabled')
    alert(`Two-factor authentication ${settings.account.twoFactorEnabled ? 'enabled' : 'disabled'}!`)
}

const saveAllChanges = () => {
    console.log('Saving all changes:', settings)
    alert('All changes saved successfully!')
}

const discardChanges = () => {
    console.log('Discarding changes')
    // Reset to original values - in a real app you'd fetch from API
    Object.assign(settings, {
        general: {
            platformName: 'Eventory',
            defaultCurrency: 'PHP',
            platformDescription: 'Eventory: Vendor Booking & Management Platform'
        },
        account: {
            adminEmail: 'admin@eventory.com',
            twoFactorEnabled: false
        },
        preferences: {
            emailNotifications: true,
            maintenanceMode: false,
            autoApproveVendors: false,
            bookingConfirmation: true
        },
        notifications: {
            pushNotifications: true,
            smsNotifications: false,
            weeklyReports: true
        }
    })
    alert('Changes discarded!')
}
</script>

<template>
    <TestLayout>


        <div class="p-6 space-y-8 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">Platform Settings</h1>
                    <p class="text-gray-600">Configure system preferences and platform-wide settings</p>
                </div>
                <div class="flex space-x-3">
                    <button @click="discardChanges"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 font-medium text-sm transition-colors">
                        Discard Changes
                    </button>
                    <button @click="saveAllChanges"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white font-medium text-sm transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>

            <!-- Settings Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                        <div class="p-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-800">Settings Categories</h3>
                        </div>
                        <nav class="p-2">
                            <a href="#" @click.prevent="activeSection = 'general'" :class="{
                                'flex items-center px-4 py-3 text-sm font-medium rounded-lg mb-1 transition-colors': true,
                                'text-indigo-700 bg-indigo-50': activeSection === 'general',
                                'text-gray-600 hover:text-gray-900 hover:bg-gray-50': activeSection !== 'general'
                            }">
                                <i class="fas fa-cog w-5 h-5 mr-3"></i>
                                General Settings
                            </a>
                            <a href="#" @click.prevent="activeSection = 'account'" :class="{
                                'flex items-center px-4 py-3 text-sm font-medium rounded-lg mb-1 transition-colors': true,
                                'text-indigo-700 bg-indigo-50': activeSection === 'account',
                                'text-gray-600 hover:text-gray-900 hover:bg-gray-50': activeSection !== 'account'
                            }">
                                <i class="fas fa-user w-5 h-5 mr-3"></i>
                                Account Settings
                            </a>
                            <a href="#" @click.prevent="activeSection = 'preferences'" :class="{
                                'flex items-center px-4 py-3 text-sm font-medium rounded-lg mb-1 transition-colors': true,
                                'text-indigo-700 bg-indigo-50': activeSection === 'preferences',
                                'text-gray-600 hover:text-gray-900 hover:bg-gray-50': activeSection !== 'preferences'
                            }">
                                <i class="fas fa-sliders-h w-5 h-5 mr-3"></i>
                                Platform Preferences
                            </a>
                            <a href="#" @click.prevent="activeSection = 'notifications'" :class="{
                                'flex items-center px-4 py-3 text-sm font-medium rounded-lg mb-1 transition-colors': true,
                                'text-indigo-700 bg-indigo-50': activeSection === 'notifications',
                                'text-gray-600 hover:text-gray-900 hover:bg-gray-50': activeSection !== 'notifications'
                            }">
                                <i class="fas fa-bell w-5 h-5 mr-3"></i>
                                Notifications
                            </a>
                            <a href="#" @click.prevent="activeSection = 'privacy'" :class="{
                                'flex items-center px-4 py-3 text-sm font-medium rounded-lg mb-1 transition-colors': true,
                                'text-indigo-700 bg-indigo-50': activeSection === 'privacy',
                                'text-gray-600 hover:text-gray-900 hover:bg-gray-50': activeSection !== 'privacy'
                            }">
                                <i class="fas fa-lock w-5 h-5 mr-3"></i>
                                Privacy & Security
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Right Content Area -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- General Settings -->
                    <section v-if="activeSection === 'general'"
                        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-800">General Settings</h2>
                            <p class="text-sm text-gray-500 mt-1">Basic platform information and configurations</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Platform Name</label>
                                    <p class="text-sm text-gray-500">The name displayed to users throughout the platform
                                    </p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="text" v-model="settings.general.platformName"
                                        class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button @click="saveSetting('general', 'platformName')"
                                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                                        Save
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Default Currency</label>
                                    <p class="text-sm text-gray-500">Primary currency used for pricing and transactions
                                    </p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <select v-model="settings.general.defaultCurrency"
                                        class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="PHP">Philippine Peso (₱)</option>
                                        <option value="USD">US Dollar ($)</option>
                                        <option value="EUR">Euro (€)</option>
                                        <option value="GBP">British Pound (£)</option>
                                    </select>
                                    <button @click="saveSetting('general', 'defaultCurrency')"
                                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                                        Save
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Platform Description</label>
                                    <p class="text-sm text-gray-500">Brief description shown in search results and about
                                        pages</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <textarea v-model="settings.general.platformDescription"
                                        class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        rows="3"></textarea>
                                    <button @click="saveSetting('general', 'platformDescription')"
                                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Account Settings -->
                    <section v-if="activeSection === 'account'"
                        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-800">Account Settings</h2>
                            <p class="text-sm text-gray-500 mt-1">Manage your administrator account details</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Admin Email</label>
                                    <p class="text-sm text-gray-500">Primary email for account notifications and
                                        recovery
                                    </p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <input type="email" v-model="settings.account.adminEmail"
                                        class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button @click="saveSetting('account', 'adminEmail')"
                                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                                        Update
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Password</label>
                                    <p class="text-sm text-gray-500">Last changed 30 days ago</p>
                                </div>
                                <button
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors w-full md:w-auto">
                                    Change Password
                                </button>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Two-Factor
                                        Authentication</label>
                                    <p class="text-sm text-gray-500">Add an extra layer of security to your account</p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-medium"
                                        :class="settings.account.twoFactorEnabled ? 'text-green-600' : 'text-red-600'">
                                        {{ settings.account.twoFactorEnabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                    <button @click="toggleTwoFactor"
                                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors">
                                        {{ settings.account.twoFactorEnabled ? 'Disable' : 'Enable' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- System Preferences -->
                    <section v-if="activeSection === 'preferences'"
                        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-800">System Preferences</h2>
                            <p class="text-sm text-gray-500 mt-1">Configure platform behavior and features</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Email Notifications</label>
                                    <p class="text-sm text-gray-500">Receive updates about platform activities and
                                        reports
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="settings.preferences.emailNotifications"
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-500 relative transition-all">
                                        <div
                                            class="absolute left-1 top-1 bg-white h-4 w-4 rounded-full transition-all peer-checked:translate-x-5">
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Maintenance Mode</label>
                                    <p class="text-sm text-gray-500">Temporarily disable public access for maintenance
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="settings.preferences.maintenanceMode"
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-500 relative transition-all">
                                        <div
                                            class="absolute left-1 top-1 bg-white h-4 w-4 rounded-full transition-all peer-checked:translate-x-5">
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Auto-approve Vendors</label>
                                    <p class="text-sm text-gray-500">Automatically approve new vendor registrations</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="settings.preferences.autoApproveVendors"
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-500 relative transition-all">
                                        <div
                                            class="absolute left-1 top-1 bg-white h-4 w-4 rounded-full transition-all peer-checked:translate-x-5">
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Booking Confirmation
                                        Required</label>
                                    <p class="text-sm text-gray-500">Require vendors to confirm bookings manually</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="settings.preferences.bookingConfirmation"
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-500 relative transition-all">
                                        <div
                                            class="absolute left-1 top-1 bg-white h-4 w-4 rounded-full transition-all peer-checked:translate-x-5">
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </section>

                    <!-- Notifications Settings -->
                    <section v-if="activeSection === 'notifications'"
                        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-800">Notification Settings</h2>
                            <p class="text-sm text-gray-500 mt-1">Configure how and when you receive notifications</p>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Push Notifications</label>
                                    <p class="text-sm text-gray-500">Receive real-time alerts in your browser</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="settings.notifications.pushNotifications"
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-500 relative transition-all">
                                        <div
                                            class="absolute left-1 top-1 bg-white h-4 w-4 rounded-full transition-all peer-checked:translate-x-5">
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">SMS Notifications</label>
                                    <p class="text-sm text-gray-500">Receive important alerts via text message</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="settings.notifications.smsNotifications"
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-500 relative transition-all">
                                        <div
                                            class="absolute left-1 top-1 bg-white h-4 w-4 rounded-full transition-all peer-checked:translate-x-5">
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-gray-700 mb-1">Weekly Reports</label>
                                    <p class="text-sm text-gray-500">Receive a summary of platform activity every week
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="settings.notifications.weeklyReports"
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-500 relative transition-all">
                                        <div
                                            class="absolute left-1 top-1 bg-white h-4 w-4 rounded-full transition-all peer-checked:translate-x-5">
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </section>

                    <!-- Danger Zone -->
                    <section v-if="activeSection === 'privacy'"
                        class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden transition-all duration-300">
                        <div class="p-6 border-b border-red-100 bg-red-50">
                            <h2 class="text-lg font-semibold text-red-800">Danger Zone</h2>
                            <p class="text-sm text-red-600 mt-1">Irreversible and destructive actions</p>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-red-700 mb-1">Delete All Test Data</label>
                                    <p class="text-sm text-red-600">Permanently remove all test users, vendors, and
                                        events
                                    </p>
                                </div>
                                <button
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors w-full md:w-auto">
                                    Delete Test Data
                                </button>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    <label class="block font-medium text-red-700 mb-1">Reset Platform</label>
                                    <p class="text-sm text-red-600">Clear all data and restore to initial state</p>
                                </div>
                                <button
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors w-full md:w-auto">
                                    Reset Platform
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </TestLayout>
</template>

<style scoped>
/* Smooth transitions for interactive elements */
section {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

section:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Custom styling for the toggle switches */
input:checked+div {
    background-color: #4f46e5;
}

input:checked+div>div {
    transform: translateX(1.25rem);
}

/* Sticky navigation */
.sticky {
    position: sticky;
}
</style>
