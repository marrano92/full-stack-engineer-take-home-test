<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import AppLogoIconCompact from '@/components/AppLogoIconCompact.vue';
import { useSidebar } from '@/components/ui/sidebar/utils';

const { state } = useSidebar();
</script>

<template>
    <transition
        mode="out-in"
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="scale-90 opacity-0"
        enter-to-class="scale-100 opacity-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="scale-100 opacity-100"
        leave-to-class="scale-90 opacity-0"
    >
        <AppLogoIconCompact 
            v-if="state === 'collapsed'"
            key="compact"
            class="w-8 h-8 mx-auto fill-current text-sidebar-primary dark:text-sidebar-primary" 
        />
        <AppLogoIcon 
            v-else
            key="full"
            class="w-full fill-current text-sidebar-primary dark:text-sidebar-primary" 
        />
    </transition>
</template>
