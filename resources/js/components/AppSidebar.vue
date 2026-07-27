<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import {
    Activity,
    ArrowLeftRight,
    ChartBarStacked,
    Database,
    Users,
} from '@lucide/vue';
import type { NavItem } from '@/types/nav-item.js';
import NavMain from './NavMain.vue';
import NavUser from './NavUser.vue';
import Sidebar from './ui/sidebar/Sidebar.vue';
import SidebarContent from './ui/sidebar/SidebarContent.vue';
import SidebarMenu from './ui/sidebar/SidebarMenu.vue';
import SidebarMenuButton from './ui/sidebar/SidebarMenuButton.vue';
import SidebarMenuItem from './ui/sidebar/SidebarMenuItem.vue';

const page = usePage();
const name = page.props.name;

const mainNavItems: NavItem[] = [
    {
        title: 'Users',
        href: '/users',
        icon: Users,
    },
    {
        title: 'Categories',
        href: '/categories',
        icon: ChartBarStacked,
    },
    {
        title: 'Assets',
        href: '/assets',
        icon: Database,
    },
    {
        title: 'Transactions',
        href: '/transactions',
        icon: ArrowLeftRight,
    },
    {
        title: 'Audit Logs',
        href: '/audit-logs',
        icon: Activity,
    },
];
</script>

<template>
    <Sidebar class="pl-2">
        <SidebarHeader class="py-2">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg">
                        <div
                            class="flex aspect-square size-10 items-center justify-center"
                        >
                            <img
                                src="/assetflow_logo.png"
                                alt="logo"
                                class="size-8 object-cover"
                            />
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-headline-sm text-primary">
                                {{ name }}
                            </h1>
                        </div>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="px-3">
            <NavMain :items="mainNavItems" />
        </SidebarContent>
        <SidebarFooter class="px-3 py-3">
            <NavUser />
        </SidebarFooter>
    </Sidebar>
</template>
