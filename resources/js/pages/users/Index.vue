<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    Edit,
    Search,
    Trash2,
    UserPlus,
    ArrowUpDown,
    ArrowUp,
    ArrowDown,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { index as usersIndex } from '@/actions/App/Http/Controllers/UserController';
import { store as exportUsers } from '@/routes/exports/users';
import { store as importUsers } from '@/routes/imports/users';
import AlertDialog from '@/components/ui/alert-dialog/AlertDialog.vue';
import AlertDialogTrigger from '@/components/ui/alert-dialog/AlertDialogTrigger.vue';
import { Button } from '@/components/ui/button';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import UserDeleteDialog from '@/components/UserDeleteDialog.vue';
import UserDialog from '@/components/UserDialog.vue';
import ExportButton from '@/components/ExportButton.vue';
import ImportButton from '@/components/ImportButton.vue';
import { getAvatarColor, getRoleBadgeClasses } from '@/helper/colors';
import { getInitials } from '@/helper/get-initial';
import type { PaginatedUsers } from '@/types/user';
import type { DataJob } from '@/types/data-job';

const props = defineProps<{
    users: PaginatedUsers;
    roles: { id: number; name: string }[];
    exportJob?: DataJob;
    importJob?: DataJob;
    filters: {
        search?: string;
        role_id?: string;
        is_active?: string;
        sort?: string;
    };
}>();

const createDialogOpen = ref(false);
const editDialogOpenMap = ref<Record<number, boolean>>({});

const search = ref(props.filters.search ?? '');
const activeFilter = ref<string>(props.filters.is_active ?? '');
const roleFilter = ref<string>(props.filters.role_id ?? '');
const sortFilter = ref<string>(props.filters.sort ?? '');

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

function applyFilters() {
    const query: Record<string, string> = {};

    if (search.value) {
        query.search = search.value;
    }

    if (activeFilter.value !== '') {
        query.is_active = activeFilter.value;
    }

    if (roleFilter.value) {
        query.role_id = roleFilter.value;
    }

    if (sortFilter.value) {
        query.sort = sortFilter.value;
    }

    router.get(usersIndex.url(), query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function toggleSort(field: string) {
    if (sortFilter.value === field) {
        sortFilter.value = `-${field}`;
    } else if (sortFilter.value === `-${field}`) {
        sortFilter.value = '';
    } else {
        sortFilter.value = field;
    }
    applyFilters();
}

function getSortIcon(field: string) {
    if (sortFilter.value === field) return ArrowUp;
    if (sortFilter.value === `-${field}`) return ArrowDown;
    return ArrowUpDown;
}

watch(search, () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

    searchTimeout = setTimeout(() => applyFilters(), 300);
});

watch([activeFilter, roleFilter], () => {
    applyFilters();
});

const paginationPages = computed(() => {
    const current = props.users.current_page;
    const last = props.users.last_page;
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

    if (activeFilter.value !== '') {
        query.is_active = activeFilter.value;
    }

    if (roleFilter.value) {
        query.role = roleFilter.value;
    }

    if (sortFilter.value) {
        query.sort = sortFilter.value;
    }

    router.get(usersIndex.url(), query, {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="User Accounts" />
    <div
        v-if="$page.props.auth.user.role.name === 'Administrator'"
        class="flex w-full flex-col gap-lg p-xl"
    >
        <!-- Page Header -->
        <div class="flex items-end justify-between">
            <div>
                <h2 class="text-headline-lg text-on-surface">
                    User Account Management
                </h2>
                <p class="text-body-md text-on-surface-variant">
                    Manage enterprise identities, roles, and access controls.
                </p>
            </div>
            <div class="flex flex-row items-center gap-md">
                <Dialog v-model:open="createDialogOpen">
                    <DialogTrigger as-child>
                        <Button
                            class="flex items-center gap-sm rounded-md px-lg py-sm text-label-md text-on-primary transition-all duration-200 active:scale-95"
                        >
                            <UserPlus class="ml-2 size-4.5" />
                            <span class="mr-2">Add New User</span>
                        </Button>
                    </DialogTrigger>
                    <UserDialog
                        label="Create User"
                        Description="Fill in the details to create a new user account."
                        :roles="roles"
                        @close="createDialogOpen = false"
                    />
                </Dialog>
                <ImportButton
                    :import-job="importJob"
                    :route-form="importUsers.form()"
                />
                <ExportButton
                    :export-job="exportJob"
                    :route-form="exportUsers.form()"
                    :available-fields="{
                        name: 'Nama',
                        email: 'Email',
                        role: 'Role',
                        is_active: 'Status',
                        created_at: 'Dibuat Pada',
                    }"
                />
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
                    placeholder="Search by name or email..."
                    class="w-full rounded-lg border border-outline-variant bg-surface py-sm pr-md pl-xl text-body-md focus:border-primary focus:ring-1 focus:ring-primary"
                />
            </div>

            <!-- Status Filter -->
            <div class="flex items-center gap-xs">
                <span class="px-sm text-label-md text-on-surface-variant"
                    >Status:</span
                >
                <div
                    class="flex rounded-lg border border-outline-variant bg-surface p-0.5"
                >
                    <button
                        class="rounded-md px-md py-0.5 text-[12px] font-semibold transition-colors"
                        :class="
                            activeFilter === ''
                                ? 'bg-white text-primary shadow-sm'
                                : 'text-on-surface-variant hover:text-primary'
                        "
                        @click="activeFilter = ''"
                    >
                        All
                    </button>
                    <button
                        class="rounded-md px-md py-0.5 text-[12px] font-semibold transition-colors"
                        :class="
                            activeFilter === '1'
                                ? 'bg-white text-primary shadow-sm'
                                : 'text-on-surface-variant hover:text-primary'
                        "
                        @click="activeFilter = '1'"
                    >
                        Active
                    </button>
                    <button
                        class="rounded-md px-md py-0.5 text-[12px] font-semibold transition-colors"
                        :class="
                            activeFilter === '0'
                                ? 'bg-white text-primary shadow-sm'
                                : 'text-on-surface-variant hover:text-primary'
                        "
                        @click="activeFilter = '0'"
                    >
                        Inactive
                    </button>
                </div>
            </div>

            <!-- Role Filter -->
            <div class="ml-auto flex items-center gap-xs">
                <span class="px-sm text-label-md text-on-surface-variant"
                    >Role:</span
                >
                <select
                    v-model="roleFilter"
                    class="rounded-lg border border-outline-variant bg-surface py-sm pr-xl text-body-sm"
                >
                    <option value="">All Roles</option>
                    <option
                        v-for="role in props.roles"
                        :key="role.id"
                        :value="role.id"
                    >
                        {{ role.name }}
                    </option>
                </select>
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
                                class="cursor-pointer px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase transition-colors hover:bg-surface-container-high"
                                @click="toggleSort('name')"
                            >
                                <div class="flex items-center gap-1">
                                    Name
                                    <component
                                        :is="getSortIcon('name')"
                                        class="size-4"
                                    />
                                </div>
                            </th>
                            <th
                                class="px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase"
                            >
                                Email
                            </th>
                            <th
                                class="px-lg py-md text-left text-label-md tracking-wider text-on-surface-variant uppercase"
                            >
                                Assigned Roles
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
                            v-for="(user, idx) in users.data"
                            :key="user.id"
                            class="transition-colors hover:bg-surface-container-low/50"
                        >
                            <!-- Name -->
                            <td class="px-lg py-md">
                                <div class="flex items-center gap-md">
                                    <div
                                        class="flex size-8 items-center justify-center rounded-full text-xs font-bold"
                                        :class="getAvatarColor(idx)"
                                    >
                                        {{ getInitials(user.name) }}
                                    </div>
                                    <span class="text-body-md font-semibold">{{
                                        user.name
                                    }}</span>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-lg py-md text-body-sm">
                                {{ user.email }}
                            </td>

                            <!-- Roles -->
                            <td class="px-lg py-md">
                                <span
                                    class="inline-flex items-center rounded-full px-sm py-0.5 text-[11px] font-bold"
                                    :class="
                                        getRoleBadgeClasses(
                                            user.role?.name || '',
                                        )
                                    "
                                >
                                    {{ user.role?.name || '-' }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-lg py-md">
                                <span
                                    v-if="user.is_active"
                                    class="inline-flex items-center rounded-full bg-success-container px-sm py-0.5 text-[11px] font-bold text-on-success-container"
                                >
                                    <span
                                        class="mr-1.5 size-1.5 rounded-full bg-success"
                                    ></span>
                                    Active
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center rounded-full bg-surface-container-highest px-sm py-0.5 text-[11px] font-bold text-on-surface-variant"
                                >
                                    <span
                                        class="mr-1.5 size-1.5 rounded-full bg-outline"
                                    ></span>
                                    Inactive
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-lg py-md text-right">
                                <div class="flex justify-end gap-xs">
                                    <Dialog
                                        v-model:open="
                                            editDialogOpenMap[user.id]
                                        "
                                    >
                                        <DialogTrigger>
                                            <div
                                                class="rounded p-xs text-on-surface-variant transition-colors hover:bg-surface-container-high"
                                                title="Edit"
                                            >
                                                <Edit class="size-5" />
                                            </div>
                                        </DialogTrigger>
                                        <UserDialog
                                            :User="user"
                                            label="Edit User"
                                            Description="Update the details of this user account."
                                            :roles="roles"
                                            @close="
                                                editDialogOpenMap[user.id] =
                                                    false
                                            "
                                        />
                                    </Dialog>
                                    <AlertDialog>
                                        <AlertDialogTrigger>
                                            <button
                                                class="rounded p-xs text-on-surface-variant transition-colors hover:bg-error-container hover:text-error"
                                                title="Delete"
                                            >
                                                <Trash2 class="size-5" />
                                            </button>
                                        </AlertDialogTrigger>
                                        <UserDeleteDialog :User="user" />
                                    </AlertDialog>
                                </div>
                            </td>
                        </tr>

                        <!-- Empty state -->
                        <tr v-if="users.data.length === 0">
                            <td
                                colspan="5"
                                class="px-lg py-xl text-center text-body-md text-on-surface-variant"
                            >
                                No users found matching your filters.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="users.last_page > 1"
                class="flex items-center justify-between border-t border-outline-variant bg-surface-container px-lg py-sm"
            >
                <span class="text-body-sm text-on-surface-variant">
                    Showing
                    <span class="font-semibold text-on-surface"
                        >{{ users.from }}-{{ users.to }}</span
                    >
                    of
                    <span class="font-semibold text-on-surface">{{
                        users.total
                    }}</span>
                    users
                </span>
                <div class="flex items-center gap-xs">
                    <button
                        class="rounded p-xs transition-colors hover:bg-surface-container-high disabled:opacity-30"
                        :disabled="!users.prev_page_url"
                        @click="goToPage(users.current_page - 1)"
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
                                    page === users.current_page
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
                        :disabled="!users.next_page_url"
                        @click="goToPage(users.current_page + 1)"
                    >
                        <ChevronRight class="size-5" />
                    </button>
                </div>
            </div>

            <!-- Single page info -->
            <div
                v-else
                class="flex items-center justify-between border-t border-outline-variant bg-surface-container px-lg py-sm"
            >
                <span class="text-body-sm text-on-surface-variant">
                    Showing
                    <span class="font-semibold text-on-surface">{{
                        users.total
                    }}</span>
                    user(s)
                </span>
            </div>
        </div>
    </div>
</template>
