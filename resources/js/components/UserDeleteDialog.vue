<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { destroy } from '@/routes/users';
import type { UserDB } from '@/types/user';
import AlertDialogAction from './ui/alert-dialog/AlertDialogAction.vue';
import AlertDialogCancel from './ui/alert-dialog/AlertDialogCancel.vue';
import AlertDialogContent from './ui/alert-dialog/AlertDialogContent.vue';
import AlertDialogDescription from './ui/alert-dialog/AlertDialogDescription.vue';
import AlertDialogFooter from './ui/alert-dialog/AlertDialogFooter.vue';
import AlertDialogHeader from './ui/alert-dialog/AlertDialogHeader.vue';
import AlertDialogTitle from './ui/alert-dialog/AlertDialogTitle.vue';

const props = defineProps<{
    User: UserDB;
}>();

function deleteUser() {
    router.delete(destroy.url(props.User.id));
}
</script>
<template>
    <AlertDialogContent class="fixed sm:max-w-106.5">
        <AlertDialogHeader>
            <AlertDialogTitle
                >Are you sure to delete user "{{
                    props.User.name
                }}"?</AlertDialogTitle
            >
            <AlertDialogDescription
                >his action cannot be undone. This will permanently delete your
                account and remove your data from our
                servers.</AlertDialogDescription
            >
        </AlertDialogHeader>
        <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction @click="deleteUser"
                >Continue</AlertDialogAction
            >
        </AlertDialogFooter>
    </AlertDialogContent>
</template>
