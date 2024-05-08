<script setup>
import { onMounted, ref } from 'vue'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import Tooltip from '@/Components/Tooltip.vue'
import InputError from '@/Components/InputError.vue'
import { DashboardIcon } from '@/Components/Icons/outline'
import TextareaInput from '@/Components/TextareaInput.vue'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'

const props = defineProps({
	inputType: {
		type: String,
		default: 'text'
	},
	inputId: {
		type: String,
	},
	dataValue: {
		type: [String, Number],
		default: ''
	},
    decimalOption: {
        type: Boolean,
        default: false
    },
    step: {
        type: Number,
        default: 1
    },
    errorMessage: {
        type: String,
        default: ''
    },
})
</script>

<template>
	<div class="input-wrapper">
        <Input 
            :id="inputId"
            :name="inputId"
            :withIcon="true"
            :class="[
                'w-full text-sm !pl-4 !py-2.5 min-w-20',
                {
                    '!border-red-600': errorMessage,
                }
            ]"
            :value="dataValue"
            @change="$emit('update:modelValue', $event.target.value)"
            v-if="inputType === 'text'"
        />
        <Input 
            :id="inputId"
            :name="inputId"
            :withIcon="true"
            :class="[
                'w-full text-sm !pl-4 !py-2.5 min-w-20',
                {
                    '!border-red-600': errorMessage,
                }
            ]"
            :value="dataValue"
            @change="$emit('update:modelValue', $event.target.value)"
            :inputType="inputType"
            :step="decimalOption ? step : 1"
            v-if="inputType === 'number'"
        />
        <TextareaInput 
            :id="inputId"
            :name="inputId"
            :withIcon="true"
            :class="[
                'w-full text-sm !pl-4 !py-2.5 min-w-20',
                {
                    '!border-red-600': errorMessage,
                }
            ]"
            :value="dataValue"
            @change="$emit('update:modelValue', $event.target.value)"
            v-if="inputType === 'textarea'"
        />
        <InputError
            :message="errorMessage"
            v-if="errorMessage"
        />
    </div>
</template>
