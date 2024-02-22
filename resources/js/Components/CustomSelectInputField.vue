<script setup>
import Label from '@/Components/Label.vue'
import InputError from '@/Components/InputError.vue'
import { ref, onMounted } from 'vue'

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
		type: Array,
		default: () => [],
	},
	dataValue: {
		type: String,
	},
    errorMessage: {
        type: String,
        default: ''
    }
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
        <Label
            :value="labelValue"
            :for="inputId"
            class="mb-2"
        >
        </Label>
        <div class="relative">
            <select 
                v-if="inputArray.length"
                :id="inputId"
                v-model="selectedValue"
                @change="$emit('update:modelValue', $event.target.value)"
                class="block appearance-none w-full bg-gray-200 border border-grey-lighter text-black 
                        py-2 px-4 pr-8 rounded-md focus:border-gray-400 focus:ring focus:ring-purple-500 
                        focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 
                        dark:bg-dark-eval-1 dark:text-gray-300 dark:focus:ring-offset-dark-eval-1" 
            >
                <option disabled>---------------</option>
                <option v-for="(option, index) in inputArray" :key="index">
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
