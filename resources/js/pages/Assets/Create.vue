<template>
    <Head title="Assets - Create" />
    <AppLayout>
        <div class="mx-auto max-w-6xl space-y-6 p-6">
            <h1 class="text-2xl font-semibold">Create new asset</h1>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form @submit.prevent="submitCreateAsset" class="space-y-4">
                    <!-- First row: Reference and Serial Number -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="reference" class="block text-sm font-medium">Reference *</label>
                            <input id="reference" type="text" v-model="form.reference" class="mt-1 w-full rounded border px-3 py-2" autocomplete="off" />
                            <p v-if="form.errors.reference" class="mt-1 text-sm text-red-600">{{ form.errors.reference }}</p>
                        </div>

                        <div>
                            <label for="serial_number" class="block text-sm font-medium">Serial Number *</label>
                            <input
                                id="serial_number"
                                type="text"
                                v-model="form.serial_number"
                                class="mt-1 w-full rounded border px-3 py-2"
                                autocomplete="off"
                            />
                            <p v-if="form.errors.serial_number" class="mt-1 text-sm text-red-600">{{ form.errors.serial_number }}</p>
                        </div>
                    </div>

                    <!-- Second row: Current Owner and Owned From -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="owner_id" class="block text-sm font-medium">Current Owner</label>
                            <select id="owner_id" v-model="form.owner_id" class="mt-1 w-full rounded border px-3 py-2">
                                <option :value="null">— Nessun owner —</option>
                                <option v-for="owner in componentProperties.owners" :key="owner.id" :value="owner.id">
                                    {{ getOwnerFullName(owner) }}
                                </option>
                            </select>
                            <p v-if="form.errors.owner_id" class="mt-1 text-sm text-red-600">{{ form.errors.owner_id }}</p>
                        </div>

                        <div>
                            <label for="owned_from" class="block text-sm font-medium">Owned From</label>
                            <input id="owned_from" type="datetime-local" v-model="form.owned_from" class="mt-1 w-full rounded border px-3 py-2" />
                            <p class="mt-1 text-xs text-gray-500">Se selezioni un owner e non imposti la data, verrà usata l'ora corrente.</p>
                            <p v-if="form.errors.owned_from" class="mt-1 text-sm text-red-600">{{ form.errors.owned_from }}</p>
                        </div>
                    </div>

                    <!-- Third row: Description (full width) -->
                    <div>
                        <label for="description" class="block text-sm font-medium">Description</label>
                        <textarea id="description" v-model="form.description" rows="4" class="mt-1 w-full rounded border px-3 py-2" />
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                    </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        Create Asset
                    </button>

                    <button type="button" class="btn-outline" @click="router.visit(route('assets.index'))">Annulla</button>
                </div>
            </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { AssetCreateUpdatePayload, Owner } from '@/types/model';
import { Head, router, useForm } from '@inertiajs/vue3';

interface CreateAssetProps {
    owners: Owner[];
}
const componentProperties = defineProps<CreateAssetProps>();

const form = useForm<AssetCreateUpdatePayload>({
    reference: '',
    serial_number: '',
    description: null,
    owner_id: null,
    owned_from: null,
});

function getOwnerFullName(owner: Owner): string {
    return `${owner.first_name} ${owner.last_name}`;
}

function submitCreateAsset(): void {
    form.post(route('assets.store'));
}
</script>
