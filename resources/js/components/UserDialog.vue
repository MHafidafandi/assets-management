<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import { store, update } from '@/routes/users';
import type { UserDB } from '@/types/user.js';
import InputError from './InputError.vue';
import Button from './ui/button/Button.vue';
import DialogContent from './ui/dialog/DialogContent.vue';
import DialogDescription from './ui/dialog/DialogDescription.vue';
import DialogHeader from './ui/dialog/DialogHeader.vue';
import DialogTitle from './ui/dialog/DialogTitle.vue';
import Input from './ui/input/Input.vue';
import Label from './ui/label/Label.vue';
import RadioGroup from './ui/radio-group/RadioGroup.vue';
import RadioGroupItem from './ui/radio-group/RadioGroupItem.vue';
import AuditableTable from './AuditableTable.vue';

const props = defineProps<{
    label?: string;
    User?: UserDB;
    Description?: string;
    roles: { id: number; name: string }[];
}>();

const emit = defineEmits<{
    close: [];
}>();

const isEdit = computed(() => !!props.User);

// Wayfinder: store.form() -> { action: '/users', method: 'post' }
//            update.form(id) -> { action: '/users/{id}', method: 'post', _method: 'PUT' }
const formTarget = computed(() =>
    isEdit.value ? update.form(props.User!.id) : store.form(),
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
                props.label || (isEdit ? 'Edit User' : 'Create User')
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
            <div class="grid gap-2">
                <div class="grid gap-3">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        name="name"
                        :default-value="props.User?.name"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        placeholder="John Doe"
                        :class="{
                            'border-destructive text-destructive placeholder:text-destructive focus:ring-destructive':
                                errors.name,
                        }"
                    />
                    <InputError v-if="errors.name" :message="errors.name" />
                </div>

                <div class="grid gap-3">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        :default-value="props.User?.email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        placeholder="john@example.com"
                        :class="{
                            'border-destructive text-destructive placeholder:text-destructive focus:ring-destructive':
                                errors.email,
                        }"
                    />
                    <InputError v-if="errors.email" :message="errors.email" />
                </div>

                <div class="grid gap-3">
                    <Label for="password">
                        Password
                        <span
                            v-if="isEdit"
                            class="text-xs text-muted-foreground"
                            >(kosongkan jika tidak diubah)</span
                        >
                    </Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        :required="!isEdit"
                        :tabindex="3"
                        autocomplete="new-password"
                        placeholder="********"
                        :class="{
                            'border-destructive text-destructive placeholder:text-destructive focus:ring-destructive':
                                errors.password,
                        }"
                    />
                    <InputError
                        v-if="errors.password"
                        :message="errors.password"
                    />
                </div>

                <div class="grid gap-3">
                    <Label for="role_id">Role</Label>
                    <select
                        id="role_id"
                        name="role_id"
                        class="w-full rounded-lg border border-outline-variant bg-surface py-sm px-md text-body-md focus:border-primary focus:ring-1 focus:ring-primary"
                        :value="props.User?.role_id"
                        required
                    >
                        <option value="" disabled selected>Pilih Role</option>
                        <option
                            v-for="role in props.roles"
                            :key="role.id"
                            :value="role.id"
                        >
                            {{ role.name }}
                        </option>
                    </select>
                    <InputError v-if="errors.role_id" :message="errors.role_id" />
                </div>
                <div v-if="props.User" class="grid gap-3">
                    <Label for="is_active">Status</Label>
                    <RadioGroup
                        id="is_active"
                        name="is_active"
                        :tabindex="5"
                        :default-value="props.User?.is_active ? '1' : '0'"
                    >
                        <div class="flex items-center space-x-2">
                            <RadioGroupItem name="is_active" value="1" />
                            <Label for="active">Active</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <RadioGroupItem name="is_active" value="0" />
                            <Label for="inactive">Inactive</Label>
                        </div>
                        <InputError
                            v-if="errors.is_active"
                            :message="errors.is_active"
                        />
                    </RadioGroup>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :tabindex="4"
                    :disabled="processing"
                >
                    {{
                        processing
                            ? 'Saving...'
                            : isEdit
                              ? 'Update User'
                              : 'Save User'
                    }}
                </Button>
            </div>
        </Form>

        <AuditableTable auditable-type="User" />
    </DialogContent>
</template>
