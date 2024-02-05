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
    withLabel: {
		type: Boolean,
		default: false,
	},
})

const onChangeFile = (event) => {
    console.log(event.target.files[0]);
    // $emit('update:modelValue', event.target.files[0]);
}
</script>

<template>
    <div class="input-wrapper">
        <Label
            :value="labelValue"
            :for="inputId"
            class="mt-4 mb-2"
            v-if="withLabel"
        >
        </Label>
        <!-- <span class="sr-only">Choose profile photo</span> -->
        <input 
            type="file" 
            :id="inputId" 
            :name="inputId"
            class="block w-full text-sm text-slate-300 file:mr-2 file:py-2 file:px-2 file:rounded-full 
                    file:border-gray-400 file:text-sm file:font-semibold file:bg-violet-50 file:text-gray-700
                    hover:file:bg-gray-300 dark:hover:file:bg-gray-700 focus:file:border-gray-400 focus:file:ring 
                    focus:file:ring-purple-500 focus:file:ring-offset-2 focus:file:ring-offset-white dark:file:border-gray-600 
                    dark:file:bg-dark-eval-1 dark:file:text-gray-300 dark:focus:file:ring-offset-dark-eval-1"
            @change="$emit('update:modelValue', $event.target.files[0])"
        />
    </div>
</template>
