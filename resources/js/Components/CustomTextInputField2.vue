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
    labelValue: {
		type: String,
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
    withTooltip: {
        type: Boolean,
        default: false
    },
})
</script>

<template>
	<div class="input-wrapper flex flex-row gap-2 w-full">
        <span class="flex flex-row gap-2 pr-6 self-center w-[25%]">
            <Label
                :value="labelValue"
                :for="inputId"
                class="mb-2 text-sm text-nowrap"
            >
            </Label>
            <Tooltip v-if="withTooltip">
                <slot></slot>
            </Tooltip>
        </span>
        <span class="w-[10%]"></span>
        <InputIconWrapper class="w-[65%]">
            <template #icon>
                <DashboardIcon 
                    class="flex-shrink-0 w-5 h-5" 
                    aria-hidden="true" 
                />
            </template>
            <Input 
                :id="inputId"
                :name="inputId"
                :withIcon="true"
                :class="'w-full text-sm'"
                :value="dataValue"
                @change="$emit('update:modelValue', $event.target.value)"
                v-if="inputType === 'text'"
            />
            <Input 
                :id="inputId"
                :name="inputId"
                :withIcon="true"
                :class="'w-full text-sm'"
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
                class="w-full text-sm"
                :value="dataValue"
                @change="$emit('update:modelValue', $event.target.value)"
                v-if="inputType === 'textarea'"
            />
        </InputIconWrapper>
        <InputError
            :message="errorMessage"
            v-if="errorMessage"
        />
    </div>
</template>
