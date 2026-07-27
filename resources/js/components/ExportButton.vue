<script setup lang="ts">
import { Form, usePoll } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { store as exportAssets } from '@/routes/exports/assets'; // hasil generate Wayfinder
import Button from './ui/button/Button.vue';

const props = defineProps<{
    exportJob?: {
        id: number;
        status: 'queued' | 'processing' | 'completed' | 'failed';
        file_path?: string | null;
        error_message?: string | null;
    } | null;
}>();

const isRunning = computed(
    () =>
        props.exportJob &&
        ['queued', 'processing'].includes(props.exportJob.status),
);

const { start, stop } = usePoll(
    2000,
    { only: ['exportJob'] },
    { autoStart: false },
);

watch(
    isRunning,
    (running) => {
        if (running) {
            start();
        } else {
            stop();
        }
    },
    { immediate: true },
);
</script>

<template>
    <div class="flex flex-col gap-2">
        <Form v-bind="exportAssets.form()" v-slot="{ processing }">
            <Button
                type="submit"
                :variant="'secondary'"
                :disabled="processing || isRunning"
            >
                {{ isRunning ? 'Memproses...' : 'Export Excel' }}
            </Button>
        </Form>

        <p
            v-if="props.exportJob?.status === 'queued'"
            class="text-xs text-muted-foreground"
        >
            Job masuk antrian...
        </p>
        <p
            v-if="props.exportJob?.status === 'processing'"
            class="text-xs text-muted-foreground"
        >
            Sedang memproses file...
        </p>
        <a
            v-if="props.exportJob?.status === 'completed'"
            :href="`/storage/${props.exportJob.file_path}`"
            target="_blank"
            class="text-xs text-primary underline"
        >
            Download hasil export
        </a>
        <p
            v-if="props.exportJob?.status === 'failed'"
            class="text-xs text-destructive"
        >
            Gagal: {{ props.exportJob.error_message }}
        </p>
    </div>
</template>
