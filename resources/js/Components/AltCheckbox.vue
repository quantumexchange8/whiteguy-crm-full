<script setup>
import { computed } from 'vue'
import { InfoCircleIcon } from '@/Components/Icons/outline';
import InputError from '@/Components/InputError.vue'
import Tooltip from '@/Components/Tooltip.vue'
import Label from '@/Components/Label.vue'

const emit = defineEmits(['update:checked'])

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        default: null,
    },
    labelValue: {
		type: String,
		default: ''
	},
	inputId: {
		type: String,
	},
    errorMessage: {
        type: String,
        default: ''
    }
})

const proxyChecked = computed({
    get() {
        return props.checked
    },

    set(val) {
        emit('update:checked', val)
    },
})
</script>

<template>
	<div class="input-wrapper">
        <div class="flex flex-row gap-3">
            <input
                type="checkbox"
                :value="value"
                v-model="proxyChecked"
                class="ml-2 text-purple-500 border-gray-300 rounded focus:border-purple-300 focus:ring focus:ring-purple-500 
                        dark:border-gray-600 dark:bg-dark-eval-1 dark:focus:ring-offset-dark-eval-1 w-6 h-6"
            />
            <Label
                :value="labelValue"
                :for="inputId"
            >
            </Label>
            <Tooltip>
                <slot></slot>
            </Tooltip>
        </div>
        <InputError
            :message="errorMessage"
            v-if="errorMessage"
        />
    </div>
</template>
