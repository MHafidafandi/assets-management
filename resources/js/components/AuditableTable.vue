<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const props = defineProps<{
    auditableType: string;
}>();

interface AuditUser {
    id: number;
    name: string;
    email: string;
}

interface AuditRecord {
    id: number;
    event: string;
    user?: AuditUser;
    old_values: Record<string, any>;
    new_values: Record<string, any>;
    created_at: string;
}

const page = usePage();
const isLoading = ref(false);

// Access audits from Inertia shared props
const audits = computed<AuditRecord[]>(() => {
    const auditsProp = page.props.audits as any;
    return auditsProp?.data || [];
});

const currentPage = computed(() => {
    const auditsProp = page.props.audits as any;
    return auditsProp?.current_page || 1;
});

const lastPage = computed(() => {
    const auditsProp = page.props.audits as any;
    return auditsProp?.last_page || 1;
});

const loadAudits = (pageNumber = 1) => {
    if (!props.auditableType) return;
    isLoading.value = true;
    router.reload({
        only: ['audits'],
        data: {
            auditable_type: props.auditableType,
            page: pageNumber
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

// Watch for Type changes to reload audits using Inertia
watch(() => props.auditableType, (newType) => {
    if (newType) {
        loadAudits(1);
    }
}, { immediate: true });

const formatFieldLabel = (field: string) => {
    return field
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const formatDate = (dateStr: string) => {
    const d = new Date(dateStr);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    const seconds = String(d.getSeconds()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
};

const getEventLabel = (event: string) => {
    return event.charAt(0).toUpperCase() + event.slice(1);
};

const getChangesNote = (audit: AuditRecord) => {
    if (audit.event === 'created') {
        return 'Created record';
    }
    if (audit.event === 'deleted') {
        return 'Deleted record';
    }
    const changedFields = Object.keys(audit.new_values).map(formatFieldLabel).join(', ');
    return `Updated ${changedFields}`;
};
</script>

<template>
    <div class="mt-6 w-full space-y-3">
        <h4 class="text-sm font-bold text-neutral-800 dark:text-neutral-250">History & Note</h4>
        
        <div class="overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-800">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-neutral-50 dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800 text-neutral-600 dark:text-neutral-400 font-semibold">
                        <th class="px-4 py-2.5 w-1/4">Date</th>
                        <th class="px-4 py-2.5 w-1/6">Action</th>
                        <th class="px-4 py-2.5 w-1/4">User</th>
                        <th class="px-4 py-2.5">Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-150 dark:divide-neutral-800/60 bg-white dark:bg-neutral-950">
                    <tr v-if="isLoading">
                        <td colspan="4" class="px-4 py-6 text-center text-neutral-400">Loading audit history...</td>
                    </tr>
                    <tr v-else-if="audits.length === 0">
                        <td colspan="4" class="px-4 py-6 text-center text-neutral-400">No history available for this table.</td>
                    </tr>
                    <tr v-else v-for="audit in audits" :key="audit.id" class="hover:bg-neutral-50/50 dark:hover:bg-neutral-900/50 text-neutral-700 dark:text-neutral-300">
                        <td class="px-4 py-2.5 font-mono whitespace-nowrap">{{ formatDate(audit.created_at) }}</td>
                        <td class="px-4 py-2.5">
                            <span class="font-medium" :class="{
                                'text-emerald-600': audit.event === 'created',
                                'text-blue-600': audit.event === 'updated',
                                'text-rose-600': audit.event === 'deleted'
                            }">
                                {{ getEventLabel(audit.event) }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 uppercase font-medium">{{ audit.user?.name || 'SYSTEM' }}</td>
                        <td class="px-4 py-2.5 truncate max-w-xs md:max-w-none" :title="getChangesNote(audit)">{{ getChangesNote(audit) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mini Pagination -->
        <div v-if="lastPage > 1" class="flex items-center justify-between pt-1">
            <button 
                @click="loadAudits(currentPage - 1)" 
                :disabled="currentPage === 1 || isLoading"
                class="px-2 py-1 text-[11px] font-semibold border rounded bg-white hover:bg-neutral-50 disabled:opacity-50 dark:bg-neutral-900 dark:border-neutral-800 dark:text-neutral-300"
            >
                Prev
            </button>
            <span class="text-[11px] text-neutral-500">
                Page {{ currentPage }} of {{ lastPage }}
            </span>
            <button 
                @click="loadAudits(currentPage + 1)" 
                :disabled="currentPage === lastPage || isLoading"
                class="px-2 py-1 text-[11px] font-semibold border rounded bg-white hover:bg-neutral-50 disabled:opacity-50 dark:bg-neutral-900 dark:border-neutral-800 dark:text-neutral-300"
            >
                Next
            </button>
        </div>
    </div>
</template>
