<script setup>
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import TextareaInput from '@/Components/TextareaInput.vue'
import InputError from '@/Components/InputError.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import { DashboardIcon } from '@/Components/Icons/outline'
import { onMounted, ref } from 'vue'

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
    }
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
        <InputIconWrapper>
            <template #icon>
                <DashboardIcon 
                    class="flex-shrink-0 w-6 h-6" 
                    aria-hidden="true" 
                />
            </template>
            <Input 
                :id="inputId"
                :name="inputId"
                :withIcon="true"
                :class="'w-full'"
                :value="dataValue"
                @change="$emit('update:modelValue', $event.target.value)"
                v-if="inputType === 'text'"
            />
            <Input 
                :id="inputId"
                :name="inputId"
                :withIcon="true"
                :class="'w-full'"
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
                class="w-full"
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
