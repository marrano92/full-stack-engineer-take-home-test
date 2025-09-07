<template>
    <div class="relative">
        <div class="relative">
            <input
                :id="inputId"
                v-model="searchQuery"
                type="text"
                :placeholder="placeholder"
                :class="inputClass"
                @input="onInput"
                @focus="onFocus"
                @blur="onBlur"
                @keydown.down.prevent="navigateDown"
                @keydown.up.prevent="navigateUp"
                @keydown.enter.prevent="selectCurrentSuggestion"
                @keydown.escape="hideSuggestions"
                autocomplete="off"
            />

            <div v-if="isLoading" class="absolute top-1/2 right-3 -translate-y-1/2 transform">
                <div class="h-4 w-4 animate-spin rounded-full border-2 border-gray-300 border-t-blue-600"></div>
            </div>
        </div>

        <!-- Suggestions dropdown -->
        <div
            v-if="showSuggestions && (suggestions.length > 0 || isNewOwner)"
            class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-300 bg-white shadow-lg"
        >
            <!-- Existing owners -->
            <div
                v-for="(owner, index) in suggestions"
                :key="owner.id"
                :class="['cursor-pointer px-4 py-2 hover:bg-gray-100', { 'bg-blue-50': index === highlightedIndex }]"
                @mousedown.prevent="selectOwnerAndEmit(owner)"
                @mouseenter="highlightedIndex = index"
            >
                <div class="font-medium">{{ owner.name }}</div>
            </div>

            <!-- Create new owner option -->
            <div
                v-if="isNewOwner"
                :class="[
                    'cursor-pointer border-t border-gray-200 px-4 py-2 hover:bg-gray-100',
                    { 'bg-blue-50': suggestions.length === highlightedIndex },
                ]"
                @mousedown.prevent="selectNewOwner"
                @mouseenter="highlightedIndex = suggestions.length"
            >
                <div class="font-medium text-blue-600">
                    <span class="text-sm">Create new owner: </span>
                    <strong>"{{ searchQuery }}"</strong>
                </div>
            </div>
        </div>

        <!-- Error message -->
        <p v-if="errorMessage" class="mt-1 text-sm text-red-600">{{ errorMessage }}</p>
    </div>
</template>

<script setup lang="ts">
import { useOwnerAutocomplete } from '@/composables/useOwnerAutocomplete';
// Watch for external changes to modelValue
import { ref, watch } from 'vue';

interface Props {
    inputId?: string;
    placeholder?: string;
    inputClass?: string;
    modelValue?: string;
    errorMessage?: string;
}

interface Emits {
    (e: 'update:modelValue', value: string): void;
    (e: 'owner-selected', owner: { id: number; name: string } | null): void;
}

const props = withDefaults(defineProps<Props>(), {
    inputId: 'owner-autocomplete',
    placeholder: 'Search or enter owner name...',
    inputClass: 'mt-1 w-full rounded border px-3 py-2',
    modelValue: '',
});

const emit = defineEmits<Emits>();

const {
    searchQuery,
    suggestions,
    isLoading,
    showSuggestions,
    isNewOwner,
    onInput: handleInput,
    selectOwner,
    hideSuggestions,
    setInitialValue,
} = useOwnerAutocomplete();

const highlightedIndex = ref(-1);

// Initialize with prop value
if (props.modelValue) {
    setInitialValue(props.modelValue);
}

function onInput(event: Event) {
    const target = event.target as HTMLInputElement;
    handleInput(target.value);
    emit('update:modelValue', target.value);
    highlightedIndex.value = -1;
}

function onFocus() {
    if (searchQuery.value && suggestions.value.length === 0) {
        handleInput(searchQuery.value);
    }
}

function onBlur() {
    setTimeout(() => {
        hideSuggestions();
    }, 200);
}

function navigateDown() {
    const maxIndex = suggestions.value.length + (isNewOwner.value ? 1 : 0) - 1;
    if (highlightedIndex.value < maxIndex) {
        highlightedIndex.value++;
    }
}

function navigateUp() {
    if (highlightedIndex.value > 0) {
        highlightedIndex.value--;
    }
}

function selectCurrentSuggestion() {
    if (highlightedIndex.value >= 0) {
        if (highlightedIndex.value < suggestions.value.length) {
            selectOwnerAndEmit(suggestions.value[highlightedIndex.value]);
        } else if (isNewOwner.value) {
            selectNewOwner();
        }
    }
}

function selectNewOwner() {
    if (searchQuery.value.trim()) {
        emit('owner-selected', { id: -1, name: searchQuery.value.trim() });
        hideSuggestions();
    }
}

// Override selectOwner to emit the selected owner
function selectOwnerAndEmit(owner: any) {
    selectOwner(owner);
    emit('owner-selected', owner);
}

watch(
    () => props.modelValue,
    (newValue) => {
        if (newValue !== searchQuery.value) {
            setInitialValue(newValue);
        }
    },
);
</script>
