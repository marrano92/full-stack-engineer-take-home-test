<template>
    <Head title="Assets - Edit" />
    <AppLayout>
        <div class="mx-auto w-full max-w-4xl space-y-8 p-6">
            <h1 class="flex items-center gap-2 text-2xl font-semibold">
                <Edit class="h-6 w-6" />
                Modify asset
            </h1>
            <div class="max-w-6xl rounded-lg bg-white p-6 shadow-sm">
                <form @submit.prevent="submitUpdateAsset" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="reference" class="block text-sm font-medium">Reference *</label>
                            <input id="reference" type="text" v-model="form.reference" class="mt-1 w-full rounded border px-3 py-2" />
                            <p v-if="form.errors.reference" class="mt-1 text-sm text-red-600">{{ form.errors.reference }}</p>
                        </div>

                        <div>
                            <label for="serial_number" class="block text-sm font-medium">Serial Number *</label>
                            <input id="serial_number" type="text" v-model="form.serial_number" class="mt-1 w-full rounded border px-3 py-2" />
                            <p v-if="form.errors.serial_number" class="mt-1 text-sm text-red-600">{{ form.errors.serial_number }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="owner_autocomplete_edit" class="block text-sm font-medium">Current Owner</label>
                            <OwnerAutocomplete
                                input-id="owner_autocomplete_edit"
                                v-model="ownerSearchValue"
                                :error-message="form.errors.owner_id"
                                @owner-selected="handleOwnerSelected"
                            />
                        </div>

                        <div>
                            <label for="owned_from" class="block text-sm font-medium">Owned From</label>
                            <input id="owned_from" type="datetime-local" v-model="form.owned_from" class="mt-1 w-full rounded border px-3 py-2" />
                            <p v-if="form.errors.owned_from" class="mt-1 text-sm text-red-600">{{ form.errors.owned_from }}</p>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium">Description</label>
                        <textarea id="description" v-model="form.description" rows="4" class="mt-1 w-full rounded border px-3 py-2" />
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="button" class="btn-outline" @click="router.visit(route('assets.index'))">CANCEL</button>

                        <button type="submit" class="btn-main flex items-center gap-2" :disabled="form.processing">
                            <Save class="h-4 w-4" />
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <section>
                <h2>Previous Owners</h2>
                <hr class="mb-4 border-t border-gray-300" />
                <div class="overflow-x-auto rounded border">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-2/5 px-3 py-2 text-left">Owner</th>
                                <th class="px-3 py-2 text-left">Owned From</th>
                                <th class="px-3 py-2 text-left">Owned Till</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="entry in componentProperties.history.data" :key="entry.id" class="border-t">
                                <td class="w-2/5 px-3 py-2">
                                    {{ entry.owner ? getOwnerFullName(entry.owner) : '—' }}
                                </td>
                                <td class="px-3 py-2">{{ formatDateTimeForDisplay(entry.owned_from) }}</td>
                                <td class="px-3 py-2">{{ entry.owned_to ? formatDateTimeForDisplay(entry.owned_to) : 'In charge' }}</td>
                            </tr>
                            <tr v-if="!componentProperties.history.data?.length">
                                <td colspan="3" class="px-3 py-6 text-center text-gray-500">Any history available</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import OwnerAutocomplete from '@/components/OwnerAutocomplete.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { AssetCreateUpdatePayload, EditAssetProps, Owner } from '@/types/model';
import { Head, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { Edit, Save } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

const componentProperties = defineProps<EditAssetProps>();

const form = useForm<AssetCreateUpdatePayload>({
    reference: componentProperties.asset.reference,
    serial_number: componentProperties.asset.serial_number,
    description: componentProperties.asset.description,
    owner_id: componentProperties.asset.current_owner_id,
    owned_from: convertIsoToDatetimeLocal(componentProperties.asset.current_owned_from),
});

const ownerSearchValue = ref('');
const selectedOwner = ref<{ id: number; name: string } | null>(null);

function getOwnerFullName(owner: Owner): string {
    return `${owner.first_name} ${owner.last_name}`;
}

function formatDateTimeForDisplay(dateTime: string): string {
    return new Date(dateTime).toLocaleString();
}

function convertIsoToDatetimeLocal(iso: string | null): string | null {
    if (!iso) return null;
    const date = new Date(iso);
    const pad = (n: number) => n.toString().padStart(2, '0');
    const y = date.getFullYear();
    const m = pad(date.getMonth() + 1);
    const d = pad(date.getDate());
    const h = pad(date.getHours());
    const i = pad(date.getMinutes());
    return `${y}-${m}-${d}T${h}:${i}`;
}

function handleOwnerSelected(owner: { id: number; name: string } | null) {
    selectedOwner.value = owner;

    if (owner && owner.id !== -1) {
        form.owner_id = owner.id;
    } else {
        form.owner_id = null;
    }
}

async function submitUpdateAsset(): Promise<void> {
    try {
        if (selectedOwner.value && selectedOwner.value.id === -1) {
            const response = await axios.post('/api/owners/find-or-create', {
                name: selectedOwner.value.name,
            });

            form.owner_id = response.data.id;
        }

        form.put(route('assets.update', componentProperties.asset.id), {
            preserveScroll: true,
            onError: () => window.scrollTo({ top: 0, behavior: 'smooth' }),
        });
    } catch (error) {
        console.error('Error creating owner:', error);
    }
}

onMounted(() => {
    const currentOwner = componentProperties.owners.find((owner) => owner.id === componentProperties.asset.current_owner_id);
    if (currentOwner) {
        ownerSearchValue.value = getOwnerFullName(currentOwner);
        selectedOwner.value = {
            id: currentOwner.id,
            name: getOwnerFullName(currentOwner),
        };
    }
});
</script>
