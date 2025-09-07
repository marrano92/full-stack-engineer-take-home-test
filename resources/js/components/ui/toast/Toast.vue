<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { X, CheckCircle, AlertCircle, AlertTriangle, Info } from 'lucide-vue-next';

interface Props {
  type?: 'success' | 'error' | 'warning' | 'info';
  message: string;
  duration?: number;
  show?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'success',
  duration: 5000,
  show: true,
});

const emit = defineEmits<{
  close: [];
}>();

const visible = ref(props.show);

const toastClasses = computed(() => {
  const baseClasses = 'fixed top-4 right-4 z-50 flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform';
  
  const typeClasses = {
    success: 'bg-green-50 border border-green-200 text-green-800',
    error: 'bg-red-50 border border-red-200 text-red-800',
    warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800',
    info: 'bg-blue-50 border border-blue-200 text-blue-800',
  };

  const visibilityClasses = visible.value 
    ? 'translate-x-0 opacity-100' 
    : 'translate-x-full opacity-0';

  return `${baseClasses} ${typeClasses[props.type]} ${visibilityClasses}`;
});

const iconComponent = computed(() => {
  const icons = {
    success: CheckCircle,
    error: AlertCircle,
    warning: AlertTriangle,
    info: Info,
  };
  return icons[props.type];
});

const iconClasses = computed(() => {
  const classes = {
    success: 'text-green-600',
    error: 'text-red-600',
    warning: 'text-yellow-600',
    info: 'text-blue-600',
  };
  return `h-5 w-5 ${classes[props.type]}`;
});

function closeToast() {
  visible.value = false;
  setTimeout(() => emit('close'), 300);
}

onMounted(() => {
  if (props.duration > 0) {
    setTimeout(() => {
      closeToast();
    }, props.duration);
  }
});
</script>

<template>
  <Transition
    enter-active-class="transition-all duration-300 ease-out"
    enter-from-class="translate-x-full opacity-0"
    enter-to-class="translate-x-0 opacity-100"
    leave-active-class="transition-all duration-300 ease-in"
    leave-from-class="translate-x-0 opacity-100"
    leave-to-class="translate-x-full opacity-0"
  >
    <div v-if="visible" :class="toastClasses">
      <component :is="iconComponent" :class="iconClasses" />
      <span class="flex-1 text-sm font-medium">{{ message }}</span>
      <button
        @click="closeToast"
        class="ml-2 text-gray-400 hover:text-gray-600 transition-colors"
        aria-label="Close notification"
      >
        <X class="h-4 w-4" />
      </button>
    </div>
  </Transition>
</template>