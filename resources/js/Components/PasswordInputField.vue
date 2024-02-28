<script setup>
import { DashboardIcon } from '@/Components/Icons/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import InputError from '@/Components/InputError.vue'
import Tooltip from '@/Components/Tooltip.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'

const props = defineProps({
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
    errorMessage: {
        type: String,
        default: ''
    }
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
            <Tooltip>
                <h3>The password must contain:</h3>
                <ul class="pl-6 list-disc">
                    <li>at least 8 characters.</li>
                    <li>at least one uppercase letter.</li>
                    <li>at least one lowercase letter.</li>
                    <li>at least one symbol.</li>
                    <li>at least one number.</li>
                </ul>
            </Tooltip>
        </span>
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
                :inputType="'password'"
                :withIcon="true"
                :class="'w-full'"
                :value="dataValue"
                @change="$emit('update:modelValue', $event.target.value)"
            />
        </InputIconWrapper>
        <InputError
            :message="errorMessage"
            v-if="errorMessage"
        />
    </div>
</template>
