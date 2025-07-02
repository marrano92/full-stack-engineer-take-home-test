<template>
    <AppLayout>
        <Head title="Assets - Edit" />
        <form @submit.prevent="submit" class="flex">
            <div>
                <label>
                    Field1:
                    <input v-model="form.field1">
                </label>
                <p v-if="form.errors.field1">
                    {{ form.errors.field1 }}
                </p>
            </div>
        </form>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Asset {
    id?: number,
    field1?: string,
}

const props = defineProps<{
    asset: Asset;
}>()

const form = useForm({
    field1: props.asset?.field1,
});

const submit = () => {
    form.post(route('assets.edit', {
        asset: props.asset.id,
    }));
};
</script>
