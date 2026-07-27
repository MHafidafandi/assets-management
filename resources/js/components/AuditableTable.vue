<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { 
    Clock, 
    User as UserIcon, 
    ChevronDown, 
    ChevronUp, 
    Activity, 
    Globe, 
    Info 
} from '@lucide/vue';
import Button from './ui/button/Button.vue';

const props = defineProps<{
    auditableType: string;
    auditableId: number;
}>();

interface AuditUser {
    id: number;
    name: string;
    email: string;
}

interface AuditRecord {
    id: number;
    event: 'created' | 'updated' | 'deleted' | string;
    user?: AuditUser;
    old_values: Record<string, any>;
    new_values: Record<string, any>;
    url?: string;
    ip_address?: string;
    user_agent?: string;
    created_at: string;
}

interface PaginatedResponse {
    data: AuditRecord[];
    current_page: number;
    last_page: number;
    total: number;
}

const audits = ref<AuditRecord[]>([]);
const currentPage = ref(1);
const lastPage = ref(1);
const totalAudits = ref(0);
const isLoading = ref(false);
const expandedAuditId = ref<number | null>(null);

const fetchAudits = async (page = 1) => {
    isLoading.value = true;
    try {
        const response = await fetch(`/audits?auditable_type=${props.auditableType}&auditable_id=${props.auditableId}&page=${page}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (response.ok) {
            const result: PaginatedResponse = await response.json();
            audits.value = result.data;
            currentPage.value = result.current_page;
            lastPage.value = result.last_page;
            totalAudits.value = result.total;
        }
    } catch (error) {
        console.error('Failed to fetch audit logs:', error);
    } finally {
        isLoading.value = false;
    }
};

const toggleExpand = (id: number) => {
    if (expandedAuditId.value === id) {
        expandedAuditId.value = null;
    } else {
        expandedAuditId.value = id;
    }
};

const getEventBadgeClass = (event: string) => {
    switch (event) {
        case 'created':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/50';
        case 'updated':
            return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/30 dark:text-blue-400 dark:border-blue-900/50';
        case 'deleted':
            return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900/50';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-800';
    }
};

const formatFieldLabel = (field: string) => {
    return field
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const formatValue = (value: any): string => {
    if (value === null || value === undefined) return '-';
    if (typeof value === 'boolean') return value ? 'Yes' : 'No';
    if (typeof value === 'object') return JSON.stringify(value);
    return String(value);
};

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleString('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short'
    });
};

// Compute changed fields to show in the summary
const getChangedFieldsSummary = (audit: AuditRecord) => {
    if (audit.event === 'created') {
        return Object.keys(audit.new_values).map(formatFieldLabel).join(', ');
    }
    if (audit.event === 'deleted') {
        return Object.keys(audit.old_values).map(formatFieldLabel).join(', ');
    }
    return Object.keys(audit.new_values).map(formatFieldLabel).join(', ');
};

onMounted(() => {
    fetchAudits();
});

watch(() => props.auditableId, () => {
    fetchAudits(1);
});
</script>

<template>
    <div class="w-full rounded-2xl border border-neutral-200/80 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900 overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-neutral-100 bg-neutral-50/50 px-6 py-4 dark:border-neutral-800 dark:bg-neutral-950/20">
            <div class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-300">
                    <Activity class="h-4.5 w-4.5" />
                </div>
                <div>
                    <h3 class="font-semibold text-neutral-900 dark:text-white text-base">Audit Trail / History</h3>
                    <p class="text-xs text-neutral-500">Track and view changes made to this record over time.</p>
                </div>
            </div>
            <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300">
                {{ totalAudits }} Changes
            </span>
        </div>

        <!-- Content -->
        <div v-if="isLoading" class="p-8 flex flex-col items-center justify-center gap-3">
            <div class="h-8 w-8 animate-spin rounded-full border-3 border-neutral-200 border-t-neutral-600"></div>
            <span class="text-sm text-neutral-500">Loading audit history...</span>
        </div>

        <div v-else-if="audits.length === 0" class="p-12 flex flex-col items-center justify-center text-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-50 dark:bg-neutral-800 text-neutral-400 mb-3">
                <Info class="h-6 w-6" />
            </div>
            <h4 class="text-sm font-semibold text-neutral-900 dark:text-white">No history found</h4>
            <p class="text-xs text-neutral-500 max-w-xs mt-1">There are no audit logs recorded for this record yet.</p>
        </div>

        <div v-else class="divide-y divide-neutral-100 dark:divide-neutral-800">
            <div v-for="audit in audits" :key="audit.id" class="transition-colors hover:bg-neutral-50/30 dark:hover:bg-neutral-950/10">
                <!-- Summary Row -->
                <div 
                    @click="toggleExpand(audit.id)"
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-4.5 cursor-pointer select-none"
                >
                    <div class="flex items-start gap-4">
                        <div class="mt-1">
                            <span 
                                class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-semibold capitalize"
                                :class="getEventBadgeClass(audit.event)"
                            >
                                {{ audit.event }}
                            </span>
                        </div>
                        
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-neutral-900 dark:text-white">
                                <span class="text-neutral-500">Changed:</span> {{ getChangedFieldsSummary(audit) }}
                            </p>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-neutral-500">
                                <span class="flex items-center gap-1">
                                    <UserIcon class="h-3.5 w-3.5" />
                                    {{ audit.user?.name || 'System' }} ({{ audit.user?.email || 'system@internal' }})
                                </span>
                                <span class="flex items-center gap-1">
                                    <Clock class="h-3.5 w-3.5" />
                                    {{ formatDate(audit.created_at) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-3 self-stretch sm:self-auto border-t sm:border-0 pt-3 sm:pt-0 border-neutral-100 dark:border-neutral-800">
                        <div class="flex items-center gap-1.5 text-xs text-neutral-400 dark:text-neutral-500">
                            <Globe class="h-3.5 w-3.5" />
                            <span>{{ audit.ip_address || '127.0.0.1' }}</span>
                        </div>
                        <button class="rounded-lg p-1.5 text-neutral-400 hover:bg-neutral-100 hover:text-neutral-700 dark:hover:bg-neutral-800 dark:hover:text-neutral-200 transition-colors">
                            <ChevronDown v-if="expandedAuditId !== audit.id" class="h-4.5 w-4.5" />
                            <ChevronUp v-else class="h-4.5 w-4.5" />
                        </button>
                    </div>
                </div>

                <!-- Expanded Details (Diff) -->
                <div v-if="expandedAuditId === audit.id" class="bg-neutral-50/50 dark:bg-neutral-950/20 border-t border-neutral-100 dark:border-neutral-800/60 px-6 py-5">
                    <div class="rounded-xl border border-neutral-200/60 dark:border-neutral-800/80 bg-white dark:bg-neutral-900 overflow-hidden shadow-sm">
                        <table class="w-full border-collapse text-left text-sm">
                            <thead>
                                <tr class="bg-neutral-50 border-b border-neutral-200 dark:bg-neutral-950/40 dark:border-neutral-800">
                                    <th class="px-4 py-3 font-semibold text-neutral-700 dark:text-neutral-300 w-1/3">Field</th>
                                    <th class="px-4 py-3 font-semibold text-neutral-700 dark:text-neutral-300 w-1/3">Old Value</th>
                                    <th class="px-4 py-3 font-semibold text-neutral-700 dark:text-neutral-300 w-1/3">New Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/60">
                                <!-- When created: only new values -->
                                <template v-if="audit.event === 'created'">
                                    <tr v-for="(val, key) in audit.new_values" :key="key" class="hover:bg-neutral-50/20 dark:hover:bg-neutral-800/20">
                                        <td class="px-4 py-3 font-medium text-neutral-800 dark:text-neutral-200">{{ formatFieldLabel(String(key)) }}</td>
                                        <td class="px-4 py-3 text-neutral-400 italic">-</td>
                                        <td class="px-4 py-3 text-emerald-600 dark:text-emerald-400 bg-emerald-50/20 dark:bg-emerald-950/10 font-medium">
                                            {{ formatValue(val) }}
                                        </td>
                                    </tr>
                                </template>

                                <!-- When deleted: only old values -->
                                <template v-else-if="audit.event === 'deleted'">
                                    <tr v-for="(val, key) in audit.old_values" :key="key" class="hover:bg-neutral-50/20 dark:hover:bg-neutral-800/20">
                                        <td class="px-4 py-3 font-medium text-neutral-800 dark:text-neutral-200">{{ formatFieldLabel(String(key)) }}</td>
                                        <td class="px-4 py-3 text-rose-600 dark:text-rose-400 bg-rose-50/20 dark:bg-rose-950/10 font-medium line-through">
                                            {{ formatValue(val) }}
                                        </td>
                                        <td class="px-4 py-3 text-neutral-400 italic">-</td>
                                    </tr>
                                </template>

                                <!-- When updated: show old and new differences -->
                                <template v-else>
                                    <tr v-for="(newVal, key) in audit.new_values" :key="key" class="hover:bg-neutral-50/20 dark:hover:bg-neutral-800/20">
                                        <td class="px-4 py-3 font-medium text-neutral-800 dark:text-neutral-200">{{ formatFieldLabel(String(key)) }}</td>
                                        <td class="px-4 py-3 text-rose-600 dark:text-rose-400 bg-rose-50/20 dark:bg-rose-950/10 font-medium">
                                            {{ formatValue(audit.old_values[key]) }}
                                        </td>
                                        <td class="px-4 py-3 text-emerald-600 dark:text-emerald-400 bg-emerald-50/20 dark:bg-emerald-950/10 font-medium">
                                            {{ formatValue(newVal) }}
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Metadata info -->
                    <div class="mt-3.5 flex flex-wrap gap-4 text-xs text-neutral-400">
                        <span v-if="audit.url"><strong>URL:</strong> {{ audit.url }}</span>
                        <span v-if="audit.user_agent"><strong>Agent:</strong> {{ audit.user_agent }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination Footer -->
        <div v-if="lastPage > 1" class="flex items-center justify-between border-t border-neutral-100 bg-neutral-50/50 px-6 py-4 dark:border-neutral-800 dark:bg-neutral-950/20">
            <button 
                @click="fetchAudits(currentPage - 1)" 
                :disabled="currentPage === 1"
                class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-white px-3.5 py-2 text-xs font-semibold text-neutral-700 shadow-sm transition-colors hover:bg-neutral-50 disabled:opacity-50 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800"
            >
                Previous
            </button>
            <span class="text-xs text-neutral-500 font-medium">
                Page {{ currentPage }} of {{ lastPage }}
            </span>
            <button 
                @click="fetchAudits(currentPage + 1)" 
                :disabled="currentPage === lastPage"
                class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-white px-3.5 py-2 text-xs font-semibold text-neutral-700 shadow-sm transition-colors hover:bg-neutral-50 disabled:opacity-50 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800"
            >
                Next
            </button>
        </div>
    </div>
</template>
