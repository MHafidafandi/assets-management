<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Home, ArrowLeft } from '@lucide/vue';

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <div
        class="relative flex min-h-screen flex-col items-center justify-center bg-background p-4"
    >
        <Head title="404 - Page Not Found" />

        <!-- Background Decoration -->
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div
                class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-primary/5 blur-3xl"
            />
            <div
                class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-primary/5 blur-3xl"
            />
        </div>

        <!-- Content -->
        <div class="mx-auto max-w-2xl text-center">
            <!-- 404 Number -->
            <div class="relative">
                <div
                    class="text-[150px] leading-none font-bold tracking-tighter text-primary/10 sm:text-[200px]"
                >
                    404
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="rounded-full bg-primary p-4 shadow-lg">
                        <svg
                            class="h-16 w-16 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Title & Description -->
            <h1 class="mt-8 text-3xl font-bold text-foreground sm:text-4xl">
                Oops! Page Not Found
            </h1>
            <p class="mt-4 text-lg text-muted-foreground">
                The page you are looking for might have been removed, had its
                name changed, or is temporarily unavailable.
            </p>

            <!-- Action Buttons -->
            <div
                class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row"
            >
                <Link
                    href="/"
                    class="inline-flex items-center gap-2 rounded-md bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-sm transition-colors hover:bg-primary/90 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                >
                    <Home class="h-4 w-4" />
                    Back to Home
                </Link>
                <button
                    @click="goBack"
                    class="inline-flex items-center gap-2 rounded-md border border-input bg-background px-6 py-3 text-sm font-semibold shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Go Back
                </button>
            </div>

            <!-- Help Links -->
            <div class="mt-8 text-sm text-muted-foreground">
                Need help?
                <a href="/support" class="text-primary hover:underline">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</template>
