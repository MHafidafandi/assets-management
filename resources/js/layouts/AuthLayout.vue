<script setup lang="ts">
import AuthSplitLayout from './auth/AuthSplitLayout.vue';

const { title = '', description = '' } = defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <auth-split-layout :title="title" :description="description">
        <slot />
    </auth-split-layout>
</template>
