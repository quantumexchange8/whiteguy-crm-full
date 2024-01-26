<script setup>
import Label from '@/Components/Label.vue'

const props = defineProps({
	inputId: {
		type: String,
		default: ''
	},
	dataValue: {
		type: String,
		default: '-'
	},
    labelValue: {
		type: String,
		default: ''
	}
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
        <div class="my-4 ml-6">
            <Label
                :value="dataValue"
                :id="inputId"
            >
            </Label>
        </div>
    </div>
</template>
