<script setup lang="ts">
import { logout } from '@/routes';
import { router, usePage } from '@inertiajs/vue3';
import { LogOut } from '@lucide/vue';

const page = usePage();
const authUser = page.props.auth.user;
const initial = authUser.name.charAt(0).toUpperCase();

function handleLogout() {
    router.post(logout.url());
}
</script>

<template>
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <div
                class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-primary-foreground"
            >
                {{ initial }}
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ authUser.name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ authUser.email }}
                </p>
            </div>
        </div>
        <button
            @click="handleLogout"
            class="tool cursor-pointer text-gray-900 hover:text-gray-500"
        >
            <LogOut class="h-5 w-5" />
        </button>
    </div>
</template>
