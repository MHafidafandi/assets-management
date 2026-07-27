<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Car, ShelvingUnit, Armchair, Cable } from '@lucide/vue';
import { ref, onMounted } from 'vue';

const isMounted = ref(false);

onMounted(() => {
    isMounted.value = true;
});
</script>

<template>
    <Head title="Homepage" />
    <body
        v-if="isMounted"
        class="font-body-md flex min-h-screen flex-col bg-background text-on-background"
    >
        <header
            class="fixed top-0 z-50 h-16 w-full border-b border-outline-variant bg-surface shadow-sm transition-opacity duration-200"
        >
            <div
                class="max-w-container-max mx-auto flex h-full items-center justify-between px-lg"
            >
                <div class="flex items-center gap-md">
                    <img
                        alt="AssetFlow Logo"
                        class="h-8 w-8 rounded-sm object-contain"
                        src="/assetflow_logo.png"
                    />
                    <span
                        class="font-headline-sm text-headline-sm font-bold text-primary"
                        >Sistem Manajemen Aset Kantor</span
                    >
                </div>
                <nav class="hidden items-center gap-lg md:flex">
                    <a
                        class="font-label-md border-b-2 border-primary pb-1 text-label-md font-bold text-primary"
                        href="#"
                        >Home</a
                    >
                    <a
                        class="font-label-md cursor-pointer text-label-md text-on-surface-variant transition-colors hover:text-primary"
                        href="#features"
                        >Features</a
                    >
                    <a
                        class="font-label-md cursor-pointer text-label-md text-on-surface-variant transition-colors hover:text-primary"
                        href="#support"
                        >Support</a
                    >
                    <button
                        class="font-label-md ml-md rounded-lg bg-primary px-md py-sm text-label-md text-on-primary transition-colors hover:bg-surface-tint"
                        @click="router.visit('/login')"
                    >
                        Login
                    </button>
                </nav>
            </div>
        </header>
        <main class="grow pt-16">
            <section
                class="max-w-container-max mx-auto mt-xl flex flex-col items-center gap-xl px-lg py-xl md:flex-row"
            >
                <div class="flex-1 space-y-md">
                    <h1 class="font-headline-lg text-headline-lg text-primary">
                        Efficient Office Asset Tracking, Simplified.
                    </h1>
                    <p
                        class="font-body-lg max-w-2xl text-body-lg text-on-surface-variant"
                    >
                        Streamline your enterprise operations with AssetFlow.
                        Gain complete visibility over your organization's
                        physical assets, from acquisition to retirement. Ensure
                        compliance, reduce loss, and optimize resource
                        allocation with our enterprise-grade management system.
                    </p>
                    <div class="pt-sm">
                        <button
                            class="font-label-md rounded-lg bg-primary px-lg py-md text-label-md text-on-primary transition-colors hover:bg-surface-tint"
                        >
                            Request Demo
                        </button>
                        <button
                            class="font-label-md ml-sm rounded-lg border border-outline px-lg py-md text-label-md text-primary transition-colors hover:bg-surface-container-high"
                        >
                            Learn More
                        </button>
                    </div>
                </div>
                <div
                    class="relative h-100 w-full flex-1 overflow-hidden rounded-xl border border-outline-variant bg-surface-container shadow-sm"
                >
                    <img
                        class="h-full w-full object-cover"
                        data-alt="A modern, high-tech dashboard interface displayed on a sleek desktop monitor in a bright, minimalist corporate office. The screen shows data charts, asset tracking grids, and status indicators in a clean, light-mode design with a corporate blue palette. The setting is professional, conveying efficiency and precision. Bright, soft natural light fills the room."
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAWN9NGgCtBER5CS_TAq0MKzE3h5lzREkTGWYkutrHBqw4QrQY4elqReEhOJA6HR0AzounLi7KE5k5uBrWrrUOyrEabuWilRth14JwCBv4RnyWVrO4-eCzNeK-2q-b2xd3HStko2mZxZA-2iIsjsrg4twTcXjpJDYdMja3iRlYi6mAia1ti49tdRNxgNVsqAZ6B5O9fEbDr0QH98BUpGMXGJ_TPzehTSRZL9tVdJmsGwqZg99oy7v6x7mUciSAIjUftkx42Gdl788Nb"
                    />
                    <div
                        class="absolute inset-0 bg-linear-to-tr from-primary/10 to-transparent"
                    ></div>
                </div>
            </section>
            <section
                class="border-t border-b border-outline-variant bg-surface-container-low py-xl"
                id="features"
            >
                <div class="max-w-container-max mx-auto px-lg">
                    <div class="mb-xl text-center">
                        <h2
                            class="font-headline-md mb-sm text-headline-md text-primary"
                        >
                            Comprehensive Asset Categories
                        </h2>
                        <p
                            class="font-body-md text-body-md text-on-surface-variant"
                        >
                            Manage every asset class efficiently within a
                            unified ecosystem.
                        </p>
                    </div>
                    <div
                        class="grid grid-cols-1 gap-lg md:grid-cols-2 lg:grid-cols-4"
                    >
                        <div
                            class="group cursor-pointer rounded-xl border border-outline-variant bg-surface p-lg transition-shadow hover:shadow-md"
                        >
                            <div
                                class="mb-md flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-container transition-colors group-hover:bg-primary-container"
                            >
                                <span
                                    class="material-symbols-outlined text-on-secondary-container group-hover:text-on-primary-container"
                                    style="font-variation-settings: 'FILL' 1"
                                    ><Cable
                                /></span>
                            </div>
                            <h3
                                class="font-headline-sm mb-xs text-headline-sm text-primary"
                            >
                                Electronic
                            </h3>
                            <p
                                class="font-body-sm text-body-sm text-on-surface-variant"
                            >
                                Track laptops, monitors, servers, and network
                                equipment with detailed specs and warranty info.
                            </p>
                        </div>
                        <div
                            class="group cursor-pointer rounded-xl border border-outline-variant bg-surface p-lg transition-shadow hover:shadow-md"
                        >
                            <div
                                class="mb-md flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-container transition-colors group-hover:bg-primary-container"
                            >
                                <span
                                    class="material-symbols-outlined text-on-secondary-container group-hover:text-on-primary-container"
                                    style="font-variation-settings: 'FILL' 1"
                                    ><Armchair
                                /></span>
                            </div>
                            <h3
                                class="font-headline-sm mb-xs text-headline-sm text-primary"
                            >
                                Furniture
                            </h3>
                            <p
                                class="font-body-sm text-body-sm text-on-surface-variant"
                            >
                                Manage office desks, ergonomic chairs, and
                                storage units across multiple floor plans.
                            </p>
                        </div>
                        <div
                            class="group cursor-pointer rounded-xl border border-outline-variant bg-surface p-lg transition-shadow hover:shadow-md"
                        >
                            <div
                                class="mb-md flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-container transition-colors group-hover:bg-primary-container"
                            >
                                <span
                                    class="material-symbols-outlined text-on-secondary-container group-hover:text-on-primary-container"
                                    style="font-variation-settings: 'FILL' 1"
                                    ><ShelvingUnit
                                /></span>
                            </div>
                            <h3
                                class="font-headline-sm mb-xs text-headline-sm text-primary"
                            >
                                Stationery
                            </h3>
                            <p
                                class="font-body-sm text-body-sm text-on-surface-variant"
                            >
                                Monitor consumable inventory levels and automate
                                reordering processes for office supplies.
                            </p>
                        </div>
                        <div
                            class="group cursor-pointer rounded-xl border border-outline-variant bg-surface p-lg transition-shadow hover:shadow-md"
                        >
                            <div
                                class="mb-md flex h-12 w-12 items-center justify-center rounded-lg bg-secondary-container transition-colors group-hover:bg-primary-container"
                            >
                                <span
                                    class="material-symbols-outlined text-on-secondary-container group-hover:text-on-primary-container"
                                    style="font-variation-settings: 'FILL' 1"
                                    ><Car
                                /></span>
                            </div>
                            <h3
                                class="font-headline-sm mb-xs text-headline-sm text-primary"
                            >
                                Vehicle
                            </h3>
                            <p
                                class="font-body-sm text-body-sm text-on-surface-variant"
                            >
                                Oversee corporate fleet, schedule maintenance,
                                and track usage logs for company vehicles.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <footer class="bg-inverse-surface py-xl text-inverse-on-surface">
            <div
                class="max-w-container-max mx-auto flex flex-col items-center justify-between gap-md px-lg md:flex-row"
            >
                <div class="flex items-center gap-sm">
                    <img
                        alt="AssetFlow Logo"
                        class="h-6 w-6 object-contain opacity-70 grayscale"
                        src="/assetflow_logo.png"
                    />
                    <span
                        class="font-label-md text-label-md font-bold text-inverse-on-surface opacity-70"
                        >© 2026 AssetFlow. All rights reserved.</span
                    >
                </div>
                <div class="flex gap-lg">
                    <a
                        class="font-body-sm text-body-sm text-inverse-on-surface opacity-70 transition-opacity hover:opacity-100"
                        href="#"
                        >Privacy Policy</a
                    >
                    <a
                        class="font-body-sm text-body-sm text-inverse-on-surface opacity-70 transition-opacity hover:opacity-100"
                        href="#"
                        >Terms of Service</a
                    >
                    <a
                        class="font-body-sm text-body-sm text-inverse-on-surface opacity-70 transition-opacity hover:opacity-100"
                        href="#"
                        >Contact Support</a
                    >
                </div>
            </div>
        </footer>
    </body>
</template>
