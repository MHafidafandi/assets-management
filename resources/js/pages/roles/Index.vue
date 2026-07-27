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
import RoleDeleteDialog from '@/components/RoleDeleteDialog.vue';
import RoleDialog from '@/components/RoleDialog.vue';
import AlertDialog from '@/components/ui/alert-dialog/AlertDialog.vue';
import AlertDialogTrigger from '@/components/ui/alert-dialog/AlertDialogTrigger.vue';
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import { index as rolesIndex } from '@/routes/roles';
import type { PaginatedRole } from '@/types/role';

const props = defineProps<{
    roles: PaginatedRole;
    filters: {
        search: string;
        sort: string;
    };
}>();

const createDialogOpen = ref(false);
const editDialogOpenMap = ref<Record<number, boolean>>({});
const search = ref(props.filters.search ?? '');
const sortFilter = ref(props.filters.sort ?? '');

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const applyFilters = () => {
    const query: Record<string, string> = {};

    if (search.value !== '') {
        query.search = search.value;
    }

    if (sortFilter.value !== '') {
        query.sort = sortFilter.value;
    }

    router.get(rolesIndex.url(), query, {
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

watch(sortFilter, () => {
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

const paginationRange = computed(() => {
    const current = props.roles.current_page;
    const last = props.roles.last_page;
    const delta = 2;
    const range = [];
    const rangeWithDots = [];
    let l;

    for (let i = 1; i <= last; i++) {
        if (
            i === 1 ||
            i === last ||
            (i >= current - delta && i <= current + delta)
        ) {
            range.push(i);
        }
    }

    for (const i of range) {
        if (l) {
            if (i - l === 2) {
                rangeWithDots.push(l + 1);
            } else if (i - l !== 1) {
                rangeWithDots.push('...');
            }
        }

        rangeWithDots.push(i);
        l = i;
    }

    return rangeWithDots;
});

function goToPage(page: number) {
    const query: Record<string, string> = { page: String(page) };

    if (search.value) {
        query.search = search.value;
    }

    if (sortFilter.value !== '') {
        query.sort = sortFilter.value;
    }

    router.get(rolesIndex.url(), query, {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Roles Management" />
    <div class="flex w-full flex-col gap-lg p-xl">
        <!-- Page Header -->
        <div class="flex flex-wrap items-center justify-between">
            <div>
                <h2 class="text-headline-lg font-bold text-on-surface">
                    Role Management
                </h2>
                <p class="text-body-md text-on-surface-variant">
                    Define and manage administrative roles in the system.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-md">
                <Dialog v-model:open="createDialogOpen">
                    <DialogTrigger as-child>
                        <Button
                            class="flex items-center gap-sm rounded-md px-lg py-sm text-label-md text-on-primary transition-all duration-200 active:scale-95"
                        >
                            <Plus class="ml-2 size-4.5" />
                            <span class="mr-2">Add New Role</span>
                        </Button>
                    </DialogTrigger>
                    <RoleDialog
                        label="Create Role"
                        Description="Fill in the details to create a new role."
                        @close="createDialogOpen = false"
                    />
                </Dialog>
            </div>
        </div>

        <!-- Filter Bar -->
        <div
            class="flex flex-wrap items-center gap-md rounded-xl border border-outline-variant bg-surface-container-lowest p-md"
        >
            <!-- Search -->
            <div class="relative grow">
                <Search
                    class="absolute top-1/2 left-sm size-5 -translate-y-1/2 text-on-surface-variant"
                />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search by name or description..."
                    class="w-full rounded-lg border border-outline-variant bg-surface py-sm pr-md pl-xl text-body-md focus:border-primary focus:ring-1 focus:ring-primary"
                />
            </div>
        </div>

        <!-- Roles Table -->
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
                                class="cursor-pointer px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase transition-colors hover:bg-surface-container-high"
                                @click="toggleSort('name')"
                            >
                                <div class="flex items-center gap-1">
                                    Name
                                    <component
                                        :is="getSortIcon('name')"
                                        class="size-4 text-on-surface-variant/70"
                                    />
                                </div>
                            </th>
                            <th
                                class="cursor-pointer px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase transition-colors hover:bg-surface-container-high"
                                @click="toggleSort('description')"
                            >
                                <div class="flex items-center gap-1">
                                    Description
                                    <component
                                        :is="getSortIcon('description')"
                                        class="size-4 text-on-surface-variant/70"
                                    />
                                </div>
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
                            v-for="role in props.roles.data"
                            :key="role.id"
                            class="transition-colors hover:bg-surface-container-low"
                        >
                            <td
                                class="px-lg py-md text-body-md font-semibold text-on-surface"
                            >
                                {{ role.name }}
                            </td>
                            <td
                                class="px-lg py-md text-body-md text-on-surface-variant"
                            >
                                {{ role.description || '-' }}
                            </td>
                            <td class="px-lg py-md text-right">
                                <div
                                    class="flex items-center justify-end gap-sm"
                                >
                                    <!-- Edit -->
                                    <Dialog
                                        v-model:open="
                                            editDialogOpenMap[role.id]
                                        "
                                    >
                                        <DialogTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="text-on-surface-variant hover:text-primary"
                                            >
                                                <Edit class="size-4" />
                                            </Button>
                                        </DialogTrigger>
                                        <RoleDialog
                                            :role="role"
                                            label="Edit Role"
                                            Description="Update the role details."
                                            @close="
                                                editDialogOpenMap[role.id] =
                                                    false
                                            "
                                        />
                                    </Dialog>

                                    <!-- Delete -->
                                    <AlertDialog>
                                        <AlertDialogTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="text-on-surface-variant hover:text-destructive"
                                            >
                                                <Trash2 class="size-4" />
                                            </Button>
                                        </AlertDialogTrigger>
                                        <RoleDeleteDialog :role="role" />
                                    </AlertDialog>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="props.roles.data.length === 0">
                            <td
                                colspan="3"
                                class="px-lg py-xl text-center text-body-md text-on-surface-variant"
                            >
                                No roles found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div
            v-if="props.roles.last_page > 1"
            class="flex items-center justify-between border-t border-outline-variant pt-lg"
        >
            <span class="text-body-sm text-on-surface-variant">
                Showing {{ props.roles.from }} to {{ props.roles.to }} of
                {{ props.roles.total }} results
            </span>
            <div class="flex items-center gap-xs">
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="props.roles.current_page === 1"
                    @click="goToPage(props.roles.current_page - 1)"
                >
                    <ChevronLeft class="size-4" />
                </Button>

                <template v-for="page in paginationRange" :key="page">
                    <span
                        v-if="page === '...'"
                        class="px-md py-sm text-body-sm text-on-surface-variant"
                    >
                        ...
                    </span>
                    <Button
                        v-else
                        :variant="
                            props.roles.current_page === page
                                ? 'default'
                                : 'outline'
                        "
                        size="sm"
                        @click="goToPage(page as number)"
                    >
                        {{ page }}
                    </Button>
                </template>

                <Button
                    variant="outline"
                    size="sm"
                    :disabled="
                        props.roles.current_page === props.roles.last_page
                    "
                    @click="goToPage(props.roles.current_page + 1)"
                >
                    <ChevronRight class="size-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
