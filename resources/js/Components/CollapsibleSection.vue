<script setup>
import { ref } from 'vue';
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'

const props = defineProps({
  title: {
    type: String,
    default: '',
  },
  hideSection: {
    type: Boolean,
    default: false,
  }
});

let showSection = ref(!props.hideSection);
</script>

<template>
    <div>
      <div class="flex justify-between items-center mb-2">
        <Label 
            :value="title" 
            class="mt-4 !text-2xl !font-bold pl-2" 
        >
        </Label>
        <Button 
          type="button"
          variant="success"
          size="sm"
          class="justify-center px-6 py-2"
          @click="showSection = !showSection"
        >
          {{ showSection ? 'Hide' : 'Show' }}
        </Button>
      </div>
      <hr class="border-b rounded-md border-gray-600 mb-6 w-full">
      <div v-show="showSection">
        <slot></slot>
      </div>
    </div>
  </template>