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
                            <input
                                id="reference"
                                type="text"
                                v-model="form.reference"
                                class="mt-1 w-full rounded border px-3 py-2"
                                autocomplete="off"
                            />
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
                            <label for="owner_autocomplete" class="block text-sm font-medium">Current Owner</label>
                            <OwnerAutocomplete
                                input-id="owner_autocomplete"
                                v-model="ownerSearchValue"
                                :error-message="form.errors.owner_id"
                                @owner-selected="handleOwnerSelected"
                            />
                        </div>

                        <div>
                            <label for="owned_from" class="block text-sm font-medium">Owned From</label>
                            <input id="owned_from" type="datetime-local" v-model="form.owned_from" class="mt-1 w-full rounded border px-3 py-2" />
                            <p class="mt-1 text-xs text-gray-500">If you select an owner and don't set the date, the current time will be used.</p>
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
                        <button type="submit" class="btn-primary" :disabled="form.processing">Create Asset</button>

                        <button type="button" class="btn-outline" @click="router.visit(route('assets.index'))">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import OwnerAutocomplete from '@/components/OwnerAutocomplete.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { AssetCreateUpdatePayload } from '@/types/model';
import { Head, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const form = useForm<AssetCreateUpdatePayload>({
    reference: '',
    serial_number: '',
    description: null,
    owner_id: null,
    owned_from: null,
});

const ownerSearchValue = ref('');
const selectedOwner = ref<{ id: number; name: string } | null>(null);
function handleOwnerSelected(owner: { id: number; name: string } | null) {
    selectedOwner.value = owner;

    if (owner && owner.id !== -1) {
        form.owner_id = owner.id;
    } else {
        form.owner_id = null;
    }
}

async function submitCreateAsset(): Promise<void> {
    try {
        if (selectedOwner.value && selectedOwner.value.id === -1) {
            const response = await axios.post('/api/owners/find-or-create', {
                name: selectedOwner.value.name,
            });

            form.owner_id = response.data.id;
        }

        form.post(route('assets.store'));
    } catch (error) {
        console.error('Error creating owner:', error);
    }
}
</script>
