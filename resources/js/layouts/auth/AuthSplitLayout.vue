<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
// import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();
const name = page.props.name;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0"
    >
        <div
            class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-r"
        >
            <img
                src="/bg-auth.webp"
                alt="Logo"
                class="absolute inset-0 h-full w-full object-cover"
            />
            <div class="absolute inset-0 bg-zinc-950/50" />
            <Link
                href="/"
                class="relative z-20 flex items-center text-lg font-medium"
            >
                <img
                    src="/assetflow_logo.png"
                    alt="Logo"
                    class="mr-2 h-8 w-8"
                />
                {{ name }}
            </Link>
        </div>
        <div class="lg:p-8">
            <div
                class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-87.5"
            >
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">
                        {{ title }}
                    </h1>
                    <p class="text-muted-foreground text-sm" v-if="description">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
