<script setup lang="ts">
import { Form, usePoll } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import Button from './ui/button/Button.vue';
import Dialog from './ui/dialog/Dialog.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogDescription from './ui/dialog/DialogDescription.vue';
import DialogHeader from './ui/dialog/DialogHeader.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';
import DialogTrigger from './ui/dialog/DialogTrigger.vue';

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
    routeForm: any;
}>();

const isRunning = computed(
    () =>
        props.importJob &&
        ['queued', 'processing'].includes(props.importJob.status),
);

const showErrors = ref(false);
const open = ref(false);

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

function handleSuccess() {
    // We keep the dialog open to show progression/results of the import job
}
</script>

<template>
    <div class="flex items-center gap-md">
        <Dialog v-model:open="open">
            <DialogTrigger as-child>
                <Button
                    :variant="'outline'"
                    class="flex items-center gap-sm px-lg py-sm text-label-md transition-all duration-200 active:scale-95"
                >
                    <span>Import Excel</span>
                </Button>
            </DialogTrigger>
            <DialogContent
                class="fixed top-1/2 max-h-[85vh] overflow-y-auto sm:max-w-140"
            >
                <DialogHeader>
                    <DialogTitle>Import Data Excel</DialogTitle>
                    <DialogDescription>
                        Unggah file Excel (.xlsx atau .csv) untuk mengimport
                        data secara massal.
                    </DialogDescription>
                </DialogHeader>

                <Form
                    v-bind="props.routeForm"
                    enctype="multipart/form-data"
                    v-slot="{ errors, processing }"
                    @success="handleSuccess"
                    class="my-md space-y-md"
                >
                    <div class="flex flex-col gap-sm">
                        <label class="text-label-md text-on-surface"
                            >Pilih File</label
                        >
                        <input
                            type="file"
                            name="file"
                            accept=".xlsx,.csv"
                            class="file:text-label-sm w-full rounded-lg border border-outline-variant bg-surface px-md py-sm text-body-sm file:mr-md file:rounded-md file:border-0 file:bg-secondary file:px-md file:py-xs file:text-on-secondary hover:file:bg-secondary/85"
                            required
                        />
                        <p v-if="errors.file" class="text-xs text-destructive">
                            {{ errors.file }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-sm">
                        <Button
                            type="button"
                            variant="outline"
                            @click="open = false"
                        >
                            Tutup
                        </Button>
                        <Button
                            type="submit"
                            :disabled="processing || isRunning"
                        >
                            {{ isRunning ? 'Memproses...' : 'Mulai Import' }}
                        </Button>
                    </div>
                </Form>

                <!-- Status Log -->
                <div
                    v-if="props.importJob"
                    class="mt-md space-y-sm border-t border-outline-variant pt-md"
                >
                    <h4 class="text-label-md font-bold">
                        Status Import Terakhir
                    </h4>

                    <div
                        v-if="props.importJob.status === 'queued'"
                        class="flex animate-pulse items-center gap-sm text-body-sm text-on-surface-variant"
                    >
                        <span
                            class="size-2 rounded-full bg-surface-tint"
                        ></span>
                        <span>File masuk antrean...</span>
                    </div>

                    <div
                        v-if="props.importJob.status === 'processing'"
                        class="flex animate-pulse items-center gap-sm text-body-sm text-on-surface-variant"
                    >
                        <span class="size-2 rounded-full bg-secondary"></span>
                        <span>Sedang memproses baris data...</span>
                    </div>

                    <div
                        v-if="props.importJob.status === 'failed'"
                        class="rounded-lg bg-destructive/10 p-md text-body-sm text-destructive"
                    >
                        <b>Gagal total:</b> {{ props.importJob.error_message }}
                    </div>

                    <div
                        v-if="
                            props.importJob.status === 'completed' &&
                            props.importJob.summary
                        "
                        class="space-y-xs text-body-sm"
                    >
                        <p class="text-on-surface-variant">
                            Total baris:
                            <b>{{ props.importJob.summary.total_rows }}</b> —
                            Berhasil:
                            <b class="text-green-600">{{
                                props.importJob.summary.success
                            }}</b>
                            — Gagal:
                            <b class="text-destructive">{{
                                props.importJob.summary.failed
                            }}</b>
                        </p>

                        <button
                            v-if="props.importJob.summary.failed > 0"
                            @click="showErrors = !showErrors"
                            class="cursor-pointer text-xs font-medium text-primary underline"
                        >
                            {{ showErrors ? 'Sembunyikan' : 'Lihat' }} detail
                            baris gagal
                        </button>

                        <div
                            v-if="
                                showErrors &&
                                props.importJob.summary.errors.length > 0
                            "
                            class="max-h-48 overflow-x-auto rounded-lg border border-outline-variant"
                        >
                            <table
                                class="w-full border-collapse text-left text-xs"
                            >
                                <thead>
                                    <tr
                                        class="border-b border-outline-variant bg-surface-container-low font-bold text-on-surface-variant"
                                    >
                                        <th class="p-xs">Baris</th>
                                        <th class="p-xs">Kolom</th>
                                        <th class="p-xs">Alasan Gagal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(err, i) in props.importJob
                                            .summary.errors"
                                        :key="i"
                                        class="border-b border-outline-variant last:border-0 hover:bg-surface-container-lowest"
                                    >
                                        <td class="p-xs font-mono font-bold">
                                            {{ err.row }}
                                        </td>
                                        <td class="p-xs font-mono">
                                            {{ err.attribute }}
                                        </td>
                                        <td class="p-xs text-destructive">
                                            {{ err.errors.join(', ') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>
