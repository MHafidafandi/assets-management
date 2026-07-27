<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import { store, update } from '@/routes/categories';
import type { Category } from '@/types/category.js';
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
    category?: Category;
    Description?: string;
}>();

const emit = defineEmits<{
    close: [];
}>();

const isEdit = computed(() => !!props.category);

// Wayfinder: store.form() -> { action: '/users', method: 'post' }
//            update.form(id) -> { action: '/users/{id}', method: 'post', _method: 'PUT' }
const formTarget = computed(() =>
    isEdit.value ? update.form(props.category!.id) : store.form(),
);

function onSuccess() {
    emit('close');
}
</script>

<template>
    <DialogContent
        class="fixed top-1/2 left-1/2 max-h-[90vh] -translate-x-1/2 -translate-y-1/2 overflow-y-auto sm:max-w-2xl"
    >
        <DialogHeader>
            <DialogTitle>{{
                props.label || (isEdit ? 'Edit Category' : 'Create Category')
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
                        :default-value="props.category?.name"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        placeholder="Electronics"
                        :class="{
                            'border-destructive text-destructive placeholder:text-destructive focus:ring-destructive':
                                errors.name,
                        }"
                    />
                    <InputError v-if="errors.name" :message="errors.name" />
                </div>
                <div class="grid gap-3">
                    <Label for="is_active">Active</Label>
                    <RadioGroup
                        id="is_active"
                        name="is_active"
                        :tabindex="5"
                        :default-value="
                            props.category?.is_active ? '1' : '0'
                        "
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
                              ? 'Update Category'
                              : 'Save Category'
                    }}
                </Button>
            </div>
        </Form>

        <AuditableTable auditable-type="Category" />
    </DialogContent>
</template>
