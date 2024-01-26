<script setup>
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'

const props = defineProps({
	inputId: {
		type: String,
		default: '',
	},
    labelValue: {
		type: String,
		default: '',
	},
})
</script>

<template>
    <div class="input-wrapper">
        <Label
            :value="labelValue"
            :for="inputId"
            class="mt-4 mb-2"
        >
        </Label>
        <span class="sr-only">Choose profile photo</span>
        <input 
            type="file" 
            :id="inputId" 
            class="block w-full text-sm text-slate-300 file:mr-4 file:py-3 file:px-6 file:rounded-full 
                    file:border-gray-400 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700
                    hover:file:bg-gray-700 focus:file:border-gray-400 focus:file:ring focus:file:ring-purple-500 
                    focus:file:ring-offset-2 focus:file:ring-offset-white dark:file:border-gray-600 
                    dark:file:bg-dark-eval-1 dark:file:text-gray-300 dark:focus:file:ring-offset-dark-eval-1"
        />
    </div>
</template>
