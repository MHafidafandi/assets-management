<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import { store, update } from '@/routes/roles';
import type { Role } from '@/types/role';
import InputError from './InputError.vue';
import Button from './ui/button/Button.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogDescription from './ui/dialog/DialogDescription.vue';
import DialogHeader from './ui/dialog/DialogHeader.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';
import Input from './ui/input/Input.vue';
import Label from './ui/label/Label.vue';
import AuditableTable from './AuditableTable.vue';

const props = defineProps<{
    label?: string;
    role?: Role;
    Description?: string;
}>();

const emit = defineEmits<{
    close: [];
}>();

const isEdit = computed(() => !!props.role);

const formTarget = computed(() =>
    isEdit.value ? update.form(props.role!.id) : store.form(),
);

function onSuccess() {
    emit('close');
}
</script>

<template>
    <DialogContent 
        class="fixed top-1/2 -translate-y-1/2 left-1/2 -translate-x-1/2 max-h-[90vh] overflow-y-auto sm:max-w-2xl"
    >
        <DialogHeader>
            <DialogTitle>{{
                props.label || (isEdit ? 'Edit Role' : 'Create Role')
            }}</DialogTitle>
            <DialogDescription>
                {{ props.Description }}
            </DialogDescription>
        </DialogHeader>

        <Form 
            v-bind="formTarget" 
            v-slot="{ errors, processing }" 
            class="w-full" 
            @success="onSuccess"
        >
            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        name="name"
                        :default-value="props.role?.name"
                        required
                        autofocus
                        placeholder="Administrator"
                        :class="{
                            'border-destructive text-destructive placeholder:text-destructive focus:ring-destructive':
                                errors.name,
                        }"
                    />
                    <InputError v-if="errors.name" :message="errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Input
                        id="description"
                        type="text"
                        name="description"
                        :default-value="props.role?.description"
                        placeholder="Superuser with full system access"
                        :class="{
                            'border-destructive text-destructive placeholder:text-destructive focus:ring-destructive':
                                errors.description,
                        }"
                    />
                    <InputError v-if="errors.description" :message="errors.description" />
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :disabled="processing"
                >
                    {{
                        processing
                            ? 'Saving...'
                            : isEdit
                              ? 'Update Role'
                              : 'Save Role'
                    }}
                </Button>
            </div>
        </Form>

        <AuditableTable auditable-type="Role" />
    </DialogContent>
</template>
