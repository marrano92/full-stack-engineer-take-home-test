<template>
    <Head title="Assets - List" />
    <AppLayout>
        <div class="space-y-4 p-6">
            <div class="overflow-x-auto border">
                <div class="flex items-center justify-between bg-secondary px-6 py-4 text-secondary-foreground">
                    <h1 class="text-xl font-semibold">List of Assets</h1>
                </div>
                <table class="min-w-full text-sm">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left">Reference</th>
                            <th class="px-3 py-2 text-left">Serial N°</th>
                            <th class="px-3 py-2 text-left">Description</th>
                            <th class="px-3 py-2 text-left">Owned By</th>
                            <th class="px-3 py-2 text-left">Owned From</th>
                            <th class="px-3 py-2 text-center"></th>
                            <th class="px-3 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="asset in componentProperties.assets.data" :key="asset.id" class="border-t">
                            <td class="px-3 py-2">{{ asset.reference }}</td>
                            <td class="px-3 py-2">{{ asset.serial_number }}</td>
                            <td class="px-3 py-2">{{ asset.description || '-' }}</td>
                            <td class="px-3 py-2">{{ getOwnerFullName(asset.owner) }}</td>
                            <td class="px-3 py-2">{{ formatDateTimeForDisplay(asset.current_owned_from) }}</td>
                            <td class="px-3 py-2">
                                <button
                                    class="flex w-full items-center justify-center gap-2 rounded-full px-4 py-2 text-white shadow-md"
                                    style="background-color: #f54f52"
                                    @click="deleteAssetByIdentifier(asset.id)"
                                    aria-label="Delete asset"
                                >
                                    <Trash2 class="h-4 w-4" />
                                    Delete
                                </button>
                            </td>
                            <td class="px-3 py-2">
                                <button
                                    class="flex w-full items-center justify-center gap-2 rounded-full px-4 py-2 text-white shadow-md"
                                    style="background-color: #4053ff"
                                    @click="router.visit(route('assets.edit', asset.id))"
                                    aria-label="Modify asset"
                                >
                                    <Edit class="h-4 w-4" />
                                    Modify
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!componentProperties.assets.data?.length">
                            <td colspan="7" class="px-3 py-6 text-center text-gray-500">Any asset present!</td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex items-center justify-end border-t bg-white px-6 py-4">
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-700">
                            {{ componentProperties.assets.from }}-{{ componentProperties.assets.to }} of {{ componentProperties.assets.total }}
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="flex items-center rounded border px-3 py-1"
                                :class="{
                                    'cursor-not-allowed opacity-50': componentProperties.assets.current_page === 1,
                                }"
                                :disabled="componentProperties.assets.current_page === 1"
                                @click="navigateToPreviousPage()"
                            >
                                <ChevronLeft class="h-4 w-4" />
                            </button>
                            <button
                                class="flex items-center rounded border px-3 py-1"
                                :class="{
                                    'cursor-not-allowed opacity-50': componentProperties.assets.current_page === componentProperties.assets.last_page,
                                }"
                                :disabled="componentProperties.assets.current_page === componentProperties.assets.last_page"
                                @click="navigateToNextPage()"
                            >
                                <ChevronRight class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end">
                <button
                    class="btn-action flex items-center gap-2 px-3 py-2"
                    @click="router.visit(route('assets.create'))"
                    aria-label="Crea nuovo asset"
                >
                    <SquarePlus class="h-4 w-4" />
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
import { ChevronLeft, ChevronRight, Edit, SquarePlus, Trash2 } from 'lucide-vue-next';

const componentProperties = defineProps<{
    assets: Pagination<AssetRow>;
}>();

function getOwnerFullName(owner: Nullable<Owner>): string {
    if (!owner) return '-';
    return `${owner.first_name} ${owner.last_name}`;
}

function formatDateTimeForDisplay(dateTime: Nullable<string>): string {
    if (!dateTime) return '-';
    return new Date(dateTime).toLocaleDateString();
}

function deleteAssetByIdentifier(assetIdentifier: number): void {
    if (!confirm('Do you want to delete this asset?')) return;
    router.delete(route('assets.destroy', assetIdentifier), {
        preserveScroll: true,
    });
}

function navigateToPreviousPage(): void {
    if (componentProperties.assets.current_page > 1) {
        const prevPageLink = componentProperties.assets.links.find((link) => link.label === '&laquo; Previous');
        if (prevPageLink?.url) {
            router.visit(prevPageLink.url);
        }
    }
}

function navigateToNextPage(): void {
    if (componentProperties.assets.current_page < componentProperties.assets.last_page) {
        const nextPageLink = componentProperties.assets.links.find((link) => link.label === 'Next &raquo;');
        if (nextPageLink?.url) {
            router.visit(nextPageLink.url);
        }
    }
}
</script>
