<script setup lang="ts">
import { Form, usePoll } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { store as importAssets } from '@/routes/imports/assets'; // hasil generate Wayfinder
import Button from './ui/button/Button.vue';

const props = defineProps<{
    importJob?: {
        id: number;
        status: 'queued' | 'processing' | 'completed' | 'failed';
        error_message?: string | null;
        summary?: {
            total_rows: number;
            success: number;
            failed: number;
            errors: { row: number; attribute: string; errors: string[] }[];
        } | null;
    } | null;
}>();

const isRunning = computed(
    () =>
        props.importJob &&
        ['queued', 'processing'].includes(props.importJob.status),
);

const showErrors = ref(false);

const { start, stop } = usePoll(
    2000,
    { only: ['importJob'] },
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
    <div class="flex flex-col gap-2 rounded border p-3">
        <Form
            v-bind="importAssets.form()"
            enctype="multipart/form-data"
            v-slot="{ errors, processing }"
        >
            <div class="flex items-center gap-2">
                <input
                    type="file"
                    name="file"
                    accept=".xlsx,.csv"
                    class="text-sm"
                    required
                />
                <Button type="submit" :disabled="processing || isRunning">
                    {{ isRunning ? 'Memproses...' : 'Import Excel' }}
                </Button>
            </div>
            <p v-if="errors.file" class="text-xs text-destructive">
                {{ errors.file }}
            </p>
        </Form>

        <p
            v-if="importJob?.status === 'queued'"
            class="text-xs text-muted-foreground"
        >
            File masuk antrian...
        </p>
        <p
            v-if="importJob?.status === 'processing'"
            class="text-xs text-muted-foreground"
        >
            Sedang memproses baris data...
        </p>
        <p
            v-if="importJob?.status === 'failed'"
            class="text-xs text-destructive"
        >
            Gagal total: {{ importJob.error_message }}
        </p>

        <div
            v-if="importJob?.status === 'completed' && importJob.summary"
            class="text-xs"
        >
            <p>
                Total baris: <b>{{ importJob.summary.total_rows }}</b> —
                Berhasil:
                <b class="text-green-600">{{ importJob.summary.success }}</b> —
                Gagal:
                <b class="text-destructive">{{ importJob.summary.failed }}</b>
            </p>

            <button
                v-if="importJob.summary.failed > 0"
                @click="showErrors = !showErrors"
                class="mt-1 text-primary underline"
            >
                {{ showErrors ? 'Sembunyikan' : 'Lihat' }} detail baris gagal
            </button>

            <table v-if="showErrors" class="mt-2 w-full border text-xs">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-1">Baris</th>
                        <th class="border p-1">Kolom</th>
                        <th class="border p-1">Alasan Gagal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(err, i) in importJob.summary.errors" :key="i">
                        <td class="border p-1">{{ err.row }}</td>
                        <td class="border p-1">{{ err.attribute }}</td>
                        <td class="border p-1">{{ err.errors.join(', ') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
