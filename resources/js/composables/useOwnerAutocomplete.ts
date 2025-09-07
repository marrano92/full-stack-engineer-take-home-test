import { ref, computed } from 'vue';
import axios from 'axios';

interface Owner {
  id: number;
  name: string;
  first_name: string;
  last_name: string;
}

export function useOwnerAutocomplete() {
  const searchQuery = ref('');
  const suggestions = ref<Owner[]>([]);
  const isLoading = ref(false);
  const showSuggestions = ref(false);
  const selectedOwner = ref<Owner | null>(null);
  const debounceTimeout = ref<NodeJS.Timeout | null>(null);

  const isNewOwner = computed(() => {
    if (!searchQuery.value.trim()) return false;
    
    const exactMatch = suggestions.value.some(
      owner => owner.name.toLowerCase() === searchQuery.value.toLowerCase()
    );
    
    return searchQuery.value.trim().length > 0 && !exactMatch;
  });

  async function searchOwners(query: string) {
    if (!query.trim()) {
      suggestions.value = [];
      showSuggestions.value = false;
      return;
    }

    try {
      isLoading.value = true;
      const response = await axios.get('/api/owners/search', {
        params: { q: query }
      });
      
      suggestions.value = response.data;
      showSuggestions.value = true;
    } catch (error) {
      console.error('Error searching owners:', error);
      suggestions.value = [];
    } finally {
      isLoading.value = false;
    }
  }

  function debouncedSearch(query: string) {
    if (debounceTimeout.value) {
      clearTimeout(debounceTimeout.value);
    }
    
    debounceTimeout.value = setTimeout(() => {
      searchOwners(query);
    }, 300);
  }

  function onInput(value: string) {
    searchQuery.value = value;
    selectedOwner.value = null;
    debouncedSearch(value);
  }

  function selectOwner(owner: Owner) {
    searchQuery.value = owner.name;
    selectedOwner.value = owner;
    suggestions.value = [];
    showSuggestions.value = false;
  }

  function clearSearch() {
    searchQuery.value = '';
    selectedOwner.value = null;
    suggestions.value = [];
    showSuggestions.value = false;
  }

  async function getOrCreateOwner(): Promise<Owner | null> {
    if (selectedOwner.value) {
      return selectedOwner.value;
    }

    if (!searchQuery.value.trim()) {
      return null;
    }

    try {
      const response = await axios.post('/api/owners/find-or-create', {
        name: searchQuery.value.trim()
      });
      
      const owner = response.data;
      selectedOwner.value = owner;
      return owner;
    } catch (error) {
      console.error('Error creating/finding owner:', error);
      throw error;
    }
  }

  function hideSuggestions() {
    setTimeout(() => {
      showSuggestions.value = false;
    }, 200);
  }

  function setInitialValue(ownerName: string) {
    searchQuery.value = ownerName;
    selectedOwner.value = null;
  }

  return {
    searchQuery,
    suggestions,
    isLoading,
    showSuggestions,
    selectedOwner,
    isNewOwner,
    onInput,
    selectOwner,
    clearSearch,
    getOrCreateOwner,
    hideSuggestions,
    setInitialValue,
  };
}