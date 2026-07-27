<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import type { NavItem } from '@/types/nav-item';
import SidebarMenu from './ui/sidebar/SidebarMenu.vue';
import SidebarMenuButton from './ui/sidebar/SidebarMenuButton.vue';
import SidebarMenuItem from './ui/sidebar/SidebarMenuItem.vue';

const navItems = defineProps<{
    items: NavItem[];
}>();

const page = usePage();

const isActive = (href: string): boolean | undefined => {
    if (href === '/') {
        return page.url === href;
    }

    return page.url.startsWith(href);
};
</script>

<template>
    <SidebarMenu class="gap-2">
        <SidebarMenuItem
            v-for="item in navItems.items"
            :key="item.title"
            :href="item.href"
        >
            <SidebarMenuButton
                as-child
                :tooltip="item.title"
                class="rounded-sm from-inverse-primary to-inverse-primary/0 py-2 text-primary hover:bg-linear-to-br"
                :class="{
                    'bg-linear-to-br from-inverse-primary to-inverse-primary/0 text-primary hover:from-inverse-primary/70':
                        isActive(item.href),
                }"
            >
                <a :href="item.href" class="flex items-center">
                    <component :is="item.icon" />
                    <span class="ml-2">{{ item.title }}</span>
                </a>
            </SidebarMenuButton>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
