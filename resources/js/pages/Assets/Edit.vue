<template>
    <Head title="Assets - Edit" />
    <AppLayout>
        <div class="mx-auto max-w-5xl space-y-8 p-6">
            <h1 class="text-2xl font-semibold">Modifica asset</h1>

            <form @submit.prevent="submitUpdateAsset" class="max-w-3xl space-y-4">
                <!-- Reference -->
                <div>
                    <label for="reference" class="block text-sm font-medium">Reference *</label>
                    <input id="reference" type="text" v-model="form.reference" class="mt-1 w-full rounded border px-3 py-2" />
                    <p v-if="form.errors.reference" class="mt-1 text-sm text-red-600">{{ form.errors.reference }}</p>
                </div>

                <!-- Serial Number -->
                <div>
                    <label for="serial_number" class="block text-sm font-medium">Serial Number *</label>
                    <input id="serial_number" type="text" v-model="form.serial_number" class="mt-1 w-full rounded border px-3 py-2" />
                    <p v-if="form.errors.serial_number" class="mt-1 text-sm text-red-600">{{ form.errors.serial_number }}</p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium">Description</label>
                    <textarea id="description" v-model="form.description" rows="4" class="mt-1 w-full rounded border px-3 py-2" />
                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>

                <!-- Owned By -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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

                    <div>
                        <label for="owned_from" class="block text-sm font-medium">Owned From</label>
                        <input id="owned_from" type="datetime-local" v-model="form.owned_from" class="mt-1 w-full rounded border px-3 py-2" />
                        <p v-if="form.errors.owned_from" class="mt-1 text-sm text-red-600">{{ form.errors.owned_from }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50" :disabled="form.processing">
                        Salva modifiche
                    </button>

                    <button type="button" class="rounded border px-4 py-2" @click="router.visit(route('assets.index'))">Torna alla lista</button>
                </div>
            </form>

            <!-- History -->
            <section>
                <h2 class="mb-3 text-xl font-semibold">Owner history</h2>
                <div class="overflow-x-auto rounded border">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Owner</th>
                                <th class="px-3 py-2 text-left">Owned From</th>
                                <th class="px-3 py-2 text-left">Owned To</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="entry in componentProperties.history.data" :key="entry.id" class="border-t">
                                <td class="px-3 py-2">
                                    {{ entry.owner ? getOwnerFullName(entry.owner) : '—' }}
                                </td>
                                <td class="px-3 py-2">{{ formatDateTimeForDisplay(entry.owned_from) }}</td>
                                <td class="px-3 py-2">{{ entry.owned_to ? formatDateTimeForDisplay(entry.owned_to) : 'In corso' }}</td>
                            </tr>
                            <tr v-if="!componentProperties.history.data?.length">
                                <td colspan="3" class="px-3 py-6 text-center text-gray-500">Nessuna history disponibile</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { AssetCreateUpdatePayload, EditAssetProps, Owner } from '@/types/model';
import { Head, router, useForm } from '@inertiajs/vue3';

const componentProperties = defineProps<EditAssetProps>();

const form = useForm<AssetCreateUpdatePayload>({
    reference: componentProperties.asset.reference,
    serial_number: componentProperties.asset.serial_number,
    description: componentProperties.asset.description,
    owner_id: componentProperties.asset.current_owner_id,
    owned_from: convertIsoToDatetimeLocal(componentProperties.asset.current_owned_from),
});

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

function submitUpdateAsset(): void {
    router.put(route('assets.update', componentProperties.asset.id), form.data(), {
        preserveScroll: true,
        onError: () => window.scrollTo({ top: 0, behavior: 'smooth' }),
    });
}
</script>
