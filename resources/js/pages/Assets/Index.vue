<template>
    <Head title="Assets - List" />
    <AppLayout>
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">Assets</h1>
            </div>
            <div class="overflow-x-auto rounded border">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left">Reference</th>
                            <th class="px-3 py-2 text-left">Serial Number</th>
                            <th class="px-3 py-2 text-left">Owned By</th>
                            <th class="px-3 py-2 text-left">Owned From</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="asset in componentProperties.assets.data" :key="asset.id" class="border-t">
                            <td class="px-3 py-2">{{ asset.reference }}</td>
                            <td class="px-3 py-2">{{ asset.serial_number }}</td>
                            <td class="px-3 py-2">{{ getOwnerFullName(asset.owner) }}</td>
                            <td class="px-3 py-2">{{ formatDateTimeForDisplay(asset.current_owned_from) }}</td>
                            <td class="space-x-2 px-3 py-2 text-right">
                                <button
                                    class="btn-secondary px-2 py-1"
                                    @click="router.visit(route('assets.edit', asset.id))"
                                    aria-label="Modifica asset"
                                >
                                    Modify
                                </button>
                                <button
                                    class="btn-destructive px-2 py-1"
                                    @click="deleteAssetByIdentifier(asset.id)"
                                    aria-label="Elimina asset"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!componentProperties.assets.data?.length">
                            <td colspan="5" class="px-3 py-6 text-center text-gray-500">Any asset present!</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap gap-2">
                <button
                    v-for="navigationLink in componentProperties.assets.links"
                    :key="navigationLink.label"
                    class="rounded border px-3 py-1"
                    :class="{
                        'bg-blue-600 text-white': navigationLink.active,
                        'cursor-not-allowed opacity-50': !navigationLink.url,
                    }"
                    :disabled="!navigationLink.url"
                    v-html="navigationLink.label"
                    @click="navigationLink.url && router.visit(navigationLink.url)"
                />
            </div>
            <div class="flex items-center justify-end">
                <button class="btn-primary px-3 py-2" @click="router.visit(route('assets.create'))" aria-label="Crea nuovo asset">
                    Add new asset
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { AssetRow, Nullable, Owner, Pagination } from '@/types/model';
import { Head, router } from '@inertiajs/vue3';

const componentProperties = defineProps<{
    assets: Pagination<AssetRow>;
}>();

function getOwnerFullName(owner: Nullable<Owner>): string {
    if (!owner) return '-';
    return `${owner.first_name} ${owner.last_name}`;
}

function formatDateTimeForDisplay(dateTime: Nullable<string>): string {
    if (!dateTime) return '-';
    return new Date(dateTime).toLocaleString();
}

function deleteAssetByIdentifier(assetIdentifier: number): void {
    if (!confirm('Do you want to delete this asset?')) return;
    router.delete(route('assets.destroy', assetIdentifier), {
        preserveScroll: true,
    });
}
</script>
