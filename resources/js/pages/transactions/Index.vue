<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    Edit,
    Plus,
    Search,
    Trash2,
    ArrowUpDown,
    ArrowUp,
    ArrowDown,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { index as transactionsIndex } from '@/actions/App/Http/Controllers/AssetTransactionController';
import ExportButton from '@/components/ExportButton.vue';
import ImportButton from '@/components/ImportButton.vue';
import TransactionDeleteDialog from '@/components/TransactionDeleteDialog.vue';
import TransactionDialog from '@/components/TransactionDialog.vue';
import AlertDialog from '@/components/ui/alert-dialog/AlertDialog.vue';
import AlertDialogTrigger from '@/components/ui/alert-dialog/AlertDialogTrigger.vue';
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import { store as exportTransactions } from '@/routes/exports/transactions';
import type { Asset } from '@/types/asset';
import type { DataJob } from '@/types/data-job';
import type { PaginatedTransactions } from '@/types/transaction';
import type { UserDB } from '@/types/user';
import { store as importTransactions } from '@/routes/imports/transactions';

const props = defineProps<{
    transactions: PaginatedTransactions;
    assets: Pick<Asset, 'id' | 'name'>[];
    users: Pick<UserDB, 'id' | 'name' | 'email'>[];
    filters: {
        search: string;
        is_returned: string;
        sort: string;
    };
    exportJob?: DataJob;
    importJob?: DataJob;
}>();
const createDialogOpen = ref(false);
const editDialogOpenMap = ref<Record<number, boolean>>({});
const search = ref(props.filters.search ?? '');
const returnedFilter = ref<string>(props.filters.is_returned ?? '');
const sortFilter = ref<string>(props.filters.sort ?? '');

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const applyFilters = () => {
    const query: Record<string, string> = {};

    if (search.value !== '') {
        query.search = search.value;
    }

    if (returnedFilter.value !== '') {
        query.is_returned = returnedFilter.value;
    }

    if (sortFilter.value !== '') {
        query.sort = sortFilter.value;
    }

    router.get(transactionsIndex.url(), query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

watch(search, () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => applyFilters(), 300);
});

watch([returnedFilter, sortFilter], () => {
    applyFilters();
});

const toggleSort = (field: string) => {
    if (sortFilter.value === field) {
        sortFilter.value = `-${field}`;
    } else if (sortFilter.value === `-${field}`) {
        sortFilter.value = '';
    } else {
        sortFilter.value = field;
    }
};

const getSortIcon = (field: string) => {
    if (sortFilter.value === field) {
        return ArrowUp;
    }

    if (sortFilter.value === `-${field}`) {
        return ArrowDown;
    }

    return ArrowUpDown;
};

const paginationPages = computed(() => {
    const current = props.transactions.current_page;
    const last = props.transactions.last_page;
    const pages: (number | '...')[] = [];

    if (last <= 7) {
        for (let i = 1; i <= last; i++) {
            pages.push(i);
        }
    } else {
        pages.push(1);

        if (current > 3) {
            pages.push('...');
        }

        for (
            let i = Math.max(2, current - 1);
            i <= Math.min(last - 1, current + 1);
            i++
        ) {
            pages.push(i);
        }

        if (current < last - 2) {
            pages.push('...');
        }

        pages.push(last);
    }

    return pages;
});

function goToPage(page: number) {
    const query: Record<string, string> = { page: String(page) };

    if (search.value) {
        query.search = search.value;
    }

    if (returnedFilter.value !== '') {
        query.is_returned = returnedFilter.value;
    }

    if (sortFilter.value !== '') {
        query.sort = sortFilter.value;
    }

    router.get(transactionsIndex.url(), query, {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Transactions" />
    <div class="flex w-full flex-col gap-lg p-xl">
        <!-- Page Header -->
        <div class="flex flex-wrap items-end justify-between">
            <div>
                <h2 class="text-headline-lg text-on-surface">Transactions</h2>
                <p class="text-body-md text-on-surface-variant">
                    Manage asset borrowing and returns.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-md">
                <Dialog v-model:open="createDialogOpen">
                    <DialogTrigger>
                        <Button
                            class="flex items-center gap-sm rounded-md px-lg py-sm text-label-md text-on-primary transition-all duration-200 active:scale-95"
                        >
                            <Plus class="ml-2 size-4.5" />
                            <span class="mr-2">New Transaction</span>
                        </Button>
                    </DialogTrigger>
                    <TransactionDialog
                        label="Create Transaction"
                        Description="Record a new asset transaction."
                        :assets="assets"
                        :users="users"
                        @close="createDialogOpen = false"
                    />
                </Dialog>
                <ImportButton
                    :import-job="importJob"
                    :route-form="importTransactions.form()"
                />
                <ExportButton
                    :export-job="exportJob"
                    :route-form="exportTransactions.form()"
                    :available-fields="{
                        user: 'User',
                        asset: 'Asset',
                        borrowed_at: 'Borrowed At',
                        returned_at: 'Returned At',
                        is_returned: 'Is Returned',
                        notes: 'Notes',
                    }"
                />
            </div>
        </div>

        <!-- Filter Bar -->
        <div
            class="flex flex-wrap items-center gap-md rounded-xl border border-outline-variant bg-surface-container-lowest p-md"
        >
            <div class="relative grow">
                <Search
                    class="absolute top-1/2 left-sm size-5 -translate-y-1/2 text-on-surface-variant"
                />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search by asset or user name..."
                    class="w-full rounded-lg border border-outline-variant bg-surface py-sm pr-md pl-xl text-body-md focus:border-primary focus:ring-1 focus:ring-primary"
                />
            </div>

            <div class="flex items-center gap-xs">
                <span class="px-sm text-label-md text-on-surface-variant"
                    >Returned:</span
                >
                <div
                    class="flex rounded-lg border border-outline-variant bg-surface p-0.5"
                >
                    <button
                        class="rounded-md px-md py-0.5 text-[12px] font-semibold transition-colors"
                        :class="
                            returnedFilter === ''
                                ? 'bg-white text-primary shadow-sm'
                                : 'text-on-surface-variant hover:text-primary'
                        "
                        @click="returnedFilter = ''"
                    >
                        All
                    </button>
                    <button
                        class="rounded-md px-md py-0.5 text-[12px] font-semibold transition-colors"
                        :class="
                            returnedFilter === '1'
                                ? 'bg-white text-primary shadow-sm'
                                : 'text-on-surface-variant hover:text-primary'
                        "
                        @click="returnedFilter = '1'"
                    >
                        Yes
                    </button>
                    <button
                        class="rounded-md px-md py-0.5 text-[12px] font-semibold transition-colors"
                        :class="
                            returnedFilter === '0'
                                ? 'bg-white text-primary shadow-sm'
                                : 'text-on-surface-variant hover:text-primary'
                        "
                        @click="returnedFilter = '0'"
                    >
                        No
                    </button>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div
            class="overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest"
        >
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr
                            class="border-b border-outline-variant bg-surface-container-low"
                        >
                            <th
                                class="px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase"
                            >
                                Asset
                            </th>
                            <th
                                class="px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase"
                            >
                                User
                            </th>
                            <th
                                class="cursor-pointer px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase transition-colors hover:bg-surface-container-high"
                                @click="toggleSort('borrowed_at')"
                            >
                                <div class="flex items-center gap-1">
                                    Borrowed At
                                    <component
                                        :is="getSortIcon('borrowed_at')"
                                        class="size-4"
                                    />
                                </div>
                            </th>
                            <th
                                class="px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-lg py-md text-right text-label-md tracking-wider text-on-surface-variant uppercase"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        <tr
                            v-for="transaction in transactions.data"
                            :key="transaction.id"
                            class="transition-colors hover:bg-surface-container-low/50"
                        >
                            <td class="px-lg py-md">
                                <span class="text-body-md font-semibold">{{
                                    transaction.asset?.name
                                }}</span>
                            </td>
                            <td class="px-lg py-md">
                                <span class="text-body-md font-semibold">{{
                                    transaction.user?.name
                                }}</span
                                ><br />
                                <span
                                    class="text-body-sm text-on-surface-variant"
                                    >{{ transaction.user?.email }}</span
                                >
                            </td>
                            <td class="px-lg py-md">
                                <span class="text-body-md">{{
                                    new Date(
                                        transaction.borrowed_at,
                                    ).toLocaleDateString()
                                }}</span>
                                <div
                                    v-if="transaction.returned_at"
                                    class="text-body-sm text-on-surface-variant"
                                >
                                    Ret:
                                    {{
                                        new Date(
                                            transaction.returned_at,
                                        ).toLocaleDateString()
                                    }}
                                </div>
                            </td>
                            <td class="px-lg py-md">
                                <span
                                    v-if="transaction.is_returned"
                                    class="inline-flex items-center rounded-full bg-success-container px-sm py-0.5 text-[11px] font-bold text-on-success-container"
                                    ><span
                                        class="mr-1.5 size-1.5 rounded-full bg-success"
                                    ></span
                                    >Returned</span
                                >
                                <span
                                    v-else
                                    class="inline-flex items-center rounded-full bg-warning-container px-sm py-0.5 text-[11px] font-bold text-on-warning-container"
                                    ><span
                                        class="mr-1.5 size-1.5 rounded-full bg-warning"
                                    ></span
                                    >Borrowed</span
                                >
                            </td>
                            <td class="px-lg py-md text-right">
                                <div class="flex justify-end gap-xs">
                                    <Dialog
                                        v-model:open="
                                            editDialogOpenMap[transaction.id]
                                        "
                                    >
                                        <DialogTrigger
                                            ><div
                                                class="rounded p-xs text-on-surface-variant transition-colors hover:bg-surface-container-high"
                                                title="Edit"
                                            >
                                                <Edit class="size-5" /></div
                                        ></DialogTrigger>
                                        <TransactionDialog
                                            :transaction="transaction"
                                            :assets="assets"
                                            :users="users"
                                            label="Edit Transaction"
                                            Description="Update the details of this transaction."
                                            @close="
                                                editDialogOpenMap[
                                                    transaction.id
                                                ] = false
                                            "
                                        />
                                    </Dialog>
                                    <AlertDialog>
                                        <AlertDialogTrigger
                                            ><button
                                                class="rounded p-xs text-on-surface-variant transition-colors hover:bg-error-container hover:text-error"
                                                title="Delete"
                                            >
                                                <Trash2
                                                    class="size-5"
                                                /></button
                                        ></AlertDialogTrigger>
                                        <TransactionDeleteDialog
                                            :transaction="transaction"
                                        />
                                    </AlertDialog>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="transactions.data.length === 0">
                            <td
                                colspan="5"
                                class="px-lg py-xl text-center text-body-md text-on-surface-variant"
                            >
                                No transactions found matching your filters.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="transactions.last_page > 1"
                class="flex items-center justify-between border-t border-outline-variant bg-surface-container px-lg py-sm"
            >
                <span class="text-body-sm text-on-surface-variant"
                    >Showing
                    <span class="font-semibold text-on-surface"
                        >{{ transactions.from }}-{{ transactions.to }}</span
                    >
                    of
                    <span class="font-semibold text-on-surface">{{
                        transactions.total
                    }}</span>
                    transactions</span
                >
                <div class="flex items-center gap-xs">
                    <button
                        class="rounded p-xs transition-colors hover:bg-surface-container-high disabled:opacity-30"
                        :disabled="!transactions.prev_page_url"
                        @click="goToPage(transactions.current_page - 1)"
                    >
                        <ChevronLeft class="size-5" />
                    </button>
                    <div class="flex gap-xs">
                        <template v-for="(page, i) in paginationPages" :key="i">
                            <span
                                v-if="page === '...'"
                                class="px-xs py-xs text-on-surface-variant"
                                >...</span
                            >
                            <button
                                v-else
                                class="flex size-8 items-center justify-center rounded text-label-md"
                                :class="
                                    page === transactions.current_page
                                        ? 'bg-primary text-on-primary'
                                        : 'hover:bg-surface-container-high'
                                "
                                @click="goToPage(page as number)"
                            >
                                {{ page }}
                            </button>
                        </template>
                    </div>
                    <button
                        class="rounded p-xs transition-colors hover:bg-surface-container-high disabled:opacity-30"
                        :disabled="!transactions.next_page_url"
                        @click="goToPage(transactions.current_page + 1)"
                    >
                        <ChevronRight class="size-5" />
                    </button>
                </div>
            </div>
            <div
                v-else
                class="flex items-center justify-between border-t border-outline-variant bg-surface-container px-lg py-sm"
            >
                <span class="text-body-sm text-on-surface-variant"
                    >Showing
                    <span class="font-semibold text-on-surface">{{
                        transactions.total
                    }}</span>
                    transaction(s)</span
                >
            </div>
        </div>
    </div>
</template>
