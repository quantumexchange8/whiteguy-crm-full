<script setup>
import Label from '@/Components/Label.vue'
import { onMounted, ref } from 'vue';

const props = defineProps({
	inputId: {
		type: String,
		default: ''
	},
	dataValue: {
		type: [ String, Number ],
		default: ''
	},
    labelValue: {
		type: [ String, Number ],
		default: ''
	}
})

const convertedDataValue = ref();

onMounted(() => {
    convertedDataValue.value = String(props.dataValue);
});

</script>

<template>
	<div class="input-wrapper">
        <Label
            :value="labelValue"
            :for="inputId"
            class="mb-2"
        >
        </Label>
        <div class="my-4 ml-6">
            <Label
                :value="convertedDataValue"
                :id="inputId"
            >
            </Label>
        </div>
    </div>
</template>
