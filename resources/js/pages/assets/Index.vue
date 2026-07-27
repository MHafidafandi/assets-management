<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Edit, Plus, Search, Trash2, ArrowUpDown, ArrowUp, ArrowDown } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { index as assetsIndex } from '@/actions/App/Http/Controllers/AssetController';
import AssetDeleteDialog from '@/components/AssetDeleteDialog.vue';
import AssetDialog from '@/components/AssetDialog.vue';
import AlertDialog from '@/components/ui/alert-dialog/AlertDialog.vue';
import AlertDialogTrigger from '@/components/ui/alert-dialog/AlertDialogTrigger.vue';
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import type { PaginatedAssets } from '@/types/asset';
import type { Category } from '@/types/category';

const props = defineProps<{
    assets: PaginatedAssets;
    categories: Pick<Category, 'id' | 'name'>[];
    filters: {
        search: string;
        is_active: string;
        category_id: string;
        sort: string;
    };
}>();

const createDialogOpen = ref(false);
const editDialogOpenMap = ref<Record<number, boolean>>({});
const search = ref(props.filters.search ?? '');
const activeFilter = ref<string>(props.filters.is_active ?? '');
const categoryFilter = ref<string>(props.filters.category_id ?? '');
const sortFilter = ref<string>(props.filters.sort ?? '');

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const applyFilters = () => {
    const query: Record<string, string> = {};

    if (search.value !== '') query.search = search.value;
    if (activeFilter.value !== '') query.is_active = activeFilter.value;
    if (categoryFilter.value !== '') query.category_id = categoryFilter.value;
    if (sortFilter.value !== '') query.sort = sortFilter.value;

    router.get(assetsIndex.url(), query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

watch(search, () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 300);
});

watch([activeFilter, categoryFilter, sortFilter], () => {
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
    if (sortFilter.value === field) return ArrowUp;
    if (sortFilter.value === `-${field}`) return ArrowDown;
    return ArrowUpDown;
};

const paginationPages = computed(() => {
    const current = props.assets.current_page;
    const last = props.assets.last_page;
    const pages: (number | '...')[] = [];

    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        pages.push(1);
        if (current > 3) pages.push('...');
        for (let i = Math.max(2, current - 1); i <= Math.min(last - 1, current + 1); i++) pages.push(i);
        if (current < last - 2) pages.push('...');
        pages.push(last);
    }
    return pages;
});

function goToPage(page: number) {
    const query: Record<string, string> = { page: String(page) };
    if (search.value) query.search = search.value;
    if (activeFilter.value !== '') query.is_active = activeFilter.value;
    if (categoryFilter.value !== '') query.category_id = categoryFilter.value;
    if (sortFilter.value !== '') query.sort = sortFilter.value;

    router.get(assetsIndex.url(), query, {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Assets" />
    <div class="flex w-full flex-col gap-lg p-xl">
        <!-- Page Header -->
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-headline-lg text-on-surface">Asset Management</h2>
                <p class="text-body-md text-on-surface-variant">Manage all company assets.</p>
            </div>
            <Dialog v-model:open="createDialogOpen">
                <DialogTrigger>
                    <Button class="flex items-center gap-sm rounded-md px-lg py-sm text-label-md text-on-primary transition-all duration-200 active:scale-95">
                        <Plus class="ml-2 size-4.5" />
                        <span class="mr-2">Add New Asset</span>
                    </Button>
                </DialogTrigger>
                <AssetDialog
                    label="Create Asset"
                    Description="Fill in the details to create a new asset."
                    :categories="categories"
                    @close="createDialogOpen = false"
                />
            </Dialog>
        </div>

        <!-- Filter Bar -->
        <div class="flex flex-wrap items-center gap-md rounded-xl border border-outline-variant bg-surface-container-lowest p-md">
            <div class="relative grow">
                <Search class="absolute top-1/2 left-sm size-5 -translate-y-1/2 text-on-surface-variant" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search by name..."
                    class="w-full rounded-lg border border-outline-variant bg-surface py-sm pr-md pl-xl text-body-md focus:border-primary focus:ring-1 focus:ring-primary"
                />
            </div>

            <div class="flex items-center gap-xs">
                <span class="px-sm text-label-md text-on-surface-variant">Category:</span>
                <select v-model="categoryFilter" class="rounded-lg border border-outline-variant bg-surface py-sm pr-xl text-body-sm">
                    <option value="">All Categories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
            </div>

            <div class="flex items-center gap-xs">
                <span class="px-sm text-label-md text-on-surface-variant">Status:</span>
                <div class="flex rounded-lg border border-outline-variant bg-surface p-0.5">
                    <button class="rounded-md px-md py-0.5 text-[12px] font-semibold transition-colors" :class="activeFilter === '' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant hover:text-primary'" @click="activeFilter = ''">All</button>
                    <button class="rounded-md px-md py-0.5 text-[12px] font-semibold transition-colors" :class="activeFilter === '1' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant hover:text-primary'" @click="activeFilter = '1'">Active</button>
                    <button class="rounded-md px-md py-0.5 text-[12px] font-semibold transition-colors" :class="activeFilter === '0' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant hover:text-primary'" @click="activeFilter = '0'">Inactive</button>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-hidden rounded-xl border border-outline-variant bg-surface-container-lowest">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-outline-variant bg-surface-container-low">
                            <th class="px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase cursor-pointer hover:bg-surface-container-high transition-colors" @click="toggleSort('name')">
                                <div class="flex items-center gap-1">Name <component :is="getSortIcon('name')" class="size-4" /></div>
                            </th>
                            <th class="px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase">Category</th>
                            <th class="px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase cursor-pointer hover:bg-surface-container-high transition-colors" @click="toggleSort('purchase_date')">
                                <div class="flex items-center gap-1">Purchase Date <component :is="getSortIcon('purchase_date')" class="size-4" /></div>
                            </th>
                            <th class="px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase">Attachment</th>
                            <th class="px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase">Status</th>
                            <th class="px-lg py-md text-right text-label-md tracking-wider text-on-surface-variant uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        <tr v-for="asset in assets.data" :key="asset.id" class="transition-colors hover:bg-surface-container-low/50">
                            <td class="px-lg py-md"><span class="text-body-md font-semibold">{{ asset.name }}</span></td>
                            <td class="px-lg py-md"><span class="text-body-md">{{ asset.category?.name }}</span></td>
                            <td class="px-lg py-md"><span class="text-body-md">{{ new Date(asset.purchase_date).toLocaleDateString() }}</span></td>
                            <td class="px-lg py-md">
                                <a v-if="asset.attachment" :href="`/storage/${asset.attachment.file_path}`" target="_blank" class="text-primary hover:underline text-body-sm flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                                    PDF
                                </a>
                                <span v-else class="text-body-sm text-on-surface-variant">-</span>
                            </td>
                            <td class="px-lg py-md">
                                <span v-if="asset.is_active" class="inline-flex items-center rounded-full bg-success-container px-sm py-0.5 text-[11px] font-bold text-on-success-container"><span class="mr-1.5 size-1.5 rounded-full bg-success"></span>Active</span>
                                <span v-else class="inline-flex items-center rounded-full bg-surface-container-highest px-sm py-0.5 text-[11px] font-bold text-on-surface-variant"><span class="mr-1.5 size-1.5 rounded-full bg-outline"></span>Inactive</span>
                            </td>
                            <td class="px-lg py-md text-right">
                                <div class="flex justify-end gap-xs">
                                    <Dialog v-model:open="editDialogOpenMap[asset.id]">
                                        <DialogTrigger><div class="rounded p-xs text-on-surface-variant transition-colors hover:bg-surface-container-high" title="Edit"><Edit class="size-5" /></div></DialogTrigger>
                                        <AssetDialog :asset="asset" :categories="categories" label="Edit Asset" Description="Update the details of this asset." @close="editDialogOpenMap[asset.id] = false" />
                                    </Dialog>
                                    <AlertDialog>
                                        <AlertDialogTrigger><button class="rounded p-xs text-on-surface-variant transition-colors hover:bg-error-container hover:text-error" title="Delete"><Trash2 class="size-5" /></button></AlertDialogTrigger>
                                        <AssetDeleteDialog :asset="asset" />
                                    </AlertDialog>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="assets.data.length === 0">
                            <td colspan="5" class="px-lg py-xl text-center text-body-md text-on-surface-variant">No assets found matching your filters.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="assets.last_page > 1" class="flex items-center justify-between border-t border-outline-variant bg-surface-container px-lg py-sm">
                <span class="text-body-sm text-on-surface-variant">Showing <span class="font-semibold text-on-surface">{{ assets.from }}-{{ assets.to }}</span> of <span class="font-semibold text-on-surface">{{ assets.total }}</span> assets</span>
                <div class="flex items-center gap-xs">
                    <button class="rounded p-xs transition-colors hover:bg-surface-container-high disabled:opacity-30" :disabled="!assets.prev_page_url" @click="goToPage(assets.current_page - 1)"><ChevronLeft class="size-5" /></button>
                    <div class="flex gap-xs">
                        <template v-for="(page, i) in paginationPages" :key="i">
                            <span v-if="page === '...'" class="px-xs py-xs text-on-surface-variant">...</span>
                            <button v-else class="flex size-8 items-center justify-center rounded text-label-md" :class="page === assets.current_page ? 'bg-primary text-on-primary' : 'hover:bg-surface-container-high'" @click="goToPage(page as number)">{{ page }}</button>
                        </template>
                    </div>
                    <button class="rounded p-xs transition-colors hover:bg-surface-container-high disabled:opacity-30" :disabled="!assets.next_page_url" @click="goToPage(assets.current_page + 1)"><ChevronRight class="size-5" /></button>
                </div>
            </div>
            <div v-else class="flex items-center justify-between border-t border-outline-variant bg-surface-container px-lg py-sm">
                <span class="text-body-sm text-on-surface-variant">Showing <span class="font-semibold text-on-surface">{{ assets.total }}</span> asset(s)</span>
            </div>
        </div>
    </div>
</template>
