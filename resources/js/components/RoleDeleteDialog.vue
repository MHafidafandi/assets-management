<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { destroy } from '@/routes/roles';
import type { Role } from '@/types/role';
import AlertDialogAction from './ui/alert-dialog/AlertDialogAction.vue';
import AlertDialogCancel from './ui/alert-dialog/AlertDialogCancel.vue';
import AlertDialogContent from './ui/alert-dialog/AlertDialogContent.vue';
import AlertDialogDescription from './ui/alert-dialog/AlertDialogDescription.vue';
import AlertDialogFooter from './ui/alert-dialog/AlertDialogFooter.vue';
import AlertDialogHeader from './ui/alert-dialog/AlertDialogHeader.vue';
import AlertDialogTitle from './ui/alert-dialog/AlertDialogTitle.vue';

const props = defineProps<{
    role: Role;
}>();

function deleteRole() {
    router.delete(destroy.url(props.role.id));
}
</script>
<template>
    <AlertDialogContent class="fixed sm:max-w-106.5">
        <AlertDialogHeader>
            <AlertDialogTitle
                >Are you sure to delete role "{{
                    props.role.name
                }}"?</AlertDialogTitle
            >
            <AlertDialogDescription
                >This action cannot be undone. This will permanently delete the role from the system.</AlertDialogDescription
            >
        </AlertDialogHeader>
        <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction @click="deleteRole"
                >Continue</AlertDialogAction
            >
        </AlertDialogFooter>
    </AlertDialogContent>
</template>
