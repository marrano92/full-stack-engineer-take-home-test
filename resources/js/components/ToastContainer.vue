<script setup lang="ts">
import { computed, watch, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Toast from '@/components/ui/toast/Toast.vue';

interface ToastItem {
  id: number;
  type: 'success' | 'error' | 'warning' | 'info';
  message: string;
}

const page = usePage();
const toasts = ref<ToastItem[]>([]);
let toastIdCounter = 0;

const flashMessages = computed(() => {
  const flash = page.props.flash as Record<string, string> | undefined;
  return flash || {};
});

function addToast(type: 'success' | 'error' | 'warning' | 'info', message: string) {
  const id = ++toastIdCounter;
  toasts.value.push({ id, type, message });
}

function removeToast(id: number) {
  const index = toasts.value.findIndex(toast => toast.id === id);
  if (index > -1) {
    toasts.value.splice(index, 1);
  }
}

watch(
  flashMessages,
  (newFlash) => {
    if (newFlash.success) {
      addToast('success', newFlash.success);
    }
    if (newFlash.error) {
      addToast('error', newFlash.error);
    }
    if (newFlash.warning) {
      addToast('warning', newFlash.warning);
    }
    if (newFlash.info) {
      addToast('info', newFlash.info);
    }
  },
  { immediate: true, deep: true }
);
</script>

<template>
  <div class="toast-container">
    <Toast
      v-for="toast in toasts"
      :key="toast.id"
      :type="toast.type"
      :message="toast.message"
      @close="removeToast(toast.id)"
    />
  </div>
</template>

<style scoped>
.toast-container {
  position: fixed;
  top: 0;
  right: 0;
  z-index: 9999;
  pointer-events: none;
}

.toast-container > * {
  pointer-events: auto;
}
</style>