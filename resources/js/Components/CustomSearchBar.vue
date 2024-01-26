<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    productData: {
        type: Array,
		default: () => [],
    }
});

const searchQuery = ref('');

const filteredData = computed(() => {
  return productData.value.filter((data) => {
    // Adjust the conditions based on your search requirements
    return (
      data.product.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  });
});

</script>

<template>
    <div class="mb-4">
        <input
            v-model="searchQuery"
            type="text"
            id="productSearch"
            name="productSearch"
            class="p-2 border border-gray-500 rounded-md dark:bg-dark-eval-1"
        />
    </div>
</template>
<!-- incomplete -->
