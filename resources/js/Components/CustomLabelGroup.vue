<script setup>
import { onMounted, ref } from 'vue';
import Label from '@/Components/Label.vue'
import Tooltip from '@/Components/Tooltip.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
	inputId: {
		type: String,
		default: ''
	},
	dataValue: {
		type: [ String, Number, Boolean ],
		default: ''
	},
    labelValue: {
		type: [ String, Number ],
		default: ''
	},
    errorMessage: {
        type: String,
        default: ''
    },
    withTooltip: {
        type: Boolean,
        default: false
    },
})

const convertedDataValue = ref();

onMounted(() => {
    convertedDataValue.value = String(props.dataValue);
});

</script>

<template>
	<div class="input-wrapper">
        <span class="flex flex-row gap-2">
            <Label
                :value="labelValue"
                :for="inputId"
                class="mb-2"
            >
            </Label>
            <Tooltip v-if="withTooltip">
                <slot name="tooltipContent"></slot>
            </Tooltip>
        </span>
        <div class="mb-4">
            <slot :id="inputId" v-if="dataValue === false"></slot>
            <Label
                :value="convertedDataValue"
                :id="inputId"
                class="py-2 px-3 border border-gray-400 rounded-md min-h-[41px]
                        dark:border-gray-600 dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1"
                v-else
            >
            </Label>
        </div>
        <InputError
            :message="errorMessage"
            v-if="errorMessage"
        />
    </div>
</template>
