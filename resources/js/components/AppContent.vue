<script setup lang="ts">
import { computed } from 'vue';
import SidebarInset from './ui/sidebar/SidebarInset.vue';

type Props = {
    variant?: 'sidebar' | 'header';
    class?: string;
};

const props = withDefaults(defineProps<Props>(), {
    variant: 'sidebar',
});
const className = computed(() => props.class);
</script>

<template>
    <SidebarInset
        v-if="props.variant === 'sidebar'"
        :class="`relative ${className}`"
    >
        <slot />
    </SidebarInset>
    <main
        v-else
        class="relative mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl"
        :class="className"
    >
        <slot />
    </main>
</template>
