<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed } from 'vue';
import { store, update } from '@/routes/transactions';
import type { Asset } from '@/types/asset';
import type { AssetTransaction } from '@/types/transaction';
import type { UserDB } from '@/types/user';
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
    transaction?: AssetTransaction;
    Description?: string;
    assets: Pick<Asset, 'id' | 'name'>[];
    users: Pick<UserDB, 'id' | 'name' | 'email'>[];
}>();

const emit = defineEmits<{
    close: [];
}>();

const isEdit = computed(() => !!props.transaction);
const formTarget = computed(() =>
    isEdit.value ? update.form(props.transaction!.id) : store.form(),
);

function onSuccess() {
    emit('close');
}

const formattedBorrowedAt = computed(() => {
    if (!props.transaction?.borrowed_at) {
        return '';
    }

    return props.transaction.borrowed_at.split('T')[0].split(' ')[0];
});

const formattedReturnedAt = computed(() => {
    if (!props.transaction?.returned_at) {
        return '';
    }

    return props.transaction.returned_at.split('T')[0].split(' ')[0];
});
</script>

<template>
    <DialogContent class="fixed top-1/2 sm:max-w-106.25">
        <DialogHeader>
            <DialogTitle>{{
                props.label ||
                (isEdit ? 'Edit Transaction' : 'Create Transaction')
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
                    <Label for="asset_id">Asset</Label>
                    <select
                        id="asset_id"
                        name="asset_id"
                        required
                        class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:ring-1 focus:ring-ring focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{ 'border-destructive': errors.asset_id }"
                    >
                        <option
                            value=""
                            disabled
                            :selected="!props.transaction"
                        >
                            Select Asset
                        </option>
                        <option
                            v-for="asset in assets"
                            :key="asset.id"
                            :value="asset.id"
                            :selected="props.transaction?.asset_id === asset.id"
                        >
                            {{ asset.name }}
                        </option>
                    </select>
                    <InputError
                        v-if="errors.asset_id"
                        :message="errors.asset_id"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="user_id">User</Label>
                    <select
                        id="user_id"
                        name="user_id"
                        required
                        class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:ring-1 focus:ring-ring focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        :class="{ 'border-destructive': errors.user_id }"
                    >
                        <option
                            value=""
                            disabled
                            :selected="!props.transaction"
                        >
                            Select User
                        </option>
                        <option
                            v-for="user in users"
                            :key="user.id"
                            :value="user.id"
                            :selected="props.transaction?.user_id === user.id"
                        >
                            {{ user.name }} ({{ user.email }})
                        </option>
                    </select>
                    <InputError
                        v-if="errors.user_id"
                        :message="errors.user_id"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="borrowed_at">Borrowed At</Label>
                    <Input
                        id="borrowed_at"
                        type="date"
                        name="borrowed_at"
                        :default-value="formattedBorrowedAt"
                        required
                        :class="{ 'border-destructive': errors.borrowed_at }"
                    />
                    <InputError
                        v-if="errors.borrowed_at"
                        :message="errors.borrowed_at"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="is_returned">Returned</Label>
                    <RadioGroup
                        id="is_returned"
                        name="is_returned"
                        :default-value="
                            props.transaction?.is_returned ? '1' : '0'
                        "
                    >
                        <div class="flex items-center space-x-2">
                            <RadioGroupItem name="is_returned" value="1" />
                            <Label for="returned">Yes</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <RadioGroupItem name="is_returned" value="0" />
                            <Label for="not_returned">No</Label>
                        </div>
                        <InputError
                            v-if="errors.is_returned"
                            :message="errors.is_returned"
                        />
                    </RadioGroup>
                </div>

                <div class="grid gap-2">
                    <Label for="returned_at">Returned At (Optional)</Label>
                    <Input
                        id="returned_at"
                        type="date"
                        name="returned_at"
                        :default-value="formattedReturnedAt"
                        :class="{ 'border-destructive': errors.returned_at }"
                    />
                    <InputError
                        v-if="errors.returned_at"
                        :message="errors.returned_at"
                    />
                </div>
                <div class="grid gap-2">
                    <Label for="notes">Notes (Optional)</Label>
                    <Input
                        id="notes"
                        type="text"
                        :name="'notes[' + 0 + ']'"
                        :default-value="String(props.transaction?.notes || '')"
                        :class="{ 'border-destructive': errors.notes }"
                    />
                    <InputError v-if="errors.notes" :message="errors.notes" />
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
                              ? 'Update Transaction'
                              : 'Save Transaction'
                    }}
                </Button>
            </div>
        </Form>
    </DialogContent>
</template>
