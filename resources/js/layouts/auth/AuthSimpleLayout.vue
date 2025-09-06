<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh flex-col bg-background">
        <!-- Top Bar -->
        <div class="w-full h-16 flex items-center px-6 bg-secondary">
            <Link :href="route('assets.index')" class="flex items-center">
                <AppLogoIcon class="size-8 text-secondary-foreground" />
            </Link>
        </div>
        
        <!-- Main Content -->
        <div class="flex flex-1 items-center justify-center gap-6 p-6 md:p-10">
            <div class="w-full max-w-xl">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <div class="flex flex-col gap-8">
                        <div class="flex flex-col items-center gap-4">
                            <div class="space-y-2 text-center">
                                <h1 class="text-3xl font-medium text-secondary">{{ title }}</h1>
                                <p class="text-center text-sm text-muted-foreground">{{ description }}</p>
                            </div>
                        </div>
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
