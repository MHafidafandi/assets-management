<script setup lang="ts">
import { Form, usePoll } from '@inertiajs/vue3';
import { computed, watch, ref } from 'vue';
import Button from './ui/button/Button.vue';
import Dialog from './ui/dialog/Dialog.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogDescription from './ui/dialog/DialogDescription.vue';
import DialogFooter from './ui/dialog/DialogFooter.vue';
import DialogHeader from './ui/dialog/DialogHeader.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';
import DialogTrigger from './ui/dialog/DialogTrigger.vue';

const props = defineProps<{
    exportJob?: {
        id: number;
        status: 'queued' | 'processing' | 'completed' | 'failed';
        file_path?: string | null;
        error_message?: string | null;
    } | null;
    routeForm: any;
    availableFields: Record<string, string>;
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

const open = ref(false);
const selectedFields = ref<string[]>(Object.keys(props.availableFields));

function handleSuccess() {
    open.value = false;
}
</script>

<template>
    <div class="flex items-center gap-md">
        <Dialog v-model:open="open">
            <DialogTrigger as-child>
                <Button
                    :variant="'outline'"
                    :disabled="isRunning"
                    class="flex items-center gap-sm px-lg py-sm text-label-md transition-all duration-200 active:scale-95"
                >
                    <span v-if="isRunning">Memproses...</span>
                    <span v-else>Export Excel</span>
                </Button>
            </DialogTrigger>
            <DialogContent class="fixed top-1/2 sm:max-w-106.25">
                <DialogHeader>
                    <DialogTitle>Konfigurasi Export Excel</DialogTitle>
                    <DialogDescription>
                        Pilih kolom yang ingin disertakan dalam file Excel.
                    </DialogDescription>
                </DialogHeader>

                <Form
                    v-bind="props.routeForm"
                    v-slot="{ processing }"
                    @success="handleSuccess"
                >
                    <!-- Hidden fields to ensure they are submitted -->
                    <template
                        v-for="key in Object.keys(props.availableFields)"
                        :key="key"
                    >
                        <input
                            v-if="selectedFields.includes(key)"
                            type="hidden"
                            name="fields[]"
                            :value="key"
                        />
                    </template>

                    <div
                        class="my-md flex max-h-60 flex-col gap-sm overflow-y-auto pr-sm"
                    >
                        <label
                            v-for="(label, key) in props.availableFields"
                            :key="key"
                            class="flex cursor-pointer items-center gap-md rounded-lg border border-outline-variant p-md transition-colors hover:bg-surface-container-low"
                        >
                            <input
                                type="checkbox"
                                :value="key"
                                v-model="selectedFields"
                                class="size-4 rounded border-outline-variant text-primary focus:ring-primary"
                            />
                            <span class="text-body-md text-on-surface">{{
                                label
                            }}</span>
                        </label>
                    </div>

                    <DialogFooter class="gap-sm">
                        <Button
                            type="button"
                            variant="outline"
                            @click="open = false"
                        >
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            :disabled="
                                processing || selectedFields.length === 0
                            "
                        >
                            Mulai Export
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>

        <!-- Status / Download links -->
        <div v-if="props.exportJob" class="flex flex-col gap-xs">
            <span
                v-if="props.exportJob.status === 'queued'"
                class="inline-flex animate-pulse items-center gap-xs rounded-full bg-surface-container-high px-md py-xs text-xs font-medium text-on-surface-variant"
            >
                Antrean...
            </span>
            <span
                v-else-if="props.exportJob.status === 'processing'"
                class="inline-flex animate-pulse items-center gap-xs rounded-full bg-secondary-container px-md py-xs text-xs font-medium text-on-secondary-container"
            >
                Memproses...
            </span>
            <a
                v-else-if="props.exportJob.status === 'completed'"
                :href="`/storage/${props.exportJob.file_path}`"
                target="_blank"
                class="inline-flex items-center gap-xs rounded-full bg-primary-container px-md py-xs text-xs font-bold text-on-primary-container underline transition-colors hover:bg-primary/20"
            >
                Unduh File
            </a>
            <span
                v-else-if="props.exportJob.status === 'failed'"
                class="inline-flex items-center gap-xs rounded-full bg-destructive/10 px-md py-xs text-xs font-medium text-destructive"
                :title="props.exportJob.error_message || undefined"
            >
                Gagal
            </span>
        </div>
    </div>
</template>
