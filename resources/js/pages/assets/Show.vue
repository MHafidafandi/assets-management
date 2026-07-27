<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { 
    ChevronLeft, 
    Calendar, 
    FileText, 
    Tag, 
    Layers, 
    Database, 
    User, 
    CheckCircle2, 
    XCircle, 
    Clock, 
    Paperclip, 
    Download 
} from '@lucide/vue';
import AuditableTable from '@/components/AuditableTable.vue';
import type { Asset } from '@/types/asset';

const props = defineProps<{
    asset: any; // Using any or matching structural type of asset loaded in show method
}>();

const formatValue = (value: any): string => {
    if (value === null || value === undefined) return '-';
    if (typeof value === 'boolean') return value ? 'Yes' : 'No';
    if (typeof value === 'object') return JSON.stringify(value);
    return String(value);
};

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <Head :title="`Asset Detail - ${asset.name}`" />

    <div class="flex w-full flex-col gap-8 p-8">
        <!-- Header & Breadcrumbs -->
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-sm text-neutral-500">
                    <Link href="/assets" class="hover:text-primary transition-colors flex items-center gap-1">
                        <ChevronLeft class="h-4 w-4" />
                        Back to Assets
                    </Link>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-neutral-900 dark:text-white">
                    {{ asset.name }}
                </h1>
            </div>

            <!-- Action Status Badge -->
            <div class="flex items-center gap-3">
                <span 
                    v-if="asset.is_active" 
                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/50"
                >
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Active
                </span>
                <span 
                    v-else 
                    class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-3.5 py-1.5 text-xs font-bold text-neutral-600 border border-neutral-200 dark:bg-neutral-850 dark:text-neutral-400 dark:border-neutral-800"
                >
                    <span class="h-2 w-2 rounded-full bg-neutral-400"></span>
                    Inactive
                </span>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Left Info Panel (1 Column) -->
            <div class="space-y-8 lg:col-span-1">
                <!-- Thumbnail Card -->
                <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <div class="aspect-square w-full overflow-hidden rounded-xl border border-neutral-100 bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-950/50 flex items-center justify-center">
                        <img 
                            v-if="asset.thumbnail"
                            :src="`/storage/${asset.thumbnail}`" 
                            alt="Asset Thumbnail"
                            class="h-full w-full object-cover transition-transform duration-300 hover:scale-105"
                        />
                        <div v-else class="text-neutral-400 dark:text-neutral-600 flex flex-col items-center gap-2">
                            <Layers class="h-16 w-16 stroke-1" />
                            <span class="text-xs text-neutral-500 font-medium">No Image Uploaded</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Details Card -->
                <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white uppercase tracking-wider mb-4">Quick Facts</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-neutral-100 text-neutral-500 dark:bg-neutral-800">
                                <Tag class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500">Category</p>
                                <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ asset.category?.name || 'Uncategorized' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-neutral-100 text-neutral-500 dark:bg-neutral-800">
                                <Calendar class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500">Purchase Date</p>
                                <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ formatDate(asset.purchase_date) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-neutral-100 text-neutral-500 dark:bg-neutral-800">
                                <Database class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-xs text-neutral-500">System UUID</p>
                                <p class="text-xs font-mono text-neutral-600 dark:text-neutral-400 break-all select-all">{{ asset.uuid }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right details and tables (2 Columns) -->
            <div class="space-y-8 lg:col-span-2">
                <!-- Specifications Section -->
                <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <h3 class="text-lg font-bold text-neutral-900 dark:text-white mb-4">Specifications</h3>
                    <div v-if="asset.specification && Object.keys(asset.specification).length > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div 
                            v-for="(val, key) in asset.specification" 
                            :key="key"
                            class="rounded-xl border border-neutral-100 bg-neutral-50/50 p-4 dark:border-neutral-800/80 dark:bg-neutral-950/20"
                        >
                            <p class="text-xs font-medium text-neutral-500 capitalize">{{ String(key).replace('_', ' ') }}</p>
                            <p class="text-sm font-semibold text-neutral-800 dark:text-neutral-200 mt-1">{{ formatValue(val) }}</p>
                        </div>
                    </div>
                    <div v-else class="text-center py-6 text-neutral-500 text-sm">
                        No custom specifications set for this asset.
                    </div>
                </div>

                <!-- Attachment Card -->
                <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <h3 class="text-lg font-bold text-neutral-900 dark:text-white mb-4">Attachment Document</h3>
                    <div v-if="asset.attachment" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-xl border border-neutral-100 bg-neutral-50/50 dark:border-neutral-800 dark:bg-neutral-950/20">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400">
                                <FileText class="h-6 w-6" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-neutral-850 dark:text-neutral-200 line-clamp-1">
                                    {{ asset.attachment.file_name }}
                                </p>
                                <p class="text-xs text-neutral-500">
                                    {{ asset.attachment.file_size }} KB &middot; {{ asset.attachment.mime_type }}
                                </p>
                            </div>
                        </div>
                        <a 
                            :href="`/storage/${asset.attachment.file_path}`" 
                            target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-neutral-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm hover:bg-neutral-800 transition-colors"
                        >
                            <Download class="h-4 w-4" />
                            Download PDF
                        </a>
                    </div>
                    <div v-else class="text-center py-6 text-neutral-500 text-sm flex flex-col items-center gap-2">
                        <Paperclip class="h-8 w-8 text-neutral-400 stroke-1" />
                        <span>No PDF attachment found.</span>
                    </div>
                </div>

                <!-- Lending/Transactions History -->
                <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <h3 class="text-lg font-bold text-neutral-900 dark:text-white mb-4">Lending / Transaction History</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-neutral-200 dark:border-neutral-800 pb-2">
                                    <th class="py-2.5 font-semibold text-neutral-700 dark:text-neutral-300">Borrower</th>
                                    <th class="py-2.5 font-semibold text-neutral-700 dark:text-neutral-300">Borrowed At</th>
                                    <th class="py-2.5 font-semibold text-neutral-700 dark:text-neutral-300">Returned At</th>
                                    <th class="py-2.5 font-semibold text-neutral-700 dark:text-neutral-300">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800/80">
                                <tr 
                                    v-for="tx in asset.transactions" 
                                    :key="tx.id"
                                    class="hover:bg-neutral-50/30 dark:hover:bg-neutral-950/10"
                                >
                                    <td class="py-3 font-medium text-neutral-900 dark:text-white">
                                        <div class="flex flex-col">
                                            <span>{{ tx.user?.name }}</span>
                                            <span class="text-xs text-neutral-500">{{ tx.user?.email }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-neutral-600 dark:text-neutral-400">
                                        {{ formatDate(tx.borrowed_at) }}
                                    </td>
                                    <td class="py-3 text-neutral-600 dark:text-neutral-400">
                                        {{ tx.returned_at ? formatDate(tx.returned_at) : '-' }}
                                    </td>
                                    <td class="py-3">
                                        <span 
                                            v-if="tx.is_returned" 
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400"
                                        >
                                            <CheckCircle2 class="h-3 w-3" />
                                            Returned
                                        </span>
                                        <span 
                                            v-else 
                                            class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-950/30 dark:text-amber-400"
                                        >
                                            <Clock class="h-3 w-3" />
                                            On Loan
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!asset.transactions || asset.transactions.length === 0">
                                    <td colspan="4" class="text-center py-6 text-neutral-500 text-sm">
                                        No transaction logs found for this asset.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <AuditableTable auditable-type="Asset" />
        </div>
    </div>
</template>
