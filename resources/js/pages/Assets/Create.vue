<template>
    <Head title="Assets - Create" />
    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 p-6">
            <h1 class="text-2xl font-semibold">Create new asset</h1>

            <form @submit.prevent="submitCreateAsset" class="space-y-4">
                <!-- Reference -->
                <div>
                    <label for="reference" class="block text-sm font-medium">Reference *</label>
                    <input id="reference" type="text" v-model="form.reference" class="mt-1 w-full rounded border px-3 py-2" autocomplete="off" />
                    <p v-if="form.errors.reference" class="mt-1 text-sm text-red-600">{{ form.errors.reference }}</p>
                </div>

                <!-- Serial Number -->
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

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium">Description</label>
                    <textarea id="description" v-model="form.description" rows="4" class="mt-1 w-full rounded border px-3 py-2" />
                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>

                <!-- Owned By (Owner) -->
                <div>
                    <label for="owner_id" class="block text-sm font-medium">Owned By</label>
                    <select id="owner_id" v-model="form.owner_id" class="mt-1 w-full rounded border px-3 py-2">
                        <option :value="null">— Nessun owner —</option>
                        <option v-for="owner in componentProperties.owners" :key="owner.id" :value="owner.id">
                            {{ getOwnerFullName(owner) }}
                        </option>
                    </select>
                    <p v-if="form.errors.owner_id" class="mt-1 text-sm text-red-600">{{ form.errors.owner_id }}</p>
                </div>

                <!-- Owned From -->
                <div>
                    <label for="owned_from" class="block text-sm font-medium">Owned From (opzionale)</label>
                    <input id="owned_from" type="datetime-local" v-model="form.owned_from" class="mt-1 w-full rounded border px-3 py-2" />
                    <p class="mt-1 text-xs text-gray-500">Se selezioni un owner e non imposti la data, verrà usata l’ora corrente.</p>
                    <p v-if="form.errors.owned_from" class="mt-1 text-sm text-red-600">{{ form.errors.owned_from }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50" :disabled="form.processing">
                        Create Asset
                    </button>

                    <button type="button" class="rounded border px-4 py-2" @click="router.visit(route('assets.index'))">Annulla</button>
                </div>
            </form>
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
