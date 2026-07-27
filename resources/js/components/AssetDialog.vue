<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import { store, update } from '@/routes/assets';
import type { Asset } from '@/types/asset';
import type { Category } from '@/types/category';
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

const props = defineProps<{
    label?: string;
    asset?: Asset;
    Description?: string;
    categories: Pick<Category, 'id' | 'name'>[];
}>();

const emit = defineEmits<{
    close: [];
}>();

const isEdit = computed(() => !!props.asset);
const formTarget = computed(() =>
    isEdit.value ? update.form(props.asset!.id) : store.form(),
);

function onSuccess() {
    emit('close');
}

// Convert date for datetime-local or date input
const formattedDate = computed(() => {
    if (!props.asset?.purchase_date) return '';
    // if purchase_date is "2023-10-10 12:00:00", we need to extract "2023-10-10"
    return props.asset.purchase_date.split('T')[0].split(' ')[0];
});
</script>

<template>
    <DialogContent class="fixed top-1/2 sm:max-w-106.25">
        <DialogHeader>
            <DialogTitle>{{
                props.label || (isEdit ? 'Edit Asset' : 'Create Asset')
            }}</DialogTitle>
            <DialogDescription>
                {{ props.Description }}
            </DialogDescription>
        </DialogHeader>

        <Form
            v-bind="formTarget"
            v-slot="{ errors, processing }"
            class=""
            @success="onSuccess"
        >
            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        name="name"
                        :default-value="props.asset?.name"
                        required
                        autofocus
                        placeholder="Macbook Pro M3"
                        :class="{ 'border-destructive': errors.name }"
                    />
                    <InputError v-if="errors.name" :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="category_id">Category</Label>
                    <select
                        id="category_id"
                        name="category_id"
                        required
                        class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{ 'border-destructive': errors.category_id }"
                    >
                        <option value="" disabled :selected="!props.asset">Select Category</option>
                        <option 
                            v-for="cat in categories" 
                            :key="cat.id" 
                            :value="cat.id"
                            :selected="props.asset?.category_id === cat.id"
                        >
                            {{ cat.name }}
                        </option>
                    </select>
                    <InputError v-if="errors.category_id" :message="errors.category_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="purchase_date">Purchase Date</Label>
                    <Input
                        id="purchase_date"
                        type="date"
                        name="purchase_date"
                        :default-value="formattedDate"
                        required
                        :class="{ 'border-destructive': errors.purchase_date }"
                    />
                    <InputError v-if="errors.purchase_date" :message="errors.purchase_date" />
                </div>

                <div class="grid gap-2">
                    <Label for="is_active">Status</Label>
                    <RadioGroup
                        id="is_active"
                        name="is_active"
                        :default-value="props.asset?.is_active ? '1' : '0'"
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

                <div class="grid gap-2">
                    <Label for="attachment">PDF Attachment (100KB - 500KB)</Label>
                    <Input
                        id="attachment"
                        type="file"
                        name="attachment"
                        accept="application/pdf"
                        :class="{ 'border-destructive': errors.attachment }"
                    />
                    <p class="text-[11px] text-on-surface-variant">Leave blank to keep existing attachment on edit.</p>
                    <InputError v-if="errors.attachment" :message="errors.attachment" />
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
                              ? 'Update Asset'
                              : 'Save Asset'
                    }}
                </Button>
            </div>
        </Form>
    </DialogContent>
</template>
