<script setup>
import { ref, onMounted } from 'vue'
import InputError from '@/Components/InputError.vue'
import Tooltip from '@/Components/Tooltip.vue'
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
	inputArray: {
		type: [Array, Object],
		default: () => [],
	},
	dataValue: {
		type: String,
	},
    errorMessage: {
        type: String,
        default: ''
    },
    withTooltip: {
        type: Boolean,
        default: false
    },
    customValue: {
        type: Boolean,
        default: false
    },
})

defineEmits(['update:modelValue'])

const selectedValue = ref('');

onMounted(() => {
  // Set the initially selected value
  selectedValue.value = props.dataValue;
})
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
                <slot></slot>
            </Tooltip>
        </span>
        <div class="relative">
            <select 
                v-if="inputArray.length || Object.keys(inputArray).length"
                :id="inputId"
                v-model="selectedValue"
                @change="$emit('update:modelValue', $event.target.value)"
                class="block appearance-none w-full bg-gray-200 border border-grey-lighter text-black 
                        py-2 px-4 pr-8 rounded-md focus:border-gray-400 focus:ring focus:ring-purple-500 
                        focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 
                        dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1" 
            >
                <option disabled>---------------</option>
                <!-- Passing an object of objects with key as id and item and value for dynamic/values that are retrieved from another table -->
                <option v-for="(option, index) in inputArray" :key="index" :value="index" v-if="customValue">
                    {{ option.value }}
                </option>
                <option v-for="(option, ix) in inputArray" :key="ix" :value="option" v-else>
                    {{ option }}
                </option>
            </select>
            <span v-else>No options available</span>
        </div>
        <InputError
            :message="errorMessage"
            v-if="errorMessage"
        />
    </div>
</template>
