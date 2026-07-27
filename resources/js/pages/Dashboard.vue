<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    Database,
    Users,
    ChartBarStacked,
    ArrowLeftRight,
    Plus,
    Activity
} from '@lucide/vue';
import Button from '@/components/ui/button/Button.vue';

interface Transaction {
    id: number;
    asset?: { name: string };
    user?: { name: string };
    type: 'check-in' | 'check-out';
    action_date: string;
    notes?: string;
}

const props = defineProps<{
    stats: {
        total_assets: number;
        active_assets: number;
        total_categories: number;
        total_users: number;
        total_transactions: number;
    };
    latestTransactions: Transaction[];
}>();

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>

<template>
    <Head title="Dashboard" />
    <div class="flex w-full flex-col gap-lg p-xl">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-headline-lg text-on-surface font-bold">
                    Dashboard
                </h2>
                <p class="text-body-md text-on-surface-variant">
                    Overview of your company's physical assets and transactions.
                </p>
            </div>
            <div class="flex flex-row items-center gap-md">
                <Button
                    class="flex items-center gap-sm rounded-md px-lg py-sm text-label-md text-on-primary transition-all duration-200 active:scale-95"
                    @click="router.visit('/assets')"
                >
                    <Plus class="ml-2 size-4.5" />
                    <span class="mr-2">Manage Assets</span>
                </Button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-md sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Assets -->
            <div class="rounded-xl border border-outline-variant bg-surface p-xl shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-label-md font-medium text-on-surface-variant">Total Assets</span>
                    <div class="rounded-lg bg-primary/10 p-sm text-primary">
                        <Database class="size-6" />
                    </div>
                </div>
                <div class="mt-md">
                    <span class="text-headline-lg font-bold text-on-surface">{{ props.stats.total_assets }}</span>
                    <p class="text-body-sm text-on-surface-variant mt-xs">
                        {{ props.stats.active_assets }} active assets in stock
                    </p>
                </div>
            </div>

            <!-- Total Users -->
            <div class="rounded-xl border border-outline-variant bg-surface p-xl shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-label-md font-medium text-on-surface-variant">System Users</span>
                    <div class="rounded-lg bg-secondary/10 p-sm text-secondary">
                        <Users class="size-6" />
                    </div>
                </div>
                <div class="mt-md">
                    <span class="text-headline-lg font-bold text-on-surface">{{ props.stats.total_users }}</span>
                    <p class="text-body-sm text-on-surface-variant mt-xs">Registered accounts</p>
                </div>
            </div>

            <!-- Categories -->
            <div class="rounded-xl border border-outline-variant bg-surface p-xl shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-label-md font-medium text-on-surface-variant">Categories</span>
                    <div class="rounded-lg bg-green-500/10 p-sm text-green-600">
                        <ChartBarStacked class="size-6" />
                    </div>
                </div>
                <div class="mt-md">
                    <span class="text-headline-lg font-bold text-on-surface">{{ props.stats.total_categories }}</span>
                    <p class="text-body-sm text-on-surface-variant mt-xs">Asset categories</p>
                </div>
            </div>

            <!-- Transactions -->
            <div class="rounded-xl border border-outline-variant bg-surface p-xl shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-label-md font-medium text-on-surface-variant">Transactions</span>
                    <div class="rounded-lg bg-orange-500/10 p-sm text-orange-600">
                        <ArrowLeftRight class="size-6" />
                    </div>
                </div>
                <div class="mt-md">
                    <span class="text-headline-lg font-bold text-on-surface">{{ props.stats.total_transactions }}</span>
                    <p class="text-body-sm text-on-surface-variant mt-xs">Total check-in / check-outs</p>
                </div>
            </div>
        </div>

        <!-- Latest Transactions -->
        <div class="rounded-xl border border-outline-variant bg-surface shadow-sm overflow-hidden">
            <div class="border-b border-outline-variant bg-surface-container-low px-xl py-lg flex items-center justify-between">
                <h3 class="text-title-md font-bold text-on-surface flex items-center gap-sm">
                    <Activity class="size-5 text-primary" />
                    Latest Transactions Activity
                </h3>
                <Button variant="ghost" size="sm" class="text-primary" @click="router.visit('/transactions')">
                    View All
                </Button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-outline-variant bg-surface-container-lowest text-label-md text-on-surface-variant uppercase">
                            <th class="px-xl py-md">Asset</th>
                            <th class="px-xl py-md">User</th>
                            <th class="px-xl py-md">Type</th>
                            <th class="px-xl py-md">Date</th>
                            <th class="px-xl py-md">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        <tr
                            v-for="tx in props.latestTransactions"
                            :key="tx.id"
                            class="hover:bg-surface-container-low transition-colors text-body-md"
                        >
                            <td class="px-xl py-md font-semibold text-on-surface">
                                {{ tx.asset?.name || 'Deleted Asset' }}
                            </td>
                            <td class="px-xl py-md text-on-surface-variant">
                                {{ tx.user?.name || 'System' }}
                            </td>
                            <td class="px-xl py-md">
                                <span
                                    class="inline-flex items-center px-md py-xs rounded-full text-xs font-semibold"
                                    :class="tx.type === 'check-out' ? 'bg-orange-500/10 text-orange-600' : 'bg-green-500/10 text-green-600'"
                                >
                                    {{ tx.type === 'check-out' ? 'Check-out' : 'Check-in' }}
                                </span>
                            </td>
                            <td class="px-xl py-md text-on-surface-variant">
                                {{ formatDate(tx.action_date) }}
                            </td>
                            <td class="px-xl py-md text-on-surface-variant max-w-xs truncate">
                                {{ tx.notes || '-' }}
                            </td>
                        </tr>
                        <tr v-if="props.latestTransactions.length === 0">
                            <td colspan="5" class="px-xl py-xl text-center text-on-surface-variant">
                                No transaction activities found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
