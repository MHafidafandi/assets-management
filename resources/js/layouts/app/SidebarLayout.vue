<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import { Toaster } from '@/components/ui/sonner';
</script>

<template>
    <AppShell variant="sidebar" class="overflow-x-hidden">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppHeader />
            <slot />
        </AppContent>
        <Toaster />
    </AppShell>
</template>
